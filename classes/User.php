<?php
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $email;
    public $password;
    public $role_id;
    public $reset_token;
    public $reset_token_expires;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (username, email, password, role_id)
                VALUES (:username, :email, :password, :role_id)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and hash password
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role_id = 2; // Default role is user

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role_id", $this->role_id);

        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT user_id, username, password, role_id 
                 FROM " . $this->table_name . "
                 WHERE username = :username AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function requestPasswordReset($email) {
        $query = "SELECT user_id, email FROM " . $this->table_name . "
                 WHERE email = :email AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update_query = "UPDATE " . $this->table_name . "
                           SET reset_token = :token,
                               reset_token_expires = :expires
                           WHERE user_id = :user_id";

            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":token", $token);
            $update_stmt->bindParam(":expires", $expires);
            $update_stmt->bindParam(":user_id", $row['user_id']);

            if($update_stmt->execute()) {
                return $token;
            }
        }
        return false;
    }

    public function resetPassword($token, $new_password) {
        $query = "SELECT user_id FROM " . $this->table_name . "
                 WHERE reset_token = :token 
                 AND reset_token_expires > NOW()
                 AND is_active = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_query = "UPDATE " . $this->table_name . "
                           SET password = :password,
                               reset_token = NULL,
                               reset_token_expires = NULL
                           WHERE user_id = :user_id";

            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":password", $hashed_password);
            $update_stmt->bindParam(":user_id", $row['user_id']);

            return $update_stmt->execute();
        }
        return false;
    }
}
?>
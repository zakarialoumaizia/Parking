<?php
class User extends Model {
    public function findUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Login logic is usually in controller, but checking password can be helper or here. 
    // Keeping it simple: Controller will use findUserByEmail and verify password.

    public function register($data) {
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (:nom, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':role', $data['role']);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

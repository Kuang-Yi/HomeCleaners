<?php
class User {
    public static function getByEmail($pdo, $email) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT id, email, user_type FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($pdo, $email, $password, $user_type) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, user_type) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $hashed, $user_type]);
    }

    public static function createByAdmin($pdo, $email, $password, $user_type) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, user_type) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $hashed, $user_type]);
    }

    public static function update($pdo, $id, $email, $user_type) {
        $stmt = $pdo->prepare("UPDATE users SET email = ?, user_type = ? WHERE id = ?");
        return $stmt->execute([$email, $user_type, $id]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT id, email, user_type FROM users WHERE user_type IN ('C', 'H')");
        return $stmt->fetchAll();
    }
}

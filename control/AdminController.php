<?php
require_once __DIR__ . '/../entity/User.php';

class AdminController {
    public static function getAllUsers() {
        global $pdo;
        return User::getAll($pdo);
    }

    public static function getUserById($id) {
        global $pdo;
        return User::getById($pdo, $id);
    }

    public static function createUser($email, $password, $user_type) {
        global $pdo;
        return User::createByAdmin($pdo, $email, $password, $user_type);
    }

    public static function updateUser($id, $email, $user_type) {
        global $pdo;
        return User::update($pdo, $id, $email, $user_type);
    }

    public static function deleteUser($id) {
        global $pdo;
        return User::delete($pdo, $id);
    }
}

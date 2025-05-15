<?php
require_once '../entity/User.php';
require_once '../config/db.php';

class AuthController {
    public static function login($email, $password) {
        global $pdo;
        $user = User::getByEmail($pdo, $email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    public static function register($email, $password, $userType) {
        global $pdo;

        if (!in_array($userType, ['C', 'H'])) {
            return "❌ Invalid user type.";
        }

        $existing = User::getByEmail($pdo, $email);
        if ($existing) {
            return "⚠️ Email already registered.";
        }

        $success = User::create($pdo, $email, $password, $userType);
        return $success
            ? "✅ Registration successful. <a href='login.php'>Login here</a>"
            : "❌ Registration failed. Please try again.";
    }
}

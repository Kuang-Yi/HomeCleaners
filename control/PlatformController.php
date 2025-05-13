<?php
require_once '../entity/Category.php';

class PlatformController {
    public static function getAllCategoriesWithServiceCount() {
        global $pdo;
        return Category::getAllWithServiceCount($pdo);
    }

    public static function addCategory($name) {
        global $pdo;
        return Category::add($pdo, $name);
    }

    public static function deleteCategory($id) {
        global $pdo;
        return Category::delete($pdo, $id);
    }
}

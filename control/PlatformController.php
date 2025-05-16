<?php
require_once '../entity/Category.php';
require_once '../entity/Report.php';

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
	
	public static function getReportOptions() {
        return Report::getAvailableBookingPeriods();
    }

    public static function getReportData($type, $value) {
        return Report::fetchBookingsByPeriod($type, $value);
    }
	
	public static function updateCategory($id, $newName) {
    global $pdo;

    if (Category::existsByName($pdo, $newName, $id)) {
        return "⚠️ A category with this name already exists.";
    }

    return Category::update($pdo, $id, $newName) ? true : "❌ Update failed.";
}


}

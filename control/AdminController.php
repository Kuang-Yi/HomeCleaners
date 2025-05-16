<?php
require_once __DIR__ . '/../entity/User.php';
require_once '../lib/fpdf/fpdf.php';

// PDF class defined outside to avoid class nesting error
class UserPDF extends FPDF {
    function header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'User Report', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 10, 'Email', 1);
        $this->Cell(40, 10, 'User Type', 1);
        $this->Cell(40, 10, 'Status', 1);
        $this->Ln();
    }

    function addUser($email, $userType, $status) {
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 10, $email, 1);
        $this->Cell(40, 10, $userType, 1);
        $this->Cell(40, 10, $status, 1);
        $this->Ln();
    }
}

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

        if (User::emailExistsForOtherUser($pdo, $email, $id)) {
            return "⚠️ Email is already used by another user.";
        }

        return User::update($pdo, $id, $email, $user_type) ? true : "❌ Update failed.";
    }

    public static function deleteUser($id) {
        global $pdo;
        return User::delete($pdo, $id);
    }

    public static function updateUserStatus($id, $status) {
        global $pdo;
        return User::updateStatus($pdo, $id, $status);
    }

    public static function exportUserReportPDF() {
        global $pdo;

        $pdf = new UserPDF();
        $pdf->AddPage();
        $users = User::getAll($pdo);

        foreach ($users as $u) {
            $role = $u['user_type'] === 'C' ? 'Cleaner' : ($u['user_type'] === 'H' ? 'Homeowner' : 'Admin');
            $status = $u['account_status'] == 1 ? 'Active' : 'Suspended';
            $pdf->addUser($u['email'], $role, $status);
        }

        $pdf->Output('D', 'User_Report.pdf');
        exit();
    }
}

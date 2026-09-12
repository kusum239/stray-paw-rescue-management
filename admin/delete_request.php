<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true
]);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

require_once "../config/db.php";

$request_id = intval($_GET['id'] ?? 0);

if ($request_id > 0) {
    $col_check = $conn->query("SHOW COLUMNS FROM rescue_requests LIKE 'request_id'");
    $id_col = ($col_check && $col_check->num_rows > 0) ? 'request_id' : 'id';

    $stmt = $conn->prepare("DELETE FROM rescue_requests WHERE `$id_col` = ?");
    if ($stmt) {
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
}

header("Location: requests.php");
exit();
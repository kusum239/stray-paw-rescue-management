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

$animal_id = intval($_GET['id'] ?? 0);

if ($animal_id > 0) {
    $col_check = $conn->query("SHOW COLUMNS FROM animals LIKE 'animal_id'");
    $id_col = ($col_check && $col_check->num_rows > 0) ? 'animal_id' : 'id';

    $stmt = $conn->prepare("DELETE FROM animals WHERE `$id_col` = ?");
    if ($stmt) {
        $stmt->bind_param("i", $animal_id);
        $stmt->execute();
    }
}

header("Location: animals.php");
exit();
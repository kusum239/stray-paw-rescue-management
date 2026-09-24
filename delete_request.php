<?php

require_once "includes/auth.php";
require_once "config/db.php";

if (!isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];
$request_id = (int) $_GET["id"];

$stmt = $conn->prepare("
    SELECT photo, video
    FROM rescue_requests
    WHERE request_id = ?
    AND user_id = ?
");

$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$request = $result->fetch_assoc();

if (!$request) {
    header("Location: dashboard.php");
    exit();
}

if (!empty($request["photo"])) {
    $photoPath = "uploads/reports/" . $request["photo"];

    if (file_exists($photoPath)) {
        unlink($photoPath);
    }
}

if (!empty($request["video"])) {
    $videoPath = "uploads/reports/" . $request["video"];

    if (file_exists($videoPath)) {
        unlink($videoPath);
    }
}

$stmt = $conn->prepare("
    DELETE FROM rescue_requests
    WHERE request_id = ?
    AND user_id = ?
");

$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();

header("Location: dashboard.php");
exit();
?>
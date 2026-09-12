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
$col_check = $conn->query("SHOW COLUMNS FROM rescue_requests LIKE 'request_id'");
$id_col = ($col_check && $col_check->num_rows > 0) ? 'request_id' : 'id';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status'] ?? 'Pending');
    
    $stmt = $conn->prepare("UPDATE rescue_requests SET status = ? WHERE `$id_col` = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $request_id);
        $stmt->execute();
        header("Location: requests.php");
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM rescue_requests WHERE `$id_col` = ? LIMIT 1");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Request Status - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; display: flex; justify-content: center; }
        .card { background: white; width: 450px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; color: #34495e; margin-bottom: 5px; }
        .form-group select { width: 100%; padding: 10px; border: 1px solid #dcdfe6; border-radius: 6px; }
        .btn-submit { background-color: #1b4d3e; color: white; border: none; padding: 10px; border-radius: 6px; width: 100%; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
<div class="card">
    <h2>Update Request #<?php echo $request_id; ?></h2>
    <?php if ($request): ?>
        <form method="POST">
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Pending" <?php if(($request['status']??'') == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if(($request['status']??'') == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Completed" <?php if(($request['status']??'') == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Save Status</button>
        </form>
    <?php else: ?>
        <p>Request not found.</p>
    <?php endif; ?>
</div>
</body>
</html>
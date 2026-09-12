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

$volunteers = [];
$table_check = $conn->query("SHOW TABLES LIKE 'volunteers'");

if ($table_check && $table_check->num_rows > 0) {
    $res = $conn->query("SELECT * FROM volunteers");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $volunteers[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteers List - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e1e8e5; }
        th { background-color: #f8fafc; color: #34495e; }
    </style>
</head>
<body>

<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="reports.php">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="animals.php">Animals</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php" style="color:white; text-decoration: underline;">Volunteers</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>

<div class="card">
    <h2>Registered Volunteers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role / Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($volunteers)): ?>
                <?php foreach ($volunteers as $v): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($v['id'] ?? $v['volunteer_id'] ?? '1'); ?></td>
                        <td><?php echo htmlspecialchars($v['name'] ?? $v['full_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($v['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($v['phone'] ?? $v['contact'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($v['status'] ?? $v['role'] ?? 'Active'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #7f8c8d; padding: 20px;">No volunteer records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
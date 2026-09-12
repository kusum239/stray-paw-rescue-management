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

$users = [];
$table_check = $conn->query("SHOW TABLES LIKE 'users'");

if ($table_check && $table_check->num_rows > 0) {
    // Dynamically check primary key column name
    $id_col = 'id';
    $col_res = $conn->query("SHOW COLUMNS FROM users");
    if ($col_res) {
        while ($c = $col_res->fetch_assoc()) {
            if (in_array(strtolower($c['Field']), ['id', 'user_id', 'userid'])) {
                $id_col = $c['Field'];
                break;
            }
        }
    }

    $res = $conn->query("SELECT * FROM users ORDER BY `$id_col` DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $users[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card h2 { color: #2c3e50; font-size: 1.4rem; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e1e8e5; }
        th { background-color: #f8fafc; color: #34495e; font-size: 0.9rem; }
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
        <a href="volunteers.php">Volunteers</a>
        <a href="users.php" style="color:white; text-decoration: underline;">Users</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>

<div class="card">
    <h2>Registered Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role / Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): 
                    $u_id = $user[$id_col] ?? 1;
                    $u_name = $user['name'] ?? $user['username'] ?? 'N/A';
                    $u_email = $user['email'] ?? 'N/A';
                ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($u_id); ?></td>
                        <td><?php echo htmlspecialchars($u_name); ?></td>
                        <td><?php echo htmlspecialchars($u_email); ?></td>
                        <td><?php echo htmlspecialchars($user['role'] ?? $user['created_at'] ?? 'Standard'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #7f8c8d; padding: 25px;">
                        No registered users found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
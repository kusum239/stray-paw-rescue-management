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

function getTableCount($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    if ($result && $result->num_rows > 0) {
        $countResult = $conn->query("SELECT COUNT(*) AS total FROM `$tableName`");
        if ($countResult) {
            $row = $countResult->fetch_assoc();
            return $row['total'] ?? 0;
        }
    }
    return 0;
}

$total_animals    = getTableCount($conn, 'animals');
$total_requests   = getTableCount($conn, 'rescue_requests');
$total_shelters   = getTableCount($conn, 'shelters');
$total_volunteers = getTableCount($conn, 'volunteers');
$total_users      = getTableCount($conn, 'users');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Summary - Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .nav-bar a:hover { color: white; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid #1b4d3e; }
        .stat-card h3 { color: #7f8c8d; font-size: 0.9rem; margin-bottom: 10px; }
        .stat-card p { font-size: 2.2rem; font-weight: bold; color: #1b4d3e; }
    </style>
</head>
<body>

<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="reports.php" style="color:white; text-decoration: underline;">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="animals.php">Animals</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php">Volunteers</a>
        <a href="users.php">Users</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Requests</h3>
        <p><?php echo $total_requests; ?></p>
    </div>
    <div class="stat-card">
        <h3>Rescued Animals</h3>
        <p><?php echo $total_animals; ?></p>
    </div>
    <div class="stat-card">
        <h3>Active Shelters</h3>
        <p><?php echo $total_shelters; ?></p>
    </div>
    <div class="stat-card">
        <h3>Volunteers</h3>
        <p><?php echo $total_volunteers; ?></p>
    </div>
    <div class="stat-card">
        <h3>Registered Users</h3>
        <p><?php echo $total_users; ?></p>
    </div>
</div>

</body>
</html>
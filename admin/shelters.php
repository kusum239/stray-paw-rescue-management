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

$shelters = [];
$res = $conn->query("SELECT * FROM shelters");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $shelters[] = $row;
    }
}

$total_shelters = count($shelters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shelter Locations - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        
        /* Navigation */
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        
        /* Main Card */
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Summary Stat Cards */
        .stats-container { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-badge { background: #f8fafc; padding: 12px 20px; border-radius: 6px; border-left: 4px solid #1b4d3e; font-size: 14px; }
        
        /* Table Styles */
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
        th, td { padding: 14px 15px; border-bottom: 1px solid #e1e8e5; vertical-align: middle; }
        th { background-color: #f8fafc; color: #34495e; font-weight: 600; }
        
        /* Buttons */
        .btn-add { background: #2e7d32; color: white; padding: 9px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; }
        .btn-edit { background: #0288d1; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 13px; }
        .btn-delete { background: #d32f2f; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 13px; margin-left: 5px; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="reports.php">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="animals.php">Animals</a>
        <a href="shelters.php" style="color:white; text-decoration: underline;">Shelters</a>
        <a href="volunteers.php">Volunteers</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>
<!-- Main Section -->
<div class="card">
    <div class="header-actions">
        <h2>Registered Shelters</h2>
        <a href="add_shelter.php" class="btn-add">+ Add New Shelter</a>
    </div>

    <!-- Summary Badges -->
    <div class="stats-container">
        <div class="stat-badge">
            <strong>Total Shelters:</strong> <?php echo $total_shelters; ?>
        </div>
    </div>

    <!-- Shelters Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Shelter Name</th>
                <th>Location</th>
                <th>Contact Phone</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($shelters)): ?>
                <?php foreach ($shelters as $s): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($s['id'] ?? $s['shelter_id'] ?? '1'); ?></td>
                        <td><strong><?php echo htmlspecialchars($s['name'] ?? $s['shelter_name'] ?? 'Main Shelter'); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['location'] ?? $s['address'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($s['phone'] ?? $s['contact'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($s['capacity'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="edit_shelter.php?id=<?php echo $s['id'] ?? $s['shelter_id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_shelter.php?id=<?php echo $s['id'] ?? $s['shelter_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this shelter?');" 
                               class="btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 25px;">No shelters registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
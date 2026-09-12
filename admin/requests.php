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

// Dynamic update handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['status'])) {
    $req_id = intval($_POST['request_id']);
    $new_status = $_POST['status'];

    $col_check = $conn->query("SHOW COLUMNS FROM rescue_requests LIKE 'request_id'");
    $id_col = ($col_check && $col_check->num_rows > 0) ? 'request_id' : 'id';

    $stmt = $conn->prepare("UPDATE rescue_requests SET status = ? WHERE `$id_col` = ?");
    if ($stmt) {
        $stmt->bind_param("si", $new_status, $req_id);
        $stmt->execute();
    }
}

$requests = [];
$table_check = $conn->query("SHOW TABLES LIKE 'rescue_requests'");
if ($table_check && $table_check->num_rows > 0) {
    $res = $conn->query("SELECT * FROM rescue_requests");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $requests[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Rescue Requests - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e1e8e5; }
        th { background-color: #f8fafc; color: #34495e; }
        .action-btn { background: #1b4d3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>

<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="reports.php">Dashboard</a>
        <a href="requests.php" style="color:white; text-decoration: underline;">Requests</a>
        <a href="animals.php">Animals</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php">Volunteers</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>

<div class="card">
    <h2>All Rescue Requests</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Animal Type</th>
                <th>Location</th>
                <th>Reporter</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $req): 
                    $curr_id = $req['request_id'] ?? $req['id'] ?? 1;
                ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($curr_id); ?></td>
                        <td><?php echo htmlspecialchars($req['animal_type'] ?? $req['animal'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($req['location'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($req['reporter_name'] ?? $req['user_id'] ?? 'Anonymous'); ?></td>
                        <td><?php echo htmlspecialchars($req['status'] ?? 'Pending'); ?></td>
                        <td>
                            <form method="POST" style="display:inline-flex; gap: 5px;">
                                <input type="hidden" name="request_id" value="<?php echo $curr_id; ?>">
                                <select name="status">
                                    <option value="Pending" <?php if(($req['status']??'') == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="In Progress" <?php if(($req['status']??'') == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="Completed" <?php if(($req['status']??'') == 'Completed') echo 'selected'; ?>>Completed</option>
                                </select>
                                <button type="submit" class="action-btn">Update</button>
                            </form>
                            <a href="request_details.php?id=<?php echo $curr_id; ?>" class="action-btn">Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 20px;">No rescue requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
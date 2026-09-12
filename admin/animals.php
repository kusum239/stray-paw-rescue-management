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

$animals = [];
$table_check = $conn->query("SHOW TABLES LIKE 'animals'");

if ($table_check && $table_check->num_rows > 0) {
    $animal_cols = [];
    $a_cols_res = $conn->query("SHOW COLUMNS FROM animals");
    if ($a_cols_res) {
        while ($c = $a_cols_res->fetch_assoc()) {
            $animal_cols[] = $c['Field'];
        }
    }

    $shelter_col = null;
    $shelter_check = $conn->query("SHOW TABLES LIKE 'shelters'");
    if ($shelter_check && $shelter_check->num_rows > 0) {
        $cols = $conn->query("SHOW COLUMNS FROM shelters");
        if ($cols) {
            while ($c = $cols->fetch_assoc()) {
                if (in_array($c['Field'], ['name', 'shelter_name', 'title'])) {
                    $shelter_col = $c['Field'];
                    break;
                }
            }
        }
    }

    $has_shelter_fk = in_array('shelter_id', $animal_cols);

    if ($has_shelter_fk && $shelter_col) {
        $query = "SELECT a.*, s.`$shelter_col` AS shelter_display 
                  FROM animals a 
                  LEFT JOIN shelters s ON a.shelter_id = s.id";
    } else {
        $query = "SELECT a.*, 'Unassigned' AS shelter_display FROM animals a";
    }

    $res = $conn->query($query);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $animals[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Animals - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e1e8e5; }
        th { background-color: #f8fafc; color: #34495e; }
        .action-btn { background: #1b4d3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.85rem; display: inline-block; }
        .edit-btn { background: #27ae60; }
        .delete-btn { background: #e74c3c; }
    </style>
</head>
<body>

<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="reports.php">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="animals.php" style="color:white; text-decoration: underline;">Animals</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php">Volunteers</a>
        <a href="../logout.php" style="color: #ff9999;">Logout</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Rescued Animals Directory</h2>
        <a href="add_animal.php" class="action-btn">+ Add New Animal</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name / Tag</th>
                <th>Species</th>
                <th>Status</th>
                <th>Shelter Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($animals)): ?>
                <?php foreach ($animals as $animal): 
                    $curr_id = $animal['id'] ?? $animal['animal_id'] ?? 1;
                ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($curr_id); ?></td>
                        <td><?php echo htmlspecialchars($animal['name'] ?? $animal['animal_name'] ?? 'Unnamed'); ?></td>
                        <td><?php echo htmlspecialchars($animal['species'] ?? $animal['type'] ?? 'Dog'); ?></td>
                        <td><?php echo htmlspecialchars($animal['status'] ?? 'Under Care'); ?></td>
                        <td><?php echo htmlspecialchars($animal['shelter_display'] ?? 'Unassigned'); ?></td>
                        <td>
                            <a href="treatment_history.php?animal_id=<?php echo $curr_id; ?>" class="action-btn">History</a>
                            <a href="edit_animal.php?id=<?php echo $curr_id; ?>" class="action-btn edit-btn">Edit</a>
                            <a href="delete_animal.php?id=<?php echo $curr_id; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this animal record?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 20px;">
                        No animal records found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
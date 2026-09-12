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

$animal_id = intval($_GET['animal_id'] ?? 0);
$treatments = [];
$animal_info = null;

// Dynamic check for animals table column names
if ($animal_id > 0) {
    $col_check = $conn->query("SHOW COLUMNS FROM animals LIKE 'animal_id'");
    $anim_col = ($col_check && $col_check->num_rows > 0) ? 'animal_id' : 'id';
    
    $stmt = $conn->prepare("SELECT * FROM animals WHERE `$anim_col` = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $animal_id);
        $stmt->execute();
        $animal_info = $stmt->get_result()->fetch_assoc();
    }
}

// Dynamic query for treatments table
$table_check = $conn->query("SHOW TABLES LIKE 'treatments'");
if ($table_check && $table_check->num_rows > 0) {
    $col_check2 = $conn->query("SHOW COLUMNS FROM treatments LIKE 'animal_id'");
    $fk_col = ($col_check2 && $col_check2->num_rows > 0) ? 'animal_id' : 'id';

    $query = ($animal_id > 0) 
        ? "SELECT * FROM treatments WHERE `$fk_col` = $animal_id" 
        : "SELECT * FROM treatments";
    
    $res = $conn->query($query);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $treatments[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Treatment History - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e1e8e5; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e1e8e5; }
        th { background-color: #f8fafc; color: #34495e; }
        .back-btn { background: #6c757d; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.9rem; }
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
        <h2>
            Treatment History 
            <?php if ($animal_info): ?>
                - <?php echo htmlspecialchars($animal_info['name'] ?? 'Animal #' . $animal_id); ?>
            <?php endif; ?>
        </h2>
        <a href="animals.php" class="back-btn">← Back to Animals</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Treatment ID</th>
                <th>Date</th>
                <th>Diagnosis / Details</th>
                <th>Treatment Given</th>
                <th>Vet Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($treatments)): ?>
                <?php foreach ($treatments as $t): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($t['id'] ?? $t['treatment_id'] ?? '1'); ?></td>
                        <td><?php echo htmlspecialchars($t['treatment_date'] ?? $t['created_at'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($t['diagnosis'] ?? $t['details'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($t['treatment'] ?? $t['medicine'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($t['vet_name'] ?? $t['doctor'] ?? 'Dr. Staff'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #7f8c8d; padding: 20px;">No medical treatment records found for this animal.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
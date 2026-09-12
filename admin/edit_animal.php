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
$error = "";

// Dynamic column lookup for primary key
$col_check = $conn->query("SHOW COLUMNS FROM animals LIKE 'animal_id'");
$id_col = ($col_check && $col_check->num_rows > 0) ? 'animal_id' : 'id';

// Handle Update Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? 'Dog');
    $status  = trim($_POST['status'] ?? 'Under Care');

    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE animals SET name = ?, species = ?, status = ? WHERE `$id_col` = ?");
        if ($stmt) {
            $stmt->bind_param("sssi", $name, $species, $status, $animal_id);
            $stmt->execute();
            header("Location: animals.php");
            exit();
        }
    } else {
        $error = "Name cannot be empty.";
    }
}

// Fetch Existing Record
$animal = null;
$stmt = $conn->prepare("SELECT * FROM animals WHERE `$id_col` = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $animal = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Animal - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; display: flex; justify-content: center; }
        .card { background: white; width: 500px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #34495e; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #dcdfe6; border-radius: 6px; }
        .btn-submit { background-color: #1b4d3e; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>

<div class="card">
    <h2>Edit Animal Record #<?php echo $animal_id; ?></h2>
    <?php if ($animal): ?>
        <form method="POST">
            <div class="form-group">
                <label>Animal Name / Tag</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($animal['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Species</label>
                <select name="species">
                    <option value="Dog" <?php if(($animal['species']??'') == 'Dog') echo 'selected'; ?>>Dog</option>
                    <option value="Cat" <?php if(($animal['species']??'') == 'Cat') echo 'selected'; ?>>Cat</option>
                    <option value="Other" <?php if(($animal['species']??'') == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Under Care" <?php if(($animal['status']??'') == 'Under Care') echo 'selected'; ?>>Under Care</option>
                    <option value="Recovered" <?php if(($animal['status']??'') == 'Recovered') echo 'selected'; ?>>Recovered</option>
                    <option value="Adopted" <?php if(($animal['status']??'') == 'Adopted') echo 'selected'; ?>>Adopted</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    <?php else: ?>
        <p>Record not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
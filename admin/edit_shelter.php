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

$shelter_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

// Fetch existing shelter details
$query = "SELECT * FROM shelters WHERE id = $shelter_id";
$res = $conn->query($query);
$shelter = $res ? $res->fetch_assoc() : null;

if (!$shelter) {
    die("<div style='font-family: Arial; padding: 20px; color: red;'>Shelter not found. <a href='shelters.php'>Go back</a></div>");
}

// Handle Form Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $capacity = intval($_POST['capacity']);

    $update_sql = "UPDATE shelters 
                   SET name = '$name', location = '$location', phone = '$phone', capacity = '$capacity' 
                   WHERE id = $shelter_id";

    if ($conn->query($update_sql)) {
        header("Location: shelters.php");
        exit();
    } else {
        $message = "Error updating shelter: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Shelter - Stray Paw Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 30px; }
        
        .nav-bar { display: flex; justify-content: space-between; align-items: center; background: #1b4d3e; padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; color: white; }
        .nav-bar a { color: #cfdfda; text-decoration: none; font-weight: 600; margin-left: 15px; }

        .card { background: white; padding: 25px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #34495e; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        
        .btn-submit { background: #2e7d32; color: white; border: none; padding: 10px 18px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px; }
        .btn-cancel { color: #666; text-decoration: none; margin-left: 15px; font-weight: 600; }
    </style>
</head>
<body>

<div class="nav-bar">
    <div style="font-size: 1.2rem; font-weight: bold;">🐾 Stray Paw Admin</div>
    <div>
        <a href="shelters.php" style="color:white; text-decoration: underline;">Back to Shelters</a>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 20px;">Edit Shelter Details</h2>

    <?php if ($message): ?>
        <p style="color: red; margin-bottom: 15px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_shelter.php?id=<?php echo $shelter_id; ?>">
        <div class="form-group">
            <label>Shelter Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($shelter['name'] ?? $shelter['shelter_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Location / Address</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($shelter['location'] ?? $shelter['address'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($shelter['phone'] ?? $shelter['contact'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Total Capacity</label>
            <input type="number" name="capacity" value="<?php echo htmlspecialchars($shelter['capacity'] ?? 10); ?>" required min="1">
        </div>

        <button type="submit" class="btn-submit">Save Changes</button>
        <a href="shelters.php" class="btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>
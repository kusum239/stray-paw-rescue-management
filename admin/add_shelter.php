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

$message = "";

// Handle Form Submission for Adding Shelter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $capacity = intval($_POST['capacity']);

    $insert_sql = "INSERT INTO shelters (name, location, phone, capacity) 
                   VALUES ('$name', '$location', '$phone', '$capacity')";

    if ($conn->query($insert_sql)) {
        header("Location: shelters.php");
        exit();
    } else {
        $message = "Error adding shelter: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Shelter - Stray Paw Admin</title>
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
    <h2 style="margin-bottom: 20px;">Add New Shelter</h2>

    <?php if ($message): ?>
        <p style="color: red; margin-bottom: 15px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="add_shelter.php">
        <div class="form-group">
            <label>Shelter Name</label>
            <input type="text" name="name" placeholder="e.g. Hope Paws Shelter" required>
        </div>

        <div class="form-group">
            <label>Location / Address</label>
            <input type="text" name="location" placeholder="e.g. Main Street, District 4" required>
        </div>

        <div class="form-group">
            <label>Contact Phone</label>
            <input type="text" name="phone" placeholder="e.g. +1 555-0192">
        </div>

        <div class="form-group">
            <label>Total Capacity</label>
            <input type="number" name="capacity" value="15" required min="1">
        </div>

        <button type="submit" class="btn-submit">Add Shelter</button>
        <a href="shelters.php" class="btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>
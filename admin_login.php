<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true
]);
session_start();

require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT admin_id, name FROM admin WHERE email = ? AND password = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $_SESSION["admin_id"] = $row["admin_id"];
                $_SESSION["admin_name"] = $row["name"];
                session_write_close();

                header("Location: admin/reports.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Database error verifying admin credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Stray Paw</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { display: flex; width: 850px; max-width: 95%; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; }
        .brand-section { background-color: #1b4d3e; color: #ffffff; padding: 40px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .brand-header { font-size: 1.4rem; font-weight: bold; }
        .brand-title h1 { font-size: 2.2rem; margin-bottom: 15px; }
        .brand-title p { color: #cfdfda; font-size: 0.95rem; line-height: 1.5; margin-bottom: 25px; }
        .feature-list { list-style: none; }
        .feature-list li { margin-bottom: 12px; font-size: 0.9rem; color: #e1ebe7; }
        .form-section { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .form-header { text-align: center; margin-bottom: 25px; }
        .form-header h2 { color: #2c3e50; font-size: 1.6rem; margin-bottom: 5px; }
        .error-msg { background: #fde8e8; color: #e74c3c; padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 15px; border: 1px solid #f8b4b4; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #34495e; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #dcdfe6; border-radius: 6px; font-size: 0.95rem; background-color: #f8fafc; }
        .btn-submit { width: 100%; padding: 12px; background-color: #1b4d3e; color: white; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background-color: #143b2f; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand-section">
        <div class="brand-header">🐾 Stray Paw</div>
        <div class="brand-title">
            <h1>Admin Portal</h1>
            <p>Manage rescue requests, rescued animals, treatments, shelters, and volunteers.</p>
            <ul class="feature-list">
                <li>✓ Rescue Requests</li>
                <li>✓ Rescued Animals</li>
                <li>✓ Shelters & Volunteers</li>
            </ul>
        </div>
        <div></div>
    </div>
    <div class="form-section">
        <div class="form-header">
            <h2>Admin Login</h2>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="amritasalinarai@gmail.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" value="admin123" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
<?php

session_start();

require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare("
            SELECT admin_id, name, email, password
            FROM admin
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["admin_id"];
                $_SESSION["admin_name"] = $admin["name"];
                $_SESSION["admin_email"] = $admin["email"];

                header("Location: admin/dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";

            }

        } else {

            $error = "Invalid email or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Stray Paw</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="admin-login-page">

    <div class="admin-login-container">

        <div class="admin-login-info">

            <div class="admin-brand">

                <div class="admin-paw">
                    <i class="fa-solid fa-paw"></i>
                </div>

                <h1>Stray Paw</h1>

            </div>

            <h2>Admin Portal</h2>

            <p>
                Manage rescue requests, rescued animals,
                treatments, shelters and volunteers.
            </p>

            <div class="admin-features">

                <div>
                    <span>
                        <i class="fa-solid fa-check"></i>
                    </span>
                    Manage Rescue Requests
                </div>

                <div>
                    <span>
                        <i class="fa-solid fa-check"></i>
                    </span>
                    Manage Rescued Animals
                </div>

                <div>
                    <span>
                        <i class="fa-solid fa-check"></i>
                    </span>
                    Track Treatment Records
                </div>

                <div>
                    <span>
                        <i class="fa-solid fa-check"></i>
                    </span>
                    Manage Volunteers
                </div>

            </div>

        </div>

        <div class="admin-login-box">

            <div class="login-heading">

                <div class="login-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <h2>Admin Login</h2>

                <p>Sign in to access the admin dashboard.</p>

            </div>

            <?php if (!empty($error)): ?>

                <div class="login-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <form method="POST"
                  action="admin_login.php"
                  class="admin-login-form">

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter admin email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter admin password"
                        required
                    >

                </div>

                <button type="submit"
                        class="admin-login-button">

                    Login

                </button>

            </form>

            <div class="back-home">

                <a href="index.php">
                    ← Back to Stray Paw
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>
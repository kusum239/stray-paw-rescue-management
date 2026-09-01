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

        $admin_stmt = $conn->prepare("
            SELECT admin_id, name, email, password
            FROM admin
            WHERE email = ?
            LIMIT 1
        ");

        $admin_stmt->bind_param("s", $email);

        $admin_stmt->execute();

        $admin_result = $admin_stmt->get_result();

        if ($admin_result->num_rows === 1) {

            $admin = $admin_result->fetch_assoc();

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

            $user_stmt = $conn->prepare("
                SELECT user_id, name, email, password
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $user_stmt->bind_param("s", $email);

            $user_stmt->execute();

            $user_result = $user_stmt->get_result();

            if ($user_result->num_rows === 1) {

                $user = $user_result->fetch_assoc();

                if (password_verify($password, $user["password"])) {

                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["user_name"] = $user["name"];
                    $_SESSION["user_email"] = $user["email"];

                    header("Location: dashboard.php");
                    exit();

                } else {

                    $error = "Invalid email or password.";

                }

            } else {

                $error = "Invalid email or password.";

            }

            $user_stmt->close();
        }

        $admin_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | Stray Paw</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<div class="auth-container">

    <div class="auth-card">

        <h1>
            Welcome Back
        </h1>

        <p>
            Login to your Stray Paw account.
        </p>

        <?php if (!empty($error)): ?>

            <p class="login-error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>

        <form method="POST"
              action="login.php">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
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
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn">

                Login

            </button>

        </form>

        <p style="margin-top:20px;">

            Don't have an account?

            <a href="register.php"
               style="color:#bd7068;">

                Register

            </a>

        </p>

    </div>

</div>

<?php include "includes/footer.php"; ?>

</body>

</html>

<?php

session_start();

require_once "config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];


    /* ==============================
       VALIDATION
    ============================== */

    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($address) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        /* ==============================
           CHECK EXISTING EMAIL
        ============================== */

        $checkQuery = "
            SELECT user_id
            FROM users
            WHERE email = ?
        ";

        $stmt = mysqli_prepare($conn, $checkQuery);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($result) > 0) {

            $error = "An account with this email already exists.";

        } else {

            /* ==============================
               HASH PASSWORD
            ============================== */

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* ==============================
               INSERT USER
            ============================== */

            $insertQuery = "
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    phone,
                    address
                )
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmt = mysqli_prepare(
                $conn,
                $insertQuery
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $name,
                $email,
                $hashedPassword,
                $phone,
                $address
            );


            if (mysqli_stmt_execute($stmt)) {

                /*
                 * Registration successful.
                 * Send user to login page.
                 */

                header("Location: login.php?registered=1");
                exit();

            } else {

                $error = "Registration failed. Please try again.";

            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register | Stray Paw</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <!-- Poppins -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

</head>


<body>


<!-- HEADER -->

<?php include "includes/header.php"; ?>


<!-- REGISTER -->

<div class="auth-container">

    <div class="auth-card">

        <h1>Create Account</h1>

        <p>
            Join Stray Paw and help make a difference
            for animals in need.
        </p>


        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="form-alert error-alert">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <!-- SUCCESS MESSAGE -->

        <?php if (!empty($success)): ?>

            <div class="form-alert success-alert">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
            id="registerForm"
        >


            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                    required
                >

            </div>


            <!-- ADDRESS -->

            <div class="form-group">

                <label for="address">
                    Address
                </label>

                <input
                    type="text"
                    id="address"
                    name="address"
                    placeholder="Enter your address"
                    value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                >

            </div>


            <!-- REGISTER BUTTON -->

            <button
                type="submit"
                class="btn"
            >
                Create Account
            </button>


        </form>


        <!-- LOGIN LINK -->

        <p class="auth-bottom-text">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </p>


    </div>

</div>


<!-- FOOTER -->

<?php include "includes/footer.php"; ?>


<script src="assets/js/script.js"></script>

</body>

</html>
```

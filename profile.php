<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT *
    FROM users
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$user =
$stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>

<html>

<head>

<title>
Profile | Stray Paw
</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

<h1>
My Profile
</h1>

<br>

<div class="form-card">

<p>
<strong>Name:</strong>
<?= htmlspecialchars($user["name"]) ?>
</p>

<p>
<strong>Email:</strong>
<?= htmlspecialchars($user["email"]) ?>
</p>

<p>
<strong>Phone:</strong>
<?= htmlspecialchars($user["phone"] ?? "") ?>
</p>

<p>
<strong>Address:</strong>
<?= htmlspecialchars($user["address"] ?? "") ?>
</p>

</div>

</main>

<?php include "includes/footer.php"; ?>

</body>

</html>
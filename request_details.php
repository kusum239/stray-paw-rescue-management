<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$request_id =
isset($_GET["id"])
? intval($_GET["id"])
: 0;


$stmt = $conn->prepare("
    SELECT *
    FROM rescue_request
    WHERE request_id = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $request_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    die("Request not found.");

}

$request =
$result->fetch_assoc();

?>

<!DOCTYPE html>

<html>

<head>

<title>
Request Details | Stray Paw
</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

<h1>
Rescue Request #RQ-<?= $request["request_id"] ?>
</h1>

<br>

<div class="form-card">

<p>
<strong>Animal:</strong>
<?= htmlspecialchars(
$request["animal_type"]
) ?>
</p>

<p>
<strong>Condition:</strong>
<?= htmlspecialchars(
$request["condition_type"]
) ?>
</p>

<p>
<strong>Location:</strong>
<?= htmlspecialchars(
$request["location"]
) ?>
</p>

<p>
<strong>Status:</strong>
<?= htmlspecialchars(
$request["status"]
) ?>
</p>

<p>
<strong>Emergency:</strong>
<?= htmlspecialchars(
$request["emergency"]
) ?>
</p>

<br>

<h3>Description</h3>

<p>
<?= nl2br(
htmlspecialchars(
$request["description"]
)
) ?>
</p>


<?php if ($request["photo"]): ?>

<br>

<h3>Photo</h3>

<img
src="uploads/reports/
<?= htmlspecialchars($request["photo"]) ?>"
style="max-width:400px;border-radius:10px;">

<?php endif; ?>


<?php if ($request["video"]): ?>

<br>

<h3>Video</h3>

<video
controls
style="max-width:500px;">

<source
src="uploads/reports/
<?= htmlspecialchars($request["video"]) ?>">

</video>

<?php endif; ?>

</div>

</main>

<?php include "includes/footer.php"; ?>

</body>

</html>
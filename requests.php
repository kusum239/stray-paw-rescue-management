<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT *
    FROM rescue_request
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$requests = $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<title>
My Rescue Requests | Stray Paw
</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="container page-section">

<h1>
My Rescue Requests
</h1>

<p style="color:#78716c;margin-bottom:25px;">
Track the rescue requests you have submitted.
</p>


<div class="table-wrapper">

<table class="data-table">

<thead>

<tr>

<th>ID</th>
<th>Animal</th>
<th>Condition</th>
<th>Location</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php while (
$row = $requests->fetch_assoc()
): ?>

<tr>

<td>
#RQ-<?= $row["request_id"] ?>
</td>

<td>
<?= htmlspecialchars(
$row["animal_type"]
) ?>
</td>

<td>
<?= htmlspecialchars(
$row["condition_type"]
) ?>
</td>

<td>
<?= htmlspecialchars(
$row["location"]
) ?>
</td>

<td>
<?= date(
"M d, Y",
strtotime($row["created_at"])
) ?>
</td>

<td>

<span class="status status-pending">

<?= htmlspecialchars(
$row["status"]
) ?>

</span>

</td>

<td>

<a href="request_details.php?id=
<?= $row["request_id"] ?>"
style="color:#bd7068;">

Details

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</main>


<?php include "includes/footer.php"; ?>

</body>

</html>
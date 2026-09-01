<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$message = "";
$error = "";


/* CHECK EXISTING APPLICATION */

$stmt = $conn->prepare("
    SELECT *
    FROM volunteer_application
    WHERE user_id = ?
    ORDER BY application_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$existing =
$stmt->get_result();


/* SUBMIT */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $skills =
        trim($_POST["skills"]);

    $availability =
        $_POST["availability"];

    $experience =
        trim($_POST["experience"]);


    $stmt = $conn->prepare("
        INSERT INTO volunteer_application
        (
            user_id,
            skills,
            availability,
            experience
        )
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isss",
        $user_id,
        $skills,
        $availability,
        $experience
    );

    if ($stmt->execute()) {

        $message =
            "Your volunteer application has been submitted.";

    } else {

        $error =
            "Unable to submit application.";

    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>
Volunteer | Stray Paw
</title>

<link rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

<h1>
Become a Volunteer
</h1>

<p style="color:#78716c;margin-bottom:25px;">

Interested users can apply to help rescue
and care for stray animals.

</p>


<div class="form-card">

<?php if ($message): ?>

<p style="color:#708d68;margin-bottom:20px;">
<?= htmlspecialchars($message) ?>
</p>

<?php endif; ?>


<?php if ($error): ?>

<p style="color:#bd645c;margin-bottom:20px;">
<?= htmlspecialchars($error) ?>
</p>

<?php endif; ?>


<form method="POST">


<div class="form-group">

<label>Skills</label>

<textarea
name="skills"
placeholder="Animal handling, first aid, driving, etc."></textarea>

</div>


<div class="form-group">

<label>Availability</label>

<select name="availability">

<option value="Weekdays">
Weekdays
</option>

<option value="Weekends">
Weekends
</option>

<option value="Both">
Both
</option>

</select>

</div>


<div class="form-group">

<label>Previous Experience</label>

<textarea
name="experience"
placeholder="Describe any previous animal rescue or volunteer experience."></textarea>

</div>


<button
class="btn"
type="submit">

Apply as Volunteer

</button>

</form>

</div>

</main>

<?php include "includes/footer.php"; ?>

</body>

</html>
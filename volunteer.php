<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$message = "";
$error = "";

$stmt = $conn->prepare("
    SELECT *
    FROM volunteer_application
    WHERE user_id = ?
    ORDER BY application_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$existing = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $skills = trim($_POST["skills"] ?? "");
    $availability = $_POST["availability"] ?? "";
    $experience = trim($_POST["experience"] ?? "");

    if ($skills === "" || $experience === "") {

        $error = "Please fill in all required fields.";

    } elseif ($existing) {

        $error = "You have already submitted a volunteer application.";

    } else {

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

            $message = "Your volunteer application has been submitted.";

            $existing = [
                "skills" => $skills,
                "availability" => $availability,
                "experience" => $experience,
                "status" => "Pending"
            ];

        } else {

            $error = "Unable to submit application.";

        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Volunteer | Stray Paw</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

    <h1>Become a Volunteer</h1>

    <p style="color:#78716c; margin-bottom:25px;">
        Interested users can apply to help rescue and care for stray animals.
    </p>

    <div class="form-card">

        <?php if ($message): ?>

            <div class="success-message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <?php if ($error): ?>

            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <?php if ($existing && !$message): ?>

            <div class="application-status">

                <h3>Volunteer Application</h3>

                <p>
                    You have already submitted a volunteer application.
                </p>

                <strong>
                    Status:
                    <?= htmlspecialchars($existing["status"] ?? "Pending") ?>
                </strong>

            </div>

        <?php else: ?>

            <form method="POST">

                <div class="form-group">

                    <label for="skills">Skills</label>

                    <textarea
                        id="skills"
                        name="skills"
                        placeholder="Animal handling, first aid, driving, etc."
                        required
                    ></textarea>

                </div>

                <div class="form-group">

                    <label for="availability">Availability</label>

                    <select
                        id="availability"
                        name="availability"
                        required
                    >

                        <option value="Weekdays">Weekdays</option>
                        <option value="Weekends">Weekends</option>
                        <option value="Both">Both</option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="experience">Previous Experience</label>

                    <textarea
                        id="experience"
                        name="experience"
                        placeholder="Describe any previous animal rescue or volunteer experience."
                        required
                    ></textarea>

                </div>

                <button
                    class="btn"
                    type="submit"
                >
                    Apply as Volunteer
                </button>

            </form>

        <?php endif; ?>

    </div>

</main>

<?php include "includes/footer.php"; ?>

</body>

</html>
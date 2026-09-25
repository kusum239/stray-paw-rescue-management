<?php

require_once "includes/auth.php";
require_once "config/db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];

    $animal_type = $_POST["animal_type"] ?? "";
    $condition_type = $_POST["condition_type"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $emergency = $_POST["emergency"] ?? "No";

    if (
        empty($animal_type) ||
        empty($condition_type) ||
        empty($description) ||
        empty($location)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $upload_dir = "uploads/reports/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $photo_path = null;
        $video_path = null;

        if (
            isset($_FILES["photo"]) &&
            $_FILES["photo"]["error"] === UPLOAD_ERR_OK
        ) {

            $allowed_images = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            $extension = strtolower(
                pathinfo(
                    $_FILES["photo"]["name"],
                    PATHINFO_EXTENSION
                )
            );

            if (in_array($extension, $allowed_images)) {

                $photo_name = uniqid("photo_", true) . "." . $extension;
                $photo_path = $upload_dir . $photo_name;

                if (!move_uploaded_file(
                    $_FILES["photo"]["tmp_name"],
                    $photo_path
                )) {
                    $photo_path = null;
                }
            }
        }

        if (
            isset($_FILES["video"]) &&
            $_FILES["video"]["error"] === UPLOAD_ERR_OK
        ) {

            $allowed_videos = [
                "mp4",
                "webm",
                "mov"
            ];

            $extension = strtolower(
                pathinfo(
                    $_FILES["video"]["name"],
                    PATHINFO_EXTENSION
                )
            );

            if (in_array($extension, $allowed_videos)) {

                $video_name = uniqid("video_", true) . "." . $extension;
                $video_path = $upload_dir . $video_name;

                if (!move_uploaded_file(
                    $_FILES["video"]["tmp_name"],
                    $video_path
                )) {
                    $video_path = null;
                }
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO rescue_requests
            (
                user_id,
                animal_type,
                condition_type,
                photo,
                video,
                description,
                location,
                emergency,
                status,
                created_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())
        ");

        $stmt->bind_param(
            "isssssss",
            $user_id,
            $animal_type,
            $condition_type,
            $photo_path,
            $video_path,
            $description,
            $location,
            $emergency
        );

        if ($stmt->execute()) {

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Unable to submit the request.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Report an Animal | Stray Paw</title>

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

<h1>
Report a Stray or Injured Animal
</h1>

<p style="color:#78716c;margin-bottom:25px;">
Your report goes directly to our rescue coordinators. Please provide accurate details.
</p>

<div class="form-card">

<?php if ($error): ?>

<p style="color:#bd645c;margin-bottom:20px;">
<?= htmlspecialchars($error) ?>
</p>

<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="form-group">

<label>
Animal Type *
</label>

<select name="animal_type" required>

<option value="">
Select animal
</option>

<option value="Dog">
Dog
</option>

<option value="Cat">
Cat
</option>

</select>

</div>

<div class="form-group">

<label>
Condition *
</label>

<select name="condition_type" required>

<option value="">
Select condition
</option>

<option value="Injured">
Injured
</option>

<option value="Stray">
Stray
</option>

<option value="Abandoned">
Abandoned
</option>

</select>

</div>

<div class="form-group">

<label>
Photo
</label>

<input
type="file"
name="photo"
accept="image/*">

</div>

<div class="form-group">

<label>
Video
</label>

<input
type="file"
name="video"
accept="video/*">

</div>

<div class="form-group">

<label>
Description *
</label>

<textarea
name="description"
placeholder="Describe the animal's condition, appearance, behavior, etc."
required></textarea>

</div>

<div class="form-group">

<label>
Location *
</label>

<input
type="text"
name="location"
placeholder="Enter street, area or nearby landmark"
required>

</div>

<div class="form-group">

<label>
Immediate Danger?
</label>

<select name="emergency">

<option value="No">
No
</option>

<option value="Yes">
Yes, it's an emergency
</option>

</select>

</div>

<button type="submit" class="btn">
Submit Rescue Request
</button>

</form>

</div>

</main>

<?php include "includes/footer.php"; ?>

</body>

</html>
```php
<?php
require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $animal_type = trim($_POST["animal_type"] ?? "");
    $animal_condition = trim($_POST["condition_type"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $emergency = $_POST["emergency"] ?? "No";

    if (
        empty($animal_type) ||
        empty($animal_condition) ||
        empty($description) ||
        empty($location)
    ) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } else {

        $upload_dir = "uploads/reports/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = "";
        $video_name = "";

        if (
            isset($_FILES["photo"]) &&
            $_FILES["photo"]["error"] === UPLOAD_ERR_OK
        ) {

            $allowed_images = ["jpg", "jpeg", "png", "webp"];

            $original_name = $_FILES["photo"]["name"];
            $extension = strtolower(
                pathinfo($original_name, PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowed_images)) {
                $message = "Invalid photo format.";
                $message_type = "error";
            } else {

                $image_name = uniqid("image_", true) . "." . $extension;

                move_uploaded_file(
                    $_FILES["photo"]["tmp_name"],
                    $upload_dir . $image_name
                );
            }
        }

        if (
            empty($message) &&
            isset($_FILES["video"]) &&
            $_FILES["video"]["error"] === UPLOAD_ERR_OK
        ) {

            $allowed_videos = ["mp4", "webm", "mov"];

            $original_name = $_FILES["video"]["name"];
            $extension = strtolower(
                pathinfo($original_name, PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowed_videos)) {
                $message = "Invalid video format.";
                $message_type = "error";
            } else {

                $video_name = uniqid("video_", true) . "." . $extension;

                move_uploaded_file(
                    $_FILES["video"]["tmp_name"],
                    $upload_dir . $video_name
                );
            }
        }

        if (empty($message)) {

            $sql = "
                INSERT INTO rescue_requests
                (
                    user_id,
                    animal_type,
                    animal_condition,
                    description,
                    location,
                    emergency,
                    image,
                    video,
                    status,
                    created_at
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())
            ";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Database error: " . $conn->error);
            }

            $stmt->bind_param(
                "isssssss",
                $user_id,
                $animal_type,
                $animal_condition,
                $description,
                $location,
                $emergency,
                $image_name,
                $video_name
            );

            if ($stmt->execute()) {

                $stmt->close();

                header("Location: dashboard.php?report=success");
                exit;

            } else {

                $message = "Failed to submit the rescue request.";
                $message_type = "error";

                $stmt->close();
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

    <title>Report an Animal | Stray Paw</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            width: 90%;
            max-width: 750px;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 8px;
        }

        .required {
            color: red;
        }

        .emergency-box {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .emergency-box label {
            font-weight: normal;
            margin: 0;
        }

        .emergency-box input {
            width: auto;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .info {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>Report an Animal</h1>

        <p class="subtitle">
            Submit the details of an animal that needs rescue.
        </p>

        <?php if (!empty($message)): ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label>
                    Animal Type <span class="required">*</span>
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

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Animal Condition <span class="required">*</span>
                </label>

                <select name="condition_type" required>

                    <option value="">
                        Select condition
                    </option>

                    <option value="Injured">
                        Injured
                    </option>

                    <option value="Sick">
                        Sick
                    </option>

                    <option value="Abandoned">
                        Abandoned
                    </option>

                    <option value="Trapped">
                        Trapped
                    </option>

                    <option value="Homeless">
                        Homeless
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Description <span class="required">*</span>
                </label>

                <textarea
                    name="description"
                    placeholder="Describe the animal's condition and situation..."
                    required
                ></textarea>

            </div>

            <div class="form-group">

                <label>
                    Location <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="location"
                    placeholder="Where is the animal located?"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Is this an emergency?
                </label>

                <div class="emergency-box">

                    <label>
                        <input
                            type="radio"
                            name="emergency"
                            value="Yes"
                        >
                        Yes
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="emergency"
                            value="No"
                            checked
                        >
                        No
                    </label>

                </div>

            </div>

            <div class="form-group">

                <label>
                    Animal Photo
                </label>

                <input
                    type="file"
                    name="photo"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="info">
                    Accepted: JPG, JPEG, PNG, WEBP
                </div>

            </div>

            <div class="form-group">

                <label>
                    Animal Video
                </label>

                <input
                    type="file"
                    name="video"
                    accept=".mp4,.webm,.mov"
                >

                <div class="info">
                    Accepted: MP4, WEBM, MOV
                </div>

            </div>

            <button type="submit">
                Submit Rescue Request
            </button>

        </form>

    </div>

</div>

</body>

</html>
```

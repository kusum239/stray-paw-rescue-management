<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true
]);

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

require_once "../config/db.php";

$request_id = intval($_GET['id'] ?? 0);
$request_data = null;

if ($request_id > 0) {

    $col_check = $conn->query(
        "SHOW COLUMNS FROM rescue_requests LIKE 'request_id'"
    );

    $id_col = ($col_check && $col_check->num_rows > 0)
        ? 'request_id'
        : 'id';

    $stmt = $conn->prepare(
        "SELECT * FROM rescue_requests WHERE `$id_col` = ? LIMIT 1"
    );

    if ($stmt) {

        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        $request_data = $stmt->get_result()->fetch_assoc();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Request Details - Admin</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f4f7f6;
    padding: 25px;
}

.nav-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #1b4d3e;
    padding: 15px 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    color: white;
}

.nav-bar a {
    color: #cfdfda;
    text-decoration: none;
    font-weight: 600;
    margin-left: 15px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 0 auto;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #e1e8e5;
    padding-bottom: 10px;
}

.detail-row {
    display: flex;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.detail-label {
    font-weight: bold;
    width: 180px;
    color: #34495e;
}

.detail-value {
    color: #2c3e50;
    flex: 1;
}

.request-img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-top: 10px;
    border: 1px solid #ddd;
}

.request-video {
    max-width: 100%;
    margin-top: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.back-btn {
    background: #6c757d;
    color: white;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.9rem;
}

.media-error {
    color: #bd645c;
    margin-top: 8px;
}

</style>

</head>

<body>

<div class="nav-bar">

    <div style="font-size: 1.2rem; font-weight: bold;">
        🐾 Stray Paw Admin
    </div>

    <div>

        <a href="reports.php">Dashboard</a>

        <a
            href="requests.php"
            style="color:white; text-decoration: underline;"
        >
            Requests
        </a>

        <a href="animals.php">
            Animals
        </a>

        <a
            href="../logout.php"
            style="color: #ff9999;"
        >
            Logout
        </a>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h2>
            Rescue Request Details #<?php echo $request_id; ?>
        </h2>

        <a
            href="requests.php"
            class="back-btn"
        >
            ← Back to Requests
        </a>

    </div>

    <?php if ($request_data): ?>

        <div class="detail-row">

            <div class="detail-label">
                Animal Type:
            </div>

            <div class="detail-value">
                <?php
                echo htmlspecialchars(
                    $request_data['animal_type']
                    ?? $request_data['animal']
                    ?? 'N/A'
                );
                ?>
            </div>

        </div>

        <div class="detail-row">

            <div class="detail-label">
                Condition:
            </div>

            <div class="detail-value">
                <?php
                echo htmlspecialchars(
                    $request_data['condition_type']
                    ?? $request_data['animal_condition']
                    ?? $request_data['condition']
                    ?? 'N/A'
                );
                ?>
            </div>

        </div>

        <div class="detail-row">

            <div class="detail-label">
                Location:
            </div>

            <div class="detail-value">
                <?php
                echo htmlspecialchars(
                    $request_data['location'] ?? 'N/A'
                );
                ?>
            </div>

        </div>

        <div class="detail-row">

            <div class="detail-label">
                Reporter Name:
            </div>

            <div class="detail-value">
                <?php
                echo htmlspecialchars(
                    $request_data['reporter_name']
                    ?? 'Anonymous'
                );
                ?>
            </div>

        </div>

        <div class="detail-row">

            <div class="detail-label">
                Status:
            </div>

            <div class="detail-value">

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $request_data['status'] ?? 'Pending'
                    );
                    ?>
                </strong>

            </div>

        </div>

        <div class="detail-row">

            <div class="detail-label">
                Description:
            </div>

            <div class="detail-value">

                <?php
                echo nl2br(
                    htmlspecialchars(
                        $request_data['description']
                        ?? 'No description provided.'
                    )
                );
                ?>

            </div>

        </div>

        <?php

        $photo = $request_data['photo'] ?? '';

        if (!empty($photo)) {

            if (strpos($photo, 'uploads/') !== 0) {
                $photo = 'uploads/reports/' . basename($photo);
            }

            $photo_url = '../' . ltrim($photo, '/');
        }

        ?>

        <?php if (!empty($photo)): ?>

            <div class="detail-row">

                <div class="detail-label">
                    Photo Evidence:
                </div>

                <div class="detail-value">

                    <img
                        src="<?php echo htmlspecialchars($photo_url); ?>"
                        class="request-img"
                        alt="Request Photo"
                        onerror="this.style.display='none'; document.getElementById('photo-error').style.display='block';"
                    >

                    <p
                        id="photo-error"
                        class="media-error"
                        style="display:none;"
                    >
                        Photo could not be loaded.
                    </p>

                </div>

            </div>

        <?php endif; ?>


        <?php

        $video = $request_data['video'] ?? '';

        if (!empty($video)) {

            if (strpos($video, 'uploads/') !== 0) {
                $video = 'uploads/reports/' . basename($video);
            }

            $video_url = '../' . ltrim($video, '/');
        }

        ?>

        <?php if (!empty($video)): ?>

            <div class="detail-row">

                <div class="detail-label">
                    Video Evidence:
                </div>

                <div class="detail-value">

                    <video
                        class="request-video"
                        controls
                    >

                        <source
                            src="<?php echo htmlspecialchars($video_url); ?>"
                        >

                        Your browser does not support video playback.

                    </video>

                </div>

            </div>

        <?php endif; ?>


    <?php else: ?>

        <p
            style="
                text-align:center;
                color:#7f8c8d;
                padding:20px;
            "
        >
            Rescue request record not found.
        </p>

    <?php endif; ?>

</div>

</body>

</html>
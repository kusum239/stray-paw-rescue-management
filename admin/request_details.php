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

$request = null;

if ($request_id > 0) {

    $stmt = $conn->prepare("
        SELECT *
        FROM rescue_requests
        WHERE request_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $request_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $request = $result->fetch_assoc();

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Request Details</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f6;
            padding: 25px;
        }

        .nav-bar {
            background: #1b4d3e;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 25px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-bar a {
            color: #cfdfda;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }

        .card {
            background: white;
            max-width: 900px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #1b4d3e;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 5px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 20px;

            padding: 14px 0;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
            color: #34495e;
        }

        .value {
            color: #333;
            line-height: 1.6;
        }

        .status {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
        }

        .media-section {
            margin-top: 30px;
        }

        .media-section h3 {
            color: #1b4d3e;
            margin-bottom: 15px;
        }

        .photo {
            max-width: 500px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        video {
            max-width: 700px;
            width: 100%;
            border-radius: 10px;
            background: black;
        }

        .no-media {
            color: #888;
            font-style: italic;
        }

        .not-found {
            text-align: center;
            padding: 40px;
        }

    </style>

</head>

<body>

<div class="nav-bar">

    <strong>🐾 Stray Paw Admin</strong>

    <div>

        <a href="reports.php">Dashboard</a>

        <a href="requests.php">Requests</a>

        <a href="animals.php">Animals</a>

        <a href="shelters.php">Shelters</a>

        <a href="volunteers.php">Volunteers</a>

        <a href="../logout.php">
            Logout
        </a>

    </div>

</div>


<div class="card">

<?php if ($request): ?>

    <div class="header">

        <h2>
            Rescue Request #<?php echo $request['request_id']; ?>
        </h2>

        <a href="requests.php" class="back-btn">
            ← Back
        </a>

    </div>


    <div class="detail-row">

        <div class="label">
            User ID
        </div>

        <div class="value">
            <?php echo htmlspecialchars($request['user_id']); ?>
        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Animal Type
        </div>

        <div class="value">
            <?php echo htmlspecialchars($request['animal_type']); ?>
        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Condition
        </div>

        <div class="value">
            <?php echo htmlspecialchars($request['animal_condition']); ?>
        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Location
        </div>

        <div class="value">
            <?php echo nl2br(
                htmlspecialchars($request['location'])
            ); ?>
        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Description
        </div>

        <div class="value">
            <?php echo nl2br(
                htmlspecialchars($request['description'])
            ); ?>
        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Status
        </div>

        <div class="value">

            <span class="status">

                <?php
                echo htmlspecialchars(
                    $request['status']
                );
                ?>

            </span>

        </div>

    </div>


    <div class="detail-row">

        <div class="label">
            Submitted At
        </div>

        <div class="value">
            <?php echo htmlspecialchars(
                $request['created_at']
            ); ?>
        </div>

    </div>


    <!-- PHOTO -->

    <div class="media-section">

        <h3>📷 Animal Photo</h3>

        <?php if (!empty($request['image'])): ?>

            <img
                class="photo"
                src="../uploads/reports/<?php
                    echo htmlspecialchars(
                        basename($request['image'])
                    );
                ?>"
                alt="Animal Photo"
            >

        <?php else: ?>

            <p class="no-media">
                No photo submitted.
            </p>

        <?php endif; ?>

    </div>


    <!-- VIDEO -->

    <div class="media-section">

        <h3>🎥 Animal Video</h3>

        <?php if (!empty($request['video'])): ?>

            <video controls>

                <source
                    src="../uploads/reports/<?php
                        echo htmlspecialchars(
                            basename($request['video'])
                        );
                    ?>"
                    type="video/mp4"
                >

                Your browser does not support video.

            </video>

        <?php else: ?>

            <p class="no-media">
                No video submitted.
            </p>

        <?php endif; ?>

    </div>


<?php else: ?>

    <div class="not-found">

        <h2>Request Not Found</h2>

        <p>
            No rescue request exists with ID
            #<?php echo $request_id; ?>
        </p>

        <br>

        <a href="requests.php">
            ← Back to Requests
        </a>

    </div>

<?php endif; ?>

</div>

</body>

</html>
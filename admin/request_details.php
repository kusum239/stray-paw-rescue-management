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

$request_id = intval($_GET['id'] ?? $_POST['request_id'] ?? 0);
$request_data = null;
$volunteers = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_request"])) {

    $request_id = intval($_POST["request_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");
    $volunteer_id = $_POST["assigned_volunteer_id"] ?? "";

    $allowed_status = [
        "Pending",
        "In Progress",
        "Rescued",
        "Completed",
        "Rejected"
    ];

    if ($request_id > 0 && in_array($status, $allowed_status, true)) {

        if ($volunteer_id === "" || $volunteer_id === "0") {
            $volunteer_id = null;
        } else {
            $volunteer_id = intval($volunteer_id);
        }

        $stmt = $conn->prepare("
            UPDATE rescue_requests
            SET status = ?, assigned_volunteer_id = ?
            WHERE request_id = ?
        ");

        if ($stmt) {
            $stmt->bind_param(
                "sii",
                $status,
                $volunteer_id,
                $request_id
            );

            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: request_details.php?id=" . $request_id);
    exit();
}


if ($request_id > 0) {

    $stmt = $conn->prepare("
        SELECT *
        FROM rescue_requests
        WHERE request_id = ?
        LIMIT 1
    ");

    if ($stmt) {
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        $request_data = $stmt->get_result()->fetch_assoc();

        $stmt->close();
    }
}


$stmt = $conn->prepare("
    SELECT
        v.volunteer_id,
        v.user_id,
        u.name,
        u.email
    FROM volunteers v
    INNER JOIN users u ON v.user_id = u.user_id
    WHERE v.status IN ('Active', 'Approved')
    ORDER BY u.name ASC
");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $volunteers[] = $row;
    }

    $stmt->close();
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

.update-card {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e1e8e5;
}

.update-card h3 {
    margin-bottom: 18px;
    color: #1b4d3e;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 7px;
    color: #34495e;
}

.form-group select {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccd6d2;
    border-radius: 6px;
    background: white;
    font-size: 0.95rem;
}

.update-btn {
    background: #1b4d3e;
    color: white;
    border: none;
    padding: 11px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 600;
}

.update-btn:hover {
    background: #143b30;
}

.no-volunteer {
    color: #777;
    font-size: 0.9rem;
    margin-top: 5px;
}

</style>

</head>

<body>

<div class="nav-bar">

    <div style="font-size: 1.2rem; font-weight: bold;">
        🐾 Stray Paw Admin
    </div>

    <div>

        <a href="reports.php">
            Dashboard
        </a>

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
            Rescue Request Details #<?= htmlspecialchars($request_id) ?>
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
                <?= htmlspecialchars($request_data['animal_type'] ?? 'N/A') ?>
            </div>

        </div>


        <div class="detail-row">

            <div class="detail-label">
                Condition:
            </div>

            <div class="detail-value">
                <?= htmlspecialchars($request_data['condition_type'] ?? 'N/A') ?>
            </div>

        </div>


        <div class="detail-row">

            <div class="detail-label">
                Location:
            </div>

            <div class="detail-value">
                <?= htmlspecialchars($request_data['location'] ?? 'N/A') ?>
            </div>

        </div>


        <div class="detail-row">

            <div class="detail-label">
                Emergency:
            </div>

            <div class="detail-value">
                <?= htmlspecialchars($request_data['emergency'] ?? 'No') ?>
            </div>

        </div>


        <div class="detail-row">

            <div class="detail-label">
                Status:
            </div>

            <div class="detail-value">

                <strong>
                    <?= htmlspecialchars($request_data['status'] ?? 'Pending') ?>
                </strong>

            </div>

        </div>


        <div class="detail-row">

            <div class="detail-label">
                Description:
            </div>

            <div class="detail-value">

                <?= nl2br(
                    htmlspecialchars(
                        $request_data['description']
                        ?? 'No description provided.'
                    )
                ) ?>

            </div>

        </div>


        <?php if (!empty($request_data['photo'])): ?>

            <div class="detail-row">

                <div class="detail-label">
                    Photo Evidence:
                </div>

                <div class="detail-value">

                    <img
                        src="../uploads/reports/<?= htmlspecialchars(
                            basename($request_data['photo'])
                        ) ?>"
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


        <?php if (!empty($request_data['video'])): ?>

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
                            src="../uploads/reports/<?= htmlspecialchars(
                                basename($request_data['video'])
                            ) ?>"
                        >

                        Your browser does not support video playback.

                    </video>

                </div>

            </div>

        <?php endif; ?>


        <div class="update-card">

            <h3>
                Update Rescue Request
            </h3>

            <form method="POST">

                <input
                    type="hidden"
                    name="request_id"
                    value="<?= htmlspecialchars($request_id) ?>"
                >

                <div class="form-group">

                    <label for="status">
                        Request Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <?php
                        $current_status = $request_data['status'] ?? 'Pending';
                        ?>

                        <option
                            value="Pending"
                            <?= $current_status === 'Pending' ? 'selected' : '' ?>
                        >
                            Pending
                        </option>

                        <option
                            value="In Progress"
                            <?= $current_status === 'In Progress' ? 'selected' : '' ?>
                        >
                            In Progress
                        </option>

                        <option
                            value="Rescued"
                            <?= $current_status === 'Rescued' ? 'selected' : '' ?>
                        >
                            Rescued
                        </option>

                        <option
                            value="Completed"
                            <?= $current_status === 'Completed' ? 'selected' : '' ?>
                        >
                            Completed
                        </option>

                        <option
                            value="Rejected"
                            <?= $current_status === 'Rejected' ? 'selected' : '' ?>
                        >
                            Rejected
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="assigned_volunteer_id">
                        Assign Volunteer
                    </label>

                    <select
                        name="assigned_volunteer_id"
                        id="assigned_volunteer_id"
                    >

                        <option value="0">
                            -- No Volunteer Assigned --
                        </option>

                        <?php foreach ($volunteers as $volunteer): ?>

                            <option
                                value="<?= htmlspecialchars($volunteer['volunteer_id']) ?>"
                                <?= (
                                    isset($request_data['assigned_volunteer_id']) &&
                                    $request_data['assigned_volunteer_id'] == $volunteer['volunteer_id']
                                ) ? 'selected' : '' ?>
                            >

                                <?= htmlspecialchars($volunteer['name']) ?>

                                <?php if (!empty($volunteer['email'])): ?>
                                    - <?= htmlspecialchars($volunteer['email']) ?>
                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <?php if (empty($volunteers)): ?>

                        <p class="no-volunteer">
                            No active or approved volunteers are available.
                        </p>

                    <?php endif; ?>

                </div>


                <button
                    type="submit"
                    name="update_request"
                    class="update-btn"
                >
                    Update Request
                </button>

            </form>

        </div>


    <?php else: ?>

        <p style="
            text-align:center;
            color:#7f8c8d;
            padding:20px;
        ">
            Rescue request record not found.
        </p>

    <?php endif; ?>

</div>

</body>

</html>
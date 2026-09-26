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

/* --------------------------------
   FIND PRIMARY ID COLUMN
--------------------------------- */
$id_col = "id";

$check_id = $conn->query("SHOW COLUMNS FROM rescue_requests LIKE 'request_id'");

if ($check_id && $check_id->num_rows > 0) {
    $id_col = "request_id";
}

/* --------------------------------
   UPDATE STATUS
--------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request_id = intval($_POST['request_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowed_statuses = [
        'Pending',
        'Accepted',
        'Rejected',
        'In Progress',
        'Rescued'
    ];

    if ($request_id > 0 && in_array($status, $allowed_statuses, true)) {

        $stmt = $conn->prepare(
            "UPDATE rescue_requests 
             SET status = ? 
             WHERE `$id_col` = ?"
        );

        if ($stmt) {
            $stmt->bind_param("si", $status, $request_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: requests.php");
    exit();
}

/* --------------------------------
   GET ALL REQUESTS
--------------------------------- */

$requests = [];

$sql = "SELECT * FROM rescue_requests";

$result = $conn->query($sql);

if ($result === false) {

    die(
        "<div style='
            margin:30px;
            padding:20px;
            background:#ffe6e6;
            color:#b00000;
            border:1px solid #ff9999;
            font-family:Arial;
        '>
        <h3>Database Error</h3>
        <p>" . htmlspecialchars($conn->error) . "</p>
        </div>"
    );
}

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Rescue Requests - Admin</title>

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

        .nav-bar a:hover {
            color: white;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        h2 {
            margin-bottom: 20px;
            color: #1b4d3e;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f1f5f3;
            color: #34495e;
        }

        tr:hover {
            background: #fafafa;
        }

        .status {
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .accepted {
            background: #d1ecf1;
            color: #0c5460;
        }

        .progress {
            background: #cce5ff;
            color: #004085;
        }

        .rescued {
            background: #d4edda;
            color: #155724;
        }

        .rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .media-btn {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 5px;
            background: #e8f1ee;
            color: #1b4d3e;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }

        .view-btn {
            display: inline-block;
            background: #1b4d3e;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }

        .view-btn:hover {
            background: #14392e;
        }

        .action-form {
            margin-top: 6px;
        }

        .action-form button {
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            color: white;
        }

        .accept {
            background: #198754;
        }

        .reject {
            background: #dc3545;
        }

        .start {
            background: #0d6efd;
        }

        .rescue {
            background: #6f42c1;
        }

        .description {
            max-width: 220px;
            white-space: normal;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>
</head>

<body>

<div class="nav-bar">

    <div style="font-size:1.2rem;font-weight:bold;">
        🐾 Stray Paw Admin
    </div>

    <div>
        <a href="reports.php">Dashboard</a>

        <a href="requests.php"
           style="color:white;text-decoration:underline;">
            Requests
        </a>

        <a href="animals.php">
            Animals
        </a>

        <a href="shelters.php">
            Shelters
        </a>

        <a href="volunteers.php">
            Volunteers
        </a>

        <a href="../logout.php"
           style="color:#ff9999;">
            Logout
        </a>
    </div>

</div>


<div class="card">

    <h2>All Rescue Requests</h2>

    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Animal</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th>Emergency</th>
                    <th>Reporter</th>
                    <th>Photo</th>
                    <th>Video</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

            <?php if (!empty($requests)): ?>

                <?php foreach ($requests as $req): ?>

                    <?php

                    $curr_id = $req[$id_col] ?? 0;

                    $animal =
                        $req['animal_type']
                        ?? $req['animal']
                        ?? 'N/A';

                    $condition =
                        $req['condition_type']
                        ?? $req['animal_condition']
                        ?? $req['condition']
                        ?? 'N/A';

                    $location =
                        $req['location']
                        ?? 'N/A';

                    $emergency =
                        $req['emergency']
                        ?? 'No';

                    $reporter =
                        $req['reporter_name']
                        ?? $req['user_id']
                        ?? 'Anonymous';

                    $photo =
                        $req['photo']
                        ?? $req['image']
                        ?? '';

                    $video =
                        $req['video']
                        ?? '';

                    $description =
                        $req['description']
                        ?? 'No description';

                    $status =
                        $req['status']
                        ?? 'Pending';

                    ?>

                    <tr>

                        <!-- ID -->
                        <td>
                            #<?php echo htmlspecialchars($curr_id); ?>
                        </td>

                        <!-- Animal -->
                        <td>
                            <?php echo htmlspecialchars($animal); ?>
                        </td>

                        <!-- Condition -->
                        <td>
                            <?php echo htmlspecialchars($condition); ?>
                        </td>

                        <!-- Location -->
                        <td>
                            <?php echo htmlspecialchars($location); ?>
                        </td>

                        <!-- Emergency -->
                        <td>
                            <?php
                            echo (
                                strtolower($emergency) === 'yes'
                                || $emergency == 1
                            )
                            ? '🚨 Yes'
                            : 'No';
                            ?>
                        </td>

                        <!-- Reporter -->
                        <td>
                            <?php echo htmlspecialchars($reporter); ?>
                        </td>

                        <!-- PHOTO -->
                        <td>

                            <?php if (!empty($photo)): ?>

                                <a
                                    href="../uploads/reports/<?php echo htmlspecialchars(basename($photo)); ?>"
                                    target="_blank"
                                    class="media-btn"
                                >
                                    🖼 View Photo
                                </a>

                            <?php else: ?>

                                No Photo

                            <?php endif; ?>

                        </td>

                        <!-- VIDEO -->
                        <td>

                            <?php if (!empty($video)): ?>

                                <a
                                    href="../uploads/reports/<?php echo htmlspecialchars(basename($video)); ?>"
                                    target="_blank"
                                    class="media-btn"
                                >
                                    🎥 View Video
                                </a>

                            <?php else: ?>

                                No Video

                            <?php endif; ?>

                        </td>

                        <!-- DESCRIPTION -->
                        <td class="description">

                            <?php
                            echo nl2br(
                                htmlspecialchars($description)
                            );
                            ?>

                        </td>

                        <!-- STATUS -->
                        <td>

                            <?php

                            $status_class = 'pending';

                            if ($status === 'Accepted') {
                                $status_class = 'accepted';
                            }

                            if ($status === 'In Progress') {
                                $status_class = 'progress';
                            }

                            if ($status === 'Rescued') {
                                $status_class = 'rescued';
                            }

                            if ($status === 'Rejected') {
                                $status_class = 'rejected';
                            }

                            ?>

                            <span class="status <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>

                        </td>

                        <!-- ACTION -->
                        <td>

                            <a
                                href="request_details.php?id=<?php echo $curr_id; ?>"
                                class="view-btn"
                            >
                                👁 View
                            </a>

                            <?php if ($status === 'Pending'): ?>

                                <form method="POST" class="action-form">

                                    <input
                                        type="hidden"
                                        name="request_id"
                                        value="<?php echo $curr_id; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="Accepted"
                                    >

                                    <button
                                        type="submit"
                                        class="accept"
                                    >
                                        ✓ Accept
                                    </button>

                                </form>

                                <form method="POST" class="action-form">

                                    <input
                                        type="hidden"
                                        name="request_id"
                                        value="<?php echo $curr_id; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="Rejected"
                                    >

                                    <button
                                        type="submit"
                                        class="reject"
                                    >
                                        ✕ Reject
                                    </button>

                                </form>

                            <?php elseif ($status === 'Accepted'): ?>

                                <form method="POST" class="action-form">

                                    <input
                                        type="hidden"
                                        name="request_id"
                                        value="<?php echo $curr_id; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="In Progress"
                                    >

                                    <button
                                        type="submit"
                                        class="start"
                                    >
                                        Start Rescue
                                    </button>

                                </form>

                            <?php elseif ($status === 'In Progress'): ?>

                                <form method="POST" class="action-form">

                                    <input
                                        type="hidden"
                                        name="request_id"
                                        value="<?php echo $curr_id; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="Rescued"
                                    >

                                    <button
                                        type="submit"
                                        class="rescue"
                                    >
                                        ✓ Mark Rescued
                                    </button>

                                </form>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="11" class="no-data">
                        No rescue requests found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
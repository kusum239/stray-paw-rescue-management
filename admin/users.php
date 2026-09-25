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

$users = [];
$id_col = 'id';

// Check if users table exists
$table_check = $conn->query("SHOW TABLES LIKE 'users'");

if ($table_check && $table_check->num_rows > 0) {

    // Find the ID column
    $col_res = $conn->query("SHOW COLUMNS FROM users");

    if ($col_res) {
        while ($c = $col_res->fetch_assoc()) {

            if (in_array(
                strtolower($c['Field']),
                ['id', 'user_id', 'userid']
            )) {
                $id_col = $c['Field'];
                break;
            }
        }
    }

    // Get users
    $res = $conn->query(
        "SELECT * FROM users ORDER BY `$id_col` DESC"
    );

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $users[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registered Users | Stray Paw Admin</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;
        }

        body {
            background: #f3f7f6;
            padding: 28px;
            color: #1f2937;
        }

        /* =========================
           NAVIGATION BAR
        ========================= */

        .nav-bar {
            width: 100%;
            min-height: 67px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background: #195444;

            padding: 0 28px;

            border-radius: 9px;

            margin-bottom: 30px;
        }

        /* Logo */

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;

            color: white;

            font-size: 21px;
            font-weight: 700;

            white-space: nowrap;
        }

        .paw {
            font-size: 20px;
        }

        /* Navigation */

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #ffffff;
            text-decoration: none;

            font-size: 16px;
            font-weight: 600;

            padding: 4px 0;
        }

        .nav-links a:hover {
            opacity: 0.85;
        }

        /* Active Users link */

        .nav-links a.active {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* Logout */

        .nav-links a.logout {
            color: #ff8f8f;
        }

        /* =========================
           MAIN CARD
        ========================= */

        .card {
            background: #ffffff;

            border-radius: 13px;

            padding: 32px 30px;

            box-shadow:
                0 4px 14px rgba(0, 0, 0, 0.06);
        }

        .card h2 {
            color: #263b50;

            font-size: 27px;

            font-weight: 700;

            margin-bottom: 25px;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            text-align: left;
        }

        thead {
            background: #f5f7f9;
        }

        th {
            color: #29415a;

            font-size: 15px;

            font-weight: 700;

            padding: 17px 18px;

            border-bottom: 1px solid #dce3e7;
        }

        td {
            padding: 18px;

            font-size: 16px;

            color: #151515;

            border-bottom: 1px solid #e1e7e5;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* ID */

        .user-id {
            color: #222;
            font-weight: 500;
        }

        /* Empty name */

        .empty-name {
            color: #555;
        }

        /* Joined date */

        .joined {
            color: #111;
            white-space: nowrap;
        }

        /* Empty table */

        .empty-message {
            text-align: center;

            color: #7f8c8d;

            padding: 30px !important;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            body {
                padding: 15px;
            }

            .nav-bar {
                flex-direction: column;
                align-items: flex-start;

                padding: 18px;

                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                gap: 15px;
            }

            .card {
                padding: 25px 18px;
            }

        }

    </style>

</head>

<body>

<!-- =========================
     NAVIGATION
========================= -->

<div class="nav-bar">

    <div class="brand">
        <span class="paw">🐾</span>
        <span>Stray Paw Admin</span>
    </div>

    <div class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="requests.php">
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

        <a href="users.php" class="active">
            Users
        </a>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </div>

</div>


<!-- =========================
     REGISTERED USERS
========================= -->

<div class="card">

    <h2>Registered Users</h2>

    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Role / Joined</th>

                </tr>

            </thead>

            <tbody>

                <?php if (!empty($users)): ?>

                    <?php foreach ($users as $user): ?>

                        <?php

                        $u_id = $user[$id_col] ?? '';

                        /*
                         * Use name if the database has it.
                         * Otherwise use username.
                         * If neither exists, leave it blank.
                         */
                        $u_name = '';

                        if (isset($user['name'])) {
                            $u_name = $user['name'];
                        } elseif (isset($user['username'])) {
                            $u_name = $user['username'];
                        }

                        $u_email = $user['email'] ?? '';

                        /*
                         * The screenshot shows the joined date.
                         */
                        $u_joined = '';

                        if (!empty($user['created_at'])) {

                            $u_joined = date(
                                'Y-m-d H:i:s',
                                strtotime($user['created_at'])
                            );

                        } elseif (!empty($user['joined_at'])) {

                            $u_joined = date(
                                'Y-m-d H:i:s',
                                strtotime($user['joined_at'])
                            );

                        } elseif (!empty($user['created'])) {

                            $u_joined = date(
                                'Y-m-d H:i:s',
                                strtotime($user['created'])
                            );
                        }

                        ?>

                        <tr>

                            <td class="user-id">
                                #<?= htmlspecialchars($u_id) ?>
                            </td>

                            <td class="empty-name">
                                <?= htmlspecialchars($u_name) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($u_email) ?>
                            </td>

                            <td class="joined">
                                <?= htmlspecialchars($u_joined) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="4"
                            class="empty-message">

                            No registered users found.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>
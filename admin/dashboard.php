<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin_login.php");
    exit();
}

$admin_name = $_SESSION["admin_name"] ?? "Admin";

$total_requests = 0;
$pending_requests = 0;
$total_treatments = 0;
$total_volunteers = 0;

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM rescue_requests
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_requests = $row["total"];
}

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM rescue_requests
    WHERE status = 'Pending'
");

if ($result) {
    $row = $result->fetch_assoc();
    $pending_requests = $row["total"];
}

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM treatment_history
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_treatments = $row["total"];
}

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM volunteer_application
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_volunteers = $row["total"];
}

$recent_requests = [];

$result = $conn->query("
    SELECT
        r.request_id,
        u.name,
        r.location,
        r.status,
        r.created_at
    FROM rescue_requests r
    LEFT JOIN users u
        ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
    LIMIT 5
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_requests[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Stray Paw</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <style>
 /* Admin Dashboard */

.admin-layout {
    min-height: 100vh;
    display: flex;
    background: #faf9f7;
    font-family: "Poppins", sans-serif;
}

.admin-sidebar {
    width: 250px;
    min-height: 100vh;
    background: #ffffff;
    color: #30262d;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #e9e2de;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 25px 22px;
    border-bottom: 1px solid #eee7e3;
}

.sidebar-paw {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #f8e5e5;
    color: #bc6b72;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.sidebar-brand h2 {
    margin: 0;
    font-size: 20px;
    color: #30262d;
}

.sidebar-brand span {
    font-size: 12px;
    color: #918487;
}

.admin-nav {
    padding: 25px 14px;
    flex: 1;
}

.admin-nav a,
.sidebar-bottom a {
    display: flex;
    align-items: center;
    gap: 13px;
    color: #74686d;
    text-decoration: none;
    padding: 13px 15px;
    border-radius: 11px;
    margin-bottom: 6px;
    font-size: 14px;
}

.admin-nav a i,
.sidebar-bottom a i {
    width: 20px;
    text-align: center;
}

.admin-nav a:hover,
.admin-nav a.active {
    background: #bc6b72;
    color: #ffffff;
}

.sidebar-bottom {
    padding: 15px;
    border-top: 1px solid #eee7e3;
}

.sidebar-bottom a:hover {
    background: #f8e5e5;
    color: #bc6b72;
}

.logout-link {
    color: #74686d !important;
}

.admin-main {
    margin-left: 250px;
    width: calc(100% - 250px);
    min-height: 100vh;
    background: #faf9f7;
}

.admin-topbar {
    height: 78px;
    background: #ffffff;
    border-bottom: 1px solid #e9e2de;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 35px;
}

.admin-topbar h1 {
    margin: 0;
    font-size: 25px;
    color: #30262d;
}

.admin-topbar p {
    margin: 4px 0 0;
    color: #918487;
    font-size: 13px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 11px;
}

.admin-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #f8dfe1;
    color: #bc6b72;
    display: flex;
    align-items: center;
    justify-content: center;
}

.admin-profile strong {
    display: block;
    color: #30262d;
    font-size: 13px;
}

.admin-profile small {
    color: #918487;
    font-size: 11px;
}

.dashboard-welcome {
    margin: 30px 35px 25px;
    padding: 28px 32px;
    border-radius: 14px;
    background: #f8e5e5;
    color: #30262d;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.welcome-small {
    font-size: 11px;
    letter-spacing: 1.5px;
    color: #bc6b72;
}

.dashboard-welcome h2 {
    margin: 8px 0;
    font-size: 24px;
    color: #30262d;
}

.dashboard-welcome p {
    margin: 0;
    font-size: 13px;
    color: #827477;
}

.welcome-icon {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    background: #ffffff;
    color: #bc6b72;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    padding: 0 35px;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e9e2de;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f8e5e5;
    color: #bc6b72;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
}

.stat-icon.pending {
    background: #fbf0da;
    color: #b4863c;
}

.stat-icon.rescued {
    background: #e5f0e5;
    color: #71996b;
}

.stat-icon.volunteers {
    background: #eee5f4;
    color: #876b9a;
}

.stat-card span {
    color: #918487;
    font-size: 12px;
}

.stat-card h3 {
    margin: 4px 0 0;
    font-size: 25px;
    color: #30262d;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1.7fr 1fr;
    gap: 20px;
    padding: 25px 35px 35px;
}

.dashboard-card {
    background: #ffffff;
    border: 1px solid #e9e2de;
    border-radius: 14px;
    overflow: hidden;
}

.card-header {
    padding: 20px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #eee7e3;
}

.card-header h3 {
    margin: 0;
    color: #30262d;
    font-size: 16px;
}

.card-header p {
    margin: 4px 0 0;
    color: #918487;
    font-size: 11px;
}

.card-header a {
    color: #bc6b72;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
}

.dashboard-card table {
    width: 100%;
    border-collapse: collapse;
}

.dashboard-card th {
    text-align: left;
    background: #faf9f7;
    color: #76696e;
    font-size: 11px;
    padding: 13px 18px;
    text-transform: uppercase;
}

.dashboard-card td {
    padding: 15px 18px;
    border-top: 1px solid #eee7e3;
    color: #30262d;
    font-size: 13px;
}

.status {
    display: inline-block;
    padding: 5px 11px;
    border-radius: 20px;
    background: #eee9eb;
    color: #756a6e;
    font-size: 11px;
}

.status.pending {
    background: #fbf0da;
    color: #a97825;
}

.status.approved,
.status.rescued,
.status.completed {
    background: #e5f0e5;
    color: #63845d;
}

.status.rejected {
    background: #f8e1df;
    color: #a95d5a;
}

.quick-actions {
    padding: 10px 18px 18px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 4px;
    text-decoration: none;
    color: #30262d;
    border-bottom: 1px solid #eee7e3;
}

.quick-action:last-child {
    border-bottom: none;
}

.quick-action:hover strong {
    color: #bc6b72;
}

.quick-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #f8e5e5;
    color: #bc6b72;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-action div:nth-child(2) {
    flex: 1;
}

.quick-action strong {
    display: block;
    font-size: 12px;
}

.quick-action span {
    display: block;
    color: #918487;
    font-size: 10px;
    margin-top: 3px;
}

.quick-action > i {
    color: #b6a8ac;
    font-size: 11px;
}

.empty-message {
    text-align: center;
    padding: 35px !important;
    color: #918487 !important;
}

.empty-message i {
    display: block;
    margin-bottom: 8px;
    color: #bc6b72;
    font-size: 24px;
}

@media (max-width: 1100px) {
    .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-sidebar {
        width: 70px;
    }

    .sidebar-brand {
        justify-content: center;
        padding: 20px 10px;
    }

    .sidebar-brand > div:last-child,
    .admin-nav span {
        display: none;
    }

    .admin-main {
        margin-left: 70px;
        width: calc(100% - 70px);
    }

    .dashboard-stats {
        grid-template-columns: 1fr;
        padding: 0 20px;
    }

    .dashboard-grid {
        padding: 20px;
    }

    .dashboard-welcome {
        margin: 20px;
    }

    .welcome-icon {
        display: none;
    }
}
            
            </style>

</head>

<body>

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div class="sidebar-brand">

            <div class="sidebar-paw">

                <i class="fa-solid fa-paw"></i>

            </div>

            <div>

                <h2>Stray Paw</h2>

                <span>Admin Panel</span>

            </div>

        </div>

        <nav class="admin-nav">

            <a
                href="dashboard.php"
                class="active">

                <i class="fa-solid fa-chart-line"></i>

                <span>
                    Dashboard
                </span>

            </a>

            <a href="requests.php">

                <i class="fa-solid fa-file-circle-exclamation"></i>

                <span>
                    Rescue Requests
                </span>

            </a>

            <a href="treatments.php">

                <i class="fa-solid fa-kit-medical"></i>

                <span>
                    Treatment History
                </span>

            </a>

            <a href="shelters.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Shelters
                </span>

            </a>

            <a href="volunteers.php">

                <i class="fa-solid fa-hand-holding-heart"></i>

                <span>
                    Volunteers
                </span>

            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="../index.php">

                <i class="fa-solid fa-globe"></i>

                View Website

            </a>

            <a
                href="logout.php"
                class="logout-link">

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </aside>

    <main class="admin-main">

        <header class="admin-topbar">

            <div>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Welcome back,
                    <?= htmlspecialchars($admin_name) ?>!
                </p>

            </div>

            <div class="admin-profile">

                <div class="admin-avatar">

                    <i class="fa-solid fa-user-shield"></i>

                </div>

                <div>

                    <strong>
                        <?= htmlspecialchars($admin_name) ?>
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>

        </header>

        <section class="dashboard-welcome">

            <div>

                <span class="welcome-small">
                    STRAY PAW MANAGEMENT
                </span>

                <h2>
                    Every Paw Deserves a Second Chance 🐾
                </h2>

                <p>
                    Manage rescue requests, treatment history,
                    shelters and volunteers from your admin dashboard.
                </p>

            </div>

            <div class="welcome-icon">

                <i class="fa-solid fa-paw"></i>

            </div>

        </section>

        <section class="dashboard-stats">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-file-circle-exclamation"></i>

                </div>

                <div>

                    <span>
                        Total Requests
                    </span>

                    <h3>
                        <?= $total_requests ?>
                    </h3>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon pending">

                    <i class="fa-solid fa-clock"></i>

                </div>

                <div>

                    <span>
                        Pending Requests
                    </span>

                    <h3>
                        <?= $pending_requests ?>
                    </h3>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon rescued">

                    <i class="fa-solid fa-kit-medical"></i>

                </div>

                <div>

                    <span>
                        Treatment Records
                    </span>

                    <h3>
                        <?= $total_treatments ?>
                    </h3>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon volunteers">

                    <i class="fa-solid fa-hand-holding-heart"></i>

                </div>

                <div>

                    <span>
                        Volunteers
                    </span>

                    <h3>
                        <?= $total_volunteers ?>
                    </h3>

                </div>

            </div>

        </section>

        <section class="dashboard-grid">

            <div class="dashboard-card recent-card">

                <div class="card-header">

                    <div>

                        <h3>
                            Recent Rescue Requests
                        </h3>

                        <p>
                            Latest reports submitted by users.
                        </p>

                    </div>

                    <a href="requests.php">
                        View All
                    </a>

                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Reporter
                                </th>

                                <th>
                                    Location
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (count($recent_requests) > 0): ?>

                            <?php foreach ($recent_requests as $request): ?>

                                <tr>

                                    <td>

                                        #<?= htmlspecialchars(
                                            $request["request_id"]
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $request["name"] ?? "Unknown User"
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $request["location"]
                                        ) ?>

                                    </td>

                                    <td>

                                        <?php

                                        $status =
                                            $request["status"]
                                            ?? "Pending";

                                        $status_class =
                                            strtolower($status);

                                        ?>

                                        <span
                                            class="status <?= htmlspecialchars($status_class) ?>">

                                            <?= htmlspecialchars($status) ?>

                                        </span>

                                    </td>

                                    <td>

                                        <?php

                                        if (!empty($request["created_at"])) {

                                            echo date(
                                                "M d, Y",
                                                strtotime(
                                                    $request["created_at"]
                                                )
                                            );

                                        } else {

                                            echo "N/A";

                                        }

                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="empty-message">

                                    <i class="fa-solid fa-paw"></i>

                                    No rescue requests yet.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="dashboard-card quick-card">

                <div class="card-header">

                    <div>

                        <h3>
                            Quick Actions
                        </h3>

                        <p>
                            Manage your rescue operations.
                        </p>

                    </div>

                </div>

                <div class="quick-actions">

                    <a
                        href="requests.php"
                        class="quick-action">

                        <div class="quick-icon">

                            <i class="fa-solid fa-file-circle-exclamation"></i>

                        </div>

                        <div>

                            <strong>
                                Rescue Requests
                            </strong>

                            <span>
                                Review and update reports
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>

                    <a
                        href="treatments.php"
                        class="quick-action">

                        <div class="quick-icon">

                            <i class="fa-solid fa-kit-medical"></i>

                        </div>

                        <div>

                            <strong>
                                Treatment History
                            </strong>

                            <span>
                                Manage treatment records
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>

                    <a
                        href="shelters.php"
                        class="quick-action">

                        <div class="quick-icon">

                            <i class="fa-solid fa-house"></i>

                        </div>

                        <div>

                            <strong>
                                Shelter Information
                            </strong>

                            <span>
                                Manage shelter records
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>

                    <a
                        href="volunteers.php"
                        class="quick-action">

                        <div class="quick-icon">

                            <i class="fa-solid fa-heart"></i>

                        </div>

                        <div>

                            <strong>
                                Volunteers
                            </strong>

                            <span>
                                Manage volunteer applications
                            </span>

                        </div>

                        <i class="fa-solid fa-chevron-right"></i>

                    </a>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>
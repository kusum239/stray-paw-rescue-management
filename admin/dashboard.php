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
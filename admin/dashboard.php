
<?php

// Start the admin session
session_start();

// Connect to the database
require_once "../config/db.php";

// Check if the admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin_login.php");
    exit();
}

// Get the logged-in admin's name
$admin_name = $_SESSION["admin_name"] ?? "Admin";


// Initialize dashboard statistics
$total_requests = 0;
$pending_requests = 0;
$total_treatments = 0;
$total_volunteers = 0;


// Get total number of rescue requests
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM rescue_requests
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_requests = $row["total"];
}


// Get total number of pending rescue requests
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM rescue_requests
    WHERE status = 'Pending'
");

if ($result) {
    $row = $result->fetch_assoc();
    $pending_requests = $row["total"];
}


// Get total number of treatment history records
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM treatment_history
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_treatments = $row["total"];
}


// Get total number of volunteer applications
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM volunteer_application
");

if ($result) {
    $row = $result->fetch_assoc();
    $total_volunteers = $row["total"];
}


// Get the five most recent rescue requests
$recent_requests = [];

$result = $conn->query("
    SELECT request_id, name, location, status, created_at
    FROM rescue_requests
    ORDER BY created_at DESC
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Stray Paw</title>

    <!-- Main website stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Font Awesome icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="admin-layout">


    <!-- Sidebar navigation -->
    <aside class="admin-sidebar">


        <!-- Stray Paw logo and brand -->
        <div class="sidebar-brand">

            <div class="sidebar-paw">
                <i class="fa-solid fa-paw"></i>
            </div>

            <div>

                <h2>Stray Paw</h2>

                <span>Admin Panel</span>

            </div>

        </div>


        <!-- Admin navigation links -->
        <nav class="admin-nav">


            <!-- Dashboard -->
            <a href="dashboard.php" class="active">

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>


            <!-- Rescue requests -->
            <a href="requests.php">

                <i class="fa-solid fa-file-circle-exclamation"></i>

                <span>Rescue Requests</span>

            </a>


            <!-- Treatment history -->
            <a href="treatments.php">

                <i class="fa-solid fa-kit-medical"></i>

                <span>Treatment History</span>

            </a>


            <!-- Shelters -->
            <a href="shelters.php">

                <i class="fa-solid fa-house"></i>

                <span>Shelters</span>

            </a>


            <!-- Volunteers -->
            <a href="volunteers.php">

                <i class="fa-solid fa-hand-holding-heart"></i>

                <span>Volunteers</span>

            </a>

        </nav>


        <!-- Bottom sidebar links -->
        <div class="sidebar-bottom">


            <!-- Open public website -->
            <a href="../index.php">

                <i class="fa-solid fa-globe"></i>

                View Website

            </a>


            <!-- Logout -->
            <a href="logout.php" class="logout-link">

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </aside>



    <!-- Main dashboard area -->
    <main class="admin-main">


        <!-- Dashboard top bar -->
        <header class="admin-topbar">


            <div>

                <h1>Dashboard</h1>

                <p>
                    Welcome back, <?= htmlspecialchars($admin_name) ?>!
                </p>

            </div>


            <!-- Logged-in admin profile -->
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



        <!-- Dashboard welcome message -->
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



        <!-- Dashboard statistics -->
        <section class="dashboard-stats">


            <!-- Total rescue requests -->
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



            <!-- Pending rescue requests -->
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



            <!-- Treatment history records -->
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



            <!-- Volunteer applications -->
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



        <!-- Dashboard lower content -->
        <section class="dashboard-grid">


            <!-- Recent rescue requests -->
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



                <!-- Rescue requests table -->
                <div class="table-wrapper">


                    <table>


                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Reporter</th>

                                <th>Location</th>

                                <th>Status</th>

                                <th>Date</th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (count($recent_requests) > 0): ?>


                            <?php foreach ($recent_requests as $request): ?>


                                <tr>


                                    <!-- Rescue request ID -->
                                    <td>

                                        #<?= htmlspecialchars(
                                            $request["request_id"]
                                        ) ?>

                                    </td>


                                    <!-- Person who submitted the report -->
                                    <td>

                                        <?= htmlspecialchars(
                                            $request["name"]
                                        ) ?>

                                    </td>


                                    <!-- Report location -->
                                    <td>

                                        <?= htmlspecialchars(
                                            $request["location"]
                                        ) ?>

                                    </td>


                                    <!-- Request status -->
                                    <td>

                                        <?php

                                        // Convert status to a CSS class
                                        $status = $request["status"];

                                        $status_class = strtolower($status);

                                        ?>


                                        <span class="status <?= htmlspecialchars($status_class) ?>">

                                            <?= htmlspecialchars($status) ?>

                                        </span>

                                    </td>


                                    <!-- Request submission date -->
                                    <td>

                                        <?= date(
                                            "M d, Y",
                                            strtotime($request["created_at"])
                                        ) ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <!-- Message shown when no requests exist -->
                            <tr>

                                <td colspan="5"
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



            <!-- Quick actions -->
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


                    <!-- Rescue requests -->
                    <a href="requests.php"
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



                    <!-- Treatment history -->
                    <a href="treatments.php"
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



                    <!-- Shelter information -->
                    <a href="shelters.php"
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



                    <!-- Volunteer applications -->
                    <a href="volunteers.php"
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
```

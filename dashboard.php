<?php

session_start();

require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT user_id, name, email
    FROM users
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();

}

$userName = $user["name"];


/* TOTAL REPORTS */

$totalReports = 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rescue_request
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$totalReports = (int) ($row["total"] ?? 0);


/* PENDING REPORTS */

$pendingReports = 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rescue_request
    WHERE user_id = ?
    AND status = 'Pending'
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$pendingReports = (int) ($row["total"] ?? 0);


/* RESCUED REPORTS */

$rescuedReports = 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM rescue_request
    WHERE user_id = ?
    AND status IN ('Rescued', 'Completed')
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$rescuedReports = (int) ($row["total"] ?? 0);


/* VOLUNTEER STATUS */

$volunteerStatus = "Not Applied";

$stmt = $conn->prepare("
    SELECT status
    FROM volunteer_application
    WHERE user_id = ?
    ORDER BY application_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($volunteer = $result->fetch_assoc()) {

    $volunteerStatus = $volunteer["status"];

}


/* RECENT REQUESTS */

$stmt = $conn->prepare("
    SELECT
        request_id,
        animal_type,
        location,
        created_at,
        status
    FROM rescue_request
    WHERE user_id = ?
    ORDER BY request_id DESC
    LIMIT 5
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$requests = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard | Stray Paw
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: #bd645c;
            background: #fff1ef;
            border: 1px solid #f3d2ce;
            transition: 0.2s ease;
        }

        .delete-btn:hover {
            background: #bd645c;
            color: white;
        }

        @media (max-width: 768px) {

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons a {
                text-align: center;
            }

        }

    </style>

</head>

<body>


<aside class="dashboard-sidebar">

    <div class="sidebar-logo">

        <div class="paw-logo">

            <i class="fa-solid fa-paw"></i>

        </div>

        <span>
            Stray Paw
        </span>

    </div>


    <nav class="sidebar-nav">

        <a
            href="dashboard.php"
            class="active">

            <i class="fa-solid fa-chart-line"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a href="report.php">

            <i class="fa-solid fa-file-circle-plus"></i>

            <span>
                Report Animal
            </span>

        </a>


        <a href="requests.php">

            <i class="fa-solid fa-location-dot"></i>

            <span>
                My Requests
            </span>

        </a>


        <a href="volunteer.php">

            <i class="fa-solid fa-hand-holding-heart"></i>

            <span>
                Volunteer
            </span>

        </a>


        <a href="about.php">

            <i class="fa-solid fa-circle-info"></i>

            <span>
                About
            </span>

        </a>

    </nav>


    <div class="sidebar-bottom">

        <a href="#">

            <i class="fa-solid fa-gear"></i>

            <span>
                Settings
            </span>

        </a>


        <a
            href="logout.php"
            class="logout-link">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Logout
            </span>

        </a>

    </div>

</aside>


<main class="dashboard-main">


    <header class="dashboard-header">

        <div>

            <button
                class="mobile-menu"
                type="button">

                <i class="fa-solid fa-bars"></i>

            </button>

            <span class="page-title">
                Rescue Ops
            </span>

        </div>


        <div class="header-user">

            <div class="notification">

                <i class="fa-regular fa-bell"></i>

            </div>


            <div class="user-profile">

                <div class="user-avatar">

                    <?php

                    echo htmlspecialchars(
                        strtoupper(
                            substr($userName, 0, 1)
                        )
                    );

                    ?>

                </div>


                <div class="user-info">

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $userName
                        );

                        ?>

                    </strong>

                    <small>
                        User
                    </small>

                </div>


                <i class="fa-solid fa-chevron-down"></i>

            </div>

        </div>

    </header>


    <section class="dashboard-content">


        <div class="welcome-section">

            <div>

                <h1>

                    Welcome back,
                    <?php echo htmlspecialchars($userName); ?>!

                </h1>

                <p>
                    Here is an overview of your rescue activities.
                </p>

            </div>


            <a
                href="report.php"
                class="new-rescue-btn">

                <i class="fa-solid fa-plus"></i>

                New Rescue

            </a>

        </div>


        <div class="stats-grid">


            <div class="stat-card">

                <div class="stat-icon reports-icon">

                    <i class="fa-solid fa-file-lines"></i>

                </div>


                <div class="stat-details">

                    <span>
                        Reports Submitted
                    </span>

                    <h2>
                        <?php echo $totalReports; ?>
                    </h2>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon pending-icon">

                    <i class="fa-regular fa-clock"></i>

                </div>


                <div class="stat-details">

                    <span>
                        Pending
                    </span>

                    <h2>
                        <?php echo $pendingReports; ?>
                    </h2>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon rescued-icon">

                    <i class="fa-solid fa-paw"></i>

                </div>


                <div class="stat-details">

                    <span>
                        Rescued
                    </span>

                    <h2>
                        <?php echo $rescuedReports; ?>
                    </h2>

                </div>

            </div>


            <div class="stat-card volunteer-card">

                <div class="stat-icon volunteer-icon">

                    <i class="fa-solid fa-hand-holding-heart"></i>

                </div>


                <div class="stat-details">

                    <span>
                        Volunteer Status
                    </span>

                    <h2 class="status-text">

                        <?php

                        echo htmlspecialchars(
                            $volunteerStatus
                        );

                        ?>

                    </h2>

                </div>

            </div>


        </div>


        <div class="quick-actions">


            <a
                href="requests.php"
                class="quick-card">

                <div class="quick-icon">

                    <i class="fa-solid fa-list-check"></i>

                </div>


                <div>

                    <h3>
                        My Requests
                    </h3>

                    <p>
                        Track your rescue reports
                    </p>

                </div>


                <i class="fa-solid fa-arrow-right"></i>

            </a>


            <a
                href="#"
                class="quick-card">

                <div class="quick-icon">

                    <i class="fa-solid fa-notes-medical"></i>

                </div>


                <div>

                    <h3>
                        Treatment
                    </h3>

                    <p>
                        View treatment records
                    </p>

                </div>


                <i class="fa-solid fa-arrow-right"></i>

            </a>


            <a
                href="#"
                class="quick-card">

                <div class="quick-icon">

                    <i class="fa-solid fa-house"></i>

                </div>


                <div>

                    <h3>
                        Shelters
                    </h3>

                    <p>
                        View shelter information
                    </p>

                </div>


                <i class="fa-solid fa-arrow-right"></i>

            </a>


        </div>


        <div class="requests-section">


            <div class="section-heading">

                <div>

                    <h2>
                        Recent Requests
                    </h2>

                    <p>
                        Your latest rescue reports
                    </p>

                </div>


                <a href="requests.php">

                    View All

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <div class="table-wrapper">

                <table class="requests-table">

                    <thead>

                        <tr>

                            <th>
                                Report ID
                            </th>

                            <th>
                                Animal
                            </th>

                            <th>
                                Location
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if ($requests->num_rows > 0): ?>


                        <?php while ($row = $requests->fetch_assoc()): ?>


                            <tr>


                                <td>

                                    <strong>

                                        #RQ-<?php

                                        echo htmlspecialchars(
                                            $row["request_id"]
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    <div class="animal-cell">

                                        <div class="animal-icon">

                                            <?php

                                            $animalType =
                                                strtolower(
                                                    trim(
                                                        $row["animal_type"]
                                                    )
                                                );

                                            if ($animalType === "cat") {

                                                echo '<i class="fa-solid fa-cat"></i>';

                                            } elseif ($animalType === "dog") {

                                                echo '<i class="fa-solid fa-dog"></i>';

                                            } else {

                                                echo '<i class="fa-solid fa-paw"></i>';

                                            }

                                            ?>

                                        </div>


                                        <div>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $row["animal_type"]
                                                );

                                                ?>

                                            </strong>


                                            <small>
                                                Animal Report
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="location-cell">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <?php

                                        echo htmlspecialchars(
                                            $row["location"]
                                        );

                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <?php

                                    if (!empty($row["created_at"])) {

                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $row["created_at"]
                                            )
                                        );

                                    } else {

                                        echo "N/A";

                                    }

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    $status =
                                        $row["status"]
                                        ?? "Pending";

                                    $statusClass =
                                        strtolower(
                                            str_replace(
                                                " ",
                                                "-",
                                                $status
                                            )
                                        );

                                    ?>


                                    <span
                                        class="status-badge <?php echo htmlspecialchars($statusClass); ?>">

                                        <span class="status-dot"></span>

                                        <?php

                                        echo htmlspecialchars(
                                            $status
                                        );

                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <div class="action-buttons">

                                        <a
                                            href="request_details.php?id=<?php echo urlencode($row["request_id"]); ?>"
                                            class="view-btn">

                                            View

                                        </a>


                                        <a
                                            href="delete_request.php?id=<?php echo urlencode($row["request_id"]); ?>"
                                            class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this rescue request?');">

                                            Delete

                                        </a>

                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="6"
                                class="no-data">

                                <i class="fa-solid fa-paw"></i>

                                <p>

                                    You haven't submitted
                                    any rescue reports yet.

                                </p>


                                <a href="report.php">

                                    Report an Animal

                                </a>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>


    </section>

</main>


<script src="assets/js/script.js"></script>

</body>

</html>

<?php

require_once "includes/auth.php";
require_once "config/db.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT
        request_id,
        animal_type,
        condition_type,
        location,
        status,
        photo,
        video,
        created_at
    FROM rescue_requests
    WHERE user_id = ?
    ORDER BY request_id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$requests = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Requests | Stray Paw</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

</head>

<body>

<?php include "includes/header.php"; ?>

<main class="container page-section">

    <div class="requests-page-header">

        <div>

            <h1>My Rescue Requests</h1>

            <p>
                View and manage the rescue requests you have submitted.
            </p>

        </div>

        <a href="report.php" class="btn">
            <i class="fa-solid fa-plus"></i>
            Report an Animal
        </a>

    </div>

    <?php if ($requests->num_rows > 0): ?>

        <div class="requests-list">

            <?php while ($request = $requests->fetch_assoc()): ?>

                <?php

                $status = $request["status"] ?? "Pending";

                $statusClass = strtolower(
                    str_replace(" ", "-", $status)
                );

                $animalType = strtolower(
                    trim($request["animal_type"])
                );

                ?>

                <div class="request-card">

                    <div class="request-card-top">

                        <div class="request-id">

                            <span>Report ID</span>

                            <strong>
                                #RQ-<?php echo htmlspecialchars($request["request_id"]); ?>
                            </strong>

                        </div>

                        <span class="status-badge <?php echo htmlspecialchars($statusClass); ?>">

                            <span class="status-dot"></span>

                            <?php echo htmlspecialchars($status); ?>

                        </span>

                    </div>

                    <div class="request-card-content">

                        <div class="request-animal">

                            <div class="animal-icon">

                                <?php

                                if ($animalType === "dog") {
                                    echo '<i class="fa-solid fa-dog"></i>';
                                } elseif ($animalType === "cat") {
                                    echo '<i class="fa-solid fa-cat"></i>';
                                } else {
                                    echo '<i class="fa-solid fa-paw"></i>';
                                }

                                ?>

                            </div>

                            <div>

                                <h3>
                                    <?php echo htmlspecialchars($request["animal_type"]); ?>
                                </h3>

                                <p>
                                    <?php echo htmlspecialchars($request["condition_type"]); ?>
                                </p>

                            </div>

                        </div>

                        <div class="request-info">

                            <div>
                                <i class="fa-solid fa-location-dot"></i>
                                <span>
                                    <?php echo htmlspecialchars($request["location"]); ?>
                                </span>
                            </div>

                            <div>
                                <i class="fa-regular fa-calendar"></i>
                                <span>
                                    <?php
                                    echo !empty($request["created_at"])
                                        ? date("M d, Y", strtotime($request["created_at"]))
                                        : "N/A";
                                    ?>
                                </span>
                            </div>

                            <div>
                                <i class="fa-solid fa-camera"></i>
                                <span>
                                    <?php echo !empty($request["photo"]) ? "Photo attached" : "No photo"; ?>
                                </span>
                            </div>

                            <div>
                                <i class="fa-solid fa-video"></i>
                                <span>
                                    <?php echo !empty($request["video"]) ? "Video attached" : "No video"; ?>
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="request-card-actions">

                        <a
                            href="request_details.php?id=<?php echo urlencode($request["request_id"]); ?>"
                            class="view-btn"
                        >
                            <i class="fa-solid fa-eye"></i>
                            View Details
                        </a>

                        <a
                            href="delete_request.php?id=<?php echo urlencode($request["request_id"]); ?>"
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this rescue request?');"
                        >
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty-requests">

            <div class="empty-icon">
                <i class="fa-solid fa-paw"></i>
            </div>

            <h2>No Rescue Requests Yet</h2>

            <p>
                You have not submitted any rescue requests.
            </p>

            <a href="report.php" class="btn">
                <i class="fa-solid fa-plus"></i>
                Report an Animal
            </a>

        </div>

    <?php endif; ?>

</main>

<?php include "includes/footer.php"; ?>

<style>

.requests-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 30px;
}

.requests-page-header h1 {
    margin: 0 0 8px;
    font-size: 30px;
    color: #29483b;
}

.requests-page-header p {
    margin: 0;
    color: #78716c;
}

.requests-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.request-card {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 8px 25px rgba(61, 47, 42, 0.06);
}

.request-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    border-bottom: 1px solid #f0e9e5;
}

.request-id {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.request-id span {
    font-size: 12px;
    color: #968c87;
}

.request-id strong {
    font-size: 17px;
    color: #29483b;
}

.request-card-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
    padding: 22px 0;
}

.request-animal {
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 220px;
}

.request-animal h3 {
    margin: 0 0 4px;
    color: #29483b;
    font-size: 18px;
}

.request-animal p {
    margin: 0;
    color: #8b817c;
    font-size: 13px;
}

.request-info {
    display: grid;
    grid-template-columns: repeat(2, minmax(180px, 1fr));
    gap: 12px 30px;
    flex: 1;
}

.request-info div {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #655b56;
    font-size: 13px;
}

.request-info i {
    width: 17px;
    color: #708d68;
}

.animal-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f6e3df;
    color: #bd645c;
    font-size: 21px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}

.status-badge.pending {
    background: #fff5df;
    color: #b58127;
}

.status-badge.rescued,
.status-badge.completed {
    background: #e9f4eb;
    color: #5d8662;
}

.status-badge.assigned {
    background: #f5e8ef;
    color: #a55c79;
}

.status-badge.in-progress {
    background: #f8e9df;
    color: #b66b46;
}

.request-card-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 18px;
    border-top: 1px solid #f0e9e5;
}

.view-btn,
.delete-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 15px;
    border-radius: 9px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: 0.2s ease;
}

.view-btn {
    background: #edf4ef;
    color: #426b52;
    border: 1px solid #d9e7dc;
}

.view-btn:hover {
    background: #426b52;
    color: #ffffff;
}

.delete-btn {
    background: #fff1ef;
    color: #bd645c;
    border: 1px solid #f1d4d0;
}

.delete-btn:hover {
    background: #bd645c;
    color: #ffffff;
}

.empty-requests {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(61, 47, 42, 0.06);
}

.empty-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f6e3df;
    color: #bd645c;
    font-size: 28px;
}

.empty-requests h2 {
    margin: 0 0 8px;
    color: #29483b;
}

.empty-requests p {
    margin: 0 0 25px;
    color: #8b817c;
}

@media (max-width: 850px) {

    .requests-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .request-card-content {
        align-items: flex-start;
        flex-direction: column;
    }

    .request-info {
        width: 100%;
    }

}

@media (max-width: 550px) {

    .request-card {
        padding: 18px;
    }

    .request-card-top {
        align-items: flex-start;
        gap: 12px;
        flex-direction: column;
    }

    .request-info {
        grid-template-columns: 1fr;
    }

    .request-card-actions {
        justify-content: stretch;
        flex-direction: column;
    }

    .view-btn,
    .delete-btn {
        width: 100%;
    }

}

</style>

</body>

</html>


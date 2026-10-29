
<?php

require_once "config/db.php";

$animal_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$animal = null;

if ($animal_id && $animal_id > 0) {
    $stmt = $conn->prepare("
        SELECT *
        FROM rescued_animal
        WHERE animal_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $animal = $result->fetch_assoc();
    $stmt->close();
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rescue Details | Stray Paw</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 16px;
            background: #f4f7f6;
            color: #263b33;
            font-family: Arial, sans-serif;
        }

        .rescue-container {
            max-width: 800px;
            margin: 25px auto;
        }

        .rescue-header {
            padding: 25px;
            background: #185846;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .rescue-header h1 {
            margin: 0 0 8px;
        }

        .rescue-header p {
            margin: 0;
            color: #e0eee8;
        }

        .rescue-card {
            background: white;
            padding: 28px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .animal-photo {
            display: block;
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            margin-bottom: 22px;
            border-radius: 10px;
            background: #f4f7f6;
        }

        .detail {
            padding: 15px 0;
            border-bottom: 1px solid #e5ebe8;
            overflow-wrap: anywhere;
        }

        .detail-label {
            margin-bottom: 7px;
            color: #185846;
            font-weight: bold;
        }

        .detail-value {
            line-height: 1.6;
        }

        .back-button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #185846;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-button:hover {
            background: #124333;
        }

        .not-found {
            text-align: center;
            padding: 30px 10px;
        }

        @media (max-width: 600px) {
            .rescue-card {
                padding: 20px;
            }

            .rescue-header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<?php include "includes/header.php"; ?>

<main class="rescue-container">

    <?php if ($animal): ?>

        <div class="rescue-header">
            <h1>
                <?= e(!empty($animal['name']) ? $animal['name'] : $animal['animal_type']) ?>
            </h1>
            <p>Rescue Story and Animal Information</p>
        </div>

        <div class="rescue-card">

            <?php if (!empty($animal['photo'])): ?>
                <?php
                $photo = basename(str_replace('\\', '/', $animal['photo']));
                $photo_url = "uploads/reports/" . rawurlencode($photo);
                ?>
                <img
                    src="<?= e($photo_url) ?>"
                    alt="<?= e($animal['name'] ?? 'Rescued animal') ?>"
                    class="animal-photo"
                    onerror="this.style.display='none';"
                >
            <?php endif; ?>

            <div class="detail">
                <div class="detail-label">Animal Name</div>
                <div class="detail-value">
                    <?= e($animal['name'] ?: 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Animal Type</div>
                <div class="detail-value">
                    <?= e($animal['animal_type'] ?? 'N/A') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Breed</div>
                <div class="detail-value">
                    <?= e($animal['breed'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Age</div>
                <div class="detail-value">
                    <?= e($animal['age'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Gender</div>
                <div class="detail-value">
                    <?= e($animal['gender'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Color</div>
                <div class="detail-value">
                    <?= e($animal['color'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Health Condition</div>
                <div class="detail-value">
                    <?= nl2br(e($animal['health_condition'] ?? 'Not provided')) ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Current Status</div>
                <div class="detail-value">
                    <?= e($animal['current_status'] ?? 'N/A') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Rescued Date</div>
                <div class="detail-value">
                    <?= e($animal['rescued_date'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-label">Additional Information</div>
                <div class="detail-value">
                    <?= nl2br(e($animal['notes'] ?? 'No additional information available.')) ?>
                </div>
            </div>

            <a href="index.php" class="back-button">← Back to Home</a>

        </div>

    <?php else: ?>

        <div class="rescue-card not-found">
            <h1>Animal Not Found</h1>

            <p>
                No rescued animal was found for this ID.
                Please select an animal with an existing record.
            </p>

            <a href="index.php" class="back-button">← Back to Home</a>
        </div>

    <?php endif; ?>

</main>

<?php include "includes/footer.php"; ?>

</body>
</html>

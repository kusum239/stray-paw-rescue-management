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

$animal_id = intval($_GET['animal_id'] ?? 0);
$treatments = [];$animal_info = null;

/* Get animal information */
if ($animal_id > 0) {
    $col_check =$conn->query("SHOW COLUMNS FROM animals LIKE 'animal_id'");
    $anim_col = ($col_check &&$col_check->num_rows > 0) ? 'animal_id' : 'id';

    $stmt =$conn->prepare("SELECT * FROM animals WHERE `$anim_col` = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $animal_id);
        $stmt->execute();$animal_info = $stmt->get_result()->fetch_assoc();$stmt->close();
    }
}

/* Get treatment records */
$table_check =$conn->query("SHOW TABLES LIKE 'treatments'");

if ($table_check &&$table_check->num_rows > 0) {

    if ($animal_id > 0) {
        // Detect exact foreign key column in treatments table
        $col_check2 =$conn->query("SHOW COLUMNS FROM treatments LIKE 'animal_id'");
        $fk_col = ($col_check2 &&$col_check2->num_rows > 0) ? 'animal_id' : 'anim_id';

        $stmt =$conn->prepare("SELECT * FROM treatments WHERE `$fk_col` = ? ORDER BY id DESC");
        if ($stmt) {$stmt->bind_param("i", $animal_id);$stmt->execute();
            $res =$stmt->get_result();

            while ($row =$res->fetch_assoc()) {
                $treatments[] =$row;
            }
            $stmt->close();
        }
    } else {
        $res =$conn->query("SELECT * FROM treatments ORDER BY id DESC");
        if ($res) {
            while ($row =$res->fetch_assoc()) {
                $treatments[] =$row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment History - Admin</title>

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
            color: #34495e;
        }

        /* Navigation */
        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #1b4d3e, #276b58);
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .brand {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .nav-bar a {
            color: #cfdfda;
            text-decoration: none;
            font-weight: 600;
            margin-left: 15px;
            padding: 7px 9px;
            border-radius: 5px;
            transition: 0.2s;
        }

        .nav-bar a:hover {
            color: white;
            background: rgba(255,255,255,0.12);
        }

        .nav-bar .active {
            color: white;
            text-decoration: underline;
        }

        .nav-bar .logout {
            color: #ff9999;
        }

        /* Main Card */
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Header */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e1e8e5;
            padding-bottom: 15px;
        }

        .card-header h2 {
            color: #1b4d3e;
            font-size: 24px;
        }

        .animal-name {
            color: #2c8067;
        }

        .page-subtitle {
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 13px;
        }

        /* Summary */
        .treatment-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }

        .summary-box {
            background: #eef7f3;
            border: 1px solid #d6ebe3;
            border-radius: 10px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-icon {
            font-size: 22px;
        }

        .summary-box strong {
            display: block;
            color: #1b4d3e;
            font-size: 18px;
        }

        .summary-box small {
            display: block;
            color: #71817b;
            font-size: 11px;
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            min-width: 700px;
        }

        th, td {
            padding: 13px 15px;
            border-bottom: 1px solid #e1e8e5;
        }

        th {
            background-color: #f1f6f4;
            color: #34495e;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        td {
            color: #52615c;
            font-size: 14px;
        }

        tbody tr {
            transition: 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8fbfa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .treatment-id {
            color: #1b4d3e;
            font-weight: 700;
        }

        .date {
            color: #5f6f69;
            white-space: nowrap;
        }

        .vet-name {
            color: #2c8067;
            font-weight: 600;
        }

        /* Back Button */
        .back-btn {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #545b62;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 45px 20px;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .empty-state h3 {
            color: #566963;
            margin-bottom: 6px;
        }

        .empty-state p {
            color: #8a9692;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 850px) {
            body {
                padding: 15px;
            }

            .nav-bar {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .nav-bar div:last-child {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .nav-bar a {
                margin-left: 0;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }

        @media (max-width: 500px) {
            .card {
                padding: 18px;
            }

            .card-header h2 {
                font-size: 20px;
            }

            .treatment-summary {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

<!-- Navigation -->
<div class="nav-bar">
    <div class="brand">
        🐾 Stray Paw Admin
    </div>
    <div>
        <a href="reports.php">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="animals.php" class="active">Animals</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php">Volunteers</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="card">
    <!-- Header -->
    <div class="card-header">
        <div>
            <h2>
                🩺 Treatment History
                <?php if ($animal_info): ?>
                    <span class="animal-name">
                        - <?= htmlspecialchars($animal_info['name'] ?? $animal_info['animal_name'] ?? 'Animal #' . $animal_id) ?>
                    </span>
                <?php endif; ?>
            </h2>
            <p class="page-subtitle">Medical records and treatment history for this animal</p>
        </div>

        <a href="animals.php" class="back-btn">← Back to Animals</a>
    </div>

    <!-- Treatment Count -->
    <div class="treatment-summary">
        <div class="summary-box">
            <div class="summary-icon">🩺</div>
            <div>
                <strong><?= count($treatments); ?></strong>
                <small>Total Treatment Records</small>
            </div>
        </div>
    </div>

    <!-- Treatment Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Treatment ID</th>
                    <th>Date</th>
                    <th>Diagnosis / Details</th>
                    <th>Treatment Given</th>
                    <th>Vet Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($treatments)): ?>
                    <?php foreach ($treatments as$t): ?>
                        <tr>
                            <td>
                                <span class="treatment-id">
                                    #<?= htmlspecialchars($t['id'] ?? $t['treatment_id'] ?? '1') ?>
                                </span>
                            </td>
                            <td>
                                <span class="date">
                                    <?= htmlspecialchars($t['treatment_date'] ?? $t['date'] ?? $t['created_at'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($t['diagnosis'] ?? $t['details'] ?? $t['description'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($t['treatment'] ?? $t['medicine'] ?? $t['treatment_given'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <span class="vet-name">
                                    👨‍⚕️ <?= htmlspecialchars($t['vet_name'] ?? $t['doctor'] ?? $t['doctor_name'] ?? 'Dr. Staff') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">🩺</div>
                                <h3>No Treatment Records</h3>
                                <p>No medical treatment records have been found for this animal.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin_login.php");
    exit();
}

$animal_id = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;

// Fetch treatment history
$treatments = [];
if ($animal_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM treatment_history WHERE request_id = ? ORDER BY treatment_date DESC");
    if ($stmt) {
        $stmt->bind_param("i", $animal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $treatments[] = $row;
        }
        $stmt->close();
    }
} else {
    $result = $conn->query("SELECT * FROM treatment_history ORDER BY treatment_date DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $treatments[] = $row;
        }
    }
}

$total_treatments = count($treatments);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment History | Stray Paw Admin</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <!-- Dark Green Top Navigation Header -->
    <header class="navbar" style="background: #1f5343; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-radius: 10px; margin: 20px;">
        <div style="color: #fff; font-weight: bold; font-size: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-paw"></i> Stray Paw Admin
        </div>
        <nav style="display: flex; gap: 20px; align-items: center;">
            <a href="dashboard.php" style="color: #fff; text-decoration: none; font-size: 14px;">Dashboard</a>
            <a href="requests.php" style="color: #fff; text-decoration: none; font-size: 14px;">Requests</a>
            <a href="animals.php" style="color: #fff; text-decoration: underline; font-weight: bold; font-size: 14px;">Animals</a>
            <a href="shelters.php" style="color: #fff; text-decoration: none; font-size: 14px;">Shelters</a>
            <a href="volunteers.php" style="color: #fff; text-decoration: none; font-size: 14px;">Volunteers</a>
            <a href="logout.php" style="color: #ff8a8a; text-decoration: none; font-size: 14px;">Logout</a>
        </nav>
    </header>

    <!-- Main Content Container matching Requests / Shelters card structure -->
    <main style="padding: 0 40px; margin-bottom: 40px;">
        
        <div style="background: #ffffff; border-radius: 8px; padding: 25px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eef0f2;">
            
            <!-- Page Title Row with Stethoscope Equipment Icon -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #1f5343; font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-stethoscope" style="font-size: 24px; color: #1f5343;"></i>
                    All Treatment Records
                </h2>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="add_treatment.php<?= $animal_id ? '?animal_id=' . $animal_id : '' ?>" style="background: #1f5343; color: white; padding: 8px 14px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        + Add Treatment
                    </a>
                    <a href="animals.php" style="background: #5c636a; color: white; padding: 8px 14px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">
                        &larr; Back
                    </a>
                </div>
            </div>

            <!-- Table layout -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-family: inherit;">
                    <thead>
                        <tr style="background: #f4f6f8; border-bottom: 2px solid #e0e0e0;">
                            <th style="padding: 12px 15px; text-align: left; font-size: 13px; color: #333; font-weight: 700;">ID</th>
                            <th style="padding: 12px 15px; text-align: left; font-size: 13px; color: #333; font-weight: 700;">Date</th>
                            <th style="padding: 12px 15px; text-align: left; font-size: 13px; color: #333; font-weight: 700;">Diagnosis / Details</th>
                            <th style="padding: 12px 15px; text-align: left; font-size: 13px; color: #333; font-weight: 700;">Treatment Given</th>
                            <th style="padding: 12px 15px; text-align: left; font-size: 13px; color: #333; font-weight: 700;">Vet Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($treatments)): ?>
                            <?php foreach ($treatments as $t): ?>
                                <tr style="border-bottom: 1px solid #eef0f2;">
                                    <td style="padding: 14px 15px; font-size: 13px; color: #333;">#<?= htmlspecialchars($t['treatment_id']) ?></td>
                                    <td style="padding: 14px 15px; font-size: 13px; color: #333;"><?= date('Y-m-d', strtotime($t['treatment_date'])) ?></td>
                                    <td style="padding: 14px 15px; font-size: 13px; color: #333;"><?= htmlspecialchars($t['diagnosis']) ?></td>
                                    <td style="padding: 14px 15px; font-size: 13px; color: #333;"><?= htmlspecialchars($t['treatment_given']) ?></td>
                                    <td style="padding: 14px 15px; font-size: 13px; color: #333;"><?= htmlspecialchars($t['vet_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 45px 15px; color: #777;">
                                    <i class="fa-solid fa-stethoscope" style="font-size: 38px; color: #1f5343; margin-bottom: 10px; display: block;"></i>
                                    <span style="font-size: 14px; color: #555; font-weight: 600;">No medical treatment records found.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

</body>
</html>
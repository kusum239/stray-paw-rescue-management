<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin_login.php");
    exit();
}

// Approve / deactivate / delete
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["volunteer_id"] ?? 0);
    $action = $_POST["action"] ?? "";
    $stmt = null;

    if ($id > 0) {
        if ($action === "approve") {
            $stmt = $conn->prepare("UPDATE volunteers SET status='Active' WHERE id=?");
        } elseif ($action === "deactivate") {
            $stmt = $conn->prepare("UPDATE volunteers SET status='Inactive' WHERE id=?");
        } elseif ($action === "delete") {
            $stmt = $conn->prepare("DELETE FROM volunteers WHERE id=?");
        }
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: volunteers.php");
    exit();
}

// Stats
$stats = ["total" => 0, "pending" => 0, "active" => 0, "inactive" => 0];
$r = $conn->query("SELECT LOWER(status) AS s, COUNT(*) AS c FROM volunteers GROUP BY LOWER(status)");
while ($row = $r->fetch_assoc()) {
    if (isset($stats[$row["s"]])) {
        $stats[$row["s"]] = (int)$row["c"];
    }
    $stats["total"] += (int)$row["c"];
}

// Search + filter
$search = trim($_GET["search"] ?? "");
$status = $_GET["status"] ?? "";

$sql = "SELECT * FROM volunteers WHERE 1=1";
$types = "";
$params = [];

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $like = "%$search%";
    $types .= "sss";
    array_push($params, $like, $like, $like);
}
if (in_array($status, ["Pending", "Active", "Inactive"], true)) {
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $status;
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$volunteers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteers | Stray Paw Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { margin: 0; background: #f4f6f5; font-family: "Segoe UI", Arial, sans-serif; color: #333; }

        .topbar { background: #1b4d3e; padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; }
        .topbar .brand { color: #fff; font-weight: 700; font-size: 22px; display: flex; align-items: center; gap: 10px; }
        .topbar nav { display: flex; gap: 8px; align-items: center; }
        .topbar nav a { color: #fff; text-decoration: none; font-size: 16px; font-weight: 500; padding: 8px 14px; border-radius: 6px; }
        .topbar nav a.active { background: rgba(255,255,255,.18); text-decoration: underline; }

        .wrap { max-width: 1512px; margin: 30px auto; padding: 0 20px; }

        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 30px; }
        .stat { background: #fff; border-radius: 10px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,.06); border-top: 5px solid #1b4d3e; }
        .stat small { font-weight: 700; letter-spacing: .5px; color: #444; font-size: 15px; }
        .stat h2 { margin: 12px 0 0; font-size: 32px; }
        .stat.pending  { border-top-color: #f5b800; } .stat.pending h2  { color: #7a5c00; }
        .stat.active   { border-top-color: #28a745; } .stat.active h2   { color: #28a745; }
        .stat.inactive { border-top-color: #dc3545; } .stat.inactive h2 { color: #dc3545; }
        .stat.total h2 { color: #1b4d3e; }

        .filterbar { background: #fff; border-radius: 10px; padding: 18px 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
                     display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .filterbar input[type=text] { padding: 12px 15px; border: 1px solid #ccc; border-radius: 5px; width: 210px; font-size: 16px; }
        .filterbar button { background: #1b4d3e; color: #fff; border: 0; padding: 12px 20px; border-radius: 5px; font-size: 16px; cursor: pointer; margin-left: 8px; }
        .filterbar select { padding: 12px 16px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; background: #fff; }

        .panel { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .panel h3 { margin: 0 0 22px; color: #1b4d3e; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f4f6f8; }
        th { text-align: left; padding: 16px 12px; color: #1b4d3e; font-size: 15px; text-transform: uppercase; }
        td { padding: 14px 12px; font-size: 15px; border-bottom: 1px solid #eef0f2; }

        .badge { padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .badge.active   { background: #d4edda; color: #155724; }
        .badge.pending  { background: #fff3cd; color: #7a5c00; }
        .badge.inactive { background: #f8d7da; color: #721c24; }

        .btn { border: 0; padding: 7px 12px; border-radius: 4px; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn.approve    { background: #28a745; }
        .btn.deactivate { background: #e0a800; }
        .btn.delete     { background: #dc3545; }

        .empty { text-align: center; padding: 35px; color: #777; font-style: italic; }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand"><i class="fa-solid fa-paw"></i> Stray Paw Admin</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="requests.php">Requests</a>
        <a href="shelters.php">Shelters</a>
        <a href="volunteers.php" class="active">Volunteers</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="wrap">

    <div class="stats">
        <div class="stat total"><small>TOTAL VOLUNTEERS</small><h2><?= $stats["total"] ?></h2></div>
        <div class="stat pending"><small>PENDING REVIEW</small><h2><?= $stats["pending"] ?></h2></div>
        <div class="stat active"><small>ACTIVE / APPROVED</small><h2><?= $stats["active"] ?></h2></div>
        <div class="stat inactive"><small>INACTIVE</small><h2><?= $stats["inactive"] ?></h2></div>
    </div>

    <form method="GET" class="filterbar">
        <div>
            <input type="text" name="search" placeholder="Search by name, email, phone"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </div>
        <select name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="Pending"  <?= $status === "Pending"  ? "selected" : "" ?>>Pending</option>
            <option value="Active"   <?= $status === "Active"   ? "selected" : "" ?>>Active</option>
            <option value="Inactive" <?= $status === "Inactive" ? "selected" : "" ?>>Inactive</option>
        </select>
    </form>

    <div class="panel">
        <h3>Registered Volunteers Directory</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
                    <th>Date Joined</th><th>Status</th><th>Assigned Task</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($volunteers): ?>
                <?php foreach ($volunteers as $v): ?>
                <?php $st = strtolower($v["status"] ?? ""); ?>
                <tr>
                    <td>#<?= (int)$v["id"] ?></td>
                    <td><?= htmlspecialchars($v["name"] ?? "") ?></td>
                    <td><?= htmlspecialchars($v["email"] ?? "") ?></td>
                    <td><?= htmlspecialchars($v["phone"] ?? "—") ?></td>
                    <td><?= date("Y-m-d", strtotime($v["date_joined"])) ?></td>
                    <td><span class="badge <?= htmlspecialchars($st) ?>"><?= htmlspecialchars($v["status"] ?? "") ?></span></td>
                    <td><?= htmlspecialchars($v["assigned_task"] ?: "—") ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="volunteer_id" value="<?= (int)$v["id"] ?>">
                            <?php if ($st !== "active"): ?>
                                <button class="btn approve" name="action" value="approve">Approve</button>
                            <?php endif; ?>
                            <?php if ($st !== "inactive"): ?>
                                <button class="btn deactivate" name="action" value="deactivate">Deactivate</button>
                            <?php endif; ?>
                            <button class="btn delete" name="action" value="delete"
                                    onclick="return confirm('Delete this volunteer?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="empty">No volunteer records match your search or filter.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
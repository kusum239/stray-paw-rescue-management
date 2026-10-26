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

$message = "";

// Handle POST actions: Approve, Reject/Deactivate, Assign Task, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['volunteer_id'])) {
    $vol_id = intval($_POST['volunteer_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE volunteers SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $vol_id);
        $stmt->execute();
        $message = "Volunteer #$vol_id approved successfully!";
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE volunteers SET status = 'Inactive' WHERE id = ?");
        $stmt->bind_param("i", $vol_id);
        $stmt->execute();
        $message = "Volunteer #$vol_id marked as Inactive.";
    } elseif ($action === 'assign_task') {
        $assigned_task = trim($_POST['assigned_task'] ?? '');
        $stmt = $conn->prepare("UPDATE volunteers SET assigned_task = ? WHERE id = ?");
        $stmt->bind_param("si", $assigned_task, $vol_id);
        $stmt->execute();
        $message = "Task assigned to Volunteer #$vol_id successfully!";
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM volunteers WHERE id = ?");
        $stmt->bind_param("i", $vol_id);
        $stmt->execute();
        $message = "Volunteer record #$vol_id deleted.";
    }
}

// Search and Filter Handling
$search_query = trim($_GET['search'] ?? '');
$filter_status = trim($_GET['status'] ?? 'all');

$volunteers = [];
$total_count = 0;
$pending_count = 0;
$approved_count = 0;
$inactive_count = 0;

$table_check = $conn->query("SHOW TABLES LIKE 'volunteers'");

if ($table_check && $table_check->num_rows > 0) {
    // Collect Counts for Stat Cards
    $c_res = $conn->query("SELECT status, COUNT(*) as count FROM volunteers GROUP BY status");
    if ($c_res) {
        while ($row = $c_res->fetch_assoc()) {
            $st = strtolower($row['status']);
            if ($st === 'approved' || $st === 'active') $approved_count += $row['count'];
            elseif ($st === 'pending') $pending_count += $row['count'];
            else $inactive_count += $row['count'];
            $total_count += $row['count'];
        }
    }

    // Build Search Query
    $sql = "SELECT * FROM volunteers WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($search_query)) {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $term = "%" . $search_query . "%";
        $params[] = $term; $params[] = $term; $params[] = $term;
        $types .= "sss";
    }

    if ($filter_status !== 'all') {
        $sql .= " AND status = ?";
        $params[] = $filter_status;
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $volunteers[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteers - Stray Paw Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; color: #333; }

        /* Navigation Bar (Green & White Theme) */
        .navbar {
            background-color: #1b4d3e;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar .logo { font-size: 20px; font-weight: bold; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 500; padding: 5px 10px; border-radius: 4px; transition: background 0.2s; }
        .nav-links a:hover, .nav-links a.active { background-color: rgba(255, 255, 255, 0.15); text-decoration: underline; }

        .container { max-width: 1250px; margin: 25px auto; padding: 0 20px; }

        /* Notification Alert */
        .alert { background-color: #d4edda; color: #155724; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c3e6cb; }

        /* Stats Cards Layout */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-card { background: #fff; padding: 18px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-top: 4px solid #1b4d3e; }
        .stat-card h4 { font-size: 13px; color: #777; text-transform: uppercase; margin-bottom: 6px; }
        .stat-card .number { font-size: 24px; font-weight: bold; color: #1b4d3e; }

        /* Control Bar (Search & Filter) */
        .control-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .search-box { display: flex; gap: 10px; }
        .search-box input, .control-bar select { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; outline: none; }
        .btn-filter { background: #1b4d3e; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; }

        /* Table Design */
        .table-section { background: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .table-section h2 { color: #1b4d3e; margin-bottom: 15px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #1b4d3e; font-size: 12px; text-transform: uppercase; font-weight: 600; }
        td { font-size: 13px; color: #444; }
        tr:hover { background-color: #f9fbfb; }

        /* Badges & Buttons */
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-approved { background-color: #d4edda; color: #155724; }
        .badge-inactive { background-color: #f8d7da; color: #721c24; }

        .action-group { display: flex; gap: 4px; flex-wrap: wrap; }
        .btn-sm { padding: 4px 8px; border: none; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-approve { background-color: #28a745; color: white; }
        .btn-reject { background-color: #ffc107; color: #333; }
        .btn-assign { background-color: #17a2b8; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-contact { background-color: #e2e8f0; color: #333; }

        .no-records { text-align: center; padding: 30px; color: #888; font-style: italic; }
        
        /* Inline Task Form */
        .task-form { display: flex; gap: 4px; margin-top: 4px; }
        .task-input { padding: 2px 6px; font-size: 11px; border: 1px solid #ccc; border-radius: 3px; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div class="logo"><span>🐾</span> Stray Paw Admin</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="requests.php">Requests</a></li>
            <li><a href="animals.php">Animals</a></li>
            <li><a href="shelters.php">Shelters</a></li>
            <li><a href="volunteers.php" class="active">Volunteers</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </header>

    <div class="container">

        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Volunteers</h4>
                <div class="number"><?php echo $total_count; ?></div>
            </div>
            <div class="stat-card" style="border-color: #ffc107;">
                <h4>Pending Review</h4>
                <div class="number" style="color: #856404;"><?php echo $pending_count; ?></div>
            </div>
            <div class="stat-card" style="border-color: #28a745;">
                <h4>Active / Approved</h4>
                <div class="number" style="color: #28a745;"><?php echo $approved_count; ?></div>
            </div>
            <div class="stat-card" style="border-color: #dc3545;">
                <h4>Inactive</h4>
                <div class="number" style="color: #dc3545;"><?php echo $inactive_count; ?></div>
            </div>
        </div>

        <!-- Filter and Search Bar -->
        <form method="GET" class="control-bar">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by name, email, phone..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-filter">Search</button>
            </div>
            <div>
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="Pending" <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo $filter_status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Inactive" <?php echo $filter_status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </form>

        <!-- Volunteer Management Table -->
        <div class="table-section">
            <h2>Registered Volunteers Directory</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                        <th>Status</th>
                        <th>Assigned Task</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($volunteers)): ?>
                        <?php foreach ($volunteers as $vol): 
                            $status = $vol['status'] ?? 'Pending';
                            $statusClass = 'badge-pending';
                            if (strtolower($status) === 'approved' || strtolower($status) === 'active') {
                                $statusClass = 'badge-approved';
                            } elseif (strtolower($status) === 'inactive' || strtolower($status) === 'rejected') {
                                $statusClass = 'badge-inactive';
                            }

                            // Format Joined Date safely
                            $raw_date = $vol['created_at'] ?? $vol['date_joined'] ?? $vol['date'] ?? null;
                            $formatted_date = $raw_date ? date("M d, Y", strtotime($raw_date)) : 'N/A';
                        ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($vol['id'] ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($vol['name'] ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($vol['email'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($vol['phone'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($formatted_date); ?></td>
                                <td>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <!-- Current Task or Assignment Form -->
                                    <span style="font-weight: 500; color: #1b4d3e;">
                                        <?php echo htmlspecialchars($vol['assigned_task'] ?? 'Unassigned'); ?>
                                    </span>
                                    <form method="POST" class="task-form">
                                        <input type="hidden" name="volunteer_id" value="<?php echo $vol['id']; ?>">
                                        <input type="hidden" name="action" value="assign_task">
                                        <input type="text" name="assigned_task" placeholder="Assign task..." class="task-input" required>
                                        <button type="submit" class="btn-sm btn-assign">Set</button>
                                    </form>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <!-- Approve / Activate Button -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="volunteer_id" value="<?php echo $vol['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn-sm btn-approve" title="Approve Request">Approve</button>
                                        </form>

                                        <!-- Reject / Deactivate Button -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="volunteer_id" value="<?php echo $vol['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn-sm btn-reject" title="Mark Inactive">Deactivate</button>
                                        </form>

                                        <!-- Direct Email Link -->
                                        <?php if (!empty($vol['email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($vol['email']); ?>" class="btn-sm btn-contact">Email</a>
                                        <?php endif; ?>

                                        <!-- Delete Button -->
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this volunteer permanently?');">
                                            <input type="hidden" name="volunteer_id" value="<?php echo $vol['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-sm btn-delete" title="Delete Record">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-records">No volunteer records match your search or filter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
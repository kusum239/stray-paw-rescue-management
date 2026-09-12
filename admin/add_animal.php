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
$error = "";

// Fetch Shelters for Dropdown Selection
$shelters = [];
$shelter_check = $conn->query("SHOW TABLES LIKE 'shelters'");
if ($shelter_check && $shelter_check->num_rows > 0) {
    $res = $conn->query("SELECT * FROM shelters");
    if ($res) {
        while ($s = $res->fetch_assoc()) {
            $shelters[] = $s;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? 'Dog');
    $status  = trim($_POST['status'] ?? 'Under Care');
    $shelter_id = intval($_POST['shelter_id'] ?? 0);

    if (empty($name)) {
        $error = "Please provide an animal name or identification tag.";
    } else {
        $existing_columns = [];
        $col_res = $conn->query("SHOW COLUMNS FROM animals");
        if ($col_res) {
            while ($c = $col_res->fetch_assoc()) {
                $existing_columns[] = $c['Field'];
            }
        }

        $data = [];
        if (in_array('name', $existing_columns)) {
            $data['name'] = $name;
        } elseif (in_array('animal_name', $existing_columns)) {
            $data['animal_name'] = $name;
        }

        if (in_array('species', $existing_columns)) {
            $data['species'] = $species;
        } elseif (in_array('type', $existing_columns)) {
            $data['type'] = $species;
        }

        if (in_array('status', $existing_columns)) {
            $data['status'] = $status;
        }

        if (in_array('shelter_id', $existing_columns)) {
            $data['shelter_id'] = $shelter_id;
        }

        $columns = implode("`, `", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = str_repeat("s", count($data));
        $values = array_values($data);

        $sql = "INSERT INTO animals (`$columns`) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                header("Location: animals.php");
                exit();
            } else {
                $error = "Database Error: " . $stmt->error;
            }
        } else {
            $error = "Query Execution Failed: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Animal - Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; padding: 25px; display: flex; justify-content: center; }
        .card { background: white; width: 550px; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card h2 { color: #1b4d3e; margin-bottom: 20px; border-bottom: 1px solid #e1e8e5; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #34495e; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #dcdfe6; border-radius: 6px; font-size: 0.95rem; }
        .btn-submit { background-color: #1b4d3e; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #143b2f; }
        .error-msg { background: #fde8e8; color: #e74c3c; padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="card">
    <h2>+ Register New Animal</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add_animal.php">
        <div class="form-group">
            <label>Animal Name / Tag *</label>
            <input type="text" name="name" placeholder="e.g. Max / Dog-104" required>
        </div>

        <div class="form-group">
            <label>Species</label>
            <select name="species">
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Puppy">Puppy</option>
                <option value="Kitten">Kitten</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Initial Status</label>
            <select name="status">
                <option value="Under Care">Under Care</option>
                <option value="Recovered">Recovered</option>
                <option value="Adopted">Adopted</option>
            </select>
        </div>

        <div class="form-group">
            <label>Assigned Shelter</label>
            <select name="shelter_id">
                <option value="0">Unassigned</option>
                <?php foreach ($shelters as $s): ?>
                    <option value="<?php echo $s['id'] ?? $s['shelter_id']; ?>">
                        <?php echo htmlspecialchars($s['name'] ?? $s['shelter_name'] ?? 'Shelter #' . ($s['id'] ?? 1)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-submit">Add Animal</button>
    </form>
</div>

</body>
</html>
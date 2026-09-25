<?php
require_once '../config/db.php';

$animal_id = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_animal_id = intval($_POST['animal_id']);
    $treatment_date = mysqli_real_escape_string($conn, $_POST['treatment_date']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $treatment = mysqli_real_escape_string($conn, $_POST['treatment']);
    $medication = mysqli_real_escape_string($conn, $_POST['medication']);
    $veterinarian = mysqli_real_escape_string($conn, $_POST['veterinarian']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "INSERT INTO treatment_history (animal_id, treatment_date, diagnosis, treatment, medication, veterinarian, notes) 
            VALUES ('$selected_animal_id', '$treatment_date', '$diagnosis', '$treatment', '$medication', '$veterinarian', '$notes')";

    if (mysqli_query($conn, $sql)) {
        header("Location: treatment_history.php?animal_id=" . $selected_animal_id);
        exit();
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

$animals_result = mysqli_query($conn, "SELECT id, name FROM animals");
?>

<!DOCTYPE html>
<html>
<head><title>Add Treatment</title></head>
<body style="font-family: Arial; padding: 20px;">
<h2>Add Medical Record</h2>
<?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>

<form method="POST" action="add_treatment.php" style="max-width: 400px;">
    <p>Select Animal:<br>
    <select name="animal_id" required style="width:100%; padding:8px;">
        <option value="">-- Choose Animal --</option>
        <?php while ($a = mysqli_fetch_assoc($animals_result)): ?>
            <option value="<?php echo $a['id']; ?>" <?php echo ($a['id'] == $animal_id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($a['name']); ?>
            </option>
        <?php endwhile; ?>
    </select></p>

    <p>Date:<br><input type="date" name="treatment_date" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:8px;"></p>
    <p>Diagnosis:<br><input type="text" name="diagnosis" placeholder="e.g. Fever" required style="width:100%; padding:8px;"></p>
    <p>Treatment:<br><input type="text" name="treatment" placeholder="e.g. Vaccination" required style="width:100%; padding:8px;"></p>
    <p>Medication:<br><input type="text" name="medication" placeholder="e.g. Antibiotics" style="width:100%; padding:8px;"></p>
    <p>Vet Name:<br><input type="text" name="veterinarian" placeholder="e.g. Dr. John" style="width:100%; padding:8px;"></p>
    <p>Notes:<br><textarea name="notes" style="width:100%; padding:8px;"></textarea></p>

    <button type="submit" style="background:green; color:white; padding:10px 15px; border:none; cursor:pointer;">Save Record</button>
</form>
</body>
</html>
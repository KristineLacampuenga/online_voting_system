<?php 
ob_start();
error_reporting(0);
include("connection.php");
include("function2.php");
include("header2.php");

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidateNumber = trim($_POST['candidateNumber']);
    $candidateName = trim($_POST['candidateName']);
    $candidatePosition = $_POST['candidatePosition'];
    $dateApply = trim($_POST['dateApply']);
    $partylist = trim($_POST['partylist']);

    // Validate inputs
    if (
        empty($candidateNumber) || 
        empty($candidateName) || 
        empty($candidatePosition) || 
        empty($dateApply) || 
        empty($partylist)
    ) {
        $error_msg = "Please fill in all required fields.";
    } elseif (!is_numeric($candidateNumber)) {
        $error_msg = "Candidate number must be a number.";
    } elseif (!isset($_FILES['candidateImage']) || $_FILES['candidateImage']['error'] != 0) {
        $error_msg = "Please upload a valid image file.";
    } else {
        // Check for duplicate candidate number
        $check_query = "SELECT * FROM candidate WHERE candidate_number = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $candidateNumber);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_msg = "Candidate number already exists. Please use a unique number.";
        } else {
            $file_name = $_FILES['candidateImage']['name'];
            $file_tmp = $_FILES['candidateImage']['tmp_name'];
            $file_error = $_FILES['candidateImage']['error'];

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_ext, $allowed)) {
                $error_msg = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            } elseif ($file_error === 0) {
                $file_destination = "./uploads/" . $file_name;

                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // Insert into DB
                    $insert_query = "INSERT INTO candidate (candidate_number, candidate_name, candidate_position, date_apply, partylist, candidate_image) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "isssss", $candidateNumber, $candidateName, $candidatePosition, $dateApply, $partylist, $file_name);
                        if (mysqli_stmt_execute($stmt)) {
                            $success_msg = "Candidate added successfully.";
                            $candidateNumber = $candidateName = $candidatePosition = $dateApply = $partylist = "";
                        } else {
                            $error_msg = "Failed to add candidate to database.";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error_msg = "Database error: failed to prepare statement.";
                    }
                } else {
                    $error_msg = "Failed to move uploaded file.";
                }
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin - Add Candidate</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #fff8f0;
    color: #333;
    margin: 0;
    padding: 0;
  }
  .form-container {
    max-width: 600px;
    margin: 2em auto;
    padding: 1.5em;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
  }
  h2 {
    text-align: center;
    margin-bottom: 1em;
  }
  .form-group {
    margin-bottom: 1em;
  }
  label {
    display: block;
    margin-bottom: 0.4em;
    font-weight: bold;
  }
  input[type="text"],
  input[type="number"],
  input[type="date"],
  textarea,
  input[type="file"],
  select {
    width: 100%;
    padding: 0.5em;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  button {
    display: block;
    margin: 1.2em auto 0;
    padding: 0.7em 2em;
    background-color: #007bff;
    color: #fff;
    font-size: 1em;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  button:hover {
    background-color: #0056b3;
  }
  .error {
    color: red;
    margin-bottom: 1em;
    text-align: center;
  }
  .success {
    color: green;
    margin-bottom: 1em;
    text-align: center;
  }
</style>
</head>
<body>

<div class="form-container">
    <h2>Add New Candidate</h2>

    <?php if ($error_msg): ?>
        <div class="error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if ($success_msg): ?>
        <div class="success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <form id="addCandidateForm" method="POST" action="admin.php" enctype="multipart/form-data">

        <div class="form-group">
            <label for="candidateNumber">Candidate Number:</label>
            <input type="number" id="candidateNumber" name="candidateNumber" required value="<?= isset($candidateNumber) ? htmlspecialchars($candidateNumber) : '' ?>">
        </div>

        <div class="form-group">
            <label for="candidateName">Candidate Name:</label>
            <input type="text" id="candidateName" name="candidateName" required value="<?= isset($candidateName) ? htmlspecialchars($candidateName) : '' ?>">
        </div>

        <div class="form-group">
            <label for="candidatePosition">Candidate Position:</label>
            <select id="candidatePosition" name="candidatePosition" required>
                <option value="">-- Select Position --</option>
                <?php 
                $positions = ['Senator', 'House Representative', 'Mayor', 'Vice Mayor', 'City Councilor'];
                foreach ($positions as $pos) {
                    $selected = (isset($candidatePosition) && $candidatePosition === $pos) ? 'selected' : '';
                    echo "<option value=\"$pos\" $selected>$pos</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="dateApply">Date Applied:</label>
            <input type="date" id="dateApply" name="dateApply" required value="<?= isset($dateApply) ? htmlspecialchars($dateApply) : '' ?>">
        </div>

        <div class="form-group">
            <label for="partylist">Partylist:</label>
            <textarea id="partylist" name="partylist" required><?= isset($partylist) ? htmlspecialchars($partylist) : '' ?></textarea>
        </div>

        <div class="form-group">
            <label for="candidateImage">Candidate Image:</label>
            <input type="file" id="candidateImage" name="candidateImage" accept="image/*" required>
        </div>

        <button type="submit">Add Candidate</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

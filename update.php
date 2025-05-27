<?php
session_start();
include 'connection.php';
include 'header2.php';


$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voter_id = $_POST['voter_id'];
    $role = $_POST['role'];
    $verify = $_POST['verify'];
    $votersCode = $_POST['VotersCode'];

    // Check if VotersCode already exists for another voter
    $check_stmt = $conn->prepare("SELECT id FROM voters WHERE VotersCode = ? AND id != ?");
    $check_stmt->bind_param("si", $votersCode, $voter_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "VotersCode already exists for another voter!";
    } else {
        // Proceed with the update
        $stmt = $conn->prepare("UPDATE voters SET role = ?, verify = ?, VotersCode = ? WHERE id = ?");
        $stmt->bind_param("sssi", $role, $verify, $votersCode, $voter_id);

        if ($stmt->execute()) {
            $success = "Voter updated successfully!";
        } else {
            $error = "Error updating voter: " . $stmt->error;
        }
    }
}

// Fetch voters for dropdown
$voters_result = $conn->query("SELECT id, first_name, last_name FROM voters");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Voter Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        form {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        label, select, input {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        button {
            background-color: sandybrown;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Edit Voter's Information</h2>

<?php if ($success): ?>
    <p class="success"><?php echo $success; ?></p>
    <script>
        setTimeout(() => {
            window.location.href = "update.php";
        }, 2000);
    </script>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label for="voter_id">Select Voter Name:</label>
    <select name="voter_id" required>
        <option value="">-- Select Voter --</option>
        <?php while ($row = $voters_result->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>">
                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="role">Role:</label>
    <select name="role" required>
        <option value="voters">Voter</option>
        <option value="admin">Admin</option>
    </select>

    <label for="verify">Verify:</label>
    <select name="verify" required>
        <option value="Unverified">Unverified</option>
        <option value="Verified">Verified</option>
    </select>

    <label for="VotersCode">Voter Code:</label>
    <input type="text" name="VotersCode" required>

    <button type="submit">Update Voter</button>
</form>

<?php
include 'tally.php';
include 'footer.php';

?>

</body>
</html>

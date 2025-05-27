<?php 
ob_start();
session_start();
include 'connection.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repass = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $barangay = $_POST['barangay'];
    $contactNo = $_POST['contactNo'];
    $sector = $_POST['sector'];
    $voterNo = $_POST['voterNo'];

    $role = 'voters'; // default value
    $verify = 'Unverified'; // default value

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";

    // Password validation
    if (!preg_match($pattern, $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    } elseif ($password !== $repass) {
        $error = "Passwords do not match!";
    } else {
        // Check for existing email or contactNo
        $check_stmt = $conn->prepare("SELECT * FROM voters WHERE email = ? OR contactNo = ?");
        $check_stmt->bind_param("ss", $email, $contactNo);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email or Contact Number already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO voters 
                (email, password,repass, first_name, middle_name, last_name, barangay, contactNo, sector, voterNo, role, verify, VotersCode)
                VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssss", 
                $email, 
                $hashed_password, 
                $repass,
                $first_name, 
                $middle_name, 
                $last_name, 
                $barangay, 
                $contactNo, 
                $sector, 
                $voterNo, 
                $role, 
                $verify, 
                $VotersCode
            );

            if ($stmt->execute()) {
                $success = "Account created successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Voter Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: sandybrown;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .container {
            max-width: 500px;
            background-color: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: sandybrown;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #d17e00;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper i {
            position: absolute;
            right: 15px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }

        .btn-login {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: sandybrown;
            color: white;
            border: none;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
        }

        a svg {
            fill: white;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="navbar">
        <h1>CREATE YOUR VOTER ACCOUNT</h1>
        <a href="index.php" title="Back">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                 class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                      d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
            </svg>
        </a>
    </div>

    <div class="container">
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) {
            echo "<div class='success'>$success</div>";
            echo "<a href='index.php' class='btn-login'>Go to Login</a>";
        } else { ?>
            <form method="POST" id="accountForm">
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="middle_name" placeholder="Middle Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="barangay" placeholder="Barangay" required>
                <input type="text" name="contactNo" placeholder="Contact Number" maxlength="11" pattern="\d{11}" title="Enter 11 digit contact number" required>

                <select name="sector" required>
                    <option value="">Select Sector</option>
                    <option value="PWD">PWD</option>
                    <option value="Senior">Senior</option>
                    <option value="Solo Parent">Solo Parent</option>
                    <option value="o">Others</option>
                </select>

                <input type="text" name="voterNo" placeholder="Voter's Number" required>

                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="far fa-eye-slash" id="togglePassword"></i>
                </div>

                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat Password" required>
                    <i class="far fa-eye-slash" id="toggleConfirmPassword"></i>
                </div>

                <input type="submit" value="Create Account">
            </form>
        <?php } ?>
    </div>

    <script>
        function validatePasswordStrength(password) {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            return regex.test(password);
        }

        document.getElementById("accountForm")?.addEventListener("submit", function (e) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (!validatePasswordStrength(password)) {
                e.preventDefault();
                alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
            } else if (password !== confirmPassword) {
                e.preventDefault();
                alert("Passwords do not match.");
            }
        });

        document.getElementById("togglePassword").addEventListener("click", function () {
            const field = document.getElementById("password");
            const type = field.type === "password" ? "text" : "password";
            field.type = type;
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });

        document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
            const field = document.getElementById("confirm_password");
            const type = field.type === "password" ? "text" : "password";
            field.type = type;
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>
</html>
<?php  
session_start();
include("connection.php");
include("function2.php"); 

$error = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password) && !is_numeric($email)) {
        $stmt = $conn->prepare("SELECT * FROM voters WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            if (password_verify($password, $user_data['password'])) {
                // ✅ Set session variables
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['voter_last_name'] = $user_data['last_name'];
                $_SESSION['voter_code'] = $user_data['VotersCode']; // Make sure column is correctly named

                if ($user_data['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: ballot.php");
                }
                exit;
            }
        }
        $error = true;
    } else {
        $error = true;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GLBCL Secure Online Voting System</title>
  <link rel="icon" href="http://localhost/election/logo.png" type="image/png" />
  <link rel="stylesheet" href="loginStyle.css" />
</head>
<body>

<!-- Logo -->
<img src="http://localhost/election/logo.png" alt="Logo" class="logo">

<h2 style="text-align:center;">GLBCL Secure Online Voting System</h2>

<!-- Login Button -->
<div style="text-align:center;">
  <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Login</button>
</div>



<!-- Modal Login Form -->
<div id="id01" class="modal" style="display:<?php echo $error ? 'block' : 'none'; ?>;">
  <form
    class="modal-content animate"
    id="loginForm"
    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
    method="post"
    onsubmit="return validateForm()"
  >
    <div class="container">
      <?php if ($error): ?>
        <div style="color:red; margin-bottom: 15px;">
          Wrong email or password!
        </div>
      <?php endif; ?>

      <label for="email"><b>Email</b></label>
      <input type="email" placeholder="Enter Email" name="email" id="email" required />
      <div id="email-error" class="error-text"></div>

      <label for="password"><b>Password</b></label>
      <div class="password-container">
        <input type="password" placeholder="Enter Password" name="password" id="password" required />
        <img
          src="http://localhost/election/bg-image/close-eye.png"
          id="eye-icon"
          onclick="togglePasswordVisibility()"
          alt="Toggle"
          style="cursor:pointer;"
        />
      </div>
      <div id="password-error" class="error-text"></div>

      <button type="submit">Login</button>

      <label>
        <input type="checkbox" checked="checked" name="remember" id="rememberMe" /> Remember me
      </label>
    </div>

    <div class="container footer">
      <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">
        Cancel
      </button>
      <span class="psw">Create Account <a href="create_account.php">here</a></span>
    </div>
  </form>
</div>

<!-- Candidates Section -->
<div style="margin-top: 40px;">
  <?php include("candidates.php"); ?>
</div>

<!-- Slideshow -->
<div class="slideshow-container">
  <img class="slide-img" src="http://localhost/election/bg-image/g.jpg" alt="Slide 1" />
  <img class="slide-img" src="http://localhost/election/bg-image/le.jpg" alt="Slide 2" />
  <img class="slide-img" src="http://localhost/election/bg-image/b.jpg" alt="Slide 3" />
  <img class="slide-img" src="http://localhost/election/bg-image/c.jpg" alt="Slide 4" />
  <img class="slide-img" src="http://localhost/election/bg-image/la.jpg" alt="Slide 5" />
</div>
<script src="script.js"></script>
<footer class="custom-footer">
  &copy; 2025 GLBCL Secure Online Voting System. All rights reserved.
</footer>

</body>
</html>

<div class="header">
  <style>
    .header {
      text-align: center;
      padding-top: 20px;
    }

    .logo {
      display: block;
      margin: 0 auto 15px auto; /* Center horizontally and add margin below */
      width: 190px;
      height: auto;
    }

    h2 {
      margin: 0 0 20px 0; /* Add margin below header text */
      color: #333;
    }

    /* Vertical nav centered and aligned below logo */
    .nav-vertical {
      width: 190px; /* Match the logo width for perfect alignment */
      margin: 0 auto; /* Center nav */
      text-align: center;
    }

    .nav-vertical ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .nav-vertical ul li {
      margin-bottom: 12px;
    }

    .nav-vertical ul li a {
      display: block;
      padding: 8px 12px;
      border: 1px solid sandybrown;
      border-radius: 8px;
      color: sandybrown;
      font-weight: bold;
      font-size: 16px;
      text-decoration: none;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-vertical ul li a:hover {
      background-color: sandybrown;
      color: white;
    }
  </style>

  <img src="http://localhost/election/logo.png" alt="Logo" class="logo" />
  <h2>GLBCL Secure Online Voting System</h2>

  <nav class="nav-vertical">
    <ul>
      <li><a href="admin.php">Home</a></li>
      <li><a href="update.php">Vote Record</a></li>
      <li><a href="index.php">Logout</a></li>
    </ul>
  </nav>
</div>

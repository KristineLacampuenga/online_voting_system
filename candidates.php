<?php
// --- Database Setup ---
$host = "localhost";
$dbname = "election";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM candidate ORDER BY date_apply ASC";
$result = $conn->query($sql);

$candidates = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['candidate_image'] = 'uploads/' . $row['candidate_image'];
        $candidates[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Candidate Showcase</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="candidatesStyle.css" />
</head>
<body>

<!-- ==== Slider Section ==== -->
<div class="slider-container">
  <div class="slider-wrapper" id="slider-wrapper">
    <?php foreach ($candidates as $candidate): ?>
      <div class="slide">
        <img src="<?= htmlspecialchars($candidate['candidate_image']) ?>" alt="<?= htmlspecialchars($candidate['candidate_name']) ?>" />
        <div class="slide-caption">
          <h2><?= htmlspecialchars($candidate['candidate_name']) ?></h2>
          <p><strong>Candidate No.:</strong> <?= htmlspecialchars($candidate['candidate_number']) ?></p>
          <p><strong>Position:</strong> <?= htmlspecialchars($candidate['candidate_position']) ?></p>
          <p><strong>Partylist:</strong> <?= htmlspecialchars($candidate['partylist']) ?></p>
          <a href="candidate.php?id=<?= $candidate['id'] ?>">View Details</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ==== Cards Section ==== -->
<h2 class="section-title">Choose Candidates Wisely </h2>
<div class="cards-container">
  <?php foreach ($candidates as $candidate): ?>
    <div class="card">
      <img src="<?= htmlspecialchars($candidate['candidate_image']) ?>" alt="<?= htmlspecialchars($candidate['candidate_name']) ?>" />
      <div class="card-body">
        <h3><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
        <p><strong>Candidate No.:</strong> <?= htmlspecialchars($candidate['candidate_number']) ?></p>
        <p><strong>Position:</strong> <?= htmlspecialchars($candidate['candidate_position']) ?></p>
        <p><strong>Partylist:</strong> <?= htmlspecialchars($candidate['partylist']) ?></p>
        <a href="candidate.php?id=<?= $candidate['id'] ?>">View Details</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<script src="script.js"></script>
</body>
</html>

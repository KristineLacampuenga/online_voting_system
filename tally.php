<?php
$conn = new mysqli("localhost", "root", "", "election");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function startPage() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Tally</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fffaf0;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
      background: #ffffff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    h1 {
      text-align: center;
      color: #8b4513;
      font-size: 74px;
      margin-bottom: 20px;
    }
 
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      margin-bottom: 20px;
    }
    th, td {
      padding: 8px;
      border: 1px solid #deb887;
      text-align: left;
    }
    th {
      background-color: #f4a460;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #fff5e1;
    }
    tr:hover {
      background-color: #ffefd5;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Election Tally Results</h1>
HTML;
}

function endPage() {
    echo "</div></body></html>";
}

function tallySingleColumn($conn, $title, $table, $column) {
    echo "<h2>$title</h2>";
    $sql = "SELECT `$column` AS candidate, COUNT(*) AS votes 
            FROM `$table` 
            WHERE `$column` IS NOT NULL 
            GROUP BY `$column` 
            ORDER BY votes DESC";
    $result = $conn->query($sql);

    echo "<table><tr><th>Candidate</th><th>Votes</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['candidate']}</td><td>{$row['votes']}</td></tr>";
    }
    echo "</table>";
}

function tallyMultiColumn($conn, $title, $table, $columns) {
    echo "<h2>$title</h2>";

    $union = [];
    foreach ($columns as $col) {
        $union[] = "SELECT `$col` AS candidate FROM `$table` WHERE `$col` IS NOT NULL";
    }
    $sql = "SELECT candidate, COUNT(*) AS votes FROM (" . implode(" UNION ALL ", $union) . ") AS all_votes 
            GROUP BY candidate 
            ORDER BY votes DESC";
    $result = $conn->query($sql);

    echo "<table><tr><th>Candidate</th><th>Votes</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['candidate']}</td><td>{$row['votes']}</td></tr>";
    }
    echo "</table>";
}

startPage();

tallyMultiColumn($conn, "Senator Vote Tally", "senator", [
    "sen1", "sen2", "sen3", "sen4", "sen5", "sen6", 
    "sen7", "sen8", "sen9", "sen10", "sen11", "sen12"
]);

tallyMultiColumn($conn, "City Councilor Vote Tally", "city_councilor", [
    "councilor1", "councilor2", "councilor3", "councilor4", "councilor5", "councilor6",
    "councilor7", "councilor8", "councilor9", "councilor10", "councilor11", "councilor12"
]);

tallySingleColumn($conn, "House Representative Vote Tally", "house_rep", "candidate_name");
tallySingleColumn($conn, "Mayor Vote Tally", "mayor", "candidate_name");
tallySingleColumn($conn, "Vice Mayor Vote Tally", "vice_mayor", "candidate_name");
tallySingleColumn($conn, "Partylist Vote Tally", "partylist", "partylist");

$conn->close();
endPage();
?>

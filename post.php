<?php
session_start();
include("header.php");

$conn = new mysqli("localhost", "root", "", "election");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['id'])) {
    die("Error: User not logged in.");
}

$voterId = (int)$_SESSION['id'];

$stmt = $conn->prepare("SELECT id, last_name, VotersCode FROM voters WHERE id = ?");
$stmt->bind_param("i", $voterId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Voter not found.");
}

$voter = $result->fetch_assoc();
$id = $voter['id'];
$last_name = $voter['last_name'];
$votersCode = $voter['VotersCode'];
?>

<style>
    body {
        background-color: #fdf5e6;
        font-family: 'Courier New', monospace;
        color: #5c3d00;
    }
    .receipt-container {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }
    .receipt {
        width: 600px;
        background-color: #fffaf0;
        border: 2px solid #deb887;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px #deb887;
    }
    .receipt h2 {
        text-align: center;
        color: #a0522d;
        margin-bottom: 30px;
    }
    .receipt p {
        margin: 5px 0;
        font-size: 16px;
    }
    .receipt .section {
        margin-top: 20px;
    }
    .vote-list {
        padding-left: 20px;
    }
    hr {
        border: none;
        border-top: 1px dashed #deb887;
        margin: 20px 0;
    }
</style>

<div class="receipt-container">
    <div class="receipt">
        <h2>Official Voting Receipt</h2>

        <p><strong>Voter ID:</strong> <?= htmlspecialchars($id) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($last_name) ?></p>
        <p><strong>Voters Code:</strong> <?= htmlspecialchars($votersCode) ?></p>

        <hr>

        <?php
        function getSingleVote($conn, $table, $field) {
            global $id, $last_name, $votersCode;
            $stmt = $conn->prepare("SELECT $field FROM $table WHERE id = ? AND last_name = ? AND VotersCode = ?");
            $stmt->bind_param("iss", $id, $last_name, $votersCode);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $vote = $result->fetch_assoc();
                return $vote[$field];
            }
            return "No vote recorded.";
        }

        function getMultipleVotes($conn, $table, $fields) {
            global $id, $last_name, $votersCode;
            $cols = implode(", ", $fields);
            $stmt = $conn->prepare("SELECT $cols FROM $table WHERE id = ? AND last_name = ? AND VotersCode = ?");
            $stmt->bind_param("iss", $id, $last_name, $votersCode);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return [];
        }

        echo "<div class='section'><strong>Mayor:</strong><br>&nbsp;&nbsp;" . htmlspecialchars(getSingleVote($conn, 'mayor', 'candidate_name')) . "</div>";
        echo "<div class='section'><strong>Vice Mayor:</strong><br>&nbsp;&nbsp;" . htmlspecialchars(getSingleVote($conn, 'vice_mayor', 'candidate_name')) . "</div>";
        echo "<div class='section'><strong>House Representative:</strong><br>&nbsp;&nbsp;" . htmlspecialchars(getSingleVote($conn, 'house_rep', 'candidate_name')) . "</div>";
        echo "<div class='section'><strong>Partylist:</strong><br>&nbsp;&nbsp;" . htmlspecialchars(getSingleVote($conn, 'partylist', 'partylist')) . "</div>";

        // Senators
        $senators = getMultipleVotes($conn, 'senator', [
            'sen1','sen2','sen3','sen4','sen5','sen6',
            'sen7','sen8','sen9','sen10','sen11','sen12'
        ]);
        echo "<div class='section'><strong>Senators:</strong><div class='vote-list'>";
        if (!empty($senators)) {
            $i = 1;
            foreach ($senators as $senator) {
                echo $i++ . ". " . htmlspecialchars($senator) . "<br>";
            }
        } else {
            echo "No vote recorded.";
        }
        echo "</div></div>";

        // Councilors
        $councilors = getMultipleVotes($conn, 'city_councilor', [
            'councilor1','councilor2','councilor3','councilor4','councilor5','councilor6',
            'councilor7','councilor8','councilor9','councilor10','councilor11','councilor12'
        ]);
        echo "<div class='section'><strong>City Councilors:</strong><div class='vote-list'>";
        if (!empty($councilors)) {
            $i = 1;
            foreach ($councilors as $councilor) {
                echo $i++ . ". " . htmlspecialchars($councilor) . "<br>";
            }
        } else {
            echo "No vote recorded.";
        }
        echo "</div></div>";
        ?>
    </div>
</div>

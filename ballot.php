<?php  
include("header.php");

// DB Connection
$conn = new mysqli("localhost", "root", "", "election");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch candidates by position
function getCandidates($position, $conn) {
    $stmt = $conn->prepare("SELECT candidate_name, candidate_number, partylist FROM candidate WHERE candidate_position = ?");
    $stmt->bind_param("s", $position);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidates = [];
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
    return $candidates;
}

function getUniquePartylists($conn) {
    $result = $conn->query("SELECT DISTINCT partylist FROM candidate WHERE partylist IS NOT NULL AND partylist != ''");
    $partylists = [];
    while ($row = $result->fetch_assoc()) {
        $partylists[] = $row['partylist'];
    }
    return $partylists;
}

$senators = getCandidates('Senator', $conn);
$house_reps = getCandidates('House Representative', $conn);
$mayors = getCandidates('Mayor', $conn);
$vice_mayors = getCandidates('Vice Mayor', $conn);
$city_councilors = getCandidates('City Councilor', $conn);
$partyLists = getUniquePartylists($conn);

function voterAlreadyVoted($conn, $votersCode) {
    $tables = ['senator', 'house_rep', 'mayor', 'vice_mayor', 'city_councilor', 'partylist'];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE VotersCode = ?");
        $stmt->bind_param("s", $votersCode);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) return true;
    }
    return false;
}

$voterValid = false;
$last_name = '';
$votersCode = trim($_POST['input_voters_code'] ?? '');
$voterId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($votersCode)) {
        $stmt = $conn->prepare("SELECT id, last_name FROM voters WHERE VotersCode = ?");
        $stmt->bind_param("s", $votersCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (voterAlreadyVoted($conn, $votersCode)) {
                echo "<p style='color:red;text-align:center;'>❌ You have already voted with this Voter's Code.</p>";
            } else {
                $voterValid = true;
                $last_name = $row['last_name'];
                $voterId = $row['id'];
            }
        } else {
            echo "<p style='color:red;text-align:center;'>❌ Invalid Voter's Code.</p>";
        }
    }

    if ($voterValid) {
        $senatorVotes = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = 'sen' . $i;
            if (!empty($_POST[$key])) $senatorVotes[] = $_POST[$key];
        }

        $house = $_POST['house_rep'] ?? '';
        $mayor = $_POST['mayor'] ?? '';
        $viceMayor = $_POST['vice_mayor'] ?? '';
        $partylistVotes = $_POST['partyList'] ?? '';
        $cityCouncilor = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = 'councilor' . $i;
            if (!empty($_POST[$key])) $cityCouncilor[] = $_POST[$key];
        }

        $errors = [];
        if (count(array_unique($senatorVotes)) !== 12) $errors[] = "Select 12 unique senators.";
        if (count(array_unique($cityCouncilor)) !== 12) $errors[] = "Select 12 unique councilors.";
        if (empty($house)) $errors[] = "House Representative is required.";
        if (empty($mayor)) $errors[] = "Mayor is required.";
        if (empty($viceMayor)) $errors[] = "Vice Mayor is required.";
        if (empty($partylistVotes)) $errors[] = "Partylist is required.";

        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO senator (id, last_name, VotersCode, sen1, sen2, sen3, sen4, sen5, sen6, sen7, sen8, sen9, sen10, sen11, sen12) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssssssss", $voterId, $last_name, $votersCode, ...$senatorVotes);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO house_rep (id, last_name, VotersCode, candidate_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $voterId, $last_name, $votersCode, $house);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO mayor (id, last_name, VotersCode, candidate_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $voterId, $last_name, $votersCode, $mayor);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO vice_mayor (id, last_name, VotersCode, candidate_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $voterId, $last_name, $votersCode, $viceMayor);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO city_councilor (id, last_name, VotersCode, councilor1, councilor2, councilor3, councilor4, councilor5, councilor6, councilor7, councilor8, councilor9, councilor10, councilor11, councilor12) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssssssss", $voterId, $last_name, $votersCode, ...$cityCouncilor);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO partylist (id, last_name, VotersCode, partylist) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $voterId, $last_name, $votersCode, $partylistVotes);
            $stmt->execute();

            echo "<p style='color:green;text-align:center;'>✅ Vote successfully submitted!</p>";
        } else {
            foreach ($errors as $error) {
                echo "<p style='color:red;text-align:center;'>❌ $error</p>";
            }
        }
    }
}
?>


<style>
    body {
        font-family: Arial, sans-serif;
         background-color: #fff8f0;
            color: #333;
            margin: 0;
            padding: 0;
        }
    form {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    h2, label, select, button {
        display: block;
        text-align: center;
        width: 100%;
    }
    select, button {
        margin: 5px 0 15px;
        padding: 8px;
    }
</style>

<form method="POST">
    <h2>🗳️ SENATOR / Vote for 12</h2>
    <h2>(Bumoto ng hindi hihigit sa 12)</h2>
    <?php for ($i = 1; $i <= 12; $i++): ?>
        <label>Senator <?= $i ?>:</label>
        <select name="sen<?= $i ?>" class="senator-dropdown" required>
            <option value="">--Select--</option>
            <?php foreach ($senators as $senator): ?>
                <option value="<?= htmlspecialchars($senator['candidate_name']) ?>">
                    <?= htmlspecialchars($senator['candidate_number'] . ' - ' . $senator['candidate_name'] . ' (' . $senator['partylist'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endfor; ?>

    <h2>🏛️ MEMBER, HOUSE REPRESENTATIVE / Vote for 1</h2>
    <h2>(Bumoto ng hindi hihigit sa 1)</h2>
    <select name="house_rep" required>
        <option value="">--Select--</option>
        <?php foreach ($house_reps as $rep): ?>
            <option value="<?= htmlspecialchars($rep['candidate_name']) ?>">
                <?= htmlspecialchars($rep['candidate_number'] . ' - ' . $rep['candidate_name'] . ' (' . $rep['partylist'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h2>👔 MAYOR / Vote for 1</h2>
    <h2>(Bumoto ng hindi hihigit sa 1)</h2>
    <select name="mayor" required>
        <option value="">--Select--</option>
        <?php foreach ($mayors as $mayor): ?>
            <option value="<?= htmlspecialchars($mayor['candidate_name']) ?>">
                <?= htmlspecialchars($mayor['candidate_number'] . ' - ' . $mayor['candidate_name'] . ' (' . $mayor['partylist'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h2>👔 VICE MAYOR / Vote for 1</h2>
    <h2>(Bumoto ng hindi hihigit sa 1)</h2>
    <select name="vice_mayor" required>
        <option value="">--Select--</option>
        <?php foreach ($vice_mayors as $vice): ?>
            <option value="<?= htmlspecialchars($vice['candidate_name']) ?>">
                <?= htmlspecialchars($vice['candidate_number'] . ' - ' . $vice['candidate_name'] . ' (' . $vice['partylist'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h2>🗳️ MEMBER, SANGUNIANG PANGLUNGSOD / Vote for 12</h2>
    <h2>(Bumoto ng hindi hihigit sa 12)</h2>
    <?php for ($i = 1; $i <= 12; $i++): ?>
        <label>City Councilor <?= $i ?>:</label>
        <select name="councilor<?= $i ?>" class="council-dropdown" required>
            <option value="">--Select--</option>
            <?php foreach ($city_councilors as $council): ?>
                <option value="<?= htmlspecialchars($council['candidate_name']) ?>">
                    <?= htmlspecialchars($council['candidate_number'] . ' - ' . $council['candidate_name'] . ' (' . $council['partylist'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endfor; ?>

    <h2>👔 PARTYLIST / Vote for 1</h2>
    <h2>(Bumoto ng hindi hihigit sa 1)</h2>
    <select name="partyList" required>
        <option value="">--Select--</option>
        <?php foreach ($partyLists as $party): ?>
            <option value="<?= htmlspecialchars($party) ?>"><?= htmlspecialchars($party) ?></option>
        <?php endforeach; ?>
    </select>

<h2>🔑 Enter Your Voter's Code to Proceed</h2>
<input type="text" name="input_voters_code" required placeholder="Enter Voter's Code" style="width:100%; padding:10px; margin-bottom:20px;">

    <button type="submit">Submit Vote</button>
</form>

<script>
    function disableSelected(dropdownClass) {
        document.querySelectorAll(dropdownClass).forEach(dropdown => {
            dropdown.addEventListener('change', () => {
                const selected = Array.from(document.querySelectorAll(dropdownClass)).map(d => d.value);
                document.querySelectorAll(dropdownClass).forEach(d => {
                    Array.from(d.options).forEach(opt => {
                        opt.disabled = selected.includes(opt.value) && d.value !== opt.value;
                    });
                });
            });
        });
    }

    disableSelected('.senator-dropdown');
    disableSelected('.council-dropdown');
</script>
<?php include 'footer.php'; ?>
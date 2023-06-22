<!DOCTYPE html>
<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $job = $_POST["job"];
    $room = $_POST["room"];
    $wage = $_POST["wage"];

    $stmt = $pdo->prepare('INSERT INTO employee (name, surname, job, room, wage) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $surname, $job, $room, $wage]);
    $employee_id = $pdo->lastInsertId();

    if (isset($_POST["rooms"])) {
        $rooms = $_POST["rooms"];
        foreach ($rooms as $room_id) {
            $stmt = $pdo->prepare('INSERT INTO `key` (employee, room) VALUES (?, ?)');
            $stmt->execute([$employee_id, $room_id]);
        }
    } 
}


$sort = filter_input(INPUT_GET, 'poradi', FILTER_SANITIZE_STRING);
if (!$sort) {
$sortStmt = $pdo->query("SELECT * FROM employee");
}

if ($sort == "prijmeni_down") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY surname DESC");
} 
else if ($sort == "mistnost_down") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY room DESC");
} 
else if ($sort == "telefon_down") {
$sortStmt = $pdo->query("SELECT * FROM room JOIN employee ON room = room_id ORDER BY phone DESC");
} 
else if ($sort == "pozice_down") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY job DESC");
} 
else if ($sort == "prijmeni_up") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY surname ASC");
} 
else if ($sort == "mistnost_up") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY room ASC");
}
else if ($sort == "telefon_up") {
$sortStmt = $pdo->query("SELECT * FROM room JOIN employee ON room = room_id ORDER BY phone ASC");
} 
else if ($sort == "pozice_up") {
$sortStmt = $pdo->query("SELECT * FROM employee ORDER BY job ASC");
} 
?>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Employees list</title>
</head>
<body class="container">

<?php
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1) {
?>
<h2>Přidat zaměstnance</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group">
        <label for="name">Jméno:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="surname">Příjmení:</label>
        <input type="text" class="form-control" id="surname" name="surname" required>
    </div>
    <div class="form-group">
        <label for="job">Práce:</label>
        <input type="text" class="form-control" id="job" name="job" required>
    </div>
    <div class="form-group">
        <label for="room">Místonost:</label>
        <select class="form-control" id="room" name="room" required>
            <?php
            $stmt = $pdo->query("SELECT * FROM room");
            while ($room = $stmt->fetch()) {
                echo '<option value="'.$room["room_id"].'">'.$room["name"].'</option>';
            }
            unset($stmt);
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="wage">Plat:</label>
        <input type="text" class="form-control" id="wage" name="wage" required>
    </div>
    <div class="form-group">
        <label for="rooms">Klíče:</label><br>
        <?php
        $stmt = $pdo->query("SELECT * FROM room");
        while ($room = $stmt->fetch()) {
            echo '<label><input type="checkbox" name="rooms[]" value="'.$room["room_id"].'"> '.$room["name"].'</label><br>';
        }
        unset($stmt);
        ?>
    </div>
    <button type="submit" class="btn btn-primary">Přidat zaměstnance</button>
</form>
<?php
}
?>

<?php
echo '<h1>Seznam zaměstnanců</h1>';

//tabulky
if ($sortStmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
echo '<table class="table">
<thead><tr>
<th>Jméno
<a href="?poradi=prijmeni_up"'.sorter("prijmeni_up",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "prijmeni_up" ? "down text-danger" : "down").'" aria-hidden="true"></span></a>
<a href="?poradi=prijmeni_down"'.sorter("prijmeni_down",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "prijmeni_down" ? "up text-danger" : "up").'" aria-hidden="true"></span></a>
</th>';
echo '<th>Místonost
<a href="?poradi=mistnost_up"'.sorter("mistnost_up",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "mistnost_up" ? "down text-danger" : "down").'" aria-hidden="true"></span></a>
<a href="?poradi=mistnost_down"'.sorter("mistnost_down",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "mistnost_down" ? "up text-danger" : "up").'" aria-hidden="true"></span></a>
</th>';
echo '<th>Telefon
<a href="?poradi=telefon_up"'.sorter("telefon_up",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "telefon_up" ? "down text-danger" : "down").'" aria-hidden="true"></span></a>
<a href="?poradi=telefon_down"'.sorter("telefon_down",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "telefon_down" ? "up text-danger" : "up").'" aria-hidden="true"></span></a>
</th>';
echo '<th>Pozice
<a href="?poradi=pozice_up"'.sorter("pozice_up",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "pozice_up" ? "down text-danger" : "down").'" aria-hidden="true"></span></a>
<a href="?poradi=pozice_down"'.sorter("pozice_down",$sort).'><span class="glyphicon glyphicon-arrow-'.($sort == "pozice_down" ? "up text-danger" : "up").'" aria-hidden="true"></span></a>
</th>';
echo '</tr></thead><tbody>';

while ($employee = $sortStmt->fetch()) {
echo '<tr>';
echo '<td><a href="EmployeeCard.php?id='.$employee["employee_id"].'">'.$employee["surname"]." ".$employee["name"]."</a></td>";
echo '<td>'.fetchRoomName($employee["room"], $pdo).'</td>';
echo '<td>'.fetchPhoneNumber($employee["room"], $pdo).'</td>';
echo '<td>'.$employee["job"].'</td>';
echo '</tr>';
}
echo '</tbody></table>';
}

echo "</tbody></table>";
echo '<div style="position:static !important"></div>';
unset($stmt);
echo '<br>';
?>
<a href='MainMenu.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span>Zpět na Prohlížeč databáze</a>
</body>
</html>
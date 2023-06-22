<!DOCTYPE html>
<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
  }

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,["options" => ["min_range" => 1]]);

if(!$id) {
throwError(400);
}

$stmt = $pdo->query("SELECT * FROM room WHERE room_id = $id");

if ($stmt->rowCount() == 0) {
    throwError(404);
}

$room = $stmt->fetch();
?>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Room card</title>
</head>
<body class="container">

<h1>Karta místnosti: <i><?php echo fetchRoomName($room["room_id"], $pdo); ?></i>
<?php
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1) {
?>
    <a href="editRoom.php?id=<?php echo $room['room_id']; ?>">Upravit</a>
    <form action="deleteroom.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
        <button type="submit" class="btn btn-danger">Odstranit</button>
    </form>
    <?php
}
?>
</h1> 

<?php
$name = $room["name"];
$no = $room["no"];
$phone = $room["phone"];

//hodnoty
echo '<h1>Místnost č.'.$no.'</h1>';
echo 
'<dl class="dl-horizontal">
<st>Číslo: </st><bt>'.$no.'</bt><br>
<st>Název: </st><bt>'.$name.'</bt><br>
<st>Telefon: </st><bt>'.$phone.'</bt><br>
<st>Lidé: </st>';

$employeeStmt = $pdo->query("SELECT * FROM employee WHERE room = $id");
$employees = $employeeStmt->fetchAll();

if ($employeeStmt->rowCount() == 0)
echo '<bt>---</bt>';
else
{
foreach($employees as $employee) {
echo '<bt><a href="EmployeeCard.php?id='.$employee["employee_id"].'">'.fetchEmployeeName($employee["employee_id"], $pdo).'</a></bt>';
}
}

if ($employeeStmt->rowCount() == 0)
echo '<st>Průměrná mzda</st><bt>---</bt>';
else {
echo '<st>Průměrná mzda</st><bt>' . FetchAvgRoomSalary($id, $pdo) . '</bt>';
}

echo '<br><st>Klíče</st>';
$keyStmt = $pdo->query("SELECT * FROM c136ip_3.key WHERE room = $id");
$keys = $keyStmt->fetchAll();
foreach($keys as $key) {
echo '<bt><a href="EmployeeCard.php?id='.$key["employee"].'">'.fetchEmployeeName($key["employee"], $pdo).'</a></bt>';
}
?>
</dl>
<a href='RoomsList.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span>Zpět na seznam místností</a>
</body>
</html>
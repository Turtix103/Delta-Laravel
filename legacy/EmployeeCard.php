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

$stmt = $pdo->query("SELECT * FROM employee WHERE employee_id = $id");
$keystmt = $pdo->query("SELECT * FROM c136ip_3.key WHERE employee = $id ORDER BY key_id");

if ($stmt->rowCount() == 0) {
throwError(404);
}

$employee = $stmt->fetch();
$keys = $keystmt->fetchAll();
?>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Karta osoby</title>
</head>
<body>
<div class="container">
    
<h1>Karta osoby: <i><?php echo fetchEmployeeName($employee["employee_id"], $pdo); ?></i>
<?php
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1) {
?>
    <a href="editEmployee.php?id=<?php echo $employee['employee_id']; ?>">Upravit</a>
    <form action="deleteEmployee.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
        <button type="submit" class="btn btn-danger">Odstranit</button>
    </form>
    <?php
}
?>
</h1> 

<?php 
$name = $employee["name"];
$surname = $employee["surname"];
$job = $employee["job"];
$wage = $employee["wage"];
$room = fetchRoomName($employee["room"], $pdo);

//hodnoty
echo '<h1>Zaměstnanec: <i>'.fetchEmployeeName($employee["employee_id"], $pdo).'</i></h1>';
echo '<dl class="dl-horizontal"><br>'
.'<st>Jméno: </st><bt>'.$name.'</bt><br>'
.'<st>Příjmení: </st><bt>'.$surname.'</bt><br>'
.'<st>Pozice: </st><bt> '.$job.'</bt><br>'
.'<st>Mzda: </st><bt>'.$wage.'</bt><br>'
.'<st>Místnost: </st><bt> <a href="RoomCard.php?id='.$employee["room"].'">'.$room.'</a></bt><br>'
.'<st>Klíče: </st>';

//klice
foreach($keys as $key) {
$name = fetchRoomName($key["room"], $pdo);
echo '<bt><a href="RoomCard.php?id='.$key["room"].'">'.$name. '</a></bt>';
}



?>
</dl>
<a href='EmployeesList.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span>Zpět na seznam zaměstnanců</a>
</div>
</body>
</html>
<?php
$host = '127.0.0.1';
$db = 'c136ip_3';
$user = 'c136admin';
$pass = '6942069420';
$charset = 'utf8mb4';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

echo '<style>
bt {
padding:2px 4px 2px 4px;
outline: #212121 solid 1px;
}
st {
padding:2px 4px 2px 4px;
outline: solid 1px;
}
.employee-table {
padding-left: 500px;
}
</style>';

function throwError($error)
{
http_response_code($error);
echo"<h1>$error) ERROR</h1>";
exit;
}

function fetchPhoneNumber($id, $pdo) 
{
$stmt = $pdo->query("SELECT * FROM room WHERE room_id = $id");
$phone = $stmt->fetch();
return $phone["phone"];
}
function fetchRoomName($id, $pdo) 
{
$stmt = $pdo->query("SELECT * FROM room WHERE room_id = $id");
$room = $stmt->fetch();
return $room["name"];
}
function fetchEmployeeName($id, $pdo) 
{
$stmt = $pdo->query("SELECT * FROM employee WHERE employee_id = $id");
$row = $stmt->fetch();
return $row['surname'] . " " . substr($row['name'], 0, 1) . ".";
}

function FetchAvgRoomSalary($id, $pdo) 
{
$stmt = $pdo->query("SELECT AVG(wage) FROM employee WHERE room = $id");
$row = $stmt->fetch();
return $row['AVG(wage)'];
}

function sorter($current,$sort)
{
if($current == $sort)
return 'class="sorted"';

return "";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Připojení k DB</title>
</head>
<body>
<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
  }

echo '<div class ="container"> <h1>Prohlížeč databáze</h1>';
echo '<ul class="list-group">';
echo '<li class="list-group-item"> <a href="EmployeesList.php"> Seznam zaměstnanců </a></li>';
echo '<li class="list-group-item"> <a href="RoomsList.php"> Seznam místnosti </a></li>';
echo '</ul>';
echo '<a href="index.php" class="btn btn-default">Odhlásit se</a>';
echo '<a href="changePassword.php" class="btn btn-default">Změnit heslo</a>';
echo "</div>";

unset($stmt);
?>
</body>
</html>
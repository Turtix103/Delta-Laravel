<?php
session_start();
require_once 'Database.php';

if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
    $room_id = $_POST['room_id'];

    $stmt = $pdo->prepare('DELETE FROM room WHERE room_id = ?');
    $stmt->execute([$room_id]);

    header('Location: RoomsList.php');
    exit; 
   } catch (PDOException $e) {
    echo "Byl zde error, nejpíše protože místnost má někdo jako svojí domovskou, kdyby jste si nebyly jistí zde je errorMessage:" ;
    echo '<br>';
    echo "". $e->getMessage();
    echo '<br>';
    echo '<a href="RoomsList.php" class="btn btn-default">Odejít</a>';
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $room_id = $_GET['id'];

    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE room_id = ?');
    $stmt->execute([$room_id]);

    if ($stmt->rowCount() == 0) {
        throwError(404);
    }

    $room = $stmt->fetch();
}
?>

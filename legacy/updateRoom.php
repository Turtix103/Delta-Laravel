<?php
session_start();
require_once 'Database.php';

if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];

    $name = $_POST['name'];
    $no = $_POST['no'];
    $phone = $_POST['phone'];

    // Check for duplicates
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM room WHERE phone = ? AND room_id != ?');
    $stmt->execute([$phone, $room_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header('Location: RoomCard.php?id=' . $room_id . '&error=duplicate_phone');
    }

    // Update the row
    $stmt = $pdo->prepare('UPDATE room SET name = ?, no = ?, phone = ? WHERE room_id = ?');
    $stmt->execute([$name, $no, $phone, $room_id]);

    header('Location: RoomCard.php?id=' . $room_id);
    exit;
} ?>
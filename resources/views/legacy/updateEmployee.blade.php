<?php
session_start();
require_once 'Database.php';

if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
    
    $stmt = $pdo->prepare('SELECT * FROM employee WHERE employee_id = ?');
    $stmt->execute([$employee_id]);
    $current_employee = $stmt->fetch();
    
    $name = $_POST['name'] ?: $current_employee['name'];
    $surname = $_POST['surname'] ?: $current_employee['surname'];
    $job = $_POST['job'] ?: $current_employee['job'];
    $wage = $_POST['wage'] ?: $current_employee['wage'];
    $room = $_POST['room'] ?: $current_employee['room'];
    
    $stmt = $pdo->prepare('UPDATE employee SET name = ?, surname = ?, job = ?, wage = ?, room = ? WHERE employee_id = ?');
    $stmt->execute([$name, $surname, $job, $wage, $room, $employee_id]);

if (isset($_POST["rooms"])) {
    $rooms = $_POST["rooms"];
    $stmt = $pdo->prepare('SELECT * FROM `key` WHERE employee = ?');
    $stmt->execute([$employee_id]);
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rooms)) {
        // The user did not check or uncheck any checkboxes, so delete all keys
        foreach ($keys as $key) {
            $stmt = $pdo->prepare('DELETE FROM `key` WHERE employee = ? AND room = ?');
            $stmt->execute([$employee_id, $key["room"]]);
        }
    } else {
        foreach ($keys as $key) {
            $room_id = $key["room"];
            if (in_array($room_id, $rooms)) {
                // The key is already assigned to the employee
                // and the checkbox is checked, do nothing
                $index = array_search($room_id, $rooms);
                unset($rooms[$index]);
            } else {
                // The key is assigned to the employee
                // but the checkbox is unchecked, delete the key
                $stmt = $pdo->prepare('DELETE FROM `key` WHERE employee = ? AND room = ?');
                $stmt->execute([$employee_id, $room_id]);
            }
        }
        foreach ($rooms as $room_id) {
            // The key is not assigned to the employee
            // and the checkbox is checked, add the key
            $stmt = $pdo->prepare('INSERT INTO `key` (employee, room) VALUES (?, ?)');
            $stmt->execute([$employee_id, $room_id]);
        }
    }
} else {
    // The user did not check or uncheck any checkboxes, so delete all keys
    $stmt = $pdo->prepare('DELETE FROM `key` WHERE employee = ?');
    $stmt->execute([$employee_id]);
}

    header('Location: EmployeeCard.php?id=' . $employee_id);
    exit;
} ?>
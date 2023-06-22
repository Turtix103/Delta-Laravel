<?php
    session_start();
    require_once 'Database.php';

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('SELECT * FROM employee WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();


    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['employee_id'];
        $_SESSION['isAdmin'] = $user['admin'];
        header('Location: MainMenu.php');
        exit;
    } else {
        echo 'Username: ' . $username . '<br>';
        echo 'Password: ' . $password . '<br>';
        echo'Invalid username or password';
    }
}
?>
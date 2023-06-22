<?php
session_start();
require_once 'Database.php';

if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM `key` WHERE employee = ?');
    $stmt->execute([$employee_id]);

    $stmt = $pdo->prepare('DELETE FROM employee WHERE employee_id = ?');
    $stmt->execute([$employee_id]);

    $pdo->commit();

    header('Location: EmployeesList.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $employee_id = $_GET['id'];

    $stmt = $pdo->prepare('SELECT * FROM employees WHERE employee_id = ?');
    $stmt->execute([$employee_id]);

    if ($stmt->rowCount() == 0) {
        throwError(404);
    }

    $employee = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Smazat zeměstnance</title>
</head>
<body>
    <h1>Smazat zeměstnance</h1>
    <p>Jste si jistu že chcete smazat zaměstnance "<?php echo $employee['name'] . ' ' . $employee['surname']; ?>"?</p>
    <form method="post">
        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
        <input type="submit" value="Delete">
        <a href="EmployeeCard.php?id=<?php echo $employee['employee_id']; ?>">Zrušit</a>
    </form>
</body>
</html>
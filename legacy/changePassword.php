<?php
session_start();
require_once 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'];
  $password2 = $_POST['password2'];

  if ($password !== $password2) {
    echo "Hesla se neschodují!";
  } else {
    $user_id = $_SESSION['user_id'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE employee SET password = ? WHERE employee_id = ?');
    $stmt->execute([$hashed_password, $user_id]);
    if (!$stmt->execute()) {
        echo "Error updating password: ";
        exit;
    }
    echo "Heslo změneno!";
  }
}
?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <div class="form-group">
    <label for="password">New Password:</label>
    <input type="password" class="form-control" id="password" name="password" required>
  </div>
  <div class="form-group">
    <label for="password2">Confirm New Password:</label>
    <input type="password" class="form-control" id="password2" name="password2" required>
  </div>
  <button type="submit" class="btn btn-primary">Change Password</button>
  <a href='MainMenu.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span>Zpět na Prohlížeč databáze</a>
</form>

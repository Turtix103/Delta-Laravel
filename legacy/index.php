<!DOCTYPE html>
<?php
session_start();
unset($_SESSION['loggedin']);
?>
<html>
  <head>
    <title>Přihlášení</title>
  </head>
  <body>
    <h1>Přihlášení</h1>
    <form action="login.php" method="post">
      <label for="username">Uživatelskě jméno:</label>
      <input type="text" name="username" id="username" required><br><br>
      <label for="password">Heslo:</label>
      <input type="password" name="password" id="password" required><br><br>
      <input type="submit" value="Login">
    </form>
  </body>
</html>
<!DOCTYPE html>
<?php
session_start();
require_once 'Database.php';

if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) || !(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === 1)) {
    header("Location: index.php");
    exit;
}


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,["options" => ["min_range" => 1]]);

if(!$id) {
    throwError(400);
}

$stmt = $pdo->query("SELECT * FROM room WHERE room_id = $id");
$keystmt = $pdo->query("SELECT * FROM c136ip_3.key WHERE room = $id ORDER BY key_id");

if ($stmt->rowCount() == 0) {
    throwError(404);
}

$room = $stmt->fetch();
$keys = $keystmt->fetchAll();
?>
<html>
<head>
  <meta charset="UTF-8">
  <title>Upravit místnosti</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2>Upravit místnost</h2>
                <form method="post" action="updateRoom.php">
                    <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $room['name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="no">no:</label>
                        <input type="text" class="form-control" id="no" name="no" value="<?php echo $room['no']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon:</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $room['phone']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Upravit</button>
                    <a href="RoomsList.php" class="btn btn-default">Zrušit</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
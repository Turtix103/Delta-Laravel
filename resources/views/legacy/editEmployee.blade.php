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

$stmt = $pdo->query("SELECT * FROM employee WHERE employee_id = $id");
$keystmt = $pdo->query("SELECT * FROM c136ip_3.key WHERE employee = $id ORDER BY key_id");

if ($stmt->rowCount() == 0) {
    throwError(404);
}

$employee = $stmt->fetch();
$keys = $keystmt->fetchAll();
?>
<html>
<head>
  <meta charset="UTF-8">
  <title>Upravit zaměstance</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2>Upravit zaměstnance</h2>
                <form method="post" action="updateEmployee.php">
                    <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                    <div class="form-group">
                        <label for="name">Jméno:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $employee['name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="surname">Příjmení:</label>
                        <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $employee['surname']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="job">Práce:</label>
                        <input type="text" class="form-control" id="job" name="job" value="<?php echo $employee['job']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="wage">Plat:</label>
                        <input type="text" class="form-control" id="wage" name="wage" value="<?php echo $employee['wage']; ?>">
                    </div>
                    <div class="form-group">
                     <label for="room">Místnost:</label>
                      <select class="form-control" id="room" name="room">
                       <?php
                            $rooms = $pdo->query("SELECT * FROM room ORDER BY name")->fetchAll();
                            foreach ($rooms as $room) {
                            $selected = "";
                            if ($room['room_id'] == $employee['room']) {
                             $selected = "selected";
                            }
                            echo "<option value='" . $room['room_id'] . "' $selected>" . $room['name'] . "</option>";
                            }
                        ?>
                      </select>
                    </div>
                    <div class="form-group">
  <label for="rooms">Klíče:</label><br>
  <?php
  $stmt = $pdo->prepare("SELECT * FROM room LEFT JOIN `key` ON room.room_id = `key`.room AND `key`.employee = ?");
  $employee_id = $employee['employee_id'];
  $stmt->execute([$employee_id]);
  while ($room = $stmt->fetch()) {
    $checked = $room["employee"] ? "checked" : "";
    echo '<label><input type="checkbox" name="rooms[]" value="'.$room["room_id"].'" '.$checked.'> '.$room["name"].'</label><br>';
  }
  unset($stmt);
  ?>
</div>
                    <button type="submit" class="btn btn-primary">Upravit</button>
                    <a href="EmployeesList.php" class="btn btn-default">Zrušit</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
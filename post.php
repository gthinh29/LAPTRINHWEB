<?php
include 'database.php';
$id = $_GET['id'];
$query = "SELECT * FROM Posts WHERE id = ?";
$stmt = sqlsrv_query($conn, $query, array($id));
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title><?php echo $row['title']; ?></title></head>
<body>
  <h1><?php echo $row['title']; ?></h1>
  <p><?php echo nl2br($row['content']); ?></p>
</body>
</html>
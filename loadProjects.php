<?php
require_once("connectdatab.php");

$stmt = $db->prepare("SELECT * FROM projects LEFT JOIN users ON users.uid=projects.uid");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($projects);
?>
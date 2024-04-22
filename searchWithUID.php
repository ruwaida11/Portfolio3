<?php
require_once("connectdatab.php");
// fetch uid using session cookie
$session_id = $_COOKIE['session_id'];
$stat = $db->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
$stat->execute([$session_id]);
$row = $stat->fetch(PDO::FETCH_ASSOC);
$uid = $row['uid'];

$stat = $db->prepare("SELECT * FROM projects p LEFT JOIN users u on u.uid=p.uid WHERE p.uid = ?");
$stat->execute([$uid]);

$projects = $stat->fetchAll(PDO::FETCH_ASSOC);

$projectTiles = [];
foreach ($projects as $project) {
    $projectTile = [
        'title' => $project['title'],
        'start_date' => $project['start_date'],
        'description' => $project['description'],
        'end_date' => $project['end_date'],
        'email' => $project['email'],
        'edit_button' => '<button class="edit-button" onclick="editProject()">E</button>'
    ];

    $projectTiles[] = $projectTile;
}

echo json_encode($projectTiles);
?>

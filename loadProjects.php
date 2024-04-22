<?php
require_once("connectdatab.php");

// Fetch the session id
$session_id = isset($_COOKIE['session_id']) ? $_COOKIE['session_id'] : null;

// Check if session id exists
$uid = null;
if (!empty($session_id)) {
    $stat = $db->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
    $stat->execute([$session_id]);
    $row = $stat->fetch(PDO::FETCH_ASSOC);
    $uid = $row['uid'];
}

// Fetch projects
$stat = $db->prepare("SELECT * FROM projects LEFT JOIN users ON users.uid=projects.uid");
$stat->execute();
$projects = $stat->fetchAll(PDO::FETCH_ASSOC);

$projectTiles = [];
foreach ($projects as $project) {
    $projectTile = [
        'title' => $project['title'],
        'start_date' => $project['start_date'],
        'description' => $project['description'],
        'end_date' => $project['end_date'],
        'email' => $project['email'],
        'edit_button' => ''
    ];

    if ($uid != null && $project['uid'] == $uid) {
        $projectTile['edit_button'] = '<button class="edit-button" href="/editProject.php">&#9998;</button>';
    }

    $projectTiles[] = $projectTile;
}

echo json_encode($projectTiles);
?>

<?php
require_once("connectdatab.php");
// get uid using session cookie
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
        'phase' => $project['phase'],
        'edit_button' => "<form action=\"editProject.php\"> 
                            <input type=\"hidden\" name=\"pid\" value=\"" . htmlspecialchars($project['pid']) . "\" />
                            <input type=\"hidden\" name=\"project_title\" value=\"" . htmlspecialchars($project['title']) . "\" />
                            <input type=\"hidden\" name=\"project_start_date\" value=\"" . htmlspecialchars($project['start_date']) . "\" />
                            <input type=\"hidden\" name=\"project_description\" value=\"" . htmlspecialchars($project['description']) . "\" />
                            <input type=\"hidden\" name=\"project_end_date\" value=\"" . htmlspecialchars($project['end_date']) . "\" />
                            <input type=\"hidden\" name=\"project_phase\" value=\"" . htmlspecialchars($project['phase']) . "\" />
                            <input class=\"edit-button\" type=\"submit\" value=\"&#9998;\" /> 
                        </form>"
    ];

    $projectTiles[] = $projectTile;
}

echo json_encode($projectTiles);
?>

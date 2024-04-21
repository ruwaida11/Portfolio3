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

    echo json_encode($projects);
?>

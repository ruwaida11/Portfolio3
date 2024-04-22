<?php
require_once("connectdatab.php");

if (isset($_GET['search'], $_GET['date'])) {
    $searchTerm = !empty($_GET['search']) ? '%' . $_GET['search'] . '%' : '';
    $dateFilter = !empty($_GET['date']) ? "AND start_date = ?" : '';
    $whereClause = '';

    if (!empty($searchTerm) || !empty($dateFilter)) {
        $whereClause .= " WHERE";
        if (!empty($searchTerm)) {
            $whereClause .= " title LIKE ?";
        }
        if (!empty($dateFilter)) {
            if (!empty($searchTerm)) {
                $whereClause .= " AND";
            }
            $whereClause .= " start_date = ?";
        }
    }

    $stat = $db->prepare("SELECT * FROM projects LEFT JOIN users ON users.uid=projects.uid $whereClause");

    $index = 1; // Parameter index for binding
    // Bind search term if not empty
    if (!empty($searchTerm)) {
        $stat->bindParam($index++, $searchTerm);
    }
    // Bind date filter if not empty
    if (!empty($_GET['date'])) {
        $stat->bindParam($index++, $_GET['date']);
    }

    $stat->execute();

    $projects = $stat->fetchAll(PDO::FETCH_ASSOC);

    // get uid if theres an active session
    $session_id = isset($_COOKIE['session_id']) ? $_COOKIE['session_id'] : null;
    $uid = null;

    if ($session_id) {
        $stat = $db->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
        $stat->execute([$session_id]);
        $row = $stat->fetch(PDO::FETCH_ASSOC);
        $uid = $row ? $row['uid'] : null;
    }

    $projectTiles = [];
    foreach ($projects as $project) {
        $projectTile = [
            'title' => $project['title'],
            'start_date' => $project['start_date'],
            'description' => $project['description'],
            'end_date' => $project['end_date'],
            'email' => $project['email'],
            'edit_button' => '' // Initialize edit_button
        ];

        // Add edit button if session id exists and the project belongs to the user
        if ($uid != null && $project['uid'] == $uid) {
            $projectTile['edit_button'] = '<button class="edit-button" onclick="editProject()">E</button>';
        }

        $projectTiles[] = $projectTile;
    }

    echo json_encode($projectTiles);
}
?>

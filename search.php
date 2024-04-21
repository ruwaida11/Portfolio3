<?php
require_once("connectdatab.php");

if (isset($_GET['search'], $_GET['date'])) {
    $searchTerm = !empty($_GET['search']) ? '%' . $_GET['search'] . '%' : '';
    $dateFilter = !empty($_GET['date']) ? "AND start_date = ?" : '';
    $whereClause = '';

    // Construct the WHERE clause based on search term and date filter
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

    // Prepare the SQL statement with placeholders
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

    // Execute the query
    $stat->execute();

    // Fetch the results
    $projects = $stat->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projects);
}
?>

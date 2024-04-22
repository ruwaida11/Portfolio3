<?php
if (isset($_POST['submitted'])){
    if (!isset($_POST['projectName'], $_POST['startDate'], $_POST['endDate'], $_POST['description'], $_POST['pid'])) {
        exit('Please fill out all fields in the form!');
    }
    $projectName = trim($_POST['projectName']);
    $pid = $_POST['pid']; 

    require_once ("connectdatab.php");
    try {
        
        $session_id = $_COOKIE['session_id'];
        $stat = $db->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
        $stat->execute([$session_id]);
        $row = $stat->fetch(PDO::FETCH_ASSOC);
        $uid = $row['uid'];

        $stat = $db->prepare("SELECT COUNT(*) FROM projects WHERE pid = ? AND uid = ?");
        $stat->execute([$pid, $uid]);
        $count = $stat->fetchColumn();
        
        if ($count == 0) {
            echo "<p style='color:red'>The project does not exist or you don't have permission to edit it!</p>";
        } else {
            $stat = $db->prepare("UPDATE projects SET title=?, start_date=?, end_date=?, phase=?, description=? WHERE pid=? AND uid=?");
            $stat->execute([$projectName, $_POST['startDate'], $_POST['endDate'], $_POST['phase'], $_POST['description'], $pid, $uid]);
            header("Location:index.html");
            echo "<p style='color:red'>Project Updated Successfully</p>";
        } 
    }
    catch(PDOException $ex) {
        echo("Failed to connect to the database.<br>");
        echo($ex->getMessage());
        exit;
    }
}
?>

<html>
<head>
	<title>Edit Project</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<header>
		<div class="container">
			<h1><a href="http://localhost/Port3/index.html">AProject</a></h1>
			<h2>Edit Project</h2>
			<a href="logout.php" class="login">Logout</a>
		</div>
	</header>
	<form action="editProject.php" method="post" id="editProject" class="loginform">
    	<input type="hidden" name="pid" value="<?php echo isset($_GET['pid']) ? htmlspecialchars($_GET['pid']) : ''; ?>">    
		<label for="projectName">Project Name</label>
		<input type="text" name="projectName" id="projectName" maxlength="25" required value="<?php echo htmlspecialchars($_GET['project_title']); ?>">
		<label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" required value="<?php echo htmlspecialchars($_GET['project_start_date']); ?>">
		<label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" required value="<?php echo htmlspecialchars($_GET['project_end_date']); ?>">
		<label for="phase">Phase</label>
		<select id="phase" name="phase" class="select-style">
			<option value="design"<?php if($_GET['project_phase'] == 'design') echo ' selected'; ?>>Design</option>
			<option value="development"<?php if($_GET['project_phase'] == 'development') echo ' selected'; ?>>Development</option>
			<option value="testing"<?php if($_GET['project_phase'] == 'testing') echo ' selected'; ?>>Testing</option>
			<option value="deployment"<?php if($_GET['project_phase'] == 'deployment') echo ' selected'; ?>>Deployment</option>
			<option value="complete"<?php if($_GET['project_phase'] == 'complete') echo ' selected'; ?>>Complete</option>
		</select>
		<label for="description">Description:</label>
		<textarea name="description" id="description" maxlength="250" required rows="4"><?php echo htmlspecialchars($_GET['project_description']); ?></textarea>
        <input type="submit" value="Save Changes">
        <input type="hidden" name="submitted" value="TRUE">
    </form>
<footer>
        <p>&copy; 2024 AProject. Aston University. All rights reserved.</p>
    </footer>
</body>
</html>

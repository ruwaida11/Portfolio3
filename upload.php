<?php
if (isset($_POST['submitted'])){
	if ( !isset($_POST['projectName'], $_POST['startDate'], $_POST['endDate'], $_POST['description']) ) {
		exit('Please fill out all fields in the form!');
	}
	$projectName = trim($_POST['projectName']);
	require_once ("connectdatab.php");
	try {
		// get uid using session cookie
		$session_id = $_COOKIE['session_id'];
		$stat = $db->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
		$stat->execute([$session_id]);
		$row = $stat->fetch(PDO::FETCH_ASSOC);
		$uid = $row['uid'];

		// Each user should have unique project names
		$stat = $db->prepare("SELECT COUNT(*) FROM projects WHERE title = ? AND uid = ?");
		$stat->execute([$projectName, $uid]);
		$count = $stat->fetchColumn();
		
		if ($count > 0) {
			echo "<p style='color:red'>A project with the same name already exists! </p>";
		} else {
			// Insert the project into db
			$stat = $db->prepare("INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (?, ?, ?, ?, ?, ?)");
			$stat->execute([$projectName, $_POST['startDate'], $_POST['endDate'], $_POST['phase'], $_POST['description'], $uid]);

			echo "<p style='color:red'>Project Uploaded Successfully</p>";
			header("Location:index.html");
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
	<title>Upload Project</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<header>
		<div class="container">
			<h1><a href="http://localhost/Port3/index.html">AProject</a></h1>
			<h2>Upload Project</h2>
			<a href="logout.php" class="login">Logout</a>
		</div>
	</header>
	<form action="upload.php" method="post" id="upload" class="loginform">
        <label for="projectName">Project Name</label>
        <input type="text" name="projectName" id="projectName" maxlength="25" required>
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" required>
		<label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" required>
		<label for="phase">Phase</label>
		<select id="phase" name="phase" class="select-style">
			<option value="Design">Design</option>
			<option value="Development">Development</option>
			<option value="Testing">Testing</option>
			<option value="Deployment">Deployment</option>
			<option value="Complete">Complete</option>
		</select>
		<label for="description">Description:</label>
		<textarea name="description" id="description" maxlength="250" required rows="4"></textarea>
        <input type="submit" value="Upload">
        <input type="hidden" name="submitted" value="TRUE">
    </form>
<footer>
        <p>&copy; 2024 AProject. Aston University. All rights reserved.</p>
    </footer>
</body>
</html>

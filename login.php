<?php
	$error_message = "";
	if (isset($_POST['submitted'])){
		if ( !isset($_POST['username'], $_POST['password']) ) {
		 exit('Please fill both the username and password fields!');
	    }
		require_once ("connectdatab.php");
		try {
			$stat = $db->prepare('SELECT uid, password FROM users WHERE username = ?');
			$stat->execute(array($_POST['username']));
		    
			// check user exists
			if ($stat->rowCount()>0){
				$row=$stat->fetch();
				if($row){
					if (password_verify($_POST['password'], $row['password'])){ //matching password
						// Generate session ID valid for 5hrs
						session_start();
						$uid = $row['uid'];
						$session_id = bin2hex(random_bytes(32));
						setcookie('session_id', $session_id, time() + (3600 * 5), '/');

						// INSERT session_id into db and map to logged in users uid
						$stat = $db->prepare("INSERT INTO user_sessions (session_id, uid, created_at) VALUES (?, ?, NOW())");
						$stat->execute([$session_id, $uid]);
						header("Location:index.html");
						exit();
					} else {
						$error_message = "<p style='color:red'>Error logging in, password does not match </p>";
					}
				} else {
					$error_message = "<p style='color:red'>Error logging in, user does not exist </p>";
				}
		    } else {
			  $error_message = "<p style='color:red'>Error logging in, Username not found </p>";
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
	<title>Login</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<header>
		<div class="container">
			<h1><a href="http://localhost/Port3/index.html">AProject</a></h1>
			<h2>Login</h2>
			<a href="http://localhost/Port3/login.php" class="login">Login</a>
		</div>
	</header>
	<form action="login.php" method="post" class="loginform">
        <label for="username">User Name</label>
        <input type="text" name="username" id="username" maxlength="25" required>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" maxlength="25" required>
        <input type="submit" value="Login">
        <input type="hidden" name="submitted" value="TRUE">
		<div style="color: red;"><?php echo $error_message; ?></div>
        <p>Not registered yet? <a href="register.php">Register</a></p>
    </form>
<footer>
        <p>&copy; 2024 AProject. Aston University. All rights reserved.</p>
    </footer>
</body>
</html>

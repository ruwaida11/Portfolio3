<?php
$error_message = '';
$success_message = '';
$username = '';
$email = '';

if (isset($_POST['submitted'])) {
    require_once('connectdatab.php');

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password1 = isset($_POST['password1']) ? $_POST['password1'] : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    $stat = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stat->execute([$username]);
    $count = $stat->fetchColumn();

    if (!$username || !$password1 || !$password2 || !$email) {
        $error_message = "Username, password, or email is missing!";
    }
    elseif ($password1 !== $password2) {
        $error_message = "Passwords do not match!";
    } elseif (strlen(trim($username)) == 0) {
        $error_message = "Username must be madeup of letters and/or numbers";
    } elseif ($count > 0) {
        $error_message = "Username already taken";
    } else {
        // hashing the pass after making sure pass1 and 2 match 
        $password = password_hash($password1, PASSWORD_DEFAULT);
        try {
            $stat = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stat->execute(array($username, $password, $email));

            $id = $db->lastInsertId();
            $success_message = "Congratulations! You are now registered.<br/>";
        } catch (PDOException $ex) {
            echo "Sorry, a database error occurred! <br>";
            echo "Error details: <em>" . $ex->getMessage() . "</em>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="http://localhost/Port3/index.html">AProject</a></h1>
            <h2>Register</h2>
            <a href="http://localhost/Port3/login.php" class="login">Login</a>
        </div>
    </header>
    <form action="register.php" method="post" class="loginform">
        
        <label for="username">User Name</label>
        <input type="text" name="username" id="username" maxlength="25" required value="<?php echo htmlspecialchars($username); ?>">
        <label for="password1">Password</label>
        <input type="password" name="password1" id="password1" maxlength="25" required>
        <label for="password2">Re-Enter Password:</label>
        <input type="password" name="password2" id="password2" maxlength="25" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" maxlength="25" required value="<?php echo htmlspecialchars($email); ?>">
        <input type="submit" value="Register">
        <input type="hidden" name="submitted" value="TRUE">
        <?php if ($error_message): ?>
            <div style="color: red;"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div style="color: green;"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <p>Already a user? <a href="login.php">Login</a></p>
    </form>
    <footer>
        <p>&copy; 2024 AProject. Aston University. All rights reserved.</p>
    </footer>
</body>
</html>

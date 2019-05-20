<?php 
	session_start();
	require_once 'db-connect.php';
	//Connect to database
	$db = mysqli_connect($server, $user, $pass, $dbname);
	$email    = "";
	$username = "";
	$error = false; 

	// ******************* User logins *************************
	if (isset($_POST['login'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);
	
		if (empty($username)){
			$error = true; 
			$error_type = "Please enter your Username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password)) {
			$error = true; 
			$error_type = "Please enter your Password.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (!$error) 
		{
			$password = md5($password);
			$query = "SELECT * FROM credentials WHERE username='$username' AND password='$password'";
			$results = mysqli_query($db, $query);
			
			while($row = $results->fetch_assoc())	
			$id =  $row["id"];
			
			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $username;
				$_SESSION['id'] = $id;
				header('location: translate.php');
			}
			
			else {
				$error_type = "Incorrect username/password.";
				echo "<script type='text/javascript'>alert('$error_type');</script>";
			}
		}
	}
	
	// ******************* User registers *************************
	if (isset($_POST['register'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password1 = mysqli_real_escape_string($db, $_POST['password1']);
		$password2 = mysqli_real_escape_string($db, $_POST['password2']);
		$email = mysqli_real_escape_string($db, $_POST['email']);

		$query = "SELECT * FROM credentials WHERE username = '$username'";	
		$result = mysqli_query($db, $query);
		if (mysqli_num_rows($result) > 0){ 
			$error = true; 
			$error_type = "User already exists. Please go back to Sign in page or use a different username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}

		if (empty($username)){
			$error = true; 
			$error_type = "Please enter your Username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($email)){
			$error = true; 
			$error_type = "Please enter your Email.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password1)){
			$error = true; 
			$error_type = "Please enter your Password.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password2)){
			$error = true; 
			$error_type = "Please enter your Password again.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if ($password1 != $password2){
			$error = true; 
			$error_type = "Please enter your Password again! The passwords don't match.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (!$error) 
		{
			$password = md5($password1);
			$query = "INSERT INTO credentials (email, username, password) VALUES('$email', '$username', '$password')";	
			mysqli_query($db, $query);

			$_SESSION['username'] = $username;
			header('location: loginpage.php');
		}
	}
mysqli_close($db);
?>
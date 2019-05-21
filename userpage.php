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
		$username = mysql_entities_fix_string($db, $_POST['username']);
		$password = mysql_entities_fix_string($db, $_POST['password']);
	
		if (empty($username)){ //no input username
			$error = true; 
			$error_type = "Please enter your Username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password)){ //no input password
			$error = true; 
			$error_type = "Please enter your Password.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		
		//When both username and password are entered
		if (!$error) 
		{
			$password = md5($password);
			$query = "SELECT * FROM credentials WHERE username='$username' AND password='$password'";
			$results = mysqli_query($db, $query);
			
			while($row = $results->fetch_assoc())	
			$id =  $row["id"];
			
			//same user and password are found in database
			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $username;
				$_SESSION['id'] = $id;
				header('location: translate.php');
			}
			else { //no record found
				$error_type = "Incorrect username/password.";
				echo "<script type='text/javascript'>alert('$error_type');</script>";
			}
		}
	}
	
	// ******************* User registers *************************
	if (isset($_POST['register'])) {
		$username = mysql_entities_fix_string($db, $_POST['username']);
		$password1 = mysql_entities_fix_string($db, $_POST['password1']);
		$password2 = mysql_entities_fix_string($db, $_POST['password2']);
		$email = mysql_entities_fix_string($db, $_POST['email']);

		$query = "SELECT * FROM credentials WHERE username = '$username'";	
		$result = mysqli_query($db, $query);

		if (mysqli_num_rows($result) > 0){ //user already exists
			$error = true; 
			$error_type = "User already exists. Please go back to Sign in page or use a different username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}

		if (empty($username)){ //no input username
			$error = true; 
			$error_type = "Please enter your Username.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($email)){ //no input email
			$error = true; 
			$error_type = "Please enter your Email.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password1)){ //no input password1
			$error = true; 
			$error_type = "Please enter your Password.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if (empty($password2)){ //no input password2
			$error = true; 
			$error_type = "Please enter your Password again.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}
		if ($password1 != $password2){ //both entered pw don't match
			$error = true; 
			$error_type = "Please enter your Password again! The passwords don't match.";
			echo "<script type='text/javascript'>alert('$error_type');</script>";
		}

		//every input box is filled and met requirements
		if (!$error) 
		{
			$password = md5($password1);
			$query = "INSERT INTO credentials (email, username, password) VALUES('$email', '$username', '$password')";	
			mysqli_query($db, $query);

			header('location: loginpage.php'); //navigate to login page
		}
	}

	//Helps avoiding XSS attacks
	function mysql_entities_fix_string($db, $string)
	{
		return htmlentities(mysql_fix_string($db, $string));
	}

	function mysql_fix_string($db, $string)
	{
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
			return $db->real_escape_string($string); 
	}
mysqli_close($db);
?>
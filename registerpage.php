<?php include('userpage.php') ?>
<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
    body {
		background: #dac9b6;
		border: <?=$border?> #000;
	}
	h1 {
		color: white;
  		text-align: center;
	}
    </style>
</head>
<body>
	<div class="header"><h1>REGISTER USER</h1></div>
	
	<form method="post" action="registerpage.php">
		<div class="input-group">
			<label>Email</label>
			<input type="email" name="email" value="<?php echo $email; ?>"></div>
		<div class="input-group">
			<label>Username</label>
			<input type="text" name="username" value="<?php echo $username; ?>"></div>
		<div class="input-group">
			<label>Password</label>
			<input type="password" name="password1"></div>
		<div class="input-group">
			<label>Verify password</label>
			<input type="password" name="password2"></div>
		<div class="input-group">
			<button type="submit" class="btn" name="register">Register</button></div>
		<p>	Already register? <a href="loginpage.php">Sign in</a></p>
	</form>
</body>
</html>

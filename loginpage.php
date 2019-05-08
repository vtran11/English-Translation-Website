<?php include('userpage.php') ?>
<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
	h1 {
		background: ##0077b3;
		text-align: center;
		font-size: 40px;
	}
    body {
		background-color: lightblue;
		border: <?=$border?> #000;
	}
	h2 {
		color: white;
	}
    </style>
</head>
<body>
	<h1> LAME TRANSLATE</h1>
	<h2> LOGIN USER</h2>
	<form method="post" action="loginpage.php">
		<div class="input-group">
			<label>Username</label>
			<input type="text" name="username" ></div>
		<div class="input-group">
			<label>Password</label>
			<input type="password" name="password"></div>
		<div class="input-group">
			<button type="submit" class="btn" name="login">Log In</button></div>
		<p>	Sign up for new account. <a href="registerpage.php">Register</a></p>
	</form>
</body>
</html>

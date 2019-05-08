<?php include('translate.php') ?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style  type="text/css">
  body {
    font-family: Arial, Helvetica, sans-serif;
    background-color: #ece8fe;
  }

  h1 {
  		background: ##0077b3;
  		text-align: center;
  		font-size: 40px;
  }

  h2 {
  		color: white;
      text-align: center;
  }

  .navbar {
    overflow: hidden;
    background-color: #af9ffb;
  }

  .dropdown {
   float: right;
   overflow: hidden;
  }

  .dropdown .dropbtn {
    font-size: 16px;  
    border: none;
    outline: none;
    color: white;
    padding: 14px 16px;
    background-color: inherit;
    font-family: inherit;
    margin: 0;
  }

  .navbar a:hover, .dropdown:hover .dropbtn {
    background-color: red;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
  }

  .dropdown-content a {
    float: none;
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
  }

  .dropdown-content a:hover {
    background-color: #ddd;
  }

  .dropdown:hover .dropdown-content {
    display: block;
  }
  </style>
</head>

<body>
  <div class="navbar">
    <div class="dropdown">
    <?php  if (isset($_SESSION['username'])) : ?>
        <a class="dropbtn"><?php echo $_SESSION['username']; ?> </a>
        <button class="dropbtn">User 
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content">
        <a href="#">User's Content</a>
        <a href="translate.php?logout='1'">Log Out</a>
      </div>
    <?php endif ?>
    <?php  if (!isset($_SESSION['username'])) : ?>
      <button class="dropbtn">Users 
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content">
        <a href="translate.php?register='1'">Register</a>
        <a href="translate.php?login='1'">Log In</a>
      </div>
    <?php endif ?>
    </div> 
  </div>

  <h1> LAME TRANSLATOR</h1>
  <h2> Welcome to our translator!</h2>
  <form method='post' action='translatepage.php' enctype='multipart/form-data'>
    Select File: <input type='file' name='filename' size='10'> 
    <input type='submit' value='Upload'>
  </form>
  
  <form action="translatepage.php" method="post"><pre>
    Content Name: <input type="text" name="file">
    File Content: <input type="text" name="content">
    <input type="submit" value="ADD RECORD">
  </pre></form>
</body>
</html>

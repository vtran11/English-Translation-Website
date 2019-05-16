<?php
  include_once "db-connect.php";

  session_start(); 

  // Connect to Database
  $conn = new mysqli($server,$user,$pass,$dbname);
  if ($conn->connect_error) die($conn->connect_error);

	if (isset($_SESSION['username'])) 
	{
    $username = $_SESSION['username'];

    //Check if files are uploaded and save data to database
    if($_FILES)
    {
       /* $name1 = $_FILES['filename1']['name']; 
        $name2 = $_FILES['filename2']['name']; 
        $name1 = strtolower(preg_replace("/[^A-Za-z0-9.]/", "", $file1));
        $name2 = strtolower(preg_replace("/[^A-Za-z0-9.]/", "", $file2));
        switch($_FILES['file1']['type'] or $_FILES['file1']['type'])
        {
          case 'text/plain': $ext='txt';break;
          default          : $ext=''; break;
        }
        if($ext) 
        {*/
          $name1 = "$name1.$ext";
          $name2 = "$name2.$ext";
          $file1 = $_FILES['filename1']['name'];
          $file2 = $_FILES['filename2']['name'];
          $content1 = file_get_contents($file1);
          $content2 = file_get_contents($file2);
          $open1 = fopen($file1, 'r') or
                        die("File does not exist");
          $open2 = fopen($file2, 'r') or
                        die("File does not exist");
          $data1 = fread($open1,20);
          $data2 = fread($open2,20);

          $query = "INSERT INTO translateModel VALUES"."('$username', $data1', '$data2')"; 
          $result = $conn->query($query);
    
          if (!$result) 
            echo "INSERT failed: $query<br>".$conn->error."<br><br>";
        //}
    }
    else "No file was uploaded!";
	}

	if (isset($_GET['logout']))
	{
		session_destroy();
		unset($_SESSION['username']);
  }
  
  if (isset($_GET['login']))
	{
		header("location: loginpage.php");
	}

  if (isset($_GET['register']))
	{
		header("location: registerpage.php");
  }

  //DELETE RECORD
  if (isset($_POST['delete']) && isset($_POST['id'])) 
    {
        $id = get_post($conn, 'id');
        $query = "DELETE FROM input WHERE id='$id'"; 
        $result = $conn->query($query);
        if (!$result) 
                echo "DELETE failed: $query<br>".$conn->error."<br><br>";
    
    }
  
  //If User is logged in
  if (isset($_SESSION['username'])) 
	{
    $username = $_SESSION['username'];
  }
  //If No Logged In User
  else if (!isset($_SESSION['username'])) 
	{

  }
  
      

    //If Input in english is entered
    if( isset($_POST['textinput']))
    {
        $file = get_post($conn, 'file'); 
        $content = get_post($conn, 'content'); 
        // this should be select query to check the files, and print out the translation
        $query = "INSERT INTO input(file,content) VALUES"."('$file', '$content')"; 
        $result = $conn->query($query);
        if (!$result) 
          echo "INSERT failed: $query<br>".$conn->error."<br><br>";
    }


  //Display database info: content names and file contents on the web server   
  $query = "SELECT * FROM input";
  $result = $conn->query($query);
  if (!$result) die ("Database access failed: ".$conn->error);
  $rows = $result->num_rows;
     
  for ($j = 0 ; $j < $rows ; ++$j)
  {
       $result->data_seek($j);
       $row = $result->fetch_array(MYSQLI_NUM);
       echo <<<_END
       <pre>
       Input Words: $row[1]
       Translation in English: $row[2]
       </pre>
       <form action="translate.php" method="post">
       <input type="hidden" name="delete" value="yes">
       <input type="hidden" name="id" value="$row[0]">
       <input type="submit" value="DELETE RECORD"></form>
_END;
  }

  $result->close();
  $conn->close();
  
  function get_post($conn, $var) {
       return $conn->real_escape_string($_POST[$var]); 
  }
?>


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
  <br><br>
  <?php  if (isset($_SESSION['username'])) : ?>
    <form method='post' action='translate.php'><pre>
      Select Translation Model in English:              <input type='file' name='filename1'> 
      Select Translation Model in your choosen Language: <input type='file' name='filename2'> 
      <input type='submit' value='Upload'></pre>
      <br><br>  
    </form>
  
    <form action="translate.php" method="post"><pre>
      Input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE"></pre>
    </form>
  <?php endif ?>

  <?php  if (!isset($_SESSION['username'])) : ?>
    <form action="translate.php" method="post"><pre>
      Input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE"></pre>
    </form>
  <?php endif ?>

</body>
</html>

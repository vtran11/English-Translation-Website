<?php
  include_once "db-connect.php";
  include_once "default_model.php";

  session_start(); 

  // Connect to Database
  $conn = new mysqli($server,$user,$pass,$dbname);
  if ($conn->connect_error) die($conn->connect_error);

  //*********************** If No Logged In User *********************************
  if (!isset($_SESSION['username'])) 
	{
    //User selects login then natigate to login page
    if (isset($_GET['login'])){
		  header("location: loginpage.php");
	  }

    //User selects register then natigate to register page
    else if (isset($_GET['register'])){
		  header("location: registerpage.php");
    }

    //If Input in english is entered
    if( isset($_POST['textinput']))
    {
      $input = strtolower(get_post($conn, 'textinput'));
      if(array_key_exists("$input", $eng_to_viet)){
        $translation = $eng_to_viet["$input"];  
        echo "English: ".$input.", Vietnamese: ".$translation;
      }
      else {
        $error_type = "Please enter valid English words!";
        echo "<script type='text/javascript'>alert('$error_type');</script>";
      }
    }
  }

  //*********************** If User is logged in *********************************
	if (isset($_SESSION['username'])) 
	{
    $username = $_SESSION['username'];
    $exist_model = false;

    $query = "SELECT * FROM translateModel";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed: ".$conn->error);
    $rows = $result->num_rows;

    for ($j = 0 ; $j < $rows ; ++$j){
      $result->data_seek($j);
      $row = $result->fetch_array(MYSQLI_NUM);

	    //if user has saved model
      if ('$row[0]' == '$username'){
        $exist_model = true;
	    }   
    }

    //When user upload new files or enter input when the translation model DOES exist
    if($exist_model == true){
      echo "User $username already uploaded a Translation Model! Don't upload a new one!<br>";

      //Flag when user trying to upload a new model
      if($_FILES){
        $error_type = "You already uploaded a Translation Model! Don't upload a new one!<br>";
        echo "<script type='text/javascript'>alert('$error_type');</script>";
      }
      else{
          //If Input in english is entered
          if( isset($_POST['textinput']))
          {
            //user has saved translate model case @@@@@@@@@@@@@@@@@@@

          }
      }
    }  
    //When user upload new files or enter input when the translation model DOESNOT exist
    else 
    {
      echo "User $username don't have any Translation Model in the system!<br>";

      //Upload the files and save data to database
      if($_FILES)
      {
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
      }
      else //No file uploaded
      {
        //If Input in english is entered, use default model
        if( isset($_POST['textinput']))
        {
          $input = strtolower(get_post($conn, 'textinput'));
          if(array_key_exists("$input", $eng_to_viet)){
            $translation = $eng_to_viet["$input"];  
            echo "English: ".$input.", Vietnamese: ".$translation;
          }
          else {
            $error_type = "Please enter valid English words!";
            echo "<script type='text/javascript'>alert('$error_type');</script>";
          }
        }
      }
    }
    
    //user selects logout then sesion destroys
	  if (isset($_GET['logout'])){
		  session_destroy();
		  unset($_SESSION['username']);
    }
	}
  

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
      Enter your input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE"></pre>
    </form>
  <?php endif ?>

  <?php  if (!isset($_SESSION['username'])) : ?>
    <form action="translate.php" method="post"><pre>
      Enter your input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE"></pre>
      <output type="text" ID="add" name="textoutput" value="fv"></pre>
    </form>
  <?php endif ?>

</body>
</html>
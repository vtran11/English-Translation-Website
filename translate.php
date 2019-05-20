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

    $query = "SELECT username FROM translateModel";
    $result = $conn->query($query);
    $columnValues = array();
    if (!$result) die ("Database access failed: ".$conn->error);
   
    $rows = $result->num_rows;
     
    for ($j = 0 ; $j < $rows ; ++$j){
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);
        if ($row[0] == $username){
          $exist_model = true;
        }
     } 

    //When user upload new files or enter input when the translation model DOES exist
    if($exist_model == true){
      //Flag when user trying to upload a new model
      if(isset($_POST["uploadfiles"])){
        $error_type = "You already uploaded a Translation Model! Don't upload a new one!";
        echo "<script type='text/javascript'>alert('$error_type');</script>";
      }
      else{
          //If Input in english is entered
          if( isset($_POST['textinput']))
          {
            $input = strtolower(get_post($conn, 'textinput'));

            $query = "SELECT translation from translateModel WHERE username = '$username' 
                      AND english_words = '$input'";
            $result = $conn->query($query);
            if (!$result) die ("Database access failed: ".$conn->error);
            $row = $result->fetch_array(MYSQLI_NUM);

            echo "English: ".$input.", Vietnamese: ".$row[0];
          }
      }
    }  
    //When user upload new files or enter input when the translation model DOESNOT exist
    else 
    {
      //WHen user submit the file
      if(isset($_POST["uploadfiles"]))
      {
        //allowing only alphanumeric characters and the period
        $file1 = preg_replace("/[^A-Za-z0-9.]/", "", $_FILES['filename1']['name']);
        $file2 = preg_replace("/[^A-Za-z0-9.]/", "", $_FILES['filename2']['name']);
        $allowed =  array('txt','pdf'); //allowed file type
        $error = false;

        //CHECK FILE TYPE - only text or pdf file is allowed
        $ext1 = pathinfo($file1, PATHINFO_EXTENSION);
        $ext2 = pathinfo($file2, PATHINFO_EXTENSION);

        if(!in_array($ext1,$allowed) or !in_array($ext2,$allowed)){
          $error_type = "Sorry! Only Text files are allowed!";
          echo "<script type='text/javascript'>alert('$error_type');</script>";
          $error = true;
        }
        if($_FILES['filename1']['size'] == 0 or $_FILES['filename1']['size'] == 0){
          $error_type = "Sorry! One or more files are empty! Please upload the new ones.";
          echo "<script type='text/javascript'>alert('$error_type');</script>";
          $error = true;
        }
        if($_FILES['filename1']['size'] > 50000 or $_FILES['filename1']['size'] > 50000){
          $error_type = "Sorry! Your files is/are too large. Please upload smaller files!";
          echo "<script type='text/javascript'>alert('$error_type');</script>";
          $error = true;
        } 
        //files pass all requirements
        if($error == false){
          $path = "./";      
          $path1 = $path.basename($file1);
          $path2 = $path.basename($file2);

          $content1 = file_get_contents($path1);
          $content2 = file_get_contents($path2);

          $line1 = explode("\n", $content1);
          $line2 = explode("\n", $content2);

          //save files' contents (translation model) to database
          for($i=0, $count = count($line1);$i<$count;$i++) {
            $word1  = $line1[$i];
            $word2 = $line2[$i];

            $query = "INSERT INTO translateModel VALUES"."('$username', '$word1', '$word2')"; 
            $result = $conn->query($query);

            if (!$result) 
              echo "INSERT failed: $query<br>".$conn->error."<br><br>";
          }
        }
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
    <form action="translate.php" method="post" enctype="multipart/form-data"><pre>
      Select Translation Model in English File(txt):               <input type="file" name="filename1"> 
      Select Translation Model in your choosen Language File(txt): <input type="file" name="filename2"> 
      <input type="submit" value="Upload" name="uploadfiles"></pre>
      <br><br>  
    </form>
  
    <form action="translate.php" method="post"><pre>
      Enter your input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE" name="translate1"></pre>
    </form>
  <?php endif ?>

  <?php  if (!isset($_SESSION['username'])) : ?>
    <form action="translate.php" method="post"><pre>
      Enter your input in English: <input type="text" name="textinput">
      <input type="submit" value="TRANSLATE" name="translate2"></pre>
      <output type="text" ID="add" name="textoutput" value="fv"></pre>
    </form>
  <?php endif ?>

</body>
</html>
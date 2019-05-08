<?php
  include_once "db-connect.php";

  session_start(); 

	/*if (isset($_SESSION['username'])) 
	{
	}*/

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
  
  // Connect to Database
  $conn = new mysqli($server,$user,$pass,$dbname);
  if ($conn->connect_error) die($conn->connect_error);
      
    //Check uploaded file and save the info into input table
    if($_FILES)
    {
        $path = "./";
        $path = $path.basename( $_FILES['filename']['name']);
        $file = $_FILES['filename']['name']; 
        move_uploaded_file($_FILES['filename']['tmp_name'], $path);
        $content = file_get_contents($path);

        $query = "INSERT INTO input(file,content) VALUES"."('$file', '$content')"; 
        $result = $conn->query($query);
    
        if (!$result) 
          echo "INSERT failed: $query<br>".$conn->error."<br><br>";
    }
    
    //Text boxes are entered
    if( isset($_POST['file']) && isset($_POST['content']))
    {
        $file = get_post($conn, 'file'); 
        $content = get_post($conn, 'content'); 
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
  }

  $result->close();
  $conn->close();
  
  function get_post($conn, $var) {
       return $conn->real_escape_string($_POST[$var]); 
  }
?>


<?php

session_start();
require_once "pdo.php";

// Obtain session name from session array
if (!isset($_SESSION['name']) )
{
      die('Not logged in: Please redirect your browser to the <a href ="index.php">front page.</a>');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) )
{
    header('Location: view.php');
    return;
}


// if the year, make and mileage have been posted via Form
if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']))
{
    if(strlen($_POST['make']) > 0 && is_numeric($_POST['year']) && is_numeric($_POST['mileage']))
    {
    // specify SQL code to insert vehicle details
    $sql = "INSERT INTO autos (make, year, mileage) VALUES ( :make, :year, :mileage)";
    // prepare the SQL
    $stmt = $pdo->prepare($sql);
    //  Execute the SQL statement to insert
    $stmt->execute(array(
        ':make' => $_POST['make'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));

    $_SESSION['success'] = "Record inserted";
    header("Location: view.php");
    return;

    }
    else if(!is_numeric($_POST['year']) || !is_numeric($_POST['mileage']))
    {
        $_SESSION["record_error"] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    }
    else if(strlen($_POST['make']) < 1)
    {
        $_SESSION["record_error"] = "Make is required";
        header("Location: add.php");
        return;
    }
}
?>

<html>
<head>
  <title>Kgotso Koete's Vehicle Database</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
  <?php
      // print a red error message if not successful
      if ( isset($_SESSION["name"]) )
      {
          echo('<h1>Tracking Autos for '.htmlentities($_SESSION["name"])."</h1>\n");
      }

     // print a red error message if not successful
     if ( isset($_SESSION["record_error"]) )
     {
         echo('<p style="color:red">'.htmlentities($_SESSION["record_error"])."</p>\n");
         unset($_SESSION["record_error"]);
     }
 ?>

<form method="post">
<p>Make:
<input type="text" name="make"></p>
<p>Year:
<input type="text" name="year"></p>
<p>Mileage:
<input type="text" name="mileage"></p>
<p><input type="submit" value="Add New"/></p>
</form>

<form method="post">
  <p><input type="submit" name = "cancel" value="cancel"/></p>
</form>
</div>
</body>

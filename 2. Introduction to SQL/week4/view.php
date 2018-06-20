<?php

session_start();
require_once "pdo.php";

// Obtain session name from session array
if (!isset($_SESSION['name']) )
{
      die('Not logged in: Please redirect your browser to the <a href ="index.php">front page.</a>');
}

if ( isset($_POST['delete']) && isset($_POST['auto_id']) )
{
    $sql = "DELETE FROM autos WHERE auto_id = :zip";
    $_SESSION["record_update"] = "Record deleted :(";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['auto_id']));

    header("Location: view.php");
    return;
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
  ?>
  <h3> Automobiles</h3>

  <p><a href = "add.php">Add New </a>|<a href = "logout.php"> Logout</a></p>

  <?php
      // print a red error message if not successful
      if ( isset($_SESSION["record_update"]) )
      {
          echo('<p style="color:red">'.htmlentities($_SESSION["record_update"])."</p>\n");
          unset($_SESSION["record_update"]);
      }
      if ( isset($_SESSION['success']) )
      {
          echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
          unset($_SESSION['success']);
      }
  ?>


  <table border="1">
    <?php
    $stmt = $pdo->query("SELECT make, year, mileage, auto_id FROM autos");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
    {
        echo "<tr style='width: 100%;'><td style='width: 40%; text-align: center;border: 2px solid grey;'>";
        echo(htmlentities($row['make']));
        echo("</td><td style='width: 20%; text-align: center;border: 2px solid grey;'>");
        echo(htmlentities($row['year']));
        echo("</td><td style='width: 20%; text-align: center;border: 2px solid grey;'>");
        echo(htmlentities($row['mileage']));
        echo("</td><td style='width: 10%; text-align: center;border: 2px solid grey;'>");
        echo('<form method="post"><input type="hidden" ');
        echo('name="auto_id" value="'.$row['auto_id'].'">'."\n");
        echo('<input type="submit" value="Del" name="delete">');
        echo("\n</form>\n");
        echo("</td></tr>\n");
    }
    ?>
</table>
</div>
</body>

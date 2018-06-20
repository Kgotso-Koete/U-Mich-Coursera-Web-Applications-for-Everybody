<?php
require_once "pdo.php";

// GET name from url (upon successful log in) or print error
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing: Please redirect your browser to the <a href ="index.php">front page.</a>');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) )
{
    header('Location: index.php');
    return;
}

// variable of when to print messages to inform users of successful insert/deletion
$InsertPostMessage = false;
$DelPostMessage = false;

// if the year, make and mileage have been posted via Form
if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']))
{
    if(strlen($_POST['make']) > 0 && is_numeric($_POST['year']) && is_numeric($_POST['mileage']))
    {
    // specify SQL code to insert vehicle details
    $sql = "INSERT INTO autos (make, year, mileage) VALUES ( :make, :year, :mileage)";
    $InsertPostMessage = true;
    // prepare the SQL
    $stmt = $pdo->prepare($sql);
    //  Execute the SQL statement to insert
    $stmt->execute(array(
        ':make' => $_POST['make'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));
    }
    else if(!is_numeric($_POST['year']) || !is_numeric($_POST['mileage']))
    {
        echo("<pre style='color:red;'>\n"."Mileage and year must be numeric"."\n</pre>\n");
    }
    else if(strlen($_POST['make']) < 1)
    {
        echo("<pre style='color:red;'>\n"."Make is required"."\n</pre>\n");
    }
}

if ( isset($_POST['delete']) && isset($_POST['auto_id']) )
{
    $sql = "DELETE FROM autos WHERE auto_id = :zip";
    $DelPostMessage = true;
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['auto_id']));
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
  // print the welcome phrase with user name
  if ( isset($_REQUEST['name']) )
  {
      echo "<h1>Tracking Autos for ";
      echo htmlentities($_REQUEST['name']);
      echo "</h1>\n";
  }
  if ($InsertPostMessage)
  {
      echo("<pre  style='color:green;'>\n"."New record created :)"."\n</pre>\n");
  }

  if ($DelPostMessage)
  {
      echo("<pre  style='color:blue;'>\n"."Record deleted :("."\n</pre>\n");
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
  <p><input type="submit" name = "logout" value="logout"/></p>
</form>
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

<?php
require_once "pdo.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['name']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="login1.php">front page.</a>');
}

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']))
{
    // missing data in any field
    if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1)
    {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }
    // if the year or mileage are not numeric
    if ( !is_numeric($_POST['year']) || !is_numeric($_POST['mileage']) )
    {
        $_SESSION['error'] = ' Year and mileage must be an integer';
        header("Location: add.php");
        return;
    }

    $sql = "INSERT INTO autos (make, model, year, mileage)
              VALUES (:make, :model, :year, :mileage)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':make' => $_POST['make'],
        ':model' => $_POST['model'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));

    $_SESSION['success'] = 'Record Added';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Add Page</title>
</head>
<body>
<?php
    // print a red error message if not successful
    if ( isset($_SESSION["name"]) )
    {
        echo('<h1>Tracking Autos for '.htmlentities($_SESSION["name"])."</h1>\n");
    }
?>
<p>Add A New Vehicle</p>
<form method="post">
<p>Make:
<input type="text" name="make"></p>
<p>Model:
<input type="text" name="model"></p>
<p>Year:
<input type="text" name="year"></p>
<p>Mileage:
<input type="text" name="mileage"></p>
<p><input type="submit" value="Add"/></p>
</form>
<form  action="index.php">
<input type="submit" value="Cancel"/> </p>
</form>


</body>
</html>

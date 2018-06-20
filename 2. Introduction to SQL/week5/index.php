<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Index Page</title>
</head>
<body>

<h1>Welcome to the Automobiles Database</h1>
<table>
<?php
// print error messages for any errors or record updates
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}

echo('<table border="1">'."\n");
$stmt = $pdo->query("SELECT make, model, year, mileage, auto_id FROM autos");


// pull SQL data to put into HTML table
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(htmlentities($row['make']));
    echo("</td><td>");
    echo(htmlentities($row['model']));
    echo("</td><td>");
    echo(htmlentities($row['year']));
    echo("</td><td>");
    echo(htmlentities($row['mileage']));
    echo("</td><td>");
    echo('<a href="edit.php?auto_id='.$row['auto_id'].'">Edit</a> / ');
    echo('<a href="delete.php?auto_id='.$row['auto_id'].'">Delete</a>');
    echo("</td></tr>\n");
}
?>
</table>
<p><a href="add.php">Add New Entry</a></p>
<p><a href="logout.php">Log Out</a></p>
</body>
</html>

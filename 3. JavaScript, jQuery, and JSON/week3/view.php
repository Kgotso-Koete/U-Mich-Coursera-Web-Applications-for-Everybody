<?php
require_once "pdo.php";
require_once "util.php";
session_start();


// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) )
{
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Edit Page</title>
</head>
<body>

<?php echo('<h1> Profile information for '.$fname."</h1>\n");?>

<p style = "border: 1px">First Name : <?= $fname ?></p>
<p>Last Name : <?= $lname ?></p>
<p>Email : <?= $email ?></p>
<p>Headline : <?= $headline ?></p>
<p>Summary : <?= $summary ?></p>

<?php
$sql ="SELECT * FROM position where profile_id = :prof ORDER BY rank";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':prof' => $_GET['profile_id']));

// print + sign before looping through available positions
echo('<p>Position:</p>');
echo('<ul>');
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
{
   $rank = htmlentities($row['rank']);
   $year = htmlentities($row['year']);
   $desc = htmlentities($row['description']);
   echo('<li>'.$year.': '.$desc.'</li>');
}
echo('</ul>');
echo ('<p><a href = "index.php"> Done </a></p>');
?>


</body>
</html>

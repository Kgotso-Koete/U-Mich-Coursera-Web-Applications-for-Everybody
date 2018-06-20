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

?>

<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Edit Page</title>
</head>
<body>

<?php flashMessages();?>

<?php
$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
echo('<h1> Profile information for '.$fname."</h1>\n");
?>

<p>First Name : <?= $fname ?></p>
<p>Last Name : <?= $lname ?></p>
<p>Email : <?= $email ?></p>
<p>Headline : <?= $headline ?></p>
<p>Summary : <?= $summary ?></p>

<?php
// looad and display educations
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
if(count($schools) > 0)
{
    echo('<p>Education:</p>');
    echo('<ul>');
    // loop though associative array
    foreach ($schools as $school){echo('<li>'.$school['year'].': '.$school['name'].'</li>');}
    echo('</ul>');
}

// load and display positions
$positions = loadPos($pdo, $_REQUEST['profile_id']);
if(count($positions) > 0)
{
    echo('<p>Position:</p>');
    echo('<ul>');
    // loop though associative array
    foreach ($positions as $position){echo('<li>'.$position['year'].': '.$position['description'].'</li>');}
    echo('</ul>');
}

echo ('<p><a href = "index.php"> Done </a></p>');
?>
 
</body>
</html>

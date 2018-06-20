<?php
require_once "pdo.php";
require_once "util.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['user_id']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="logout.php">front page.</a>');
      return;
}

if (isset($_POST['cancel']) )
{
      header('Location: index.php');
      return;
}

// make sure the request parameter is present
if ( ! isset($_REQUEST['profile_id']) )
{
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

// only allow records to be edited if the user created them
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :profile_id AND user_id = :user_id");
$stmt->execute(array(
    ':profile_id' => $_GET['profile_id'],
    ':user_id' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false )
{
    $_SESSION['error'] = 'Only entries created by your user_id can be edited';
    header( 'Location: index.php' ) ;
    return;
}
else
{
    // check if all fields have been filed in
    if ( isset($_POST['first_name'])
         && isset($_POST['last_name'])
         && isset($_POST['email'])
         && isset($_POST['headline'])
         && isset($_POST['summary']))
    {
        // validate the profile and print error message
        $msg = validateProfile();
        if(is_string($msg))
        {
          $_SESSION['error'] = $msg;
          header("Location: edit.php?profile_id=". $_REQUEST["profile_id"]);
          return;
        }

        // validate the profile and print error message
        $msg = validatePos();
        if(is_string($msg))
        {
          $_SESSION['error'] = $msg;
          header("Location: edit.php?profile_id=". $_REQUEST["profile_id"]);
          return;
        }

        $sql = "UPDATE profile SET first_name = :first_name,
                last_name = :last_name, email = :email, headline = :headline, summary = :summary
                WHERE profile_id = :profile_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':profile_id' => $_REQUEST['profile_id'],
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary']));

        //$profile_id = $_GET['profile_id'])

        $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        // insert the position entries
        $rank = 1;
        for($i = 1; $i <= 9; $i++)
        {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
            VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
            );
            $rank++;
        }

        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
    }
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
<?php require_once "head.php";?>
<script src="util.js"></script>
</head>
<body>
  <div class = "container">
  <h1> Editing Profile for <?= htmlentities($positions["name"]); ?></h1>
  <?php flashMessages();?>

  <p>Please fill in the form below: </p>

  <form method="post">
    <p>First Name:<input type="text" name="first_name" size= "60" id='fn' value="<?= $fname ?>"></p>
    <p>Last Name:<input type="text" name="last_name" size= "60" id='ln' value="<?= $lname ?>"></p>
    <p>Email:<input type="text" name="email" size= "60" id='ema'  value="<?= $email ?>"></p>
    <p>Headline:<input type="text" name="headline" size= "60" id='hd' value="<?= $headline ?>"></p>
    <p>Summary:<br><textarea name="summary" rows="8" cols="69" id='sum' ><?= $summary ?></textarea></p>

    <?php
    $sql ="SELECT * FROM position where profile_id = :prof ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $_GET['profile_id']));

    // print + sign before looping through available positions
    echo('<p>Position: <input type="submit" id="addPos" value ="+">');
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
    {
       $rank = htmlentities($row['rank']);
       $year = htmlentities($row['year']);
       $desc = htmlentities($row['description']);
       echo('<div id="position'.$rank.'">');
       echo('<p>Year: <input type="text" name="year'.$rank.'" value="'.$year.'">');
       echo('<input type="button" value="-" onclick="$("#position'.$rank.').remove();return false;""></p>');
       echo('<textarea name="desc'.$rank.'" rows="8" cols="80">'.$desc.'</textarea></div>');
    }
    // print the extra boxes that emerge when + is clicked
    echo('<div id="position_fields"></div></p>');
    echo ('<p><input type="submit" value="Save"> <input type="submit" name="cancel" value="Cancel"></p></form></div>');
    ?>

<script>createPosBox();</script>
</body>
</html>

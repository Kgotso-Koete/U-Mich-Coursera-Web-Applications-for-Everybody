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

        // validate position in entries if present
        $msg = validateEdu();
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

        // delete entries from position before re-inserting
        $stmt = $pdo->prepare('DELETE FROM position
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        // insert the position entries
        insertPos($pdo, $_REQUEST['profile_id']);

        // delete entries from education before re-inserting
        $stmt = $pdo->prepare('DELETE FROM education
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        // insert the education entries
        insertEdu($pdo, $_REQUEST['profile_id']);

        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Edit Page</title>
<?php require_once "head.php";?>
<script src="util.js"></script>
</head>
<body>
  <?php
  $fname = htmlentities($row['first_name']);
  $lname = htmlentities($row['last_name']);
  $email = htmlentities($row['email']);
  $headline = htmlentities($row['headline']);
  $summary = htmlentities($row['summary']);
  ?>
  <div class = "container">
  <h1> Editing Profile for <?= htmlentities($_SESSION["name"]); ?></h1>
  <?php flashMessages();?>

  <p>Please fill in the form below: </p>

  <form method="post">
    <p>First Name:<input type="text" name="first_name" size= "60" id='fn' value="<?= $fname ?>"></p>
    <p>Last Name:<input type="text" name="last_name" size= "60" id='ln' value="<?= $lname ?>"></p>
    <p>Email:<input type="text" name="email" size= "60" id='ema'  value="<?= $email ?>"></p>
    <p>Headline:<input type="text" name="headline" size= "60" id='hd' value="<?= $headline ?>"></p>
    <p>Summary:<br><textarea name="summary" rows="8" cols="69" id='sum' ><?= $summary ?></textarea></p>

    <?php
    // load and display education
    $schools = loadEdu($pdo, $_REQUEST['profile_id']);
    $countEdu = 0;

    echo('<p>Education: <input type="submit" id="addEdu" value ="+">'."\n");
    echo('<div id="edu_fields">'."\n");

    if(count($schools) > 0)
    {
        foreach($schools as $school)
        {
            $countEdu++;
            $year = htmlentities($school['year']);
            $name = htmlentities($school['name']);

            echo('<div id="edu'.$countEdu.'">'."\n");
            echo('<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$year.'">');
            echo('<input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>'."\n");
            echo('<p> School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school" value="'.$name.'"></div>'."\n"."\n");
        }
    }
    echo('</div></p>'."\n"."\n");

    // load and display positions
    $positions = loadPos($pdo, $_REQUEST['profile_id']);
    $countPos = 0;
    echo('<p>Position: <input type="submit" id="addPos" value ="+">'."\n");
    echo('<div id="position_fields">'."\n");

    if(count($positions) > 0)
    {
        foreach($positions as $position)
        {
            $countPos++;
            $year = htmlentities($position['year']);
            $desc = htmlentities($position['description']);

            echo('<div id="position'.$countPos.'">'."\n"."\n");
            echo('<p>Year: <input type="text" name="year'.$countPos.'" value="'.$year.'">');
            echo('<input type="button" value="-" onclick="$("#position'.$countPos.').remove();return false;""></p>'."\n");
            echo('<textarea name="desc'.$countPos.'" rows="8" cols="80">'.$desc.'</textarea></div>');
        }
    }
    // print the extra boxes that emerge when + is clicked
    echo("\n\n");
    echo ('<p><input type="submit" value="Save"> <input type="submit" name="cancel" value="Cancel"></p></form></div>'."\n"."\n");
    ?>
 
<script>createPosBox();</script>
<script>createEduBox();</script>
<script>eduSearch();</script>
</body>
</html>

<?php
require_once "pdo.php";
require_once "util.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['name']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="logout.php">front page.</a>');
}

if (isset($_POST['cancel']) )
{
      header('Location: index.php');
      return;
}

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
      header('Location: add.php');
      return;
    }

    // validate position in entries if present
    $msg = validatePos();
    if(is_string($msg))
    {
      $_SESSION['error'] = $msg;
      header('Location: add.php');
      return;
    }

    // validate position in entries if present
    $msg = validateEdu();
    if(is_string($msg))
    {
      $_SESSION['error'] = $msg;
      header('Location: add.php');
      return;
    }

    // data is valid so we should insert into table
    $sql = "INSERT INTO profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']));

    // provide the primary key for pos
    $profile_id = $pdo->lastInsertId();

    // insert the position entries
    insertPos($pdo, $profile_id);

    // insert the position entries
    insertEdu($pdo, $profile_id);

    $_SESSION['success'] = 'Profile Added';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Add Page</title>
<?php require_once "head.php";?>
<?php require_once "util.php";?>
<script src="util.js"></script>
</head>
<body>

<div class = "container">
  <h1> Adding Profile for <?= htmlentities($_SESSION["name"]); ?></h1>
  <?php flashMessages();?>

  <p>Please fill in the form below: </p>
    <form method="post">
    <p>First Name:<input type="text" name="first_name" size = "60"></p>
    <p>Last Name:<input type="text" name="last_name" size = "60"></p>
    <p>Email:<input type="text" name="email" size = "30"></p>
    <p>Headline:<br/><input type="text" name="headline" size = "80"></p>
    <p>Summary:<br/><textarea name="summary" rows="8" cols="80"></textarea></p>
    <p>Education:<input type="submit" id="addEdu" value ="+"><div id="edu_fields"></div></p>
    <p>Position:<input type="submit" id="addPos" value ="+"><div id="position_fields"></div></p>
    <p><input type="submit" value="Add"> <input type="submit" name="cancel" value="Cancel" ></p>
  </form>
</div>

<script>createPosBox();</script>
<script>createEduBox();</script>
<script>eduSearch();</script>
</body>
</html>

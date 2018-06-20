<?php

session_start();
require_once "pdo.php";

// if the 'cancel' button is pressed, then go back to log in page
if ( isset($_POST['Cancel'] ) )
{
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

// check the stored hash for autograder
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123, email is csev@autos.com
$failure = false;

if ( isset($_POST['email']) && isset($_POST['pass'])  )
{
    // Logout current user
    unset($_SESSION["email"]);

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 && preg_match('/\b@\b/',$_POST['email']) )
    {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    }
    else if (strlen($_POST['email']) > 1 && !preg_match('/\b@\b/',$_POST['email']) )
    {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }
    else if(strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0 && preg_match('/\b@\b/',$_POST['email']))
    {
         // create the SQL statement to log in
         $sql = "SELECT name FROM users
             WHERE Email = :em AND password = :pw";
         // prepare the SQL statement
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
             ':em' => $_POST['email'],
             ':pw' => $_POST['pass']));
         // fetch the first row and put in associated array
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         $check = hash('md5', $salt.$_POST['pass']);
         if ( $check == $stored_hash )
         {
             error_log("Login success ".$_POST['email']);
             // update current session and user message
             $_SESSION["email"] = $_POST["email"];
             $_SESSION["name"] = $_POST["email"];
             $_SESSION["success"] = "Logged in.";

             // Redirect the browser toview.php
             header( 'Location: view.php' ) ;
             return;
         }
         else
         {
             error_log("Login fail ".$_POST['email']." $check");
             // update current session and user message
             $_SESSION["error"] = "Incorrect password.";

             // Redirect the browser to login.php
             header( 'Location: login.php' ) ;
             return;
         }
     }
}
// Fall through into the View
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Kgotso Koete's Login Page </title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
    // print a red error message if not successful
    if ( isset($_SESSION["error"]) )
    {
        echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
        unset($_SESSION["error"]);
    }
?>

<form method="post">
<label for="email">User Name</label>
<input type="text" name="email" id= "who"><br/>
<label for="password">Password</label>
<input type="text" name="pass" id = "pass"><br/>
<input type="submit" value="Log In">
<input type="submit" name="Cancel" value="Cancel">
</form>
<p>
Enjoy trading your car for cash!
</p>
</div>
</body>

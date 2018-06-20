<?php
require_once "pdo.php";

// if the 'cancel' button is pressed, then go back to log in page
if ( isset($_POST['Cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

// check the stored hash for autograder
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123, email is csev@autos.com
$failure = false;

if ( isset($_POST['who']) && isset($_POST['pass'])  )
{
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 && preg_match('/\b@\b/',$_POST['who']) )
    {
        echo("<pre style='color:red;'>\n"."Email and password are required"."\n</pre>\n");
    }
    else if (strlen($_POST['who']) > 1 && !preg_match('/\b@\b/',$_POST['who']) )
    {
        $failure = "Email must have an at-sign (@)";
    }
    else if(strlen($_POST['who']) > 0 && strlen($_POST['pass']) > 0 && preg_match('/\b@\b/',$_POST['who']))
    {
         // create the SQL statement to log in
         $sql = "SELECT name FROM users
             WHERE Email = :em AND password = :pw";
         // prepare the SQL statement
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
             ':em' => $_POST['who'],
             ':pw' => $_POST['pass']));
         // fetch the first row and put in associated array
         $row = $stmt->fetch(PDO::FETCH_ASSOC);

         $check = hash('md5', $salt.$_POST['pass']);
         if ( $check == $stored_hash )
         {
             // Redirect the browser to game.php
             header("Location: autos.php?name=".urlencode($_POST['who']));
             return;
         }
         else
         {
             echo("<pre style='color:red;'>\n"."Incorrect password"."\n</pre>\n");
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
// Note triple not equals and think how badly double
// not equals would work here...
if ( $failure !== false )
{
    // print a failure message if incorrect log in detail
    echo('<p style="color: red;">'.htmlentities($failure)."</p>");
}
?>
<form method="post">
<label for="email">User Name</label>
<input type="text" name="who" id= "who"><br/>
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

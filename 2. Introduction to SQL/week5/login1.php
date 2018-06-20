<?php
    // first clear the session in case user navigates back to first page
    session_start();
    session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
<title>Kgotso Koete: Front Login</title>
</head>
<body>

<h1>Welcome to the Automobiles Database</h1>

<p>
<a href="login2.php">Please log in</a>
</p>
<p> Attempt to go to <a href="add.php">add.php</a> without logging in - it should fail with an error message.</p>
<p> Attempt to go to <a href="edit.php">edit.php</a> without logging in - it should fail with an error message.</p>


</body>
</html>

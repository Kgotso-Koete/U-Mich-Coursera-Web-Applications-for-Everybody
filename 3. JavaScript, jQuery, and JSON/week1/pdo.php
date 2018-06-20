<?php

// open up new SQL connection using default user name and password
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=cv_register',
   'root', 'root');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

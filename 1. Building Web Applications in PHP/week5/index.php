<!DOCTYPE html>
<html>
  <head>
    <!-- Your name must be in the <title> tag like 'Charles Severance PHP -->
    <title>Kgotso Koete's SHA256 encryption</title>
  </head>
<!-- There should be an <h1> tag with your name and text like 'Charles Severance PHP' -->
<h1>Here is the SHA256 for my name</h1>
<pre> ASCI Art:
      *      *
      *    *
      *  *
      **
      *  *
      *    *
      *      *
</pre>
<p>
<?php
// Your code should use PHP to compute the SHA256 of your name and print it out
$secret_key = "Kgotso Koete";
$key = hash('sha256', $secret_key);
echo "My encypted name is: ", $key;
?>
</p>
</html>

<?php
// source of code: https://github.com/daveismyname/autocomplete/blob/master/search.php
if (isset($_GET['term']))
{
	$return_arr = array();
	try {
	    $conn = new PDO('mysql:host=localhost;port=3306;dbname=cv_register',
         'root', 'root');
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    $stmt = $conn->prepare('SELECT name FROM Institution WHERE name LIKE :term');
	    $stmt->execute(array('term' => '%'.$_GET['term'].'%'));

	    while($row = $stmt->fetch()) {
	        $return_arr[] =  $row['name'];
	    }
	} catch(PDOException $e) {
	    echo 'ERROR: ' . $e->getMessage();
	}
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>

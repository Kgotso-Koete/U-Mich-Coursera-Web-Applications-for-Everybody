<?php
// unil.php
function flashMessages()
{
  // print error messages for any errors or record updates
  if ( isset($_SESSION['error']) )
  {
      echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
      unset($_SESSION['error']);
  }
  if ( isset($_SESSION['success']) )
  {
      echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n";
      unset($_SESSION['success']);
  }
}

function validateProfile()
{
    // check for missing data in any field
    if (
      strlen($_POST['first_name']) == 0 ||
      strlen($_POST['last_name']) == 0 ||
      strlen($_POST['email']) == 0 ||
      strlen($_POST['headline']) == 0 ||
      strlen($_POST['summary']) == 0)
    {
        return 'All main profile fields are required';
    }
    // check if the email does not have an @ sign
    if (strlen($_POST['email']) > 0 && !preg_match('/\b@\b/',$_POST['email']) )
    {
      return "Profile email must have an at-sign (@)";
    }
    return true;
}

function validatePos()
{
    for($i = 1; $i <= 9; $i++)
    {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ){return "Position year and job description are required";}
        if ( ! is_numeric($year) ){return "Position year must be numeric";}
    }
    return true;
}

function validateEdu()
{
    for($i = 1; $i <= 9; $i++)
    {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;

        $edu_year = $_POST['edu_year'.$i];
        $school_name = $_POST['edu_school'.$i];
        if ( strlen($edu_year ) == 0 || strlen($school_name) == 0 ){return "Education year and school name are required";}
        if ( ! is_numeric($edu_year) ){return "Education year must be numeric";}
    }
    return true;
}

function loadPos($pdo, $profile_id)
{
    $sql ='SELECT * FROM position where profile_id = :prof ORDER BY rank';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $profile_id));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

function loadEdu($pdo, $profile_id)
{
    $sql ='SELECT year, name FROM education JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id = :prof ORDER BY rank';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $profile_id));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $educations;
}

function insertPos($pdo, $profile_id)
{
    $pos_rank = 1;
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
            ':pid' => $profile_id,
            ':rank' => $pos_rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $pos_rank++;
    }
}

function insertEdu($pdo, $profile_id)
{
    $ed_rank = 1;
    for($i = 1; $i <= 9; $i++)
    {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];

        // lookup the school if it exists
        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false){ $institution_id = $row['institution_id'];}

        // if there is no institution_id, insert it
        if($institution_id === false)
        {
            $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            // provide the primary key for edu
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO education
            (profile_id, rank, year, institution_id)
        VALUES ( :pid, :rank, :year, :iid)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $ed_rank,
            ':year' => $year,
            ':iid' => $institution_id)
        );

        $ed_rank++;
    }

}

?>

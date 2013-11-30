<?php

/*
2013 - Dennis Johnson

This page allows you to administer the list of holidays used in the Find-Next-Weekdays script 

*/

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $pwd;
    $fh = fopen('weekdays.config','r');
    if ($fh) {
      while (($line = fgets($fh)) != false) {
        $pwd = $line;  
        // trim trailing ' ' character
        $pwd = substr($pwd, 0, -1);
      }

      fclose($fh);
    }

    $id = $_POST['id'];

    // gets the holiday dates from the db
    $conn = mysql_connect("localhost", "root", $pwd);
    $db = mysql_select_db("working_days", $conn);
    $sql = "DELETE FROM dates WHERE id=$id";

    // make db query
    $result = mysql_query($sql);
    if (!$result)
      echo mysql_error();
}


if(isset($_POST['input_date']) && isset($_POST['submit']))
  {
    // $total is the number of business days the script interprets
    $total = 0;
    $error = '';
    $date = '';
    try {
        $date = new DateTime($_POST['input_date']);
    } catch (Exception $e) {
        $error = "Date entered does not match a valid calendar date.";
    }

    // If there are no errors, proceed with finding next workdays
    if($error == '')
    {
      $date = new DateTime($_POST['input_date']);
      $date_format = (string)date_format($date, 'Y-m-d');

      $pwd;
      $fh = fopen('weekdays.config','r');
      if ($fh) {
        while (($line = fgets($fh)) != false) {
          $pwd = $line;  
          // trim trailing ' ' character
          $pwd = substr($pwd, 0, -1);
        }

        fclose($fh);
      }
 
      // gets the holiday dates from the db
      $conn = mysql_connect("localhost", "root", $pwd);
      $db = mysql_select_db("working_days", $conn);
      $sql = "INSERT INTO dates SET date='$date_format'";

      // make db query
      $result = mysql_query($sql);
      if (!$result)
        echo mysql_error();
   
    }
  }

  // Display all the entries currently in the db
   
  $pwd;
  $fh = fopen('weekdays.config','r');
  if ($fh) {
    while (($line = fgets($fh)) != false) {
      $pwd = $line;  
      // trim trailing ' ' character
      $pwd = substr($pwd, 0, -1);
    }

    fclose($fh);
  }

  // gets the holiday dates from the db
  $conn = mysql_connect("localhost", "root", $pwd);
  $db = mysql_select_db("working_days", $conn);
  $sql = "select unix_timestamp(date), id from dates";

  // make db query
  $result = mysql_query($sql);
  if (!$result)
    echo mysql_error();

  // place holiday timestamps into array
  while ($row = mysql_fetch_array($result)) { 
    $converted_holidays[] = $row[0];
    $ids[] = $row[1];
  }

?>


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

<!DOCTYPE>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Holiday Admin</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $('.to_delete').click(function(event) {
    if (!window.confirm("Are you sure you wish to delete this date?")) {
      event.preventDefault();
    } 
  });
  $('#submit').click(function(event) {
    if(validate()){
      $('#date_form').submit();
    }
    else {
      event.preventDefault();
    }
  });
});

function validate() {
    var re = /\d{2}\/\d{2}\/\d{2}/;
    var OK = re.test($("#input_date").val());
    if (OK) {
      return true;
    }
    
    else {
      $(".error_message").text('*The date you entered is either empty or is not formatted properly.');
      return false;
    }
}
</script>
</head>
<body>

<h1>Routing Helper Admin</h1>


<p>Here is the current list of all days recognized as holidays:</p>

<ul>

<?php
if(isset($converted_holidays) && isset($ids))
  {
    $count = 0;
    $action = $_SERVER['PHP_SELF'];
    foreach($converted_holidays as $holiday)
    {
        echo 
          "<li>" . 
          "<form action='$action' method='post'>" . /* action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>" . */
          "<input class='to_delete' type='submit' value='Delete' name='delete'/>" . 
          date('l, M j Y', $holiday) . 
          "<input type='hidden' value='$ids[$count]' name='id'/>" .
          "</form>" .  
          "</li>";
        $count += 1;
    }
  }
?>

</ul>

<p>
Please enter the desired date in the format of <strong>01/11/12</strong><br /> where 01 is the month (January), 11 is the day, and 12 (2012) is the year.</p>

<p>
<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
  <input id="submit" type="submit" name="submit" value="Add" />
  <label for="input_date">Input Date: </label>
  <input id="input_date" type="textarea" name="input_date" />
  <input type="submit" name="clear" value="clear" />
</form>
</p>


<p class="error_message"></p>
<?php
if(isset($error))
{
    echo "<p class='error_message'>$error</p>";
}
?>


</body>
</html>

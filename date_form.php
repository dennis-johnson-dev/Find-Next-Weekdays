<?php

/*
2012 - Dennis Johnson

This script allows you to get the date of the nth business day. This excludes weekends and holidays. 

You add your holidays of the year to the $holidays array and the script converts them to Unix timestamps from 1970.
*/

if(isset($_POST['input_date']) && isset($_POST['submit']))
  {
    // $total is the number of business days the script interprets
    $total = 0;
    $error = '';
   
    // checks the values of the radio inputs selecting either paper or electronic 
    if(isset($_POST['submission_type']) && $_POST['submission_type'] == "electronic")
    {
      $total = 12;
    }
    else if(isset($_POST['submission_type']) && $_POST['submission_type'] == "paper")
    {
      $total = 5;
    }
    else 
    {
      $error = "Please select Electronic or Paper";
    }

    // set default timezone so PHP knows where to base the times off of

    date_default_timezone_set('America/Denver');

    // human readable holidays YEAR-MO-DA
    $holidays = array(
    "2012-10-18", "2012-10-19", "2012-9-4", "2012-11-23", "2012-11-22", "2012-12-25", "2012-12-24", "2013-01-01", "2013-01-21", "2013-02-18", "2013-05-27", "2013-07-04", "2013-09-02", "2013-10-14", "2013-11-11", "2013-11-18", "2013-11-28", "2013-11-29", "2013-12-25", "2013-12-24" 
    );

    $converted_holidays = array ();

    // read password from file
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
    $sql = "select unix_timestamp(date) from dates";

    // make db query
    $result = mysql_query($sql);
    if (!$result)
      echo mysql_error();

    // place holiday timestamps into array
    while ($row = mysql_fetch_array($result)) { 
      $converted_holidays[] = $row[0];
    }


    // as the script finds valid weekdays, it puts the UNIX timestamp in the weekdays array
    $weekdays = array ();

    // receives the date input from form and checks for a valid date
    // if there is an exception and PHP computes an invalid date
    // it returns an error to the user
    date_default_timezone_set('America/Denver');
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
      $date_format = date_format($date, 'U');
      $workdays = 0;
      $final_time = 0;
      
      // checks to see if any of the next 30 days are a weekday that
      // is not a holiday or weekend

      for ($i=1; $i<30; $i++)
      {

          if(date("N", $date_format-($i*86400)) < 6 && !in_array($date_format-($i*86400), $converted_holidays))
          {
              $weekdays[] = $date_format-($i*86400);
              $workdays += 1; 
          }
 
          // once the desired number of workdays are found, the script exits the loop 
          if($workdays == $total)
          {
              $final_time = $date_format-($i*86400);
              break;
          }
      }

      $time = $final_time; 
    }
  }
  
?>

<!DOCTYPE>
<html lang="en">
<head>
<meta charset=utf-8>
<title>Date Helper</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
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

<h1>Routing Date Helper</h1>

<form id="date_form" name="date_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<p>
Please enter the desired date in the format of <strong>01/11/12</strong><br /> where 01 is the month (January), 11 is the day, and 12 (2012) is the year.</p>
<p>Please select the format of your submission.</p>

<p>
<input type="radio" name="submission_type" value="electronic"> Electronic (12 days)
<input type="radio" name="submission_type" value="paper"> Paper (5 days)
</p>

<p>
<label for="input_date">Input Date: </label>
<input id="input_date" type="textarea" name="input_date" />
<input id="submit" type="submit" name="submit" value="submit" />
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
<?php 

if(isset($time))
  {
    echo date('D, dS \o\f F Y', $date_format) . " -> ";
    echo "<strong>" . date('D, dS \o\f F Y', $time) . "*</strong><br />";
    echo "<p>*Please verify this with your academic calendar </p>" .
         "These are the dates we are considering to be Workdays:</p>";
  }

?>


<ol>
<?php
 
  if(isset($weekdays))
  {
    foreach($weekdays as $weekday)
    {
        echo "<li>" . date('l, M j Y', $weekday) . "</li>";
    }
  }

?>
</ol>

</body>
</html>

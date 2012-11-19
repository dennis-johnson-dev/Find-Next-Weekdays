<?php

/*
2012 - Dennis Johnson

This script allows you to get the date of 14th workday. This excludes weekends and holidays. 

You add your holidays of the year to the $holidays array and the script converts them to Unix timestamps from 1970.
*/

if(isset($_POST['input_date']) && isset($_POST['submit']))
  {

    // set default timezone so PHP knows where to base the times off of

    date_default_timezone_set('America/Denver');

    $holidays = array(
    "2012-10-18", "2012-10-19", "2012-9-4", "2012-11-23", "2012-11-22", "2012-12-25", "2012-12-31"
    );

    $converted_holidays = array ();

    // converts the holidays to Unix timestamps
    foreach($holidays as $holidays_item)
    {
        $holidays_date = new DateTime($holidays_item);
        $unix_holidays_item = date_format($holidays_date, 'U');
        $converted_holidays[] = $unix_holidays_item;
    }

    $weekdays = array ();

    // receives the date input from form
    date_default_timezone_set('America/Denver');
    $date = '';
    $error = '';
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
 
          // once 14 workdays are found, the script exits the loop 
          if($workdays == 14)
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
<html>
<head>
<title>Date Helper</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="./jquery-1.8.2.min.js"></script>
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
Please enter the desired date in the format of <strong>01/11/12</strong><br /> where 01 is the month (January), 11 is the day, and 12 is the year.</p>
<p>
<label for="input_date">Input Date: </label>
<input id="input_date" type="textarea" name="input_date" />

<input id="submit" type="submit" name="submit" value="submit" />
<input type="submit" name="clear" value="clear" />
</form>

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


<ul>
<?php
 
  if(isset($weekdays))
  {
    foreach($weekdays as $weekday)
    {
        echo "<li>" . date('l, M j', $weekday) . "</li>";
    }
  }

?>
</ul>

</body>
</html>

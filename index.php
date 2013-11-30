<?php include 'input_process.php'?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Single Page Theme</title>
  <link rel="stylesheet" type="text/css" href="css/resets.css">  
  <link rel="stylesheet" type="text/css" href="css/style.css">

  <script src="js/jquery-1.6.3.min.js"></script>
  <!--JS for nav -->
  <script type="text/javascript">
    $(document).ready(function(){
      $("#nav ul.child").removeClass("child");

      $("#nav li").has("ul").hover(function() {
        $(this).addClass("current").children("ul").show();
      }, function () {
        $(this).removeClass("current").children("ul").hide();
      });

    });
  </script>

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
<div id="container">
  <div id="header">
    <div id="logo">
      <a href="./index.html">
        <img src="./img/clas_chemistry.png" width="564" height="78" />
      </a>
    </div>
    <ul id="nav" class="nav">
      <li>
        <a href="./index.html">Home</a>
        <ul class="child">
          <li>
            <a href="http://www.ucdenver.edu/academics/colleges/CLAS/Departments/chemistry/Pages/Chemistry.aspx">Chemistry Home</a>
          </li>
        </ul>
      </li>
       <li>
        <a href="">Contact Us</a>
      </li>
    </ul>
</div>
<div id="wrapper">
<div id="content">
<h1>Home</h1>
<h2>Routing Helper Admin</h2>

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

</div>
</div
</div>
</div>

<div id="footer">
  <img src="./img/UCDlogo.png" width="60"/>
    <div id="footer_content">
      <ul>
        <li>
          <a href="http://www.ucdenver.edu/about/contact/Pages/default.aspx">Contact Us |</a>
        </li>
        <li>
          <a href="http://www.ucdenver.edu/websitefeedback/Pages/form.aspx">Website Feedback |</a>
        </li>
        <li>
          <a href="https://www.cu.edu/">CU System |</a>
        </li>
        <li>
          <a href="http://www.ucdenver.edu/policy/Pages/PrivacyPolicy.aspx">Privacy Policy |</a>
        </li>
        <li>
          <a href="http://www.ucdenver.edu/policy/Pages/LegalNotices.aspx">Legal Notices |</a>
        </li>
        <li>
          <a href="http://www.ucdenver.edu/about/departments/HR/jobsoncampus/Pages/index.aspx">Employment</a>
        </li>
      </ul>
      <p>&copy; 2012 <a href="https://www.cusys.edu/regents/">The Regents of the University of Colorado</a>, a body corporate. All rights reserved. <br /> All trademarks are registered property of the Univeristy. Used by permission only</p>
    </div>
</div>
</body>

</html>

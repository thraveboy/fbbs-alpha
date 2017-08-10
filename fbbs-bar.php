<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="fbbs-style.css">

</head>
<body>

<?php
  $previous_cmd_trim = '';

  if (isset($_POST['command'])) {
    $previous_cmd_trim = trim($_POST['command']);
    $_POST['command'] = $previous_cmd_trim;
  }

  if (empty($previous_cmd_trim) && isset($_GET['command'])) {
    $previous_cmd_trim = trim($_GET['command']);
    $_POST['command'] = $previous_cmd_trim;
  }

  $_LOCAL_API_CALLS = 1;

  require_once 'fbbs-api.php';

  $previous_command = explode(" ", $previous_cmd_trim)[0];
  echo '<div id="previous_command" hidden>';
  print($previous_command);
  echo '</div>';

  require_once 'fbbs-user-auth.php';

  $username = authorize_user();

  $lastauth = last_auth_user();

  require_once 'fbbs-std-cmd.php';
?>

<script>

function showDash(str_full) {
  var xhttp;
  var str_trim = str_full.trim();
  var str = str_trim.split(" ")[0];
  if (str.length == 0) {
    return;
  }

  fbbsUpdateBoardInfo(str);

  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var ctx = document.getElementById("dashChart");
      var dashHtml = "";
      var label_array = [];
      var label_sym_array = [];
      var data_array = [];
      var label_locations = [];
      var color_array = [];
      var color_label_array = [];
      var min_timestamp = new Date().getTime();
      var max_timestamp = min_timestamp;
      var current_time = min_timestamp/1000;
      var previous_time = current_time;

      var dataTransformDraw = new FBBSDataDraw(ctx, str,
                                               "value_height_label_bar");
      dataTransformDraw.processDataDraw(this.responseText);
      return;
    }
  }
  xhttp.open("POST", "fbbs-api.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("command="+str);
}

</script>

</body>
</html>




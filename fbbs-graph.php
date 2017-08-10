<!DOCTYPE html>
<html>
<head>
<style>
body, input {
    font-family: monospace;
    background-color: white;
    color: black;
    font-size: large;
}

input {
    border-top-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 0px;
    border-right-width: 0px;
    border-color: blue;
    outline-width: 1px;
    outline-width-left: 0px;
    outline-width-right: 0px;
    outline-color: blue;
    color: blue;
}

input[type=submit] {
    outline-color: blue;
    background-color: white;
}

p {
  white-space: nowrap;
}

</style>
</head>
<body>

<script src="moment-with-locales.min.js"></script>
<script src="Chart.min.js"></script>

<p>
<canvas id="dashChart" width="400" height="100"></canvas>
</p>

<?php
  $previous_cmd_trim = trim($_POST['command']);
  $_POST['command'] = $previous_cmd_trim;

  if (empty($previous_cmd_trim)) {
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
?>
|\\:::::::::::::::::::::::::::::::::::|\::::::::::::::::::
<span id="back_to_main">
  <FORM NAME="backtomain" METHOD="POST" ACTION="fbbs-main.php" style="display:inline">
    <INPUT TYPE="Submit"  Value="<<--back to main">
  </form>
</span>
<br>
||| <b>f</b>ury <b>b</b>ulletin <b>b</b>oard <b>s</b>ystem (<b>fbbs</b>) ||: board :
<span id="board_name"></span>
<br>
|||...................................|/:::::::::::::::...last online...
<b>[<span id="last_active"><?=$lastauth?></span>]</b>...

<FORM NAME="form1" METHOD="POST" id="form1">
    board name:
<?php
  echo '<INPUT TYPE="text" VALUE="' . $previous_command  . ' " ';
  echo 'id="command" NAME="command" SIZE="20" autofocus>';
?>
    <INPUT TYPE="submit" Value="|/\enter/\|">
</FORM>
|||||||||||||||||
<br>
|||<u> board info </u>||
<br>
<div id="board_info"></div>

<div id="dash"></span>

<script>

String.prototype.hashCode = function(){
	var hash = 0;
	if (this.length == 0) return hash;
	for (i = 0; i < this.length; i++) {
		char = this.charCodeAt(i);
		hash = ((hash<<5)-hash)+char;
		hash = hash & hash; // Convert to 32bit integer
	}
	return hash;
}
var prev_cmd_val = document.getElementById("previous_command").innerText;
prev_cmd_val = prev_cmd_val.split(" ")[0];

function funPrefixes(prefix_length = 5) {
  var char_set = ["_", "_", "_", "_", "_", "_", "_", "_",
                  " ", " ", ".", "O", "0", "o"];

  var return_string = "";
  for (var i=0; i<prefix_length; i++) {
    return_string += char_set[Math.floor(Math.random() * char_set.length)];
  }
  return return_string;
}

function infoOutput(msgObj) {
  var return_html = "";
  if (msgObj) {
    if (msgObj["value"] !=  undefined) {
      return_html += "|" + funPrefixes(1) + "| <b>" + msgObj["value"] +
                     "</b> ";
    }
  }
  return return_html;
}

function messageOutput(msgObj) {
  var return_html = "";

  if (msgObj) {
    if (msgObj["value"] !=  undefined) {
      var valueTrimmed = msgObj["value"].trim();
      var valueSplit = valueTrimmed.split(" ");
      var returnValue = valueTrimmed;
      var valSplitLen = valueSplit.length;
      if (valSplitLen == 2) {
        return_html += "y: " + valueSplit[0] + ", x: " +
                       valueSplit[1];
      }
      else if (valSplitLen == 3) {
        return_html += "y: " + valueSplit[0] + ", x: " +
                       valueSplit[1] + ", r: " + valueSplit[2];
      }
      else {
        return_html += returnValue;
      }
    }
  }
  return return_html ;
}

function messageLabel(msgObj) {
  var return_html = "";

  if (msgObj) {
    if (msgObj["timestamp"] !=  undefined) {
      return msgObj["timestamp"].trim();
    }
  }
  return return_html ;
}

function getValueColor(str) {
  var currentColor = "rgba(0, 0, 255, 0.25)";
  if (!isNaN(parseFloat(str)) && (isFinite(str))) {
    if (str < 50) {
      currentColor = "rgba(255, 0, 0, 0.15)";
    } else if (str < 75) {
      currentColor = "rgba(255, 255, 0, 0.2)";
    }
  }
  return currentColor;
}

function showDash(str_full) {
  var xhttp;
  var str_trim = str_full.trim();
  var str = str_trim.split(" ")[0];
  if (str.length == 0) {
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var ctx = document.getElementById("dashChart");
      var label_array = [];
      var label_sym_array = [];
      var data_array = [];
      var color_array = [];
      var data_avg = 0;
      var current_time = (new Date()).getTime()/1000;
      try {
        var jsonresponseparsed = JSON.parse(this.responseText);
      } catch(err) {
        return;
      }
      if (jsonresponseparsed == undefined ||
          jsonresponseparsed.value == undefined) return;
      var jsonresponseobj = jsonresponseparsed.value[0];
      Object.keys(jsonresponseobj).forEach(function(key,index) {
        var array_obj = jsonresponseobj[key];
        var entry_obj = new Object();
        for (var i=0; i < array_obj.length; i++) {
          var keyval_obj = array_obj[i];
          Object.keys(keyval_obj).forEach(function(key,index) {
            Object.keys(keyval_obj).forEach(function(id,idx) {
                entry_obj[id] = keyval_obj[id];
              });
          });
        }
        var new_val = messageOutput(entry_obj);
        var new_label = parseInt(messageLabel(entry_obj));
        new_label = parseInt(current_time - new_label) + " secs ago";
        var isTuple = new_val.split(" ").length > 1;
        var currentColor = "rgba(0,0,150,0.5)";
        if (isTuple) {
          var new_val_obj = eval("({"+new_val+"})");
          if (typeof new_val_obj == 'object') {
              label_array.unshift(new_label);
              data_array.unshift(new_val_obj);
              currentColor = getValueColor(new_val.split(" ")[1]);
              data_avg += parseFloat(new_val.split(" ")[1]);
              color_array.unshift(currentColor);
          }
        }
        else {
          label_array.unshift(new_label);
          data_array.unshift(parseInt(new_val, 10));
          currentColor = getValueColor(new_val);
          data_avg += parseFloat(new_val);
          color_array.unshift(currentColor);
        }
      });
      data_avg /= data_array.length;
      var avg_color = getValueColor(data_avg);
      var dataStruct = {
        type: 'bar',
        labels: label_array,
        datasets: [
          {
            type: 'bar',
            label: '',
            data : data_array,
            lineTension: 0.1,
            fillColor: "rgba(100,100,0,0.1)",
            borderColor: color_array,
            backgroundColor: color_array,
            borderWidth: 10
           },
          {
            type: 'line',
            label: str,
            data : data_array,
            lineTension: 0.5,
            fill: true,
            fillColor: avg_color,
            borderColor: "rgba(0,0,200,0.02)",
            backgroundColor: avg_color,
            borderWidth: 3
           }
         ]
        };
      var charInstance = new Chart(ctx, {
          label: str,
          type: 'bar',
          data : dataStruct,
          options: {
            legend: {
              position: 'bottom',
              labels: {
                  usePointStyle: true,
                  fontColor: "rgba(150,0,150,0.8)",
                  fontFamily: "monospace"
                },
              reverse: true,
              responsive: false
            },
            scales: {
              xAxes: [{
                  display: true,
                  gridLines: { display: false},
                  position: "bottom",
                  ticks: {
                    fontColor: "rgba(150,0,150,0.8)",
                    fontFamily: "monospace",
                    mirror: true
                  },
                  max: 0
              }],
              yAxes: [{
                  display: true,
                  position: "left",
                  ticks: {
                    fontColor: "rgba(150,0,150,0.8)",
                    fontFamily: "monospace"
                  }
                }]
              }
            }
        });
    }
  }
  xhttp.open("POST", "fbbs-api.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("command="+str);

  var xhttp_dashinfo;
  xhttp_dashinfo = new XMLHttpRequest();
  xhttp_dashinfo.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("board_info").innerHTML = "|||<br>";
      var current_time = (new Date()).getTime();
      try {
        var jsonresponseparsed = JSON.parse(this.responseText);
      } catch(err) {
        return;
      }
      if (jsonresponseparsed == undefined ||
          jsonresponseparsed.value == undefined) return;
      var jsonresponseobj = jsonresponseparsed.value[0];
      Object.keys(jsonresponseobj).forEach(function(key,index) {
        var array_obj = jsonresponseobj[key];
        var entry_obj = new Object();
        for (var i=0; i < array_obj.length; i++) {
          var keyval_obj = array_obj[i];
          Object.keys(keyval_obj).forEach(function(key,index) {
            Object.keys(keyval_obj).forEach(function(id,idx) {
                entry_obj[id] = keyval_obj[id];
              });
          });
        }
        var entry_output = infoOutput(entry_obj);

        document.getElementById("board_info").innerHTML += entry_output + "<br>";
      });
    }
  }

  xhttp_dashinfo.open("POST", "fbbs-api.php", true);
  xhttp_dashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  xhttp_dashinfo.send("command="+str+" @");

  document.getElementById("board_name").innerHTML = str;
}

if (prev_cmd_val) {
  showDash(prev_cmd_val);
  document.getElementById("command").value = prev_cmd_val;
}

function getURLWithoutParams() {
  return location.pathname;
}

function updateDash(addCommandToUrl = false ) {
  var dashName = document.getElementById("command").value;
  if (dashName) {
    showDash(dashName);
    if (addCommandToUrl) {
      history.pushState({}, '',
                        getURLWithoutParams() + '?command=' + dashName);
    }
  }
}

function captureFormEnter(e) {
  if (e.preventDefault) e.preventDefault();

  updateDash(true);

  return false;
}

var formElement = document.getElementById('form1');
if (formElement.attachEvent) {
  formElement.attachEvent("submit", captureFormEnter);
}
else {
  formElement.addEventListener("submit", captureFormEnter);
}

var dashUpdater = setInterval(updateDash, 5000);

</script>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
<style>
body, input {
    font-family: monospace;
    background-color: black;
    color: green;
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
    outline-color: lime;
    color: green;
}

input:focus {
    border-color: white;
    outline-color: white;
    color: yellow;
}

input[type=submit]:focus {
    border-color: white;
    outline-color: white;
}

input[type=submit] {
    outline-color: lime;
    background-color: black;
}

p {
  white-space: nowrap;
}

canvas {
    outline-color: purple;
    border-color: purple;
    border-width: 2px;
    outline-width: 2px;
    background-color: black;
//    background-image: url('earth.png');
    background-size: 100% 100%;
    color: cyan;
}
</style>
</head>
<body>

<script src="moment-with-locales.min.js"></script>
<script src="Chart.min.js"></script>
<script>
const POINT_RADIUS = 1.0;
</script>

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
?>
|\\:::::::::::::::::::::::::::::::::::|\
<br>
||| <b>f</b>ury <b>b</b>ulletin <b>b</b>oard <b>s</b>ystem (<b>fbbs</b>) ||\
<?=$username?>
<br>
<u>|||</u>...................................|//\
<br>
--==>(last online:
<b>[<span id="last_active"><?=$lastauth?></span>]</b>):
<span id="back_to_main">
  <FORM NAME="backtomain" METHOD="POST" ACTION="fbbs-main.php" style="display:inline">
    <INPUT TYPE="Submit"  Value="<<--back to main">
  </form>
</span>
<br>
|||||||||||||||||
<br>
|||<u> board info </u>|| <span id="board_name"></span>
<br>
<u>|||||||||||||||||</u>
<br>
<div id="board_info"></div>
<u>|||||||||||||||||</u>
<br>
<p>
<canvas id="dashChart" width="640"
 height="480"></canvas> </p> <br> <FORM NAME="form1" METHOD="POST"
id="form1">
    board name:
<?php
  echo '<INPUT TYPE="text" VALUE="' . $previous_command  . ' " ';
  echo 'id="command" NAME="command" SIZE="20" autofocus>';
?>
    <INPUT TYPE="submit" Value="|/\enter/\|">
</FORM>
<br>
<FORM NAME="postmsg" METHOD="POST" ID="postmsg" ACTION="">
  post data=>
<?php
  echo '<INPUT TYPE="Text" VALUE="" ';
  echo 'id="message" NAME="command" SIZE="60">';
?>
  <INPUT TYPE="Submit" Value="<-enter|" SIZE="7">
</FORM>
<br>
<FORM NAME="viewmsg" METHOD="POST" ID="getmsg" ACTION="">
read message->
<INPUT TYPE="TEXT" VALUE="" id="getmsgid" SIZE="5">
<INPUT TYPE="Submit" Value="<=enter|" SIZE ="7">
<span id="displaymsg"></span>
</form>
<br>
<div id="dash"></div>

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

function strToRGBA(str) {
  var split_str = str.split(" ");
  var return_obj = { r:0, g:0, b:0, a: 0.5};
  if ((split_str[0] != undefined) && (!isNaN(parseInt(split_str[0],10)))) {
    return_obj.r = parseInt(split_str[0], 10);
  }
  if ((split_str[1] != undefined) && (!isNaN(parseInt(split_str[1],10)))) {
    return_obj.g = parseInt(split_str[1], 10);
  }
  if ((split_str[2] != undefined) && (!isNaN(parseInt(split_str[2],10)))) {
    return_obj.b = parseInt(split_str[2], 10);
  }
  if ((split_str[3] != undefined) && (!isNaN(parseFloat(split_str[3])))) {
    return_obj.a = parseFloat(split_str[3]);
  }
  return "rgba("+return_obj.r+","+return_obj.g+","+return_obj.b+
          ","+return_obj.a+")";
}

function curvePointValue(index) {
  var index_orig = index;
  var current_point = { x: 0.5, y: 0.5, r:POINT_RADIUS, label: index};
  var current_delta = 0.5;
  var current_level = 0;
  var current_level_amp = 4;
  var last_level_amp = 1;
  var accum_level_amp = 0;
  var level_offset = 1;
  var inner_offset = 0;
  var remainder;
  var new_x = current_point.x;
  var new_y = current_point.y;

  if (index > 0) {
    index -= 1;
    do {
      inner_offset = Math.floor((index_orig-1) / 4);
      remainder = (index+inner_offset) % 4;
      current_delta *= 0.5;
      current_point.r *= 0.5;
      switch (remainder) {
        case 0:
          new_x = current_point.x + current_delta;
          current_point.x = new_x;
          new_y = current_point.y + current_delta;
          current_point.y = new_y;
          break;
        case 1:
          new_x = current_point.x - current_delta;
          current_point.x = new_x;
          new_y = current_point.y + current_delta;
          current_point.y = new_y;
          break;
        case 2:
          new_x = current_point.x + current_delta;
          current_point.x = new_x;
          new_y = current_point.y - current_delta;
          current_point.y = new_y;
          break;
        case 3:
          new_x = current_point.x - current_delta;
          current_point.x = new_x;
          new_y = current_point.y - current_delta;
          current_point.y = new_y;
          break;
        default:
          break;
      }
      index -= (last_level_amp + inner_offset);
      last_level_amp = current_level_amp;
      current_level_amp *= 4;
      accum_level_amp += last_level_amp;
    } while (index_orig > accum_level_amp);
  }
  return current_point;
}

function strToXY(str) {
  var x = 0;
  var y = 0;
  var coordObj = { x: 0, y:0 };
  var currentXScale = 0.1;
  var currentYScale = 0.1;
  var currentAxis = "x";
  if (str) {
    var valueArray = str.split("");
    var projectedIntVal = 0;
    for (var i = 0; i < valueArray.length; i++) {
     projectedIntVal = parseInt(valueArray[i],10);
     if (!isNaN(projectedIntVal)) {
       switch (currentAxis) {
         case "x":
           x += (currentXScale * projectedIntVal);
           currentXScale /= 10;
           currentAxis = "y";
           break;
         case "y":
           y += (currentYScale * projectedIntVal);
           currentYScale /= 10;
           currentAxis = "x";
           break;
         default:
           currentAxis = "x";
           break;
       }
     }
     coordObj.x = x;
     coordObj.y = y;
    }
  }
  return coordObj;
}

function msgId(msgObj) {
  var return_html = "";
  if (msgObj) {
    if (msgObj["id"] !=  undefined) {
      return_html += msgObj["id"];
    }
  }
  return return_html;
}

function msgIP(msgObj) {
  var return_html = "";
  if (msgObj) {
    if (msgObj["ip"] !=  undefined) {
      return_html += msgObj["ip"];
    }
  }
  return return_html;
}

function msgValue(msgObj) {
  var return_html = "";
  if (msgObj) {
    if (msgObj["value"] !=  undefined) {
      return_html += msgObj["value"];
    }
  }
  return return_html;
}

function msgTimestamp(msgObj) {
  var return_html = "";

  if (msgObj) {
    if (msgObj["timestamp"] !=  undefined) {
     return_html += msgObj["timestamp"];
    }
  }
  return return_html ;
}

function getTimeDiffColor(sec_diff) {
  var return_color = "rgba(100,0,0,0.2)";
  if (sec_diff < 600) {
    return_color = "rgba(0,0,200,0.8)";
  }
  else if (sec_diff < 3600) {
    return_color = "rgba(50,200,0,0.5)";
  }
  return return_color;
}

function getTimeDiffBorder(sec_diff) {
  var return_color = "rgba(100,0,0,0.8)";
  if (sec_diff < 600) {
    return_color = "rgba(0,0,200,0.2)";
  }
  else if (sec_diff < 3600) {
    return_color = "rgba(50,250,0,0.3)";
  }
  return return_color;
}

function getValueColor(str) {
  var currentColor = "rgba(0, 0, 255, 0.8)";
  if (!isNaN(parseFloat(str)) && (isFinite(str))) {
    if (str < 50) {
      currentColor = "rgba(255, 0, 0, 0.2)";
    } else if (str < 75) {
      currentColor = "rgba(255, 255, 0, 0.5)";
    }
  }
  return currentColor;
}

function getDataColor(str) {
  var currentColor = "rgba(0, 0, 255, 0.1)";
  if (!isNaN(parseFloat(str)) && (isFinite(str))) {
    if (str < 50) {
      currentColor = "rgba(155, 0, 255, 0.2)";
    } else if (str < 75) {
      currentColor = "rgba(0, 155, 255, 0.5)";
    }
  }
  return currentColor;
}

var globalCharInstance = null;

function showBoardInfo(str_full) {
  var xhttp;
  var str_trim = str_full.trim();
  var str = str_trim.split(" ")[0];
  if (str.length == 0) {
    return;
  }

  var xhttp_dashinfo;
  xhttp_dashinfo = new XMLHttpRequest();
  xhttp_dashinfo.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var current_time = (new Date()).getTime();
      try {
        var jsonresponseparsed = JSON.parse(this.responseText);
      } catch(err) {
        return;
      }
      if (jsonresponseparsed == undefined ||
          jsonresponseparsed.value == undefined) return;
      var jsonresponseobj = jsonresponseparsed.value[0];
      document.getElementById("board_info").innerHTML = "";
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
        var entry_output = msgValue(entry_obj);
        document.getElementById("board_info").innerHTML += entry_output + "<br>";
      });
    }
  }

  xhttp_dashinfo.open("POST", "fbbs-api.php", true);
  xhttp_dashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  xhttp_dashinfo.send("command="+str+" @");
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
      var dashHtml = "";
      var label_array = [];
      var label_sym_array = [];
      var data_array = [];
      var new_data_array = [];
      var color_array = [];
      var color_label_array = [];
      var current_time = (new Date()).getTime()/1000;
      var previous_time = current_time;
      try {
        var jsonresponseparsed = JSON.parse(this.responseText);
      } catch(err) {
        return;
      }
      if (jsonresponseparsed == undefined ||
          jsonresponseparsed.value == undefined) return;
      var jsonresponseobj = jsonresponseparsed.value[0];
      var curve_index = 0;
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
        var new_value = msgValue(entry_obj).trim();
        var new_id = msgId(entry_obj);
        var entry_time = parseInt(msgTimestamp(entry_obj));
        var timestamp_diff = (current_time - entry_time);
        var previous_diff = previous_time - entry_time;
        previous_time = entry_time;
        var new_length = new_value.length;
        var new_data_entry = Math.round((timestamp_diff/60)*10)/10; // In minutes
        var data_value = 0;
        var label_value = new_value;
        var transformedVal = { x: 0, y:0, r: 0.5};
        if (new_value) {
            var split_data = new_value.split(" ");
            for (var j=0; j<split_data.length; j++) {
              var curr_vals = split_data[j].split("");
              var curr_color = {r: 0, g:0, b:0, a:0.5};
              if (curr_vals[0] != undefined &&
                  (!isNaN(parseInt(curr_vals[0], 10)))) {
                curr_color.r = parseInt(curr_vals[0],10) * 25;
              }
              if (curr_vals[1] != undefined &&
                  (!isNaN(parseInt(curr_vals[1], 10)))) {
                curr_color.g = parseInt(curr_vals[1],10) * 25;
              }
              if (curr_vals[2] != undefined &&
                  (!isNaN(parseInt(curr_vals[2], 10)))) {
                curr_color.b = parseInt(curr_vals[2],10) * 25;
              }
              if (curr_vals[3] != undefined &&
                  (!isNaN(parseInt(curr_vals[3],10)))) {
                curr_color.a = parseInt(curr_vals[3],10) * 0.1;
              }
              new_data_array.push(curr_color);
            }
            transformedVal = curvePointValue(curve_index);
            curve_index += 1;
            transformedVal.r = transformedVal.r * 100;
            transformedVal.label = transformedVal.label;
            split_data.shift();
            label_value = split_data.join(" ");
        }
        data_array.push(transformedVal);
        label_value = curve_index;
        label_array.push(label_value);
        currentColor = strToRGBA(new_value);
        color_array.push(currentColor);
        color_label_array.push(getTimeDiffBorder(previous_diff));
        dashHtml += "@" + new_id + ":" + new_value + ":minsago[" +
                    new_data_entry + "]<br>";
      });
      var dataStruct = {
        datasets: [
          {
            label: str,
            labels: label_array,
            type: "bubble",
            data: [{x:1.0,y:1.0,r:1}],
            fill: false,
            fillColor: "rgba(0,0,125,0.5)",
            borderColor: "rgba(0,200,200,0.2)",
            backgroundColor: "rgba(200,200,200,0.2)",
            borderWidth: 1
           }
         ]
        };
      if (globalCharInstance != null) {
        globalCharInstance.destroy();
        globalCharInstance = null;
      }

      globalCharInstance = new Chart(ctx, {
          type: 'bar',
          display: true,
          data : dataStruct,
          options: {
            responsive: false,
            animation: {
                onComplete: function () {
                    var ctx = this.chart.ctx;
                    var chart_elem = document.getElementById("dashChart");
                    var chart_x_max = chart_elem.width;
                    var chart_y_max = chart_elem.height;
                    var current_data;
                    var current_color = "rgb(0,0,0)";
                    var clean_x_div = 1.0;
                    var clean_y_div = 1.0;
                    for (var j = 0; j < new_data_array.length; j++) {
                         var color_data = new_data_array[j];
                         current_color = "rgba("+color_data.r+
                                               ","+color_data.g+","+
                                               color_data.b+","+
                                               color_data.a+")";
                         ctx.fillStyle = current_color;
                         var curvePoint = curvePointValue(j);
                         var pointLoc = {
                             x: chart_x_max*curvePoint.y,
                             y: chart_y_max-(chart_y_max*curvePoint.x)
                           };

                         ctx.beginPath();
                         ctx.lineWidth=10;
                         ctx.strokeStyle=current_color;
                         ctx.fillStyle = current_color;
                         var rect_width = curvePoint.r * ctx.canvas.width;
                         var rect_height = curvePoint.r * ctx.canvas.height;
                         ctx.fillRect(pointLoc.x-(.5*rect_width),
                                      pointLoc.y-(.5*rect_height),
                                      curvePoint.r * ctx.canvas.width,
                                      curvePoint.r * ctx.canvas.height);
                         ctx.stroke();
                      };
                  }
             },
            hover: {
                mode: 'nearest'
              },
            legend: {
                position: 'top',
                labels: {
                    showScaleLabels: true,
                    usePointStyle: true,
                    fontColor: "rgba(0,0,250,0.9)",
                    fontFamily: "monospace",
                    fontStyle: "bold"
                  },
                reverse: true,
                responsive: true
              },
            display: false,
            scales: {
                xAxes: [{
                    display: false,
                    gridLines: {
                        display: false,
                        lineWidth: 3,
                        color: color_label_array,
                        offsetGridLines: true
                      },
                    position: "top",
                    ticks: {
                      fontSize: 12,
                      fontColor: "rgba(200,250,0,0.9)",
                      fontFamily: "monospace",
                      mirror: true,
                      autoSkip: false,
                      display: false
                    },
                    min: 0
                }],
                yAxes: [{
                    display: false,
                    ticks: {
                      fontColor: "rgba(150,0,150,0.8)",
                      fontFamily: "monospace",
                      min: 0
                    }
                }]
              }
            }
          });
        document.getElementById("dash").innerHTML = dashHtml;
    }
  }
  xhttp.open("POST", "fbbs-api.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("command="+str);

  document.getElementById("board_name").innerHTML = str;
}

if (prev_cmd_val) {
  document.getElementById("command").value = prev_cmd_val;
  updateBoardInfo();
  showDash(prev_cmd_val);
}

function getURLWithoutParams() {
  return location.pathname;
}

function updateBoardInfo() {
  var dashName = document.getElementById("command").value;
  if (dashName) {
    showBoardInfo(dashName);
  }
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

  updateBoardInfo();
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

function capturePostEnter(e) {
  if (e.preventDefault) e.preventDefault();

  var dashName = document.getElementById("command").value;
  var xhttp_dashinfo;
  xhttp_dashinfo = new XMLHttpRequest();

  xhttp_dashinfo.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      updateDash();
    }
  }

  xhttp_dashinfo.open("POST", "fbbs-api.php", true);
  xhttp_dashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  var data = document.getElementById("message");
  if (data && data.value) {
    var commandString = "command="+dashName+" "+data.value;
    xhttp_dashinfo.send(commandString);
    document.getElementById("message").value = "";
  }

  updateDash();

  return false;
}

var formElementMsg = document.getElementById("postmsg");
if (formElementMsg.attachEvent) {
  formElementMsg.attachEvent("submit", capturePostEnter);
}
else {
  formElementMsg.addEventListener("submit", capturePostEnter);
}


function captureGetMsgEnter(e) {
  if (e.preventDefault) e.preventDefault();

  var dashName = document.getElementById("command").value;
  var xhttp_mdashinfo;
  xhttp_mdashinfo = new XMLHttpRequest();
  xhttp_mdashinfo.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("displaymsg").innerHTML = "";
      var current_time = (new Date()).getTime()/1000;
      try {
        var jsonresponseparsed = JSON.parse(this.responseText);
      } catch(err) {
        return;
      }
      if (jsonresponseparsed == undefined ||
          jsonresponseparsed.value == undefined) return;
      var jsonresponseobj = jsonresponseparsed.value;
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
        var entry_time = parseInt(msgTimestamp(entry_obj));
        var timestamp_diff = (entry_time - current_time);
        var new_data_entry = Math.round((timestamp_diff/60)*10)/10; // In minutes
        var entry_output = msgValue(entry_obj)+" [minsago("+new_data_entry+"]";

        document.getElementById("displaymsg").innerHTML += entry_output + "<br>";
      });
    }
  }

  var msgId = document.getElementById("getmsgid").value.trim()
  xhttp_mdashinfo.open("POST", "fbbs-api.php", true);
  xhttp_mdashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  xhttp_mdashinfo.send("command="+dashName+" @"+msgId);

  return false;
}

var formGetMsg = document.getElementById("getmsg");
if (formGetMsg.attachEvent) {
  formGetMsg.attachEvent("submit", captureGetMsgEnter);
}
else {
  formGetMsg.addEventListener("submit", captureGetMsgEnter);
}

updateBoardInfo();
updateDash();

var dashUpdater = setInterval(updateDash, 10000);

</script>

</body>
</html>

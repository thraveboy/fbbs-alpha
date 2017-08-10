: Welcome, <?=$username?>.
<br>
: <b>f</b>ury <b>b</b>ulletin <b>b</b>oard <b>s</b>ystem (<b>fbbs</b>) :
<span id="back_to_main">
  <FORM NAME="backtomain" METHOD="POST" ACTION="fbbs-main.php" style="display:inline">
    <INPUT TYPE="Submit"  Value="<-back to main->">
  </form>
</span>
<br>
: (last online:
<b>[<span id="last_active"><?=$lastauth?></span>]</b>)
<br>
<FORM id="launch_command_form" ACTION="" METHOD="POST">
<?php
  echo ': command: <INPUT TYPE="Text" VALUE="" ';
  echo 'id="launch_command" NAME="launch_command" SIZE="12" autofocus>';
?>
    <INPUT ID="submission" TYPE="Submit" Value="|/\enter/\|"
     onclick="launchCommand()">
</FORM>
:
<br> <FORM NAME="form1" METHOD="POST"
id="form1">
: board name::0>
<?php
  echo '<INPUT TYPE="text" VALUE="' . $previous_command  . ' " ';
  echo 'id="command" NAME="command" SIZE="20" autofocus>';
?>
    <INPUT TYPE="submit" Value="|/\enter/\|">
</FORM>
<FORM NAME="postmsg" METHOD="POST" ID="postmsg" ACTION="">
:  post message|>
<?php
  echo '<INPUT TYPE="Text" VALUE="" ';
  echo 'id="message" NAME="command" SIZE="25">';
?>
  <INPUT TYPE="Submit" Value="<-enter|" SIZE="7">
</FORM>
<FORM NAME="viewmsg" METHOD="POST" ID="getmsg" ACTION="">
: read message@>
<INPUT TYPE="TEXT" VALUE="" id="getmsgid" SIZE="5">
<INPUT TYPE="Submit" Value="<=enter|" SIZE ="7">
<span id="displaymsg"></span>
</form>
<canvas id="dashChart"></canvas>
<br>
: <u>data</u> :
<br>
<br>
<div id="dash"></div>

<script src="moment-with-locales.min.js"></script>
<script src="Chart.min.js"></script>
<script src="fbbs-core-data-draw-monitor.js"></script>

<script>

function launchCommand() {
  var str = document.getElementById("launch_command").value;
  var str_trim = str.trim();
  var str = str_trim.split(" ")[0];
   if (str.length == 0) {
    document.getElementById("dash").innerHTML = "Try help... ";
    return false;
  }
  else {
    var dashName = document.getElementById("command").value;
    document.getElementById("launch_command_form").action ="fbbs-" + str +
                                                    ".php?command=" +dashName;
    document.getElementById("launch_command_form").submit();
  }
  return false;
}

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

if (prev_cmd_val) {
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
    var curr_username = '[' + "<?php echo $username ?>" + ']';
    var commandString = "command="+dashName+" "+data.value+" "+curr_username;
    xhttp_dashinfo.send(commandString);
    document.getElementById("message").value = "";
  }

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

var dashUpdater = setInterval(updateDash, 10000);

</script>


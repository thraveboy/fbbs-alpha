<canvas id="dashChart"></canvas>

<br> <FORM NAME="form1" METHOD="POST"
id="form1">
: board name::0>
<?php
  echo '<INPUT TYPE="text" VALUE="' . $previous_command  . ' " ';
  echo 'id="command" NAME="command" SIZE="20" autofocus>';
?>
    <INPUT TYPE="submit" Value="|/\enter/\|">
</FORM>

<br>
: <u>board info</u> : <span id="board_name"></span>
<br>
<br>
<div id="board_info"></div>
<br>

: <u>data</u> :
<br>
<br>
<div id="dash"></div>

<script src="moment-with-locales.min.js"></script>
<script src="Chart.min.js"></script>
<script src="fbbs-core-data-draw.js"></script>

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

var dashUpdater = setInterval(updateDash, 5000);

</script>


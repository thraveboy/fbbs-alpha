<div id="reacting"></div>
<span id="displaymsg"></span>
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

<br>
: <u>board info</u> : <span id="board_name"></span>
<br>
<br>
<div id="board_info"></div>
<br>
<canvas id="dashChart"></canvas>

: <u>data</u> :
<br>
<br>
<div id="dash"></div>

<script src="moment-with-locales.min.js"></script>
<script src="Chart.min.js"></script>
<script src="fbbs-core-data-draw.js"></script>
<script src="react.js"></script>
<script src="react-dom.js"></script>
<script src="fbbs-std-input-widget.js"></script>

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
  document.getElementById("board_name").value = prev_cmd_val;
}

var dashUpdater = setInterval(SwitchBoard, 5000);

</script>


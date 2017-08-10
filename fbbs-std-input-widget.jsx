function FBBSStdInputWidget(props)  {
  var form_id = props.id + "_form";
  var on_click = LaunchCommand;
  if ((props.onClick != undefined) && (typeof props.onClick == "function")) {
    on_click = props.onClick;
  }
  return <div className="fbbs_std_in_widget">
      <form id={form_id}>
        {props.label}
        <input type="text" id={props.id} size="10" onClick={on_click}/>
        &nbsp;
        <input type="button" value="<=enter|" size="7"
          onClick={on_click} />
      </form>
    </div>;
}

function FBBSStdCmdLine(props) {
  return <div>
      <FBBSStdInputWidget label=": command ::: " id="launch_command"
          onClick={LaunchCommand} />
      <FBBSStdInputWidget label=": board name: " id="board_name"
          onClick={SwitchBoard} />
      <FBBSStdInputWidget label=": post data : " id="post_data"
          onClick={PostData} />
      <FBBSStdInputWidget label=": read data : " id="read_data"
          onClick={ReadData} />
    </div>;
}

function LaunchCommand() {
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


function PostData() {
  var dashName = document.getElementById("board_name").value;
  var xhttp_dashinfo;
  xhttp_dashinfo = new XMLHttpRequest();

  xhttp_dashinfo.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      SwitchBoard();
    }
  }

  xhttp_dashinfo.open("POST", "fbbs-api.php", true);
  xhttp_dashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  var data = document.getElementById("post_data");
  var username_elem = document.getElementById("username");
  if (data && data.value) {
    var curr_username = '[]';
    if (username_elem && username_elem.innerHTML) {
      curr_username = '[' + username_elem.innerHTML + ']';
    }
    var commandString = "command="+dashName+" "+data.value+" "+curr_username;
    xhttp_dashinfo.send(commandString);
    document.getElementById("post_data").value = "";
  }

  return false;
}


function ReadData() {
  var dashName = document.getElementById("board_name").value;
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

  var msgId = document.getElementById("read_data").value.trim()
  xhttp_mdashinfo.open("POST", "fbbs-api.php", true);
  xhttp_mdashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  xhttp_mdashinfo.send("command="+dashName+" @"+msgId);

  return false;
}

function getURLWithoutParams() {
  return location.pathname;
}

function SwitchBoard () {
  var dashName = document.getElementById("board_name").value;
  if (dashName) {
    showDash(dashName);
    history.pushState({}, '',
                      getURLWithoutParams() + '?command=' + dashName);
  }
}

ReactDOM.render(
  <FBBSStdCmdLine />,
  document.getElementById('reacting')
);

var fbbsGlobalCharInstance = null;

function FBBSDataDraw (ctx, title = "", type = "value_label") {
  this.ctx = ctx;
  this.ctx_2d_context = ctx.getContext("2d");
  this.title = title;
  this.type = type;
  this.xaxis_type = "category";
  this.chart_type = "line";
  this.label_type = "descent";
  this.label_font_size = "6pt";
  this.label_descent_size = 25;
  this.legend_font_size = "6pt";
  this.title_font_size = 15;
  this.title_padding = 20;
  this.chart_border_color = "rgba(200,0,100,0.9)";
  this.chart_fill_color = "rgba(150,20,200,0.6)";
  this.xaxis_display = false;
  this.xaxis_grid_color = "rgba(30,180,18,0.2)";
  this.ctx_2d_context.width = 320;
  this.ctx_2d_context.height = 180;
  this.ornate = true;
  var chart_elem = this.ctx_2d_context;
  var chart_x_max = chart_elem.width;

  var gradient = chart_elem.createLinearGradient(0,0, chart_x_max, 0);
  gradient.addColorStop("1.0","white");
  gradient.addColorStop("0.5","blue");
  gradient.addColorStop("0.25","cyan");
  gradient.addColorStop("0.15","yellow");
  gradient.addColorStop("0.0","white");

  var gradient2 = chart_elem.createLinearGradient(0,0, chart_x_max, 0);
  gradient2.addColorStop("1.0","rgba(250,250,250,0.6)");
  gradient2.addColorStop("0.5","rgba(0,0,250,0.15)");
  gradient2.addColorStop("0.25","rgba(0,250,250,0.2)");
  gradient2.addColorStop("0.15", "rgba(250,250,0,0.2)");
  gradient2.addColorStop("0.0","rgba(250,250,250,0.5)");

  this.chart_fill_color= "rgba(100,25,225,0.6)";
  this.label_text_color = gradient;
  this.label_line_color = gradient2;

  this.processDataDraw = processDataDraw;
  this.generateDataObj = generateTimeValueDataObj;
  if (type == "value_time") {
    this.generateDataObj = generateValueTimeDataObj;
    this.xaxis_type = "time";
    this.xaxis_display = true;
    this.label_type = "point";
    this.ornate = false;
  }
  if (type == "value_height_label") {
    this.generateDataObj = generateValueHeightLabelDataObj;
    this.xaxis_type = "time";
    this.label_type = "point";
    this.xaxis_display = true;
    this.ornate = false;
  }
  if (type == "value_height_label_bar") {
    this.generateDataObj = generateValueHeightLabelDataObj;
    this.xaxis_type = "category";
    this.xaxis_display = true;
    this.label_type = "point";
    this.chart_type = "bar";
    this.ornate = false;
  }
  if (type == "prev_timediff_60min") {
    this.generateDataObj = generatePrevTimeDiff60MinDataObj;
    this.xaxis_type = "category";
    this.label_type = "descent";
    this.ornate = false;
  }
}

function generateTimeValueDataObj (keyval_obj) {
  var current_time = new Date().getTime();
  var return_obj = { 
      data: "", 
      label: "", 
      html: "",
      min_timestamp: current_time,
      max_timestamp: current_time
  };
  var entry_obj = new Object();
 
  Object.keys(keyval_obj).forEach(function(key,index) {
    Object.keys(keyval_obj).forEach(function(id,idx) {
        entry_obj[id] = keyval_obj[id];
       });
    });
  var new_id = msgId(entry_obj);
  var new_value = msgValue(entry_obj).trim();
  var new_timestamp = msgTimestamp(entry_obj);
  return_obj.label = new_value;
  var timestamp_to_milli = parseInt(new_timestamp,10)*1000;
  if (timestamp_to_milli < return_obj.min_timestamp)
    return_obj.min_timestamp = timestamp_to_milli;
  if (timestamp_to_milli > return_obj.max_timestamp)
    return_obj.max_timestamp = timestamp_to_milli;
  return_obj.data = {x:  new_timestamp*1000, y: new_value.length};
  var entry_time = parseInt(msgTimestamp(entry_obj));
  var timestamp_diff = (timestamp_to_milli - current_time);
  var new_timediff = Math.abs(Math.round(timestamp_diff/6000)/10);
  return_obj.html = "|@" + new_id + "|minsago(" + new_timediff + ")-=> " +
                    new_value + "<br>";
  return_obj.label = new_value + "|minsago(" + new_timediff +")";
  return return_obj;
}

function generateValueTimeDataObj (keyval_obj) {
  var current_time = new Date().getTime();
  var return_obj = { 
      data: "", 
      label: "", 
      html: "",
      min_timestamp: current_time,
      max_timestamp: current_time
  };
  var entry_obj = new Object();
 
  Object.keys(keyval_obj).forEach(function(key,index) {
    Object.keys(keyval_obj).forEach(function(id,idx) {
        entry_obj[id] = keyval_obj[id];
       });
    });
  var new_id = msgId(entry_obj);
  var new_value = msgValue(entry_obj).trim();

  var splitted_new = new_value.split(" ");
  var new_timestamp = "";
  if ((splitted_new != undefined) && (splitted_new[0] != undefined)) {
    new_timestamp = moment(splitted_new[0]).unix();
  } 
  else {
    new_timestamp = msgTimestamp(entry_obj);
  }
  return_obj.label = new_value;
  var timestamp_to_milli = parseInt(new_timestamp,10)*1000;
  if (timestamp_to_milli < return_obj.min_timestamp)
    return_obj.min_timestamp = timestamp_to_milli;
  if (timestamp_to_milli > return_obj.max_timestamp)
    return_obj.max_timestamp = timestamp_to_milli;
  return_obj.data = {x:  new_timestamp*1000, y: new_value.length};
  var entry_time = parseInt(msgTimestamp(entry_obj));
  var timestamp_diff = (timestamp_to_milli - current_time);
  var new_timediff = Math.abs(Math.round(timestamp_diff/6000)/10);
  return_obj.html = "|@" + new_id + "|ts(" + new_timestamp*1000 + ")-=> " +
                    new_value + "<br>";
  return_obj.label = new_value;
  return return_obj;
}

function generateValueHeightLabelDataObj (keyval_obj) {
  var current_time = new Date().getTime();
  var return_obj = { 
      data: "", 
      label: "", 
      html: "",
      min_timestamp: current_time,
      max_timestamp: 0
  };
  var entry_obj = new Object();
 
  Object.keys(keyval_obj).forEach(function(key,index) {
    Object.keys(keyval_obj).forEach(function(id,idx) {
        entry_obj[id] = keyval_obj[id];
       });
    });
  var new_id = msgId(entry_obj);
  var new_value = msgValue(entry_obj).trim();
  var new_value_splitted = new_value.split(" ");
  var new_data = 0;
  if ((new_value_splitted != undefined) && 
      (new_value_splitted[0] != undefined)) {
    new_data = parseInt(new_value_splitted[0],10);
    if (new_data == undefined) {
      new_data = 0;
    }
  }
  var new_timestamp = msgTimestamp(entry_obj);
  return_obj.label = new_value;
  var timestamp_to_milli = parseInt(new_timestamp,10)*1000;
  return_obj.data = {x: timestamp_to_milli, y: new_data};
  if (timestamp_to_milli < return_obj.min_timestamp)
    return_obj.min_timestamp = timestamp_to_milli;
  if (timestamp_to_milli > return_obj.max_timestamp)
    return_obj.max_timestamp = timestamp_to_milli;
  var entry_time = parseInt(msgTimestamp(entry_obj));
  var timestamp_diff = (timestamp_to_milli - current_time);
  var new_timediff = Math.abs(Math.round(timestamp_diff/6000)/10);
  return_obj.html = "|@" + new_id + "|minsago=" + new_timediff + ")-=> " +
                    new_value + "<br>";
  return return_obj;
}

function generateValueLabelDataObj (keyval_obj) {
  var current_time = new Date().getTime();
  var return_obj = { 
      data: "", 
      label: "", 
      html: "",
      min_timestamp: current_time,
      max_timestamp: current_time
  };
  var entry_obj = new Object();
 
  Object.keys(keyval_obj).forEach(function(key,index) {
    Object.keys(keyval_obj).forEach(function(id,idx) {
        entry_obj[id] = keyval_obj[id];
       });
    });
  var new_id = msgId(entry_obj);
  var new_value = msgValue(entry_obj).trim();
  var new_timestamp = msgTimestamp(entry_obj);
  return_obj.label = new_value;
  var timestamp_to_milli = parseInt(new_timestamp,10)*1000;
  if (timestamp_to_milli < return_obj.min_timestamp)
    return_obj.min_timestamp = timestamp_to_milli;
  if (timestamp_to_milli > return_obj.max_timestamp)
    return_obj.max_timestamp = timestamp_to_milli;
  return_obj.data = new_value.length;
  var entry_time = parseInt(msgTimestamp(entry_obj));
  var timestamp_diff = (timestamp_to_milli - current_time);
  var new_timediff = Math.abs(Math.round(timestamp_diff/6000)/10);
  return_obj.html = "|@" + new_id + "|minsago=" + new_timediff + ")-=> " +
                    new_value + "<br>";
  return return_obj;
}

function generatePrevTimeDiff60MinDataObj( keyval_obj) {
  var current_time = new Date().getTime();
  var return_obj = { 
      data: "", 
      label: "", 
      html: "",
      min_timestamp: current_time,
      max_timestamp: current_time
  };
  var entry_obj = new Object();
 
  Object.keys(keyval_obj).forEach(function(key,index) {
    Object.keys(keyval_obj).forEach(function(id,idx) {
        entry_obj[id] = keyval_obj[id];
       });
    });
  var new_id = msgId(entry_obj);
  var new_value = msgValue(entry_obj).trim();
  var new_timestamp = msgTimestamp(entry_obj);
  return_obj.label = new_value;
  var timestamp_to_milli = parseInt(new_timestamp,10)*1000;
  if (timestamp_to_milli < return_obj.min_timestamp)
    return_obj.min_timestamp = timestamp_to_milli;
  if (timestamp_to_milli > return_obj.max_timestamp)
    return_obj.max_timestamp = timestamp_to_milli;
  var entry_time = parseInt(msgTimestamp(entry_obj));
  var timestamp_diff = (timestamp_to_milli - current_time);
  var new_timediff = Math.abs((Math.round(timestamp_diff/6000))/10);
  return_obj.data = (6000 - (new_timediff*100)) + new_value.length;
  return_obj.html = "|@" + new_id + "|minsago=" + new_timediff + ")-=> " +
                    new_value + "<br>";
  return return_obj;
}



function processDataDraw( input_json ) {
  var data_array = [];
  var label_array = [];
  var dashHtml = "";
  var min_timestamp = new Date().getTime();
  var max_timestamp = 0;
  var current_time = min_timestamp/1000;
  var previous_time = current_time;

  try {
    var jsonresponseparsed = JSON.parse(input_json);
  } catch(err) {
    return;
  }

  if ((jsonresponseparsed == undefined) ||
      (jsonresponseparsed.value == undefined) ||
      (jsonresponseparsed.value[0] == undefined)) return;

  var jsonresponseobj = jsonresponseparsed.value[0];

  var dataProcessor = this.generateDataObj;
  Object.keys(jsonresponseobj).forEach(function(key,index) {
    var array_obj = jsonresponseobj[key];
    var raw_obj = {}
    array_obj.forEach(function addObj (var_obj) { 
        Object.keys(var_obj).forEach(function(vkey,vindex) {
            raw_obj[vkey] = var_obj[vkey];
          });
      });
    var data_obj = dataProcessor(raw_obj);
    data_array.push(data_obj.data);
    label_array.push(data_obj.label);
    dashHtml += data_obj.html;
    if (data_obj.min_timestamp < min_timestamp) {
      min_timestamp = data_obj.min_timestamp;
    }
    if (data_obj.min_timestamp > max_timestamp) {
      max_timestamp = data_obj.max_timestamp;
    }
   });

   var min_moment = moment(min_timestamp);
   var max_moment = moment(max_timestamp);

   var background_fill_color = this.chart_fill_color;
   
   if (this.chart_type == "bar") {
     var background_fill_colors = [];
     if (data_array.length > 0) {
       var color_selector = 0;
       var current_color = "rgba(50,100,200,0.9)";
       for (var i = 0; i < data_array.length; i++) {
         color_selector = i%3;
         switch (color_selector) {
           case 0:
             current_color = "rgba(150,25,200,9)";
             break;
           case 1:
             current_color = "rgba(200,190,20,0.9)";
             break;
           case 2:
             current_color = "rgba(100,150,195,0.9)";
             break; 
           default:
             break;
          }
         background_fill_colors.push(current_color);
       }
       background_fill_color = background_fill_colors;
     }
   }

  var dataStruct = {
    labels: label_array,
    datasets: [
      {
        label: this.title,
        type: this.chart_type,
        data : data_array,
        fill: true,
        fillColor: "rgba(0,0,125,0.5)",
        borderColor: this.chart_border_color,
        backgroundColor: background_fill_color,
        pointStyle: "rectRot",
        pointRadius: 5,
        pointBackgroundColor: "rgba(40,200,170,0.9)",
        borderWidth: 2,
      }],
    };

  var label_text_color = this.label_text_color;
  var label_line_color = this.label_line_color;
  var label_type = this.label_type;
  var xaxis_type = this.xaxis_type;
  var xaxis_display = this.xaxis_display;
  var xaxis_grid_color = this.xaxis_grid_color;
  var draw_ornate = this.ornate;
  var label_font_size = this.label_font_size;
  var label_descent_size = this.label_descent_size;
  var legend_font_size = this.legend_font_size;
  var title_text = this.title;
  var title_font_size = this.title_font_size;
  var title_padding = this.title_padding;

  var chart_options = {
           responsive: true,
           maintainAspectRatio: true,
           animation: {
               duration: 10,
               onComplete: function () {
                   var ctx = this.chart.ctx;
                   ctx.font = "monospace",
                   ctx.fillStyle = "rgba(200, 220, 20, 0.6)";
                   ctx.strokeColor = "rgba(200,220, 20, 0.9)";
                   var chart_elem = document.getElementById("dashChart");
                   var chart_x_max = chart_elem.width;
                   var chart_y_max = chart_elem.height;
                   var current_data;
                   var current_color = "rgb(0,0,0)";
                   var clean_x_div = 1.0;
                   var clean_y_div = 1.0;
                   this.data.datasets.forEach(function (dataset) {
                       var current_x = 0;
                       var current_y = 0;
                       ctx.fillStyle=label_text_color;
                       ctx.strokeStyle="rgba(255,255,255,0.3)";
                       var text_wid_pix = 0;
                       var text_height_pix = 0;
                       var prev_x = 0;
                       var prev_y = 0;
                       var prev_point_y = 0;
                       var prev_point_x = 0;
                       Object.keys(dataset._meta).forEach(function (key,idx) {
                           var j=0;
                           ctx.font = label_font_size + " monospace";
                           dataset._meta[key].data.forEach(function (p_obj) {
                               current_y = Math.max(p_obj._model.y,
                                                    25);
                               if (label_type == "descent") {
                                 current_y = (chart_y_max)  - 
                                             (chart_y_max - ((j+3) * 
                                             label_descent_size));
                               }
                               text_wid_pix = ctx.measureText(label_array[j]);
                               current_x = p_obj._model.x;
                               if (current_x <
                                   (chart_x_max+50)) {
                                 current_x = Math.min(current_x,
                                     chart_x_max-(text_wid_pix.width));
                                 if (draw_ornate) {
                                   ctx.beginPath();
                                   ctx.moveTo(prev_x, prev_y);
                                   ctx.lineTo(p_obj._model.x + 1,
                                              p_obj._model.y * 2);
                                   ctx.strokeStyle=label_line_color;
                                   ctx.stroke();
                                   ctx.moveTo(prev_point_x, prev_point_y);
                                   ctx.lineTo(p_obj._model.x,
                                              p_obj._model.y);
                                   ctx.stroke();
                                   ctx.moveTo(p_obj._model.x, p_obj._model.y);
                                   ctx.lineTo(prev_x, prev_y);
                                   ctx.stroke();
                                   ctx.fillStyle=label_text_color;
                                   ctx.fillRect(p_obj._model.x, p_obj._model.y,
                                                1,
                                                Math.abs(p_obj._model.y));
                                 }
                                 prev_x = p_obj._model.x + 1;
                                 prev_y = p_obj._model.y * 2;
                                 prev_point_x = p_obj._model.x;
                                 prev_point_y = p_obj._model.y;
                               }
                               j++;
                             });
                         });
                       });
                     }
                   },
               title: { 
                   display: true,
                   text: title_text,
                   fontStyle: "bold",
                   fontSize: title_font_size,
                   padding: title_padding,
                   fontColor: "rgba(190,250,220,0.9)",
                 },
               legend: {
                   display: false,
                   position: 'top',
                   fontSize: legend_font_size,
                   labels: {
                       showScaleLabels: true,
                       usePointStyle: true,
                     },
                   reverse: false
                 },
               scales: {
                  xAxes: [{
                      type: xaxis_type,
                      time: {
                          max: max_moment,
                          min: min_moment
                      },
                      display: xaxis_display,
                      gridLines: {
                          color: xaxis_grid_color,
                          borderDash: [5, 5],
                          display: xaxis_display,
                          offsetGridLines: false
                        },
                      position: "bottom",
                      fontSize: legend_font_size,
                      ticks: {
                        fontColor: "rgba(0,250,0,0.9)",
                        fontFamily: "monospace",
                        mirror: false,
                        display: true
                      }
                  }],
                  yAxes: [{
                    display: true,
                    position: "left",
                    gridLines: {
                        display: true,
                        lineWidth: 3,
                      },
                    ticks: {
                      fontColor: "rgba(250,250,0,0.8)",
                      fontFamily: "monospace",
                      min: 0,
                      max: 100
                    },
                  }]
                }
              };

 
  var old_graph = null; 
  if (fbbsGlobalCharInstance != null) {
    old_graph = fbbsGlobalCharInstance;
    old_graph.destroy();
    old_graph = null;
  }

  var new_node_2d_context = this.ctx_2d_context;
  var new_graph =  new Chart(new_node_2d_context, {
      type: "bar",
      data: dataStruct,
      options: chart_options
    });
  fbbsGlobalCharInstance = new_graph;

  document.getElementById("dash").innerHTML = 
    "<span class=\"data_output\">" + dashHtml + "</span>";
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

function fbbsUpdateBoardInfo(str) {
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
      var data_html = "";
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
        data_html += entry_output + "<br>";
      });
      document.getElementById("board_info").innerHTML = "<span " +
        "class=\"data_output\">" + data_html + "</span>";
    }
  }
  xhttp_dashinfo.open("POST", "fbbs-api.php", true);
  xhttp_dashinfo.setRequestHeader("Content-type",
                                  "application/x-www-form-urlencoded");
  xhttp_dashinfo.send("command="+str+" @");

  document.getElementById("board_name").textContent = str;
}

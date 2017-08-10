<!DOCTYPE html>
<html>
<head>
<style>
body, input {
    font-family: monospace;
    background-color: black;
    color: green;
    font-size: large;
}

input {
    border-top-width: 1px;
    border-bottom-width: 1px;
    border-left-width: 0px;
    border-right-width: 0px;
    border-color: green;
    outline-width: 1px;
    outline-width-left: 0px;
    outline-width-right: 0px;
    outline-color: green;
}

</style>
</head>

<body>
<?php
  $_LOCAL_API_CALLS = 1;
?>

<p>
|\______
<br>
--------->>>>
<br>
[o]----[o]---=>>>>>>>
<br>
-------------->>>>>>>>>
<br>
****************************
<br>
** Fury's Fortress (fbbs) **
<br>
**------------------------**
<br>
****************************
<br>
<FORM NAME="form1" METHOD="POST" ACTION="fbbs-login-submit.php">
username:
<INPUT TYPE="Text" VALUE="" id="username" NAME="username" SIZE="40" autofocus>
<br>
password:
<INPUT TYPE="Text" VALUE="" id="password" NAME="password" SIZE="40" autofocus>
<br>
password again (if new user):
<INPUT TYPE="Text" VALUE="" id="password-again" NAME="password-again" SIZE="40" autofocus>
<div class="userpassDiv" />
<input type="submit" style="display:none" />
</FORM>
</p>

</body>
</html>

<?php
  class FDBUSER extends SQLite3
  {
    function __construct()
    {
      $this->open('fbbs-user.db');
    }
  }

  function authorize_user() {
    $userauthorized = FALSE;
    $username = $_COOKIE['username'];
    $token = $_COOKIE['authToken'];
    if (($username != "") && ($token != "")) {
      $fdbuser = new FDBUSER();
      if (!$fdbuser) {
        echo $fdbuser->lastErrorMsg();
      }
      $username = $fdbuser->escapeString($username);
      $auth_query = 'SELECT token FROM auth_tokens where username = "' .
                    $username . '"';
      $auth_result = $fdbuser->query($auth_query);
      if (!empty($auth_result)) {
        $auth_array = $auth_result->fetchArray(SQLITE3_ASSOC);
        $auth_encoded = $auth_array['token'];
        if (!empty($auth_encoded)) {
          if (password_verify($token, $auth_encoded)) {
            $userauthorized = TRUE;
          }
        }
      }
    }
    if ($userauthorized) {
      $request_time = time();
      $auth_access_insert = 'INSERT INTO user_auth_log ' .
                            '(username, token, timestamp) VALUES ' .
                            '("' . $username . '", "' . $auth_encoded .
                            '", ' . $request_time . ')';
      $fdbuser->exec($auth_access_insert);
      return $username;
    }
    else {
      header("Location: index.php");
    }
    return FALSE;
  }

  function current_user_id() {
    $return_id = "-1";
    $username = authorize_user();
    if ($username) {
      $fdbuser = new  FDBUSER();
      if (!$fdbuser) {
       echo $fdbuser->lastErrorMsg();
      }
      else {
        $user_id_query = 'SELECT id, username FROM "users" where username = "'.
                         $username . '" LIMIT 1';
        $id_result = $fdbuser->query($user_id_query);
        if (!empty($id_result)) {
          $id_result_array = $id_result->fetchArray(SQLITE3_ASSOC);
          if ($id_result_array) {
            $return_id = $id_result_array["id"];
          }
        }
      }
    }
    return $return_id;
  }

  function last_auth_user() {
    $return_string = "";
    $fdbuser = new FDBUSER();
    if (!$fdbuser) {
     echo $fdbuser->lastErrorMsg();
    }
    else {
      $cleanusername = "";
      if (!empty($_COOKIE['username'])) {
        $cleanusername = $fdbuser->escapeString($_COOKIE['username']);
      }
      $last_auth_query = 'SELECT username, timestamp FROM user_auth_log ' .
                         'WHERE username != "'. $cleanusername .
                         '" ORDER BY timestamp DESC LIMIT 1';
      $last_auth_result = $fdbuser->query($last_auth_query);
      if (!empty($last_auth_result)) {
        $result_array = $last_auth_result->fetchArray(SQLITE3_ASSOC);
        $return_string = $result_array['username'];
      }
    }
    return $return_string;
  }

?>

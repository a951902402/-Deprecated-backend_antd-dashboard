<?php
require_once('./mysql/ConnSql.php');
function tologin($auth)
{
  $flag = false;
  $sequence = 0;
  $token = ['code' => 500, 'token' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $change = "userID=''";
  $condition = "";
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while($sequence < $rows && !$flag) {
    $uif = $Sql->sqlarray($result);
    $tokencode = $uif['Token'];
    if(strcmp($auth, substr($tokencode, 10, 10)) == 0) {
      $token['code'] = 200;
      $token['token'] = $tokencode;
      $flag = true;
    }
    $sequence++;
  }
  $Sql->update($tableName, $change, '');
  $Sql->dbClose();
  return json_encode($token);
}

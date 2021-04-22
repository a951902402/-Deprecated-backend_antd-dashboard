<?php
require_once('./mysql/ConnSql.php');
function confirmpoll($user)
{
  $status = [];
  $sequence = 0;
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $condition = "WHERE username='$user' AND userID=''";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $uif = $Sql->sqlarray($result);
    $status['token'] = $uif['Token'];
    $status['username'] = $uif['username'];
    $status['code'] = 200;
    $sequence++;
  }
  if($sequence == 0) {
    $status['code'] = 404;
  }
  $Sql->dbClose();
  return json_encode($status);
}
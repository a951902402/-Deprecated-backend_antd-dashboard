<?php
require_once('./mysql/ConnSql.php');
function scanpoll()
{
  $status = [];
  $sequence = 0;
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $condition = "WHERE userID='scanned'";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $status['username'] = $Sql->sqlarray($result)['username'];
    $status['code'] = 200;
    $sequence++;
  }
  if($sequence == 0) {
    $status['code'] = 404;
  }
  $Sql->dbClose();
  return json_encode($status);
}

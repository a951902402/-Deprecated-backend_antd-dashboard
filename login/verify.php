<?php
require_once('./mysql/ConnSql.php');
function verify($verifytoken) {
  $sequence = 0;
  $flag = false;
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $condition = '';
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while($sequence < $rows && !$flag) {
    $currenttoken = $Sql->sqlarray($result)['Token'];
    if($verifytoken == md5(substr($currenttoken, 10, 10))) {
      $flag = true;
    }
    $sequence++;
  }
  $Sql->dbClose();
  if($flag) {
    return 200;
  }
  else {
    return 403;
  }
}
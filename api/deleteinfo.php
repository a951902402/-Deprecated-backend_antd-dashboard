<?php
require_once('./mysql/ConnSql.php');
function deleteinfo($deviceID)
{
  $deleteinfo = ['code' => 500, 'data' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'device';
  $condition = "WHERE deviceID = '$deviceID'";
  $result = $Sql->delete($tableName, $condition);
  if($result) {
    $deleteinfo['code'] = 200;
  }
  else {
    $deleteinfo['code'] = 200;
  }
  return json_encode($deleteinfo);
  $Sql->dbClose();
}
<?php
require_once('./mysql/ConnSql.php');
function changeinfo($deviceID, $devName, $devIP)
{
  $changeinfo = ['code' => 500, 'data' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'device';
  $change = "hostname = '$devName', mgmtIP = '".ip2long($devIP)."'";
  $condition = "WHERE deviceID = '$deviceID'";
  $result = $Sql->update($tableName, $change, $condition);
  $result = $Sql->select($tableName, $condition);
  $data = $Sql->sqlarray($result);
  $Sql->dbClose();
  if($result) {
    $changeinfo['code'] = 200;
    $changeinfo['data'] = $data;
  }
  else {
    $changeinfo['code'] = 500;
  }
  return json_encode($changeinfo);
}
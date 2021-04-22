<?php
require_once('./mysql/ConnSql.php');
function info($deviceid)
{
  $info = ['code' => 404, 'data' => ''];
  $flag = 0;
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $condition = "WHERE deviceID = '$deviceid'";
  //cpu表
  $tableName = 'cpu';
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  if($rows) {
    $data['cpu'] = $Sql->sqlarray($result);
    $flag++;
  }
  //memory表
  $tableName = 'memory';
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  if($rows) {
    $data['memory'] = $Sql->sqlarray($result);
    $flag++;
  }
  //storage表
  $tableName = 'storage';
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  if($rows) {
    $data['storage'] = $Sql->sqlarray($result);
    $flag++;
  }
  //nic表
  $tableName = 'nic';
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  if($rows) {
    $data['nic'] = $Sql->sqlarray($result);
    $flag++;
  }
  if($flag) {
    $info['code'] = 200;
    $info['data'] = $data;
  }
  $Sql->dbClose();
  return json_encode($info);
}

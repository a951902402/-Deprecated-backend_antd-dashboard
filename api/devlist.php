<?php
require_once('./mysql/ConnSql.php');
function devlist()
{
  $devlist = ['code' => 404, 'data' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $sequence = 0;
  $tableName = 'device';
  $condition = "";
  //device表
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $data[$sequence] = $Sql->sqlarray($result);
    $data[$sequence] = array_merge(['id' => $sequence], $data[$sequence]);
    $data[$sequence]['mgmtIP'] = long2ip(intval($data[$sequence]['mgmtIP']));
    $sequence++;
  }
  if($sequence) {
    $devlist['code'] = 200;
    $devlist['data'] = $data;
  }
  $Sql->dbClose();
  return json_encode($devlist);
}

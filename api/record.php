<?php
require_once('./mysql/ConnSql.php');
function forinjection($str)
{
  $pattern = '/\d+/';
  if ($n = preg_match_all($pattern, $str, $arr)) {
    $str = implode($arr[0]);
  }
  return $str;
}
function record($deviceid, $socket, $slot, $storageID, $nicID)
{
  //防注入
  $socket = forinjection($socket);
  $slot = forinjection($slot);
  $storageID = forinjection($storageID);
  $nicID = forinjection($nicID);
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $record = ['code' => 404, 'data' => ''];
  $flag = 0;
  //cpu_rec表
  $sequence = 0;
  $tableName = 'cpu_rec';
  $condition = "WHERE deviceID = '$deviceid' AND socket = '$socket' limit 500";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $data['record']['cpu'][$sequence] = $Sql->sqlarray($result);
    $data['record']['cpu'][$sequence]['cpu_rec_id'] = strtotime($data['record']['cpu'][$sequence]['cpu_rec_id']) * 1000;
    $sequence++;
  }
  $data['count']['cpu'] = $sequence;
  //memory_rec表
  $sequence = 0;
  $tableName = 'memory_rec';
  $condition = "WHERE deviceID = '$deviceid' AND slot = '$slot' limit 500";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $data['record']['memory'][$sequence] = $Sql->sqlarray($result);
    $data['record']['memory'][$sequence]['memory_rec_id'] = strtotime($data['record']['memory'][$sequence]['memory_rec_id']) * 1000;
    $sequence++;
  }
  $data['count']['memory'] = $sequence;
  //storage_rec表
  $sequence = 0;
  $tableName = 'storage_rec';
  $condition = "WHERE deviceID = '$deviceid' AND storageID = '$storageID' limit 500";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $data['record']['storage'][$sequence] = $Sql->sqlarray($result);
    $data['record']['storage'][$sequence]['storage_rec_id'] = strtotime($data['record']['storage'][$sequence]['storage_rec_id']) * 1000;
    $sequence++;
  }
  $data['count']['storage'] = $sequence;
  //nic_rec表
  $sequence = 0;
  $tableName = 'nic_rec';
  $condition = "WHERE deviceID = '$deviceid' AND nicID = '$nicID' limit 500";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  while ($sequence < $row) {
    $data['record']['nic'][$sequence] = $Sql->sqlarray($result);
    $data['record']['nic'][$sequence]['nic_rec_id'] = strtotime($data['record']['nic'][$sequence]['nic_rec_id']) * 1000;
    $sequence++;
  }
  $data['count']['nic'] = $sequence;
  foreach($data['count'] as $value) {
    $flag += $value;
  }
  if($flag) {
    $record['code'] = 200;
    $record['data'] = $data;
  }
  $Sql->dbClose();
  return json_encode($record);
}

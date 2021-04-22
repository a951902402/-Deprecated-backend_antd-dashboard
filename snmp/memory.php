<?php
require_once('../mysql/ConnSql.php');
$sequence = 0;
$devlist = [];
$Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
$tableName = 'device';
$condition = 'WHERE connectable = 1';
$result = $Sql->select($tableName, $condition);
$row = $Sql->rows($result);
while ($sequence < $row) {
  $currentdev = $Sql->sqlarray($result);
  $devlist[$sequence]['deviceID'] = $currentdev['deviceID'];
  $devlist[$sequence]['mgmtIP'] = long2ip(intval($currentdev['mgmtIP']));
  //memory_rec
  $sequence_mem = 0;
  $tableName_mem = 'memory';
  $condition_mem = "WHERE deviceID = '" . $devlist[$sequence]['deviceID'] . "'";
  $result_mem = $Sql->select($tableName_mem, $condition_mem);
  $rows_mem = $Sql->rows($result_mem);
  while ($sequence_mem < $rows_mem) {
    $currentmem = $Sql->sqlarray($result_mem);
    $devlist[$sequence]['memory'][$sequence_mem] = ['slot' => $currentmem['slot']];
    $mem_used = str_replace("INTEGER: ", "", snmpwalk($devlist[$sequence]['mgmtIP'], 'public', "hrStorageUsed")[0]);
    $mem_per = ceil($mem_used / str_replace(" KBytes", "", $currentmem['capacity']) * 100);
    $now = date("Y-m-d H:i:s", time());
    $tableName_mem_rec = 'memory_rec';
    $fields_mem_rec = '(memory_rec_id,deviceID,slot,usedCol,cap_usage)';
    $values_mem_rec = "('$now','".$currentmem['deviceID']."','".$currentmem['slot']."','$mem_used','$mem_per')";
    $Sql->insert($tableName_mem_rec, $fields_mem_rec, $values_mem_rec);
    $sequence_mem++;
  }
  $sequence++;
}
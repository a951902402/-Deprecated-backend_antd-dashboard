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
  // cpu_rec
  $sequence_cpu = 0;
  $tableName_cpu = 'cpu';
  $condition_cpu = "WHERE deviceID = '" . $devlist[$sequence]['deviceID'] . "'";
  $result_cpu = $Sql->select($tableName_cpu, $condition_cpu);
  $rows_cpu = $Sql->rows($result_cpu);
  while ($sequence_cpu < $rows_cpu) {
    $currentcpu = $Sql->sqlarray($result_cpu);
    $devlist[$sequence]['cpu'][$sequence_cpu] = ['socket' => $currentcpu['socket']];
    $cpu_load = str_replace("INTEGER: ", "", snmpget($devlist[$sequence]['mgmtIP'], 'public', "hrProcessorLoad.".$currentcpu['socket']));
    $now = date("Y-m-d H:i:s", time());
    $tableName_cpu_rec = 'cpu_rec';
    $fields_cpu_rec = '(cpu_rec_id,deviceID,socket,cap_usage)';
    $values_cpu_rec = "('$now','".$currentcpu['deviceID']."','".$currentcpu['socket']."','$cpu_load')";
    $Sql->insert($tableName_cpu_rec, $fields_cpu_rec, $values_cpu_rec);
    $sequence_cpu++;
  }
  $sequence++;
}
$Sql->dbClose();

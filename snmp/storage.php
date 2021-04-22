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
  //storage_rec
  $sequence_sto = 0;
  $tableName_sto = 'storage';
  $condition_sto = "WHERE deviceID = '" . $devlist[$sequence]['deviceID'] . "'";
  $result_sto = $Sql->select($tableName_sto, $condition_sto);
  $rows_sto = $Sql->rows($result_sto);
  while ($sequence_sto < $rows_sto) {
    $currentsto = $Sql->sqlarray($result_sto);
    $devlist[$sequence]['storage'][$sequence_sto] = ['storageID' => $currentsto['storageID']];
    $sto_used_arr = snmpwalk($devlist[$sequence]['mgmtIP'], 'public', "hrStorageUsed");
    $sto_used = 0;
    foreach($sto_used_arr as $sto_used_key => $sto_used_value) {
      $sto_used_arr[$sto_used_key] = str_replace("INTEGER: ", "", $sto_used_value);
      if($sto_used_key > 5) {
        $sto_used += $sto_used_arr[$sto_used_key];
      }
    }
    $sto_per = ceil($sto_used / str_replace(" KBytes", "", $currentsto['capacity']) * 100);
    $now = date("Y-m-d H:i:s", time());
    $tableName_sto_rec = 'storage_rec';
    $fields_sto_rec = '(storage_rec_id,deviceID,storageID,usedCol,cap_usage)';
    $values_sto_rec = "('$now','".$currentsto['deviceID']."','".$currentsto['storageID']."','$sto_used','$sto_per')";
    $Sql->insert($tableName_sto_rec, $fields_sto_rec, $values_sto_rec);
    $sequence_sto++;
  }
  $sequence++;
}
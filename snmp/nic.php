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
  //nic
  $sequence_nic = 0;
  $tableName_nic = 'nic';
  $condition_nic = "WHERE deviceID = '" . $devlist[$sequence]['deviceID'] . "'";
  $result_nic = $Sql->select($tableName_nic, $condition_nic);
  $rows_nic = $Sql->rows($result_nic);
  while ($sequence_nic < $rows_nic) {
    $currentnic = $Sql->sqlarray($result_nic);
    $devlist[$sequence]['nic'][$sequence_nic] = ['nicID' => $currentnic['nicID']];
    $nic_in1 = str_replace("Counter32: ", "", snmpget($devlist[$sequence]['mgmtIP'], 'public', "ifInOctets.".$currentnic['nicID']));
    $nic_out1 = str_replace("Counter32: ", "", snmpget($devlist[$sequence]['mgmtIP'], 'public', "ifOutOctets.".$currentnic['nicID']));
    sleep(1);
    $nic_in2 = str_replace("Counter32: ", "", snmpget($devlist[$sequence]['mgmtIP'], 'public', "ifInOctets.".$currentnic['nicID']));
    $nic_out2 = str_replace("Counter32: ", "", snmpget($devlist[$sequence]['mgmtIP'], 'public', "ifOutOctets.".$currentnic['nicID']));
    $nic_in = $nic_in2 - $nic_in1;
    $nic_out = $nic_out2 - $nic_out1;
    $now = date("Y-m-d H:i:s", time());
    $tableName_nic_rec = 'nic_rec';
    $fields_nic_rec = '(nic_rec_id,deviceID,nicID,upload,download)';
    $values_nic_rec = "('$now','".$currentnic['deviceID']."','".$currentnic['nicID']."','$nic_out','$nic_in')";
    $Sql->insert($tableName_nic_rec, $fields_nic_rec, $values_nic_rec);
    $sequence_nic++;
  }
  $sequence++;
}
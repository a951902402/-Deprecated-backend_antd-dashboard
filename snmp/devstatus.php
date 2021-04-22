<?php
require_once('../mysql/ConnSql.php');
$sequence = 0;
$devlist = [];
$Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
$tableName = 'device';
$condition = 'WHERE connectable = 0';
$result = $Sql->select($tableName, $condition);
$row = $Sql->rows($result);
while ($sequence < $row) {
  $currentdev = $Sql->sqlarray($result);
  $devlist[$sequence]['deviceID'] = $currentdev['deviceID'];
  $devlist[$sequence]['mgmtIP'] = long2ip(intval($currentdev['mgmtIP']));
  $sequence++;
}
/* print_r($devlist); */
foreach ($devlist as $key => $value) {
  $hostname = snmpwalk($value['mgmtIP'], 'public', "SysName");
  if ($hostname[0] == false) {
    $devlist[$key]['connectable'] = 0;
  } else {
    $devlist[$key]['hostname'] = str_replace("STRING: ", "", $hostname[0]);
    $devlist[$key]['connectable'] = 1;
  }
}
/* print_r($devlist); */
foreach ($devlist as $value) {
  if ($value['connectable']) {
    $change = "connectable = 1, hostname = '" . $value['hostname'] . "'";
    $condition = "WHERE deviceID = '" . $value['deviceID'] . "'";
    $Sql->update($tableName, $change, $condition);
  }
}
$Sql->dbClose();

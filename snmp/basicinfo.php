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
  $devlist[$sequence]['cpu'] = [];
  $devlist[$sequence]['memory'] = [];
  $devlist[$sequence]['storage'] = [];
  $devlist[$sequence]['nic'] = [];
  $sequence++;
}

// cpu表
foreach ($devlist as $key => $value) {
  $hrDeviceType = snmpwalk($value['mgmtIP'], 'public', "hrDeviceType");
  $hrDeviceIndex = snmpwalk($value['mgmtIP'], 'public', "hrDeviceIndex");
  foreach ($hrDeviceType as $Typekey => $Typevalue) {
    if ($Typevalue == 'OID: HOST-RESOURCES-TYPES::hrDeviceProcessor') {
      $devlist[$key]['cpu'][$Typekey] = ['socket' => str_replace('INTEGER: ', '', $hrDeviceIndex[$Typekey])];
    }
  }
  $hrDeviceDescr = snmpwalk($value['mgmtIP'], 'public', "hrDeviceDescr");
  foreach($hrDeviceDescr as $cpukey => $cpuvalue) {
    $hrDeviceDescr[$cpukey] = str_replace('STRING: ', '', $cpuvalue);
  }
  foreach($devlist[$key]['cpu'] as $cpukey => $cpuvalue) {
    $cpuinfoarray = explode(' @ ', $hrDeviceDescr[$cpukey]);
    $devlist[$key]['cpu'][$cpukey]['model'] = $cpuinfoarray[0];
    $devlist[$key]['cpu'][$cpukey]['baseFrequency'] = $cpuinfoarray[1];
  }
}

// memory表
foreach ($devlist as $key => $value) {
  $devlist[$key]['memory'][0]['slot'] = 1;
  $devlist[$key]['memory'][0]['model'] = str_replace('STRING: ', '', snmpwalk($value['mgmtIP'], 'public', "hrStorageDescr")[0]);
  $devlist[$key]['memory'][0]['capacity'] = str_replace('INTEGER: ', '', snmpwalk($value['mgmtIP'], 'public', "hrMemorySize")[0]);
}

// storage表
foreach ($devlist as $key => $value) {
  $storageIndex = [];
  $hrStorageType = snmpwalk($value['mgmtIP'], 'public', "hrStorageType");
  foreach($hrStorageType as $stokey => $stovalue) {
    if($stovalue == 'OID: HOST-RESOURCES-TYPES::hrStorageFixedDisk') {
      array_push($storageIndex, $stokey);
    }
  }
  $hrStorageAllocationUnits = snmpwalk($value['mgmtIP'], 'public', "hrStorageAllocationUnits");
  $hrStorageSize = snmpwalk($value['mgmtIP'], 'public', "hrStorageSize");
  $hrStorageAllSize = 0;
  foreach($storageIndex as $Indexvalue) {
    $hrStorageAllocationUnits[$Indexvalue] = str_replace(['INTEGER: ', ' Bytes'], '', $hrStorageAllocationUnits[$Indexvalue]) / 1024;
    $hrStorageSize[$Indexvalue] = str_replace('INTEGER: ', '', $hrStorageSize[$Indexvalue]) * $hrStorageAllocationUnits[$Indexvalue];
    $hrStorageAllSize += $hrStorageSize[$Indexvalue];
  }
  $devlist[$key]['storage'][0]['storageID'] = 1;
  $devlist[$key]['storage'][0]['model'] = 'hrStorageFixedDisk';
  $devlist[$key]['storage'][0]['capacity'] = $hrStorageAllSize.' KBytes';
}

//nic表
foreach ($devlist as $key => $value) {
  $ifIndex = snmpwalk($value['mgmtIP'], 'public', "ifIndex");
  $ifType = snmpwalk($value['mgmtIP'], 'public', "ifType");
  foreach($ifType as $Typekey => $Typevalue) {
    if($Typevalue == 'INTEGER: ethernetCsmacd(6)') {
      $devlist[$key]['nic'][$Typekey] = ['nicID' => str_replace('INTEGER: ', '', $ifIndex[$Typekey])];
    }
  }
  $ifDescr = snmpwalk($value['mgmtIP'], 'public', "ifDescr");
  foreach($ifDescr as $ifkey => $ifvalue) {
    $ifDescr[$ifkey] = str_replace('STRING: ', '', $ifvalue);
  }
  $ifPhysAddress = snmpwalk($value['mgmtIP'], 'public', "ifPhysAddress");
  foreach($ifPhysAddress as $ifkey => $ifvalue) {
    $ifPhysAddress[$ifkey] = str_replace('STRING: ', '', $ifvalue);
  }
  foreach($devlist[$key]['nic'] as $nickey => $nicvalue) {
    $devlist[$key]['nic'][$nickey]['nic_name'] = $ifDescr[$nickey];
    $devlist[$key]['nic'][$nickey]['model'] = 'ethernetCsmacd(6)';
    $devlist[$key]['nic'][$nickey]['mac_addr'] = $ifPhysAddress[$nickey];
    $devlist[$key]['nic'][$nickey]['ip_addr'] = $value['mgmtIP'];
    $devlist[$key]['nic'][$nickey]['speed'] = '1000Mbps';
  }
}
/* $devlist = [
  0 => [
    'deviceID' => '1556072813-4294967295',
    'mgmtIP' => '127.0.0.1',
    'cpu' => [
      0 => [
        'socket' => 196608,
        'model' => 'GenuineIntel: Intel(R) Xeon(R) CPU E5-2682 v4',
        'baseFrequency' => '2.50GHz',
      ],
    ],
    'memory' => [
      0 => [
        'slot' => 1,
        'model' => 'Physical memory',
        'capacity' => '1882236 KBytes',
      ],
    ],
    'storage' => [
      0 => [
        'storageID' => 1,
        'model' => 'hrStorageFixedDisk',
        'capacity' => '44159044 KBytes',
      ],
    ],
    'nic' => [
      1 => [
        'nicID' => 2,
        'nic_name' => 'eth0',
        'model' => 'ethernetCsmacd(6)',
        'mac_addr' => '0:16:3e:8:66:84',
        'ip_addr' => '127.0.0.1',
        'speed' => '1000Mbps',
      ],
    ],
  ],
]; */
/* print_r($devlist); */
//update or insert
//cpu
foreach ($devlist as $value) {
  $sequence = 0;
  $tableName = 'cpu';
  $condition = "WHERE deviceID = '" . $value['deviceID'] . "'";
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while ($sequence < $rows) {
    $rowarray = $Sql->sqlarray($result);
    foreach ($value['cpu'] as $cpuvalue) {
      if ($cpuvalue['socket'] == $rowarray['socket']) {
        $change = "model = '" . $cpuvalue['model'] . "', baseFrequency = '" . $cpuvalue['baseFrequency'] . "'";
        $Sql->update($tableName, $change, $condition);
      } else {
        $fields = "(socket,deviceID,model,baseFrequency,cap_timeStep)";
        $values = "('" . $cpuvalue['socket'] . "','" . $value['deviceID'] . "','" . $cpuvalue['model'] . "','" . $cpuvalue['baseFrequency'] . "',10)";
        $Sql->insert($tableName, $fields, $values);
      }
    }
    $sequence++;
  }
  if ($rows == 0) {
    foreach ($value['cpu'] as $cpuvalue) {
      $fields = "(socket,deviceID,model,baseFrequency,cap_timeStep)";
      $values = "('" . $cpuvalue['socket'] . "','" . $value['deviceID'] . "','" . $cpuvalue['model'] . "','" . $cpuvalue['baseFrequency'] . "',10)";
      $Sql->insert($tableName, $fields, $values);
    }
  }
}
//memory
foreach ($devlist as $value) {
  $sequence = 0;
  $tableName = 'memory';
  $condition = "WHERE deviceID = '" . $value['deviceID'] . "'";
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while ($sequence < $rows) {
    $rowarray = $Sql->sqlarray($result);
    foreach ($value['memory'] as $memvalue) {
      if ($memvalue['slot'] == $rowarray['slot']) {
        $change = "model = '" . $memvalue['model'] . "', capacity = '" . $memvalue['capacity'] . "'";
        $Sql->update($tableName, $change, $condition);
      } else {
        $fields = "(slot,deviceID,model,capacity,cap_timeStep)";
        $values = "('" . $memvalue['slot'] . "','" . $value['deviceID'] . "','" . $memvalue['model'] . "','" . $memvalue['capacity'] . "',10)";
        $Sql->insert($tableName, $fields, $values);
      }
    }
    $sequence++;
  }
  if ($rows == 0) {
    foreach ($value['memory'] as $memvalue) {
      $fields = "(slot,deviceID,model,capacity,cap_timeStep)";
      $values = "('" . $memvalue['slot'] . "','" . $value['deviceID'] . "','" . $memvalue['model'] . "','" . $memvalue['capacity'] . "',10)";
      $Sql->insert($tableName, $fields, $values);
    }
  }
}
//storage
foreach ($devlist as $value) {
  $sequence = 0;
  $tableName = 'storage';
  $condition = "WHERE deviceID = '" . $value['deviceID'] . "'";
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while ($sequence < $rows) {
    $rowarray = $Sql->sqlarray($result);
    foreach ($value['storage'] as $stovalue) {
      if ($stovalue['storageID'] == $rowarray['storageID']) {
        $change = "model = '" . $stovalue['model'] . "', capacity = '" . $stovalue['capacity'] . "'";
        $Sql->update($tableName, $change, $condition);
      } else {
        $fields = "(storageID,deviceID,model,capacity,cap_timeStep)";
        $values = "('" . $stovalue['storageID'] . "','" . $value['deviceID'] . "','" . $stovalue['model'] . "','" . $stovalue['capacity'] . "',3600)";
        $Sql->insert($tableName, $fields, $values);
      }
    }
    $sequence++;
  }
  if ($rows == 0) {
    foreach ($value['storage'] as $stovalue) {
      $fields = "(storageID,deviceID,model,capacity,cap_timeStep)";
      $values = "('" . $stovalue['storageID'] . "','" . $value['deviceID'] . "','" . $stovalue['model'] . "','" . $stovalue['capacity'] . "',3600)";
      $Sql->insert($tableName, $fields, $values);
    }
  }
}
//nic
foreach ($devlist as $value) {
  $sequence = 0;
  $tableName = 'nic';
  $condition = "WHERE deviceID = '" . $value['deviceID'] . "'";
  $result = $Sql->select($tableName, $condition);
  $rows = $Sql->rows($result);
  while ($sequence < $rows) {
    $rowarray = $Sql->sqlarray($result);
    foreach ($value['nic'] as $nicvalue) {
      if ($nicvalue['nicID'] == $rowarray['nicID']) {
        $change = "nic_name = '" . $nicvalue['nic_name'] . "', model = '" . $nicvalue['model'] . "', mac_addr = '" . $nicvalue['mac_addr'] . "', ip_addr = '" . $nicvalue['ip_addr'] . "', speed = '" . $nicvalue['speed'] . "'";
        $Sql->update($tableName, $change, $condition);
      } else {
        $fields = "(nicID,deviceID,nic_name,model,mac_addr,ip_addr,speed)";
        $values = "('" . $nicvalue['nicID'] . "','" . $value['deviceID'] . "','" . $nicvalue['nic_name'] . "','" . $nicvalue['model'] . "','" . $nicvalue['mac_addr'] . "','" . $nicvalue['ip_addr'] . "','" . $nicvalue['speed'] . "')";
        $Sql->insert($tableName, $fields, $values);
      }
    }
    $sequence++;
  }
  if ($rows == 0) {
    foreach ($value['nic'] as $nicvalue) {
      $fields = "(nicID,deviceID,nic_name,model,mac_addr,ip_addr,speed)";
      $values = "('" . $nicvalue['nicID'] . "','" . $value['deviceID'] . "','" . $nicvalue['nic_name'] . "','" . $nicvalue['model'] . "','" . $nicvalue['mac_addr'] . "','" . $nicvalue['ip_addr'] . "','" . $nicvalue['speed'] . "')";
      $Sql->insert($tableName, $fields, $values);
    }
  }
}
$Sql->dbClose();

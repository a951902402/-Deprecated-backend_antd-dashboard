<?php
require_once('./mysql/ConnSql.php');
function post($ip, $hostname, $factory, $OS, $community)
{
  if(!$ip) {
    return false;
  }
  $ip = sprintf("%u", ip2long($ip));
  $deviceID = time().'-'.$ip;
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'device';
  $fields = "(deviceID,displayname,hostname,factory,OS,mgmtIP,community)";
  $value = "('$deviceID','$hostname','-','$factory','$OS','$ip','$community')";
  $result = $Sql->insert($tableName, $fields, $value);
  $Sql->dbClose();
}

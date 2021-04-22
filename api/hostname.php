<?php
function hostname($addr) {
  $hostname = ['result' => '', 'hostname' => ''];
  $name = snmpget($addr,'public',".1.3.6.1.2.1.1.5.0");
  if($name == false) {
    $hostname['result'] = "No response from $addr";
  }
  else {
    $hostname['hostname'] = str_replace("STRING: ","",$name);
    $hostname['result'] = $name;
  }
  return json_encode($hostname);
}
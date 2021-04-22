<?php
/*//Allow Cross-Origin-Resource-Sharing from access of Axios in React
header("Access-Control-Allow-Origin: http://localhost:8000");
$result_flag = 0;
require("./ConnSql.php");
$ConnSql = new ConnSql("localhost", "root", "", "dashboard");
$select = $ConnSql->select('device', '');
while ($result = $ConnSql->sqlarray($select)){
  $json[$result_flag] = $result;
  $result_flag++;
}
echo json_encode($json);
$dbClose = $ConnSql->dbClose();*/
/*
  /* Dashboard Server Information script
  /* ------------------------------
   */
  // Predefine character
  $info = array();
  $ip = "2.2.2.2";
  $community = "public";
  // SNMP Function
  function get_OS_information()
  {
    global $info;
    global $ip;
    global $community;
    $OS_information = snmpget($ip,$community,".1.3.6.1.2.1.1.1.0");
    $info["OS_information"] = str_replace("STRING: ","",$OS_information);
  }
  function get_up_time()
  {
    global $info;
    global $ip;
    global $community;
    $up_time = snmpget($ip,$community,"1.3.6.1.2.1.25.1.1");
    $info["up_time"] = str_replace("Timeticks: ","",$up_time);
  }
  /*function get_package_lost_load()
  {
    global $snmp;
    global $ip;
    global $community;
    $ip
    $snmp["Package_Lost"] = mt_rand(0, 100);
  }*/
  function get_process_num()
  {
    global $info;
    global $ip;
    global $community;
    $process_num = snmpget($ip,$community,"1.3.6.1.2.1.25.1.6.0");
    $info["process_num"] = str_replace("Gauge32: ","",$process_num);
  }
  

  
  // Execute functions
  get_OS_information();
  get_up_time();
  get_process_num();
  // get_package_lost_load();

  
  // Print to Front Side
  // print_r($snmp);
  echo json_encode($info);
  
?>
<?php
require_once('./mysql/ConnSql.php');
function login($username, $password)
{
  $token = ['token' => '', 'username' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $condition = "WHERE username = '$username'";
  $result = $Sql->select($tableName, $condition);
  $uif = $Sql->sqlarray($result);
  if ($password == $uif['password']) {
    $token['token'] = md5($uif['WechatID'] . time() . $uif['username'] . $uif['password']);
    $token['username'] = $uif['username'];
  }
  $change = "Token = '".$token['token']."'";
  $result = $Sql->update($tableName, $change, $condition);
  return json_encode($token);
}

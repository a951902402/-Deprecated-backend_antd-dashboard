<?php
require_once('./mysql/ConnSql.php');
function userinfo($token) {
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $userinfo = [];
  $tableName = 'users';
  $condition = "WHERE Token='$token'";
  $result = $Sql->select($tableName, $condition);
  $allinfo = $Sql->sqlarray($result);
  $userinfo['username'] = $allinfo['username'];
  $userinfo['wxAvatar'] = $allinfo['wxAvatar'];
  $userinfo['wxNickname'] = $allinfo['wxNickname'];
  $Sql->dbClose();
  return json_encode($userinfo);
}
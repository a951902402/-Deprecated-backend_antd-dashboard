<?php
require_once('./mysql/ConnSql.php');
function initqrcode()
{
  $id = time();
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $change = "userID='$id'";
  $condition = '';
  $Sql->update($tableName, $change, $condition);
  $Sql->dbClose();
  return $id;
}

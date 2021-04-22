<?php
require_once('./mysql/ConnSql.php');
function fingerprint($raw, $wxid, $avatar, $nickname)
{
  $param = '123456';
  $code = ['code' => 404, 'auth' => ''];
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  if (strcmp($param, $raw) === 0) {
    $tableName = 'users';
    $condition = "WHERE WechatID='$wxid'";
    $result = $Sql->select($tableName, $condition);
    $row = $Sql->rows($result);
    if (!$row) {
      //用户不存在
      $code['code'] = 404;
    } else {
      $uif = $Sql->sqlarray($result);
      $token = md5($uif['WechatID'] . $param . $uif['username'] . $uif['password']);
      $change = "token='$token', wxAvatar='$avatar', wxNickname='$nickname'";
      $result = $Sql->update($tableName, $change, $condition);
      if ($result) {
        //查询OK
        $code['auth'] = substr($token, 10, 10);
        $code['code'] = 200;
      } else {
        //状态更改失败
        $code['code'] = 500;
      }
    }
  } else {
    //未授权请求
    $code['code'] = 403;
  }
  $Sql->dbClose();
  return json_encode($code);
}

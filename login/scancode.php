<?php
require_once('./mysql/ConnSql.php');
function scancode($id, $wxid, $avatar, $nickname)
{
  $code = ['code' => 0, 'auth' => ''];
  if(time() < $id || strlen($id) > 14 || !is_numeric($id)) {
    //未识别的id
    $code['code'] = 401;
    return json_encode($code);
  }
/*   if (time() - $id > 63) {
    //登录超时
    $code['code'] = 403;
    return json_encode($code);
  } */
  $Sql = new Sql("localhost", "root", "antd-dashboard", "antd-dashboard");
  $tableName = 'users';
  $change = "userID='scanned'";
  $condition = "WHERE WechatID='$wxid'";
  $result = $Sql->select($tableName, $condition);
  $row = $Sql->rows($result);
  if (!$row) {
    //用户不存在
    $code['code'] = 404;
    $Sql->dbClose();
    return json_encode($code);
  }
  else {
    $uif = $Sql->sqlarray($result);
    $token = md5($uif['WechatID'].$uif['userID'].$uif['username'].$uif['password']);
    $change = "$change, Token='$token', wxAvatar='$avatar', wxNickname='$nickname'";
  }
  $result = $Sql->update($tableName, $change, $condition);
  if ($result) {
    //查询OK
    $code['auth'] = substr($token, 10, 10);
    $code['code'] = 200;
    $Sql->dbClose();
    return json_encode($code);
  } else {
    //状态更改失败
    $code['code'] = 500;
    $Sql->dbClose();
    return json_encode($code);
  }
}

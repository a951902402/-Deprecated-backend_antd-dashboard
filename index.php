<?php
$allow_origin = array(
  'https://localhost:8000',
  'https://call-pizza.xyz:8000',
);
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (isset($_SERVER['HTTP_REFERER'])) {
  $reg = "/(https:\/\/servicewechat.com\/)+/";
  $referer = preg_match($reg, $_SERVER['HTTP_REFERER']);
}
if (in_array($origin, $allow_origin)) {
  header('Access-Control-Allow-Origin:' . $origin);
}
if ($origin == '' && !$referer) {
  echo 404;
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: token");
if (isset($_GET['login'])) {
  switch ($_GET['login']) {
    case 'login':
      require_once('./login/login.php');
      echo login($_POST['username'], $_POST['password']);
      break;
    case 'initqrcode':
      require_once('./login/initqrcode.php');
      echo initqrcode();
      break;
    case 'scanpoll':
      require_once('./login/scanpoll.php');
      echo scanpoll();
      break;
    case 'confirmpoll':
      require_once('./login/confirmpoll.php');
      echo confirmpoll($_GET['user']);
      break;
    case 'getwxid':
      require_once('./login/getwxid.php');
      echo getwxid($_GET['code']);
      break;
    case 'scancode':
      require_once('./login/scancode.php');
      echo scancode($_GET['id'], $_GET['wxid'], $_GET['avatar'], $_GET['nickname']);
      break;
    case 'fingerprint':
      require_once('./login/fingerprint.php');
      echo fingerprint($_GET['param'], $_GET['wxid'], $_GET['avatar'], $_GET['nickname']);
      break;
    case 'tologin':
      require_once('./login/tologin.php');
      echo tologin($_GET['auth']);
      break;
    case 'verify':
      require_once('./login/verify.php');
      echo verify($_GET['verifytoken']);
      break;
    default:
      echo 'developing...';
      break;
  }
  return;
}
$section = @$_GET['section'];
switch ($section) {
  case 'userinfo':
    require_once('./api/userinfo.php');
    echo userinfo($_GET['token']);
    break;
  case 'info':
    require_once('./api/info.php');
    echo info($_GET['deviceid']);
    break;
  case 'rec':
    require_once('./api/record.php');
    echo record($_GET['id'], $_GET['socket'], $_GET['slot'], $_GET['stoID'],  $_GET['nicID']);
    break;
  case 'deleteinfo':
    require_once('./api/deleteinfo.php');
    echo deleteinfo($_GET['deviceID']);
    break;
  case 'changeinfo':
    require_once('./api/changeinfo.php');
    echo changeinfo($_GET['deviceID'], $_GET['devName'], $_GET['devIP']);
    break;
  case 'devlist':
    require_once('./api/devlist.php');
    echo devlist();
    break;
  case 'ping':
    require_once('./api/ping.php');
    echo ping($_GET['ip']);
    break;
  case 'hostname':
    require_once('./api/hostnmae.php');
    echo hostname($_GET['ip']);
    break;
  case 'post':
    require_once('./api/post.php');
    echo post($_POST['ip'], $_POST['hostname'], $_POST['factory'], $_POST['OS'], $_POST['community']);
    break;
    // for test
  case 'sessid':
    require_once('./api/test.php');
    echo test();
    break;
  default:
    echo 'developing...';
    break;
}

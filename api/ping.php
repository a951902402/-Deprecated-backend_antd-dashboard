<?php
function ping($addr)
{
  $status = -1;
  if (strcasecmp(PHP_OS, 'WINNT') === 0) {
    //Windows
    $ping = exec("ping -n 5 {$addr}", $outcome, $status);
  } else if (strcasecmp(PHP_OS, 'Linux') === 0) {
    //Linux
    $ping = exec("ping -c 5 {$addr}", $outcome, $status);
  }
  $status ? array_push($outcome,'false') : array_push($outcome,'true');
  return json_encode($outcome);
}
?>

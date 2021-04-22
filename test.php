<?php
$tableName = 'users';
$condition = "WHERE wechatID='olAXU5ENDPeY4FyO1ZjzVtweFq0U'";
$conn = mysqli_connect("localhost", "root", "", "antd-dashboard") or die("connect fail!" . mysqli_error($conn));
$sql = "SELECT * FROM $tableName $condition";
echo $sql."<br>";
$result = mysqli_query($conn, $sql);
var_dump($result);
mysqli_close($conn);
/* echo strtotime('2019-04-16 08:23:19');
echo strtotime('2019-04-16 08:23:29'); */
 
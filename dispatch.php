<?php
require_once "routine.php";
#move to index.php. use javascript.

$conn = startConn();
# login.
login($_POST["username"],$_POST["password"],$conn);
echo "您已登录；点击<a href=\"./index.php\">这里</a>返回首页。";
$conn->close();
?>
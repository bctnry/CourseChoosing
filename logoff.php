<?php
require_once "routine.php";

$conn1 = startConn();
logoff($_COOKIE["session_id"],$conn1);
$conn1->close();
echo "您已登出；点击<a href=\"./index.php\">此处</a>返回首页。";
?>
<html>
    <head>
        <title>用了PHP+MySQL的选课系统</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
      <div class="header"><h1>🎕用了PHP+MySQL的选课系统</h1><br />
	<?php
	  require_once "./routine.php";
	  $session_id = $_COOKIE["session_id"];
	  $userID = getUserID($session_id);
	  $usertype = getUsertype($session_id); $c = startConn();
	  if(!$_COOKIE["session_id"]) echo "You are not logged in.";
	  elseif(!ifLoginHashExists($session_id,$c)) {
	      echo "YOU ARE NOT LOGGED IN.";
	  } else {
	      echo '选课中，学生信息：' . $userID . " " . getStudentDept($userID) . " " . getStudentName($userID);
	  } $c->close(); ?></div>
        <div class="nav"><a href="course_choosing.php">选课首页</a> |
        <a href="caution.html">注意事项</a> |
        <a href="logoff.php">注销当前登录信息</a> |
        <a href="account_settings.php">账号设置</a></div>
        <div class="body">
	  <?php
	    
	    ?>
        </div>
        <div class="footnote">&copy; BCT 2017</div>
    </body>
</html>

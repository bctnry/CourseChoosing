<html>
    <head>
        <title>用了PHP+MySQL的选课系统</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
      <div class="header"><h1>🎕用了PHP+MySQL的选课系统</h1>
	<?php
	  require_once "./routine.php";
          if(!$_COOKIE["session_id"]) {
	  echo "You are not logged in.";
	  echo '</body></html>'; die();
	  }
	  $session_id = $_COOKIE["session_id"];
	  $userID = getUserID($session_id);
	  $usertype = getUserType($session_id);
	  echo '您已登录，用户账户：' . getUserID($session_id) .
	             '，账户类型：' . getUserType($session_id);
	  
	    ?>
      </div>
      <div class="nav">
	<?php
	  if($usertype == "superadmin" || $usertype == "admin")
	  echo '<a href="admin.php">管理首页</a>';
	  else echo '<a href="course_choosing.php">选课首页</a>'; ?> |
        <a href="caution.html">注意事项</a> |
        <a href="./logoff.php">注销当前登录信息</a></div>
      <div class="body">
	<?php
	  if(!$_POST["oldpassword"] && !$_POST["newpassword"]) ;
	  else {
	  if(!password_verify($_POST["oldpassword"],retrUserHash_singl($userID))) {
	  echo "密码错误。请重新输入。";
	  } else {
	  updateUserHash_singl($userID,calcHash($_POST["newpassword"]));
	  echo "密码更新完成。";
	  }}?>
        <table class="login_form"><form method="POST">
            <tr><td class="input_form_left">旧密码：</td>
              <td class="input_form_right"><input type="password" name="oldpassword" /></td></tr>
            <tr><td class="input_form_left">新密码：</td>
                 <td class="input_form_right"><input type="password" name="newpassword" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="确认更改" name="submit" id="login_submit" /></td></tr>
            </form>
        </table>	
        <div class="footnote">&copy; BCT 2017</div>
        </div>
    </body>
</html>

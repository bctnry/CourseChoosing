<html>
    <head>
        <title>用了PHP+MySQL的选课系统</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <?php require "routine.php"; ?>
        <div class="header"><h1>🎕用了PHP+MySQL的选课系统</h1>
	  <?php
	    $session_id = $_COOKIE["session_id"];
	    $userID = ""; $usertype = "";
	    if($_COOKIE["session_id"]) {
	        $userID = getUserID($session_id);
	        $usertype = getUserType($session_id);
	        echo '您已登录，用户账户：' . getUserID($session_id) .
	             '，账户类型：' . getUserType($session_id);
	    }
	    ?></div>
        <div class="nav"><a href="index.php">首页</a> |
        <a href="caution.html">注意事项</a> |
        <a href="logoff.php">注销当前登录信息</a></div>
        <div class="body">
	  <?php
	    if(!$_COOKIE["session_id"]) {
	    echo '
        <table class="login_form"><form method="POST" action="dispatch.php">
            <tr><td class="input_form_left">用户ID：</td>
                <td class="input_form_right"><input type="text" name="username" /></td></tr>
            <tr><td class="input_form_left">密码：</td>
                 <td class="input_form_right"><input type="password" name="password" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="登录" name="login_submit" id="login_submit" /></td></tr>
            </form>
        </table>';
	} else {
	    if ($usertype == "superadmin" || $usertype == "admin") {
	        echo "您现在是管理员；请点击<a href=\"./admin.php\">这里</a>进行管理。";
	    } else {
	        echo "您现在是学生；请点击<a href=\"./course_choosing.php\">这里</a>进行选课。";
	    }
	}
	?>
        <div class="footnote">&copy; BCT 2017</div>
        </div>
    </body>
</html>

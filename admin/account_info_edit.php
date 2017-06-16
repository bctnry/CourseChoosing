<div class="sidebar">
  <ul>
    <li><a href="./admin.php?mode=AInfo&submode=NewA">新增账号</a></li>
    <li><a href="./admin.php?mode=AInfo&submode=AInfo">查询/修改账号</a></li>
  </ul>
</div>
<div class="main" id="main">
<?php
  if($_REQUEST["submode"]) {
    if(strcmp($_REQUEST["submode"],"NewA")==0) {
      echo '
        <table>
	  <form method="POST" action="./admin.php">
	    <input type="hidden" name="mode" value="AInfo" />
	    <input type="hidden" name="submode" value="NewA" />
	    <input type="hidden" name="action" value="NewA" />
	    <tr><td>用户名：</td><td><input type="text" name="new_username" /></td></tr>
	    <tr><td>用户类型：</td><td>
	        <select name="new_usertype">
		  <option value="student">学生</option>
		  <option value="admin">管理员</option>
		  <option value="superadmin">超级管理员</option>
		</select>
		</td>
		</tr>
            <tr><td>密码：</td><td><input type="password" name="new_password" /></td></tr>
	    <tr><td colspan="2"><input type="submit" /></td></tr>
	  </form>
	</table>
      ';
    } elseif(strcmp($_REQUEST["submode"],"AInfo")==0) {
      if(!$_REQUEST["usrID"]) {
        echo '
          <table>
	    <tr><form action="./admin.php" method="GET">
	      <input type="hidden" name="mode" value="AInfo" />
	      <input type="hidden" name="submode" value="AInfo" />
	      <td><input type="text" name="f_id" /></td>
	      <td><select name="f_usertype">
	        <option value="student">学生</option>
                <option value="admin">管理员</option>
	        <option value="superadmin">超级管理员</option>
	        </select></td>
  	      <td><input type="submit" value="搜索" /></td></tr>
        ';
        $c = startConn();
        $s = prepareSQL($c,"SELECT * FROM Usr");
        $s->execute();
        $s->bind_result($usrID,$usrType,$usrHash);
        while($s->fetch()) {
          if($_REQUEST["f_id"]&&strcmp($_REQUEST["f_id"],$usrID)!=0) continue;
	  if($_REQUEST["f_usertype"]&&strcmp($_REQUEST["f_usertype"],$usrType)!=0) continue;
          echo '
	    <tr><td>' . $usrID . '</td>
	    <td>' . $usrType . '</td>
	    <td><form action="./admin.php" method="GET">
	      <input type="hidden" name="mode" value="AInfo" />
	      <input type="hidden" name="submode" value="AInfo" />
	      <input type="hidden" name="usrID" value="' . $usrID . '" />
	      <input type="submit" value="修改" />
	      </form>
	      </td></tr>
	  ';
        }
      } else {
        $usrID = $_REQUEST["usrID"];
	$c = startConn();
	$s = prepareSQL($c,"SELECT * FROM Usr WHERE Usr.ID = '" . $usrID . "'");
	$s->execute();
	$s->bind_result($usrID,$usrType,$usrHash);
	$s->fetch();
	$s->close();
	$c->close();
	echo '
	  <form action="./admin.php" method="GET">
	    <input type="hidden" name="mode" value="AInfo" />
	    <input type="hidden" name="submode" value="AInfo" />
	    <input type="hidden" name="action" value="ChangeAInfo" />
	    <input type="hidden" name="usrID" value="' . $usrID . '" />
	    <table>
	      <tr><td>用户名：</td><td>' . $usrID . '</td><td></td>
	      <tr><td>用户类型：</td><td>' . $usrType . '</td><td>
	        <select name="new_usertype">
		  <option value="student">学生</option>
		  <option value="admin">管理员</option>
		  ' . (strcmp(getUsertype($_COOKIE["session_id"]),"superadmin")==0?
		       '<option value="superadmin">超级管理员</option>'
		       :"") . '
		</select></td>
              <tr><td>密码：</td><td></td><td><input type="password" name="new_password" /></td></tr>
	      <tr><td colspan="3"><input type="submit" /></td></tr>
	    </table>
	  </form>
	';
      }
  }
}
?>
</div>
<html>
    <head>
        <title>用了PHP+MySQL的选课系统</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script src="./admin.js"></script>
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
	      echo "YOU ARE NOT LOGGED IN."; $c->close();
	} else {
	  echo '
        管理中，管理员ID：' . $userID . '，权限：' . $usertype ;
	} ?></div>
      <?php if(!$_COOKIE["session_id"] || !($usertype == "superadmin" || $usertype == "admin")) {
	    echo '
        <div class="hline" />
        <div class="footnote">&copy; BCT 2017</div>
    </body>
</html> '; die(""); } ?>
        <div class="nav"><a href="./admin.php">管理首页</a> |
        <a href="caution.html">注意事项</a> |
        <a href="logoff.php">注销当前登录信息</a> |
        <a href="account_settings.php">账号设置</a></div>
        <div class="body">
            <div class="sidebar">
            <ul>
                <li><a href="./admin.php?mode=StInfo&submode=StInfo">学生信息修改</a></li>
                <li><a href="./admin/course_info_edit.php">课程信息修改</a></li>
                <li><a href="./admin.php?mode=ClInfo&submode=ClInfo">教室信息修改</a></li>
                <li><a href="./admin.php?mode=TInfo&submode=TInfo">教师信息修改</a></li>
                <li><a href="./admin.php?mode=AInfo&submode=AInfo">账号信息修改</a></li>
                <li><a href="./admin/announcement_edit.php">公告设置</a></li>
            </ul>
            </div>
            <div class="main" id="main">
	      <?php
		if(!$_REQUEST["action"]) ;
		else {
		    $action = $_REQUEST["action"];

                    # new user.
		    if(strcmp($action,"NewA")==0) {
		      regUsr_singl($_REQUEST["new_username"],calcHash($_REQUEST["new_password"],$_REQUEST["new_usertype"]));
		      echo 'done.';
		    }

                    # edit user info.
		    if(strcmp($action,"ChangeAInfo")==0) {
		      $c = startConn();
		      if($_REQUEST["new_usertype"]) {
		        $s = prepareSQL($c,"CALL UPDATE_USER_TYPE(?,?)");
			$s->bind_param('ss',$_REQUEST["usrID"],$_REQUEST["new_usertype"]);
			$s->execute();
		      }
		      if($_REQUEST["new_password"]) {
		        $v = calcHash($_REQUEST["new_password"]);
			$s = prepareSQL($c,"CALL UPDATE_USER_HASH(?,?)");
			$s->bind_param('ss',$_REQUEST["usrID"],$v);
			$s->execute();
		      }
		      echo 'done.';
		    }

                    # new classroom.
		    if(strcmp($action,"NewClassroom")==0) {
		        if((!$_REQUEST["building"]&&!$_REQUEST["new_building"]) ||  (!$_REQUEST["new_room_num"]) || !$_REQUEST["new_room_capacity"]) die("Command not fully applied.");
			$room_building = ($_REQUEST["new_building"]?$_REQUEST["new_building"]:$_REQUEST["building"]);
			newClassroom($room_building,$_REQUEST["new_room_num"],intval($_REQUEST["new_room_capacity"]));
			echo "教室新建完成。";
		    }

                    # new student.
		    if(strcmp($action,"NewSt")==0) {
		      $c = startConn();
		      $s = prepareSQL($c,"CALL INSERT_STUDENT(?,?,?,?)");
		      $s->bind_param('sssi',$_REQUEST["new_id"],$_REQUEST["new_name"],$_REQUEST["new_dept"],intval($_REQUEST["new_year"]));
		      $s->execute();
		      $s->close();
		      $c->close();
		      echo "done.";
		    }

                    # edit student info.
		    if(strcmp($action,"ChangeStInfo")==0) {
		      if((!$_REQUEST["original_id"])||strcmp($_REQUEST["original_id"],"")==0) die("COmmand not fully applied.");
		      $c = startConn();
		      if(!strcmp($_REQUEST["new_name"],"")==0) {
		        $s = prepareSQL($c,"CALL UPDATE_ST_NAME(?,?)");
			$s->bind_param('ss',$_REQUEST["original_id"],$_REQUEST["new_name"]);
			$s->execute();
			$s->close();
		      } if(!strcmp($_REQUEST["new_dept"],"")==0) {
		        $s = prepareSQL($c,"CALL UPDATE_ST_DEPT(?,?)");
			$s->bind_param('ss',$_REQUEST["original_id"],$_REQUEST["new_dept"]);
			$s->execute();
			$s->close();
	              } if(!strcmp($_REQUEST["new_register_year"],"")==0) {
		        $s = prepareSQL($c,"CALL UPDATE_ST_REG_YEAR(?,?)");
			$s->bind_param('si',$_REQUEST["original_id"],$_REQUEST["new_register_year"]);
			$s->execute();
			$s->close();
		      }
	              $c->close();
		      echo "学生信息修改完成。";
                    }

                    # new teacher.
		    if(strcmp($action,"NewT")==0) {
		      $c = startConn();
		      $s = prepareSQL($c,"CALL INSERT_INSTRUCTOR(?,?,?,?,?,?)");
		      $s->bind_param('sssiss',$_REQUEST["new_id"],$_REQUEST["new_name"],$_REQUEST["new_sex"],intval($_REQUEST["new_age"]),$_REQUEST["new_dept"],$_REQUEST["new_rank"]);
		      $s->execute();
		      $s->close();
		      $c->close();
		      echo "done.";
		    }

                    # edit instructor info
		    if(strcmp($action,"ChangeTInfo")==0) {
		      if((!$_REQUEST["original_id"])||strcmp($_REQUEST["original_id"],"")==0) die("Command not fully applied.");
		      $c = startConn();
		      if(!strcmp($_REQUEST["new_name"],"")==0)
		        updateTName($_REQUEST["original_id"],$_REQUEST["new_name"],$c);
		      if(!strcmp($_REQUEST["new_sex"],"")==0)
		        updateTSex($_REQUEST["original_id"],$_REQUEST["new_sex"],$c);
		      if(!strcmp($_REQUEST["new_age"],"")==0)
		        updateTAge($_REQUEST["original_id"],$_REQUEST["new_age"],$c);
		      if(!strcmp($_REQUEST["new_dept"],"")==0)
		        updateTDept($_REQUEST["original_id"],$_REQUEST["new_dept"],$c);
		      if(!strcmp($_REQUEST["new_rank"],"")==0)
		        updateTRank($_REQUEST["original_id"],$_REQUEST["new_rank"],$c);
	              updateTIsLeft($_REQUEST["original_id"],
		        (strcmp($_REQUEST["new_is_left"],"T")==0?1:0),$c);
                      $c->close();
		      echo "教师信息修改完成。";
		    }
	          
		
		}
		if(!$_REQUEST["mode"]) ;
		else {
		if(strcmp($_REQUEST["mode"],"ClInfo")==0) require "./admin/classroom_info_edit.php";
		if(strcmp($_REQUEST["mode"],"StInfo")==0) require "./admin/student_info_edit.php";
		if(strcmp($_REQUEST["mode"],"TInfo")==0) require "./admin/teacher_info_edit.php";
		if(strcmp($_REQUEST["mode"],"CoInfo")==0) require "./admin/course_info_edit.php";
		if(strcmp($_REQUEST["mode"],"AInfo")==0) require "./admin/account_info_edit.php";
		}
		?>
            </div>
        </div>
        <div class="hline" />
        <div class="footnote">&copy; BCT 2017</div>
    </body>
</html>

<div class="sidebar">
  <ul>
    <li><a href="./admin.php?mode=StInfo&submode=NewSt">新增学生信息</a></li>
    <li><a href="./admin.php?mode=StInfo&submode=StInfo">查询/修改学生信息</a></li>
  </ul>
</div>
<div class="main" id="main">
<?php
if($_REQUEST["submode"]) {
if(strcmp($_REQUEST["submode"],"NewSt")==0) {
echo '
<table>
  <form method="GET" action="./admin.php">
    <input type="hidden" name="mode" value="StInfo" />
    <input type="hidden" name="submode" value="NewSt" />
    <input type="hidden" name="action" value="NewSt" />
    <tr><td>学号：</td><td><input type="text" name="new_id" /></td></tr>
    <tr><td>姓名：</td><td><input type="text" name="new_name" /></td></tr>
    <tr><td>所属院系：</td><td><input type="text" name="new_dept" /></td></tr>
    <tr><td>注册年份：</td><td><input type="text" name="new_year" /></td></tr>
    <tr><td colspan="2"><input type="submit" /></tr>
  </form>
</table>
';
} elseif(strcmp($_REQUEST["submode"],"StInfo")==0) {
if(!$_GET["studentID"]) {
echo '
  <table class="st_info">
  <form action="./admin.php" mode="GET">
  <input type="hidden" name="mode" value="StInfo" />
  <tr><td><input type="text" name="f_id" width="20%" /></td>
  <td><input type="text" name="f_name" /></td>
  <td><input type="text" name="f_dept" /></td>
  <td><input type="text" name="f_year" /></td>
  <td><input type="submit" value="搜索" /></td>
  </tr>
  </form>
  <tr><td>ID</td><td>名字</td><td>所属院系</td><td>注册年份</td><td></td></tr>
';
$c = startConn();
$stmt = prepareSQL($c,"SELECT * FROM Student");
$stmt->execute();
$stmt->bind_result($studentID,$studentName,$studentDept,$studentRegisterYear);
$i = 0;
if(!$_GET["page"] || strcmp($_GET["page"],"1")==0) { $min = 0; $max = 24; }
else { $min = intval($_GET["page"])*25; $max = $min + 24; }
while($stmt->fetch()) {
  if($_GET["f_id"]&&strcmp($_GET["f_id"],"")!=0&&(!strcmp($_GET["f_id"],$studentID)==0)) continue;
  if($_GET["f_name"]&&(strcmp($_GET["f_name"],"")!=0)&&(!strcmp($_GET["f_name"],$studentName)==0)) continue;
  if($_GET["f_dept"]&&(strcmp($_GET["f_dept"],"")!=0)&&(!strcmp($_GET["f_dept"],$studentDept)==0)) continue;
  if($_GET["f_year"]&&(strcmp($_GET["f_year"],"")!=0)&&(!intval($_GET["f_year"])==$studentRegisterYear)) continue;
  if($i < $min || $i > $max) { $i = $i + 1; continue; }
  echo '<tr><td>' . $studentID . '</td>';
  echo '<td>' . $studentName . '</td>';
  echo '<td>' . $studentDept . '</td>';
  echo '<td>' . $studentRegisterYear . '</td>';
  echo
       '<td><form action="./admin.php" mode="GET">
        <input type="hidden" name="mode" value="StInfo" />
	<input type="hidden" name="submode" value="StInfo" />
        <input type="hidden" name="studentID" value="' . $studentID . '" />
        <input type="submit" value="详细信息" />
        </form></td></tr>';
  $i = $i + 1;
}
echo '
</table>
';
} else {
  $studentID = $_GET["studentID"];
  $c = startConn(); $s = prepareSQL($c,"SELECT * FROM Student WHERE Student.ID = ?");
  $s->bind_param('s',$studentID);
  $s->execute();
  $s->bind_result($studentID,$studentName,$studentDept,$studentRegisterYear);
  $s->fetch();
  $s->close();
  $c->close();
echo '
  <form action="./admin.php" method="GET">
  <input type="hidden" name="mode" value="StInfo" />
  <input type="hidden" name="action" value="ChangeStInfo" />
  <input type="hidden" name="original_id" value="' . $studentID . '" />
  <table>
  <tr><td>学号：</td><td>' . $studentID . '</td><td></tr>
  <tr><td>名字：</td><td>' . $studentName . '</td><td><input type="text" name="new_name" /></td></tr>
  <tr><td>所属院系：</td><td>' . $studentDept . '</td><td><input type="text" name="new_dept" /></td></tr>
  <tr><td>注册年份：</td><td>' . $studentRegisterYear . '</td><td><input type="text" name="new_register_year" /></td></tr>
  <tr><td colspan="3"><input type="submit" value="修改" /></td></tr>';
  
}
}
}

?>
</div>
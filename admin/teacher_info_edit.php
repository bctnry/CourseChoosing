<div class="sidebar">
  <ul>
    <li><a href="./admin.php?mode=TInfo&submode=NewT">新增教师信息</a></li>
    <li><a href="./admin.php?mode=TInfo&submode=TInfo">查询/修改教师信息</a></li>
  </ul>
</div>
<div class="main" id="main">
<?php
if($_REQUEST["submode"]) {
if(strcmp($_REQUEST["submode"],"NewT")==0) {
echo '
<table>
  <form method="GET" action="./admin.php">
    <input type="hidden" name="mode" value="TInfo" />
    <input type="hidden" name="submode" value="NewT" />
    <input type="hidden" name="action" value="NewT" />
    <tr><td>工号：</td><td><input type="text" name="new_id" /></td></tr>
    <tr><td>姓名：</td><td><input type="text" name="new_name" /></td></tr>
    <tr><td>性别：</td><td><input type="text" name="new_sex" /></td></tr>
    <tr><td>年龄：</td><td><input type="text" name="new_age" /></td></tr>
    <tr><td>所属院系：</td><td><input type="text" name="new_dept" /></td></tr>
    <tr><td>职称：</td><td><input type="text" name="new_rank" /></td></tr>
    <tr><td colspan="2"><input type="submit" /></td></tr>
  </form>
</table>
';
} elseif(strcmp($_REQUEST["submode"],"TInfo")==0) {
if(!$_REQUEST["instructorID"]) {
echo '
  <table>
  <form action="./admin.php" mode="GET">
  <input type="hidden" name="mode" value="TInfo" />
  <input type="hidden" name="submode" value="TInfo" />
  <tr><td><input type="text" name="f_id" /></td>
  <td><input type="text" name="f_name" /></td>
  <td><input type="text" name="f_sex" /></td>
  <td><input type="text" name="f_age" /></td>
  <td><input type="text" name="f_dept" /></td>
  <td><input type="text" name="f_rank" /></td>
  <td><input type="checkbox" name="f_is_left" /></td>
  <td><input type="submit" value="搜索" /></td>
  </tr>
  </form>
  <tr><td>ID</td><td>名字</td><td>性别</td><td>年龄</td><td>所属院系</td><td>职称</td><td>已解雇？</td></tr>
';
$c = startConn();
$stmt = prepareSQL($c,"SELECT * FROM Instructor");
$stmt->execute();
$stmt->bind_result(
    $instructorID,$instructorName,$instructorSex,$instructorAge,
    $instructorDept,$instructorRank,$instructorIsLeft);
$i = 0;
if(!$_REQUEST["page"] || strcmp($_REQUEST["page"],"1")==0) { $min = 0; $max = 24; }
else { $min = intval($_REQUEST["page"])*25; $max = $min + 24; }
while($stmt->fetch()) {
  if($_REQUEST["f_id"]&&strcmp($_REQUEST["f_id"],"")!=0&&(!strcmp($_REQUEST["f_id"],$instructorID)==0)) continue;
  if($_REQUEST["f_name"]&&(strcmp($_REQUEST["f_name"],"")!=0)&&(!strcmp($_REQUEST["f_name"],$instructorName)==0)) continue;
    if($_REQUEST["f_sex"]&&(strcmp($_REQUEST["f_sex"],"")!=0)&&(!strcmp($_REQUEST["f_sex"],$instructorSex)==0)) continue;
      if($_REQUEST["f_age"]&&(strcmp($_REQUEST["f_age"],"")!=0)&&(!intval($_REQUEST["f_age"]==$instructorAge))) continue;
  if($_REQUEST["f_dept"]&&(strcmp($_REQUEST["f_dept"],"")!=0)&&(!strcmp($_REQUEST["f_dept"],$instructorDept)==0)) continue;
  if($_REQUEST["f_rank"]&&(strcmp($_REQUEST["f_rank"],"")!=0)&&(!strcmp($_REQUEST["f_rank"],$instructorRank)==0)) continue;
  if(!$_REQUEST["f_is_left"]&&$instructorIsLeft==1) continue;
  if($i < $min || $i > $max) { $i = $i + 1; continue; }
  echo '<tr><td>' . $instructorID . '</td>';
  echo '<td>' . $instructorName . '</td>';
  echo '<td>' . $instructorSex . '</td>';
  echo '<td>' . $instructorAge . '</td>';
  echo '<td>' . $instructorDept . '</td>';
  echo '<td>' . $instructorRank . '</td>';
  echo '<td>' . ($instructorIsLeft==1?"是":"否") . '</td>';
  echo
       '<td><form action="./admin.php" mode="GET">
        <input type="hidden" name="mode" value="TInfo" />
	<input type="hidden" name="submode" value="TInfo" />
        <input type="hidden" name="instructorID" value="' . $instructorID . '" />
        <input type="submit" value="详细信息" />
        </form></td></tr>';
  $i = $i + 1;
}
echo '
</table>
';
} else {
  $instructorID = $_REQUEST["instructorID"];
  $c = startConn(); $s = prepareSQL($c,"SELECT * FROM Instructor WHERE Instructor.ID = ?");
  $s->bind_param('s',$instructorID);
  $s->execute();
  $s->bind_result(
      $instructorID,$instructorName,$instructorSex,$instructorAge,
      $instructorDept,$instructorRank,$instructorIsLeft);
  $s->fetch();
  $s->close();
  $c->close();
echo '
  <form action="./admin.php" method="GET">
  <input type="hidden" name="mode" value="TInfo" />
  <input type="hidden" name="submode" value="TInfo" />
  <input type="hidden" name="action" value="ChangeTInfo" />
  <input type="hidden" name="original_id" value="' . $instructorID . '" />
  <table>
  <tr><td>工号：</td><td>' . $instructorID . '</td><td></tr>
  <tr><td>名字：</td><td>' . $instructorName . '</td><td><input type="text" name="new_name" /></td></tr>
  <tr><td>性别：</td><td>' . $instructorSex . '</td><td><input type="text" name="new_sex" /></td></tr>
  <tr><td>年龄：</td><td>' . $instructorAge . '</td><td><input type="text" name="new_age" /></td></tr>
  <tr><td>所属院系：</td><td>' . $instructorDept . '</td><td><input type="text" name="new_dept" /></td></tr>
  <tr><td>职称：</td><td>' . $instructorRank . '</td><td><input type="text" name="new_rank" /></td></tr>
  <tr><td>已解雇？：</td><td>' . ($instructorIsLeft==1?"是":"否") . '</td><td><input type="radio" name="new_is_left" value="T" />是 <input type="radio" name="new_is_left" value="F" />否</td></tr>
  <tr><td colspan="3"><input type="submit" value="修改" /></td></tr>';
  
}
}
}

?>
</div>
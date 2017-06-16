
<div class="sidebar">
      <ul>
	<li><a href="./admin.php?mode=ClInfo&submode=NewCl">新增教室</a></li>
	<li><a href="./admin.php?mode=ClInfo&submode=ClInfo">查询/修改教室信息</a></li>
      </ul>
</div>    
<div class="main" id="main">
  <?php
    if ($_GET["submode"]) {
       if(strcmp($_GET["submode"],"NewCl")==0) {
	  echo '
   	    <table>
	      <form method="GET" action="./admin.php">
		<input type="hidden" name="mode" value="ClInfo" />
		<input type="hidden" name="submode" value="NewCl" />
		<input type="hidden" name="action" value="NewClassroom" />
	        <tr><td>教学楼：</td>
		<td>
	          <select name="building">';
          $c = startConn();
	  $stmt = getBuildings($c);
	  $stmt->bind_result($building_name);
      while($stmt->fetch()) {
          echo '<option value="' . $building_name . '">' . $building_name . '</option>';
      } $c->close();
	  echo '
	      </select>或在另外的教学楼：<input type="text" name="new_building" /></td></tr>
	      <tr><td>房间号：</td><td><input type="text" name="new_room_num" /></td></tr>
	      <tr><td>容量：</td><td><input type="text" name="new_room_capacity" /></td></tr>
	      <tr><td colspan="2""><input type="submit" values="查询" id="login_submit" />
                  </td></tr>
            </form>
          </table>';
    } elseif(strcmp($_GET["submode"],"ClInfo")==0) {
      echo '
        <table>
          <tr>
            <form action="./admin.php" method="GET">
              <input type="hidden" name="mode" value="ClInfo" />
              <input type="hidden" name="submode" value="ClInfo" />
              <td><select name="f_building">
       ';
       $c = startConn();
       $s = getBuildings($c);
       $s->bind_result($building_name);
       while($s->fetch()) {
         echo '<option value="' . $building_name . '">' . $building_name . '</option>';
       } $s->close(); $c->close();
       echo ' </select></td>
	      <td><input type="text" name="f_room_num" /></td>
	      <td><input type="text" name="f_capacity" /></td>
	      <td><input type="submit" value="搜索" /></td>
           </form></tr>
        <tr><td>建筑</td><td>房间号</td><td>容量</td></tr>
      ';
      $c = startConn();
      $s = prepareSQL($c,"SELECT * FROM Classroom");
      $s->execute();
      $s->bind_result($room_building,$room_num,$room_capacity);
      while($s->fetch()) {
        if($_REQUEST["f_building"]&&strcmp($_REQUEST["f_building"],"")!=0&&strcmp($_REQUEST["f_building"],$room_building)!=0) continue;
        if($_REQUEST["f_room_num"]&&strcmp($_REQUEST["f_room_num"],"")!=0&&strcmp($_REQUEST["f_room_num"],$room_num)!=0) continue;
        if($_REQUEST["f_capacity"]&&strcmp($_REQUEST["f_capacity"],"")!=0&&intval($_REQUEST["f_capacity"])!=$room_capacity) continue;
        echo '
          <tr><td>' . $room_building . '</td>
              <td>' . $room_num . '</td>
              <td>' . $room_capacity . '</td>
              <td><form action="./admin.php" mode="GET">
                  <input type="hidden" name="mode" value="ClInfo" />
                  <input type="hidden" name="submode" value="ShowInfo" />
                  <input type="hidden" name="building" value="' . $room_building . '" />
                  <input type="hidden" name="classroom" value="' . $room_num . '" />
                  <input type="submit" value="详细信息" />
                  </form></td></tr>
        ';
      }
      echo '
        </table>
      ';
    } elseif(strcmp($_REQUEST["submode"],"ShowInfo")==0) {
        if($_REQUEST["sett"]) {
            $s = prepareSQL($c,"CALL SET_CLASSROOM" . $_REQUEST["sett"] . "(?,?,?,?)");
            $s->bind_param('ssii',$_REQUEST["building"],$_REQUEST["classroom"],intval($_REQUEST["v1"]),intval($_REQUEST["v2"]));
            $s->execute();
            $s->close();
        }
        echo '
        <div>
        <table>
        <tr><td>建筑：</td><td>' . $_GET["building"] . '</td></tr>
        <tr><td>房间号：</td><td>' . $_GET["classroom"] . '</td></tr>
        <tr><td>容量：</td><td>' . retrCapacity($_GET["building"],$_GET["classroom"]) . '</td></tr>
        <tr><td colspan="2">可用时间：</td></tr>
        <tr><td colspan="2">
        <table>
        <tr><td></td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td><td>日</td></tr>';
        $c = startConn(); $tseg = retrTimeSecMaximum() * 0.01;
        $j = 0.01; while($j<=$tseg) {
          echo '
            <tr><td>第' . $j * 100 . '节</td>';
          $i = 1; while($i<=7) {
            $s = prepareSQL($c,"CALL IS_CLASSROOM_AVAILABLE(?,?,?)");
            $v = $i + $j;
            $s->bind_param('ssd',$_REQUEST["building"],$_REQUEST["classroom"],$v);
            $s->execute();
            $s->bind_result($resultV);
            $s->fetch();
            echo '
              <td>' . ($resultV==0?"*":"") . '
              <form action="./admin.php" method="GET">
                <input type="hidden" name="mode" value="ClInfo" />
                <input type="hidden" name="submode" value="ShowInfo" />
                <input type="hidden" name="building" value="' . $_REQUEST["building"] .'" />
                <input type="hidden" name="classroom" value="' . $_REQUEST["classroom"] . '" />
                <input type="hidden" name="sett" value="' . ($resultV==0?"_OFF":"_ON") . '" />
                <input type="hidden" name="v1" value="' . $i . '" />
                <input type="hidden" name="v2" value="' . $j*100 . '" />
                <input type="submit" value="' . ($resultV==0?"":"*") . '" />
               </form> </td>';
            $s->close();
            $i = $i + 1;
         } $j = $j + 0.01;
         echo '</tr>';
       } $c->close();
        echo '
        </table>
        </td></tr>
        </table>
        </div>
        ';
      }
    }
?>
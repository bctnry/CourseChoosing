<?php
require "config.php";

# connection utils.
function startConn() {
    global $mysql_db_addr, $mysql_username, $mysql_password, $mysql_database;
    $mysql_connection =
        mysqli_connect($mysql_db_addr,$mysql_username,$mysql_password,$mysql_database,'6379');
    if(!$mysql_connection) {
        echo('Failed to establish a connection: ' . mysqli_connect_error());
    } else {
	return $mysql_connection;
    }
}
function closeConn($conn) {
    mysqli_close($conn);
}
function prepareSQL($conn,$stmt) {
  if(!$resultstmt=mysqli_prepare($conn,$stmt)) {
      die("Failed to prepare SQL statement." . mysqli_error($conn));
  };
  return $resultstmt;
}
function guardExec($stmt) { if(!$stmt->execute()) die("Failed to execute."); }

# used to compute hash values.
function calcHash($s) { return password_hash($s,PASSWORD_BCRYPT); }

function regUser($userID,$userPasswd,$userType,$conn) {/*
    $stmt = prepareSQL($conn, "CALL IF_USER_EXISTS(?)");
    if(!$stmt->bind_param('s',$userID)) die("Failed to bind parameter."); guardExec($stmt);
    if(!$stmt->bind_result($resultV)) die("Failed to bind result.");
    if($resultV==1) die("User already exists.");
    else {*/
        $stmt = prepareSQL($conn, "CALL INSERT_USER(?,?,?)");
        $hashval = calcHash($userPasswd);
        if(!$stmt->bind_param('sss',$userID,$hashval,$userType)) echo "Failed to bind parameter.";
        guardExec($stmt);
        return true;
    //}
}
function regUsr_singl($userID,$userPasswd,$userType) {
    $c = startConn(); regUser($userID,$userPasswd,$userType,$c); $c->close();
}

# retrieving funcs
function retrUserHash($userID,$conn) {
  $stmt = prepareSQL($conn, "CALL RETRIEVE_HASH_OF_PASSWORD(?)");
  if(!$stmt->bind_param('s',$userID)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
  $stmt->fetch();
  $stmt->close();
  return $resultV;
}
function retrUserHash_singl($userID) {
  $c = startConn(); $r = retrUserHash($userID,$c); return $r;
}
function getUserID($sessionID) {
  $c = startConn();
  $stmt = prepareSQL($c,"CALL RETRIEVE_USER_ID_BY_HASHVAL(?)");
  if(!$stmt->bind_param('s',$sessionID)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
  $stmt->fetch();
  $stmt->close();
  $c->close();
  return $resultV;
  }
function getUserType($sessionID) {
  $c = startConn();
  $stmt = prepareSQL($c,"CALL RETRIEVE_USER_TYPE_BY_HASHVAL(?)");
  if(!$stmt->bind_param('s',$sessionID)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
  $stmt->fetch();
  $stmt->close();
  $c->close();
  return $resultV;
  }
function getStudentDept($studentID) {
  $c = startConn();
  $stmt = prepareSQL($c,"CALL RETRIEVE_STUDENT_DEPT(?)");
  if(!$stmt->bind_param('s',$studentID)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
  $stmt->fetch();
  $stmt->close();
  $c->close();
  return $resultV;
  }
function getStudentName($studentID) {
  $c = startConn();
  $stmt = prepareSQL($c,"CALL RETRIEVE_STUDENT_NAME(?)");
  if(!$stmt->bind_param('s',$studentID)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
  $stmt->fetch();
  $stmt->close();
  $c->close();
  return $resultV;
  }
function getBuildings($conn) {
  $stmt = prepareSQL($conn, "CALL RETRIEVE_BUILDING()");
  if(!$stmt->execute()) echo "Failed to execute." . mysqli_error($stmt);
  return $stmt;
  }


function retrClassrooms($building,$c) {
  $stmt = prepareSQL($c,"CALL RETR_CLASSROOMS(?)");
  if(!$stmt->bind_param('s',$building)) die("Failed to bind parameters.");
  if(!$stmt->execute()) die("Failed to execute.");
  return $stmt;
}

function retrTimeSecMaximum() {
  $c = startConn();
  $stmt = prepareSQL($c,"SELECT Time_Sec_Maximum FROM Settings");
  if(!$stmt->execute()) die("Failed to execute.");
  if(!$stmt->bind_result($resultV)) die("Failed to bind result.");
  $stmt->fetch(); $stmt->close(); $c->close();
  return $resultV;
  }

function retrCapacity($buildingName,$roomNum) {
  $c = startConn();
  $stmt = prepareSQL($c,"SELECT Capacity FROM Classroom WHERE Building = ? AND Room_Number = ?");
  $stmt->bind_param('ss',$buildingName,$roomNum);
  $stmt->execute();
  $stmt->bind_result($resultV);
  $stmt->fetch();
  $stmt->close(); $c->close();
  return $resultV;
  }

# updating funcs
function updateUserHash($userID,$newhash,$conn) {
  $stmt = prepareSQL($conn, "CALL UPDATE_USER_HASH(?,?)");
  if(!$stmt->bind_param('ss',$userID,$newhash)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  $stmt->close();
}
function updateUserHash_singl($userID,$newhash) {
  $c = startConn(); updateUserHash($userID,$newhash,$c);
}
function updateUserType($userID,$newtype,$conn) {
  $stmt = prepareSQL($conn,"CALL UPDATE_USER_TYPE(?,?)");
  if(!$stmt->bind_param('ss',$userID,$newtype)) die("Failed to bind parameters.");
  if(!$stmt->execute()) echo "Failed to execute.";
  $stmt->close();
}
function updateUserType_singl($userID,$newtype) {
  $c = startConn(); updateUserType($userID,$newtype,$c); $c->close();
}
function updateTName($tID,$newName,$c) {
  $s = prepareSQL($c,"CALL UPDATE_T_NAME(?,?)");
  $s->bind_param('ss',$tID,$newName);
  $s->execute();
  $s->close();
}
function updateTName_singl($tID,$newName) {
  $c = startConn(); updateTName($tID,$newName,$c);
}
function updateTSex($tID,$newSex,$c) {
  $s = prepareSQL($c,"CALL UPDATE_T_SEX(?,?)");
  $s->bind_param('ss',$tID,$newSex);
  $s->execute();
  $s->close();
}
function updateTSex_singl($tID,$newSex) { $c = startConn(); updateTSex($tID,$newSex,$c); }
function updateTAge($tID,$newAge,$c) {
  $v = intval($newAge);
  $s = prepareSQL($c,"CALL UPDATE_T_AGE(?,?)");
  $s->bind_param('si',$tID,$v);
  $s->execute();
  $s->close();
}
function updateTAge_singl($tID,$newAge) {
  $c = startConn(); $v = intval($newAge); updateTAge($tID,$v,$c);
}
function updateTDept($tID,$newDept,$c) {
  $s = prepareSQL($c,"CALL UPDATE_T_DEPT(?,?)");
  $s->bind_param('ss',$tID,$newDept);
  $s->execute();
  $s->close();
}
function updateTDept_singl($tID,$newDept) {
  $c = startConn(); updateTDept($tID,$newDept,$c);
}
function updateTRank($tID,$newRank,$c) {
  $s = prepareSQL($c,"CALL UPDATE_T_RANK(?,?)");
  $s->bind_param('ss',$tID,$newRank);
  $s->execute();
  $s->close();
}
function updateTRank_singl($tID,$newRank) {
  $c = startConn(); updateTRank($tID,$newRank,$c);
}
function updateTIsLeft($tID,$newIsLeft,$c) {
  $s = prepareSQL($c,"CALL UPDATE_T_IS_LEFT(?,?)");
  $s->bind_param('si',$tID,$newIsLeft);
  $s->execute();
  $s->close();
}
function updateTIsLeft_singl($tID,$newIsLeft) {
  $c = startConn(); updateTIsLeft($tID,$newIsLeft,$c);
}



function login($userID,$password,$conn) {
  if(!password_verify($password,retrUserHash($userID,$conn))) die("Wrong password.\n");
  $session_hashval = calcHash($userID . $password . time());
  $stmt = prepareSQL($conn,"CALL INSERT_LOGIN_HASH(?,?)");
  if(!$stmt->bind_param('ss',$userID,$session_hashval)) echo "Failed to bind parameter.";
  if(!$stmt->execute()) die("Failed to execue." . mysqli_error($conn));
  $stmt->close();
  setcookie("session_id",$session_hashval);
}
function clearCookie() {
    setcookie("session_id",null,time()-3600);
}
function ifLoginHashExists($loginHash,$conn) {
    $stmt = prepareSQL($conn, "CALL IF_LOGIN_HASH_EXISTS(?)");
    if(!$stmt->bind_param('s',$loginHash)) echo "Failed to bind parameter.";
    if(!$stmt->execute()) die("Failed to execute.\n" . mysqli_error($conn));
    if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
    $stmt->fetch();
    $stmt->close();
    return $resultV;
}
function ifUserExists($username,$conn) {
    $stmt = prepareSQL($conn,"CALL IF_USER_EXISTS(?)");
    if(!$stmt->bind_param('s',$username)) echo "Failed to bind parameter.";
    if(!$stmt->execute()) die("Failed to execute.\n" . mysqli_error($conn));
    if(!$stmt->bind_result($resultV)) echo "Failed to bind result.";
    $stmt->fetch();
    $stmt->close();
    echo $resultV;
    return $resultV;
}
function ifUserExists_singl($username) {
  $c = startConn(); $r = ifUserExists($username,$c); return $r;
}
function logoff($loginHash,$conn) {
    if(ifLoginHashExists($loginHash,$conn)) {
	    $stmt = prepareSQL($conn, "CALL DELETE_LOGIN_HASH(?)");
        if(!$stmt->bind_param('s',$loginHash))
            echo "Failed to bind parameter.";
        guardExec($stmt);
        $stmt->close();
    } clearCookie();
    echo "Done. ";
}


# inserting funcs
function newClassroom($building,$room_num,$capacity) {
  $c = startConn();
  $s = prepareSQL($c,"CALL INSERT_CLASSROOM(?,?,?)");
  $s->bind_param('ssi',$building,$room_num,$capacity);
  $s->execute();
  $s->close();
  $c->close();
}


?>
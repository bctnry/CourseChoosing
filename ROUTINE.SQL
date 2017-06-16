DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DELETE_LOGIN_HASH`(IN `hashval` VARCHAR(60))
    MODIFIES SQL DATA
    DETERMINISTIC
DELETE FROM Session_Control WHERE Session_Control.Session_ID = hashval$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IF_LOGIN_HASH_EXISTS`(IN `hashval` VARCHAR(60))
    NO SQL
SELECT EXISTS (SELECT * FROM Session_Control WHERE Session_Control.Session_ID = hashval)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IF_USER_EXISTS`(IN `userID` VARCHAR(32))
    READS SQL DATA
    DETERMINISTIC
SELECT EXISTS (SELECT * FROM Usr WHERE Usr.ID = userID)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `INSERT_LOGIN_HASH`(IN `username` VARCHAR(32), IN `hashval` VARCHAR(60))
    MODIFIES SQL DATA
    DETERMINISTIC
    COMMENT 'Login'
IF EXISTS (SELECT * FROM Session_Control WHERE Session_Control.Usr_ID = username) 
THEN SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO = '31001', MESSAGE_TEXT = "There's already a session.";
ELSE INSERT INTO Session_Control VALUES (username, hashval);
END IF$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `INSERT_CLASSROOM`(IN `building` VARCHAR(3), IN `room_num` VARCHAR(5), IN `capacity` SMALLINT(6))
    MODIFIES SQL DATA
INSERT INTO Classroom(Classroom.Building,Classroom.Room_Number,Classroom.Capacity) VALUES(building,room_num,capacity)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `INSERT_USER`(IN `userID` VARCHAR(32), IN `userPassword` VARCHAR(60), IN `userType` VARCHAR(10))
    MODIFIES SQL DATA
    DETERMINISTIC
INSERT INTO Usr(Usr.ID,Usr.Usertype,Usr.Passwd) VALUES (userID,userType,userPassword)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_HASH_OF_PASSWORD`(IN `username` VARCHAR(32))
    READS SQL DATA
    DETERMINISTIC
    COMMENT 'Get the hashval of the password of specified user.'
SELECT Passwd FROM Usr WHERE ID = username$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `INSERT_INSTRUCTOR`(IN `instID` VARCHAR(32), IN `instName` VARCHAR(40), IN `instSex` VARCHAR(5), IN `instAge` SMALLINT, IN `instDept` VARCHAR(10), IN `instRank` VARCHAR(10))
    MODIFIES SQL DATA
BEGIN
    SET @errormsg = 'Instructor with same ID already exists.';
    IF EXISTS (SELECT * FROM Instructor WHERE Instructor.ID = instID)
    THEN SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO = 30001, MESSAGE_TEXT = @errormsg;
    ELSE INSERT INTO Instructor VALUES (instID,instName,instSex,instAge,instDept,instRank);
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_BUILDING`()
    READS SQL DATA
    DETERMINISTIC
SELECT DISTINCT Classroom.Building FROM Classroom$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_STUDENT_NAME`(IN `studentID` VARCHAR(32))
    READS SQL DATA
    DETERMINISTIC
SELECT Student.Name FROM Student WHERE Student.ID = studentID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_STUDENT_DEPT`(IN `studentID` VARCHAR(32))
    READS SQL DATA
    DETERMINISTIC
SELECT Student.Dept FROM Student WHERE Student.ID = studentID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_LOGIN_HASH`(IN `userID` VARCHAR(32))
    READS SQL DATA
    DETERMINISTIC
SELECT Session_Control.Session_ID FROM Session_Control WHERE Session_Control.Usr_ID = userID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IS_CLASSROOM_AVAILABLE`(IN `building` VARCHAR(3), IN `room_num` VARCHAR(5), IN `time_sec` DECIMAL(3,2))
    NO SQL
SELECT EXISTS (SELECT * FROM Classroom_Unavailable_Information WHERE Classroom_Unavailable_Information.Building = building AND Classroom_Unavailable_Information.Room_Number = room_num AND  Classroom_Unavailable_Information.Time_Sec = time_sec)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_USER_ID_BY_HASHVAL`(IN `hashval` VARCHAR(60))
    READS SQL DATA
    DETERMINISTIC
SELECT Session_Control.Usr_ID FROM Session_Control WHERE Session_Control.Session_ID = hashval$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SET_CLASSROOM_ON`(IN `building` VARCHAR(3), IN `room_num` VARCHAR(5), IN `day` INT, IN `sec` INT)
    NO SQL
DELETE FROM Classroom_Unavailable_Information WHERE Classroom_Unavailable_Information.Building = building AND Classroom_Unavailable_Information.Room_Number = room_number AND
Classroom_Unavailable_Information.Time_Sec = day + sec * 0.01$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_ST_NAME`(IN `oID` VARCHAR(32), IN `name` VARCHAR(40))
    MODIFIES SQL DATA
UPDATE Student SET Student.Name = name WHERE Student.ID = oID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_ST_REG_YEAR`(IN `oID` VARCHAR(32), IN `reg_year` DECIMAL(4,0))
    MODIFIES SQL DATA
UPDATE Student SET Student.Register_Year = reg_year WHERE Student.ID = oID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETR_CLASSROOMS`(IN `buildingName` VARCHAR(3))
    READS SQL DATA
SELECT Classroom.Room_Number FROM Classroom WHERE Classroom.Building = buildingName$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_AGE`(IN `tID` VARCHAR(32), IN `newage` SMALLINT(6))
    MODIFIES SQL DATA
UPDATE Instructor SET Instructor.Age = newage WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SET_CLASSROOM_OFF`(IN `building` VARCHAR(3), IN `room_num` VARCHAR(5), IN `day` INT, IN `time_sec` INT)
    NO SQL
INSERT INTO Classroom_Unavailable_Information VALUES (building,room_num,day+time_sec*0.01)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_DEPT`(IN `tID` VARCHAR(32), IN `newdept` VARCHAR(10))
    NO SQL
UPDATE Instructor SET Instructor.Dept = newdept WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RETRIEVE_USER_TYPE_BY_HASHVAL`(IN `hashval` VARCHAR(60))
    NO SQL
SELECT Usr.Usertype FROM Usr, (SELECT Session_Control.Usr_ID FROM Session_Control WHERE Session_Control.Session_ID = hashval) AS Hashval WHERE Usr.ID = Hashval.Usr_ID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_IS_LEFT`(IN `tID` VARCHAR(32), IN `newisleft` BOOLEAN)
    NO SQL
UPDATE Instructor SET Instructor.IsLeft = newisleft WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_RANK`(IN `tID` VARCHAR(32), IN `newrank` VARCHAR(10))
    MODIFIES SQL DATA
UPDATE Instructor SET Instructor.Rank = newrank WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_NAME`(IN `tID` VARCHAR(32), IN `newname` VARCHAR(40))
    READS SQL DATA
UPDATE Instructor SET Instructor.Name = newname WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_T_SEX`(IN `tID` VARCHAR(32), IN `newsex` VARCHAR(5))
    NO SQL
UPDATE Instructor SET Instructor.Sex = newsex WHERE Instructor.ID = tID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_USER_TYPE`(IN `userID` VARCHAR(32), IN `newtype` VARCHAR(20))
    MODIFIES SQL DATA
UPDATE Usr SET Usertype = newtype WHERE Usr.ID = userID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_ST_DEPT`(IN `oID` VARCHAR(32), IN `dept` VARCHAR(10))
    MODIFIES SQL DATA
UPDATE Student SET Student.Dept = dept WHERE Student.ID = oID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_USER_HASH`(
  IN username VARCHAR(32), IN newhash VARCHAR(60)
)
    MODIFIES SQL DATA
    DETERMINISTIC
UPDATE Usr SET Passwd = newhash WHERE ID = username$$
DELIMITER ;

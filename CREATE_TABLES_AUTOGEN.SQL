SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `CourseChoosing_New` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `CourseChoosing_New`;

CREATE TABLE `Classroom` (
  `Building` varchar(3) NOT NULL,
  `Room_Number` varchar(5) NOT NULL,
  `Capacity` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Classroom_Unavailable_Information` (
  `Building` varchar(3) DEFAULT NULL,
  `Room_Number` varchar(5) DEFAULT NULL,
  `Time_Sec` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Course` (
  `ID` varchar(32) NOT NULL,
  `Section` varchar(2) NOT NULL,
  `Course_Session` decimal(5,1) NOT NULL,
  `Course_Type` varchar(10) DEFAULT NULL,
  `Name` varchar(20) DEFAULT NULL,
  `Credit` smallint(6) DEFAULT NULL,
  `Instructor_ID` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Course_Constraint` (
  `Student_Dept` varchar(10) NOT NULL,
  `Course_Type` varchar(10) NOT NULL,
  `Maximum_Credit` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Instructor` (
  `ID` varchar(32) NOT NULL,
  `Name` varchar(40) DEFAULT NULL,
  `Sex` varchar(5) DEFAULT NULL,
  `Age` smallint(6) DEFAULT NULL,
  `Dept` varchar(10) DEFAULT NULL,
  `Rank` varchar(10) DEFAULT NULL,
  `IsLeft` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Session_Control` (
  `Usr_ID` varchar(32) DEFAULT NULL,
  `Session_ID` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Settings` (
  `Time_Sec_Maximum` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Settings` (`Time_Sec_Maximum`) VALUES
(15);

CREATE TABLE `Student` (
  `ID` varchar(32) DEFAULT NULL,
  `Name` varchar(40) DEFAULT NULL,
  `Dept` varchar(10) DEFAULT NULL,
  `Register_Year` decimal(4,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Takes` (
  `Student_ID` varchar(32) NOT NULL,
  `Course_ID` varchar(32) NOT NULL,
  `Course_Section` varchar(2) NOT NULL,
  `Course_Session` decimal(5,1) NOT NULL,
  `Grade` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Take_Place` (
  `Course_ID` varchar(32) DEFAULT NULL,
  `Course_Section` varchar(2) DEFAULT NULL,
  `Course_Session` decimal(5,1) DEFAULT NULL,
  `Classroom_Building` varchar(3) NOT NULL,
  `Classroom_Room_Number` varchar(5) NOT NULL,
  `Classroom_Time_Sec` smallint(6) NOT NULL,
  `Length` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Usr` (
  `ID` varchar(32) NOT NULL,
  `Usertype` varchar(20) DEFAULT NULL,
  `Passwd` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `Classroom`
  ADD PRIMARY KEY (`Building`,`Room_Number`) USING BTREE;

ALTER TABLE `Classroom_Unavailable_Information`
  ADD KEY `Building` (`Building`,`Room_Number`);

ALTER TABLE `Course`
  ADD PRIMARY KEY (`ID`,`Section`,`Course_Session`),
  ADD KEY `Instructor_ID` (`Instructor_ID`);

ALTER TABLE `Course_Constraint`
  ADD PRIMARY KEY (`Student_Dept`,`Course_Type`);

ALTER TABLE `Instructor`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `Session_Control`
  ADD KEY `Usr_ID` (`Usr_ID`);

ALTER TABLE `Student`
  ADD KEY `ID` (`ID`);

ALTER TABLE `Takes`
  ADD PRIMARY KEY (`Student_ID`,`Course_ID`,`Course_Section`,`Course_Session`);

ALTER TABLE `Take_Place`
  ADD PRIMARY KEY (`Classroom_Building`,`Classroom_Room_Number`,`Classroom_Time_Sec`);

ALTER TABLE `Usr`
  ADD PRIMARY KEY (`ID`);


ALTER TABLE `Classroom_Unavailable_Information`
  ADD CONSTRAINT `Classroom_Unavailable_Information_ibfk_1` FOREIGN KEY (`Building`,`Room_Number`) REFERENCES `Classroom` (`Building`, `Room_Number`);

ALTER TABLE `Course`
  ADD CONSTRAINT `Course_ibfk_1` FOREIGN KEY (`Instructor_ID`) REFERENCES `Instructor` (`ID`);

ALTER TABLE `Session_Control`
  ADD CONSTRAINT `Session_Control_ibfk_1` FOREIGN KEY (`Usr_ID`) REFERENCES `Usr` (`ID`);

ALTER TABLE `Student`
  ADD CONSTRAINT `Student_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `Usr` (`ID`);

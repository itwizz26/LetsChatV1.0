/* Create Database */
CREATE DATABASE `live_chat`;

/* Comments table */
CREATE TABLE `comments` (
  `commentId` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Auto comment Id',
  `memberId` varchar(32) NOT NULL COMMENT 'UUID',
  `comment` text NOT NULL COMMENT 'User comment',
  `date_created` int(11) NOT NULL COMMENT 'Date comment captured',
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* Members table */
CREATE TABLE `members` (
  `memberId` varchar(32) NOT NULL COMMENT 'UUID',
  `username` varchar(50) NOT NULL COMMENT 'Username',
  `pass` varchar(100) NOT NULL COMMENT 'pass phrase',
  `name` varchar(20) DEFAULT NULL COMMENT 'Firstname',
  `surname` varchar(20) DEFAULT NULL COMMENT 'Lastname',
  `gender` varchar(2) DEFAULT NULL COMMENT 'User gender',
  `date_created` int(11) NOT NULL COMMENT 'Date user created',
  PRIMARY KEY (`memberId`),
  UNIQUE KEY `UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* Replies table */
CREATE TABLE `replies` (
  `replyId` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Auto Id',
  `commentId` int(11) NOT NULL COMMENT 'Original comment Id',
  `memberId` varchar(32) NOT NULL COMMENT 'UUID',
  `reply` text NOT NULL COMMENT 'Reply message',
  `date_created` int(11) NOT NULL COMMENT 'Date reply captured',
  PRIMARY KEY (`replyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* Sessions table */
CREATE TABLE `sessions` (
  `sessionId` varchar(32) NOT NULL COMMENT 'Unique session Id',
  `memberId` varchar(32) NOT NULL COMMENT 'UUID',
  `date_started` int(11) NOT NULL COMMENT 'Date session started',
  `date_ended` int(11) NOT NULL COMMENT 'Date session ended',
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

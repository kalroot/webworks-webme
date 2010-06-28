<?php
// {
  if($version==0){//Create the tables
  	dbQuery('CREATE TABLE IF NOT EXISTS `quiz_quizzes` (
							`id` int (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							`name` text NOT NULL,
							`topic` text NOT NULL
						   ) ENGINE=MYISAM DEFAULT CHARSET=utf8');

    dbQuery('CREATE TABLE IF NOT EXISTS `quiz_questions` (
						    `id` int (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							`quiz` text NOT NULL,
						    `question` text NOT NULL,
						    `topic` text,
						    `answer1` text NOT NULL,
							`answer2` text NOT NULL,
							`answer3` text,
							`answer4` text,
							`correctAnswer` int (1) NOT NULL
						  ) ENGINE=MYISAM DEFAULT CHARSET= utf8');
    $version = 1;
  }
  if ($version==1) {
  	dbQuery ("ALTER TABLE quiz_questions CHANGE quiz quiz_id int default 0");
	$version=2;
  }
  if ($version==2) {
  	dbQuery ("ALTER TABLE quiz_quizzes CHANGE topic description text");
	$version = 3;
  }
  $DBVARS[$pname.'|version']=$version;
  config_rewrite();
// }

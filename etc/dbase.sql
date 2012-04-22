
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`onetask-ow` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `ot_project` */

DROP TABLE IF EXISTS `ot_project`;

CREATE TABLE `ot_project` (
  `project_id` int(255) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `project_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_project` */

insert  into `ot_project`(`project_id`,`project_name`,`project_status`) values (1,'Default Project',1);

/*Table structure for table `ot_task` */

DROP TABLE IF EXISTS `ot_task`;

CREATE TABLE `ot_task` (
  `task_id` int(255) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `task_description` text COLLATE latin1_general_ci,
  `task_project` int(255) NOT NULL,
  `task_type` int(10) NOT NULL,
  `task_reporter` int(255) NOT NULL,
  `task_developer` int(255) DEFAULT NULL,
  `task_estimated_dev_time` int(255) DEFAULT NULL,
  `task_create_time` int(255) NOT NULL,
  `task_update_time` int(255) NOT NULL,
  `task_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_task` */

/*Table structure for table `ot_task_dependancy` */

DROP TABLE IF EXISTS `ot_task_dependancy`;

CREATE TABLE `ot_task_dependancy` (
  `task_dependancy_id` int(255) NOT NULL AUTO_INCREMENT,
  `task_dependancy_task` int(255) NOT NULL,
  `task_dependancy_dependancy` int(255) NOT NULL,
  PRIMARY KEY (`task_dependancy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_task_dependancy` */

/*Table structure for table `ot_task_note` */

DROP TABLE IF EXISTS `ot_task_note`;

CREATE TABLE `ot_task_note` (
  `task_note_id` int(255) NOT NULL AUTO_INCREMENT,
  `task_note_task_id` int(255) NOT NULL,
  `task_note_comment` blob,
  `task_note_time` int(32) DEFAULT NULL,
  `task_note_creator` int(255) DEFAULT NULL,
  `task_note_status` int(10) DEFAULT '1',
  PRIMARY KEY (`task_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_task_note` */

/*Table structure for table `ot_task_status` */

DROP TABLE IF EXISTS `ot_task_status`;

CREATE TABLE `ot_task_status` (
  `task_status_id` int(255) NOT NULL AUTO_INCREMENT,
  `task_status_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `task_status_description` varbinary(255) DEFAULT NULL,
  `task_status_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_task_status` */

insert  into `ot_task_status`(`task_status_id`,`task_status_name`,`task_status_description`,`task_status_status`) values (1,'New','New task, not yet assigned',1);
insert  into `ot_task_status`(`task_status_id`,`task_status_name`,`task_status_description`,`task_status_status`) values (2,'Assigned','Assigned to a Developer',1);
insert  into `ot_task_status`(`task_status_id`,`task_status_name`,`task_status_description`,`task_status_status`) values (3,'In Progress','Currently being worked on',1);
insert  into `ot_task_status`(`task_status_id`,`task_status_name`,`task_status_description`,`task_status_status`) values (4,'Complete','Task Completed, yet to be confirmed',1);
insert  into `ot_task_status`(`task_status_id`,`task_status_name`,`task_status_description`,`task_status_status`) values (5,'Closed','Task Confirmed to be Complete',1);

/*Table structure for table `ot_task_type` */

DROP TABLE IF EXISTS `ot_task_type`;

CREATE TABLE `ot_task_type` (
  `task_type_id` int(255) NOT NULL AUTO_INCREMENT,
  `task_type_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `task_type_description` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `task_type_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_task_type` */

insert  into `ot_task_type`(`task_type_id`,`task_type_name`,`task_type_description`,`task_type_status`) values (1,'Bug','a Problem that needs to be fixed.',1);
insert  into `ot_task_type`(`task_type_id`,`task_type_name`,`task_type_description`,`task_type_status`) values (2,'Feature Request','a feature that would be nice to have.',1);
insert  into `ot_task_type`(`task_type_id`,`task_type_name`,`task_type_description`,`task_type_status`) values (3,'Modification','a Change that should be made.',1);

/*Table structure for table `ot_user` */

DROP TABLE IF EXISTS `ot_user`;

CREATE TABLE `ot_user` (
  `user_id` int(255) NOT NULL AUTO_INCREMENT,
  `user_type` int(10) NOT NULL DEFAULT '1',
  `user_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `user_pass` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `user_task` int(255) NOT NULL DEFAULT '0',
  `user_default_developer` int(255) NOT NULL DEFAULT '0',
  `user_default_type` int(10) NOT NULL DEFAULT '0',
  `user_project` int(255) NOT NULL DEFAULT '1',
  `user_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `ot_user` */

insert  into `ot_user`(`user_id`,`user_type`,`user_name`,`user_pass`,`user_task`,`user_default_developer`,`user_default_type`,`user_project`,`user_status`) values (1,3,'Administrator','7014173d5c564e2a5c5132a32e514e32',99,30,1,1,1);

/*Table structure for table `ot_user_type` */

DROP TABLE IF EXISTS `ot_user_type`;

CREATE TABLE `ot_user_type` (
  `user_type_id` int(255) NOT NULL AUTO_INCREMENT,
  `user_type_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `user_type_description` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `user_type_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `ot_user_type` */

insert  into `ot_user_type`(`user_type_id`,`user_type_name`,`user_type_description`,`user_type_status`) values (1,'Reporter','Allowed to view tasks, and make tasks',1);
insert  into `ot_user_type`(`user_type_id`,`user_type_name`,`user_type_description`,`user_type_status`) values (2,'Developer','Allowed to view tasks, make tasks, and work on tasks',1);
insert  into `ot_user_type`(`user_type_id`,`user_type_name`,`user_type_description`,`user_type_status`) values (3,'Administrator','Allowed to do everything',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

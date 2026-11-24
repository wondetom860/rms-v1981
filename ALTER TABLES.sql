ALTER TABLE `ac_active_programs`
ADD COLUMN `entry_type` INT(1) NULL DEFAULT 1 AFTER `spec_relId`;

ALTER TABLE `w_group_setup`
ADD COLUMN `gs_entry_type` INT(1) NULL DEFAULT 1 AFTER `gs_active_prog_relId`;

ALTER TABLE `course`
ADD COLUMN `course_entry_type` INT(1) NULL DEFAULT 1 AFTER `course_updated_at`;

ALTER TABLE `course`
ADD COLUMN `course_equivalet` JSON NULL AFTER `course_entry_type`;

ALTER TABLE `student`
ADD COLUMN `entry_type` INT(1) NULL DEFAULT 1 AFTER `std_acProgSched_relId`;

ALTER TABLE `course_catalog`
ADD COLUMN `cocat_entry_type` INT(1) NULL DEFAULT 1 AFTER `cocat_status`;

ALTER TABLE `semester_registration_2`
ADD COLUMN `semreg_entry_type` INT(1) NULL DEFAULT 1 AFTER `semreg_project_course_credits`;

-- Feb

ALTER TABLE `program`
ADD COLUMN `total_crdt_hrs` DECIMAL(5, 2) NULL DEFAULT 160 AFTER `program_duration`;

ALTER TABLE `user`
ADD COLUMN `hrms_registration_code` VARCHAR(45) NULL AFTER `activated_by`;

-- March
ALTER TABLE `semester_registration_2_detail`
ADD COLUMN `srd2_letter_grade` VARCHAR(30) NULL AFTER `srd2_requested_reassessment`,
ADD COLUMN `srd2_grade_point` DECIMAL(5, 3) NULL AFTER `srd2_letter_grade`,
ADD COLUMN `srd2_pass_status` INT(1) NULL DEFAULT 0 AFTER `srd2_grade_point`,
ADD COLUMN `srd2_grade_point_sum` DECIMAL(7, 4) NULL AFTER `srd2_pass_status`;

ALTER TABLE `course_catalog`
ADD COLUMN `min_pass_grade_relId` INT(11) NULL AFTER `cocat_entry_type`;

ALTER TABLE `course_catalog`
ADD COLUMN `course_category_relId` INT(11) NULL AFTER `min_pass_grade_relId`;

CREATE TABLE `course_category` (
    `id` int NOT NULL AUTO_INCREMENT,
    `catgeory_name` varchar(50) NOT NULL DEFAULT 'Major',
    `pass_grade_relId` int DEFAULT NULL COMMENT 'Minimum pass grade for courses under this category',
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `created_by` int DEFAULT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `course_category_user_created` (`created_by`),
    KEY `course_category_user_updated` (`updated_by`),
    CONSTRAINT `course_category_user_created` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
    CONSTRAINT `course_category_user_updated` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '1',
        '2012/13',
        '1',
        '2012-02-01 12:00:13',
        '2012-02-01 12:00:13',
        '12'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '2',
        '2013/14',
        '1',
        '2014-02-01 12:00:13',
        '2014-02-01 12:00:13',
        '13'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '3',
        '2014/15',
        '1',
        '2015-02-01 12:00:13',
        '2015-02-01 12:00:13',
        '14'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '4',
        '2015/16',
        '1',
        '2016-02-01 12:00:13',
        '2016-02-01 12:00:13',
        '15'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '5',
        '2016/17',
        '1',
        '2017-02-01 12:00:13',
        '2017-02-01 12:00:13',
        '16'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '6',
        '2017/18',
        '1',
        '2018-02-01 12:00:13',
        '2018-02-01 12:00:13',
        '17'
    );

INSERT INTO
    `school_year` (
        `id`,
        `sch_year_desc`,
        `sch_year_status`,
        `sch_created_at`,
        `sch_updated_at`,
        `sch_bach_number`
    )
VALUES (
        '7',
        '2018/19',
        '1',
        '2019-02-01 12:00:13',
        '2019-02-01 12:00:13',
        '18'
    );

-- April update
ALTER TABLE `clearance_allowed_student`
ADD COLUMN `cas_reason` VARCHAR(67) NULL AFTER `cas_dob`;

ALTER TABLE `user`
ADD COLUMN `first_name_am` VARCHAR(45) NULL AFTER `hrms_registration_code`,
ADD COLUMN `middle_name_am` VARCHAR(45) NULL AFTER `first_name_am`,
ADD COLUMN `last_name_am` VARCHAR(45) NULL AFTER `middle_name_am`;

ALTER TABLE `ase_evaluation_result`
ADD COLUMN `eval_point_rate` INT(4) NULL AFTER `data`;

CREATE TABLE `student_profile_change` (
    `id` int NOT NULL AUTO_INCREMENT,
    `std_attribute` varchar(45) NOT NULL,
    `attribute_label` varchar(50) NOT NULL,
    `old_value` varchar(100) NOT NULL,
    `new_value` varchar(100) NOT NULL,
    `std_relId` int NOT NULL,
    `approval_status` int DEFAULT '0',
    `approved_by` int DEFAULT NULL,
    `approved_at` int DEFAULT NULL,
    `created_at` int NOT NULL,
    `created_by` int NOT NULL,
    `updated_at` int DEFAULT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    `reason` text NOT NULL,
    `relation_name` varchar(50) DEFAULT NULL,
    `related_attribute_name` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_student_profile_change_1_idx` (`std_relId`),
    KEY `fk_student_profile_change_2_idx` (`approved_by`),
    KEY `fk_student_profile_change_3_idx` (`created_by`),
    KEY `fk_student_profile_change_4_idx` (`updated_by`),
    CONSTRAINT `fk_student_profile_change_1` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_student_profile_change_2` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_student_profile_change_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
    CONSTRAINT `fk_student_profile_change_4` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci

ALTER TABLE `user`
ADD COLUMN `last_password_changed_date` INT(11) NULL AFTER `last_name_am`;

-- tables
SELECT
    concat(
        school_year.sch_year_desc,
        '-',
        semester.semester_name
    ) as ACY,
    course.course_code as COURSECODE,
    course.course_crdthr as CREDITHOUR
FROM
    semester_registration_2
    inner join student on student.id = semester_registration_2.semreg_std_relId
    inner join semester_registration_2_detail on semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId
    inner join course on course.id = semester_registration_2_detail.srd2_course_relId
    inner join school_year on school_year.id = semester_registration_2.semreg_acadYear_relId
    inner join semester on semester.id = semester_registration_2.semreg_semester_relId
WHERE
    student.std_acadYear_relId = 17
GROUP BY
    course.id
ORDER BY school_year.id, semester.id, course.course_dept_relId, course.course_title;

--

SELECT
    semester_registration_2.id as SEMRID,
    semester_registration_2_detail.id as SRD2ID,
    student.std_regId as REGID,
    concat(
        school_year.sch_year_desc,
        '-',
        semester.semester_name
    ) as ACY,
    course.course_code as COURSECODE,
    course.course_crdthr as CREDITHOUR
FROM
    semester_registration_2
    inner join student on student.id = semester_registration_2.semreg_std_relId
    inner join semester_registration_2_detail on semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId
    inner join course on course.id = semester_registration_2_detail.srd2_course_relId
    inner join school_year on school_year.id = semester_registration_2.semreg_acadYear_relId
    inner join semester on semester.id = semester_registration_2.semreg_semester_relId
WHERE
    student.std_acadYear_relId = 17
ORDER BY student.id, school_year.id, semester.id, course.course_title;

-- July
CREATE TABLE `ase_evaluation_result_detail` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `evaluation_result_relId` INT(11) NOT NULL,
    `evaluee_relId` INT(11) NOT NULL,
    `criteria_relId` INT(11) NOT NULL,
    `ratings` TEXT NOT NULL,
    `average` DECIMAL(5, 2) NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` INT(11) NOT NULL,
    `updated_by` INT(11) NULL,
    `updated_at` INT(11) NULL,
    `data` TINYTEXT NULL,
    PRIMARY KEY (`id`)
);

ALTER TABLE `ase_evaluation_result`
ADD COLUMN `result_distributed` INT(1) NULL DEFAULT 0 AFTER `eval_point_rate`;

-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: dec44
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!50503 SET NAMES utf8 */
;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;
/*!40103 SET TIME_ZONE='+00:00' */
;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */
;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

--
-- Table structure for table `re_registration`
--

DROP TABLE IF EXISTS `re_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `re_registration` (
    `id` int NOT NULL AUTO_INCREMENT,
    `prev_semreg` mediumtext NOT NULL,
    `prev_srd2` mediumtext NOT NULL,
    `std_relId` int NOT NULL,
    `prev_gas_relId` int NOT NULL,
    `new_gas_relId` int DEFAULT NULL,
    `course_relId` int NOT NULL,
    `new_coff2det_relId` int DEFAULT NULL,
    `trail_count` smallint NOT NULL,
    `reg_approved` smallint DEFAULT NULL,
    `reg_approved_at` int DEFAULT NULL,
    `reg_approved_by` int DEFAULT NULL,
    `dept_approved` smallint DEFAULT NULL,
    `dept_approved_at` int DEFAULT NULL,
    `dept_approved_by` int DEFAULT NULL,
    `admin_processed` smallint DEFAULT '0',
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `deleted_at` int DEFAULT NULL,
    `deleted_by` int DEFAULT NULL,
    `created_by` int NOT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk-re_registration-std_relId` (`std_relId`),
    KEY `fk-re_registration-course_relId` (`course_relId`),
    KEY `fk-re_registration-new_coff2det_relId` (`new_coff2det_relId`),
    KEY `fk-re_registration-reg_approved_by` (`reg_approved_by`),
    KEY `fk-re_registration-dept_approved_by` (`dept_approved_by`),
    KEY `fk-re_registration-created_by` (`created_by`),
    KEY `fk-re_registration-updated_by` (`updated_by`),
    KEY `fk-re_registration-deleted_by` (`deleted_by`),
    KEY `fk-re_registration-prev_gas_relId_idx` (`prev_gas_relId`),
    KEY `fk-re_registration-new_gas_relId_idx` (`new_gas_relId`),
    CONSTRAINT `fk-re_registration-course_relId` FOREIGN KEY (`course_relId`) REFERENCES `course` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-dept_approved_by` FOREIGN KEY (`dept_approved_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-new_coff2det_relId` FOREIGN KEY (`new_coff2det_relId`) REFERENCES `course_offering2_detail` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-new_gas_relId` FOREIGN KEY (`new_gas_relId`) REFERENCES `w_group_setup` (`id`),
    CONSTRAINT `fk-re_registration-prev_gas_relId` FOREIGN KEY (`prev_gas_relId`) REFERENCES `w_group_setup` (`id`),
    CONSTRAINT `fk-re_registration-reg_approved_by` FOREIGN KEY (`reg_approved_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-std_relId` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-re_registration-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */
;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */
;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */
;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */
;

-- Dump completed on 2025-07-12 23:17:02

ALTER TABLE `semester_registration_2`
ADD COLUMN `semreg_re_registered` INT(1) NULL AFTER `semreg_entry_type`;

--july 15
ALTER TABLE `ac_active_programs`
ADD COLUMN `gc_students_exported` SMALLINT(1) NULL AFTER `remark`;

-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: dec44
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!50503 SET NAMES utf8 */
;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;
/*!40103 SET TIME_ZONE='+00:00' */
;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */
;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

--
-- Table structure for table `gc_entry`
--

DROP TABLE IF EXISTS `gc_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `gc_entry` (
    `id` int NOT NULL AUTO_INCREMENT,
    `admission_relId` int NOT NULL,
    `program_relId` int NOT NULL,
    `dept_relId` int NOT NULL,
    `spec_relId` int DEFAULT NULL,
    `acad_year_relId` int NOT NULL,
    `senate_approve_date` int NOT NULL,
    `header_title` varchar(255) DEFAULT NULL,
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `deleted_at` int DEFAULT NULL,
    `deleted_by` int DEFAULT NULL,
    `created_by` int NOT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk-gc_entry-admission_relId` (`admission_relId`),
    KEY `fk-gc_entry-program_relId` (`program_relId`),
    KEY `fk-gc_entry-dept_relId` (`dept_relId`),
    KEY `fk-gc_entry-spec_relId` (`spec_relId`),
    KEY `fk-gc_entry-acad_year_relId` (`acad_year_relId`),
    KEY `fk-gc_entry-created_by` (`created_by`),
    KEY `fk-gc_entry-updated_by` (`updated_by`),
    KEY `fk-gc_entry-deleted_by` (`deleted_by`),
    CONSTRAINT `fk-gc_entry-acad_year_relId` FOREIGN KEY (`acad_year_relId`) REFERENCES `ac_academic_year` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-admission_relId` FOREIGN KEY (`admission_relId`) REFERENCES `admission` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-dept_relId` FOREIGN KEY (`dept_relId`) REFERENCES `department` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-program_relId` FOREIGN KEY (`program_relId`) REFERENCES `program` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-spec_relId` FOREIGN KEY (`spec_relId`) REFERENCES `specialization` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_entry-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Table structure for table `gc_student`
--

DROP TABLE IF EXISTS `gc_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `gc_student` (
    `id` int NOT NULL AUTO_INCREMENT,
    `entry_relId` int NOT NULL,
    `std_relId` int NOT NULL,
    `graduation_mode` smallint DEFAULT '1' COMMENT '1:normal,2:delayed',
    `total_credit_hr` int NOT NULL,
    `course_count` smallint NOT NULL,
    `total_gps` decimal(7, 4) NOT NULL,
    `cgpa` decimal(7, 3) NOT NULL,
    `sgpa` decimal(7, 3) DEFAULT NULL,
    `major_gpa` decimal(7, 3) DEFAULT NULL,
    `last_semreg_relId` int DEFAULT NULL,
    `reg_approved` smallint DEFAULT NULL,
    `reg_approved_at` int DEFAULT NULL,
    `reg_approved_by` int DEFAULT NULL,
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `deleted_at` int DEFAULT NULL,
    `deleted_by` int DEFAULT NULL,
    `created_by` int NOT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk-gc_student-entry_relId` (`entry_relId`),
    KEY `fk-gc_student-reg_approved_by` (`reg_approved_by`),
    KEY `fk-gc_student-created_by` (`created_by`),
    KEY `fk-gc_student-updated_by` (`updated_by`),
    KEY `fk-gc_student-deleted_by` (`deleted_by`),
    KEY `fk_gc_last_semreg_relId_idx` (`last_semreg_relId`),
    CONSTRAINT `fk-gc_student-created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_student-deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_student-entry_relId` FOREIGN KEY (`entry_relId`) REFERENCES `gc_entry` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_student-reg_approved_by` FOREIGN KEY (`reg_approved_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-gc_student-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_gc_last_semreg_relId` FOREIGN KEY (`last_semreg_relId`) REFERENCES `semester_registration_2` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */
;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */
;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */
;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */
;

-- Dump completed on 2025-07-16 22:22:53

ALTER TABLE `semester_registration_2_detail`
ADD COLUMN `srd2_cocat_relId` INT(11) NULL AFTER `srd2_grade_point_sum`;

-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: chs
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!50503 SET NAMES utf8 */
;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;
/*!40103 SET TIME_ZONE='+00:00' */
;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */
;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

--
-- Table structure for table `transfer_student`
--

DROP TABLE IF EXISTS `transfer_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `transfer_student` (
    `id` int NOT NULL AUTO_INCREMENT,
    `std_relId` int NOT NULL,
    `transfered_from` varchar(255) NOT NULL,
    `year_admitted` varchar(255) NOT NULL DEFAULT '2019/20',
    `year_transfered` int NOT NULL COMMENT 'link to school year id',
    `sgpa` decimal(5, 3) NOT NULL DEFAULT '0.000',
    `cgpa` decimal(5, 3) NOT NULL DEFAULT '0.000',
    `exempted_course_ids` text,
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `deleted_at` int DEFAULT NULL,
    `deleted_by` int DEFAULT NULL,
    `created_by` int NOT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    `class_level_joined` int NOT NULL,
    `approved_by` int DEFAULT NULL,
    `approved_at` int DEFAULT NULL,
    `approved` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk-transfer_student-created_by` (`created_by`),
    KEY `fk-transfer_student-updated_by` (`updated_by`),
    KEY `fk-transfer_student-deleted_by` (`deleted_by`),
    KEY `fk-transfer_student-year_transfered` (`year_transfered`),
    KEY `fk-transfer_student-std_relId` (`std_relId`),
    CONSTRAINT `fk-transfer_student-created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-transfer_student-deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
    CONSTRAINT `fk-transfer_student-std_relId` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-transfer_student-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk-transfer_student-year_transfered` FOREIGN KEY (`year_transfered`) REFERENCES `ac_academic_year` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Table structure for table `exempted_course`
--

DROP TABLE IF EXISTS `exempted_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `exempted_course` (
    `id` int NOT NULL AUTO_INCREMENT,
    `ts_relid` int NOT NULL,
    `course_code` varchar(20) NOT NULL,
    `course_name` varchar(255) NOT NULL,
    `course_hours` smallint NOT NULL,
    `grade_obtained` varchar(3) NOT NULL,
    `grade_point` decimal(3, 2) NOT NULL DEFAULT '0.00',
    `grade_point_sum` decimal(5, 2) NOT NULL DEFAULT '0.00',
    `equivalent_course_ids` text,
    `approved` smallint DEFAULT NULL COMMENT '0: not approved, 1: approved',
    `created_at` int DEFAULT NULL,
    `updated_at` int DEFAULT NULL,
    `deleted_at` int DEFAULT NULL,
    `deleted_by` int DEFAULT NULL,
    `created_by` int NOT NULL,
    `updated_by` int DEFAULT NULL,
    `data` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk-exempted_course-created_by` (`created_by`),
    KEY `fk-exempted_course-updated_by` (`updated_by`),
    KEY `fk-exempted_course-deleted_by` (`deleted_by`),
    CONSTRAINT `fk-exempted_course-created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk-exempted_course-deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
    CONSTRAINT `fk-exempted_course-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

/*!40101 SET character_set_client = @saved_cs_client */
;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */
;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */
;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */
;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */
;

-- Dump completed on 2025-07-31 13:36:23

ALTER TABLE `w_student_withdrawal_history`
ADD COLUMN `swh_igraded_srd2s` VARCHAR(45) NULL AFTER `section_relId`;

ALTER TABLE `course_catalog`
ADD COLUMN `preq_course_ids` TINYTEXT NULL AFTER `course_category_relId`;

ALTER TABLE `course`
ADD COLUMN `course_type` SMALLINT(1) 1 AFTER `course_equivalet`;

CREATE TABLE `removed_srd2` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `semreg_relId` INT NOT NULL,
    `std_relId` INT NOT NULL,
    `srd2_serialized` TINYTEXT NULL,
    `cont_ass_marks` TINYTEXT NULL,
    `total_mark` DECIMAL(7, 3) NULL DEFAULT 0,
    `grade_relId` INT NULL,
    `hod_approved` SMALLINT(1) NULL DEFAULT 0,
    `hod_approved_at` INT NULL,
    `hod_approved_by` INT NULL,
    `reg_approved` SMALLINT(1) NULL DEFAULT 0,
    `reg_approved_at` INT NULL,
    `reg_approved_by` INT NULL,
    `created_at` INT NOT NULL,
    `created_by` INT NOT NULL,
    `updated_at` INT NULL,
    `updated_by` INT NULL,
    `data` TINYTEXT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_removed_srd2_1_idx` (`semreg_relId` ASC) VISIBLE,
    INDEX `fk_removed_srd2_2_idx` (`std_relId` ASC) VISIBLE,
    INDEX `fk_removed_srd2_3_idx` (`hod_approved_by` ASC) VISIBLE,
    INDEX `fk_removed_srd2_4_idx` (`reg_approved_by` ASC) VISIBLE,
    INDEX `fk_removed_srd2_5_idx` (`created_by` ASC) VISIBLE,
    INDEX `fk_removed_srd2_6_idx` (`updated_by` ASC) VISIBLE,
    INDEX `fk_removed_srd2_7_idx` (`grade_relId` ASC) VISIBLE,
    CONSTRAINT `fk_removed_srd2_1` FOREIGN KEY (`semreg_relId`) REFERENCES `semester_registration_2` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_2` FOREIGN KEY (`std_relId`) REFERENCES `student` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_3` FOREIGN KEY (`hod_approved_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_4` FOREIGN KEY (`reg_approved_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_5` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_6` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_removed_srd2_7` FOREIGN KEY (`grade_relId`) REFERENCES `grade_scale` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

ALTER TABLE `removed_srd2`
ADD COLUMN `coff2det_relId` INT NOT NULL AFTER `data`,
ADD INDEX `fk_removed_srd2_8_idx` (`coff2det_relId` ASC) VISIBLE;
;
ALTER TABLE `removed_srd2`
ADD CONSTRAINT `fk_removed_srd2_8` FOREIGN KEY (`coff2det_relId`) REFERENCES `course_offering2_detail` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `semester_registration_2_detail`
ADD COLUMN `srd2_requested_remove` SMALLINT(1) NULL DEFAULT 0 AFTER `srd2_cocat_relId`;

ALTER TABLE `removed_srd2`
CHANGE COLUMN `srd2_serialized` `srd2_serialized` LONGTEXT NULL DEFAULT NULL,
CHANGE COLUMN `cont_ass_marks` `cont_ass_marks` LONGTEXT NULL DEFAULT NULL;

ALTER TABLE `touched_grades` DROP FOREIGN KEY `tg_srd2_rel`;

ALTER TABLE `touched_grades`
ADD CONSTRAINT `tg_srd2_rel` FOREIGN KEY (`tg_srd2_relId`) REFERENCES `semester_registration_2_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `student`
ADD COLUMN `total_credit_hrs` SMALLINT(4) NULL DEFAULT 0 AFTER `entry_type`,
ADD COLUMN `total_cgpa` DECIMAL(7, 4) NULL DEFAULT 0 AFTER `total_credit_hrs`;

CREATE DEFINER=`root`@`%` TRIGGER `CHS_YYY_M_D` BEFORE INSERT ON `student` FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Something went wrong, please try again';
END

CREATE TABLE `sent_publishd_grades` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `semreg_relId` INT(11) NOT NULL,
    `date_published` INT(11) NOT NULL,
    `recipient_email` VARCHAR(50) NOT NULL,
    `relation` VARCHAR(50) NOT NULL,
    `reg_approved` SMALLINT(1) NULL DEFAULT 0,
    `mail_sent` SMALLINT(1) NULL DEFAULT 0,
    `date_sent` INT(11) NULL,
    `grade_report_path` VARCHAR(50) NULL,
    `trail_count` SMALLINT(1) NULL DEFAULT 1,
    PRIMARY KEY (`id`)
);





-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: dec44
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.22.04.5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sent_publishd_grades`
--

DROP TABLE IF EXISTS `sent_publishd_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sent_publishd_grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `semreg_relId` int NOT NULL,
  `date_published` int NOT NULL,
  `recipient_email` varchar(150) NOT NULL,
  `relation` varchar(150) NOT NULL,
  `reg_approved` smallint DEFAULT '0',
  `mail_sent` smallint DEFAULT '0',
  `date_sent` int DEFAULT NULL,
  `grade_report_path` varchar(150) DEFAULT NULL,
  `trail_count` smallint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_pgr_semreg_idx` (`semreg_relId`),
  CONSTRAINT `fk_pgr_semreg` FOREIGN KEY (`semreg_relId`) REFERENCES `semester_registration_2` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-14 17:40:58

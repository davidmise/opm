-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for overland_pm_workflow
CREATE DATABASE IF NOT EXISTS `overland_pm_workflow` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `overland_pm_workflow`;

-- Dumping structure for table overland_pm_workflow.opm_activity_logs
CREATE TABLE IF NOT EXISTS `opm_activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `action` enum('created','updated','deleted','bitbucket_notification_received','github_notification_received') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `log_type` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `log_type_title` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `log_type_id` int NOT NULL DEFAULT '0',
  `changes` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `log_for` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `log_for_id` int NOT NULL DEFAULT '0',
  `log_for2` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `log_for_id2` int DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `log_for` (`log_for`,`log_for_id`),
  KEY `log_for2` (`log_for2`,`log_for_id2`),
  KEY `log_type` (`log_type`,`log_type_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_announcements
CREATE TABLE IF NOT EXISTS `opm_announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT 'general',
  `priority` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT 'normal',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `share_with` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_by` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_article_helpful_status
CREATE TABLE IF NOT EXISTS `opm_article_helpful_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `status` enum('yes','no') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_attendance
CREATE TABLE IF NOT EXISTS `opm_attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` enum('incomplete','pending','approved','rejected','deleted') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'incomplete',
  `user_id` int NOT NULL,
  `in_time` datetime NOT NULL,
  `out_time` datetime DEFAULT NULL,
  `checked_by` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `checked_at` datetime DEFAULT NULL,
  `reject_reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `checked_by` (`checked_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_automation_settings
CREATE TABLE IF NOT EXISTS `opm_automation_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `matching_type` enum('match_any','match_all') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `event_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `conditions` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `actions` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `related_to` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_checklist_groups
CREATE TABLE IF NOT EXISTS `opm_checklist_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `checklists` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_checklist_items
CREATE TABLE IF NOT EXISTS `opm_checklist_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_checked` int NOT NULL DEFAULT '0',
  `task_id` int NOT NULL DEFAULT '0',
  `sort` double NOT NULL DEFAULT '1',
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_checklist_template
CREATE TABLE IF NOT EXISTS `opm_checklist_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_ci_sessions
CREATE TABLE IF NOT EXISTS `opm_ci_sessions` (
  `id` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_clients
CREATE TABLE IF NOT EXISTS `opm_clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` enum('organization','person') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'organization',
  `address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `city` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `zip` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `website` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `starred_by` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `group_ids` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_lead` tinyint(1) NOT NULL DEFAULT '0',
  `lead_status_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `created_by` int NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `lead_source_id` int NOT NULL,
  `last_lead_status` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `client_migration_date` date DEFAULT NULL,
  `vat_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `gst_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `stripe_customer_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `stripe_card_ending_digit` int NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `disable_online_payment` tinyint(1) NOT NULL DEFAULT '0',
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `managers` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `created_by` (`created_by`),
  KEY `lead_source_id` (`lead_source_id`),
  KEY `is_lead` (`is_lead`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_client_groups
CREATE TABLE IF NOT EXISTS `opm_client_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `department_id` int DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_client_wallet
CREATE TABLE IF NOT EXISTS `opm_client_wallet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `payment_date` date NOT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `client_id` int NOT NULL,
  `created_by` int DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_company
CREATE TABLE IF NOT EXISTS `opm_company` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `phone` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `website` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vat_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `gst_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `logo` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_contracts
CREATE TABLE IF NOT EXISTS `opm_contracts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `client_id` int NOT NULL,
  `project_id` int NOT NULL,
  `contract_date` date NOT NULL,
  `valid_until` date NOT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `last_email_sent_date` date DEFAULT NULL,
  `status` enum('draft','sent','accepted','declined') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_amount` double NOT NULL,
  `discount_amount_type` enum('percentage','fixed_amount') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `public_key` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `accepted_by` int NOT NULL DEFAULT '0',
  `staff_signed_by` int NOT NULL DEFAULT '0',
  `meta_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_contract_items
CREATE TABLE IF NOT EXISTS `opm_contract_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `contract_id` int NOT NULL,
  `item_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_contract_templates
CREATE TABLE IF NOT EXISTS `opm_contract_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_custom_fields
CREATE TABLE IF NOT EXISTS `opm_custom_fields` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title_language_key` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `placeholder_language_key` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `show_in_embedded_form` tinyint NOT NULL DEFAULT '0',
  `placeholder` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template_variable_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `options` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `field_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `related_to` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `add_filter` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_table` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_invoice` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_estimate` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_contract` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_order` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_proposal` tinyint(1) NOT NULL DEFAULT '0',
  `visible_to_admins_only` tinyint(1) NOT NULL DEFAULT '0',
  `hide_from_clients` tinyint(1) NOT NULL DEFAULT '0',
  `disable_editing_by_clients` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_kanban_card` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_subscription` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `related_to` (`related_to`),
  KEY `field_type` (`field_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_custom_field_values
CREATE TABLE IF NOT EXISTS `opm_custom_field_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `related_to_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `related_to_id` int NOT NULL,
  `custom_field_id` int NOT NULL,
  `value` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `related_to_type` (`related_to_type`),
  KEY `related_to_id` (`related_to_id`),
  KEY `custom_field_id` (`custom_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_custom_widgets
CREATE TABLE IF NOT EXISTS `opm_custom_widgets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `content` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `show_title` tinyint(1) NOT NULL DEFAULT '0',
  `show_border` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_dashboards
CREATE TABLE IF NOT EXISTS `opm_dashboards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `color` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_departments
CREATE TABLE IF NOT EXISTS `opm_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `color` varchar(7) COLLATE utf8mb3_unicode_ci DEFAULT '#4CAF50',
  `icon` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT 'ti ti-building',
  `is_active` tinyint(1) DEFAULT '1',
  `head_user_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `head_user_id` (`head_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_department_permissions
CREATE TABLE IF NOT EXISTS `opm_department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `permission_type` enum('view','manage','admin') COLLATE utf8mb3_unicode_ci DEFAULT 'view',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_dept_perm_unique` (`user_id`,`department_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_department_templates
CREATE TABLE IF NOT EXISTS `opm_department_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_description` text COLLATE utf8mb4_unicode_ci,
  `template_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d',
  `template_permissions` text COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_email_templates
CREATE TABLE IF NOT EXISTS `opm_email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email_subject` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `default_message` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `custom_message` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `template_type` enum('default','custom') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'default',
  `language` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_estimates
CREATE TABLE IF NOT EXISTS `opm_estimates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `estimate_request_id` int NOT NULL DEFAULT '0',
  `estimate_date` date NOT NULL,
  `valid_until` date NOT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `last_email_sent_date` date DEFAULT NULL,
  `status` enum('draft','sent','accepted','declined') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_amount` double NOT NULL,
  `discount_amount_type` enum('percentage','fixed_amount') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `project_id` int NOT NULL DEFAULT '0',
  `accepted_by` int NOT NULL DEFAULT '0',
  `meta_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_by` int NOT NULL,
  `signature` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `public_key` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_estimate_comments
CREATE TABLE IF NOT EXISTS `opm_estimate_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estimate_id` int NOT NULL DEFAULT '0',
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_estimate_forms
CREATE TABLE IF NOT EXISTS `opm_estimate_forms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `assigned_to` int NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `enable_attachment` tinyint NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_estimate_items
CREATE TABLE IF NOT EXISTS `opm_estimate_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `estimate_id` int NOT NULL,
  `item_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_estimate_requests
CREATE TABLE IF NOT EXISTS `opm_estimate_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estimate_form_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `client_id` int NOT NULL,
  `lead_id` int NOT NULL,
  `assigned_to` int NOT NULL,
  `status` enum('new','processing','hold','canceled','estimated') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'new',
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_events
CREATE TABLE IF NOT EXISTS `opm_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_by` int NOT NULL,
  `location` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `client_id` int NOT NULL DEFAULT '0',
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `share_with` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `editable_google_event` tinyint(1) NOT NULL DEFAULT '0',
  `google_event_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `lead_id` int NOT NULL DEFAULT '0',
  `ticket_id` int NOT NULL DEFAULT '0',
  `project_id` int NOT NULL DEFAULT '0',
  `task_id` int NOT NULL DEFAULT '0',
  `proposal_id` int NOT NULL DEFAULT '0',
  `contract_id` int NOT NULL DEFAULT '0',
  `subscription_id` int NOT NULL DEFAULT '0',
  `invoice_id` int NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `estimate_id` int NOT NULL DEFAULT '0',
  `related_user_id` int NOT NULL DEFAULT '0',
  `next_recurring_time` datetime DEFAULT NULL,
  `no_of_cycles_completed` int NOT NULL DEFAULT '0',
  `snoozing_time` datetime DEFAULT NULL,
  `reminder_status` enum('new','shown','done') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'new',
  `type` enum('event','reminder') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'event',
  `color` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `recurring` int NOT NULL DEFAULT '0',
  `repeat_every` int NOT NULL DEFAULT '0',
  `repeat_type` enum('days','weeks','months','years') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT '0',
  `last_start_date` date DEFAULT NULL,
  `recurring_dates` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `confirmed_by` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `rejected_by` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `recurring` (`recurring`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_event_tracker
CREATE TABLE IF NOT EXISTS `opm_event_tracker` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context_id` int NOT NULL,
  `read_count` int DEFAULT NULL,
  `status` enum('new','read') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'new',
  `last_read_time` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `logs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `random_id` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_expenses
CREATE TABLE IF NOT EXISTS `opm_expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `category_id` int NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `amount` double NOT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `project_id` int NOT NULL DEFAULT '0',
  `department_id` int DEFAULT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `client_id` int NOT NULL DEFAULT '0',
  `recurring` tinyint NOT NULL DEFAULT '0',
  `recurring_expense_id` tinyint NOT NULL DEFAULT '0',
  `repeat_every` int NOT NULL DEFAULT '0',
  `repeat_type` enum('days','weeks','months','years') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT '0',
  `next_recurring_date` date DEFAULT NULL,
  `no_of_cycles_completed` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_expense_categories
CREATE TABLE IF NOT EXISTS `opm_expense_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_e_invoice_templates
CREATE TABLE IF NOT EXISTS `opm_e_invoice_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_file_category
CREATE TABLE IF NOT EXISTS `opm_file_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `type` enum('project') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'project',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_folders
CREATE TABLE IF NOT EXISTS `opm_folders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `folder_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parent_id` int NOT NULL,
  `level` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `permissions` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `context` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context_id` int NOT NULL,
  `starred_by` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_general_files
CREATE TABLE IF NOT EXISTS `opm_general_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `service_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `file_size` double NOT NULL,
  `created_at` datetime NOT NULL,
  `client_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `uploaded_by` int NOT NULL,
  `folder_id` int DEFAULT '0',
  `context` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context_id` int DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_help_articles
CREATE TABLE IF NOT EXISTS `opm_help_articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `total_views` int NOT NULL DEFAULT '0',
  `sort` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_help_categories
CREATE TABLE IF NOT EXISTS `opm_help_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` enum('help','knowledge_base') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `articles_order` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `related_articles` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `banner_image` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `banner_url` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_invoices
CREATE TABLE IF NOT EXISTS `opm_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('invoice','credit_note') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'invoice',
  `client_id` int NOT NULL,
  `project_id` int NOT NULL DEFAULT '0',
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `last_email_sent_date` date DEFAULT NULL,
  `status` enum('draft','not_paid','cancelled','credited') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `tax_id3` int NOT NULL DEFAULT '0',
  `recurring` tinyint NOT NULL DEFAULT '0',
  `recurring_invoice_id` int NOT NULL DEFAULT '0',
  `repeat_every` int NOT NULL DEFAULT '0',
  `repeat_type` enum('days','weeks','months','years') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT '0',
  `next_recurring_date` date DEFAULT NULL,
  `no_of_cycles_completed` int NOT NULL DEFAULT '0',
  `due_reminder_date` date DEFAULT NULL,
  `recurring_reminder_date` date DEFAULT NULL,
  `discount_amount` double NOT NULL,
  `discount_amount_type` enum('percentage','fixed_amount') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int NOT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `estimate_id` int NOT NULL DEFAULT '0',
  `main_invoice_id` int NOT NULL DEFAULT '0',
  `subscription_id` int NOT NULL DEFAULT '0',
  `invoice_total` double NOT NULL,
  `invoice_subtotal` double NOT NULL,
  `discount_total` double NOT NULL,
  `tax` double NOT NULL,
  `tax2` double NOT NULL,
  `tax3` double NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `display_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `number_year` int DEFAULT NULL,
  `number_sequence` int DEFAULT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `due_date` (`due_date`),
  KEY `client_id` (`client_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_invoice_items
CREATE TABLE IF NOT EXISTS `opm_invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `invoice_id` int NOT NULL,
  `item_id` int NOT NULL DEFAULT '0',
  `taxable` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_invoice_payments
CREATE TABLE IF NOT EXISTS `opm_invoice_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method_id` int NOT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `invoice_id` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `transaction_id` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_by` int DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_items
CREATE TABLE IF NOT EXISTS `opm_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `show_in_client_portal` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int NOT NULL,
  `taxable` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_item_categories
CREATE TABLE IF NOT EXISTS `opm_item_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_labels
CREATE TABLE IF NOT EXISTS `opm_labels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context` enum('event','invoice','note','project','task','ticket','to_do','subscription','client','help','knowledge_base') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `context` (`context`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_lead_source
CREATE TABLE IF NOT EXISTS `opm_lead_source` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_lead_status
CREATE TABLE IF NOT EXISTS `opm_lead_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_leave_applications
CREATE TABLE IF NOT EXISTS `opm_leave_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leave_type_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_hours` decimal(7,2) NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `applicant_id` int NOT NULL,
  `reason` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','canceled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `checked_at` datetime DEFAULT NULL,
  `checked_by` int NOT NULL DEFAULT '0',
  `files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `user_id` (`applicant_id`),
  KEY `checked_by` (`checked_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_leave_types
CREATE TABLE IF NOT EXISTS `opm_leave_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_likes
CREATE TABLE IF NOT EXISTS `opm_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_comment_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_messages
CREATE TABLE IF NOT EXISTS `opm_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Untitled',
  `message` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `status` enum('unread','read') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'unread',
  `message_id` int NOT NULL DEFAULT '0',
  `deleted` int NOT NULL DEFAULT '0',
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted_by_users` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_from` (`from_user_id`),
  KEY `message_to` (`to_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_milestones
CREATE TABLE IF NOT EXISTS `opm_milestones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `project_id` int NOT NULL,
  `due_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_notes
CREATE TABLE IF NOT EXISTS `opm_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `project_id` int NOT NULL DEFAULT '0',
  `client_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int DEFAULT '0',
  `color` varchar(14) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_note_category
CREATE TABLE IF NOT EXISTS `opm_note_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_notifications
CREATE TABLE IF NOT EXISTS `opm_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `notify_to` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_by` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `event` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `project_id` int NOT NULL,
  `task_id` int NOT NULL,
  `project_comment_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `ticket_comment_id` int NOT NULL,
  `project_file_id` int NOT NULL,
  `leave_id` int NOT NULL,
  `post_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `activity_log_id` int NOT NULL,
  `client_id` int NOT NULL,
  `lead_id` int NOT NULL,
  `invoice_payment_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `estimate_id` int NOT NULL,
  `contract_id` int NOT NULL,
  `order_id` int NOT NULL,
  `estimate_request_id` int NOT NULL,
  `actual_message_id` int NOT NULL,
  `parent_message_id` int NOT NULL,
  `event_id` int NOT NULL,
  `announcement_id` int NOT NULL,
  `proposal_id` int NOT NULL,
  `estimate_comment_id` int NOT NULL,
  `subscription_id` int NOT NULL,
  `expense_id` int NOT NULL,
  `proposal_comment_id` int NOT NULL,
  `reminder_log_id` int NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_notification_settings
CREATE TABLE IF NOT EXISTS `opm_notification_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `enable_email` int NOT NULL DEFAULT '0',
  `enable_web` int NOT NULL DEFAULT '0',
  `enable_slack` int NOT NULL DEFAULT '0',
  `notify_to_team` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `notify_to_team_members` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `notify_to_terms` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_orders
CREATE TABLE IF NOT EXISTS `opm_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `order_date` date NOT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status_id` int NOT NULL,
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `discount_amount` double NOT NULL,
  `discount_amount_type` enum('percentage','fixed_amount') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `project_id` int NOT NULL DEFAULT '0',
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_by_hash` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_order_items
CREATE TABLE IF NOT EXISTS `opm_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `order_id` int NOT NULL,
  `created_by` int NOT NULL,
  `item_id` int NOT NULL DEFAULT '0',
  `sort` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_by_hash` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_order_status
CREATE TABLE IF NOT EXISTS `opm_order_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_pages
CREATE TABLE IF NOT EXISTS `opm_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `slug` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `internal_use_only` tinyint(1) NOT NULL DEFAULT '0',
  `visible_to_team_members_only` tinyint(1) NOT NULL DEFAULT '0',
  `visible_to_clients_only` tinyint(1) NOT NULL DEFAULT '0',
  `full_width` tinyint(1) NOT NULL DEFAULT '0',
  `hide_topbar` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_payment_methods
CREATE TABLE IF NOT EXISTS `opm_payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'custom',
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `online_payable` tinyint(1) NOT NULL DEFAULT '0',
  `available_on_invoice` tinyint(1) NOT NULL DEFAULT '0',
  `minimum_payment_amount` double NOT NULL DEFAULT '0',
  `settings` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_paypal_ipn
CREATE TABLE IF NOT EXISTS `opm_paypal_ipn` (
  `id` int NOT NULL AUTO_INCREMENT,
  `verification_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payment_verification_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `invoice_id` int NOT NULL,
  `contact_user_id` int NOT NULL,
  `client_id` int NOT NULL,
  `payment_method_id` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_pin_comments
CREATE TABLE IF NOT EXISTS `opm_pin_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_comment_id` int NOT NULL DEFAULT '0',
  `ticket_comment_id` int NOT NULL DEFAULT '0',
  `pinned_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_posts
CREATE TABLE IF NOT EXISTS `opm_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `post_id` int NOT NULL,
  `share_with` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_projects
CREATE TABLE IF NOT EXISTS `opm_projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `project_type` enum('client_project','internal_project') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'client_project',
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `client_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `department_filter_enabled` tinyint(1) DEFAULT '1',
  `created_date` date DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `status` enum('open','completed','hold','canceled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'open',
  `status_id` int NOT NULL DEFAULT '1',
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `price` double NOT NULL DEFAULT '0',
  `starred_by` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estimate_id` int NOT NULL,
  `order_id` int NOT NULL,
  `proposal_id` int DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `status_id` (`status_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_comments
CREATE TABLE IF NOT EXISTS `opm_project_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `project_id` int NOT NULL DEFAULT '0',
  `comment_id` int NOT NULL DEFAULT '0',
  `task_id` int NOT NULL DEFAULT '0',
  `file_id` int NOT NULL DEFAULT '0',
  `customer_feedback_id` int NOT NULL DEFAULT '0',
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_files
CREATE TABLE IF NOT EXISTS `opm_project_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `service_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `file_size` double NOT NULL,
  `created_at` datetime NOT NULL,
  `project_id` int NOT NULL,
  `uploaded_by` int NOT NULL,
  `category_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `folder_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_members
CREATE TABLE IF NOT EXISTS `opm_project_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `project_id` int NOT NULL,
  `is_leader` tinyint(1) DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_settings
CREATE TABLE IF NOT EXISTS `opm_project_settings` (
  `project_id` int NOT NULL,
  `setting_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `unique_index` (`project_id`,`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_status
CREATE TABLE IF NOT EXISTS `opm_project_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title_language_key` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `key_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_project_time
CREATE TABLE IF NOT EXISTS `opm_project_time` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `user_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `hours` float NOT NULL,
  `status` enum('open','logged','approved') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'logged',
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `task_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_proposals
CREATE TABLE IF NOT EXISTS `opm_proposals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `proposal_date` date NOT NULL,
  `valid_until` date NOT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `last_email_sent_date` date DEFAULT NULL,
  `status` enum('draft','sent','accepted','declined') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_amount` double NOT NULL,
  `discount_amount_type` enum('percentage','fixed_amount') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `public_key` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `accepted_by` int NOT NULL DEFAULT '0',
  `created_by` int NOT NULL DEFAULT '0',
  `total_views` int NOT NULL DEFAULT '0',
  `last_preview_seen` datetime DEFAULT NULL,
  `meta_data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `project_id` int DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_proposal_comments
CREATE TABLE IF NOT EXISTS `opm_proposal_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `proposal_id` int NOT NULL DEFAULT '0',
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_proposal_items
CREATE TABLE IF NOT EXISTS `opm_proposal_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `proposal_id` int NOT NULL,
  `item_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_proposal_templates
CREATE TABLE IF NOT EXISTS `opm_proposal_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_reminder_logs
CREATE TABLE IF NOT EXISTS `opm_reminder_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `context` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `context_id` int NOT NULL,
  `reminder_event` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notification_status` enum('draft','completed') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `reminder_date` date DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_reminder_settings
CREATE TABLE IF NOT EXISTS `opm_reminder_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'app',
  `context` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `reminder_event` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `reminder1` int DEFAULT NULL,
  `reminder2` int DEFAULT NULL,
  `reminder3` int DEFAULT NULL,
  `reminder4` int DEFAULT NULL,
  `reminder5` int DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_roles
CREATE TABLE IF NOT EXISTS `opm_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `permissions` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_settings
CREATE TABLE IF NOT EXISTS `opm_settings` (
  `setting_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'app',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_social_links
CREATE TABLE IF NOT EXISTS `opm_social_links` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `facebook` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `twitter` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `linkedin` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `googleplus` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `digg` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `youtube` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `pinterest` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `instagram` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `github` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `tumblr` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `vine` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `whatsapp` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_stripe_ipn
CREATE TABLE IF NOT EXISTS `opm_stripe_ipn` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `verification_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payment_verification_code` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `invoice_id` int NOT NULL,
  `contact_user_id` int NOT NULL,
  `client_id` int NOT NULL,
  `payment_method_id` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `setup_intent` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `subscription_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_subscriptions
CREATE TABLE IF NOT EXISTS `opm_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `client_id` int NOT NULL,
  `bill_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('draft','pending','active','cancelled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_status` enum('success','failed') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'success',
  `tax_id` int NOT NULL DEFAULT '0',
  `tax_id2` int NOT NULL DEFAULT '0',
  `repeat_every` int NOT NULL DEFAULT '1',
  `repeat_type` enum('days','weeks','months','years') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT '0',
  `next_recurring_date` date DEFAULT NULL,
  `no_of_cycles_completed` int NOT NULL DEFAULT '0',
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int NOT NULL,
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('app','stripe') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'app',
  `stripe_subscription_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `stripe_product_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `stripe_product_price_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_subscription_items
CREATE TABLE IF NOT EXISTS `opm_subscription_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` double NOT NULL,
  `unit_type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rate` double NOT NULL,
  `total` double NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `subscription_id` int NOT NULL,
  `item_id` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_tasks
CREATE TABLE IF NOT EXISTS `opm_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `project_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `milestone_id` int NOT NULL DEFAULT '0',
  `assigned_to` int NOT NULL,
  `deadline` datetime DEFAULT NULL,
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `points` tinyint NOT NULL DEFAULT '1',
  `status` enum('to_do','in_progress','done') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'to_do',
  `status_id` int NOT NULL,
  `priority_id` int NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `collaborators` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL DEFAULT '0',
  `recurring` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_every` int NOT NULL DEFAULT '0',
  `repeat_type` enum('days','weeks','months','years') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `no_of_cycles` int NOT NULL DEFAULT '0',
  `recurring_task_id` int NOT NULL DEFAULT '0',
  `no_of_cycles_completed` int NOT NULL DEFAULT '0',
  `created_date` date DEFAULT NULL,
  `blocking` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `blocked_by` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parent_task_id` int NOT NULL,
  `next_recurring_date` date DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `ticket_id` int NOT NULL,
  `status_changed_at` datetime DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `expense_id` int NOT NULL DEFAULT '0',
  `subscription_id` int NOT NULL DEFAULT '0',
  `proposal_id` int NOT NULL DEFAULT '0',
  `contract_id` int NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `estimate_id` int NOT NULL DEFAULT '0',
  `invoice_id` int NOT NULL DEFAULT '0',
  `lead_id` int NOT NULL DEFAULT '0',
  `client_id` int NOT NULL DEFAULT '0',
  `context` enum('project','client','lead','invoice','estimate','order','contract','proposal','subscription','ticket','expense','department','general') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'general',
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_id` (`status_id`),
  KEY `priority_id` (`priority_id`),
  KEY `sort` (`sort`),
  KEY `project_id` (`project_id`),
  KEY `milestone_id` (`milestone_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `ticket_id` (`ticket_id`),
  KEY `client_id` (`client_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `estimate_id` (`estimate_id`),
  KEY `order_id` (`order_id`),
  KEY `contract_id` (`contract_id`),
  KEY `proposal_id` (`proposal_id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `expense_id` (`expense_id`),
  KEY `lead_id` (`lead_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_task_priority
CREATE TABLE IF NOT EXISTS `opm_task_priority` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icon` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_task_status
CREATE TABLE IF NOT EXISTS `opm_task_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `key_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` int NOT NULL,
  `hide_from_kanban` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `hide_from_non_project_related_tasks` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_taxes
CREATE TABLE IF NOT EXISTS `opm_taxes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `percentage` double NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `stripe_tax_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_team
CREATE TABLE IF NOT EXISTS `opm_team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `members` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_team_member_job_info
CREATE TABLE IF NOT EXISTS `opm_team_member_job_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `salary` double NOT NULL DEFAULT '0',
  `salary_term` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_job_info` (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_tickets
CREATE TABLE IF NOT EXISTS `opm_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `project_id` int NOT NULL DEFAULT '0',
  `ticket_type_id` int NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_by` int NOT NULL,
  `requested_by` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `status` enum('new','client_replied','open','closed') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'new',
  `last_activity_at` datetime DEFAULT NULL,
  `assigned_to` int NOT NULL DEFAULT '0',
  `creator_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `creator_email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `task_id` int NOT NULL,
  `closed_at` datetime NOT NULL,
  `merged_with_ticket_id` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `cc_contacts_and_emails` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `client_last_activity_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `ticket_type_id` (`ticket_type_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_ticket_comments
CREATE TABLE IF NOT EXISTS `opm_ticket_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ticket_id` int NOT NULL,
  `files` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `is_note` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_ticket_templates
CREATE TABLE IF NOT EXISTS `opm_ticket_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ticket_type_id` int NOT NULL,
  `private` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_ticket_types
CREATE TABLE IF NOT EXISTS `opm_ticket_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_to_do
CREATE TABLE IF NOT EXISTS `opm_to_do` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `labels` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` enum('to_do','done') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'to_do',
  `start_date` date DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `files` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort` double NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_users
CREATE TABLE IF NOT EXISTS `opm_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_type` enum('staff','client','lead') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'client',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` int NOT NULL DEFAULT '0',
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `message_checked_at` datetime DEFAULT NULL,
  `client_id` int NOT NULL DEFAULT '0',
  `notification_checked_at` datetime DEFAULT NULL,
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT '0',
  `job_title` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Untitled',
  `disable_login` tinyint(1) NOT NULL DEFAULT '0',
  `note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `alternative_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alternative_phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `ssn` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sticky_note` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `skype` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `language` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `enable_web_notification` tinyint(1) NOT NULL DEFAULT '1',
  `enable_email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `last_online` datetime DEFAULT NULL,
  `requested_account_removal` tinyint(1) NOT NULL DEFAULT '0',
  `client_permissions` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`),
  KEY `email` (`email`),
  KEY `client_id` (`client_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_user_departments
CREATE TABLE IF NOT EXISTS `opm_user_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_dept_unique` (`user_id`,`department_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_verification
CREATE TABLE IF NOT EXISTS `opm_verification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` enum('invoice_payment','reset_password','verify_email','invitation') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `params` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_documents
CREATE TABLE IF NOT EXISTS `opm_workflow_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `document_name` varchar(200) NOT NULL,
  `document_type` enum('bill_of_lading','commercial_invoice','packing_list','customs_declaration','tax_certificate','permits','inspection_report','other') NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `uploaded_by` int DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `opm_workflow_documents_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opm_workflow_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_shipments
CREATE TABLE IF NOT EXISTS `opm_workflow_shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_number` varchar(50) NOT NULL,
  `client_id` int NOT NULL,
  `cargo_type` varchar(100) NOT NULL,
  `cargo_weight` decimal(10,2) DEFAULT NULL,
  `cargo_value` decimal(12,2) DEFAULT NULL,
  `origin_port` varchar(100) DEFAULT NULL,
  `destination_port` varchar(100) DEFAULT NULL,
  `final_destination` varchar(200) DEFAULT NULL,
  `estimated_arrival` datetime DEFAULT NULL,
  `actual_arrival` datetime DEFAULT NULL,
  `current_phase` enum('clearing_intake','regulatory_processing','internal_review','transport_loading','tracking') DEFAULT 'clearing_intake',
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipment_number` (`shipment_number`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_tasks
CREATE TABLE IF NOT EXISTS `opm_workflow_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int DEFAULT NULL,
  `workflow_type` enum('shipment','document','tracking','truck') DEFAULT 'shipment',
  `reference_id` int DEFAULT NULL,
  `shipment_id` int NOT NULL,
  `task_name` varchar(200) NOT NULL,
  `task_description` text,
  `assigned_to` int DEFAULT NULL,
  `phase` enum('clearing_intake','regulatory_processing','internal_review','transport_loading','tracking') NOT NULL,
  `status` enum('pending','in_progress','completed','on_hold') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `idx_task_id` (`task_id`),
  CONSTRAINT `opm_workflow_tasks_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opm_workflow_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_tracking
CREATE TABLE IF NOT EXISTS `opm_workflow_tracking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `location` varchar(200) NOT NULL,
  `status_update` text,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `opm_workflow_tracking_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opm_workflow_tracking_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_trucks
CREATE TABLE IF NOT EXISTS `opm_workflow_trucks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `truck_number` varchar(50) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `driver_phone` varchar(20) DEFAULT NULL,
  `truck_capacity` decimal(8,2) DEFAULT NULL,
  `truck_type` enum('container','flatbed','tanker','refrigerated') NOT NULL,
  `status` enum('available','assigned','in_transit','maintenance') DEFAULT 'available',
  `current_location` varchar(200) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `truck_number` (`truck_number`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table overland_pm_workflow.opm_workflow_truck_allocations
CREATE TABLE IF NOT EXISTS `opm_workflow_truck_allocations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_id` int NOT NULL,
  `allocated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `status` enum('allocated','in_transit','delivered','cancelled') DEFAULT 'allocated',
  `mileage_start` decimal(8,2) DEFAULT NULL,
  `mileage_end` decimal(8,2) DEFAULT NULL,
  `fuel_cost` decimal(8,2) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `truck_id` (`truck_id`),
  CONSTRAINT `opm_workflow_truck_allocations_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opm_workflow_truck_allocations_ibfk_2` FOREIGN KEY (`truck_id`) REFERENCES `opm_workflow_trucks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

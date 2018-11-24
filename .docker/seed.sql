-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Nov 24, 2018 at 09:05 AM
-- Server version: 5.6.42
-- PHP Version: 7.2.8

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `cake`
--
DROP DATABASE IF EXISTS `cake`;
CREATE DATABASE `cake` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cake`;
-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
                        `id` int(11) NOT NULL,
                        `parent_id` int(11) DEFAULT NULL,
                        `lft` int(11) NOT NULL,
                        `rght` int(11) NOT NULL,
                        `name` varchar(45) DEFAULT NULL,
                        `added_by_user_id` int(11) DEFAULT NULL,
                        `created` datetime NOT NULL,
                        `modified` datetime NOT NULL,
                        `deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `parent_id`, `lft`, `rght`, `name`, `added_by_user_id`, `created`, `modified`, `deleted`) VALUES
(1, NULL, 1, 16, 'Gruene', NULL, '2018-11-23 00:00:00', '2018-11-23 09:59:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups_logos`
--

DROP TABLE IF EXISTS `groups_logos`;
CREATE TABLE `groups_logos` (
                              `id` int(11) NOT NULL,
                              `logo_id` int(11) NOT NULL,
                              `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups_logos`
--

INSERT INTO `groups_logos` (`id`, `logo_id`, `group_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
                        `id` int(11) NOT NULL,
                        `user_id` int(11) NOT NULL,
                        `filename` varchar(255) DEFAULT NULL,
                        `original_id` int(11) DEFAULT NULL,
                        `width` float DEFAULT NULL,
                        `height` float DEFAULT NULL,
                        `flattext` text,
                        `hash` varchar(40) DEFAULT NULL,
                        `legal` text,
                        `reusable` tinyint(1) DEFAULT NULL,
                        `logo_id` int(11) DEFAULT NULL,
                        `created` datetime NOT NULL,
                        `modified` datetime NOT NULL,
                        `deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login_hashes`
--

DROP TABLE IF EXISTS `login_hashes`;
CREATE TABLE `login_hashes` (
                              `id` int(11) NOT NULL,
                              `user_id` int(11) NOT NULL,
                              `type` varchar(12) NOT NULL,
                              `selector` varchar(32) NOT NULL,
                              `token` varchar(64) NOT NULL,
                              `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
                            `id` int(11) NOT NULL,
                            `email` varchar(120) NOT NULL,
                            `ip` varchar(32) NOT NULL,
                            `successful` tinyint(1) NOT NULL,
                            `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `logos`
--

DROP TABLE IF EXISTS `logos`;
CREATE TABLE `logos` (
                       `id` int(11) NOT NULL,
                       `top_path` varchar(255) DEFAULT NULL,
                       `subline` varchar(255) DEFAULT NULL,
                       `name` varchar(255) DEFAULT NULL,
                       `added_by_user_id` int(11) DEFAULT NULL,
                       `created` datetime NOT NULL,
                       `modified` datetime NOT NULL,
                       `deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logos`
--

INSERT INTO `logos` (`id`, `top_path`, `subline`, `name`, `added_by_user_id`, `created`, `modified`, `deleted`) VALUES
(1, '/protected/logos/gruene.svg', 'gruene.ch', 'gruene.ch', 1, '2018-11-23 00:00:00', '2018-11-23 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                       `id` int(11) NOT NULL,
                       `first_name` varchar(60) DEFAULT NULL,
                       `last_name` varchar(60) DEFAULT NULL,
                       `email` varchar(120) NOT NULL,
                       `password` varchar(255) NOT NULL,
                       `added_by_user_id` int(11) DEFAULT NULL,
                       `super_admin` tinyint(1) DEFAULT NULL,
                       `lang` varchar(2) DEFAULT NULL,
                       `managed_by_group_id` int(11) NOT NULL,
                       `login_count` int(11) NOT NULL DEFAULT '0',
                       `last_login` datetime DEFAULT NULL,
                       `created` datetime NOT NULL,
                       `modified` datetime NOT NULL,
                       `deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `added_by_user_id`, `super_admin`, `lang`, `managed_by_group_id`, `login_count`, `last_login`, `created`, `modified`, `deleted`) VALUES
(1, 'Dev', 'Eloper', 'admin@admin.admin', '$2y$10$xINpYMYYnO4fyt06TIU2MOj6/7R3ZkJEguLXf3EboVaOArM01JxfS', 1, 1, 'de', 1, 1, '2018-11-23 21:44:59', '2018-11-23 00:00:00', '2018-11-23 21:44:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
                              `id` int(11) NOT NULL,
                              `group_id` int(11) NOT NULL,
                              `user_id` int(11) NOT NULL,
                              `added_by_user_id` int(11) DEFAULT NULL,
                              `admin` tinyint(1) DEFAULT NULL,
                              `created` datetime NOT NULL,
                              `modified` datetime NOT NULL,
                              `deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `group_id`, `user_id`, `added_by_user_id`, `admin`, `created`, `modified`, `deleted`) VALUES
(1, 1, 1, 1, 1, '2018-11-23 00:00:00', '2018-11-23 00:00:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups_logos`
--
ALTER TABLE `groups_logos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_groups_logos_logos_idx` (`logo_id`),
  ADD KEY `fk_groups_logos_groups1_idx` (`group_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_users1_idx` (`user_id`),
  ADD KEY `idx_hash` (`hash`),
  ADD KEY `fk_images_logos1_idx` (`logo_id`);

--
-- Indexes for table `login_hashes`
--
ALTER TABLE `login_hashes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector_UNIQUE` (`selector`),
  ADD KEY `selector_idx` (`selector`),
  ADD KEY `fk_login_hashes_users1_idx` (`user_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_idx` (`email`),
  ADD KEY `ip_idx` (`ip`),
  ADD KEY `created_idx` (`created`);

--
-- Indexes for table `logos`
--
ALTER TABLE `logos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_groups1_idx` (`managed_by_group_id`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_groups_groups1_idx` (`group_id`),
  ADD KEY `fk_users_groups_users1_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `groups_logos`
--
ALTER TABLE `groups_logos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_hashes`
--
ALTER TABLE `login_hashes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logos`
--
ALTER TABLE `logos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups_logos`
--
ALTER TABLE `groups_logos`
  ADD CONSTRAINT `fk_groups_logos_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_groups_logos_logos1` FOREIGN KEY (`logo_id`) REFERENCES `logos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_logos1` FOREIGN KEY (`logo_id`) REFERENCES `logos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_images_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `login_hashes`
--
ALTER TABLE `login_hashes`
  ADD CONSTRAINT `fk_login_hashes_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_groups1` FOREIGN KEY (`managed_by_group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

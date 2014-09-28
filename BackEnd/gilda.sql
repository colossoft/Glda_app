-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2014 at 05:16 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gilda`
--

-- --------------------------------------------------------

--
-- Table structure for table `gilda_devaluation`
--

CREATE TABLE IF NOT EXISTS `gilda_devaluation` (
`id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `text` longtext NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `languageId` int(11) NOT NULL,
  `devaluationId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gilda_events`
--

CREATE TABLE IF NOT EXISTS `gilda_events` (
`id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `end_time` varchar(5) NOT NULL,
  `trainer` int(11) NOT NULL,
  `training` int(11) NOT NULL,
  `spots` int(3) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `gilda_events`
--

INSERT INTO `gilda_events` (`id`, `room_id`, `date`, `start_time`, `end_time`, `trainer`, `training`, `spots`) VALUES
(1, 1, '2014-09-01', '07:00', '08:00', 1, 12, 30),
(2, 1, '2014-09-01', '12:00', '13:00', 8, 1, 1),
(3, 1, '2014-09-01', '18:00', '19:00', 6, 8, 25),
(4, 1, '2014-09-01', '19:00', '20:00', 6, 1, 10),
(5, 1, '2014-09-20', '20:00', '21:00', 19, 7, 10),
(6, 1, '2014-09-22', '10:00', '11:00', 9, 4, 0),
(7, 1, '2014-09-02', '07:00', '08:00', 2, 1, 32),
(8, 1, '2014-09-02', '11:00', '12:00', 14, 10, 12),
(9, 1, '2014-09-02', '16:00', '17:00', 18, 6, 20),
(10, 1, '2014-09-02', '17:00', '18:00', 18, 3, 25),
(11, 1, '2014-09-02', '18:00', '19:00', 3, 1, 40),
(12, 1, '2014-09-02', '19:00', '20:00', 3, 9, 13),
(13, 1, '2014-09-02', '20:00', '21:00', 20, 11, 30),
(14, 1, '2014-09-03', '10:00', '11:00', 2, 2, 25),
(15, 1, '2014-09-03', '17:00', '18:00', 6, 8, 20),
(16, 1, '2014-09-03', '18:00', '19:00', 6, 1, 32),
(17, 1, '2014-09-04', '07:00', '08:00', 13, 1, 40),
(18, 1, '2014-09-23', '16:00', '17:00', 2, 12, 23),
(19, 1, '2014-09-04', '17:00', '18:00', 2, 5, 13),
(20, 1, '2014-09-04', '18:00', '19:00', 12, 1, 27),
(21, 1, '2014-09-04', '19:00', '20:00', 10, 9, 30),
(22, 1, '2014-09-04', '20:00', '21:00', 11, 1, 22),
(23, 1, '2014-09-05', '07:00', '08:00', 4, 12, 10),
(24, 1, '2014-09-05', '09:00', '10:00', 2, 1, 15),
(25, 1, '2014-09-05', '10:00', '11:00', 9, 4, 20),
(26, 1, '2014-09-05', '12:00', '13:00', 8, 1, 20),
(27, 1, '2014-09-05', '16:00', '17:00', 14, 2, 25),
(28, 1, '2014-09-05', '17:00', '18:00', 14, 13, 23),
(29, 1, '2014-09-05', '18:00', '19:00', 8, 12, 30),
(30, 1, '2014-09-05', '19:00', '20:00', 7, 1, 16),
(31, 1, '2014-09-05', '20:00', '21:00', 7, 7, 28),
(32, 1, '2014-09-06', '10:00', '11:00', 17, 2, 0),
(33, 1, '2014-09-06', '11:00', '12:00', 17, 4, 15),
(34, 1, '2014-09-06', '18:00', '19:00', 14, 1, 25),
(35, 1, '2014-09-07', '10:00', '11:00', 2, 1, 15),
(36, 1, '2014-09-07', '11:00', '12:00', 5, 7, 30),
(37, 1, '2014-09-07', '17:00', '18:00', 16, 2, 18),
(38, 1, '2014-09-07', '18:00', '19:00', 15, 1, 23),
(39, 1, '0000-00-00', '12:00', '13:00', 19, 2, 10),
(40, 1, '2014-10-01', '12:00', '13:00', 20, 1, 30),
(41, 4, '2014-09-30', '18:00', '19:00', 9, 2, 15);

-- --------------------------------------------------------

--
-- Table structure for table `gilda_language`
--

CREATE TABLE IF NOT EXISTS `gilda_language` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gilda_language`
--

INSERT INTO `gilda_language` (`id`, `name`) VALUES
(1, 'Magyar'),
(2, 'Angol'),
(3, 'Német');

-- --------------------------------------------------------

--
-- Table structure for table `gilda_locations`
--

CREATE TABLE IF NOT EXISTS `gilda_locations` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `gilda_locations`
--

INSERT INTO `gilda_locations` (`id`, `name`, `address`, `latitude`, `longitude`) VALUES
(1, 'Allee', '1117 Budapest, Október huszonharmadika u. 8-10.', '47.475080', '19.049067'),
(2, 'Óbuda Gate', '1023 Budapest, Árpád Fejedelem útja 26-28.', '47.524042', '19.038660'),
(3, 'River Estates', '1134 Budapest, Váci Út 35.', '47.525773', '19.064170'),
(4, 'Hermina Towers', '1146 Budapest, Hungária Krt. 162-166.', '47.513764', '19.094762'),
(5, 'Flórián', '1033 Budapest, Flórián Tér 6.', '47.542371', '19.040138'),
(6, 'Savoya Park', '1117 Budapest, Hunyadi János Út 19.', '47.434985', '19.042670');

-- --------------------------------------------------------

--
-- Table structure for table `gilda_news`
--

CREATE TABLE IF NOT EXISTS `gilda_news` (
`id` int(11) NOT NULL,
  `newsId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `newsText` longtext NOT NULL,
  `created_date` date NOT NULL,
  `languageId` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `gilda_news`
--

INSERT INTO `gilda_news` (`id`, `newsId`, `title`, `newsText`, `created_date`, `languageId`) VALUES
(7, 1, 'Harmadik hir', 'Harmadik hir text', '2014-09-26', 3),
(6, 1, 'Masodik hir', 'Masodik hir text', '2014-09-26', 2),
(5, 1, 'Elso hir', 'Elso hir text', '2014-09-26', 1),
(8, 2, 'Elso hir', 'Elso hir text', '2014-09-26', 1),
(9, 2, 'Masodik hir', 'Masodik hir text', '2014-09-26', 2),
(10, 2, 'Harmadik hir', 'Harmadik hir text', '2014-09-26', 3);

-- --------------------------------------------------------

--
-- Table structure for table `gilda_reservations`
--

CREATE TABLE IF NOT EXISTS `gilda_reservations` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `gilda_reservations`
--

INSERT INTO `gilda_reservations` (`id`, `user_id`, `event_id`) VALUES
(21, 31, 3),
(23, 34, 4),
(26, 34, 2),
(27, 31, 36),
(30, 31, 1),
(31, 31, 21),
(32, 34, 3),
(33, 35, 3),
(35, 34, 1),
(36, 35, 1),
(37, 35, 12),
(38, 35, 14),
(39, 37, 5),
(40, 37, 6),
(41, 37, 18);

-- --------------------------------------------------------

--
-- Table structure for table `gilda_rooms`
--

CREATE TABLE IF NOT EXISTS `gilda_rooms` (
`id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `gilda_rooms`
--

INSERT INTO `gilda_rooms` (`id`, `location_id`, `name`) VALUES
(1, 1, 'Aerobic nagyterem'),
(2, 1, 'Aerobic Kisterem'),
(3, 1, 'Boxterem'),
(4, 2, 'Aerobic nagyterem'),
(5, 2, 'Aerobic Kisterem'),
(6, 2, 'Spinning nagyterem'),
(7, 3, 'Aerobic terem'),
(8, 3, 'Funkcionális terem'),
(9, 3, 'Kisterem'),
(10, 4, 'Aerobic terem'),
(11, 4, 'Kisterem'),
(12, 5, 'Aerobic terem'),
(13, 5, 'Boxterem'),
(14, 5, 'Funkcionális terem'),
(15, 6, 'Aerobic terem'),
(16, 6, 'Body & Mind terem'),
(17, 6, 'Spinning terem'),
(18, 6, 'Boxterem'),
(19, 6, 'Crossfit terem');

-- --------------------------------------------------------

--
-- Table structure for table `gilda_trainer`
--

CREATE TABLE IF NOT EXISTS `gilda_trainer` (
`id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `gilda_trainer`
--

INSERT INTO `gilda_trainer` (`id`, `first_name`, `email`, `last_name`) VALUES
(1, 'Réka', '', 'Béres'),
(2, 'Fanny', '', 'Dobos'),
(3, 'Kriszti', '', 'Dósa'),
(4, 'Eszter', '', 'Dugonics'),
(5, 'Kitti', '', 'Fazekas'),
(6, 'Bence', '', 'Gárdus'),
(7, 'Anikó', '', 'Gyurkovics'),
(8, 'Claudia', '', 'Klobusitzky'),
(9, 'Évi', '', 'Kun'),
(10, 'Esztella', '', 'Nyárádi'),
(11, 'Alexa', '', 'Rácz'),
(12, 'Kriszti', '', 'Samu'),
(13, 'Kata', '', 'Vörös'),
(14, 'Ági', '', 'Vajda'),
(15, 'Brigi', '', 'Szabó'),
(16, 'Kitti', '', 'Szabó'),
(17, 'Luca', '', 'Valóczy'),
(18, 'Andi', '', 'Szalka'),
(19, 'Tímea', '', 'Sifter'),
(20, 'Enikő', '', 'Sólyom');

-- --------------------------------------------------------

--
-- Table structure for table `gilda_training`
--

CREATE TABLE IF NOT EXISTS `gilda_training` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `gilda_training`
--

INSERT INTO `gilda_training` (`id`, `name`) VALUES
(1, 'Alakformáló'),
(2, 'Has-Láb-Popsi'),
(3, 'Hot Iron'),
(4, 'Hot Iron 1'),
(5, 'Hot Iron 2'),
(6, 'Intervall'),
(7, 'Kangoo'),
(8, 'Kickbox aerobic'),
(9, 'Pilates'),
(10, 'Pocakos torna'),
(11, 'Stepmánia'),
(12, 'Zsírégető'),
(13, 'Zsírégető box');

-- --------------------------------------------------------

--
-- Table structure for table `gilda_user`
--

CREATE TABLE IF NOT EXISTS `gilda_user` (
`id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `gilda_user`
--

INSERT INTO `gilda_user` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `api_key`, `status`, `created_at`) VALUES
(37, 'Valaki', '0', 'atyins@gmail.com', '$2a$10$f7b0c5ce20c2f69ba3f9buxIBXW1hMkARBQoTIWaGnxEUyNHo6tV6', '11a05dff2907cb7e8abc00847dea1c19', 3, '2014-09-03 15:33:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gilda_devaluation`
--
ALTER TABLE `gilda_devaluation`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_events`
--
ALTER TABLE `gilda_events`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_language`
--
ALTER TABLE `gilda_language`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_locations`
--
ALTER TABLE `gilda_locations`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_news`
--
ALTER TABLE `gilda_news`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_reservations`
--
ALTER TABLE `gilda_reservations`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_rooms`
--
ALTER TABLE `gilda_rooms`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_trainer`
--
ALTER TABLE `gilda_trainer`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_training`
--
ALTER TABLE `gilda_training`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gilda_user`
--
ALTER TABLE `gilda_user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gilda_devaluation`
--
ALTER TABLE `gilda_devaluation`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `gilda_events`
--
ALTER TABLE `gilda_events`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `gilda_language`
--
ALTER TABLE `gilda_language`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `gilda_locations`
--
ALTER TABLE `gilda_locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `gilda_news`
--
ALTER TABLE `gilda_news`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `gilda_reservations`
--
ALTER TABLE `gilda_reservations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `gilda_rooms`
--
ALTER TABLE `gilda_rooms`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `gilda_trainer`
--
ALTER TABLE `gilda_trainer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `gilda_training`
--
ALTER TABLE `gilda_training`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `gilda_user`
--
ALTER TABLE `gilda_user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

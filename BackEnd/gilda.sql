-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Hoszt: 127.0.0.1
-- Létrehozás ideje: 2014. Okt 04. 18:10
-- Szerver verzió: 5.5.34
-- PHP verzió: 5.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Adatbázis: `gilda`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_devaluation`
--

CREATE TABLE IF NOT EXISTS `gilda_devaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `text` varchar(500) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `languageId` int(11) NOT NULL,
  `devaluationId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_events`
--

CREATE TABLE IF NOT EXISTS `gilda_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(5) NOT NULL,
  `end_time` varchar(5) NOT NULL,
  `trainer` int(11) NOT NULL,
  `training` int(11) NOT NULL,
  `spots` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- A tábla adatainak kiíratása `gilda_events`
--

INSERT INTO `gilda_events` (`id`, `room_id`, `date`, `start_time`, `end_time`, `trainer`, `training`, `spots`) VALUES
(1, 1, '2014-09-01', '07:00', '08:00', 1, 12, 30),
(2, 1, '2014-09-01', '12:00', '13:00', 8, 1, 1),
(3, 1, '2014-09-01', '18:00', '19:00', 6, 8, 25),
(4, 1, '2014-09-01', '19:00', '20:00', 6, 1, 10),
(5, 1, '2014-09-20', '20:00', '21:00', 19, 7, 10),
(6, 1, '2014-09-22', '10:00', '11:00', 9, 4, 5),
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
(40, 1, '2014-10-01', '12:00', '13:00', 20, 1, 30),
(41, 4, '2014-09-30', '18:00', '19:00', 9, 2, 15);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_language`
--

CREATE TABLE IF NOT EXISTS `gilda_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- A tábla adatainak kiíratása `gilda_language`
--

INSERT INTO `gilda_language` (`id`, `name`) VALUES
(1, 'Magyar'),
(2, 'Angol'),
(3, 'Német');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_locations`
--

CREATE TABLE IF NOT EXISTS `gilda_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- A tábla adatainak kiíratása `gilda_locations`
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
-- Tábla szerkezet ehhez a táblához `gilda_log`
--

CREATE TABLE IF NOT EXISTS `gilda_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operation` varchar(500) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_news`
--

CREATE TABLE IF NOT EXISTS `gilda_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `newsText` varchar(500) NOT NULL,
  `created_date` date NOT NULL,
  `languageId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_reservations`
--

CREATE TABLE IF NOT EXISTS `gilda_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_rooms`
--

CREATE TABLE IF NOT EXISTS `gilda_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- A tábla adatainak kiíratása `gilda_rooms`
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
-- Tábla szerkezet ehhez a táblához `gilda_trainer`
--

CREATE TABLE IF NOT EXISTS `gilda_trainer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- A tábla adatainak kiíratása `gilda_trainer`
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
(20, 'Enikő', '', 'Sólyom'),
(21, 'Anita', 'nagy.anita@gmail.com', 'Nagy');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_training`
--

CREATE TABLE IF NOT EXISTS `gilda_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- A tábla adatainak kiíratása `gilda_training`
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
(13, 'Zsírégető box'),
(14, 'Body');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gilda_user`
--

CREATE TABLE IF NOT EXISTS `gilda_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- A tábla adatainak kiíratása `gilda_user`
--

INSERT INTO `gilda_user` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `api_key`, `status`, `created_at`) VALUES
(49, 'Józsi', 'Kamu', 'jozsi@kamu.com', '$2a$10$77549a0a06d39e1ee86e4uZHVwTIjJKRybaBqBFGRZ.WzIL3kp7Ju', 'd7a4b5b931d18276e43400a01646fd39', 1, '2014-10-03 18:41:56'),
(48, 'Ákos', 'Átyin', 'atyins@gmail.com', '$2a$10$5cc0bbfa8304ce6bd2e61uzimvhM5SmsT/GveNa.OjBeMzGzOexMe', 'd0758438913e95c21bdfa6109d780908', 3, '2014-10-03 17:35:48');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

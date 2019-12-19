-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 27. Feb 2019 um 15:10
-- Server-Version: 5.5.56-MariaDB
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `graffiti`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `collaboration`
--

CREATE TABLE `collaboration` (
  `id` int(11) NOT NULL,
  `from` varchar(255) COLLATE utf8_bin NOT NULL,
  `to` varchar(255) COLLATE utf8_bin NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `albumlink` varchar(255) COLLATE utf8_bin NOT NULL,
  `graffiti` varchar(255) COLLATE utf8_bin NOT NULL,
  `surface` varchar(255) COLLATE utf8_bin NOT NULL,
  `collaborators` varchar(255) COLLATE utf8_bin NOT NULL,
  `request_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `galleries`
--

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL,
  `surface_id` int(11) DEFAULT NULL,
  `key` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gallery_entries`
--

CREATE TABLE `gallery_entries` (
  `id` int(11) NOT NULL,
  `fbid` varchar(255) COLLATE utf8_bin NOT NULL,
  `user` varchar(255) COLLATE utf8_bin NOT NULL,
  `surface` varchar(255) COLLATE utf8_bin NOT NULL,
  `caption` varchar(255) COLLATE utf8_bin NOT NULL,
  `collab_id` int(11) DEFAULT NULL,
  `collaborators` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_bin NOT NULL,
  `path_720` varchar(255) COLLATE utf8_bin NOT NULL,
  `path_icon` varchar(255) COLLATE utf8_bin NOT NULL,
  `path_png` varchar(255) COLLATE utf8_bin NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `image_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gallery_flags`
--

CREATE TABLE `gallery_flags` (
  `id` int(11) NOT NULL,
  `entry_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `user` varchar(255) COLLATE utf8_bin NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gallery_likes`
--

CREATE TABLE `gallery_likes` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_bin NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gallery_picks`
--

CREATE TABLE `gallery_picks` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `pod` datetime DEFAULT NULL,
  `pow` datetime DEFAULT NULL,
  `pom` datetime DEFAULT NULL,
  `poy` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `payment_request`
--

CREATE TABLE `payment_request` (
  `request_id` char(40) NOT NULL,
  `fbid` varchar(255) DEFAULT NULL,
  `item_key` varchar(30) NOT NULL,
  `item_type` varchar(30) NOT NULL,
  `price` double UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `surface`
--

CREATE TABLE `surface` (
  `id` int(11) NOT NULL,
  `key` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '999999',
  `type` varchar(255) NOT NULL DEFAULT 'swat',
  `temp_free` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tool`
--

CREATE TABLE `tool` (
  `id` int(11) NOT NULL,
  `key` varchar(30) COLLATE utf8_bin NOT NULL,
  `children` varchar(255) COLLATE utf8_bin NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '999999'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `fbid` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `lang` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `album_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `album_count` tinyint(4) NOT NULL DEFAULT '0',
  `banned_photos` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `first_login` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `bannerSeen` datetime DEFAULT NULL,
  `notified` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_surface`
--

CREATE TABLE `user_surface` (
  `id` int(11) NOT NULL,
  `fbid` bigint(20) UNSIGNED NOT NULL,
  `sid` int(11) NOT NULL,
  `acquisition` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'granted',
  `event_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_tool`
--

CREATE TABLE `user_tool` (
  `id` int(11) NOT NULL,
  `fbid` bigint(20) UNSIGNED NOT NULL,
  `sid` int(11) NOT NULL,
  `acquisition` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'granted',
  `event_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `order_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `collaboration`
--
ALTER TABLE `collaboration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from` (`from`),
  ADD KEY `to` (`to`),
  ADD KEY `surface` (`surface`);

--
-- Indizes für die Tabelle `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surface_id` (`surface_id`),
  ADD KEY `key` (`key`);

--
-- Indizes für die Tabelle `gallery_entries`
--
ALTER TABLE `gallery_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `user_2` (`user`),
  ADD KEY `surface` (`surface`),
  ADD KEY `collab_id` (`collab_id`);

--
-- Indizes für die Tabelle `gallery_flags`
--
ALTER TABLE `gallery_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indizes für die Tabelle `gallery_likes`
--
ALTER TABLE `gallery_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indizes für die Tabelle `gallery_picks`
--
ALTER TABLE `gallery_picks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indizes für die Tabelle `surface`
--
ALTER TABLE `surface`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key` (`key`);

--
-- Indizes für die Tabelle `tool`
--
ALTER TABLE `tool`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key` (`key`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fbid` (`fbid`),
  ADD KEY `fbid_2` (`fbid`);

--
-- Indizes für die Tabelle `user_surface`
--
ALTER TABLE `user_surface`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fbid` (`fbid`),
  ADD KEY `sid` (`sid`);

--
-- Indizes für die Tabelle `user_tool`
--
ALTER TABLE `user_tool`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fbid` (`fbid`),
  ADD KEY `sid` (`sid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `collaboration`
--
ALTER TABLE `collaboration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gallery_entries`
--
ALTER TABLE `gallery_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gallery_flags`
--
ALTER TABLE `gallery_flags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gallery_likes`
--
ALTER TABLE `gallery_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `gallery_picks`
--
ALTER TABLE `gallery_picks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `surface`
--
ALTER TABLE `surface`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tool`
--
ALTER TABLE `tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user_surface`
--
ALTER TABLE `user_surface`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user_tool`
--
ALTER TABLE `user_tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

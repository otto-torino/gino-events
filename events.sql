--
-- Permissions
--

INSERT INTO `auth_permission` (`class`, `code`, `label`, `description`, `admin`) VALUES
('events', 'can_admin', 'Amministrazione modulo', 'Amministrazione completa di categorie, eventi, opzioni e frontend', 1),
('events', 'can_publish', 'Pubblicazione eventi', 'Pubblicazione degli eventi', 1),
('events', 'can_write', 'Redazione', 'Redazione dei contenuti, categorie ed eventi, no pubblicazione', 1),
('events', 'can_view_private', 'Visualizzazione eventi privati', 'Visualuizzazione degli eventi settati come privati', 0);

--
-- Table structure for table `events_category`
--

CREATE TABLE IF NOT EXISTS `events_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `events_event`
--

CREATE TABLE IF NOT EXISTS `events_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text,
  `tags` varchar(255) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `attachment` varchar(200) DEFAULT NULL,
  `private` int(1) NOT NULL,
  `lat` varchar(64) DEFAULT NULL,
  `lng` varchar(64) DEFAULT NULL,
  `social` int(1) NOT NULL,
  `published` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `events_event_category`
--

CREATE TABLE IF NOT EXISTS `events_event_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `events_opt`
--

CREATE TABLE IF NOT EXISTS `events_opt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `monday_first_week_day` int(1) NOT NULL,
  `day_chars` int(2) NOT NULL,
  `open_link_in_layer` int(1) NOT NULL,
  `ifp` int(3) NOT NULL,
  `showcase_events_number` int(2) NOT NULL,
  `showcase_events_category` int(3) NOT NULL,
  `img_width` int(4) NOT NULL,
  `thumb_width` int(3) NOT NULL,
  `newsletter_events_number` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

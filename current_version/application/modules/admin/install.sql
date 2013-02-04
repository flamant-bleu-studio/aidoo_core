--
-- Structure de la table `1_config`
--

DROP TABLE IF EXISTS `1_config`;
CREATE TABLE `1_config` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `1_config`
--

INSERT INTO `1_config` (`name`, `value`) VALUES
('defaultFrontLang', '1'),
('availableFrontLang', '{"1":"fr"}'),
('defaultBackLang', '1'),
('availableBackLang', '{"1":"fr","2":"en"}'),
('mobileConfig', '{"mobile":"0","tablet":"0"}'),
('logConfig', '{"log_stream":"0","log_stream_min_level":"3","log_mail":"0","log_mail_min_level":"4","log_mail_to":"","log_db":"0","log_db_min_level":"7","log_firebug":"0","log_firebug_min_level":"7"}'),
('configThumbSizes', '{"default":{"name":"default","width":550,"height":350,"adaptiveResize":false},"small":{"name":"small","width":182,"height":136,"adaptiveResize":true},"small2":{"name":"small2","width":165,"height":210,"adaptiveResize":true},"big":{"name":"big","width":400,"height":325,"adaptiveResize":true}}'),
('maintenance', 0),
('activeModule', '["admin", "front", "contact", "users", "sitemap", "skins", "seo", "documents", "blocs", "packager", "articles", "menu", "advertising", "galerieImage"]');

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` text NOT NULL,
  `priorityName` text NOT NULL,
  `message` text NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_hits`
--

DROP TABLE IF EXISTS `1_hits`;
CREATE TABLE `1_hits` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(254) NOT NULL,
  `cle` varchar(1024) NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_permissions`
--

DROP TABLE IF EXISTS `1_permissions`;
CREATE TABLE `1_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `rights` varchar(5120) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('admin', '{"login":["3","4"],"login_middle":[],"manage":[],"view":[]}');

-- --------------------------------------------------------

--
-- Structure de la table `1_view_access`
--

DROP TABLE IF EXISTS `1_view_access`;
CREATE TABLE `1_view_access` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `groups` varchar(5120) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `1_view_access`
--

INSERT INTO `1_view_access` (`id`, `name`, `groups`) VALUES
(1, 'Tous', '[1,2,3,4]'),
(2, 'Registered uniquement', '[1,3,4]'),
(3, 'Visiteurs uniquement', '[2]');

-- --------------------------------------------------------

--
-- Structure de la table `1_activities`
--

DROP TABLE IF EXISTS `1_activities`;
CREATE TABLE IF NOT EXISTS `1_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activityClass` varchar(1024) NOT NULL,
  `activityId` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_core_pages`
--

DROP TABLE IF EXISTS `1_core_pages`;
CREATE TABLE IF NOT EXISTS `1_core_pages` (
  `id_page` int(11) NOT NULL AUTO_INCREMENT,
  `url_system` varchar(512) CHARACTER SET utf8 NOT NULL,
  `enable` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `wildcard` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(254) NOT NULL,
  `api` varchar(254) CHARACTER SET utf8 DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `template` int(11) DEFAULT NULL,
  `diaporama` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_core_pages_lang`
--

DROP TABLE IF EXISTS `1_core_pages_lang`;
CREATE TABLE IF NOT EXISTS `1_core_pages_lang` (
  `id_page` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(10) NOT NULL,
  `title` varchar(254) CHARACTER SET utf8 DEFAULT NULL,
  `url_rewrite` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `meta_keywords` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_page`,`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Structure de la table `1_core_pages_types`
--

DROP TABLE IF EXISTS `1_core_pages_types`;
CREATE TABLE `1_core_pages_types` (
  `id_type` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(256) NOT NULL,
  `parent_type` varchar(256) DEFAULT NULL,
  `default_tpl` int(11) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(512) NOT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Ajout de la page d'acceuil
--

INSERT INTO `1_core_pages` (`id_page`, `url_system`, `enable`, `visible`, `wildcard`, `type`, `api`, `content_id`, `template`, `diaporama`, `date_add`, `date_upd`) VALUES
(1, '', 1, 0, 0, '', NULL, NULL, NULL, NULL, '', '');

INSERT INTO `1_core_pages_lang` (`id_page`, `id_lang`, `title`, `url_rewrite`, `meta_keywords`, `meta_description`) VALUES
(1, 1, 'Accueil', NULL, '', '');

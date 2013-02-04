--
-- Structure de la table `1_galeries`
--

DROP TABLE IF EXISTS `1_galeries`;
CREATE TABLE `1_galeries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` enum('1','2') NOT NULL,
  `nb_image` int(11) NOT NULL,
  `access` int(11) DEFAULT NULL,
  `bg_color` varchar(7) CHARACTER SET utf8 DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `style` tinyint(2) DEFAULT '0',
  `transition` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `controls_position` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `controls_style` tinyint(1) DEFAULT '0',
  `autostart` tinyint(1) DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_galeries_images`
--

DROP TABLE IF EXISTS `1_galeries_images`;
CREATE TABLE `1_galeries_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `isPermanent` tinyint(1) NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `path` varchar(254) CHARACTER SET utf8 NOT NULL,
  `path_thumb` varchar(254) CHARACTER SET utf8 NOT NULL,
  `bg_color` varchar(7) CHARACTER SET utf8 DEFAULT NULL,
  `datas` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('galerieImage', NULL, NULL, 'Module Galerie Photos', 'Toutes les galeries photos');

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_galeriePhoto', '{"manage":[],"view":[],"create":[]}'),
('mod_galeriePhoto-default', '{"manage":[],"edit":[],"delete":[],"create":[]}'),
('mod_diaporama', '{"manage":[],"view":["3","4"],"create":["3","4"]}'),
('mod_diaporama-default', '{"manage":[],"edit":["3","4"],"delete":["3","4"],"create":["3","4"]}');

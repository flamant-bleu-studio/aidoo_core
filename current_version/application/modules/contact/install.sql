--
-- Structure de la table `1_contact`
--

DROP TABLE IF EXISTS `1_contact`;
CREATE TABLE IF NOT EXISTS `1_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `emails` text,
  `emailsCci` text,
  `content` text,
  `auto_response` text,
  `save_data` tinyint(1) DEFAULT '1',
  `typeSelect` tinyint(1) DEFAULT '0',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `1_contact_save`;
CREATE TABLE IF NOT EXISTS `1_contact_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `key` varchar(255),
  `value` text,
  `num_send` text,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_contact', '{"manage":[],"view":[], "editOption":[], "export":[]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('contact', NULL, NULL, 'Module Contact', 'Toutes pages de contact');

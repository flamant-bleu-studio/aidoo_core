--
-- Structure de la table `1_campaign`
--

DROP TABLE IF EXISTS `1_campaign`;
CREATE TABLE `1_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `limited` tinyint(1) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `1_campaign_advert`
--

DROP TABLE IF EXISTS `1_campaign_advert`;
CREATE TABLE `1_campaign_advert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `datas` text CHARACTER SET ucs2 NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_advertising', '{"manage":[],"view":["3","4"],"edit":["3","4"],"create":["3","4"],"delete":["3","4"]}'),
('mod_advertising-default', '{"manage":[],"view":["3","4"],"edit":["3","4"],"create":["3","4"],"delete":["3","4"]}');

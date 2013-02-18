--
-- Structure de la table `1_menu`
--

DROP TABLE IF EXISTS `1_menu`;
CREATE TABLE `1_menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `link` varchar(254) CHARACTER SET latin1 DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `hidetitle` tinyint(1) NOT NULL DEFAULT '0',
  `loadAjax` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `tblank` tinyint(1) NOT NULL DEFAULT '0',
  `cssClass` varchar(50) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Structure de la table `1_menu_lang`
--

DROP TABLE IF EXISTS `1_menu_lang`;
CREATE TABLE `1_menu_lang` (
  `id_menu` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `label` varchar(254) NOT NULL,
  `subtitle` varchar(254) DEFAULT NULL,
  PRIMARY KEY (`id_menu`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_menu', '{"manage":[],"view":["3","4"],"create":[]}'),
('mod_menu-menu-default', '{"manage":[],"view":["3","4"],"edit":["3"],"delete":[],"insert":["3","4"] }'),
('mod_menu-item-default', '{"manage":[],"view":["3","4"],"fullview":[],"move":["3","4"],"edit":["3","4"],"delete":["3","4"]}');

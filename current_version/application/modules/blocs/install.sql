--
-- Structure de la table `1_core_templates`
--
DROP TABLE IF EXISTS `1_core_templates`;
CREATE TABLE IF NOT EXISTS `1_core_templates` (
  `id_template` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(254) NOT NULL,
  `defaut` tinyint(1) NOT NULL DEFAULT '0',
  `theme` varchar(50) DEFAULT NULL,
  `classCss` varchar(254) DEFAULT NULL,
  `bgType` tinyint(4) DEFAULT NULL,
  `bgColor1` varchar(10) DEFAULT NULL,
  `bgColor2` varchar(10) DEFAULT NULL,
  `bgGradient` tinyint(4) DEFAULT NULL,
  `bgPicture` varchar(254) DEFAULT NULL,
  `bgRepeat` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id_template`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `1_core_templates`
--

INSERT INTO `1_core_templates` (`id_template`, `title`, `defaut`) VALUES
(1, 'général', 1);

-- --------------------------------------------------------

--
-- Structure de la table `1_core_templates_items`
--

DROP TABLE IF EXISTS `1_core_templates_items`;
CREATE TABLE IF NOT EXISTS `1_core_templates_items` (
  `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `templateFront` varchar(150) NOT NULL,
  `decorator` varchar(150) NOT NULL,
  `classCss` varchar(50) DEFAULT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `type` varchar(254) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_core_templates_items_lang`
--

DROP TABLE IF EXISTS `1_core_templates_items_lang`;
CREATE TABLE IF NOT EXISTS `1_core_templates_items_lang` (
  `id_item` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `designation` varchar(100) NOT NULL,
  `title` varchar(254) NOT NULL,
  `params` text,
  PRIMARY KEY (`id_item`,`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `1_core_templates_map`
--

DROP TABLE IF EXISTS `1_core_templates_map`;
CREATE TABLE IF NOT EXISTS `1_core_templates_map` (
  `id_template_map` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `template_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `item_id` int(11) NOT NULL,
  `placeholder` varchar(254) NOT NULL,
  PRIMARY KEY (`id_template_map`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_bloc', '{"manage":[],"view":["3"],"viewBlocs":[3],"createBlocs":["3"],"createTemplates":["3"],"editTemplates":["3"],"deleteTemplates":["3"]}'),
('mod_bloc-default', '{"manage":[],"edit":["3"],"delete":["3"]}');

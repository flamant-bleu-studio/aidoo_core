-- --------------------------------------------------------

--
-- Structure de la table `1_faq`
--

DROP TABLE IF EXISTS `1_faq`;
CREATE TABLE IF NOT EXISTS `1_faq` (
  `id_faq` int(11) NOT NULL AUTO_INCREMENT,
  `access` tinyint(3) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_faq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Structure de la table `1_faq_lang`
--

DROP TABLE IF EXISTS `1_faq_lang`;
CREATE TABLE IF NOT EXISTS `1_faq_lang` (
  `id_faq` int(11) NOT NULL,
  `id_lang` tinyint(4) NOT NULL,
  `title` varchar(254) NOT NULL,
  PRIMARY KEY (`id_faq`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `1_faq_items`
--

DROP TABLE IF EXISTS `1_faq_items`;
CREATE TABLE IF NOT EXISTS `1_faq_items` (
  `id_faq_item` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `question_order` smallint(8) NOT NULL,
  PRIMARY KEY (`id_faq_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_faq_items_lang`
--

DROP TABLE IF EXISTS `1_faq_items_lang`;
CREATE TABLE IF NOT EXISTS `1_faq_items_lang` (
  `id_faq_item` int(11) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id_faq_item`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_faq', '{"manage":[],"view":[], "create":[]}'),
('mod_faq-default', '{"manage":[],"view":["3","4"],"edit":["3","4"]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('faq', NULL, NULL, 'Module Faq', '');

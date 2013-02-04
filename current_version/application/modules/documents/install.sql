
--
-- Structure de la table `1_documents`
--

DROP TABLE IF EXISTS `1_documents`;
CREATE TABLE IF NOT EXISTS `1_documents` (
  `id_document` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `template` varchar(50) NOT NULL,
  `author` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_document`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_documents_lang`
--

DROP TABLE IF EXISTS `1_documents_lang`;
CREATE TABLE IF NOT EXISTS `1_documents_lang` (
  `id_document` int(10) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `title` varchar(254) NOT NULL,
  PRIMARY KEY (`id_document`,`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `1_documents_nodes`
--

DROP TABLE IF EXISTS `1_documents_nodes`;
CREATE TABLE IF NOT EXISTS `1_documents_nodes` (
  `id_node` int(10) NOT NULL AUTO_INCREMENT,
  `id_document` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_node`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_documents_nodes_lang`
--

DROP TABLE IF EXISTS `1_documents_nodes_lang`;
CREATE TABLE IF NOT EXISTS `1_documents_nodes_lang` (
  `id_node` int(10) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `value` text,
  PRIMARY KEY (`id_node`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_documents', '{"manage":[],"view":["3","4"],"create":["3","4"]}'),
('mod_documents-default', '{"manage":[],"edit":["3","4"],"delete":["3","4"]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('document', NULL, NULL, 'Module Contenus Divers', 'Toutes les contenus divers');

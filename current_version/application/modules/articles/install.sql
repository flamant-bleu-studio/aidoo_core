-- --------------------------------------------------------

--
-- Structure de la table `1_articles`
--

DROP TABLE IF EXISTS `1_articles`;
CREATE TABLE `1_articles` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `template` varchar(50) NOT NULL,
  `readmore` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(512) DEFAULT NULL,
  `isPermanent` tinyint(1) NOT NULL DEFAULT '1',
  `author` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `isSubmitted` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `fb_comments_active` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_categories`
--

DROP TABLE IF EXISTS `1_articles_categories`;
CREATE TABLE `1_articles_categories` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `countByPage` int(11) DEFAULT NULL,
  `typeView` INT( 10 ) DEFAULT NULL,
  `image` VARCHAR( 512 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `fb_comments_number_show` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_categories_lang`
--

DROP TABLE IF EXISTS `1_articles_categories_lang`;
CREATE TABLE `1_articles_categories_lang` (
  `id_categorie` int(11) NOT NULL,
  `id_lang` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id_categorie`,`id_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_categories_map`
--

DROP TABLE IF EXISTS `1_articles_categories_map`;
CREATE TABLE `1_articles_categories_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_lang`
--

DROP TABLE IF EXISTS `1_articles_lang`;
CREATE TABLE `1_articles_lang` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(10) NOT NULL,
  `title` varchar(254) NOT NULL,
  `chapeau` text,
  PRIMARY KEY (`id_article`,`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_nodes`
--

DROP TABLE IF EXISTS `1_articles_nodes`;
CREATE TABLE `1_articles_nodes` (
  `id_node` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_node`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_articles_nodes_lang`
--

DROP TABLE IF EXISTS `1_articles_nodes_lang`;
CREATE TABLE `1_articles_nodes_lang` (
  `id_node` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `value` text,
  PRIMARY KEY (`id_node`,`id_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_articles', '{"manage":[],"view":["3","4"],"create":["3","4"]}'),
('mod_articles-default', '{"manage":[],"edit":["3","4"],"delete":["3","4"]}'),
('mod_categories', '{"manage":[],"view":["3","4"],"create":[]}'),
('mod_categories-default', '{"manage":[],"edit":["3","4"],"delete":["3","4"],"create":["3","4"]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('articles', NULL, NULL, 'Module Articles', ''),
('articles-article', 'articles', NULL, 'Pages articles', 'Liste des articles du module Article'),
('articles-categorie', 'articles', NULL, 'Pages catégorie', 'Liste des catégorie du module Article'),
('articles-middle', 'articles', NULL, 'Middle office article', '');

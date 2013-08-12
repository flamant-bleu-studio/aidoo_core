
--
-- Structure de la table `1_diaporamas`
--

DROP TABLE IF EXISTS `1_diaporamas`;
CREATE TABLE IF NOT EXISTS `1_diaporamas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `1_diaporamas_images`
--

DROP TABLE IF EXISTS `1_diaporamas_images`;
CREATE TABLE IF NOT EXISTS `1_diaporamas_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `text` text,
  `background_color` varchar(7) DEFAULT NULL,
  `image` text NOT NULL,
  `link_type` tinyint(3) DEFAULT '0',
  `link_internal` int(11) DEFAULT NULL,
  `link_external` text,
  `link_target_blank` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- UPDATE `1_diaporamas_images` SET image=REPLACE(image, '/resources/cms_local/upload/', '');

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  `id_facebook` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  `isConfirm` tinyint(1) NOT NULL DEFAULT '0',
  `civility` varchar(50) DEFAULT NULL COMMENT 'Default : M, Mme, Mlle',
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `users` (`id`, `group`, `email`, `password`, `isActive`, `isConfirm`, `civility`, `firstname`, `lastname`) VALUES
(1, 1, 'admin@demo.com', 'SHA1PASSWORD', 1, 1, 'M', 'Admin', 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL auto_increment,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `user_groups` (`id`, `lft`, `rgt`, `name`) VALUES
(1, 2, 3, 'Superadmin'),
(2, 1, 6, 'Public'),
(3, 4, 5, 'Clients');


-- --------------------------------------------------------

--
-- Structure de la table `user_metas`
--

DROP TABLE IF EXISTS `user_metas`;
CREATE TABLE `user_metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meta` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_users', '{"manage":[],"view":[]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('users', NULL, NULL, 'Module Users', 'Pages relatives aux comptes membres'),
('users-middle', 'users', NULL, 'Middle office user', ''),
('users-front', 'users', NULL, 'Front office user', '');

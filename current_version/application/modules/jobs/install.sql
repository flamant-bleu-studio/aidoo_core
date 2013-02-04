--
-- Structure de la table `1_jobs`
--

DROP TABLE IF EXISTS `1_jobs`;
CREATE TABLE `1_jobs` (
  `id` int(11) NOT NULL auto_increment,
  `job_title` tinytext NOT NULL,
  `contract_type` varchar(150) NOT NULL,
  `sector` varchar(200) NOT NULL,
  `domain` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `contact` tinytext NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_jobs', '{"manage":[],"view":["3"],"edit":["3"],"create":["3"],"delete":["3"]}'),
('mod_jobs-default', '{"manage":[],"view":["3"],"edit":["3"],"create":["3"],"delete":["3"]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('jobs', NULL, NULL, 'Module Jobs', NULL),
('jobs-view', 'jobs', NULL, 'Fiches', 'Fiches annonces'),
('jobs-apply', 'jobs', NULL, 'Postuler', 'Formulaires postuler'),
('jobs-list', 'jobs', NULL, 'Listes', 'Page listing des annonces');

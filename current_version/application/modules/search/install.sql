--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_searchengine', '{"manage":[],"view":[]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('search', NULL, NULL, 'Recherche', 'Page des résultats de recherche sur le site');

--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_sitemap', '{"manage":[],"view":[]}');

INSERT INTO `1_core_pages_types` (`type`, `parent_type`, `default_tpl`, `name`, `description`) VALUES
('sitemap', NULL, NULL, 'Sitemap', 'Sitemap du site');

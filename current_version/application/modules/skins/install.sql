--
-- Ajout des permissions dans la table `1_permissions`
--

INSERT INTO `1_permissions` (`name`, `rights`) VALUES
('mod_skins', '{"manage":[],"view":[],"change":[]}');

-- --------------------------------------------------------

--
-- Ajout de la config dans la table `1_config`
--

INSERT INTO `1_config` (`name`, `value`) VALUES
('skinfront', 'undefined'),
('skinback', 'adminCMS');

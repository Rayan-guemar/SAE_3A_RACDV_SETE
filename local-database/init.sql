-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 22 nov. 2023 à 18:55
-- Version du serveur : 10.5.15-MariaDB-0+deb11u1
-- Version de PHP : 8.1.13


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `macet`
--

-- --------------------------------------------------------

--
-- Structure de la table `creneaux`
--

CREATE TABLE `creneaux` (
  `id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `creneaux`
--

INSERT INTO `creneaux` (`id`, `date_debut`, `date_fin`) VALUES
(1, '2023-10-26 10:05:10', '2023-10-26 10:05:10'),
(4, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(5, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(6, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(7, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(8, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(9, '2023-10-10 13:38:00', '2023-10-10 16:38:00'),
(10, '2023-10-10 12:00:00', '2023-10-10 14:00:00'),
(11, '2023-10-11 14:46:00', '2023-10-11 17:46:00'),
(12, '2023-10-11 16:49:00', '2023-10-11 20:49:00'),
(13, '2023-10-11 16:56:00', '2023-10-11 19:57:00'),
(14, '2023-10-11 16:56:00', '2023-10-11 19:57:00'),
(15, '2023-10-11 16:56:00', '2023-10-11 19:57:00'),
(16, '2023-10-11 18:33:00', '2023-10-11 21:33:00'),
(17, '2023-10-31 14:45:00', '2023-10-31 19:45:00'),
(18, '2023-10-30 01:34:00', '2023-10-30 05:35:00'),
(19, '2023-10-28 00:36:00', '2023-10-28 02:36:00'),
(20, '2023-10-29 00:37:00', '2023-10-31 03:37:00'),
(21, '2023-10-30 01:43:00', '2023-10-30 04:43:00'),
(22, '2023-10-31 06:46:00', '2023-10-31 10:46:00'),
(23, '2023-10-30 01:49:00', '2023-10-30 06:49:00'),
(24, '2023-10-28 00:49:00', '2023-10-28 09:49:00'),
(25, '2023-10-31 03:50:00', '2023-10-31 07:50:00'),
(26, '2023-10-30 10:23:00', '2023-10-30 14:23:00'),
(27, '2023-10-31 15:26:00', '2023-10-31 18:26:00'),
(28, '2023-10-29 10:30:00', '2023-10-29 12:30:00'),
(29, '2023-10-28 11:29:00', '2023-10-28 14:29:00'),
(30, '2023-10-31 18:29:00', '2023-10-31 21:29:00'),
(31, '2023-10-28 02:33:00', '2023-10-28 07:33:00'),
(32, '2023-10-29 21:42:00', '2023-10-31 23:42:00'),
(33, '2023-10-29 17:05:00', '2023-10-29 19:17:00'),
(34, '2023-10-31 16:06:00', '2023-11-01 16:07:00'),
(35, '2023-10-30 02:15:00', '2023-10-30 04:45:00'),
(36, '2023-10-29 01:52:00', '2023-10-29 03:52:00'),
(37, '2023-11-01 00:08:00', '2023-11-01 03:09:00'),
(38, '2023-11-01 02:08:00', '2023-11-01 05:09:00'),
(39, '2023-11-01 12:08:00', '2023-11-01 16:09:00'),
(40, '2023-10-10 03:02:00', '2023-10-10 09:02:00'),
(41, '2023-10-10 18:00:00', '2023-10-11 21:00:00'),
(42, '2023-10-10 18:00:00', '2023-10-10 19:00:00'),
(43, '2023-10-10 18:00:00', '2023-10-10 19:00:00'),
(44, '2023-11-02 00:00:00', '2023-11-02 14:00:00'),
(45, '2023-10-28 01:00:00', '2023-10-28 03:00:00'),
(46, '2023-11-07 02:00:00', '2023-11-08 00:03:00'),
(47, '2023-11-07 06:00:00', '2023-11-07 07:10:00'),
(48, '2023-11-07 00:05:00', '2023-11-07 01:00:00'),
(49, '2023-10-29 00:00:00', '2023-10-29 04:00:00'),
(50, '2023-10-30 00:00:00', '2023-10-30 14:00:00'),
(51, '2023-10-30 16:00:00', '2023-10-30 17:00:00'),
(52, '2023-10-31 03:00:00', '2023-10-31 04:00:00'),
(53, '2023-10-31 04:00:00', '2023-10-31 05:00:00'),
(54, '2023-10-11 02:00:00', '2023-10-11 04:00:00'),
(55, '2023-10-29 06:00:00', '2023-10-29 10:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_benevole`
--

CREATE TABLE `demandes_benevole` (
  `utilisateur_id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande_festival`
--

CREATE TABLE `demande_festival` (
  `id` int(11) NOT NULL,
  `organisateur_festival_id` int(11) NOT NULL,
  `nom_festival` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_debut_festival` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `date_fin_festival` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `description_festival` longtext COLLATE utf8_unicode_ci NOT NULL,
  `lieu_festival` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `affiche_festival` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `tags` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `demande_festival`
--

INSERT INTO `demande_festival` (`id`, `organisateur_festival_id`, `nom_festival`, `date_debut_festival`, `date_fin_festival`, `description_festival`, `lieu_festival`, `affiche_festival`, `lat`, `lon`, `tags`) VALUES
(7, 6, 'hoooooyeah fest', '2023-11-22 00:00:00', '2023-11-23 00:00:00', 'zdjneojkez v eeinifcneinzi', 'Andören, Raseborg, Uusimaa, Finland', '-655e2fbcded89.png', 59.8789289, 23.672894012427, 'R&R,R&B,House');

-- --------------------------------------------------------

--
-- Structure de la table `disponibilite`
--

CREATE TABLE `disponibilite` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL,
  `creneau_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20231026222659', '2023-10-26 22:27:04', 556),
('DoctrineMigrations\\Version20231027161015', '2023-10-27 16:10:21', 201),
('DoctrineMigrations\\Version20231027161117', '2023-10-27 16:11:22', 58),
('DoctrineMigrations\\Version20231030175654', '2023-10-30 17:57:03', 151),
('DoctrineMigrations\\Version20231102151333', '2023-11-02 15:13:56', 352),
('DoctrineMigrations\\Version20231102212429', '2023-11-02 21:28:02', 176),
('DoctrineMigrations\\Version20231102213548', '2023-11-02 21:35:55', 54),
('DoctrineMigrations\\Version20231103094316', '2023-11-03 09:43:25', 305),
('DoctrineMigrations\\Version20231103095611', '2023-11-03 09:56:21', 80),
('DoctrineMigrations\\Version20231120122521', '2023-11-20 12:25:30', 52),
('DoctrineMigrations\\Version20231121075205', '2023-11-21 07:52:25', 62),
('DoctrineMigrations\\Version20231121083555', '2023-11-21 08:36:36', 60),
('DoctrineMigrations\\Version20231121092907', '2023-11-21 10:30:44', 379),
('DoctrineMigrations\\Version20231121095801', '2023-11-21 09:58:18', 305),
('DoctrineMigrations\\Version20231121162801', '2023-11-21 16:28:15', 257),
('DoctrineMigrations\\Version20231122091721', '2023-11-22 09:17:39', 471);

-- --------------------------------------------------------

--
-- Structure de la table `est_benevole`
--

CREATE TABLE `est_benevole` (
  `utilisateur_id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `est_benevole`
--

INSERT INTO `est_benevole` (`utilisateur_id`, `festival_id`) VALUES
(3, 12),
(3, 18),
(3, 21),
(6, 12),
(21, 12),
(21, 18),
(21, 20),
(23, 18),
(23, 21),
(33, 18);

-- --------------------------------------------------------

--
-- Structure de la table `est_responsable`
--

CREATE TABLE `est_responsable` (
  `utilisateur_id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `est_responsable`
--

INSERT INTO `est_responsable` (`utilisateur_id`, `festival_id`) VALUES
(3, 12),
(23, 18),
(33, 18);

-- --------------------------------------------------------

--
-- Structure de la table `festival`
--

CREATE TABLE `festival` (
  `id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `organisateur_id` int(11) NOT NULL,
  `lieu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `affiche` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `is_archive` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `festival`
--

INSERT INTO `festival` (`id`, `date_debut`, `date_fin`, `nom`, `description`, `organisateur_id`, `lieu`, `affiche`, `lat`, `lon`, `is_archive`) VALUES
(12, '2023-10-10 00:00:00', '2023-10-11 00:00:00', 'ici sétois', 'Le lorem ipsum est, en imprimerie, une suite de mots sans signification utilisée à titre provisoire pour calibrer une mise en page, le texte définitif venant remplacer le faux-texte dès qu\'il est prêt ou que la mise en page est achevée. Généralement, on utilise un texte en faux latin, le Lorem ipsum ou Lipsum', 6, 'Théâtre de la Mer, Promenade du Maréchal Leclerc, 34200 Sète, France', '-6524636289eb8.jpg', NULL, NULL, NULL),
(18, '2023-10-28 00:00:00', '2023-11-08 00:00:00', 'Festival du Vin et de la super Bière !', 'Rejoignez-nous pour un festival enivrant célébrant l\'art du vin et de la bière ! Découvrez une sélection exquise de vins régionaux et de bières artisanales, dégustez des saveurs uniques et participez à des démonstrations de brassage et d\'œnologie. Une expérience gustative inoubliable vous attend au cœur de notre festival du vin et de la bière !!', 3, '01000 Bourg-en-Bresse, France', '-652566f2cde58.png', 46.2051192, 5.2250324, NULL),
(19, '2024-07-10 00:00:00', '2024-07-14 00:00:00', 'Summer Festival', 'Préparez-vous à vivre une explosion de divertissement estival au Summer Festival ! Profitez de journées ensoleillées et de nuits étoilées remplies de musique en live, d\'activités excitantes, de dégustations gastronomiques, de défis amusants et de la meilleure ambiance. Que vous soyez un amateur de musique, un aventurier en quête d\'expériences ou simplement à la recherche de bonne humeur, ce festival est fait pour vous. Joignez-vous à nous pour créer des souvenirs mémorables sous le soleil d\'été !', 3, '34000 Montpellier, France', '-652567c4431e5.png', 43.6112422, 3.8767337, NULL),
(20, '2024-03-15 00:00:00', '2024-03-17 00:00:00', 'Star Night Festival', 'Plongez dans un monde de glamour et de divertissement au Star Night Festival, où les plus grands chanteurs de renommée mondiale illuminent la scène de leur talent époustouflant. Cette nuit magique est imprégnée d\'une ambiance électrisante, de spectacles à couper le souffle, d\'activités exaltantes et d\'une fête qui ne s\'arrête jamais. Rejoignez-nous pour une soirée étoilée où la musique, la célébration et la joie vous emporteront vers des hauteurs inégalées. Une expérience inoubliable vous attend sous le ciel étoilé du Star Night Festival.', 3, 'Paris, IDF, France', '-6539b3f8ed0b9.jpg', 48.8534951, 2.3483915, 1),
(21, '2024-06-29 00:00:00', '2024-06-30 00:00:00', 'Sunset Festival', 'Le Sunset Festival, une expérience musicale inoubliable, est une soirée magique dédiée à la musique en plein air, se déroulant au bord de l\'eau au coucher du soleil. Imaginez-vous entouré de paysages pittoresques, les vagues douces caressant la plage, tandis que le soleil commence à se fondre dans l\'horizon orangé. C\'est le cadre idyllique pour une soirée mémorable.', 21, '64200 Biarritz, France', '-65256a2d1ece9.png', 43.47114375, -1.5527265906663, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `festival_tag`
--

CREATE TABLE `festival_tag` (
  `festival_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lieu`
--

CREATE TABLE `lieu` (
  `id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL,
  `nom_lieu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `lieu`
--

INSERT INTO `lieu` (`id`, `festival_id`, `nom_lieu`, `address`) VALUES
(7, 18, 'cuisine', NULL),
(8, 12, 'main stage', NULL),
(9, 12, 'Lieu de la tâche', NULL),
(10, 18, 'Dans la chambre de tanguy', ''),
(11, 18, 'Dans la chambre de tanguy', '');

-- --------------------------------------------------------

--
-- Structure de la table `poste`
--

CREATE TABLE `poste` (
  `id` int(11) NOT NULL,
  `festival_id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `poste`
--

INSERT INTO `poste` (`id`, `festival_id`, `nom`, `description`) VALUES
(2, 12, 'RUNN', ''),
(4, 12, 'Sécurité', ''),
(5, 18, 'Manutention', ''),
(6, 18, 'Service', ''),
(7, 18, 'Cuisine', ''),
(8, 18, 'Run', ''),
(9, 18, 'Sécurité', ''),
(12, 18, 'Billetteri', ''),
(13, 12, 'faire coucou', ''),
(14, 18, 'test', '');

-- --------------------------------------------------------

--
-- Structure de la table `poste_utilisateur`
--

CREATE TABLE `poste_utilisateur` (
  `poste_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `poste_utilisateur`
--

INSERT INTO `poste_utilisateur` (`poste_id`, `utilisateur_id`) VALUES
(8, 33);

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

CREATE TABLE `reset_password_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tache`
--

CREATE TABLE `tache` (
  `id` int(11) NOT NULL,
  `lieu_id` int(11) DEFAULT NULL,
  `crenaux_id` int(11) DEFAULT NULL,
  `poste_id` int(11) NOT NULL,
  `remarque` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nombre_benevole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `tache`
--

INSERT INTO `tache` (`id`, `lieu_id`, `crenaux_id`, `poste_id`, `remarque`, `nombre_benevole`) VALUES
(1, 7, 4, 2, 'Description de la tâche', 2),
(2, 8, 5, 2, 'Description de la tâche', 2),
(3, 7, 6, 2, 'Description de la tâche', 2),
(4, 9, 7, 2, 'Description de la tâche', 2),
(5, 8, 8, 2, 'Description de la tâche', 2),
(6, 7, 9, 2, 'test', 2),
(7, 8, 10, 2, 'Description de la tâche', 2),
(8, 7, 11, 2, 'a', 2),
(9, 9, 12, 2, '12345', 8),
(10, 9, 13, 2, 'le créneau', 19),
(11, 9, 14, 2, 'le créneau', 19),
(12, 8, 15, 9, 'Super remarque 3', 28),
(13, 7, 16, 2, 'le créneau', 19),
(34, NULL, 37, 5, 'Préparer la scene', 2),
(35, NULL, 38, 5, 'Préparer la scene', 2),
(36, NULL, 39, 6, 'Préparer la scene', 2),
(37, NULL, 40, 2, 'a', 2),
(38, NULL, 41, 4, 'aaaa', 2),
(39, NULL, 42, 4, 'a', 5),
(41, NULL, 43, 4, 'a', 5),
(42, NULL, 44, 7, 'coucou', 3),
(43, NULL, 45, 9, 'a', 4),
(47, NULL, 49, 5, 'test', 4),
(48, NULL, 50, 12, 'S\'occuper de la billetterie', 3),
(49, NULL, 51, 12, 'test', 3),
(50, 10, 52, 12, 'Faire le ménage chez tanguy', 69),
(51, 11, 53, 8, 'Faire le ménage chez tanguy', 2),
(52, NULL, 54, 13, '', 4),
(53, NULL, 55, 14, 'lol', 4);

-- --------------------------------------------------------

--
-- Structure de la table `tache_utilisateur`
--

CREATE TABLE `tache_utilisateur` (
  `tache_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `tache_utilisateur`
--

INSERT INTO `tache_utilisateur` (`tache_id`, `utilisateur_id`) VALUES
(4, 6),
(42, 3),
(43, 3),
(43, 23),
(47, 3),
(47, 21),
(47, 23);

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `is_verified`) VALUES
(1, 'test@gmail.com', '[]', '$2y$13$kEPftFSXBdk0fIjuEYqjxeQ.qFC.dzpUUItmQBFefjLPcE0qDY7GS', 'tetst', 'test', 1),
(3, 'pouet@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$yvxHHHsFJtsJNui9rjU4z.wD/oF.mMaA80tx3QL5oO0gy077BcP4O', 'Pouete', 'Pouete', 1),
(5, 'tanguy@gmail.com', '[]', '$2y$13$w9/LH1JcP17gV95uXXBqTuq1Qt0TOyfGs/W1nU/QwJseWwbdafT0S', 'yuyugnat', 'poipi', 1),
(6, 'adamhenchiri02@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$/ZAK3Gh4Qn5dje.hzMDxBOCyZuS5XS7ANa4Y7KrKD.HbG7mhSVR3a', 'henchiri', 'adam', 1),
(7, 'zizi@gmail.com', '[]', '$2y$13$E3NT2LivOIQV2tr57LuAQuylnzRed7d5.6gibSUi5VpePfY50JnMq', 'rayanzizi', 'guemarzizi', 1),
(10, 'masex@gmail.com', '[]', 'zizi', 'MASEX', 'tanguycaca', 1),
(12, 'masex@wanadoo.fr', '[]', '$2y$13$ak/sY/4k.SIi3Xfc8DtNQeKKC03d4BEquDYstgnV4qEWMVT3MxzpS', 'MASEX', 'TANCACA', 1),
(13, 'zizi@wandoo.fr', '[]', '$2y$13$TpW/kTEsGKwdRnXbWa4e7evKixYKGtqphWHyWKbtwdzwQ.b/kA8Lu', 'MASEX', 'TANGZIZI', 1),
(14, 'aaa@gmail.com', '[]', '$2y$13$gjXu5bWhcquCnuUmviGw0OK0CnpAp5OyanN6citnZ7VKvyKOv/kPK', 'MASEX', 'TANCACA', 1),
(15, 'tanguymace2002@gmail.com', '[]', '$2y$13$g91MyuAI3su6XZktRc1.Cu.CTBaJSHw81EL.OvSZ1ZH8YU5nsjgYO', 'Macé', 'Tanguy', 1),
(16, 'rayan@gmail.com', '[]', '$2y$13$h.HDXXBx5XTHEJFJuzDy3OGDBiIrBEpo.NQ66Ow0afBw4mB5DqtWW', 'rayan', 'guemar', 1),
(20, 'nathan@admin.com', '[\"ROLE_ADMIN\"]', '$2y$13$Tl/Nk2LRyH/zA8L/Fua50uuKt.nlwHZ/3mpnD3XZlEEj8uWTgDQWq', 'Admin', 'Nathan', 1),
(21, 'dorian@gmail.com', '[]', '$2y$13$ik8o57/Uo5JjxXox6BgJ4OVDn8UohxtajYtmBwULuUkxu7KreOkfK', 'dorian', 'grasset', 1),
(23, 'lucas@gmail.com', '[]', '$2y$13$BAfEQVmXJlDOhUf9VETMX.y33FUds0b1LL4rDVYxmVYw510bPa7Tu', 'Crouzet', 'Lucas', 1),
(24, 'dockertest@gmail.com', '[]', '$2y$13$TxABZSD86AtUbgUr0TEBg.oUMTMzfjqX2bgES2HuQucQGJboati.K', 'dockertest', 'dockertest', 1),
(25, 'nathan@user.com', '[]', '$2y$13$5HLJkiM5zfuF16IIO2IZFuS1tmcYkgEf8ZEWiyRGp14f03SkMOyNu', 'User', 'Nathan', 1),
(26, 'jean@gmail.com', '[]', '$2y$13$m2OhT9AdfYb0qdG5BoXbvuus6BPqEcOhaQbfLSlrUUwZzzOVXfcNa', 'jean', 'moulin', 1),
(28, 'sarah@gmail.com', '[]', '$2y$13$oLJPoYWTcTFtNxHm8dUzsuf6wKVei750dUZcG74J7JxrTBsWHJWOG', 'Sarah', 'B', 1),
(30, 'henriLeDuc@camelCase.com', '[]', '$2y$13$7gKGBEafz3KmREULww7Q5.hytb9cBdYC.BhQspUKYpXNKPxzDhZCu', 'leDuc', 'henri', 1),
(31, 'monsieurnadal@gmail.com', '[]', '$2y$13$.UhOR4k/0iluOFk4qJ21EeHmo1TJJUn1xLJnivfSjiWg.tONCF6qi', 'monsieur', 'nadal', 1),
(32, 'max@gmail.com', '[]', '$2y$13$zdnCqiYB0nli0ARHvKkJE.U7haQwEGn7QPG/DnDx33cltJj79jcqq', 'max', 'lamenace', 1),
(33, 'demo@gmail.com', '[]', '$2y$13$ftt1A.0wJHhgXWBPX7Htq.NdRZviP9/GJ/TggOG069zLHs4h3ne5y', 'demo', 'demo', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `creneaux`
--
ALTER TABLE `creneaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes_benevole`
--
ALTER TABLE `demandes_benevole`
  ADD PRIMARY KEY (`utilisateur_id`,`festival_id`),
  ADD KEY `IDX_BA8C1330FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_BA8C13308AEBAF57` (`festival_id`);

--
-- Index pour la table `demande_festival`
--
ALTER TABLE `demande_festival`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4325BE4B9D7943F3` (`organisateur_festival_id`);

--
-- Index pour la table `disponibilite`
--
ALTER TABLE `disponibilite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2CBACE2FFB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_2CBACE2F8AEBAF57` (`festival_id`),
  ADD KEY `IDX_2CBACE2F7D0729A9` (`creneau_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `est_benevole`
--
ALTER TABLE `est_benevole`
  ADD PRIMARY KEY (`utilisateur_id`,`festival_id`),
  ADD KEY `IDX_2F8ED272FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_2F8ED2728AEBAF57` (`festival_id`);

--
-- Index pour la table `est_responsable`
--
ALTER TABLE `est_responsable`
  ADD PRIMARY KEY (`utilisateur_id`,`festival_id`),
  ADD KEY `IDX_3D130195FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_3D1301958AEBAF57` (`festival_id`);

--
-- Index pour la table `festival`
--
ALTER TABLE `festival`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_57CF789D936B2FA` (`organisateur_id`);

--
-- Index pour la table `festival_tag`
--
ALTER TABLE `festival_tag`
  ADD PRIMARY KEY (`festival_id`,`tag_id`),
  ADD KEY `IDX_9DD8F0E88AEBAF57` (`festival_id`),
  ADD KEY `IDX_9DD8F0E8BAD26311` (`tag_id`);

--
-- Index pour la table `lieu`
--
ALTER TABLE `lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2F577D598AEBAF57` (`festival_id`);

--
-- Index pour la table `poste`
--
ALTER TABLE `poste`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7C890FAB8AEBAF57` (`festival_id`);

--
-- Index pour la table `poste_utilisateur`
--
ALTER TABLE `poste_utilisateur`
  ADD PRIMARY KEY (`poste_id`,`utilisateur_id`),
  ADD KEY `IDX_D5BA2797A0905086` (`poste_id`),
  ADD KEY `IDX_D5BA2797FB88E14F` (`utilisateur_id`);

--
-- Index pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CE748AA76ED395` (`user_id`);

--
-- Index pour la table `tache`
--
ALTER TABLE `tache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_93872075819C8220` (`crenaux_id`),
  ADD KEY `IDX_938720756AB213CC` (`lieu_id`),
  ADD KEY `IDX_93872075A0905086` (`poste_id`);

--
-- Index pour la table `tache_utilisateur`
--
ALTER TABLE `tache_utilisateur`
  ADD PRIMARY KEY (`tache_id`,`utilisateur_id`),
  ADD KEY `IDX_8E15C4FDD2235D39` (`tache_id`),
  ADD KEY `IDX_8E15C4FDFB88E14F` (`utilisateur_id`);

--
-- Index pour la table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `creneaux`
--
ALTER TABLE `creneaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pour la table `demande_festival`
--
ALTER TABLE `demande_festival`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `disponibilite`
--
ALTER TABLE `disponibilite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `festival`
--
ALTER TABLE `festival`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `lieu`
--
ALTER TABLE `lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `poste`
--
ALTER TABLE `poste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `tache`
--
ALTER TABLE `tache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demandes_benevole`
--
ALTER TABLE `demandes_benevole`
  ADD CONSTRAINT `FK_BA8C13308AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`),
  ADD CONSTRAINT `FK_BA8C1330FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `demande_festival`
--
ALTER TABLE `demande_festival`
  ADD CONSTRAINT `FK_4325BE4B9D7943F3` FOREIGN KEY (`organisateur_festival_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `disponibilite`
--
ALTER TABLE `disponibilite`
  ADD CONSTRAINT `FK_2CBACE2F7D0729A9` FOREIGN KEY (`creneau_id`) REFERENCES `creneaux` (`id`),
  ADD CONSTRAINT `FK_2CBACE2F8AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`),
  ADD CONSTRAINT `FK_2CBACE2FFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `est_benevole`
--
ALTER TABLE `est_benevole`
  ADD CONSTRAINT `FK_2F8ED2728AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`),
  ADD CONSTRAINT `FK_2F8ED272FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `est_responsable`
--
ALTER TABLE `est_responsable`
  ADD CONSTRAINT `FK_3D1301958AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`),
  ADD CONSTRAINT `FK_3D130195FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `festival`
--
ALTER TABLE `festival`
  ADD CONSTRAINT `FK_57CF789D936B2FA` FOREIGN KEY (`organisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `festival_tag`
--
ALTER TABLE `festival_tag`
  ADD CONSTRAINT `FK_9DD8F0E88AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9DD8F0E8BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `lieu`
--
ALTER TABLE `lieu`
  ADD CONSTRAINT `FK_2F577D598AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`);

--
-- Contraintes pour la table `poste`
--
ALTER TABLE `poste`
  ADD CONSTRAINT `FK_7C890FAB8AEBAF57` FOREIGN KEY (`festival_id`) REFERENCES `festival` (`id`);

--
-- Contraintes pour la table `poste_utilisateur`
--
ALTER TABLE `poste_utilisateur`
  ADD CONSTRAINT `FK_D5BA2797A0905086` FOREIGN KEY (`poste_id`) REFERENCES `poste` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_D5BA2797FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `tache`
--
ALTER TABLE `tache`
  ADD CONSTRAINT `FK_938720756AB213CC` FOREIGN KEY (`lieu_id`) REFERENCES `lieu` (`id`),
  ADD CONSTRAINT `FK_93872075819C8220` FOREIGN KEY (`crenaux_id`) REFERENCES `creneaux` (`id`),
  ADD CONSTRAINT `FK_93872075A0905086` FOREIGN KEY (`poste_id`) REFERENCES `poste` (`id`);

--
-- Contraintes pour la table `tache_utilisateur`
--
ALTER TABLE `tache_utilisateur`
  ADD CONSTRAINT `FK_8E15C4FDD2235D39` FOREIGN KEY (`tache_id`) REFERENCES `tache` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8E15C4FDFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- phpMyAdmin SQL Dump
-- version 5.1.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 29 nov. 2023 à 01:45
-- Version du serveur : 10.5.15-MariaDB-0+deb11u1
-- Version de PHP : 8.1.13


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : macet
--

--
-- Déchargement des données de la table creneaux
--

INSERT INTO creneaux (id, date_debut, date_fin) VALUES
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
(53, '2023-10-31 04:00:00', '2023-10-31 05:00:00'),
(54, '2023-10-11 02:00:00', '2023-10-11 04:00:00'),
(55, '2023-10-29 06:00:00', '2023-10-29 10:00:00'),
(56, '2023-10-10 20:00:00', '2023-10-10 21:00:00'),
(57, '2023-11-03 00:38:00', '2023-11-03 03:38:00'),
(58, '2023-11-03 00:38:00', '2023-11-03 03:38:00'),
(59, '2023-11-03 00:38:00', '2023-11-03 03:38:00'),
(60, '2023-11-03 00:38:00', '2023-11-03 03:38:00'),
(61, '2023-10-31 03:46:00', '2023-10-31 06:46:00'),
(62, '2023-10-31 03:46:00', '2023-10-31 06:46:00'),
(63, '2023-10-31 09:10:00', '2023-10-31 13:08:00'),
(64, '2023-10-31 09:11:00', '2023-10-31 13:12:00'),
(65, '2023-10-31 09:10:00', '2023-10-31 13:17:00'),
(66, '2023-10-29 13:09:00', '2023-10-29 15:10:00'),
(67, '2023-10-29 17:19:00', '2023-10-29 19:20:00'),
(68, '2023-10-10 12:42:00', '2023-10-10 12:46:00'),
(69, '2023-10-28 09:47:00', '2023-10-28 11:47:00'),
(70, '2023-10-10 01:02:00', '2023-10-10 02:02:00'),
(71, '2023-10-10 00:33:00', '2023-10-10 02:34:00'),
(72, '2023-10-30 06:37:00', '2023-10-30 08:38:00'),
(73, '2023-10-30 07:38:00', '2023-10-30 21:38:00'),
(74, '2023-10-30 07:38:00', '2023-10-30 21:38:00'),
(75, '2023-10-28 19:39:00', '2023-10-28 21:39:00'),
(76, '2023-10-31 20:40:00', '2023-10-31 22:40:00'),
(77, '2023-10-30 01:41:00', '2023-10-30 04:41:00'),
(78, '2023-10-30 01:41:00', '2023-10-30 04:41:00'),
(79, '2023-10-30 01:41:00', '2023-10-30 04:41:00'),
(80, '2023-10-28 12:00:00', '2023-10-28 14:00:00'),
(81, '2023-10-28 14:13:00', '2023-10-28 15:55:00'),
(82, '2023-10-28 19:10:00', '2023-10-28 20:10:00'),
(83, '2023-10-28 08:14:00', '2023-10-28 11:14:00'),
(84, '2023-11-03 05:36:00', '2023-11-03 08:36:00'),
(85, '2023-10-10 01:34:00', '2023-10-10 02:34:00'),
(86, '2023-10-28 03:11:00', '2023-10-28 04:11:00'),
(87, '2023-10-28 05:32:00', '2023-10-28 06:33:00'),
(88, '2024-07-10 13:00:00', '2024-07-10 14:00:00'),
(89, '2024-07-11 20:00:00', '2024-07-11 22:00:00'),
(90, '2024-07-10 04:00:00', '2024-07-10 06:00:00'),
(91, '2023-10-31 20:56:00', '2023-10-31 22:56:00'),
(92, '2023-10-29 04:27:00', '2023-10-29 08:27:00'),
(93, '2023-10-29 02:38:00', '2023-10-29 04:39:00');

INSERT INTO utilisateur (id, email, roles, password, nom, prenom, is_verified, adresse, description, nom_photo_profil) VALUES
(1, 'test@gmail.com', '[]', '$2y$13$kEPftFSXBdk0fIjuEYqjxeQ.qFC.dzpUUItmQBFefjLPcE0qDY7GS', 'tetst', 'test', true, '', '', NULL),
(3, 'pouet@gmail.com', '["ROLE_ADMIN"]', '$2y$13$yvxHHHsFJtsJNui9rjU4z.wD/oF.mMaA80tx3QL5oO0gy077BcP4O', 'Pouete', 'Pouet', true, '1 rue du camember', 'J"aime bien faire pouet pouet', '65645af58ef68.jpg'),
(5, 'tanguy@gmail.com', '[]', '$2y$13$w9/LH1JcP17gV95uXXBqTuq1Qt0TOyfGs/W1nU/QwJseWwbdafT0S', 'yuyugnat', 'poipi', true, '', '', NULL),
(6, 'adamhenchiri02@gmail.com', '["ROLE_ADMIN"]', '$2y$13$/ZAK3Gh4Qn5dje.hzMDxBOCyZuS5XS7ANa4Y7KrKD.HbG7mhSVR3a', 'henchiri', 'adam', true, '', '', NULL),
(7, 'zizi@gmail.com', '[]', '$2y$13$E3NT2LivOIQV2tr57LuAQuylnzRed7d5.6gibSUi5VpePfY50JnMq', 'rayanzizi', 'guemarzizi', true, '', '', NULL),
(10, 'masex@gmail.com', '[]', 'zizi', 'MASEX', 'tanguycaca', true, '', '', NULL),
(12, 'masex@wanadoo.fr', '[]', '$2y$13$ak/sY/4k.SIi3Xfc8DtNQeKKC03d4BEquDYstgnV4qEWMVT3MxzpS', 'MASEX', 'TANCACA', true, '', '', NULL),
(13, 'zizi@wandoo.fr', '[]', '$2y$13$TpW/kTEsGKwdRnXbWa4e7evKixYKGtqphWHyWKbtwdzwQ.b/kA8Lu', 'MASEX', 'TANGZIZI', true, '', '', NULL),
(14, 'aaa@gmail.com', '[]', '$2y$13$gjXu5bWhcquCnuUmviGw0OK0CnpAp5OyanN6citnZ7VKvyKOv/kPK', 'MASEX', 'TANCACA', true, '', '', NULL),
(15, 'tanguymace2002@gmail.com', '[]', '$2y$13$g91MyuAI3su6XZktRc1.Cu.CTBaJSHw81EL.OvSZ1ZH8YU5nsjgYO', 'Macé', 'Tanguy', true, '', '', NULL),
(16, 'rayan@gmail.com', '[]', '$2y$13$h.HDXXBx5XTHEJFJuzDy3OGDBiIrBEpo.NQ66Ow0afBw4mB5DqtWW', 'rayan', 'guemar', true, '', '', NULL),
(20, 'nathan@admin.com', '["ROLE_ADMIN"]', '$2y$13$Tl/Nk2LRyH/zA8L/Fua50uuKt.nlwHZ/3mpnD3XZlEEj8uWTgDQWq', 'Admin', 'Nathan', true, '', '', NULL),
(21, 'dorian@gmail.com', '[]', '$2y$13$ik8o57/Uo5JjxXox6BgJ4OVDn8UohxtajYtmBwULuUkxu7KreOkfK', 'dorian', 'grasset', true, '', '', NULL),
(23, 'lucas@gmail.com', '[]', '$2y$13$BAfEQVmXJlDOhUf9VETMX.y33FUds0b1LL4rDVYxmVYw510bPa7Tu', 'Crouzet', 'Lucas', true, '', '', NULL),
(24, 'dockertest@gmail.com', '[]', '$2y$13$TxABZSD86AtUbgUr0TEBg.oUMTMzfjqX2bgES2HuQucQGJboati.K', 'dockertest', 'dockertest', true, '', '', NULL),
(25, 'nathan@user.com', '[]', '$2y$13$5HLJkiM5zfuF16IIO2IZFuS1tmcYkgEf8ZEWiyRGp14f03SkMOyNu', 'User', 'Nathan', true, '', '', NULL),
(26, 'jean@gmail.com', '[]', '$2y$13$m2OhT9AdfYb0qdG5BoXbvuus6BPqEcOhaQbfLSlrUUwZzzOVXfcNa', 'jean', 'moulin', true, '', '', NULL),
(28, 'sarah@gmail.com', '[]', '$2y$13$oLJPoYWTcTFtNxHm8dUzsuf6wKVei750dUZcG74J7JxrTBsWHJWOG', 'Sarah', 'B', true, '', '', NULL),
(30, 'henriLeDuc@camelCase.com', '[]', '$2y$13$7gKGBEafz3KmREULww7Q5.hytb9cBdYC.BhQspUKYpXNKPxzDhZCu', 'leDuc', 'henri', true, '', '', NULL),
(31, 'monsieurnadal@gmail.com', '[]', '$2y$13$.UhOR4k/0iluOFk4qJ21EeHmo1TJJUn1xLJnivfSjiWg.tONCF6qi', 'monsieur', 'nadal', true, '', '', NULL),
(32, 'max@gmail.com', '[]', '$2y$13$zdnCqiYB0nli0ARHvKkJE.U7haQwEGn7QPG/DnDx33cltJj79jcqq', 'max', 'lamenace', true, '', '', NULL),
(33, 'demo@gmail.com', '[]', '$2y$13$ftt1A.0wJHhgXWBPX7Htq.NdRZviP9/GJ/TggOG069zLHs4h3ne5y', 'demo', 'demo', true, '', '', NULL),
(34, 'nathan@test.com', '[]', '$2y$13$trOOk13UkgRmobxxoxcXkeV.MKQliu.Qr/fxHpfFFsZvs144TzpVa', 'Test', 'Nathan', false, '1 rue de festiflux', 'J"aime bien manger', 'C:\\xampp\\tmp\\php7183.tmp'),
(35, 'luktest@gmail.com', '[]', '$2y$13$j.OFOiqhy.pPFjnteMYYGedJosERGgnFVaU9yYB5.Zk0OdegR4CP6', 'Luk', 'Test', false, 'chemin du test', 'je fais des tests', NULL);

INSERT INTO festival (id, date_debut, date_fin, nom, description, organisateur_id, lieu, affiche, lat, lon, is_archive) VALUES
(12, '2023-10-10 00:00:00', '2023-10-12 00:00:00', 'ici sétois', 'Le lorem ipsum est, en imprimerie, une suite de mots sans signification utilisée à titre provisoire pour calibrer une mise en page, le texte définitif venant remplacer le faux-texte dès qu"il est prêt ou que la mise en page est achevée. Généralement, on utilise un texte en faux latin, le Lorem ipsum ou Lipsum', 6, 'Théâtre de la Mer, Promenade du Maréchal Leclerc, 34200 Sète, France', '-6524636289eb8.jpg', NULL, NULL, NULL),
(18, '2023-10-28 00:00:00', '2023-11-08 00:00:00', 'Festival du Vin et de la super Bière !', 'Rejoignez-nous pour un festival enivrant célébrant l"art du vin et de la bière ! Découvrez une sélection exquise de vins régionaux et de bières artisanales, dégustez des saveurs uniques et participez à des démonstrations de brassage et d"œnologie. Une expérience gustative inoubliable vous attend au cœur de notre festival du vin et de la bière !!', 3, '01000 Bourg-en-Bresse, France', '-652566f2cde58.png', 46.2051192, 5.2250324, NULL),
(19, '2024-07-10 00:00:00', '2024-07-14 00:00:00', 'Summer Festival', 'Préparez-vous à vivre une explosion de divertissement estival au Summer Festival ! Profitez de journées ensoleillées et de nuits étoilées remplies de musique en live, d"activités excitantes, de dégustations gastronomiques, de défis amusants et de la meilleure ambiance. Que vous soyez un amateur de musique, un aventurier en quête d"expériences ou simplement à la recherche de bonne humeur, ce festival est fait pour vous. Joignez-vous à nous pour créer des souvenirs mémorables sous le soleil d"été !', 3, '34000 Montpellier, France', '-652567c4431e5.png', 43.6112422, 3.8767337, NULL),
(20, '2024-03-15 00:00:00', '2024-03-17 00:00:00', 'Star Night Festival', 'Plongez dans un monde de glamour et de divertissement au Star Night Festival, où les plus grands chanteurs de renommée mondiale illuminent la scène de leur talent époustouflant. Cette nuit magique est imprégnée d"une ambiance électrisante, de spectacles à couper le souffle, d"activités exaltantes et d"une fête qui ne s"arrête jamais. Rejoignez-nous pour une soirée étoilée où la musique, la célébration et la joie vous emporteront vers des hauteurs inégalées. Une expérience inoubliable vous attend sous le ciel étoilé du Star Night Festival.', 3, 'Paris, IDF, France', '-6539b3f8ed0b9.jpg', 48.8534951, 2.3483915, 1),
(21, '2024-06-29 00:00:00', '2024-06-30 00:00:00', 'Sunset Festival', 'Le Sunset Festival, une expérience musicale inoubliable, est une soirée magique dédiée à la musique en plein air, se déroulant au bord de l"eau au coucher du soleil. Imaginez-vous entouré de paysages pittoresques, les vagues douces caressant la plage, tandis que le soleil commence à se fondre dans l"horizon orangé. C"est le cadre idyllique pour une soirée mémorable.', 21, '64200 Biarritz, France', '-65256a2d1ece9.png', 43.47114375, -1.5527265906663, NULL),
(25, '2023-11-22 00:00:00', '2023-11-23 00:00:00', 'hoooooyeah fest', 'zdjneojkez v eeinifcneinzi', 6, 'Andören, Raseborg, Uusimaa, Finland', '-655e2fbcded89.png', 59.8789289, 23.672894012427, 0),
(26, '2023-11-22 00:00:00', '2023-11-23 00:00:00', 'hoooooyeah fest2', 'fneenzfnez jnzefnke knzpfnpn', 30, 'معتمدية جربة ميدون, Tunisia', '-655e4188cc041.jpg', 33.7536409, 10.935266091986, 0);

--
-- Déchargement des données de la table demandes_benevole

INSERT INTO tag (id, nom) VALUES
(1, 'R&R'),
(2, 'R&B'),
(3, 'House');
--

INSERT INTO demandes_benevole (utilisateur_id, festival_id) VALUES
(28, 12);

--
-- Déchargement des données de la table doctrine_migration_versions
--

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES
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
('DoctrineMigrations\\Version20231122091721', '2023-11-22 09:17:39', 471),
('DoctrineMigrations\\Version20231122202203', '2023-11-22 20:22:14', 89),
('DoctrineMigrations\\Version20231122230004', '2023-11-22 23:00:19', 128),
('DoctrineMigrations\\Version20231123142201', '2023-11-23 14:24:13', 231),
('DoctrineMigrations\\Version20231124125914', '2023-11-24 14:00:03', 819),
('DoctrineMigrations\\Version20231127104941', '2023-11-27 10:49:50', 221),
('DoctrineMigrations\\Version20231127193716', '2023-11-27 19:37:26', 80),
('DoctrineMigrations\\Version20231127211814', '2023-11-27 21:18:20', 268);

--
-- Déchargement des données de la table est_benevole
--

INSERT INTO est_benevole (utilisateur_id, festival_id) VALUES
(3, 12),
(3, 18),
(3, 21),
(6, 12),
(21, 12),
(21, 18),
(21, 20),
(23, 18),
(23, 21),
(33, 18),
(35, 19);

--
-- Déchargement des données de la table est_responsable
--

INSERT INTO est_responsable (utilisateur_id, festival_id) VALUES
(3, 12),
(23, 18),
(33, 18);

--
-- Déchargement des données de la table festival
--


--
-- Déchargement des données de la table festival_creneaux
--

INSERT INTO festival_creneaux (festival_id, creneaux_id) VALUES
(12, 40),
(12, 85),
(18, 92),
(18, 93);

--
-- Déchargement des données de la table festival_tag
--

INSERT INTO festival_tag (festival_id, tag_id) VALUES
(25, 1),
(25, 2),
(25, 3),
(26, 1);

--
-- Déchargement des données de la table lieu
--

INSERT INTO lieu (id, festival_id, nom_lieu, address) VALUES
(7, 18, 'cuisine', NULL),
(8, 12, 'main stage', NULL),
(9, 12, 'Lieu de la tâche', NULL),
(10, 18, 'Dans la chambre de tanguy', ''),
(11, 18, 'Dans la chambre de tanguy', ''),
(12, 18, 'Kuizine', 'Chez sarace'),
(13, 18, 'kuizinella', 'Chez moi wesh'),
(14, 18, '', ''),
(15, 18, 'Lieu test', 'test adresse'),
(16, 12, 'guichet', ''),
(27, 18, 'Dans la chambre de tanguy', 'ici'),
(32, 18, 'cuisine', ''),
(33, 18, 'cuisine', ''),
(34, 18, 'aaa', 'aaaa'),
(35, 18, 'a', 'a'),
(36, 18, '333', '333'),
(37, 18, '333', '333'),
(38, 18, '333', '333'),
(39, 18, ',', 'n'),
(40, 18, ',', 'n'),
(41, 18, 'test', 'test'),
(42, 18, 'test', 'test'),
(43, 18, 'mtp', ''),
(44, 18, 'test', 'test'),
(45, 18, 'test', 'test'),
(46, 19, 'Dans la chambre de tanguy', ''),
(47, 19, 'Tente quechua', ''),
(48, 19, 'scene 1', ''),
(49, 18, 'filtre', 'filtre');

--
-- Déchargement des données de la table poste
--

INSERT INTO poste (id, festival_id, nom, description, couleur) VALUES
(2, 12, 'RUNN', '', '#2f1d1d'),
(4, 12, 'Sécurité', '', NULL),
(5, 18, 'Manutention', '', '#1406db'),
(6, 18, 'Service', '', '#906f6f'),
(7, 18, 'Cuisine', 'couco', NULL),
(8, 18, 'Run', '', NULL),
(9, 18, 'Sécurité', '', '#8c8f00'),
(13, 12, 'faire coucou', '', NULL),
(14, 18, 'test 4', 'desc', '#067406'),
(16, 12, 'La plonge', '', '#84b5f5'),
(17, 12, 'billetterie ', '', '#37e143'),
(21, 18, 'guide de f1', '', '#fd0ddd'),
(22, 18, 'test2', '', '#fd0ddd'),
(24, 12, 'billetterie ', '', '#d1c21f'),
(25, 12, 'billetterie', '', '#1a1a19'),
(26, 12, 'Faire à manger', '', '#ff0000'),
(27, 12, 'Accueil artistes', '', '#347deb'),
(30, 12, 'test poste', '', '#347deb'),
(31, 18, 'Faire la paella', '', '#347deb'),
(32, 18, 'test 3433948', '', '#800057'),
(34, 18, 'mon super poste', '', '#ff0000'),
(35, 18, 'nouveau poste', '', '#2a4e84'),
(36, 19, 'Chambre', 'Monter les tentes', '#2f5183'),
(37, 12, 'ma super tache', '', '#6c6f62'),
(38, 12, 'test4208', '', '#404245'),
(39, 12, 'test', '', '#6b6f76'),
(40, 12, 'test2', '', '#676c74'),
(41, 12, 'test3', '', '#177082'),
(46, 19, 'Accrobatie', '', '#8ab5f4');

--
-- Déchargement des données de la table poste_utilisateur_preferences
--

INSERT INTO poste_utilisateur_preferences (id, poste_id_id, utilisateur_id_id, preferences_degree) VALUES
(5, 24, 3, 0),
(6, 24, 6, 1),
(7, 24, 21, 1),
(8, 25, 3, -1),
(9, 25, 6, 1),
(10, 25, 21, 1),
(11, 26, 3, 1),
(12, 26, 6, 1),
(13, 26, 21, 1),
(14, 27, 3, 1),
(15, 27, 6, 1),
(16, 27, 21, 1),
(23, 30, 3, 1),
(24, 30, 6, 1),
(25, 30, 21, 1),
(26, 31, 3, 1),
(27, 31, 21, 1),
(28, 31, 23, 1),
(29, 31, 33, 1),
(30, 32, 3, 1),
(31, 32, 21, 1),
(32, 32, 23, 1),
(33, 32, 33, 1),
(38, 34, 3, 1),
(39, 34, 21, 1),
(40, 34, 23, 1),
(41, 34, 33, 1),
(42, 35, 3, 1),
(43, 35, 21, 1),
(44, 35, 23, 1),
(45, 35, 33, 1),
(46, 37, 3, 1),
(47, 37, 6, 1),
(48, 37, 21, 1),
(49, 38, 3, 1),
(50, 38, 6, 1),
(51, 38, 21, 1),
(52, 39, 3, 1),
(53, 39, 6, 1),
(54, 39, 21, 1),
(55, 40, 3, 1),
(56, 40, 6, 1),
(57, 40, 21, 1),
(58, 41, 3, 1),
(59, 41, 6, 1),
(60, 41, 21, 1),
(73, 46, 35, 1);

--
-- Déchargement des données de la table question_benevole
--

INSERT INTO question_benevole (id, festival_id, label) VALUES
(1, 19, 'test'),
(2, 19, 'test2');

--
-- Déchargement des données de la table tache
--

INSERT INTO tache (id, lieu_id, crenaux_id, poste_id, remarque, nombre_benevole) VALUES
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
(13, 7, 16, 2, 'le créneau', 19),
(34, 9, 37, 5, 'Préparer la scene', 2),
(35, 9, 38, 5, 'Préparer la scene', 2),
(36, 11, 39, 6, 'Préparer la scene', 2),
(37, 11, 40, 2, 'a', 2),
(38, 11, 41, 4, 'aaaa', 2),
(39, 11, 42, 4, 'a', 5),
(41, 11, 43, 4, 'a', 5),
(42, 11, 44, 7, 'coucou', 3),
(43, 11, 45, 9, 'a', 4),
(47, 11, 49, 5, 'test', 4),
(51, 11, 53, 8, 'Faire le ménage chez tanguy', 2),
(52, 27, 54, 13, '', 4),
(53, 27, 55, 14, 'lol', 4),
(54, 27, 56, 16, 'laver la vaiselle', 5),
(55, 27, 57, 5, 'a', 2),
(56, 27, 58, 5, 'a', 2),
(57, 27, 59, 5, 'a', 2),
(58, 27, 60, 5, 'a', 2),
(59, 27, 61, 9, 'test', 4),
(60, 27, 62, 9, 'test', 4),
(61, 27, 63, 7, 'test', 35),
(62, 27, 64, 5, 'coucou', 4),
(63, 27, 65, 6, 'coucou', 3),
(64, 12, 66, 6, 'test', 3),
(65, 13, 67, 21, 'test', 3),
(66, 16, 68, 25, 'test routes benevoles', 4),
(67, 27, 69, 5, 'Faire le ménage chez tanguy', 2),
(68, 32, 73, 5, 'test', 4),
(69, 33, 74, 5, 'test', 4),
(70, 34, 75, 8, 'aaa', 1),
(71, 35, 76, 7, 'a', 19),
(72, 36, 77, 9, 'le créneau', 2),
(73, 37, 78, 9, 'le créneau', 2),
(74, 38, 79, 9, 'le créneau', 2),
(75, 39, 80, 5, 'nj', 19),
(76, 40, 81, 32, 'nj', 19),
(77, 41, 82, 5, 'test', 3),
(78, 42, 83, 5, 'test', 3),
(79, 43, 84, 5, 'teset', 4),
(80, 44, 86, 35, 'test', 3),
(81, 45, 87, 5, 'test', 3),
(82, 46, 88, 36, 'Faire le ménage chez tanguy', 2),
(83, 47, 89, 36, 'dormir', 1),
(84, 48, 90, 46, 'faire un salto', 1),
(85, 49, 91, 7, 'filtre', 4);

--
-- Déchargement des données de la table tache_utilisateur
--

INSERT INTO tache_utilisateur (tache_id, utilisateur_id) VALUES
(4, 6),
(42, 3),
(43, 3),
(43, 23),
(47, 3),
(47, 21),
(47, 23),
(84, 35);

--
-- Déchargement des données de la table tag
--

--
-- Déchargement des données de la table utilisateur
--


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
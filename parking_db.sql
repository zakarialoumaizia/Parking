-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 26, 2025 at 03:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `amendes`
--

CREATE TABLE `amendes` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `motif` text NOT NULL,
  `statut` enum('non_payee','payee') DEFAULT 'non_payee',
  `date_amende` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_notification` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `utilisateur_id`, `message`, `lu`, `date_notification`) VALUES
(4, 18, 'Reservation Created: Code RES-694E9CC630DA4', 0, '2025-12-26 14:33:42'),
(5, 18, 'Reservation Created: Code RES-694E9EA4C9A02', 0, '2025-12-26 14:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `mode` enum('en_ligne','sur_place') NOT NULL,
  `statut` enum('paye','non_paye') DEFAULT 'non_paye',
  `date_paiement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paiements`
--

INSERT INTO `paiements` (`id`, `reservation_id`, `montant`, `mode`, `statut`, `date_paiement`) VALUES
(6, 14, 2000.00, 'en_ligne', 'non_paye', '2025-12-26 15:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `type` enum('standard','PMR','VIP') NOT NULL,
  `statut` enum('libre','reservee','occupee','indisponible') DEFAULT 'libre',
  `prix_custom` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `numero`, `type`, `statut`, `prix_custom`) VALUES
(1, 'A1', 'standard', 'libre', NULL),
(2, 'A2', 'standard', 'libre', NULL),
(3, 'A3', 'standard', 'libre', NULL),
(4, 'A4', 'standard', 'libre', NULL),
(5, 'B1', 'PMR', 'libre', NULL),
(6, 'B2', 'PMR', 'libre', NULL),
(7, 'V1', 'VIP', 'libre', NULL),
(8, 'V2', 'VIP', 'libre', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `code_reservation` varchar(50) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `statut` enum('active','annulee','terminee') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `code_reservation`, `utilisateur_id`, `place_id`, `date_debut`, `date_fin`, `statut`) VALUES
(14, 'RES-694E9EA4C9A02', 18, 2, '2025-12-26 15:42:00', '2025-12-26 16:42:00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tarifs`
--

CREATE TABLE `tarifs` (
  `id` int(11) NOT NULL,
  `type_place` enum('standard','PMR','VIP') NOT NULL,
  `prix_heure` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tarifs`
--

INSERT INTO `tarifs` (`id`, `type_place`, `prix_heure`) VALUES
(1, 'standard', 2000.00),
(2, 'PMR', 3500.00),
(3, 'VIP', 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `types_taxe`
--

CREATE TABLE `types_taxe` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `is_percentage` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types_taxe`
--

INSERT INTO `types_taxe` (`id`, `nom`, `slug`, `montant`, `is_percentage`, `updated_at`) VALUES
(2, 'Amende PMR (Non autorisé)', 'amende_pmr', 1000.00, 0, '2025-12-26 13:59:49'),
(3, 'Retard (par heure)', 'retard_heure', 750.00, 0, '2025-12-26 13:59:49'),
(4, 'Taxe VIP', 'taxe_vip', 750.00, 0, '2025-12-26 13:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('usager','premium','agent','admin') NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_creation`) VALUES
(14, 'Admin System', 'admin@pr.com', '$2a$10$QH7Jmw/OV0CaLkxr7euSheMkJT2LBLxdHnTwowdCXEFjAgckfuxyi', 'admin', '2025-12-26 12:25:03'),
(15, 'Agent', 'agent@pr.com', '$2a$10$QH7Jmw/OV0CaLkxr7euShem51YEze9Cr4baXMJBXSMu40.KyojOBG', 'agent', '2025-12-26 12:25:03'),
(16, 'Zakaria Loumizia', 'loumiziazakaria@gmail.com', '$2a$10$QH7Jmw/OV0CaLkxr7euSheMlm2tsT5qr0Lq5NFKZp6h.kbuJ/6fm6', 'premium', '2025-12-26 12:25:03'),
(17, 'Zakaria lao', 'zakarialoumaizia@gmail.com', '$2a$10$QH7Jmw/OV0CaLkxr7euSheNwjE0DKIbrWiNhnjhg3QozcATBfMjP.', 'premium', '2025-12-26 12:25:03'),
(18, 'Zakaria lao', 'shopzakarialao@gmail.com', '$2a$10$QH7Jmw/OV0CaLkxr7euSheNwjE0DKIbrWiNhnjhg3QozcATBfMjP.', 'usager', '2025-12-26 14:28:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amendes`
--
ALTER TABLE `amendes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_amende_agent` (`agent_id`),
  ADD KEY `fk_amende_reservation` (`reservation_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notification_user` (`utilisateur_id`);

--
-- Indexes for table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_reservation` (`code_reservation`),
  ADD KEY `fk_reservation_user` (`utilisateur_id`),
  ADD KEY `fk_reservation_place` (`place_id`);

--
-- Indexes for table `tarifs`
--
ALTER TABLE `tarifs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_place` (`type_place`);

--
-- Indexes for table `types_taxe`
--
ALTER TABLE `types_taxe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amendes`
--
ALTER TABLE `amendes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `types_taxe`
--
ALTER TABLE `types_taxe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amendes`
--
ALTER TABLE `amendes`
  ADD CONSTRAINT `fk_amende_agent` FOREIGN KEY (`agent_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_amende_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `fk_paiement_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservation_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reservation_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

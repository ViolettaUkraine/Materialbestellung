-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 28. Mai 2025 um 03:22
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `materialbestellung_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `materials`
--

INSERT INTO `materials` (`id`, `name`, `description`, `price`, `stock`, `image_url`) VALUES
(21, 'Kugelschreiber', 'Blauer Kugelschreiber', 0.80, 200, 'img/kugelschreiber.jpg'),
(22, 'Bleistift', 'HB-Bleistift mit Radiergummi', 0.50, 132, 'img/Bleistift.jpg'),
(23, 'Textmarker', 'Gelber Leuchtmarker', 1.20, 94, 'img/Textmarker.jpg'),
(24, 'Collegeblock A4', 'Liniert, 80 Blatt', 2.50, 77, 'img/Collegeblock A4.jpg'),
(25, 'Heft A5', 'Liniert, 16 Seiten', 0.70, 118, 'img/Heft A5.jpg'),
(26, 'Ordner A4', 'Breit, schwarz', 3.90, 40, 'img/Ordner A4.jpg'),
(27, 'Trennblätter', 'Farbige Registerblätter', 1.00, 88, 'img/Trennblätter.jpg'),
(28, 'Karteikarten', 'A7, unliniert', 1.50, 68, 'img/Karteikarten.jpg'),
(29, 'Büroklammern', '100 Stück, silber', 0.90, 173, 'img/Büroklammern.jpg'),
(30, 'Tacker', 'Standard-Tischhefter', 5.50, 39, 'img/Tacker.jpg'),
(31, 'Heftklammern', '1000 Stück, für Standard-Tacker', 1.10, 97, 'img/Heftklammern.jpg'),
(32, 'Locher', '2-fach-Locher, Metall', 4.20, 47, 'img/Locher.jpg'),
(33, 'Radiergummi', 'PVC-frei, weiß', 0.60, 98, 'img/Radiergummi.jpg'),
(34, 'Lineal 30cm', 'Transparent, Kunststoff', 0.90, 75, 'img/Lineal 30cm.jpg'),
(35, 'Notizzettel', 'Haftnotizen, 75x75mm, gelb', 1.30, 120, 'img/Notizzettel.jpg'),
(36, 'USB-Stick 16GB', 'Speicherstick für Daten', 7.90, 30, 'img/USB-Stick 16GB.jpg'),
(37, 'Schere', 'Büroschere, 21 cm', 2.80, 57, 'img/Schere.jpg'),
(38, 'Tesafilm', 'Klebefilm, transparent', 1.00, 90, 'img/Tesafilm.jpg'),
(39, 'Klebeband-Abroller', 'Für Standardrollen', 3.50, 41, 'img/Klebeband-Abroller.jpg'),
(40, 'Briefumschläge DIN C6', '50 Stück, haftklebend', 2.40, 62, 'img/Briefumschläge DIN C6.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('offen','in Bearbeitung','abgeschlossen','storniert') DEFAULT 'offen',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `material_id`, `quantity`, `status`, `created_at`) VALUES
(4, 1, 31, 1, 'offen', '2025-05-23 14:27:03'),
(5, 1, 30, 1, 'storniert', '2025-05-23 14:27:03'),
(6, 1, 32, 1, 'abgeschlossen', '2025-05-23 14:27:22'),
(7, 1, 27, 1, 'in Bearbeitung', '2025-05-23 14:28:01'),
(9, 1, 22, 1, 'abgeschlossen', '2025-05-23 14:28:33'),
(70, 4, 32, 1, 'storniert', '2025-05-27 21:22:29'),
(71, 4, 28, 1, 'abgeschlossen', '2025-05-27 21:22:53'),
(72, 4, 32, 1, 'in Bearbeitung', '2025-05-27 21:22:53'),
(73, 4, 33, 1, 'offen', '2025-05-27 21:39:50'),
(74, 4, 33, 1, 'offen', '2025-05-27 21:41:11');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('besteller','bearbeiter','admin') NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `status` enum('online','offline') NOT NULL DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `address`, `city`, `phone`, `email`, `password_hash`, `role`, `last_activity`, `status`) VALUES
(1, 'tamer', 'Tamer', 'Kurt', 'Musterstr. 2', '12345 Musterstadt', '040 55667788', 'tamer@gmail.com', '$2y$10$hfeB3.HcoScWdLoToBsNR.sC4KufWX1lBBKbQhrc.QA3N3oG1Yl3K', 'admin', '2025-05-28 03:18:31', 'offline'),
(2, 'admin', 'Tam', 'Tam', 'Adminstr. 1', '12345 Adminstadt', '040 1234567', 'admin@gmail.com', '$2y$10$wbIf2HaOnZQnPO7MVKD79uT4avjLOyoQbYADVd0C/QYoHKt6j2cyq', 'bearbeiter', '2025-05-28 03:18:31', 'offline'),
(3, 'bearbeiter1', 'Tim', 'Tommy', 'tak', 'tik', '04012585', 'bearbeiter@gmail.com', '$2y$10$wbIf2HaOnZQnPO7MVKD79uT4avjLOyoQbYADVd0C/QYoHKt6j2cyq', 'bearbeiter', NULL, 'offline'),
(4, 'besteller1', 'Max', 'Mister', 'straße 1', '22211 Hamburg', '040 542458', 'besteller@gmail.com', '$2y$10$wbIf2HaOnZQnPO7MVKD79uT4avjLOyoQbYADVd0C/QYoHKt6j2cyq', 'besteller', '2025-05-28 03:17:32', 'offline');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
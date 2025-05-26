-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Mai 2025 um 22:02
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
(26, 'Ordner A4', 'Breit, schwarz', 3.90, 60, 'img/Ordner A4.jpg'),
(27, 'Trennblätter', 'Farbige Registerblätter', 1.00, 88, 'img/Trennblätter.jpg'),
(28, 'Karteikarten', 'A7, unliniert', 1.50, 70, 'img/Karteikarten.jpg'),
(29, 'Büroklammern', '100 Stück, silber', 0.90, 175, 'img/Büroklammern.jpg'),
(30, 'Tacker', 'Standard-Tischhefter', 5.50, 39, 'img/Tacker.jpg'),
(31, 'Heftklammern', '1000 Stück, für Standard-Tacker', 1.10, 97, 'img/Heftklammern.jpg'),
(32, 'Locher', '2-fach-Locher, Metall', 4.20, 49, 'img/Locher.jpg'),
(33, 'Radiergummi', 'PVC-frei, weiß', 0.60, 100, 'img/Radiergummi.jpg'),
(34, 'Lineal 30cm', 'Transparent, Kunststoff', 0.90, 75, 'img/Lineal 30cm.jpg'),
(35, 'Notizzettel', 'Haftnotizen, 75x75mm, gelb', 1.30, 120, 'img/Notizzettel.jpg'),
(36, 'USB-Stick 16GB', 'Speicherstick für Daten', 7.90, 30, 'img/USB-Stick 16GB.jpg'),
(37, 'Schere', 'Büroschere, 21 cm', 2.80, 57, 'img/Schere.jpg'),
(38, 'Tesafilm', 'Klebefilm, transparent', 1.00, 90, 'img/Tesafilm.jpg'),
(39, 'Klebeband-Abroller', 'Für Standardrollen', 3.50, 43, 'img/Klebeband-Abroller.jpg'),
(40, 'Briefumschläge DIN C6', '50 Stück, haftklebend', 2.40, 64, 'img/Briefumschläge DIN C6.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('offen','bearbeitet') DEFAULT 'offen',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `material_id`, `quantity`, `status`, `created_at`) VALUES
(1, 1, 21, 2, 'offen', '2025-05-23 08:30:17'),
(2, 1, 22, 1, 'offen', '2025-05-23 08:35:47'),
(3, 1, 40, 1, 'offen', '2025-05-23 08:35:47'),
(4, 1, 31, 1, 'offen', '2025-05-23 14:27:03'),
(5, 1, 30, 1, 'offen', '2025-05-23 14:27:03'),
(6, 1, 32, 1, 'offen', '2025-05-23 14:27:22'),
(7, 1, 27, 1, 'offen', '2025-05-23 14:28:01'),
(8, 1, 27, 1, 'offen', '2025-05-23 14:28:16'),
(9, 1, 22, 1, 'offen', '2025-05-23 14:28:33'),
(10, 1, 24, 3, 'offen', '2025-05-23 14:29:08'),
(11, 1, 22, 5, 'offen', '2025-05-23 14:43:08'),
(12, 1, 40, 1, 'offen', '2025-05-23 14:43:08'),
(13, 1, 29, 1, 'offen', '2025-05-23 14:43:08'),
(14, 1, 25, 1, 'offen', '2025-05-23 14:51:48'),
(15, 1, 25, 1, 'offen', '2025-05-23 14:53:55'),
(16, 1, 22, 5, 'offen', '2025-05-23 14:55:55'),
(17, 1, 40, 1, 'offen', '2025-05-23 14:55:55'),
(18, 1, 29, 1, 'offen', '2025-05-23 14:55:55'),
(19, 1, 22, 1, 'offen', '2025-05-23 14:56:42'),
(20, 1, 40, 3, 'offen', '2025-05-23 14:56:42'),
(21, 1, 29, 5, 'offen', '2025-05-23 14:57:12'),
(22, 1, 22, 1, 'offen', '2025-05-23 14:59:24'),
(23, 1, 40, 1, 'offen', '2025-05-23 14:59:24'),
(24, 1, 29, 1, 'offen', '2025-05-23 14:59:24'),
(25, 1, 29, 1, 'offen', '2025-05-23 15:16:46'),
(26, 1, 40, 1, 'offen', '2025-05-23 15:21:13'),
(27, 1, 29, 1, 'offen', '2025-05-23 15:22:51'),
(28, 1, 40, 20, 'offen', '2025-05-23 15:23:52'),
(29, 1, 29, 1, 'offen', '2025-05-23 15:23:52'),
(30, 1, 40, 1, 'offen', '2025-05-23 15:37:48'),
(31, 1, 40, 1, 'offen', '2025-05-23 15:38:15'),
(32, 1, 40, 1, 'offen', '2025-05-23 15:38:21'),
(33, 1, 40, 1, 'offen', '2025-05-23 15:38:27'),
(34, 1, 29, 1, 'offen', '2025-05-23 15:38:27'),
(35, 1, 40, 1, 'offen', '2025-05-23 15:39:01'),
(36, 1, 29, 1, 'offen', '2025-05-23 15:39:01'),
(37, 1, 40, 1, 'offen', '2025-05-23 15:39:13'),
(38, 1, 29, 1, 'offen', '2025-05-23 15:39:13'),
(39, 1, 40, 1, 'offen', '2025-05-23 15:39:21'),
(40, 1, 29, 1, 'offen', '2025-05-23 15:39:21'),
(41, 1, 29, 1, 'offen', '2025-05-23 16:25:16'),
(42, 1, 29, 1, 'offen', '2025-05-23 16:30:53'),
(43, 1, 29, 1, 'offen', '2025-05-23 16:30:59'),
(44, 1, 29, 1, 'offen', '2025-05-23 16:31:03'),
(45, 1, 37, 1, 'offen', '2025-05-23 16:34:21'),
(46, 1, 37, 1, 'offen', '2025-05-23 16:34:48'),
(47, 1, 29, 1, 'offen', '2025-05-23 16:42:53'),
(48, 1, 31, 1, 'offen', '2025-05-23 16:42:53'),
(49, 1, 29, 1, 'offen', '2025-05-23 16:43:15'),
(50, 1, 31, 1, 'offen', '2025-05-23 16:43:15'),
(51, 1, 22, 1, 'offen', '2025-05-23 17:50:17'),
(52, 1, 39, 1, 'offen', '2025-05-23 17:50:17'),
(53, 1, 22, 1, 'offen', '2025-05-23 17:50:28'),
(54, 1, 39, 1, 'offen', '2025-05-23 17:50:28'),
(55, 1, 23, 3, 'offen', '2025-05-23 23:00:02'),
(56, 1, 23, 3, 'offen', '2025-05-23 23:00:17'),
(57, 1, 29, 2, 'offen', '2025-05-23 23:00:42'),
(58, 1, 29, 2, 'offen', '2025-05-23 23:00:45'),
(59, 1, 22, 1, 'offen', '2025-05-23 23:56:05'),
(60, 1, 22, 1, 'offen', '2025-05-23 23:56:28'),
(61, 1, 37, 1, 'offen', '2025-05-23 23:56:34'),
(62, 1, 40, 1, 'offen', '2025-05-24 17:02:25');

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
  `role` enum('besteller','bearbeiter','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `address`, `city`, `phone`, `email`, `password_hash`, `role`) VALUES
(1, 'tamer', 'Tamer', 'Kurt', 'Musterstr. 2', '12345 Musterstadt', '040 55667788', 'tamer@gmail.com', '$2y$10$OXj8KOJboT0Wnj297AGgPerqdStFb128siDDxz/w/hq2JNZxw2rwS', 'besteller');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

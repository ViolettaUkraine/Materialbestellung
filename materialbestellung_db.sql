-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Mai 2025 um 11:04
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
(22, 'Bleistift', 'HB-Bleistift mit Radiergummi', 0.50, 149, 'img/Bleistift.jpg'),
(23, 'Textmarker', 'Gelber Leuchtmarker', 1.20, 100, NULL),
(24, 'Collegeblock A4', 'Liniert, 80 Blatt', 2.50, 80, NULL),
(25, 'Heft A5', 'Liniert, 16 Seiten', 0.70, 120, NULL),
(26, 'Ordner A4', 'Breit, schwarz', 3.90, 60, NULL),
(27, 'Trennblätter', 'Farbige Registerblätter', 1.00, 90, NULL),
(28, 'Karteikarten', 'A7, unliniert', 1.50, 70, NULL),
(29, 'Büroklammern', '100 Stück, silber', 0.90, 200, NULL),
(30, 'Tacker', 'Standard-Tischhefter', 5.50, 40, NULL),
(31, 'Heftklammern', '1000 Stück, für Standard-Tacker', 1.10, 100, NULL),
(32, 'Locher', '2-fach-Locher, Metall', 4.20, 50, NULL),
(33, 'Radiergummi', 'PVC-frei, weiß', 0.60, 100, NULL),
(34, 'Lineal 30cm', 'Transparent, Kunststoff', 0.90, 75, NULL),
(35, 'Notizzettel', 'Haftnotizen, 75x75mm, gelb', 1.30, 120, NULL),
(36, 'USB-Stick 16GB', 'Speicherstick für Daten', 7.90, 30, NULL),
(37, 'Schere', 'Büroschere, 21 cm', 2.80, 60, NULL),
(38, 'Tesafilm', 'Klebefilm, transparent', 1.00, 90, NULL),
(39, 'Klebeband-Abroller', 'Für Standardrollen', 3.50, 45, NULL),
(40, 'Briefumschläge DIN C6', '50 Stück, haftklebend', 2.40, 99, NULL);

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
(3, 1, 40, 1, 'offen', '2025-05-23 08:35:47');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('besteller','bearbeiter','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`) VALUES
(1, 'tamer', '$2y$10$OXj8KOJboT0Wnj297AGgPerqdStFb128siDDxz/w/hq2JNZxw2rwS', 'besteller');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

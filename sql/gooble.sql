-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Mag 22, 2017 alle 18:55
-- Versione del server: 5.5.40
-- Versione PHP: 5.4.35-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gooble`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `percorso`
--

CREATE TABLE IF NOT EXISTS `percorso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `percorso`
--

INSERT INTO `percorso` (`id`, `desc`) VALUES
(1, 'Tre salite');

-- --------------------------------------------------------

--
-- Struttura della tabella `segmento`
--

CREATE TABLE IF NOT EXISTS `segmento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` varchar(255) NOT NULL,
  `stop` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `per_id` int(11) NOT NULL,
  `progr` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `per_id` (`per_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dump dei dati per la tabella `segmento`
--

INSERT INTO `segmento` (`id`, `start`, `stop`, `descr`, `per_id`, `progr`) VALUES
(1, 'Via Siepelunga, 2 Bologna', 'Via Santa Liberata Bologna', 'Monte Donato', 1, 10),
(2, 'via del genio 3 bologna', 'via di gaibola 6 bologna', 'via Del Genio', 1, 20),
(4, '44.489623,11.309306', '44.479385,11.296335', 'San Luca', 1, 30);

-- --------------------------------------------------------

--
-- Struttura della tabella `stato`
--

CREATE TABLE IF NOT EXISTS `stato` (
  `who` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `what` varchar(255) NOT NULL,
  `how` varchar(255) NOT NULL,
  `ts` datetime NOT NULL,
  PRIMARY KEY (`who`,`id`,`what`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `stato`
--

INSERT INTO `stato` (`who`, `id`, `what`, `how`, `ts`) VALUES
('gb', 0, 'v', '9', '2017-05-21 19:10:03'),
('wc', 0, 'p', '1', '2017-05-21 17:07:01');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `segmento`
--
ALTER TABLE `segmento`
  ADD CONSTRAINT `segmento_ibfk_1` FOREIGN KEY (`per_id`) REFERENCES `percorso` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Sam 13 Mars 2010 à 16:40
-- Version du serveur: 5.1.37
-- Version de PHP: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `drbl`
--

-- --------------------------------------------------------

--
-- Structure de la table `computers`
--

CREATE TABLE IF NOT EXISTS `computers` (
  `C_id` int(11) NOT NULL AUTO_INCREMENT,
  `C_name` varchar(30) NOT NULL,
  `C_mac_addr` varchar(17) NOT NULL,
  `C_room` varchar(30) NOT NULL,
  PRIMARY KEY (`C_id`),
  UNIQUE KEY `C_mac_addr` (`C_mac_addr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Contenu de la table `computers`
--


-- --------------------------------------------------------

--
-- Structure de la table `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `D_id` int(11) NOT NULL AUTO_INCREMENT,
  `D_name` varchar(30) NOT NULL,
  `D_host_admin` varchar(30) NOT NULL,
  `D_domain_admin` varchar(30) NOT NULL,
  `D_domain_password` varchar(30) NOT NULL,
  PRIMARY KEY (`D_id`),
  UNIQUE KEY `D_name` (`D_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `domain`
--


-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `I_id` int(11) NOT NULL AUTO_INCREMENT,
  `I_name` varchar(30) NOT NULL,
  `I_disk_name` varchar(3) NOT NULL,
  `I_description` varchar(30) NOT NULL,
  PRIMARY KEY (`I_id`),
  UNIQUE KEY `I_name` (`I_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Contenu de la table `images`
--


-- --------------------------------------------------------

--
-- Structure de la table `partitions`
--

CREATE TABLE IF NOT EXISTS `partitions` (
  `P_id` int(11) NOT NULL AUTO_INCREMENT,
  `P_name` varchar(4) NOT NULL,
  `P_description` varchar(30) NOT NULL,
  `P_id_image` int(11) NOT NULL,
  PRIMARY KEY (`P_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Contenu de la table `partitions`
--


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `U_id` int(11) NOT NULL AUTO_INCREMENT,
  `U_login` varchar(20) NOT NULL,
  `U_password` varchar(30) NOT NULL,
  `U_right` int(11) NOT NULL,
  PRIMARY KEY (`U_id`,`U_login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`U_id`, `U_login`, `U_password`, `U_right`) VALUES
(5, 'admin', 'admin', 10);

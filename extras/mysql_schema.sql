-- phpMyAdmin SQL Dump
-- version 3.1.3.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 10, 2011 at 02:20 PM
-- Server version: 5.0.77
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `pico_users`
--

DROP TABLE IF EXISTS `pico_users`;
CREATE TABLE IF NOT EXISTS `pico_users` (
  `id` int(11) NOT NULL auto_increment,
  `firstname` varchar(15) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `access_token` varchar(30) NOT NULL,
  `access_token_secret` varchar(50) NOT NULL,
  `login_token` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `firstname` (`firstname`,`surname`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

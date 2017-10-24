-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2017 at 05:55 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nbga`
--

-- --------------------------------------------------------

--
-- Table structure for table `attribute`
--

CREATE TABLE `attribute` (
  `id_attribute` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `used` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attribute`
--

INSERT INTO `attribute` (`id_attribute`, `name`, `used`) VALUES
(1, 'age', 1),
(2, 'sex', 1),
(3, 'cp', 1),
(4, 'trestbps', 0),
(5, 'chol', 0),
(6, 'fbs', 1),
(7, 'restecg', 0),
(8, 'thalach', 0),
(9, 'exang', 0),
(10, 'oldpeak', 0),
(11, 'slope', 0),
(12, 'ca', 1),
(13, 'thal', 1),
(14, 'num', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_likelihood`
--

CREATE TABLE `attribute_likelihood` (
  `id_likelihood` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `likelihood` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `class_prior`
--

CREATE TABLE `class_prior` (
  `id_prior` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `value` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `preprocessed_data`
--

CREATE TABLE `preprocessed_data` (
  `id_data` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `sex` int(11) NOT NULL,
  `cp` int(11) NOT NULL,
  `trestbps` int(11) NOT NULL,
  `chol` int(11) NOT NULL,
  `fbs` int(11) NOT NULL,
  `restecg` int(11) NOT NULL,
  `thalach` int(11) NOT NULL,
  `exang` int(11) NOT NULL,
  `oldpeak` int(11) NOT NULL,
  `slope` int(11) NOT NULL,
  `ca` int(11) NOT NULL,
  `thal` int(11) NOT NULL,
  `num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `raw_patient`
--

CREATE TABLE `raw_patient` (
  `id_patient` int(11) NOT NULL,
  `age` float DEFAULT NULL,
  `sex` float DEFAULT NULL,
  `cp` float DEFAULT NULL,
  `trestbps` float DEFAULT NULL,
  `chol` float DEFAULT NULL,
  `fbs` float DEFAULT NULL,
  `restecg` float DEFAULT NULL,
  `thalach` float DEFAULT NULL,
  `exang` float DEFAULT NULL,
  `oldpeak` float DEFAULT NULL,
  `slope` float DEFAULT NULL,
  `ca` float DEFAULT NULL,
  `thal` float DEFAULT NULL,
  `num` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attribute`
--
ALTER TABLE `attribute`
  ADD PRIMARY KEY (`id_attribute`);

--
-- Indexes for table `attribute_likelihood`
--
ALTER TABLE `attribute_likelihood`
  ADD PRIMARY KEY (`id_likelihood`),
  ADD KEY `id_attribute` (`id_attribute`);

--
-- Indexes for table `class_prior`
--
ALTER TABLE `class_prior`
  ADD PRIMARY KEY (`id_prior`);

--
-- Indexes for table `preprocessed_data`
--
ALTER TABLE `preprocessed_data`
  ADD PRIMARY KEY (`id_data`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `raw_patient`
--
ALTER TABLE `raw_patient`
  ADD PRIMARY KEY (`id_patient`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attribute`
--
ALTER TABLE `attribute`
  MODIFY `id_attribute` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `attribute_likelihood`
--
ALTER TABLE `attribute_likelihood`
  MODIFY `id_likelihood` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `class_prior`
--
ALTER TABLE `class_prior`
  MODIFY `id_prior` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `preprocessed_data`
--
ALTER TABLE `preprocessed_data`
  MODIFY `id_data` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `raw_patient`
--
ALTER TABLE `raw_patient`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `attribute_likelihood`
--
ALTER TABLE `attribute_likelihood`
  ADD CONSTRAINT `attribute_likelihood_ibfk_1` FOREIGN KEY (`id_attribute`) REFERENCES `attribute` (`id_attribute`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `preprocessed_data`
--
ALTER TABLE `preprocessed_data`
  ADD CONSTRAINT `preprocessed_data_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `raw_patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

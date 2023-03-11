-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 12, 2023 at 12:02 AM
-- Server version: 10.6.12-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sametozden_flipcoin`
--

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(6) NOT NULL,
  `roomid` varchar(100) NOT NULL,
  `roomname` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `amount` int(6) NOT NULL,
  `datex` int(15) NOT NULL,
  `p1id` int(5) NOT NULL,
  `p2id` int(5) NOT NULL,
  `p1info` varchar(100) NOT NULL,
  `p2info` varchar(100) NOT NULL,
  `winnerid` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userevents`
--

CREATE TABLE `userevents` (
  `id` int(7) NOT NULL,
  `userid` int(5) NOT NULL,
  `amount` int(7) NOT NULL,
  `event` varchar(10) NOT NULL,
  `datex` int(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `userkey` varchar(250) NOT NULL,
  `walletid` varchar(500) NOT NULL,
  `datex` int(15) NOT NULL,
  `amount` int(7) NOT NULL,
  `nickname` varchar(25) NOT NULL,
  `avatar` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userkey`, `walletid`, `datex`, `amount`, `nickname`, `avatar`) VALUES
(2, 'w4g1azq6yt243vt6f2gk85cna40jg832p3dnlb4g7ik9up0zikrinqkzptseq9jg9yuaz2h427v6y4rcwmv40steaqsisfes30hylbhnmcjflg442b1vq7qpkt692ffebm2gx603fesl7q31zk3phgqrljxwya5ab7y8a0ygtfvuyeme97tippzfc7q39te9hqinu8hoavpnspaborblwtziydxhi8nj23i6dhtyya0booqfdifdn2vk04', '111111', 1675629460, 2900, '', ''),
(3, 'ahumcbp84tj5wtkgjd48x8rxuq3pl64zi4vaj3gb82pc3fz547a7t7ey11gx3yd9cjqy412vzm6ak9l8n1w8mkr9ruhny8wo55ipfmh473qvvf68m573b7p0arlz71jj0jgfvml7z580edc2pn52i9brbgtey60p1ap1xpbwt0dfzef4ny5vr41bobr0oci1vcfjfq96nm6lc8s3jn7e1bkeosgctgz3lhqn84ujjeitir2p7hg6t56emj', '222222', 1675869688, 4300, '', ''),
(4, 'm4gv6smr943zsjhefeuxrcihvpythb62lvvqgdratxxx7y74zwduwwokuk4m9a826wgh9c6n5wn30cvh70ef3m56jgznm23rf3uz2237oaku2ummvuvgifni4ogw4fx0083ykno808phhyroaw3d4uc15ivnb8pqaaevnm2shmp08bq1o8g0z6b7bn28z9lr5ed5uyk2trwurbnmzuxzh49mdvz6ebcl0v7kpo718w7k3y92kry68mtb6j', '333333', 1675898109, 6800, '', ''),
(5, '0n46qd0h7d1f0rin15q9m3m3yfbp1jgreo2xwqhly1guzq6zc8p2xc65c49xk8drfibr16ciuv1vi0hebxct1ld3n1nre2kkborqsji8tep7j3kuw09mzsdv9ntlb0b4bpnisp4s3x7kp69hymbr3vbcopn6xtkop3uhqg1f96m9f3xtya92dlnxnk4lw49x4ptk9go5calcsirolo22xmscn385i38wh1ze03ckuwk8ewdu7i1d1l93hv', '666666', 1676299951, 4500, '', ''),
(6, '78ds67gs867ds6dsg7sd78d7sg86s7d8gg7', '444444', 1675869688, 3800, '', ''),
(7, 'sdgjkhdsg789sd7g89sd789gd789ds78dgs789g', '555555', 1675869688, 8600, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userevents`
--
ALTER TABLE `userevents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userevents`
--
ALTER TABLE `userevents`
  MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

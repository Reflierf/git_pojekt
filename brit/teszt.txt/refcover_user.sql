-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2018. Jan 15. 16:53
-- Kiszolgáló verziója: 10.1.16-MariaDB
-- PHP verzió: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `refcover`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `refcover_user`
--

CREATE TABLE `refcover_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_secret_mail` tinyint(1) NOT NULL DEFAULT '1',
  `user_email_code` varchar(64) NOT NULL,
  `user_firstname` varchar(200) NOT NULL,
  `user_lastname` varchar(200) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  `user_active` int(10) NOT NULL DEFAULT '0',
  `user_password_recover` int(11) NOT NULL DEFAULT '0',
  `user_type` int(1) NOT NULL DEFAULT '0',
  `user_allow_email` tinyint(1) NOT NULL DEFAULT '1',
  `user_profile` varchar(255) NOT NULL,
  `user_makecover` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `refcover_user`
--

INSERT INTO `refcover_user` (`user_id`, `user_name`, `user_password`, `user_email`, `user_secret_mail`, `user_email_code`, `user_firstname`, `user_lastname`, `user_ip`, `user_active`, `user_password_recover`, `user_type`, `user_allow_email`, `user_profile`, `user_makecover`) VALUES
(1, 'relierf', '37643e626fb594b41cf5c86683523cbb2fdb0ddc', 'ref@freemail.hu', 1, '096e9fa3e1287413263303f1c4072be66ea31a45', 'Béla', 'Freiler', '0', 1, 0, 1, 1, 'img/profile/relierf.png', 1),
(2, 'ref', '25c45a6e643ab5429078c3cc26d30f6f04973afe', 'ref68@upcmail.hu', 1, '90d9511598197de3bd608aed8ce4d44e8d5aa938', 'Béla', '', '0', 1, 0, 0, 1, 'img/profile/ref.jpg', 1),
(3, 'krisztabella', '7eeef1adc2b8704b52addb140ce64614f7c48769', 'refg@freemail.hu', 1, '5c51fe25599f270072243640a2c341ff931ea9ed', 'Krisztabella', '', '0', 1, 0, 0, 1, 'img/profile/krisztabella.jpg', 0),
(4, 'tessa', 'c74cbd47699f3274bebe5403fc89170a4e914508', 'cica.reff@freemail.hu', 1, 'a5b78cbd4635a65a7cb09adef148abb97ad17df0', 'Tessa', 'Fowler', '0', 0, 0, 0, 1, 'img/profile/tessa.jpg', 0);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `refcover_user`
--
ALTER TABLE `refcover_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_type` (`user_type`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `refcover_user`
--
ALTER TABLE `refcover_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

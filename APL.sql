-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 01, 2023 at 01:45 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `APL`
--

-- --------------------------------------------------------

--
-- Table structure for table `AppUser`
--

CREATE TABLE `AppUser` (
  `user_id` int(11) NOT NULL,
  `hashed_user_id` varchar(255) DEFAULT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 0,
  `activation_code` varchar(255) NOT NULL,
  `activation_expiry` datetime NOT NULL,
  `activated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login_at` datetime DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `AppUser`
--

INSERT INTO `AppUser` (`user_id`, `hashed_user_id`, `fname`, `lname`, `gender`, `date_of_birth`, `email_address`, `user_password`, `mobile_number`, `is_admin`, `is_active`, `activation_code`, `activation_expiry`, `activated_at`, `created_at`, `updated_at`, `last_login_at`, `team_id`) VALUES
(1, '$2y$10$9cTyIJTJMJT/7bfBb3oule0dyB2LDMhUwpbLveKntBKS5KUUQlP2a', 'Kwaku', 'Osafo', 'Male', '2002-07-03', 'kwakuosafo20@gmail.com', '$2y$10$jRapuYWWFTdlX5MPNIjN6OsRrT43SLwbPv23Z743sLKbaULZ.iFly', '0201579150', 1, 1, '99d3481ea29f9432a33940c655b048d7c13b9c10169775891ba90e73c034d9ff', '2023-07-12 19:46:18', '2023-07-11 17:47:54', '2023-07-11 17:46:18', '2023-09-16 03:49:35', '2023-09-16 03:49:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Assist`
--

CREATE TABLE `Assist` (
  `assist_id` int(11) NOT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Coach`
--

CREATE TABLE `Coach` (
  `coach_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `year_group` int(11) DEFAULT NULL,
  `gender` varchar(6) NOT NULL,
  `is_retired` tinyint(1) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `Competition`
--

CREATE TABLE `Competition` (
  `competition_id` int(11) NOT NULL,
  `competition_name` varchar(255) NOT NULL,
  `competition_abbrev` varchar(10) DEFAULT NULL,
  `gender` varchar(6) NOT NULL
) ;

--
-- Dumping data for table `Competition`
--

INSERT INTO `Competition` (`competition_id`, `competition_name`, `competition_abbrev`, `gender`) VALUES
(1, 'Ashesi Premier League', 'APL', 'Male'),
(2, 'Ashesi Champions League', 'ACL', 'Male'),
(3, 'Ashesi FA Cup', 'AFA Cup', 'Male'),
(4, 'Ashesi Premier League', 'APL', 'Female'),
(5, 'Ashesi Champions League', 'ACL', 'Female'),
(6, 'Ashesi FA Cup', 'AFA Cup', 'Female');

-- --------------------------------------------------------

--
-- Table structure for table `CupGame`
--

CREATE TABLE `CupGame` (
  `game_id` int(11) DEFAULT NULL,
  `stage_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Foul`
--

CREATE TABLE `Foul` (
  `foul_id` int(11) NOT NULL,
  `minute_fouled` time DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Game`
--

CREATE TABLE `Game` (
  `game_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `gameweek_id` int(11) DEFAULT NULL,
  `home_id` int(11) DEFAULT NULL,
  `away_id` int(11) DEFAULT NULL,
  `competition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Gameweek`
--

CREATE TABLE `Gameweek` (
  `gameweek_id` int(11) NOT NULL,
  `gameweek_date` date NOT NULL,
  `gameweek_number` int(11) NOT NULL,
  `season_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Goal`
--

CREATE TABLE `Goal` (
  `goal_id` int(11) NOT NULL,
  `minute_scored` int(11) DEFAULT NULL,
  `player_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ManOfTheMatch`
--

CREATE TABLE `ManOfTheMatch` (
  `game_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `NewsItem`
--

CREATE TABLE `NewsItem` (
  `news_item_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `news_tag_id` int(11) DEFAULT NULL,
  `cover_pic` varchar(255) DEFAULT NULL,
  `time_published` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `NewsTag`
--

CREATE TABLE `NewsTag` (
  `news_tag_id` int(11) NOT NULL,
  `news_tag_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `NewsTag`
--

INSERT INTO `NewsTag` (`news_tag_id`, `news_tag_name`) VALUES
(1, 'Elite'),
(2, 'Highlanders'),
(3, 'Kasanoma'),
(4, 'Legends United'),
(5, 'Northside'),
(6, 'Red Army'),
(7, 'ACL'),
(8, 'APL'),
(9, 'FA Cup'),
(10, 'Women\'s football'),
(11, 'Men\'s Football'),
(12, 'Inter-Class'),
(13, 'Course Clash Cup'),
(14, 'Inter-School'),
(15, 'Tactics'),
(16, 'Press conference'),
(17, 'Transfer'),
(18, 'Injury'),
(19, 'Suspension'),
(20, 'Match preview'),
(21, 'Match report');

-- --------------------------------------------------------

--
-- Table structure for table `PasswordResetTemp`
--

CREATE TABLE `PasswordResetTemp` (
  `reset_token_id` int(11) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) NOT NULL,
  `password_reset_expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PenaltyShootout`
--

CREATE TABLE `PenaltyShootout` (
  `shootout_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PenaltyShootoutPlayer`
--

CREATE TABLE `PenaltyShootoutPlayer` (
  `scored` tinyint(1) DEFAULT NULL,
  `shootout_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Player`
--

CREATE TABLE `Player` (
  `player_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `year_group` int(11) DEFAULT NULL,
  `gender` varchar(6) NOT NULL,
  `is_retired` tinyint(1) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `PlayerPosition`
--

CREATE TABLE `PlayerPosition` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL,
  `position_abbrev` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PlayerPosition`
--

INSERT INTO `PlayerPosition` (`position_id`, `position_name`, `position_abbrev`) VALUES
(1, 'Goalkeeper', 'GK'),
(2, 'Defender', 'DEF'),
(3, 'Midfielder', 'MID'),
(4, 'Forward', 'FW');

-- --------------------------------------------------------

--
-- Table structure for table `RedCard`
--

CREATE TABLE `RedCard` (
  `foul_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Season`
--

CREATE TABLE `Season` (
  `season_id` int(11) NOT NULL,
  `season_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SeasonCompetition`
--

CREATE TABLE `SeasonCompetition` (
  `season_id` int(11) DEFAULT NULL,
  `competition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Stage`
--

CREATE TABLE `Stage` (
  `stage_id` int(11) NOT NULL,
  `stage_name` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Stage`
--

INSERT INTO `Stage` (`stage_id`, `stage_name`) VALUES
(1, 'Group Stage'),
(2, 'Quarter-Finals'),
(3, 'Semi-Finals'),
(4, 'Finals');

-- --------------------------------------------------------

--
-- Table structure for table `Standings`
--

CREATE TABLE `Standings` (
  `standings_id` int(11) NOT NULL,
  `standings_name` varchar(255) DEFAULT NULL,
  `season_id` int(11) DEFAULT NULL,
  `competition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StandingsTeam`
--

CREATE TABLE `StandingsTeam` (
  `points` int(11) DEFAULT 0,
  `goals_scored` int(11) DEFAULT 0,
  `goals_conceded` int(11) DEFAULT 0,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `draws` int(11) DEFAULT 0,
  `no_played` int(11) DEFAULT 0,
  `goal_difference` int(11) DEFAULT 0,
  `standings_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StartingXI`
--

CREATE TABLE `StartingXI` (
  `xi_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StartingXIPlayer`
--

CREATE TABLE `StartingXIPlayer` (
  `xi_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Substitution`
--

CREATE TABLE `Substitution` (
  `substitution_id` int(11) NOT NULL,
  `substitution_time` time DEFAULT NULL,
  `player_in` int(11) DEFAULT NULL,
  `player_out` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Team`
--

CREATE TABLE `Team` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `team_name_abbrev` varchar(5) DEFAULT NULL,
  `team_logo_url` text DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `color_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Team`
--

INSERT INTO `Team` (`team_id`, `team_name`, `team_name_abbrev`, `team_logo_url`, `twitter_url`, `color_code`) VALUES
(1, 'Elite', 'ELI', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/elite_logo_hbxvxi.png', 'https://twitter.com/elite1_fc', '121354'),
(2, 'Legends United', 'LU', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/lu_logo_wqdrik.jpg', 'https://twitter.com/LUFCau', '000000'),
(3, 'Highlanders', 'HIG', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/highlanders_logo_g8mdfa.jpg', 'https://twitter.com/Highlandersoff1', 'a79958'),
(4, 'Kasanoma', 'KAS', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/kasanoma_logo_slt3hs.jpg', 'https://twitter.com/FcKasanoma', '0b6667'),
(5, 'Northside', 'NOR', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/northside_logo_ms6jgd.jpg', 'https://twitter.com/NorthsideFooty', 'a7a6ab'),
(6, 'Red Army', 'RAR', 'https://res.cloudinary.com/dvghxq3ba/image/upload/v1691588180/Team%20Logos/red_army_logo_ydwfpc.jpg', 'https://twitter.com/officalRedArmy', 'e8272c');

-- --------------------------------------------------------

--
-- Table structure for table `Transfer`
--

CREATE TABLE `Transfer` (
  `transfer_id` int(11) NOT NULL,
  `transfer_date` date DEFAULT NULL,
  `transferred_player_id` int(11) DEFAULT NULL,
  `new_team_id` int(11) DEFAULT NULL,
  `prev_team_id` int(11) DEFAULT NULL,
  `transfer_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `YellowCard`
--

CREATE TABLE `YellowCard` (
  `foul_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AppUser`
--
ALTER TABLE `AppUser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD UNIQUE KEY `hashed_user_id` (`hashed_user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `Assist`
--
ALTER TABLE `Assist`
  ADD PRIMARY KEY (`assist_id`),
  ADD KEY `goal_id` (`goal_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `Coach`
--
ALTER TABLE `Coach`
  ADD PRIMARY KEY (`coach_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `Competition`
--
ALTER TABLE `Competition`
  ADD PRIMARY KEY (`competition_id`);

--
-- Indexes for table `CupGame`
--
ALTER TABLE `CupGame`
  ADD KEY `game_id` (`game_id`),
  ADD KEY `stage_id` (`stage_id`);

--
-- Indexes for table `Foul`
--
ALTER TABLE `Foul`
  ADD PRIMARY KEY (`foul_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `Game`
--
ALTER TABLE `Game`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `home_id` (`home_id`),
  ADD KEY `away_id` (`away_id`),
  ADD KEY `gameweek_id` (`gameweek_id`),
  ADD KEY `competition_id` (`competition_id`);

--
-- Indexes for table `Gameweek`
--
ALTER TABLE `Gameweek`
  ADD PRIMARY KEY (`gameweek_id`),
  ADD KEY `season_id` (`season_id`);

--
-- Indexes for table `Goal`
--
ALTER TABLE `Goal`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `ManOfTheMatch`
--
ALTER TABLE `ManOfTheMatch`
  ADD KEY `game_id` (`game_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `NewsItem`
--
ALTER TABLE `NewsItem`
  ADD PRIMARY KEY (`news_item_id`),
  ADD KEY `news_tag_id` (`news_tag_id`);

--
-- Indexes for table `NewsTag`
--
ALTER TABLE `NewsTag`
  ADD PRIMARY KEY (`news_tag_id`);

--
-- Indexes for table `PasswordResetTemp`
--
ALTER TABLE `PasswordResetTemp`
  ADD PRIMARY KEY (`reset_token_id`),
  ADD KEY `email_address` (`email_address`);

--
-- Indexes for table `PenaltyShootout`
--
ALTER TABLE `PenaltyShootout`
  ADD PRIMARY KEY (`shootout_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `PenaltyShootoutPlayer`
--
ALTER TABLE `PenaltyShootoutPlayer`
  ADD KEY `shootout_id` (`shootout_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `Player`
--
ALTER TABLE `Player`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `PlayerPosition`
--
ALTER TABLE `PlayerPosition`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `RedCard`
--
ALTER TABLE `RedCard`
  ADD KEY `foul_id` (`foul_id`);

--
-- Indexes for table `Season`
--
ALTER TABLE `Season`
  ADD PRIMARY KEY (`season_id`),
  ADD UNIQUE KEY `season_name` (`season_name`);

--
-- Indexes for table `SeasonCompetition`
--
ALTER TABLE `SeasonCompetition`
  ADD KEY `season_id` (`season_id`),
  ADD KEY `competition_id` (`competition_id`);

--
-- Indexes for table `Stage`
--
ALTER TABLE `Stage`
  ADD PRIMARY KEY (`stage_id`);

--
-- Indexes for table `Standings`
--
ALTER TABLE `Standings`
  ADD PRIMARY KEY (`standings_id`),
  ADD KEY `season_id` (`season_id`),
  ADD KEY `competition_id` (`competition_id`);

--
-- Indexes for table `StandingsTeam`
--
ALTER TABLE `StandingsTeam`
  ADD KEY `standings_id` (`standings_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `StartingXI`
--
ALTER TABLE `StartingXI`
  ADD PRIMARY KEY (`xi_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `StartingXIPlayer`
--
ALTER TABLE `StartingXIPlayer`
  ADD KEY `xi_id` (`xi_id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `Substitution`
--
ALTER TABLE `Substitution`
  ADD PRIMARY KEY (`substitution_id`),
  ADD KEY `player_in` (`player_in`),
  ADD KEY `player_out` (`player_out`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `Team`
--
ALTER TABLE `Team`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `Transfer`
--
ALTER TABLE `Transfer`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `transferred_player_id` (`transferred_player_id`),
  ADD KEY `new_team_id` (`new_team_id`),
  ADD KEY `prev_team_id` (`prev_team_id`);

--
-- Indexes for table `YellowCard`
--
ALTER TABLE `YellowCard`
  ADD KEY `foul_id` (`foul_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `AppUser`
--
ALTER TABLE `AppUser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Coach`
--
ALTER TABLE `Coach`
  MODIFY `coach_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Competition`
--
ALTER TABLE `Competition`
  MODIFY `competition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Foul`
--
ALTER TABLE `Foul`
  MODIFY `foul_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Game`
--
ALTER TABLE `Game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Gameweek`
--
ALTER TABLE `Gameweek`
  MODIFY `gameweek_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Goal`
--
ALTER TABLE `Goal`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NewsItem`
--
ALTER TABLE `NewsItem`
  MODIFY `news_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NewsTag`
--
ALTER TABLE `NewsTag`
  MODIFY `news_tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `PasswordResetTemp`
--
ALTER TABLE `PasswordResetTemp`
  MODIFY `reset_token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PenaltyShootout`
--
ALTER TABLE `PenaltyShootout`
  MODIFY `shootout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Player`
--
ALTER TABLE `Player`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PlayerPosition`
--
ALTER TABLE `PlayerPosition`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Season`
--
ALTER TABLE `Season`
  MODIFY `season_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Stage`
--
ALTER TABLE `Stage`
  MODIFY `stage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Standings`
--
ALTER TABLE `Standings`
  MODIFY `standings_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `StartingXI`
--
ALTER TABLE `StartingXI`
  MODIFY `xi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Substitution`
--
ALTER TABLE `Substitution`
  MODIFY `substitution_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Team`
--
ALTER TABLE `Team`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Transfer`
--
ALTER TABLE `Transfer`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AppUser`
--
ALTER TABLE `AppUser`
  ADD CONSTRAINT `appuser_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Assist`
--
ALTER TABLE `Assist`
  ADD CONSTRAINT `assist_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `Goal` (`goal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assist_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Coach`
--
ALTER TABLE `Coach`
  ADD CONSTRAINT `coach_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `CupGame`
--
ALTER TABLE `CupGame`
  ADD CONSTRAINT `cupgame_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cupgame_ibfk_2` FOREIGN KEY (`stage_id`) REFERENCES `Stage` (`stage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Foul`
--
ALTER TABLE `Foul`
  ADD CONSTRAINT `foul_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foul_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Game`
--
ALTER TABLE `Game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`home_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`away_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`gameweek_id`) REFERENCES `Gameweek` (`gameweek_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_4` FOREIGN KEY (`competition_id`) REFERENCES `Competition` (`competition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Gameweek`
--
ALTER TABLE `Gameweek`
  ADD CONSTRAINT `gameweek_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `Season` (`season_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Goal`
--
ALTER TABLE `Goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goal_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goal_ibfk_3` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ManOfTheMatch`
--
ALTER TABLE `ManOfTheMatch`
  ADD CONSTRAINT `manofthematch_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `manofthematch_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NewsItem`
--
ALTER TABLE `NewsItem`
  ADD CONSTRAINT `newsitem_ibfk_1` FOREIGN KEY (`news_tag_id`) REFERENCES `NewsTag` (`news_tag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PasswordResetTemp`
--
ALTER TABLE `PasswordResetTemp`
  ADD CONSTRAINT `passwordresettemp_ibfk_1` FOREIGN KEY (`email_address`) REFERENCES `AppUser` (`email_address`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PenaltyShootout`
--
ALTER TABLE `PenaltyShootout`
  ADD CONSTRAINT `penaltyshootout_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PenaltyShootoutPlayer`
--
ALTER TABLE `PenaltyShootoutPlayer`
  ADD CONSTRAINT `penaltyshootoutplayer_ibfk_1` FOREIGN KEY (`shootout_id`) REFERENCES `PenaltyShootout` (`shootout_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penaltyshootoutplayer_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Player`
--
ALTER TABLE `Player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `player_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `PlayerPosition` (`position_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `RedCard`
--
ALTER TABLE `RedCard`
  ADD CONSTRAINT `redcard_ibfk_1` FOREIGN KEY (`foul_id`) REFERENCES `Foul` (`foul_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SeasonCompetition`
--
ALTER TABLE `SeasonCompetition`
  ADD CONSTRAINT `seasoncompetition_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `Season` (`season_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `seasoncompetition_ibfk_2` FOREIGN KEY (`competition_id`) REFERENCES `Competition` (`competition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Standings`
--
ALTER TABLE `Standings`
  ADD CONSTRAINT `standings_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `Season` (`season_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `standings_ibfk_2` FOREIGN KEY (`competition_id`) REFERENCES `Competition` (`competition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StandingsTeam`
--
ALTER TABLE `StandingsTeam`
  ADD CONSTRAINT `standingsteam_ibfk_1` FOREIGN KEY (`standings_id`) REFERENCES `Standings` (`standings_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `standingsteam_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StartingXI`
--
ALTER TABLE `StartingXI`
  ADD CONSTRAINT `startingxi_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `startingxi_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StartingXIPlayer`
--
ALTER TABLE `StartingXIPlayer`
  ADD CONSTRAINT `startingxiplayer_ibfk_1` FOREIGN KEY (`xi_id`) REFERENCES `StartingXI` (`xi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `startingxiplayer_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `startingxiplayer_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `PlayerPosition` (`position_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Substitution`
--
ALTER TABLE `Substitution`
  ADD CONSTRAINT `substitution_ibfk_1` FOREIGN KEY (`player_in`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `substitution_ibfk_2` FOREIGN KEY (`player_out`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `substitution_ibfk_3` FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Transfer`
--
ALTER TABLE `Transfer`
  ADD CONSTRAINT `transfer_ibfk_1` FOREIGN KEY (`transferred_player_id`) REFERENCES `Player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transfer_ibfk_2` FOREIGN KEY (`new_team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transfer_ibfk_3` FOREIGN KEY (`prev_team_id`) REFERENCES `Team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `YellowCard`
--
ALTER TABLE `YellowCard`
  ADD CONSTRAINT `yellowcard_ibfk_1` FOREIGN KEY (`foul_id`) REFERENCES `Foul` (`foul_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

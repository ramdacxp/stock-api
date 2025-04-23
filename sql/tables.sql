DROP TABLE IF EXISTS `history`;
DROP TABLE IF EXISTS `stocks`;
DROP TABLE IF EXISTS `stockdata`;

CREATE TABLE IF NOT EXISTS `stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `isin` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=3;

-- Sample Data
INSERT INTO `stocks` (`id`, `name`, `isin`) VALUES
(1, 'Microsoft', 'US5949181045'),
(2, 'Apple', 'US0378331005');

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` int(11) NOT NULL,
  `ts` datetime NOT NULL DEFAULT current_timestamp(),
  `price` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `RefStocksId` (`ref`)
) AUTO_INCREMENT=7;

ALTER TABLE `history`
  ADD CONSTRAINT `RefStocksId`
  FOREIGN KEY (`ref`) REFERENCES `stocks` (`id`);

-- Sample Data
INSERT INTO `history` (`id`, `ref`, `ts`, `price`) VALUES
(1, 1, '2025-03-03 07:48:00', 1.5),
(2, 1, '2025-03-03 08:48:00', 2),
(3, 1, '2025-03-03 09:48:15', 3.75),
(4, 1, '2025-03-04 07:48:15', 0),
(5, 1, '2025-03-04 08:48:30', 5.55),
(6, 1, '2025-03-04 09:48:30', 7.01);

-- New simple combined table
CREATE TABLE IF NOT EXISTS `stockdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `isin` tinytext NOT NULL,
  `price` float NOT NULL,
  `priceChange` datetime NOT NULL DEFAULT current_timestamp(),
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

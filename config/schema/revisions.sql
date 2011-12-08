
CREATE TABLE IF NOT EXISTS `revisions` (
  `id` varchar(36) NOT NULL,
  `row_id` varchar(36) NOT NULL,
  `model` varchar(100) NOT NULL,
  `data` longblob NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `row_id` (`row_id`,`model`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

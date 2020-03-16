CREATE TABLE IF NOT EXISTS`subs` (
  `chat_id` int(11) NOT NULL,
  `name` varchar(1000) COLLATE utf8_bin NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT '1',
  `last_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(1000) COLLATE utf8_bin NOT NULL,
  `ts_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
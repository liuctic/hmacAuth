-- run this sql directly OR use the build-in buildDB function to add this table 

CREATE TABLE `hmacauth` (
      `user_id` int(10) unsigned NOT NULL,
      `user_name` varchar(64) NOT NULL,
      `user_passwd_hash` varchar(40) NOT NULL COMMENT 'password md5 hash',
      `user_passwd_key` varchar(40) NOT NULL COMMENT 'random key',
      `user_passwd_auth_hash` varchar(80) NOT NULL COMMENT 'auth hash',
      `user_auth_expire_time` datetime NOT NULL,
      `user_auth_last_time` datetime NOT NULL COMMENT 'last success time',
      `lock_auth` tinyint(4) NOT NULL DEFAULT '0',
      PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8


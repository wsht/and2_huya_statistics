create TABLE danmu_user(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`)
);

create TABLE danmu_count_minutes_5(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `date` TIMESTAMP NULL DEFAULT NULL ,
  `timer` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`rid`)
);

create TABLE danmu_count_hours(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `date` TIMESTAMP NULL DEFAULT NULL ,
  `timer` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`rid`)
);


CREATE TABLE danmu_count_day(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `date` TIMESTAMP NULL DEFAULT NULL ,
  `timer` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`rid`)
);

CREATE TABLE danmu_count_total(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `timer` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`rid`)
);

CREATE TABLE danmu_message(
  `id` VARCHAR(50) NOT NULL DEFAULT '',
  `content` TEXT NOT NULL DEFAULT '',
  `sendTime` TIMESTAMP NULL DEFAULT NULL ,
  `rid` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

ALTER TABLE danmu_user add ctime TIMESTAMP NULL DEFAULT  NULL;


ALTER TABLE danmu_message add `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1:chat, 2:gift';

ALTER TABLE danmu_count_minutes_5 add `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1:chat, 2:gift';
ALTER TABLE danmu_count_hours add `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1:chat, 2:gift';
ALTER TABLE danmu_count_day add `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1:chat, 2:gift';
ALTER TABLE danmu_count_total add `type` TINYINT NOT NULL DEFAULT 1 COMMENT '1:chat, 2:gift';

alter TABLE danmu_count_minutes_5 DROP PRIMARY KEY ;
ALTER TABLE danmu_count_minutes_5 add PRIMARY KEY (`date`,`type`,`rid`);

alter TABLE danmu_count_hours DROP PRIMARY KEY ;
ALTER TABLE danmu_count_hours add PRIMARY KEY (`date`,`type`,`rid`);

alter TABLE danmu_count_day DROP PRIMARY KEY ;
ALTER TABLE danmu_count_day add PRIMARY KEY (`date`,`type`,`rid`);

alter TABLE danmu_count_total DROP PRIMARY KEY ;
ALTER TABLE danmu_count_total add PRIMARY KEY (`rid`,`type`);


create TABLE gift_detail(
  `id` INT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL DEFAULT '',
  `price` NUMERIC(10,2) NOT NULL  DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
);

create table gift_count_hours(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `date` TIMESTAMP NULL DEFAULT NULL ,
  `giftid` INT UNSIGNED NOT NULL DEFAULT 0,
  `timer` BIGINT NOT NULL DEFAULT 0,

  PRIMARY KEY (`date`, `giftid`, `rid`)
);

CREATE TABLE gift_count_total(
  `rid` BIGINT NOT NULL DEFAULT 0,
  `giftid` INT UNSIGNED NOT NULL DEFAULT 0,
  `timer` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`giftid`, `rid`)
);


create table gift_history(
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `rid` BIGINT UNSIGNED NOT NULL  DEFAULT 0,
  `gift_id` INT(10) UNSIGNED NOT NULL  DEFAULT 0,
  `count` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `price` NUMERIC(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `ctime` TIMESTAMP NULL  DEFAULT NULL ,
  PRIMARY KEY (`id`),
  INDEX (`rid`)
);





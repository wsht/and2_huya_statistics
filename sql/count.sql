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


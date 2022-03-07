CREATE TABLE "sessions" (
    "id" varchar(120) NOT NULL,
    "salt" varchar(40) NOT NULL DEFAULT '',
    "timestamp" int(11) NOT NULL,
    "data" blob,
    PRIMARY KEY  ("id"),
    KEY "timestamp" ("timestamp")
) ENGINE=innoDB DEFAULT CHARSET=utf8;

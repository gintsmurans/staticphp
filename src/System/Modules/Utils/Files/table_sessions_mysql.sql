CREATE TABLE "sessions" (
  "id" varchar(120) NOT NULL,
  "data" blob,
  "timestamp" int(11) NOT NULL,
  PRIMARY KEY  ("id"),
  KEY "timestamp" ("timestamp")
) ENGINE=innoDB DEFAULT CHARSET=utf8;

CREATE TABLE "public"."i18n_cached" (
    "id" text NOT NULL,
    "created" bigint NOT NULL DEFAULT date_part('epoch'::text, now()),
    PRIMARY KEY ("id")
);


CREATE TABLE "public"."i18n_keys" (
    "id" serial NOT NULL,
    "created" Bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    "key" Text DEFAULT ''::text NOT NULL,
    PRIMARY KEY ( "id" ),
    CONSTRAINT "i18n_keys_key_key" UNIQUE( "key" ) );
 ;

CREATE TABLE "public"."i18n_translations" (
    "id" serial NOT NULL,
    "key_id" Integer,
    "created" Bigint DEFAULT date_part('epoch'::text, now()) NOT NULL,
    "language" Character Varying( 24 ) NOT NULL,
    "value" Text DEFAULT ''::text NOT NULL,
    PRIMARY KEY ( "id" ) );
 ;

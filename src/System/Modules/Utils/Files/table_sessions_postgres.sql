CREATE TABLE public.sessions (
    id varchar(120) NOT NULL,
    salt varchar(40) NOT NULL DEFAULT '',
    "timestamp" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "data" text NOT NULL DEFAULT '',
    CONSTRAINT sessions_pk PRIMARY KEY (id)
);
CREATE INDEX sessions_timestamp_idx ON public.sessions ("timestamp");

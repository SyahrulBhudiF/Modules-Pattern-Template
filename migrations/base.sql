CREATE EXTENSION IF NOT EXISTS "pgcrypto";

CREATE TABLE IF NOT EXISTS users
(
    id         UUID PRIMARY KEY                  DEFAULT gen_random_uuid(),
    username   VARCHAR(100)             NOT NULL UNIQUE,
    password   VARCHAR(255)             NOT NULL,
    email      VARCHAR(255) UNIQUE,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    deleted_at TIMESTAMP WITH TIME ZONE
);

-- file: backend/database/migrations/001_create_entries.sql

CREATE TABLE entries (
    id CHAR(36) NOT NULL PRIMARY KEY,       -- UUID v4 (EntryId)
    title VARCHAR(200) NOT NULL,            -- BR-1: 1..200 chars after trimming
    body TEXT NOT NULL,                     -- BR-2: up to 50000 chars
    date DATE NOT NULL,                     -- BR-6: YYYY-MM-DD, valid calendar date
    created_at DATETIME NOT NULL,           -- BR-4: UTC timestamps
    updated_at DATETIME NOT NULL,           -- BR-4: UTC timestamps
    INDEX idx_entries_date (date)           -- optional: helps queries by entry date
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


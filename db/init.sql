-- USE cyber;

-- -- CREATE TABLE IF NOT EXISTS users (
-- --  id INT AUTO_INCREMENT PRIMARY KEY,
-- --  username VARCHAR(50),
-- --  password VARCHAR(50)
-- -- );

-- CREATE TABLE users (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   nim VARCHAR(20),
--   team VARCHAR(50),
--   role ENUM('mahasiswa','dosen'),
--   password VARCHAR(50)
-- );

-- INSERT INTO users (username, password)
-- VALUES ('admin','admin123');

-- CREATE TABLE IF NOT EXISTS comments (
--  comment TEXT
-- );

-- CREATE TABLE flags (
--  id INT AUTO_INCREMENT PRIMARY KEY,
--  flag VARCHAR(100)
-- );

-- INSERT INTO flags VALUES
-- (1,'CTF{basic_sql_injection}');

-- CREATE TABLE students (
--     nim VARCHAR(20) PRIMARY KEY,
--     name VARCHAR(100)
-- );

-- CREATE TABLE scores (
--     nim VARCHAR(20),
--     score INT DEFAULT 0,
--     grade CHAR(1),
--     PRIMARY KEY (nim)
-- );

-- -- CREATE TABLE submissions (
-- --     id INT AUTO_INCREMENT PRIMARY KEY,
-- --     nim VARCHAR(20),
-- --     challenge VARCHAR(20),
-- --     time DATETIME,
-- --     score INT
-- -- );

-- CREATE TABLE submissions (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   user_id INT,
--   challenge VARCHAR(50),
--   score INT,
--   submit_time DATETIME
-- );

-- INSERT INTO users (nim, team, role, password)
-- VALUES ('0000', 'DOSEN', 'dosen', 'admin');

-- INSERT INTO users (nim, team, role, password)
-- VALUES ('12345678', 'TIM_A', 'mahasiswa', '123');

DROP DATABASE IF EXISTS cyber;
CREATE DATABASE cyber;
USE cyber;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nim VARCHAR(20),
  team VARCHAR(50),
  role ENUM('mahasiswa','dosen'),
  password VARCHAR(50)
);

CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  challenge VARCHAR(50),
  score INT,
  submit_time DATETIME
);

INSERT INTO users (nim, team, role, password)
VALUES ('0000', 'DOSEN', 'dosen', 'admin');

INSERT INTO users (nim, team, role, password)
VALUES ('12345678', 'TIM_A', 'mahasiswa', '123');
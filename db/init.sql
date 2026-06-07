-- Database Cyber UAS - Modern CTF Platform
DROP DATABASE IF EXISTS cyber;
CREATE DATABASE cyber;
USE cyber;

-- Users Table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nim VARCHAR(20) UNIQUE,
  name VARCHAR(100),
  team VARCHAR(50),
  role ENUM('admin', 'peserta') DEFAULT 'peserta',
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Challenges Table
CREATE TABLE challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  category ENUM('web', 'crypto', 'forensics', 'reverse', 'network', 'linux') NOT NULL,
  difficulty ENUM('easy', 'medium', 'hard') NOT NULL,
  description TEXT,
  points INT DEFAULT 100,
  flag VARCHAR(255) NOT NULL,
  downloads TEXT,
  hints TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Submissions Table
CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  challenge_id INT,
  flag VARCHAR(255),
  is_correct BOOLEAN DEFAULT FALSE,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE,
  UNIQUE KEY unique_submission (user_id, challenge_id)
);

-- Challenge Solves (for tracking solve counts)
CREATE TABLE challenge_solves (
  challenge_id INT PRIMARY KEY,
  solve_count INT DEFAULT 0,
  FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
);

-- Insert Default Admin (username: admin, password: admin123)
INSERT INTO users (nim, name, team, role, password)
VALUES ('0000', 'Administrator', 'ADMIN', 'admin', 'admin123');

-- Insert Sample Challenges
INSERT INTO challenges (title, category, difficulty, description, points, flag, downloads) VALUES
('SQL Injection Basic', 'web', 'easy', 'Temukan flag dengan melakukan SQL injection pada form login. Flag format: CTF{...}', 100, 'CTF{sql_injection_success}', NULL),
('XSS Hunter', 'web', 'medium', 'Temukan celah XSS pada halaman comment dan submit proof.', 200, 'CTF{xss_master_2024}', NULL),
('Buffer Overflow', 'reverse', 'hard', 'Analisa binary file dan temukan flag dengan teknik buffer overflow.', 300, 'CTF{b0f_3xp3rt}', './challenges/binary'),
('PCAP Analysis', 'forensics', 'easy', 'Analisa file pcap dan temukan flag yang tersembunyi.', 100, 'CTF{p0rc8p157_m4573r}', NULL),
('RSA Decrypt', 'crypto', 'medium', 'Dekripsi pesan terenkripsi RSA. Diberikan n, e, dan c.', 200, 'CTF{rsa_br34k3r}', NULL),
('Network Sniffing', 'network', 'easy', 'Sniff jaringan dan temukan password yang dikirim dalam plain text.', 100, 'CTF{sn1ff3r_d0g}', NULL),
('Linux Privilege Escalation', 'linux', 'medium', 'Escalate privilege dan baca file flag di /root/flag.txt', 200, 'CTF{l1nx_r00t}', NULL),
('Advanced Web Exploit', 'web', 'hard', 'Combine multiple vulnerabilities to get the flag.', 300, 'CTF{w3b_h4ck3r_pr0}', NULL);

-- Insert initial solve counts
INSERT INTO challenge_solves (challenge_id, solve_count) VALUES
(1, 0), (2, 0), (3, 0), (4, 0), (5, 0), (6, 0), (7, 0), (8, 0);

-- Create View for Challenge Statistics
CREATE VIEW challenge_stats AS
SELECT
    c.id,
    c.title,
    c.category,
    c.difficulty,
    c.points,
    c.description,
    c.downloads,
    c.hints,
    COALESCE(cs.solve_count, 0) as solve_count,
    (SELECT COUNT(*) FROM users WHERE role = 'peserta') as total_users,
    ROUND(COALESCE(cs.solve_count, 0) * 100.0 / NULLIF((SELECT COUNT(*) FROM users WHERE role = 'peserta'), 0), 1) as solve_percentage
FROM challenges c
LEFT JOIN challenge_solves cs ON c.id = cs.challenge_id
ORDER BY c.points ASC;

CREATE DATABASE IF NOT EXISTS sportshub;
USE sportshub;

CREATE TABLE IF NOT EXISTS sports_clubs (
    club_id INT AUTO_INCREMENT PRIMARY KEY,
    club_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NULL,
    match_name VARCHAR(120) NOT NULL,
    opponent VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    venue VARCHAR(120) NOT NULL,
    FOREIGN KEY (club_id) REFERENCES sports_clubs(club_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS match_results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL UNIQUE,
    club_score INT NOT NULL DEFAULT 0,
    opponent_score INT NOT NULL DEFAULT 0,
    winner VARCHAR(100) NOT NULL,
    remarks VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(match_id)
        ON DELETE CASCADE
);

INSERT INTO sports_clubs (club_name)
SELECT 'Football Club' WHERE NOT EXISTS (SELECT 1 FROM sports_clubs WHERE club_name='Football Club');
INSERT INTO sports_clubs (club_name)
SELECT 'Basketball Club' WHERE NOT EXISTS (SELECT 1 FROM sports_clubs WHERE club_name='Basketball Club');

INSERT INTO matches (club_id, match_name, opponent, match_date, venue)
SELECT c.club_id, 'Friendly Match', 'UOC FC', '2026-10-05', 'Main Ground'
FROM sports_clubs c
WHERE c.club_name='Football Club'
AND NOT EXISTS (SELECT 1 FROM matches WHERE match_name='Friendly Match' AND opponent='UOC FC');

INSERT INTO matches (club_id, match_name, opponent, match_date, venue)
SELECT c.club_id, 'Inter-University Match', 'SLIIT', '2026-10-08', 'Indoor Court'
FROM sports_clubs c
WHERE c.club_name='Basketball Club'
AND NOT EXISTS (SELECT 1 FROM matches WHERE match_name='Inter-University Match' AND opponent='SLIIT');

INSERT INTO match_results (match_id, club_score, opponent_score, winner, remarks)
SELECT m.match_id, 3, 1, 'NSBM', 'Good performance'
FROM matches m
WHERE m.match_name='Friendly Match' AND m.opponent='UOC FC'
AND NOT EXISTS (SELECT 1 FROM match_results r WHERE r.match_id=m.match_id);

-- EventHub - Simple Event Planner
-- Import with: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS event_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_hub;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'other',
    event_date DATE NOT NULL,
    event_time TIME NOT NULL DEFAULT '18:00:00',
    location VARCHAR(255) NOT NULL DEFAULT '',
    description TEXT,
    guests INT NOT NULL DEFAULT 0,
    attending TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample events
INSERT INTO events (title, category, event_date, event_time, location, description, guests, attending) VALUES
('Birthday Bash for Ayesha', 'birthday', '2026-11-20', '19:00:00', 'Home, Peshawar', 'Cake, music and dinner with close friends and family.', 25, 1),
('Web Development Workshop', 'study', '2026-11-22', '10:00:00', 'City University, Peshawar', 'Hands-on session on PHP and MySQL for beginners.', 40, 1),
('Cricket Practice Match', 'sports', '2026-11-18', '15:30:00', 'University Ground', 'Practice match before University Sports Week 2026.', 22, 1),
('Team Dinner', 'party', '2026-11-25', '20:00:00', 'Sheraz Restaurant', 'Celebration dinner with the cricket team.', 15, 0),
('Project Demo Meeting', 'meeting', '2026-11-17', '11:00:00', 'Computer Lab 2', 'Demo of the new semester project to the class.', 8, 1);

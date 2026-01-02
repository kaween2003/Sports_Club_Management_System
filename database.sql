-- Sports Club Management System (SCMS) - Version 1.0
-- Database structure created for N.G.Kaween Newmal

CREATE DATABASE IF NOT EXISTS scms_db;
USE scms_db;

-- Users table (Admins + Members)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin','member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Event registrations
CREATE TABLE event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id),
    CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Equipment table
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    total_quantity INT DEFAULT 0,
    available_quantity INT DEFAULT 0
);

-- Equipment reservations
CREATE TABLE equipment_reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    user_id INT NOT NULL,
    event_id INT NULL,
    quantity INT NOT NULL,
    reserved_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_eq FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    CONSTRAINT fk_res_user FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Messages between users
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id) REFERENCES users(id),
    CONSTRAINT fk_msg_receiver FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Default admin account (password = admin123)
INSERT INTO users (username, password, email, role)
VALUES ('admin', SHA2('admin123', 256), 'admin@scms.local', 'admin');









-- Extra sample users (will NOT insert again if they already exist)
INSERT IGNORE INTO users (username, password, email, role) VALUES
('member1', SHA2('member123', 256), 'member1@scms.local', 'member'),
('member2', SHA2('member123', 256), 'member2@scms.local', 'member');


-- Sample equipment records
INSERT INTO equipment (name, description, total_quantity, available_quantity) VALUES
('Football', 'Standard size 5 training football', 20, 20),
('Cricket Bat', 'Adult size wooden cricket bat', 10, 8),
('Badminton Racket', 'Lightweight racket for indoor courts', 30, 25),
('Tennis Ball Set', 'Pack of 4 tennis balls', 15, 12),
('Goalkeeper Gloves', 'Pair of medium size gloves', 8, 6);


-- Sample events (created_by kept NULL to avoid foreign key problems)
INSERT INTO events (name, event_date, event_time, location, description, capacity, created_by) VALUES
('Football Practice Session', '2025-01-10', '16:00:00', 'Main Ground',
 'Weekly football practice for all club members.', 30, NULL),

('Badminton Friendly Matches', '2025-01-12', '10:00:00', 'Indoor Court 1',
 'Friendly badminton matches to improve skills.', 24, NULL),

('Cricket Net Training', '2025-01-15', '15:30:00', 'Practice Nets',
 'Bowling and batting practice in the nets.', 18, NULL),

('Fitness & Warm-up Session', '2025-01-18', '08:30:00', 'Club Gym',
 'General fitness and stretching session.', 25, NULL);


-- Register member1 for one event (adjust IDs if needed)
INSERT INTO event_registrations (event_id, user_id)
SELECT e.id, u.id
FROM events e, users u
WHERE e.name = 'Football Practice Session'
  AND u.username = 'member1'
LIMIT 1;

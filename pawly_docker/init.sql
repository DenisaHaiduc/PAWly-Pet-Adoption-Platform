-- Creare tabele pentru PAWly
CREATE TABLE IF NOT EXISTS utilizatori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    parola VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'utilizator'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS anunturi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizator_id INT NOT NULL,
    titlu VARCHAR(255) NOT NULL,
    specie VARCHAR(100),
    descriere TEXT,
    pret DECIMAL(10,2) DEFAULT 0,
    imagine VARCHAR(255),
    likes INT DEFAULT 0,
    FOREIGN KEY (utilizator_id) REFERENCES utilizatori(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS likes_utilizatori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizator_id INT NOT NULL,
    anunt_id INT NOT NULL,
    UNIQUE KEY unic_like (utilizator_id, anunt_id),
    FOREIGN KEY (utilizator_id) REFERENCES utilizatori(id) ON DELETE CASCADE,
    FOREIGN KEY (anunt_id) REFERENCES anunturi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert admin user (password: admin)
INSERT IGNORE INTO utilizatori (id, username, email, parola, rol)
VALUES (1, 'admin', 'admin@pawly.ro', '$2y$10$YourHashHere', 'admin');

-- Tabel pentru tracking-ul like-urilor per utilizator (toggle like/unlike)
CREATE TABLE IF NOT EXISTS likes_utilizatori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizator_id INT NOT NULL,
    anunt_id INT NOT NULL,
    UNIQUE KEY unic_like (utilizator_id, anunt_id),
    FOREIGN KEY (utilizator_id) REFERENCES utilizatori(id) ON DELETE CASCADE,
    FOREIGN KEY (anunt_id) REFERENCES anunturi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

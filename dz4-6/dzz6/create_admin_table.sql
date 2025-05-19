CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

-- Пример добавления администратора: логин 'admin', пароль 'admin123'
INSERT INTO admins (login, password_hash)
VALUES ('admin', '$2y$10$zNg4u1xvIbs6Z0Jp6OwHhu4G30EMRTejJPQOvdAF4rAm3zBrWIg9m');

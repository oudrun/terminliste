CREATE TABLE IF NOT EXISTS dog_trials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trial_number VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    organizer VARCHAR(150) NOT NULL,
    location VARCHAR(150) NOT NULL,
    trial_place VARCHAR(150) NOT NULL DEFAULT '',
    date_start DATE NOT NULL,
    date_end DATE NOT NULL,
    registration_deadline DATE NOT NULL,
    description TEXT NOT NULL,
    contact_person VARCHAR(150) NOT NULL DEFAULT '',
    contact_phone VARCHAR(40) NOT NULL DEFAULT '',
    contact_email VARCHAR(160) NOT NULL DEFAULT '',
    responsible_club VARCHAR(150) NOT NULL DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS trial_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trial_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_time TIME DEFAULT NULL,
    judge VARCHAR(150) DEFAULT '',
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    max_participants INT NOT NULL DEFAULT 10,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trial_classes_trial FOREIGN KEY (trial_id) REFERENCES dog_trials(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    owner_name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) DEFAULT '',
    dog_name VARCHAR(120) NOT NULL,
    dog_regno VARCHAR(80) NOT NULL DEFAULT '',
    dog_breed VARCHAR(120) DEFAULT '',
    dog_class VARCHAR(80) DEFAULT '',
    comment TEXT DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_registrations_class FOREIGN KEY (class_id) REFERENCES trial_classes(id) ON DELETE CASCADE
);

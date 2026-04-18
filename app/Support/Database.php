<?php
declare(strict_types=1);

namespace App\Support;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function getPdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require APP_BASE_PATH . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::ensureSchema(self::$pdo);

        return self::$pdo;
    }

    private static function ensureSchema(PDO $pdo): void
    {
        $sql = <<<'SQL'
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
SQL;

        $pdo->exec($sql);

        $schemaUpdates = [
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS trial_number VARCHAR(50) NULL UNIQUE AFTER id",
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS trial_place VARCHAR(150) NOT NULL DEFAULT '' AFTER location",
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS contact_person VARCHAR(150) NOT NULL DEFAULT '' AFTER description",
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(40) NOT NULL DEFAULT '' AFTER contact_person",
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS contact_email VARCHAR(160) NOT NULL DEFAULT '' AFTER contact_phone",
            "ALTER TABLE dog_trials ADD COLUMN IF NOT EXISTS responsible_club VARCHAR(150) NOT NULL DEFAULT '' AFTER contact_email",
            "ALTER TABLE registrations ADD COLUMN IF NOT EXISTS dog_regno VARCHAR(80) NOT NULL DEFAULT '' AFTER dog_name",
        ];

        foreach ($schemaUpdates as $statement) {
            try {
                $pdo->exec($statement);
            } catch (PDOException) {
            }
        }

        $pdo->exec("UPDATE dog_trials SET trial_number = CONCAT('TRIAL-', id) WHERE trial_number IS NULL OR trial_number = ''");

        $count = (int) $pdo->query('SELECT COUNT(*) FROM dog_trials')->fetchColumn();

        if ($count === 0) {
            self::seedSampleData($pdo);
        }
    }

    private static function seedSampleData(PDO $pdo): void
    {
        $pdo->beginTransaction();

        $trialStmt = $pdo->prepare(
            'INSERT INTO dog_trials (
                trial_number, title, organizer, location, trial_place, date_start, date_end,
                registration_deadline, description, contact_person, contact_phone, contact_email, responsible_club
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $trialStmt->execute([
            'VF-2026-001',
            'Vårprøve for stående fuglehunder',
            'Norsk Fuglehundklubb avd. Øst',
            'Trysil',
            'Søre Osen prøveområde',
            '2026-05-24',
            '2026-05-25',
            '2026-05-10',
            'Helgesamling med UK, AK og VK. Prøven inneholder informasjon om sted, dato, dommere og ledige plasser slik at deltakere kan vurdere påmelding.',
            'Anne Johansen',
            '90123456',
            'post@fuglehund-ost.no',
            'Norsk Fuglehundklubb avd. Øst',
        ]);

        $trialId = (int) $pdo->lastInsertId();

        $classStmt = $pdo->prepare(
            'INSERT INTO trial_classes (trial_id, name, start_time, judge, price, max_participants, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $classStmt->execute([$trialId, 'UK', '08:00:00', 'Anne Johansen', 550, 18, 'Unghundklasse med fokus på basic apport og søk.']);
        $classStmt->execute([$trialId, 'AK', '09:00:00', 'Per Nilsen', 650, 16, 'Åpen klasse for hunder med tidligere erfaring.']);
        $classStmt->execute([$trialId, 'VK', '10:00:00', 'Kari Berg', 750, 12, 'Vinnerklasse for ekvipasjer med dokumenterte resultater.']);

        $pdo->commit();
    }
}

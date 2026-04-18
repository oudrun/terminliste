<?php
declare(strict_types=1);

return [
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('MYSQL_DATABASE') ?: getenv('MARIADB_DATABASE') ?: 'db',
    'username' => getenv('MYSQL_USER') ?: getenv('MARIADB_USER') ?: 'db',
    'password' => getenv('MYSQL_PASSWORD') ?: getenv('MARIADB_PASSWORD') ?: 'db',
];

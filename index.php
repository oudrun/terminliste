<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pdo = getPdo();
$flash = getFlash();
$trials = $pdo->query('SELECT * FROM dog_trials ORDER BY date_start ASC')->fetchAll();
$classStmt = $pdo->prepare(
    "SELECT c.*, COUNT(r.id) AS registration_count
     FROM trial_classes c
     LEFT JOIN registrations r ON r.class_id = c.id AND r.status <> 'cancelled'
     WHERE c.trial_id = ?
     GROUP BY c.id
     ORDER BY c.start_time ASC"
);
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminliste for hundeprøver</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Terminliste for hundeprøver</h1>
        <p>Oversikt over prøver, klasser og påmelding.</p>
        <nav>
            <a href="index.php">Terminliste</a>
            <a href="admin.php">Administrasjon</a>
        </nav>
    </div>
</header>

<main class="container">
    <?php if ($flash): ?>
        <div class="notice <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <?php if (!$trials): ?>
        <div class="card">
            <h2>Ingen prøver registrert</h2>
            <p>Gå til administrasjon for å legge inn første prøve.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($trials as $trial): ?>
        <?php $classStmt->execute([$trial['id']]); ?>
        <?php $classes = $classStmt->fetchAll(); ?>
        <section class="card">
            <div class="actions" style="justify-content: space-between; align-items: start;">
                <div>
                    <h2><?= h($trial['title']) ?></h2>
                    <p><?= nl2br(h($trial['description'])) ?></p>
                </div>
                <span class="badge">Påmeldingsfrist <?= h(formatDate($trial['registration_deadline'])) ?></span>
            </div>

            <div class="meta">
                <span>Prøvenummer: <?= h($trial['trial_number'] ?? '') ?></span>
                <span>Arrangør: <?= h($trial['organizer']) ?></span>
                <span>Ansvarlig klubb: <?= h($trial['responsible_club'] ?? '') ?></span>
                <span>Sted: <?= h($trial['location']) ?></span>
                <span>Prøveplass: <?= h($trial['trial_place'] ?? '') ?></span>
                <span>Kontakt: <?= h($trial['contact_person'] ?? '') ?><?= !empty($trial['contact_phone']) ? ' / ' . h($trial['contact_phone']) : '' ?><?= !empty($trial['contact_email']) ? ' / ' . h($trial['contact_email']) : '' ?></span>
                <span>Dato: <?= h(formatDate($trial['date_start'])) ?> - <?= h(formatDate($trial['date_end'])) ?></span>
            </div>

            <h3>Klasser</h3>
            <?php if (!$classes): ?>
                <p>Ingen klasser er registrert ennå.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Klasse</th>
                        <th>Start</th>
                        <th>Dommer</th>
                        <th>Pris</th>
                        <th>Ledige plasser</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($classes as $class): ?>
                        <?php $available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']); ?>
                        <tr>
                            <td>
                                <strong><?= h($class['name']) ?></strong><br>
                                <small class="muted"><?= h($class['notes'] ?? '') ?></small>
                            </td>
                            <td><?= h(formatTime($class['start_time'])) ?></td>
                            <td><?= h($class['judge']) ?></td>
                            <td><?= h(number_format((float) $class['price'], 0, ',', ' ')) ?> kr</td>
                            <td><?= $available ?> / <?= (int) $class['max_participants'] ?></td>
                            <td>
                                <?php if ($available > 0): ?>
                                    <a class="button primary" href="apply.php?class_id=<?= (int) $class['id'] ?>">Meld på</a>
                                <?php else: ?>
                                    <span class="badge">Fullt</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>

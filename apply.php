<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$pdo = getPdo();
$classId = (int) ($_GET['class_id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT c.*, t.title, t.organizer, t.location, t.trial_place, t.trial_number, t.contact_person,
            t.contact_phone, t.contact_email, t.responsible_club, t.date_start, t.date_end, t.registration_deadline,
            COUNT(r.id) AS registration_count
     FROM trial_classes c
     INNER JOIN dog_trials t ON t.id = c.trial_id
     LEFT JOIN registrations r ON r.class_id = c.id AND r.status <> 'cancelled'
     WHERE c.id = ?
     GROUP BY c.id, t.title, t.organizer, t.location, t.trial_place, t.trial_number, t.contact_person,
              t.contact_phone, t.contact_email, t.responsible_club, t.date_start, t.date_end, t.registration_deadline"
);
$stmt->execute([$classId]);
$class = $stmt->fetch();

if (!$class) {
    flash('Fant ikke valgt klasse.', 'error');
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerName = trim($_POST['owner_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dogName = trim($_POST['dog_name'] ?? '');
    $dogRegno = trim($_POST['dog_regno'] ?? '');
    $dogBreed = trim($_POST['dog_breed'] ?? '');
    $dogClass = trim($_POST['dog_class'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($ownerName === '') {
        $errors[] = 'Fører/navn må fylles ut.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Gyldig e-post må fylles ut.';
    }
    if ($dogName === '') {
        $errors[] = 'Hundens navn må fylles ut.';
    }

    $available = (int) $class['max_participants'] - (int) $class['registration_count'];
    if ($available <= 0) {
        $errors[] = 'Denne klassen er fulltegnet.';
    }

    if (!$errors) {
        $insert = $pdo->prepare(
            'INSERT INTO registrations (class_id, owner_name, email, phone, dog_name, dog_regno, dog_breed, dog_class, comment)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $insert->execute([$classId, $ownerName, $email, $phone, $dogName, $dogRegno, $dogBreed, $dogClass, $comment]);

        flash('Påmeldingen er registrert og venter på bekreftelse.');
        redirect('index.php');
    }
}

$available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']);
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Påmelding</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Meld på hundeprøve</h1>
        <p><?= h($class['title']) ?> - <?= h($class['name']) ?></p>
        <nav>
            <a href="index.php">Tilbake til terminliste</a>
            <a href="admin.php">Administrasjon</a>
        </nav>
    </div>
</header>

<main class="container grid">
    <section class="card">
        <h2>Informasjon om prøven</h2>
        <div class="meta">
            <span>Prøvenummer: <?= h($class['trial_number']) ?></span>
            <span>Sted: <?= h($class['location']) ?></span>
            <span>Prøveplass: <?= h($class['trial_place']) ?></span>
            <span>Dato: <?= h(formatDate($class['date_start'])) ?> - <?= h(formatDate($class['date_end'])) ?></span>
            <span>Dommer: <?= h($class['judge']) ?></span>
        </div>
        <p><strong>Pris:</strong> <?= h(number_format((float) $class['price'], 0, ',', ' ')) ?> kr</p>
        <p><strong>Ansvarlig klubb:</strong> <?= h($class['responsible_club']) ?></p>
        <p><strong>Kontakt:</strong> <?= h($class['contact_person']) ?> / <?= h($class['contact_phone']) ?> / <?= h($class['contact_email']) ?></p>
        <p><strong>Ledige plasser:</strong> <?= $available ?> / <?= (int) $class['max_participants'] ?></p>
        <p><strong>Påmeldingsfrist:</strong> <?= h(formatDate($class['registration_deadline'])) ?></p>
        <p><?= nl2br(h($class['notes'] ?? '')) ?></p>
    </section>

    <section class="card">
        <h2>Registrer påmelding</h2>
        <?php if ($errors): ?>
            <div class="notice error">
                <?php foreach ($errors as $error): ?>
                    <div><?= h($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div>
                <label for="owner_name">Fører / eier</label>
                <input id="owner_name" name="owner_name" value="<?= h($_POST['owner_name'] ?? '') ?>" required>
            </div>
            <div>
                <label for="email">E-post</label>
                <input id="email" type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>
            </div>
            <div>
                <label for="phone">Telefon</label>
                <input id="phone" name="phone" value="<?= h($_POST['phone'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_name">Hundens navn</label>
                <input id="dog_name" name="dog_name" value="<?= h($_POST['dog_name'] ?? '') ?>" required>
            </div>
            <div>
                <label for="dog_regno">Registreringsnummer</label>
                <input id="dog_regno" name="dog_regno" value="<?= h($_POST['dog_regno'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_breed">Rase</label>
                <input id="dog_breed" name="dog_breed" value="<?= h($_POST['dog_breed'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_class">Registrert konkurranseklasse</label>
                <input id="dog_class" name="dog_class" value="<?= h($_POST['dog_class'] ?? '') ?>" placeholder="F.eks. UK / AK / VK">
            </div>
            <div>
                <label for="comment">Kommentar</label>
                <textarea id="comment" name="comment"><?= h($_POST['comment'] ?? '') ?></textarea>
            </div>
            <button class="button primary" type="submit">Send påmelding</button>
        </form>
    </section>
</main>
</body>
</html>

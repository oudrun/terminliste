<?php
$pageTitle = 'Påmelding';
require APP_BASE_PATH . '/resources/views/partials/header.php';
?>

<div class="grid">
    <section class="card">
        <h2>Meld på hundeprøve</h2>
        <div class="meta">
            <span>Prøvenummer: <?= h($class['trial_number']) ?></span>
            <span>Sted: <?= h($class['location']) ?></span>
            <span>Prøveplass: <?= h($class['trial_place']) ?></span>
            <span>Dato: <?= h(formatDate($class['date_start'])) ?> - <?= h(formatDate($class['date_end'])) ?></span>
            <span>Dommer: <?= h($class['judge']) ?></span>
        </div>
        <p><strong>Ansvarlig klubb:</strong> <?= h($class['responsible_club']) ?></p>
        <p><strong>Kontakt:</strong> <?= h($class['contact_person']) ?> / <?= h($class['contact_phone']) ?> / <?= h($class['contact_email']) ?></p>
        <p><strong>Pris:</strong> <?= h(number_format((float) $class['price'], 0, ',', ' ')) ?> kr</p>
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
                <input id="owner_name" name="owner_name" value="<?= h($formData['owner_name'] ?? '') ?>" required>
            </div>
            <div>
                <label for="email">E-post</label>
                <input id="email" type="email" name="email" value="<?= h($formData['email'] ?? '') ?>" required>
            </div>
            <div>
                <label for="phone">Telefon</label>
                <input id="phone" name="phone" value="<?= h($formData['phone'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_name">Hundens navn</label>
                <input id="dog_name" name="dog_name" value="<?= h($formData['dog_name'] ?? '') ?>" required>
            </div>
            <div>
                <label for="dog_regno">Registreringsnummer</label>
                <input id="dog_regno" name="dog_regno" value="<?= h($formData['dog_regno'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_breed">Rase</label>
                <input id="dog_breed" name="dog_breed" value="<?= h($formData['dog_breed'] ?? '') ?>">
            </div>
            <div>
                <label for="dog_class">Registrert konkurranseklasse</label>
                <input id="dog_class" name="dog_class" value="<?= h($formData['dog_class'] ?? '') ?>">
            </div>
            <div>
                <label for="comment">Kommentar</label>
                <textarea id="comment" name="comment"><?= h($formData['comment'] ?? '') ?></textarea>
            </div>
            <button class="button primary" type="submit">Send påmelding</button>
        </form>
    </section>
</div>

<?php require APP_BASE_PATH . '/resources/views/partials/footer.php'; ?>

<?php
/**
 * Plugin Name: Terminliste for hundeprøver
 * Description: WordPress-plugin for å vise, vedlikeholde og ta imot påmeldinger til hundeprøver.
 * Version: 1.0.0
 * Author: GitHub Copilot
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class TerminlisteHundeproverPlugin
{
    private static ?self $instance = null;

    private wpdb $db;

    private string $trialsTable;

    private string $classesTable;

    private string $registrationsTable;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->trialsTable = $wpdb->prefix . 'terminliste_trials';
        $this->classesTable = $wpdb->prefix . 'terminliste_classes';
        $this->registrationsTable = $wpdb->prefix . 'terminliste_registrations';

        add_action('admin_menu', [$this, 'registerAdminMenu']);
        add_action('admin_post_terminliste_save_trial', [$this, 'handleSaveTrial']);
        add_action('admin_post_terminliste_save_class', [$this, 'handleSaveClass']);
        add_action('admin_post_terminliste_delete_trial', [$this, 'handleDeleteTrial']);
        add_action('admin_post_terminliste_delete_class', [$this, 'handleDeleteClass']);
        add_action('admin_post_terminliste_update_registration', [$this, 'handleUpdateRegistration']);
        add_shortcode('terminliste_hundeprover', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    public static function activate(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = $wpdb->get_charset_collate();
        $trialsTable = $wpdb->prefix . 'terminliste_trials';
        $classesTable = $wpdb->prefix . 'terminliste_classes';
        $registrationsTable = $wpdb->prefix . 'terminliste_registrations';

        $sqlTrials = "CREATE TABLE {$trialsTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            trial_number VARCHAR(50) NOT NULL,
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
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY trial_number (trial_number)
        ) {$charsetCollate};";

        $sqlClasses = "CREATE TABLE {$classesTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            trial_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            start_time TIME NULL,
            judge VARCHAR(150) NOT NULL DEFAULT '',
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            max_participants INT NOT NULL DEFAULT 10,
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY trial_id (trial_id)
        ) {$charsetCollate};";

        $sqlRegistrations = "CREATE TABLE {$registrationsTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            class_id BIGINT UNSIGNED NOT NULL,
            owner_name VARCHAR(120) NOT NULL,
            email VARCHAR(160) NOT NULL,
            phone VARCHAR(40) NOT NULL DEFAULT '',
            dog_name VARCHAR(120) NOT NULL,
            dog_regno VARCHAR(80) NOT NULL DEFAULT '',
            dog_breed VARCHAR(120) NOT NULL DEFAULT '',
            dog_class VARCHAR(80) NOT NULL DEFAULT '',
            comment TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY class_id (class_id)
        ) {$charsetCollate};";

        dbDelta($sqlTrials);
        dbDelta($sqlClasses);
        dbDelta($sqlRegistrations);

        $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$trialsTable}");

        if ($count === 0) {
            $wpdb->insert(
                $trialsTable,
                [
                    'trial_number' => 'VF-2026-001',
                    'title' => 'Vårprøve for stående fuglehunder',
                    'organizer' => 'Norsk Fuglehundklubb avd. Øst',
                    'location' => 'Trysil',
                    'trial_place' => 'Søre Osen prøveområde',
                    'date_start' => '2026-05-24',
                    'date_end' => '2026-05-25',
                    'registration_deadline' => '2026-05-10',
                    'description' => 'Eksempelprøve med UK, AK og VK.',
                    'contact_person' => 'Anne Johansen',
                    'contact_phone' => '90123456',
                    'contact_email' => 'post@example.no',
                    'responsible_club' => 'Norsk Fuglehundklubb avd. Øst',
                ]
            );

            $trialId = (int) $wpdb->insert_id;

            $wpdb->insert(
                $classesTable,
                [
                    'trial_id' => $trialId,
                    'name' => 'UK',
                    'start_time' => '08:00:00',
                    'judge' => 'Anne Johansen',
                    'price' => 550,
                    'max_participants' => 18,
                    'notes' => 'Unghundklasse.',
                ]
            );
        }
    }

    public function enqueueStyles(): void
    {
        wp_register_style(
            'terminliste-hundeprover',
            plugin_dir_url(__FILE__) . 'assets/terminliste.css',
            [],
            '1.0.0'
        );

        wp_enqueue_style('terminliste-hundeprover');
    }

    public function registerAdminMenu(): void
    {
        add_menu_page(
            'Terminliste',
            'Terminliste',
            'manage_options',
            'terminliste-hundeprover',
            [$this, 'renderAdminPage'],
            'dashicons-calendar-alt',
            26
        );
    }

    public function renderShortcode(): string
    {
        wp_enqueue_style('terminliste-hundeprover');

        $output = '<div class="thp-app">';
        $output .= $this->handleFrontendRegistration();

        $classId = isset($_GET['terminliste_apply']) ? (int) $_GET['terminliste_apply'] : 0;

        if ($classId > 0) {
            $output .= $this->renderApplyForm($classId);
        } else {
            $output .= $this->renderTrialsOverview();
        }

        $output .= '</div>';

        return $output;
    }

    private function handleFrontendRegistration(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($this->postValue('thp_action') !== 'register')) {
            return '';
        }

        if (!wp_verify_nonce($this->postValue('_wpnonce'), 'thp_register')) {
            return $this->renderNotice('Sikkerhetskontroll feilet.', 'error');
        }

        $classId = (int) $this->postValue('class_id');
        $ownerName = sanitize_text_field($this->postValue('owner_name'));
        $email = sanitize_email($this->postValue('email'));
        $phone = sanitize_text_field($this->postValue('phone'));
        $dogName = sanitize_text_field($this->postValue('dog_name'));
        $dogRegno = sanitize_text_field($this->postValue('dog_regno'));
        $dogBreed = sanitize_text_field($this->postValue('dog_breed'));
        $dogClass = sanitize_text_field($this->postValue('dog_class'));
        $comment = sanitize_textarea_field($this->postValue('comment'));

        if ($ownerName === '' || $dogName === '' || $email === '') {
            return $this->renderNotice('Navn, e-post og hundens navn må fylles ut.', 'error');
        }

        $class = $this->getClassWithTrial($classId);

        if (!$class) {
            return $this->renderNotice('Fant ikke valgt klasse.', 'error');
        }

        $available = (int) $class['max_participants'] - (int) $class['registration_count'];

        if ($available <= 0) {
            return $this->renderNotice('Denne klassen er fulltegnet.', 'error');
        }

        $inserted = $this->db->insert(
            $this->registrationsTable,
            [
                'class_id' => $classId,
                'owner_name' => $ownerName,
                'email' => $email,
                'phone' => $phone,
                'dog_name' => $dogName,
                'dog_regno' => $dogRegno,
                'dog_breed' => $dogBreed,
                'dog_class' => $dogClass,
                'comment' => $comment,
                'status' => 'pending',
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($inserted === false) {
            return $this->renderNotice('Kunne ikke lagre påmeldingen.', 'error');
        }

        return $this->renderNotice('Påmeldingen er registrert.', 'success');
    }

    private function renderTrialsOverview(): string
    {
        $trials = $this->db->get_results("SELECT * FROM {$this->trialsTable} ORDER BY date_start ASC", ARRAY_A);

        if (!$trials) {
            return '<div class="thp-card"><h2>Ingen prøver registrert</h2><p>Legg inn første prøve i WordPress-admin.</p></div>';
        }

        $output = '';

        foreach ($trials as $trial) {
            $classes = $this->getClassesByTrial((int) $trial['id']);
            $output .= '<section class="thp-card">';
            $output .= '<h2>' . esc_html($trial['title']) . '</h2>';
            $output .= '<p>' . nl2br(esc_html($trial['description'])) . '</p>';
            $output .= '<div class="thp-meta">';
            $output .= '<span>Prøvenummer: ' . esc_html($trial['trial_number']) . '</span>';
            $output .= '<span>Arrangør: ' . esc_html($trial['organizer']) . '</span>';
            $output .= '<span>Ansvarlig klubb: ' . esc_html($trial['responsible_club']) . '</span>';
            $output .= '<span>Sted: ' . esc_html($trial['location']) . '</span>';
            $output .= '<span>Prøveplass: ' . esc_html($trial['trial_place']) . '</span>';
            $output .= '<span>Kontakt: ' . esc_html($trial['contact_person']) . ' / ' . esc_html($trial['contact_phone']) . ' / ' . esc_html($trial['contact_email']) . '</span>';
            $output .= '</div>';
            $output .= '<table class="thp-table"><thead><tr><th>Klasse</th><th>Start</th><th>Dommer</th><th>Pris</th><th>Ledige plasser</th><th></th></tr></thead><tbody>';

            foreach ($classes as $class) {
                $available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']);
                $applyUrl = add_query_arg(['terminliste_apply' => (int) $class['id']]);

                $output .= '<tr>';
                $output .= '<td><strong>' . esc_html($class['name']) . '</strong><br><small>' . esc_html((string) $class['notes']) . '</small></td>';
                $output .= '<td>' . esc_html(substr((string) $class['start_time'], 0, 5)) . '</td>';
                $output .= '<td>' . esc_html($class['judge']) . '</td>';
                $output .= '<td>' . esc_html(number_format((float) $class['price'], 0, ',', ' ')) . ' kr</td>';
                $output .= '<td>' . esc_html((string) $available) . ' / ' . esc_html((string) $class['max_participants']) . '</td>';
                $output .= '<td>';

                if ($available > 0) {
                    $output .= '<a class="thp-button thp-primary" href="' . esc_url($applyUrl) . '">Meld på</a>';
                } else {
                    $output .= '<span class="thp-badge">Fullt</span>';
                }

                $output .= '</td></tr>';
            }

            $output .= '</tbody></table></section>';
        }

        return $output;
    }

    private function renderApplyForm(int $classId): string
    {
        $class = $this->getClassWithTrial($classId);

        if (!$class) {
            return $this->renderNotice('Fant ikke valgt klasse.', 'error');
        }

        $available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']);
        $backUrl = remove_query_arg('terminliste_apply');

        ob_start();
        ?>
        <div class="thp-card">
            <p><a class="thp-button" href="<?php echo esc_url($backUrl); ?>">Tilbake</a></p>
            <h2><?php echo esc_html($class['title']); ?> / <?php echo esc_html($class['name']); ?></h2>
            <div class="thp-meta">
                <span>Prøvenummer: <?php echo esc_html($class['trial_number']); ?></span>
                <span>Sted: <?php echo esc_html($class['location']); ?></span>
                <span>Prøveplass: <?php echo esc_html($class['trial_place']); ?></span>
                <span>Kontakt: <?php echo esc_html($class['contact_person']); ?> / <?php echo esc_html($class['contact_phone']); ?></span>
            </div>
            <p><strong>Påmeldingsfrist:</strong> <?php echo esc_html($class['registration_deadline']); ?></p>
            <p><strong>Ledige plasser:</strong> <?php echo esc_html((string) $available); ?></p>
            <form method="post" class="thp-form">
                <input type="hidden" name="thp_action" value="register">
                <input type="hidden" name="class_id" value="<?php echo esc_attr((string) $classId); ?>">
                <?php wp_nonce_field('thp_register'); ?>
                <label>Fører / eier
                    <input type="text" name="owner_name" required>
                </label>
                <label>E-post
                    <input type="email" name="email" required>
                </label>
                <label>Telefon
                    <input type="text" name="phone">
                </label>
                <label>Hundens navn
                    <input type="text" name="dog_name" required>
                </label>
                <label>Registreringsnummer
                    <input type="text" name="dog_regno">
                </label>
                <label>Rase
                    <input type="text" name="dog_breed">
                </label>
                <label>Konkurranseklasse
                    <input type="text" name="dog_class">
                </label>
                <label>Kommentar
                    <textarea name="comment"></textarea>
                </label>
                <button type="submit" class="thp-button thp-primary">Send påmelding</button>
            </form>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function renderAdminPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        wp_enqueue_style('terminliste-hundeprover');

        $trials = $this->db->get_results("SELECT * FROM {$this->trialsTable} ORDER BY date_start ASC", ARRAY_A) ?: [];
        $classes = $this->db->get_results(
            "SELECT c.*, t.title AS trial_title
            FROM {$this->classesTable} c
            INNER JOIN {$this->trialsTable} t ON t.id = c.trial_id
            ORDER BY t.date_start ASC, c.start_time ASC",
            ARRAY_A
        ) ?: [];
        $registrations = $this->db->get_results(
            "SELECT r.*, c.name AS class_name, t.title AS trial_title
            FROM {$this->registrationsTable} r
            INNER JOIN {$this->classesTable} c ON c.id = r.class_id
            INNER JOIN {$this->trialsTable} t ON t.id = c.trial_id
            ORDER BY r.created_at DESC",
            ARRAY_A
        ) ?: [];

        $message = isset($_GET['message']) ? sanitize_text_field(wp_unslash($_GET['message'])) : '';

        echo '<div class="wrap thp-admin">';
        echo '<h1>Terminliste for hundeprøver</h1>';

        if ($message !== '') {
            echo wp_kses_post($this->renderNotice('Endringen er lagret.', 'success'));
        }

        echo '<div class="thp-grid">';
        echo '<section class="thp-card">';
        echo '<h2>Ny prøve</h2>';
        echo '<form class="thp-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="terminliste_save_trial">';
        wp_nonce_field('terminliste_save_trial');
        echo '<label>Prøvenummer<input type="text" name="trial_number" required></label>';
        echo '<label>Tittel<input type="text" name="title" required></label>';
        echo '<label>Arrangør<input type="text" name="organizer" required></label>';
        echo '<label>Ansvarlig klubb<input type="text" name="responsible_club"></label>';
        echo '<label>Sted<input type="text" name="location" required></label>';
        echo '<label>Prøveplass<input type="text" name="trial_place"></label>';
        echo '<label>Startdato<input type="date" name="date_start" required></label>';
        echo '<label>Sluttdato<input type="date" name="date_end" required></label>';
        echo '<label>Påmeldingsfrist<input type="date" name="registration_deadline" required></label>';
        echo '<label>Kontaktperson<input type="text" name="contact_person"></label>';
        echo '<label>Kontakttelefon<input type="text" name="contact_phone"></label>';
        echo '<label>Kontakt e-post<input type="email" name="contact_email"></label>';
        echo '<label>Beskrivelse<textarea name="description" required></textarea></label>';
        echo '<button type="submit" class="thp-button thp-primary">Lagre prøve</button>';
        echo '</form></section>';

        echo '<section class="thp-card">';
        echo '<h2>Ny klasse</h2>';
        echo '<form class="thp-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="terminliste_save_class">';
        wp_nonce_field('terminliste_save_class');
        echo '<label>Prøve<select name="trial_id" required><option value="">Velg prøve</option>';
        foreach ($trials as $trial) {
            echo '<option value="' . esc_attr((string) $trial['id']) . '">' . esc_html($trial['title']) . '</option>';
        }
        echo '</select></label>';
        echo '<label>Klasse<input type="text" name="name" required></label>';
        echo '<label>Starttid<input type="time" name="start_time"></label>';
        echo '<label>Dommer<input type="text" name="judge"></label>';
        echo '<label>Pris<input type="number" step="0.01" name="price" value="0"></label>';
        echo '<label>Maks deltakere<input type="number" name="max_participants" value="10"></label>';
        echo '<label>Notat<textarea name="notes"></textarea></label>';
        echo '<button type="submit" class="thp-button thp-primary">Lagre klasse</button>';
        echo '</form></section>';
        echo '</div>';

        echo '<section class="thp-card"><h2>Prøver</h2><table class="thp-table"><thead><tr><th>Prøve</th><th>Nummer</th><th>Kontakt</th><th>Handling</th></tr></thead><tbody>';
        foreach ($trials as $trial) {
            echo '<tr>';
            echo '<td><strong>' . esc_html($trial['title']) . '</strong><br><small>' . esc_html($trial['location']) . '</small></td>';
            echo '<td>' . esc_html($trial['trial_number']) . '<br><small>' . esc_html($trial['responsible_club']) . '</small></td>';
            echo '<td>' . esc_html($trial['contact_person']) . '<br><small>' . esc_html($trial['contact_email']) . '</small></td>';
            echo '<td><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="terminliste_delete_trial">';
            echo '<input type="hidden" name="trial_id" value="' . esc_attr((string) $trial['id']) . '">';
            wp_nonce_field('terminliste_delete_trial_' . $trial['id']);
            echo '<button type="submit" class="thp-button thp-danger">Slett</button></form></td>';
            echo '</tr>';
        }
        echo '</tbody></table></section>';

        echo '<section class="thp-card"><h2>Klasser</h2><table class="thp-table"><thead><tr><th>Prøve</th><th>Klasse</th><th>Pris</th><th>Handling</th></tr></thead><tbody>';
        foreach ($classes as $class) {
            echo '<tr>';
            echo '<td>' . esc_html($class['trial_title']) . '</td>';
            echo '<td>' . esc_html($class['name']) . '</td>';
            echo '<td>' . esc_html(number_format((float) $class['price'], 0, ',', ' ')) . ' kr</td>';
            echo '<td><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="terminliste_delete_class">';
            echo '<input type="hidden" name="class_id" value="' . esc_attr((string) $class['id']) . '">';
            wp_nonce_field('terminliste_delete_class_' . $class['id']);
            echo '<button type="submit" class="thp-button thp-danger">Slett</button></form></td>';
            echo '</tr>';
        }
        echo '</tbody></table></section>';

        echo '<section class="thp-card"><h2>Påmeldinger</h2><table class="thp-table"><thead><tr><th>Deltaker</th><th>Hund</th><th>Prøve</th><th>Status</th></tr></thead><tbody>';
        foreach ($registrations as $registration) {
            echo '<tr>';
            echo '<td><strong>' . esc_html($registration['owner_name']) . '</strong><br><small>' . esc_html($registration['email']) . '</small></td>';
            echo '<td><strong>' . esc_html($registration['dog_name']) . '</strong><br><small>Reg.nr: ' . esc_html($registration['dog_regno']) . '</small></td>';
            echo '<td>' . esc_html($registration['trial_title']) . ' / ' . esc_html($registration['class_name']) . '</td>';
            echo '<td><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            echo '<input type="hidden" name="action" value="terminliste_update_registration">';
            echo '<input type="hidden" name="registration_id" value="' . esc_attr((string) $registration['id']) . '">';
            wp_nonce_field('terminliste_update_registration_' . $registration['id']);
            echo '<select name="status">';
            foreach (['pending' => 'Avventer', 'confirmed' => 'Bekreftet', 'cancelled' => 'Avlyst'] as $value => $label) {
                $selected = $registration['status'] === $value ? ' selected' : '';
                echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
            }
            echo '</select> <button type="submit" class="thp-button">Oppdater</button></form></td>';
            echo '</tr>';
        }
        echo '</tbody></table></section>';

        echo '<p><strong>Bruk shortcode:</strong> [terminliste_hundeprover]</p>';
        echo '</div>';
    }

    public function handleSaveTrial(): void
    {
        $this->guardAdminRequest('terminliste_save_trial');

        $this->db->insert(
            $this->trialsTable,
            [
                'trial_number' => sanitize_text_field($this->postValue('trial_number')),
                'title' => sanitize_text_field($this->postValue('title')),
                'organizer' => sanitize_text_field($this->postValue('organizer')),
                'location' => sanitize_text_field($this->postValue('location')),
                'trial_place' => sanitize_text_field($this->postValue('trial_place')),
                'date_start' => sanitize_text_field($this->postValue('date_start')),
                'date_end' => sanitize_text_field($this->postValue('date_end')),
                'registration_deadline' => sanitize_text_field($this->postValue('registration_deadline')),
                'description' => sanitize_textarea_field($this->postValue('description')),
                'contact_person' => sanitize_text_field($this->postValue('contact_person')),
                'contact_phone' => sanitize_text_field($this->postValue('contact_phone')),
                'contact_email' => sanitize_email($this->postValue('contact_email')),
                'responsible_club' => sanitize_text_field($this->postValue('responsible_club')),
            ]
        );

        $this->redirectAdmin();
    }

    public function handleSaveClass(): void
    {
        $this->guardAdminRequest('terminliste_save_class');

        $this->db->insert(
            $this->classesTable,
            [
                'trial_id' => (int) $this->postValue('trial_id'),
                'name' => sanitize_text_field($this->postValue('name')),
                'start_time' => sanitize_text_field($this->postValue('start_time')),
                'judge' => sanitize_text_field($this->postValue('judge')),
                'price' => (float) $this->postValue('price'),
                'max_participants' => (int) $this->postValue('max_participants'),
                'notes' => sanitize_textarea_field($this->postValue('notes')),
            ]
        );

        $this->redirectAdmin();
    }

    public function handleDeleteTrial(): void
    {
        $trialId = (int) $this->postValue('trial_id');
        $this->guardAdminRequest('terminliste_delete_trial_' . $trialId);

        $classIds = $this->db->get_col($this->db->prepare("SELECT id FROM {$this->classesTable} WHERE trial_id = %d", $trialId));

        if ($classIds) {
            $ids = implode(',', array_map('intval', $classIds));
            $this->db->query("DELETE FROM {$this->registrationsTable} WHERE class_id IN ({$ids})");
        }

        $this->db->delete($this->classesTable, ['trial_id' => $trialId], ['%d']);
        $this->db->delete($this->trialsTable, ['id' => $trialId], ['%d']);

        $this->redirectAdmin();
    }

    public function handleDeleteClass(): void
    {
        $classId = (int) $this->postValue('class_id');
        $this->guardAdminRequest('terminliste_delete_class_' . $classId);

        $this->db->delete($this->registrationsTable, ['class_id' => $classId], ['%d']);
        $this->db->delete($this->classesTable, ['id' => $classId], ['%d']);

        $this->redirectAdmin();
    }

    public function handleUpdateRegistration(): void
    {
        $registrationId = (int) $this->postValue('registration_id');
        $this->guardAdminRequest('terminliste_update_registration_' . $registrationId);

        $allowed = ['pending', 'confirmed', 'cancelled'];
        $status = sanitize_text_field($this->postValue('status'));

        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        $this->db->update(
            $this->registrationsTable,
            ['status' => $status],
            ['id' => $registrationId],
            ['%s'],
            ['%d']
        );

        $this->redirectAdmin();
    }

    private function getClassesByTrial(int $trialId): array
    {
        $query = $this->db->prepare(
            "SELECT c.*, COUNT(r.id) AS registration_count
            FROM {$this->classesTable} c
            LEFT JOIN {$this->registrationsTable} r ON r.class_id = c.id AND r.status <> 'cancelled'
            WHERE c.trial_id = %d
            GROUP BY c.id
            ORDER BY c.start_time ASC",
            $trialId
        );

        return $this->db->get_results($query, ARRAY_A) ?: [];
    }

    private function getClassWithTrial(int $classId): ?array
    {
        $query = $this->db->prepare(
            "SELECT c.*, t.title, t.organizer, t.location, t.trial_place, t.trial_number, t.contact_person,
                    t.contact_phone, t.contact_email, t.responsible_club, t.date_start, t.date_end,
                    t.registration_deadline, COUNT(r.id) AS registration_count
            FROM {$this->classesTable} c
            INNER JOIN {$this->trialsTable} t ON t.id = c.trial_id
            LEFT JOIN {$this->registrationsTable} r ON r.class_id = c.id AND r.status <> 'cancelled'
            WHERE c.id = %d
            GROUP BY c.id",
            $classId
        );

        $row = $this->db->get_row($query, ARRAY_A);

        return is_array($row) ? $row : null;
    }

    private function renderNotice(string $message, string $type): string
    {
        return '<div class="thp-notice thp-' . esc_attr($type) . '">' . esc_html($message) . '</div>';
    }

    private function redirectAdmin(): void
    {
        wp_safe_redirect(add_query_arg(['page' => 'terminliste-hundeprover', 'message' => 'saved'], admin_url('admin.php')));
        exit;
    }

    private function guardAdminRequest(string $nonceAction): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Mangler tilgang.');
        }

        check_admin_referer($nonceAction);
    }

    private function postValue(string $key): string
    {
        return isset($_POST[$key]) ? (string) wp_unslash($_POST[$key]) : '';
    }
}

register_activation_hook(__FILE__, ['TerminlisteHundeproverPlugin', 'activate']);
TerminlisteHundeproverPlugin::instance();

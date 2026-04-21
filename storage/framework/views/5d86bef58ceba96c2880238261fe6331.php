<?php $__env->startSection('content'); ?>
    <div class="grid">
        <section class="card">
            <h2>Ny prøve</h2>
            <form method="post" action="<?php echo e(route('admin')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="addTrial">
                <div>
                    <label for="trial_number">Prøvenummer</label>
                    <input id="trial_number" name="trial_number" required>
                </div>
                <div>
                    <label for="title">Tittel</label>
                    <input id="title" name="title" required>
                </div>
                <div>
                    <label for="organizer">Arrangør</label>
                    <input id="organizer" name="organizer" required>
                </div>
                <div>
                    <label for="responsible_club">Ansvarlig klubb</label>
                    <input id="responsible_club" name="responsible_club">
                </div>
                <div>
                    <label for="location">Sted</label>
                    <input id="location" name="location" required>
                </div>
                <div>
                    <label for="trial_place">Prøveplass</label>
                    <input id="trial_place" name="trial_place">
                </div>
                <div>
                    <label for="date_start">Startdato</label>
                    <input id="date_start" type="date" name="date_start" required>
                </div>
                <div>
                    <label for="date_end">Sluttdato</label>
                    <input id="date_end" type="date" name="date_end" required>
                </div>
                <div>
                    <label for="registration_deadline">Påmeldingsfrist</label>
                    <input id="registration_deadline" type="date" name="registration_deadline" required>
                </div>
                <div>
                    <label for="contact_person">Kontaktperson</label>
                    <input id="contact_person" name="contact_person">
                </div>
                <div>
                    <label for="contact_phone">Kontakttelefon</label>
                    <input id="contact_phone" name="contact_phone">
                </div>
                <div>
                    <label for="contact_email">Kontakt e-post</label>
                    <input id="contact_email" type="email" name="contact_email">
                </div>
                <div>
                    <label for="description">Beskrivelse</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <button class="button primary" type="submit">Lagre prøve</button>
            </form>
        </section>

        <section class="card">
            <h2>Ny klasse</h2>
            <form method="post" action="<?php echo e(route('admin')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="addClass">
                <div>
                    <label for="trial_id">Prøve</label>
                    <select id="trial_id" name="trial_id" required>
                        <option value="">Velg prøve</option>
                        <?php $__currentLoopData = $trials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e((int) $trial['id']); ?>"><?php echo e($trial['title']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="name">Klassenavn</label>
                    <input id="name" name="name" placeholder="UK, AK, VK ..." required>
                </div>
                <div>
                    <label for="start_time">Starttid</label>
                    <input id="start_time" type="time" name="start_time">
                </div>
                <div>
                    <label for="judge">Dommer</label>
                    <input id="judge" name="judge">
                </div>
                <div>
                    <label for="price">Pris</label>
                    <input id="price" type="number" step="0.01" name="price" value="0">
                </div>
                <div>
                    <label for="max_participants">Maks deltakere</label>
                    <input id="max_participants" type="number" name="max_participants" value="10">
                </div>
                <div>
                    <label for="notes">Notat</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button class="button primary" type="submit">Lagre klasse</button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Registrerte prøver</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Prøve</th>
                <th>Nummer / klubb</th>
                <th>Sted</th>
                <th>Dato</th>
                <th>Handling</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $trials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($trial['title']); ?></strong><br><small class="muted"><?php echo e($trial['organizer']); ?></small></td>
                    <td><strong><?php echo e($trial['trial_number'] ?? ''); ?></strong><br><small class="muted"><?php echo e($trial['responsible_club'] ?? ''); ?></small></td>
                    <td><?php echo e($trial['location']); ?><?php if(!empty($trial['trial_place'])): ?><br><small class="muted"><?php echo e($trial['trial_place']); ?></small><?php endif; ?></td>
                    <td><?php echo e(formatDate($trial['date_start'])); ?> - <?php echo e(formatDate($trial['date_end'])); ?></td>
                    <td>
                        <form method="post" action="<?php echo e(route('admin')); ?>" onsubmit="return confirm('Slette prøven med alle tilhørende klasser?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="deleteTrial">
                            <input type="hidden" name="trial_id" value="<?php echo e((int) $trial['id']); ?>">
                            <button class="button danger" type="submit">Slett</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Klasser</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Prøve</th>
                <th>Klasse</th>
                <th>Dommer</th>
                <th>Pris</th>
                <th>Handling</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($class['trial_title']); ?></td>
                    <td><?php echo e($class['name']); ?></td>
                    <td><?php echo e($class['judge']); ?></td>
                    <td><?php echo e(number_format((float) $class['price'], 0, ',', ' ')); ?> kr</td>
                    <td>
                        <form method="post" action="<?php echo e(route('admin')); ?>" onsubmit="return confirm('Slette denne klassen?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="deleteClass">
                            <input type="hidden" name="class_id" value="<?php echo e((int) $class['id']); ?>">
                            <button class="button danger" type="submit">Slett</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Påmeldinger</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Deltaker</th>
                <th>Hund</th>
                <th>Prøve / klasse</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <strong><?php echo e($registration['owner_name']); ?></strong><br>
                        <small class="muted"><?php echo e($registration['email']); ?> / <?php echo e($registration['phone']); ?></small>
                    </td>
                    <td>
                        <strong><?php echo e($registration['dog_name']); ?></strong><br>
                        <small class="muted">Reg.nr: <?php echo e($registration['dog_regno'] ?? ''); ?></small><br>
                        <small class="muted"><?php echo e($registration['dog_breed']); ?> <?php echo e(!empty($registration['dog_class']) ? '(' . $registration['dog_class'] . ')' : ''); ?></small>
                    </td>
                    <td><?php echo e($registration['trial_title']); ?> / <?php echo e($registration['class_name']); ?></td>
                    <td>
                        <form method="post" action="<?php echo e(route('admin')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="updateStatus">
                            <input type="hidden" name="registration_id" value="<?php echo e((int) $registration['id']); ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php if($registration['status'] === 'pending'): echo 'selected'; endif; ?>>Avventer</option>
                                <option value="confirmed" <?php if($registration['status'] === 'confirmed'): echo 'selected'; endif; ?>>Bekreftet</option>
                                <option value="cancelled" <?php if($registration['status'] === 'cancelled'): echo 'selected'; endif; ?>>Avlyst</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin.blade.php ENDPATH**/ ?>
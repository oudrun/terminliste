<?php $__env->startSection('content'); ?>
    <?php if(!$trials): ?>
        <section class="card">
            <h2>Ingen prøver registrert</h2>
            <p>Gå til administrasjon for å legge inn første prøve.</p>
        </section>
    <?php endif; ?>

    <?php $__currentLoopData = $trials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($classes = $classesByTrial[(int) $trial['id']] ?? []); ?>
        <section class="card">
            <div class="actions" style="justify-content: space-between; align-items: start;">
                <div>
                    <h2><?php echo e($trial['title']); ?></h2>
                    <p><?php echo nl2br(e($trial['description'])); ?></p>
                </div>
                <span class="badge">Påmeldingsfrist <?php echo e(formatDate($trial['registration_deadline'])); ?></span>
            </div>

            <div class="meta">
                <span>Prøvenummer: <?php echo e($trial['trial_number'] ?? ''); ?></span>
                <span>Arrangør: <?php echo e($trial['organizer']); ?></span>
                <span>Ansvarlig klubb: <?php echo e($trial['responsible_club'] ?? ''); ?></span>
                <span>Sted: <?php echo e($trial['location']); ?></span>
                <span>Prøveplass: <?php echo e($trial['trial_place'] ?? ''); ?></span>
                <span>Kontakt: <?php echo e($trial['contact_person'] ?? ''); ?><?php if(!empty($trial['contact_phone'])): ?> / <?php echo e($trial['contact_phone']); ?> <?php endif; ?> <?php if(!empty($trial['contact_email'])): ?> / <?php echo e($trial['contact_email']); ?> <?php endif; ?></span>
                <span>Dato: <?php echo e(formatDate($trial['date_start'])); ?> - <?php echo e(formatDate($trial['date_end'])); ?></span>
            </div>

            <h3>Klasser</h3>
            <?php if(!$classes): ?>
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
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($available = max(0, (int) $class['max_participants'] - (int) $class['registration_count'])); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($class['name']); ?></strong><br>
                                <small class="muted"><?php echo e($class['notes'] ?? ''); ?></small>
                            </td>
                            <td><?php echo e(formatTime($class['start_time'])); ?></td>
                            <td><?php echo e($class['judge']); ?></td>
                            <td><?php echo e(number_format((float) $class['price'], 0, ',', ' ')); ?> kr</td>
                            <td><?php echo e($available); ?> / <?php echo e((int) $class['max_participants']); ?></td>
                            <td>
                                <?php if($available > 0): ?>
                                    <a class="button primary" href="<?php echo e(route('apply', ['class_id' => (int) $class['id']])); ?>">Meld på</a>
                                <?php else: ?>
                                    <span class="badge">Fullt</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>
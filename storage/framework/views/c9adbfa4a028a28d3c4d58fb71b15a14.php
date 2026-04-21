<?php $__env->startSection('content'); ?>
    <div class="grid">
        <section class="card">
            <h2>Meld på hundeprøve</h2>
            <div class="meta">
                <span>Prøvenummer: <?php echo e($class['trial_number']); ?></span>
                <span>Sted: <?php echo e($class['location']); ?></span>
                <span>Prøveplass: <?php echo e($class['trial_place']); ?></span>
                <span>Dato: <?php echo e(formatDate($class['date_start'])); ?> - <?php echo e(formatDate($class['date_end'])); ?></span>
                <span>Dommer: <?php echo e($class['judge']); ?></span>
            </div>
            <p><strong>Ansvarlig klubb:</strong> <?php echo e($class['responsible_club']); ?></p>
            <p><strong>Kontakt:</strong> <?php echo e($class['contact_person']); ?> / <?php echo e($class['contact_phone']); ?> / <?php echo e($class['contact_email']); ?></p>
            <p><strong>Pris:</strong> <?php echo e(number_format((float) $class['price'], 0, ',', ' ')); ?> kr</p>
            <p><strong>Ledige plasser:</strong> <?php echo e($available); ?> / <?php echo e((int) $class['max_participants']); ?></p>
            <p><strong>Påmeldingsfrist:</strong> <?php echo e(formatDate($class['registration_deadline'])); ?></p>
            <p><?php echo nl2br(e($class['notes'] ?? '')); ?></p>
        </section>

        <section class="card">
            <h2>Registrer påmelding</h2>

            <?php if($errors): ?>
                <div class="notice error">
                    <?php $__currentLoopData = $errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo e(route('apply', ['class_id' => (int) $class['id']])); ?>">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="owner_name">Fører / eier</label>
                    <input id="owner_name" name="owner_name" value="<?php echo e($formData['owner_name'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="email">E-post</label>
                    <input id="email" type="email" name="email" value="<?php echo e($formData['email'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="phone">Telefon</label>
                    <input id="phone" name="phone" value="<?php echo e($formData['phone'] ?? ''); ?>">
                </div>
                <div>
                    <label for="dog_name">Hundens navn</label>
                    <input id="dog_name" name="dog_name" value="<?php echo e($formData['dog_name'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="dog_regno">Registreringsnummer</label>
                    <input id="dog_regno" name="dog_regno" value="<?php echo e($formData['dog_regno'] ?? ''); ?>">
                </div>
                <div>
                    <label for="dog_breed">Rase</label>
                    <input id="dog_breed" name="dog_breed" value="<?php echo e($formData['dog_breed'] ?? ''); ?>">
                </div>
                <div>
                    <label for="dog_class">Registrert konkurranseklasse</label>
                    <input id="dog_class" name="dog_class" value="<?php echo e($formData['dog_class'] ?? ''); ?>">
                </div>
                <div>
                    <label for="comment">Kommentar</label>
                    <textarea id="comment" name="comment"><?php echo e($formData['comment'] ?? ''); ?></textarea>
                </div>
                <button class="button primary" type="submit">Send påmelding</button>
            </form>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/apply.blade.php ENDPATH**/ ?>
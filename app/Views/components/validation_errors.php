<!-- Visualizzazione errori di validazione -->
<?php if (isset($validation) && $validation->getErrors()): ?>
    <div class="alert alert-danger mt-3">
        <h5><i class="icon fas fa-exclamation-triangle"></i> Errori di validazione</h5>
        <ul class="mb-0">
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?> 
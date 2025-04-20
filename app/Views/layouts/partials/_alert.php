<?php
// File: app/Views/layout/partials/_alert.php
// Mostra i messaggi flash di successo, errore o avviso
?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-check"></i> Successo!</h5>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-ban"></i> Errore!</h5>
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')) : ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-exclamation-triangle"></i> Attenzione!</h5>
        <?= session()->getFlashdata('warning') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('info')) : ?>
    <div class="alert alert-info alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-info"></i> Informazione!</h5>
        <?= session()->getFlashdata('info') ?>
    </div>
<?php endif; ?>

<?php if (isset($validation)) : ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-exclamation-circle"></i> Ci sono errori nel form!</h5>
        <?= $validation->listErrors() ?>
    </div>
<?php endif; ?> 
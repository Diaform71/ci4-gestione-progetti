<?= $this->extend('layouts/install') ?>

<?= $this->section('title') ?>
<title><?= $title ?? 'Installazione Completata - Gestione Progetti' ?></title>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .install-box {
        max-width: 800px;
        margin: 0 auto;
    }
    .login-page, .register-page {
        background: linear-gradient(to bottom, #007bff, #6c757d);
        height: auto;
        min-height: 100vh;
        align-items: start;
        padding-top: 50px;
    }
    .card-header {
        padding: 1rem;
    }
    .logo-container {
        text-align: center;
        margin-bottom: 20px;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .logo-container h1 {
        font-weight: 300;
        color: #fff;
    }
    .logo-container h1 b {
        font-weight: 700;
    }
    .success-icon {
        font-size: 100px;
        color: #28a745;
        margin-bottom: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('logo-container') ?>
<div class="logo-container">
    <h1><b>Gestione</b> Progetti</h1>
</div>
<?= $this->endSection() ?>

<?= $this->section('install_box') ?>
<div class="install-box">
    <div class="card">
        <div class="card-header bg-success">
            <h4 class="mb-0 text-white"><i class="fas fa-check-circle mr-2"></i> Installazione Completata</h4>
        </div>
        <div class="card-body text-center">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h4 class="mb-3">Congratulazioni!</h4>
            <p class="mb-4">L'applicazione è stata installata con successo.</p>
            
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Importante!</h5>
                <p>Per motivi di sicurezza, si consiglia di eliminare o rinominare la cartella "install" o disabilitare il controller di installazione.</p>
            </div>
            
            <div class="mt-4">
                <a href="<?= base_url() ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home mr-2"></i> Vai alla pagina iniziale
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>
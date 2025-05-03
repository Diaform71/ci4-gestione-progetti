<?= $this->extend('layouts/install') ?>

<?= $this->section('title') ?>
<title><?= $title ?? 'Gestione Progetti' ?></title>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
        .install-box {
            max-width: 1000px;
            min-width: 600px;
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
    </style>
<?= $this->endSection() ?>

<?= $this->section('logo-container') ?>
<div class="logo-container">
    <h1><b>Gestione</b> Progetti</h1>
</div>
<?= $this->endSection() ?>

<?= $this->section('install_box') ?>
<div class="install-box">
    <div class="card shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="mb-0 text-white"><i class="fas fa-cog mr-2"></i> Installazione Applicazione</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-4">Benvenuto nella procedura di installazione</h5>
            <p>Questa procedura ti guiderà attraverso i seguenti passaggi:</p>
            <ol>
                <li>Verifica dei requisiti di sistema</li>
                <li>Configurazione del database</li>
                <li>Migrazione del database e creazione dell'utente amministratore</li>
            </ol>
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> Nota:</h5>
                <p>Assicurati di avere le seguenti informazioni prima di procedere:</p>
                <ul>
                    <li>Dati di accesso al database (host, nome utente, password)</li>
                    <li>Dati per la creazione dell'account amministratore</li>
                </ul>
            </div>
            <div class="mt-4 text-end">
                <a href="<?= site_url('install/requirements') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-right mr-2"></i> Inizia Installazione
                </a>
            </div>
        </div>
        <div class="card-footer">
            <small class="text-muted">Versione Installer: <?= $version ?></small>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->endSection() ?>
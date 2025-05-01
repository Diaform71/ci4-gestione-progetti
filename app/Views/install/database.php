<?= $this->extend('layouts/install') ?>

<?= $this->section('title') ?>
<title><?= $title ?></title>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .install-box {
        max-width: 800px;
        margin: 0 auto;
    }

    .login-page,
    .register-page {
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

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 0 15px;
    }

    .step {
        text-align: center;
        width: 33%;
        position: relative;
    }

    .step.active .step-icon {
        background-color: #007bff;
        color: white;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 5px;
        color: #6c757d;
        font-weight: bold;
        border: 2px solid #e9ecef;
    }

    .step-title {
        font-size: 0.8rem;
        color: rgb(234, 238, 241);
    }

    .step.active .step-title {
        color: rgb(4, 41, 82);
        font-weight: bold;
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
    <div class="step-indicator">
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-title">Benvenuto</div>
        </div>
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-title">Requisiti</div>
        </div>
        <div class="step active">
            <div class="step-icon">3</div>
            <div class="step-title">Database</div>
        </div>
        <div class="step">
            <div class="step-icon">4</div>
            <div class="step-title">Amministratore</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="mb-0 text-white"><i class="fas fa-database mr-2"></i> Configurazione Database</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">Inserisci i dettagli di connessione al database</h5>

            <div class="alert alert-info">
                <i class="icon fas fa-info-circle"></i>
                <strong>Nota:</strong> Se il database specificato non esiste, il sistema tenterà di crearlo automaticamente.
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Errore!</h5>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Attenzione!</h5>
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('install/database') ?>" method="post" id="databaseForm" class="form-horizontal">
                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="hostname" class="col-sm-3 col-form-label">Host Database</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="hostname" name="hostname" value="localhost" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="port" class="col-sm-3 col-form-label">Porta Database</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="port" name="port" value="3306" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="database" class="col-sm-3 col-form-label">Nome Database</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-database"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="database" name="database" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="username" class="col-sm-3 col-form-label">Utente Database</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-sm-3 col-form-label">Password Database</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="prefix" class="col-sm-3 col-form-label">Prefisso Tabelle</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="prefix" name="prefix" placeholder="Opzionale">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?= site_url('install/requirements') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Indietro
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="defaultText">Verifica Connessione <i class="fas fa-arrow-right ml-2"></i></span>
                        <span id="loadingText" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Connessione in corso...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('databaseForm');
        const defaultText = document.getElementById('defaultText');
        const loadingText = document.getElementById('loadingText');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            // Mostra loading state
            defaultText.classList.add('d-none');
            loadingText.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    });
</script>
<?= $this->endSection() ?>
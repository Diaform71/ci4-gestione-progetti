<?= $this->extend('layouts/install') ?>

<?= $this->section('title') ?>
<title><?= $title ?></title>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .install-box {
        max-width: 1000px;
        min-width: 600px;
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
        <div class="step active">
            <div class="step-icon">2</div>
            <div class="step-title">Requisiti</div>
        </div>
        <div class="step">
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
            <h4 class="mb-0 text-white"><i class="fas fa-clipboard-check mr-2"></i> Verifica Requisiti</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">Verifica dei requisiti di sistema</h5>

            <div class="card card-outline card-primary">
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Requisito</th>
                                <th style="width: 25%">Stato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PHP >= 7.4.0</td>
                                <td>
                                    <?php if ($requirements['php_version']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK (<?= PHP_VERSION ?>)</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non soddisfatto (<?= PHP_VERSION ?>)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Estensione cURL</td>
                                <td>
                                    <?php if ($requirements['curl']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non installata</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Estensione Intl</td>
                                <td>
                                    <?php if ($requirements['intl']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non installata</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Estensione JSON</td>
                                <td>
                                    <?php if ($requirements['json']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non installata</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Estensione mbstring</td>
                                <td>
                                    <?php if ($requirements['mbstring']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non installata</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Estensione XML</td>
                                <td>
                                    <?php if ($requirements['xml']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non installata</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>File .env scrivibile</td>
                                <td>
                                    <?php if ($requirements['writable_env']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non scrivibile</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Cartella writable/ scrivibile</td>
                                <td>
                                    <?php if ($requirements['writable_writepath']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non scrivibile</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Cartella uploads/ scrivibile</td>
                                <td>
                                    <?php if ($requirements['writable_uploadpath']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> OK</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non scrivibile</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="<?= site_url('install') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Indietro
                </a>
                <?php if ($requirements_satisfied): ?>
                    <a href="<?= site_url('install/database') ?>" class="btn btn-primary">
                        Avanti <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                <?php else: ?>
                    <button class="btn btn-danger" disabled>
                        <i class="fas fa-times-circle mr-2"></i> Requisiti non soddisfatti
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->endSection() ?>
<
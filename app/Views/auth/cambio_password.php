<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Cambio Password
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Cambio Password
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Cambio Password</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifica la tua password</h3>
                </div>
                
                <!-- Visualizzazione errori di validazione -->
                <?= $this->include('components/validation_errors') ?>
                
                <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
                <?php endif; ?>
                
                <?php if (session()->has('message')): ?>
                <div class="alert alert-success">
                    <?= session('message') ?>
                </div>
                <?php endif; ?>
                
                <form action="<?= base_url('cambio-password') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="password_attuale">Password Attuale</label>
                            <input type="password" class="form-control" id="password_attuale" name="password_attuale" required>
                            <small class="text-muted">Inserisci la tua password attuale</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_nuova">Nuova Password</label>
                            <input type="password" class="form-control" id="password_nuova" name="password_nuova" required>
                            <small class="text-muted">Inserisci la nuova password (almeno 8 caratteri)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_conferma">Conferma Password</label>
                            <input type="password" class="form-control" id="password_conferma" name="password_conferma" required>
                            <small class="text-muted">Conferma la nuova password</small>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salva Nuova Password
                        </button>
                        <a href="<?= base_url() ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annulla
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const passwordNuova = document.getElementById('password_nuova');
    const passwordConferma = document.getElementById('password_conferma');
    
    form.addEventListener('submit', function(e) {
        if (passwordNuova.value !== passwordConferma.value) {
            e.preventDefault();
            alert('Le password non corrispondono');
        }
    });
});
</script>
<?= $this->endSection() ?> 
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Modifica Profilo<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modifica Profilo<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('profilo') ?>">Il Mio Profilo</a></li>
<li class="breadcrumb-item active">Modifica</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifica Dati Personali</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($validation) && $validation->getErrors()): ?>
                        <div class="alert alert-danger">
                            <?= $validation->listErrors() ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('profilo/aggiorna') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?= old('nome', $utente['nome']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cognome">Cognome</label>
                                    <input type="text" class="form-control" id="cognome" name="cognome" 
                                           value="<?= old('cognome', $utente['cognome']) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= old('email', $utente['email']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" 
                                   value="<?= esc($utente['username']) ?>" readonly disabled>
                            <small class="text-muted">Lo username non può essere modificato.</small>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva Modifiche
                                </button>
                                <a href="<?= base_url('profilo') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annulla
                                </a>
                                <a href="<?= base_url('cambio-password') ?>" class="btn btn-info">
                                    <i class="fas fa-key"></i> Cambia Password
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 
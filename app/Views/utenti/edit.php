<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?? 'Modifica Utente' ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('utenti') ?>">Utenti</a></li>
                    <li class="breadcrumb-item active">Modifica</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('error') ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Modifica Utente: <?= esc($utente['username']) ?></h3>
            </div>
            <form action="<?= base_url('utenti/update/' . $utente['id']) ?>" method="post" id="form-modifica-utente">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                                       id="username" name="username" value="<?= old('username', $utente['username']) ?>" required>
                                <?php if (session('errors.username')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.username') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                       id="password" name="password">
                                <?php if (session('errors.password')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.password') ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Lasciare vuoto per mantenere la password attuale. La nuova password deve essere di almeno 8 caratteri.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                       id="email" name="email" value="<?= old('email', $utente['email']) ?>">
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control <?= session('errors.nome') ? 'is-invalid' : '' ?>" 
                                       id="nome" name="nome" value="<?= old('nome', $utente['nome']) ?>">
                                <?php if (session('errors.nome')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.nome') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="cognome">Cognome</label>
                                <input type="text" class="form-control <?= session('errors.cognome') ? 'is-invalid' : '' ?>" 
                                       id="cognome" name="cognome" value="<?= old('cognome', $utente['cognome']) ?>">
                                <?php if (session('errors.cognome')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.cognome') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="ruolo">Ruolo *</label>
                                <select class="form-control <?= session('errors.ruolo') ? 'is-invalid' : '' ?>" 
                                        id="ruolo" name="ruolo" required>
                                    <option value="user" <?= old('ruolo', $utente['ruolo']) === 'user' ? 'selected' : '' ?>>Utente Standard</option>
                                    <option value="admin" <?= old('ruolo', $utente['ruolo']) === 'admin' ? 'selected' : '' ?>>Amministratore</option>
                                </select>
                                <?php if (session('errors.ruolo')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.ruolo') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="attivo" name="attivo" value="1" 
                                           <?= old('attivo', $utente['attivo']) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="attivo">Utente attivo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">Ultimo accesso:</span>
                                    <span class="info-box-number text-muted mb-0">
                                        <?= $utente['ultimo_accesso'] ? date('d/m/Y H:i:s', strtotime($utente['ultimo_accesso'])) : 'Mai' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                    <a href="<?= base_url('utenti') ?>" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script>
$(function() {
    // Validazione client-side del form
    $('#form-modifica-utente').validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            password: {
                minlength: 8
            },
            email: {
                email: true,
                maxlength: 100
            },
            nome: {
                maxlength: 100
            },
            cognome: {
                maxlength: 100
            },
            ruolo: {
                required: true
            }
        },
        messages: {
            username: {
                required: "Il campo username è obbligatorio",
                minlength: "L'username deve essere composto da almeno {0} caratteri",
                maxlength: "L'username non può superare i {0} caratteri"
            },
            password: {
                minlength: "La password deve essere composta da almeno {0} caratteri"
            },
            email: {
                email: "Inserisci un indirizzo email valido",
                maxlength: "L'email non può superare i {0} caratteri"
            },
            nome: {
                maxlength: "Il nome non può superare i {0} caratteri"
            },
            cognome: {
                maxlength: "Il cognome non può superare i {0} caratteri"
            },
            ruolo: {
                required: "Seleziona un ruolo"
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
<?= $this->endSection() ?> 
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
<li class="breadcrumb-item active">Modifica Impostazione</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <?= view('layouts/partials/_alert') ?>
        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Modifica impostazione: <?= esc($impostazione['chiave']) ?></h3>
            </div>
            <div class="card-body">
                <form action="<?= site_url('impostazioni/aggiorna/' . $impostazione['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group row">
                        <label for="chiave" class="col-sm-3 col-form-label">Chiave</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control-plaintext" id="chiave" value="<?= esc($impostazione['chiave']) ?>" readonly>
                            <small class="form-text text-muted">La chiave non può essere modificata.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="valore" class="col-sm-3 col-form-label">Valore <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <?php if ($impostazione['tipo'] === 'booleano'): ?>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="valore" 
                                           name="valore" 
                                           value="1" 
                                           <?= $impostazione['valore'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="valore">
                                        <?= $impostazione['valore'] ? 'Attivo' : 'Disattivo' ?>
                                    </label>
                                </div>
                            <?php elseif ($impostazione['tipo'] === 'textarea' || ($impostazione['tipo'] === 'stringa' && strlen($impostazione['valore']) > 100)): ?>
                                <textarea class="form-control <?= session('errors.valore') ? 'is-invalid' : '' ?>" 
                                          id="valore" 
                                          name="valore" 
                                          rows="3"><?= esc($impostazione['valore']) ?></textarea>
                            <?php elseif ($impostazione['tipo'] === 'select' && $impostazione['chiave'] === 'tema_predefinito'): ?>
                                <select class="form-control <?= session('errors.valore') ? 'is-invalid' : '' ?>" 
                                        id="valore" 
                                        name="valore">
                                    <option value="light" <?= $impostazione['valore'] === 'light' ? 'selected' : '' ?>>Light</option>
                                    <option value="dark" <?= $impostazione['valore'] === 'dark' ? 'selected' : '' ?>>Dark</option>
                                </select>
                            <?php else: ?>
                                <input type="<?= $impostazione['tipo'] === 'intero' || $impostazione['tipo'] === 'decimale' ? 'number' : 'text' ?>" 
                                       class="form-control <?= session('errors.valore') ? 'is-invalid' : '' ?>" 
                                       id="valore" 
                                       name="valore" 
                                       value="<?= esc($impostazione['valore']) ?>"
                                       <?= $impostazione['tipo'] === 'intero' ? 'step="1"' : '' ?>
                                       <?= $impostazione['tipo'] === 'decimale' ? 'step="0.01"' : '' ?>
                                       required>
                            <?php endif; ?>
                            
                            <?php if (session('errors.valore')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.valore') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="tipo" class="col-sm-3 col-form-label">Tipo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control-plaintext" id="tipo" value="<?= esc($impostazione['tipo']) ?>" readonly>
                            <small class="form-text text-muted">Il tipo non può essere modificato.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="descrizione" class="col-sm-3 col-form-label">Descrizione</label>
                        <div class="col-sm-9">
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" 
                                id="descrizione" name="descrizione" rows="3"><?= esc($impostazione['descrizione']) ?></textarea>
                            <?php if (session('errors.descrizione')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.descrizione') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="gruppo" class="col-sm-3 col-form-label">Gruppo <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control <?= session('errors.gruppo') ? 'is-invalid' : '' ?>" 
                                id="gruppo" name="gruppo" required>
                                <option value="generale" <?= $impostazione['gruppo'] === 'generale' ? 'selected' : '' ?>>Generale</option>
                                <option value="email" <?= $impostazione['gruppo'] === 'email' ? 'selected' : '' ?>>Email</option>
                                <option value="notifiche" <?= $impostazione['gruppo'] === 'notifiche' ? 'selected' : '' ?>>Notifiche</option>
                                <option value="sistema" <?= $impostazione['gruppo'] === 'sistema' ? 'selected' : '' ?>>Sistema</option>
                                <option value="sicurezza" <?= $impostazione['gruppo'] === 'sicurezza' ? 'selected' : '' ?>>Sicurezza</option>
                                <option value="altro" <?= $impostazione['gruppo'] === 'altro' ? 'selected' : '' ?>>Altro</option>
                            </select>
                            <?php if (session('errors.gruppo')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.gruppo') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Aggiorna Impostazione
                            </button>
                            <a href="<?= site_url('impostazioni') ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Annulla
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 
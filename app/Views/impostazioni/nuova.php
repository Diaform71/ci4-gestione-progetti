<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('impostazioni') ?>">Impostazioni</a></li>
<li class="breadcrumb-item active">Nuova Impostazione</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <?= view('layouts/partials/_alert') ?>
        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Aggiungi una nuova impostazione</h3>
            </div>
            <div class="card-body">
                <form action="<?= site_url('impostazioni/salva') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group row">
                        <label for="chiave" class="col-sm-3 col-form-label">Chiave <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control <?= session('errors.chiave') ? 'is-invalid' : '' ?>" 
                                id="chiave" name="chiave" value="<?= old('chiave') ?>" required>
                            <?php if (session('errors.chiave')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.chiave') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Identificativo univoco dell'impostazione. Può contenere solo lettere, numeri, trattini e underscore.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="valore" class="col-sm-3 col-form-label">Valore <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control <?= session('errors.valore') ? 'is-invalid' : '' ?>" 
                                id="valore" name="valore" value="<?= old('valore') ?>" required>
                            <?php if (session('errors.valore')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.valore') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="tipo" class="col-sm-3 col-form-label">Tipo <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control <?= session('errors.tipo') ? 'is-invalid' : '' ?>" 
                                id="tipo" name="tipo" required>
                                <option value="stringa" <?= old('tipo') === 'stringa' ? 'selected' : '' ?>>Stringa</option>
                                <option value="intero" <?= old('tipo') === 'intero' ? 'selected' : '' ?>>Intero</option>
                                <option value="decimale" <?= old('tipo') === 'decimale' ? 'selected' : '' ?>>Decimale</option>
                                <option value="booleano" <?= old('tipo') === 'booleano' ? 'selected' : '' ?>>Booleano</option>
                                <option value="data" <?= old('tipo') === 'data' ? 'selected' : '' ?>>Data</option>
                                <option value="datetime" <?= old('tipo') === 'datetime' ? 'selected' : '' ?>>Data e Ora</option>
                                <option value="json" <?= old('tipo') === 'json' ? 'selected' : '' ?>>JSON</option>
                            </select>
                            <?php if (session('errors.tipo')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.tipo') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Il tipo di dato determina come verrà gestito e visualizzato il valore.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="descrizione" class="col-sm-3 col-form-label">Descrizione</label>
                        <div class="col-sm-9">
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" 
                                id="descrizione" name="descrizione" rows="3"><?= old('descrizione') ?></textarea>
                            <?php if (session('errors.descrizione')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.descrizione') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Una breve descrizione dell'impostazione e del suo scopo.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="gruppo" class="col-sm-3 col-form-label">Gruppo <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control <?= session('errors.gruppo') ? 'is-invalid' : '' ?>" 
                                id="gruppo" name="gruppo" required>
                                <option value="generale" <?= old('gruppo') === 'generale' ? 'selected' : '' ?>>Generale</option>
                                <option value="email" <?= old('gruppo') === 'email' ? 'selected' : '' ?>>Email</option>
                                <option value="notifiche" <?= old('gruppo') === 'notifiche' ? 'selected' : '' ?>>Notifiche</option>
                                <option value="sistema" <?= old('gruppo') === 'sistema' ? 'selected' : '' ?>>Sistema</option>
                                <option value="sicurezza" <?= old('gruppo') === 'sicurezza' ? 'selected' : '' ?>>Sicurezza</option>
                                <option value="altro" <?= old('gruppo') === 'altro' ? 'selected' : '' ?>>Altro</option>
                            </select>
                            <?php if (session('errors.gruppo')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.gruppo') ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Categoria dell'impostazione per raggruppare impostazioni simili.</small>
                        </div>
                    </div>
                    
                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salva Impostazione
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
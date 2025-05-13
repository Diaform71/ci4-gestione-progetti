<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('aliquote-iva') ?>">Aliquote IVA</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= isset($aliquota_iva) ? 'Modifica' : 'Nuova' ?> Aliquota IVA</h3>
                </div>
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php 
                $action = isset($aliquota_iva) ? base_url('aliquote-iva/update/' . $aliquota_iva['id']) : base_url('aliquote-iva/create');
                ?>
                
                <form action="<?= $action ?>" method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codice">Codice *</label>
                                    <input type="text" class="form-control" id="codice" name="codice" maxlength="10"
                                        value="<?= old('codice', isset($aliquota_iva) ? $aliquota_iva['codice'] : '') ?>" required>
                                    <small class="text-muted">Esempio: 22, 10, 4, 0, FC, NI</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descrizione">Descrizione *</label>
                                    <input type="text" class="form-control" id="descrizione" name="descrizione" 
                                        value="<?= old('descrizione', isset($aliquota_iva) ? $aliquota_iva['descrizione'] : '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="percentuale">Percentuale *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="percentuale" name="percentuale" step="0.01" min="0" max="100"
                                            value="<?= old('percentuale', isset($aliquota_iva) ? $aliquota_iva['percentuale'] : '') ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" id="note" name="note" rows="5"><?= old('note', isset($aliquota_iva) ? $aliquota_iva['note'] : '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('aliquote-iva') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Indietro
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salva
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 
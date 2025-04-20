<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('anagrafiche') ?>">Anagrafiche</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= isset($anagrafica) ? 'Modifica' : 'Nuova' ?> Anagrafica</h3>
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
                $action = isset($anagrafica) ? base_url('anagrafiche/update/' . $anagrafica['id']) : base_url('anagrafiche/create');
                ?>
                
                <form action="<?= $action ?>" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ragione_sociale">Ragione Sociale *</label>
                                    <input type="text" class="form-control <?= session('errors.ragione_sociale') ? 'is-invalid' : '' ?>" id="ragione_sociale" name="ragione_sociale" 
                                        value="<?= old('ragione_sociale', isset($anagrafica) ? $anagrafica['ragione_sociale'] : '') ?>" >
                                    <?php if (session('errors.ragione_sociale')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.ragione_sociale') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="indirizzo">Indirizzo</label>
                                    <input type="text" class="form-control <?= session('errors.indirizzo') ? 'is-invalid' : '' ?>" id="indirizzo" name="indirizzo" 
                                        value="<?= old('indirizzo', isset($anagrafica) ? $anagrafica['indirizzo'] : '') ?>">
                                    <?php if (session('errors.indirizzo')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.indirizzo') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="citta">Città</label>
                                            <input type="text" class="form-control <?= session('errors.citta') ? 'is-invalid' : '' ?>" id="citta" name="citta" 
                                                value="<?= old('citta', isset($anagrafica) ? $anagrafica['citta'] : '') ?>">
                                            <?php if (session('errors.citta')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.citta') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cap">CAP</label>
                                            <input type="text" class="form-control <?= session('errors.cap') ? 'is-invalid' : '' ?>" id="cap" name="cap" 
                                                value="<?= old('cap', isset($anagrafica) ? $anagrafica['cap'] : '') ?>">
                                            <?php if (session('errors.cap')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.cap') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nazione">Nazione</label>
                                    <input type="text" class="form-control <?= session('errors.nazione') ? 'is-invalid' : '' ?>" id="nazione" name="nazione" 
                                        value="<?= old('nazione', isset($anagrafica) ? $anagrafica['nazione'] : 'Italia') ?>">
                                    <?php if (session('errors.nazione')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.nazione') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email" 
                                        value="<?= old('email', isset($anagrafica) ? $anagrafica['email'] : '') ?>">
                                    <?php if (session('errors.email')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="url">Sito Web</label>
                                    <input type="text" class="form-control <?= session('errors.url') ? 'is-invalid' : '' ?>" id="url" name="url" 
                                        value="<?= old('url', isset($anagrafica) ? $anagrafica['url'] : '') ?>">
                                    <?php if (session('errors.url')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.url') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Telefono</label>
                                            <input type="text" class="form-control <?= session('errors.telefono') ? 'is-invalid' : '' ?>" id="telefono" name="telefono" 
                                                value="<?= old('telefono', isset($anagrafica) ? $anagrafica['telefono'] : '') ?>">
                                            <?php if (session('errors.telefono')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.telefono') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fax">Fax</label>
                                            <input type="text" class="form-control <?= session('errors.fax') ? 'is-invalid' : '' ?>" id="fax" name="fax" 
                                                value="<?= old('fax', isset($anagrafica) ? $anagrafica['fax'] : '') ?>">
                                            <?php if (session('errors.fax')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.fax') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="partita_iva">Partita IVA</label>
                                            <input type="text" class="form-control <?= session('errors.partita_iva') ? 'is-invalid' : '' ?>" id="partita_iva" name="partita_iva" 
                                                value="<?= old('partita_iva', isset($anagrafica) ? $anagrafica['partita_iva'] : '') ?>">
                                            <?php if (session('errors.partita_iva')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.partita_iva') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codice_fiscale">Codice Fiscale</label>
                                            <input type="text" class="form-control <?= session('errors.codice_fiscale') ? 'is-invalid' : '' ?>" id="codice_fiscale" name="codice_fiscale" 
                                                value="<?= old('codice_fiscale', isset($anagrafica) ? $anagrafica['codice_fiscale'] : '') ?>">
                                            <?php if (session('errors.codice_fiscale')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.codice_fiscale') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sdi">Codice SDI</label>
                                            <input type="text" class="form-control <?= session('errors.sdi') ? 'is-invalid' : '' ?>" id="sdi" name="sdi" maxlength="7" 
                                                value="<?= old('sdi', isset($anagrafica) ? $anagrafica['sdi'] : '') ?>">
                                            <?php if (session('errors.sdi')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.sdi') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_iva">Aliquota IVA</label>
                                            <select class="form-control <?= session('errors.id_iva') ? 'is-invalid' : '' ?>" id="id_iva" name="id_iva">
                                                <option value="">Seleziona...</option>
                                                <?php foreach ($aliquote_iva as $id => $aliquota): ?>
                                                    <option value="<?= $id ?>" <?= old('id_iva', isset($anagrafica) && $anagrafica['id_iva'] == $id ? 'selected' : '') ?>>
                                                        <?= esc($aliquota) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (session('errors.id_iva')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.id_iva') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input <?= session('errors.logo') ? 'is-invalid' : '' ?>" id="logo" name="logo" accept="image/*">
                                            <label class="custom-file-label" for="logo">Scegli file</label>
                                        </div>
                                    </div>
                                    <?php if (session('errors.logo')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= session('errors.logo') ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($anagrafica) && !empty($anagrafica['logo'])): ?>
                                        <div class="mt-2">
                                            <img src="<?= base_url('uploads/logos/' . $anagrafica['logo']) ?>" alt="Logo" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input <?= session('errors.cliente') ? 'is-invalid' : '' ?>" id="cliente" name="cliente" value="1" 
                                                    <?= old('cliente', isset($anagrafica) && $anagrafica['cliente'] ? 'checked' : '') ?>>
                                                <label class="custom-control-label" for="cliente">Cliente</label>
                                                <?php if (session('errors.cliente')): ?>
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.cliente') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input <?= session('errors.fornitore') ? 'is-invalid' : '' ?>" id="fornitore" name="fornitore" value="1" 
                                                    <?= old('fornitore', isset($anagrafica) && $anagrafica['fornitore'] ? 'checked' : '') ?>>
                                                <label class="custom-control-label" for="fornitore">Fornitore</label>
                                                <?php if (session('errors.fornitore')): ?>
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.fornitore') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input <?= session('errors.attivo') ? 'is-invalid' : '' ?>" id="attivo" name="attivo" value="1" 
                                                    <?= old('attivo', isset($anagrafica) ? ($anagrafica['attivo'] ? 'checked' : '') : 'checked') ?>>
                                                <label class="custom-control-label" for="attivo">Attivo</label>
                                                <?php if (session('errors.attivo')): ?>
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.attivo') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('anagrafiche') ?>" class="btn btn-secondary">
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

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Script per mostrare il nome del file selezionato nel campo di upload
        $('input[type="file"]').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
<?= $this->endSection() ?> 
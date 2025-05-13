<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Template Email' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Template Email' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('email-templates') ?>">Template Email</a></li>
<li class="breadcrumb-item active"><?= isset($template) ? 'Modifica' : 'Nuovo' ?></li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .note-editor {
        margin-bottom: 20px;
    }
    .placeholder-list {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .placeholder-list code {
        background-color: #e9ecef;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($template) ? 'Modifica Template Email' : 'Nuovo Template Email' ?></h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-12">
                <div class="placeholder-list">
                    <h5>Placeholder disponibili:</h5>
                    <p>Puoi utilizzare i seguenti placeholder nel tuo template. Verranno sostituiti con i dati effettivi durante l'invio.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-unstyled">
                                <li><code>{{azienda}}</code> - Nome dell'azienda</li>
                                <li><code>{{cliente}}</code> - Nome del cliente</li>
                                <li><code>{{data}}</code> - Data attuale</li>
                                <li><code>{{riferimento}}</code> - Riferimento</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-unstyled">
                                <li><code>{{utente}}</code> - Nome dell'utente</li>
                                <li><code>{{numero_ordine}}</code> - Numero ordine</li>
                                <li><code>{{numero_offerta}}</code> - Numero offerta</li>
                                <li><code>{{progetto}}</code> - Nome del progetto</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-unstyled">
                                <li><code>{{scadenza}}</code> - Data scadenza</li>
                                <li><code>{{totale}}</code> - Importo totale</li>
                                <li><code>{{validita}}</code> - Data validità</li>
                                <li><code>{{materiali}}</code> - Elenco materiali/servizi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= base_url('email-templates/salva') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if(isset($template)): ?>
                <input type="hidden" name="id" value="<?= $template['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= session('errors.nome') ? 'is-invalid' : '' ?>" 
                              id="nome" name="nome" value="<?= old('nome', $template['nome'] ?? '') ?>" required>
                        <?php if(session('errors.nome')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.nome') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo">Tipo Template <span class="text-danger">*</span></label>
                        <select class="form-control <?= session('errors.tipo') ? 'is-invalid' : '' ?>" 
                               id="tipo" name="tipo" required>
                            <option value="">Seleziona tipo...</option>
                            <?php foreach($tipi as $key => $label): ?>
                                <option value="<?= $key ?>" <?= old('tipo', $template['tipo'] ?? '') == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if(session('errors.tipo')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.tipo') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="oggetto">Oggetto Email <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= session('errors.oggetto') ? 'is-invalid' : '' ?>" 
                      id="oggetto" name="oggetto" value="<?= old('oggetto', $template['oggetto'] ?? '') ?>" required>
                <?php if(session('errors.oggetto')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.oggetto') ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="corpo">Corpo Email <span class="text-danger">*</span></label>
                <textarea class="form-control <?= session('errors.corpo') ? 'is-invalid' : '' ?>" 
                         id="corpo" name="corpo" rows="10"><?= old('corpo', $template['corpo'] ?? '') ?></textarea>
                <?php if(session('errors.corpo')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.corpo') ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <a href="<?= base_url('email-templates') ?>" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva Template</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#corpo').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
<?= $this->endSection() ?> 
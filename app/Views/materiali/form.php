<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Gestione Materiale' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Gestione Materiale' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('materiali') ?>">Materiali</a></li>
<li class="breadcrumb-item active"><?= isset($materiale) ? 'Modifica' : 'Nuovo' ?></li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
    .file-input-wrapper {
        margin-bottom: 10px;
    }
    .category-switch {
        margin-bottom: 15px;
    }
    .category-switch .custom-control {
        margin-right: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($materiale) ? 'Modifica Materiale' : 'Nuovo Materiale' ?></h3>
    </div>
    <div class="card-body">
        <form action="<?= isset($materiale) ? base_url('materiali/update/' . $materiale['id']) : base_url('materiali/create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codice">Codice <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= session('errors.codice') ? 'is-invalid' : '' ?>" 
                              id="codice" name="codice" value="<?= old('codice', $materiale['codice'] ?? '') ?>" required>
                        <?php if(session('errors.codice')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.codice') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="produttore">Produttore</label>
                        <input type="text" class="form-control <?= session('errors.produttore') ? 'is-invalid' : '' ?>" 
                              id="produttore" name="produttore" value="<?= old('produttore', $materiale['produttore'] ?? '') ?>">
                        <?php if(session('errors.produttore')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.produttore') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="descrizione">Descrizione</label>
                <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" 
                         id="descrizione" name="descrizione" rows="3"><?= old('descrizione', $materiale['descrizione'] ?? '') ?></textarea>
                <?php if(session('errors.descrizione')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.descrizione') ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="materiale">Materiale</label>
                <input type="text" class="form-control <?= session('errors.materiale') ? 'is-invalid' : '' ?>" 
                      id="materiale" name="materiale" value="<?= old('materiale', $materiale['materiale'] ?? '') ?>">
                <?php if(session('errors.materiale')): ?>
                    <div class="invalid-feedback">
                        <?= session('errors.materiale') ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Categorie</label>
                <div class="row category-switch">
                    <div class="col-md-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="commerciale" name="commerciale" 
                                <?= (old('commerciale', $materiale['commerciale'] ?? '') == '1') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="commerciale">Commerciale</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="meccanica" name="meccanica" 
                                <?= (old('meccanica', $materiale['meccanica'] ?? '') == '1') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="meccanica">Meccanica</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="elettrica" name="elettrica" 
                                <?= (old('elettrica', $materiale['elettrica'] ?? '') == '1') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="elettrica">Elettrica</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="pneumatica" name="pneumatica" 
                                <?= (old('pneumatica', $materiale['pneumatica'] ?? '') == '1') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="pneumatica">Pneumatica</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="in_produzione" name="in_produzione" 
                        <?= (old('in_produzione', $materiale['in_produzione'] ?? '1') == '1') ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="in_produzione">In Produzione</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="immagine">Immagine</label>
                <div class="file-input-wrapper">
                    <input type="file" class="form-control-file <?= session('errors.immagine') ? 'is-invalid' : '' ?>" 
                           id="immagine" name="immagine" accept="image/*" onchange="previewImage(this);">
                    <?php if(session('errors.immagine')): ?>
                        <div class="invalid-feedback">
                            <?= session('errors.immagine') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if(isset($materiale) && !empty($materiale['immagine'])): ?>
                    <div class="current-image mb-2">
                        <p>Immagine attuale:</p>
                        <img src="<?= base_url('uploads/materiali/' . $materiale['immagine']) ?>" alt="Immagine attuale" class="preview-image">
                    </div>
                <?php endif; ?>
                
                <div id="imagePreviewContainer" style="display: none;">
                    <p>Anteprima:</p>
                    <img id="imagePreview" class="preview-image" src="" alt="Anteprima immagine">
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <a href="<?= base_url('materiali') ?>" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary"><?= isset($materiale) ? 'Aggiorna' : 'Salva' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function previewImage(input) {
        var preview = document.getElementById('imagePreview');
        var container = document.getElementById('imagePreviewContainer');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            container.style.display = 'none';
        }
    }
</script>
<?= $this->endSection() ?> 
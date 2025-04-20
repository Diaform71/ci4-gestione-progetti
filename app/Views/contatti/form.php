<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('contatti') ?>">Contatti</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= isset($contatto) ? 'Modifica' : 'Nuovo' ?> Contatto</h3>
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
                $action = isset($contatto) ? base_url('contatti/update/' . $contatto['id']) : base_url('contatti/create');
                ?>
                
                <form action="<?= $action ?>" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                        value="<?= old('nome', isset($contatto) ? $contatto['nome'] : '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cognome">Cognome *</label>
                                    <input type="text" class="form-control" id="cognome" name="cognome" 
                                        value="<?= old('cognome', isset($contatto) ? $contatto['cognome'] : '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="<?= old('email', isset($contatto) ? $contatto['email'] : '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono">Telefono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" 
                                        value="<?= old('telefono', isset($contatto) ? $contatto['telefono'] : '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="interno">Interno</label>
                                    <input type="text" class="form-control" id="interno" name="interno" 
                                        value="<?= old('interno', isset($contatto) ? $contatto['interno'] : '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cellulare">Cellulare</label>
                                    <input type="text" class="form-control" id="cellulare" name="cellulare" 
                                        value="<?= old('cellulare', isset($contatto) ? $contatto['cellulare'] : '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="immagine">Foto</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="immagine" name="immagine" accept="image/*">
                                            <label class="custom-file-label" for="immagine">Scegli file</label>
                                        </div>
                                    </div>
                                    <?php if (isset($contatto) && !empty($contatto['immagine'])): ?>
                                        <div class="mt-2">
                                            <img src="<?= base_url('uploads/contatti/' . $contatto['immagine']) ?>" alt="Foto" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" id="note" name="note" rows="3"><?= old('note', isset($contatto) ? $contatto['note'] : '') ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="attivo" name="attivo" value="1" 
                                            <?= old('attivo', isset($contatto) ? ($contatto['attivo'] ? 'checked' : '') : 'checked') ?>>
                                        <label class="custom-control-label" for="attivo">Attivo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('contatti') ?>" class="btn btn-secondary">
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
        
        // Mostra messaggi di successo con SweetAlert2 se presenti nella sessione flash
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Successo',
                text: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        <?php endif; ?>
        
        // Aggiungi gestione del submit del form con feedback visivo
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...').prop('disabled', true);
            // Il form continuerà a essere inviato normalmente
        });
    });
</script>
<?= $this->endSection() ?> 
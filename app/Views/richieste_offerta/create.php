<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Nuova Richiesta d'Offerta<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Nuova Richiesta d'Offerta<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta') ?>">Richieste d'Offerta</a></li>
<li class="breadcrumb-item active">Nuova Richiesta</li>
<?= $this->endSection() ?>  

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <?= view('layouts/partials/_alert') ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inserisci i dati della nuova richiesta d'offerta</h3>
            </div>
            <form action="<?= site_url('richieste-offerta/create') ?>" method="post" id="formRichiestaOfferta">
                <?= csrf_field() ?>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="data">Data *</label>
                                <div class="input-group date" id="datepicker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input <?= session('errors.data') ? 'is-invalid' : '' ?>" 
                                           id="data_display" 
                                           data-target="#datepicker" data-toggle="datetimepicker"
                                           value="<?= set_value('data', date('d/m/Y')) ?>">
                                    <input type="hidden" name="data" id="data" 
                                           value="<?= set_value('data', date('Y-m-d')) ?>">
                                    <div class="input-group-append" data-target="#datepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <?php if (session('errors.data')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= session('errors.data') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="oggetto">Oggetto *</label>
                                <input type="text" class="form-control" id="oggetto" name="oggetto" 
                                       value="<?= set_value('oggetto') ?>" required 
                                       data-error="L'oggetto è obbligatorio">
                                <div class="invalid-feedback"><?= session('errors.oggetto') ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_anagrafica">Fornitore *</label>
                                <select class="form-control " id="id_anagrafica" name="id_anagrafica" required 
                                        data-error="Il fornitore è obbligatorio">
                                    <option value="">-- Seleziona fornitore --</option>
                                    <?php foreach ($fornitori as $fornitore): ?>
                                        <option value="<?= $fornitore['id'] ?>" <?= set_select('id_anagrafica', $fornitore['id']) ?>>
                                            <?= esc($fornitore['ragione_sociale']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= session('errors.id_anagrafica') ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_referente">Referente</label>
                                <select class="form-control" id="id_referente" name="id_referente">
                                    <option value="">-- Prima seleziona un fornitore --</option>
                                </select>
                                <div class="invalid-feedback"><?= session('errors.id_referente') ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_progetto">Progetto Collegato</label>
                                <select class="form-control select2" id="id_progetto" name="id_progetto">
                                    <option value="">-- Seleziona progetto --</option>
                                    <?php foreach ($progetti as $progetto): ?>
                                        <option value="<?= $progetto['id'] ?>" <?= set_select('id_progetto', $progetto['id']) ?>>
                                            <?= esc($progetto['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= session('errors.id_progetto') ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descrizione">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="4"><?= set_value('descrizione') ?></textarea>
                        <div class="invalid-feedback"><?= session('errors.descrizione') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"><?= set_value('note') ?></textarea>
                        <div class="invalid-feedback"><?= session('errors.note') ?></div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Salva</button>
                    <a href="<?= site_url('richieste-offerta') ?>" class="btn btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/additional-methods.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Inizializza Select2
    $('select').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Inizializza Datepicker
    $('#datepicker').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: 'it',
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'fas fa-calendar-check',
            clear: 'far fa-trash-alt',
            close: 'far fa-times-circle'
        }
    });
    
    // Aggiorna il campo nascosto quando cambia il datepicker
    $('#datepicker').on('change.datetimepicker', function(e) {
        if (e.date) {
            $('#data').val(e.date.format('YYYY-MM-DD'));
        } else {
            $('#data').val('');
        }
    });
    
    // Validator con jQuery Validation
    $('#formRichiestaOfferta').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        rules: {
            oggetto: {
                required: true
            },
            id_anagrafica: {
                required: true
            }
        },
        messages: {
            oggetto: {
                required: "L'oggetto è obbligatorio"
            },
            id_anagrafica: {
                required: "Il fornitore è obbligatorio"
            }
        }
    });
    
    // Carica i contatti quando viene selezionato un fornitore
    $('#id_anagrafica').change(function() {
        let idAnagrafica = $(this).val();
        
        if (idAnagrafica) {
            $.ajax({
                url: '<?= site_url('richieste-offerta/get-contatti-by-anagrafica') ?>',
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    'id_anagrafica': idAnagrafica
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Risposta AJAX ricevuta:', response);
                    let options = '<option value="">-- Seleziona referente --</option>';
                    
                    if (response.success && response.contatti && response.contatti.length > 0) {
                        $.each(response.contatti, function(index, contatto) {
                            options += '<option value="' + contatto.id_contatto + '">' + 
                                       contatto.nome + ' ' + contatto.cognome + 
                                       (contatto.email ? ' (' + contatto.email + ')' : '') + 
                                       (contatto.ruolo ? ' - ' + contatto.ruolo : '') +
                                       (contatto.principale == 1 ? ' (Principale)' : '') +
                                       '</option>';
                        });
                    } else {
                        options = '<option value="">Nessun contatto disponibile</option>';
                        console.log('Nessun contatto trovato per fornitore ID:', idAnagrafica);
                    }
                    
                    $('#id_referente').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Errore AJAX:', xhr.responseText);
                    alert('Errore durante il caricamento dei contatti: ' + error);
                }
            });
        } else {
            $('#id_referente').html('<option value="">-- Prima seleziona un fornitore --</option>');
        }
    });
});
</script>
<?= $this->endSection() ?> 
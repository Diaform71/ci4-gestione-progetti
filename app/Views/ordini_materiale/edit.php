<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Modifica Ordine Materiale<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modifica Ordine Materiale<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale') ?>">Ordini Materiale</a></li>
<li class="breadcrumb-item active">Modifica Ordine</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modifica Ordine Materiale</h3>
        </div>
        <div class="card-body">
            <form action="<?= site_url('ordini-materiale/update/' . $ordine['id']) ?>" method="post" id="editOrdineForm">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero">Numero Ordine*</label>
                            <input type="text" class="form-control" id="numero" name="numero" 
                                   value="<?= old('numero', $ordine['numero']) ?>" readonly>
                            <small class="text-muted">Il numero ordine è generato automaticamente e non può essere modificato</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data">Data*</label>
                            <div class="input-group date" id="dataOrdine" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="data" name="data" 
                                       data-target="#dataOrdine" value="<?= old('data', isset($ordine['data']) ? date('d/m/Y', strtotime($ordine['data'])) : '') ?>" required>
                                <div class="input-group-append" data-target="#dataOrdine" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="oggetto">Oggetto*</label>
                    <input type="text" class="form-control" id="oggetto" name="oggetto" 
                           value="<?= old('oggetto', $ordine['oggetto']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descrizione">Descrizione</label>
                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3"><?= old('descrizione', $ordine['descrizione']) ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_anagrafica">Fornitore*</label>
                            <select class="form-control select2" id="id_anagrafica" name="id_anagrafica" required>
                                <option value="">Seleziona fornitore...</option>
                                <?php foreach ($fornitori as $fornitore): ?>
                                    <option value="<?= $fornitore['id'] ?>" <?= (old('id_anagrafica', $ordine['id_anagrafica']) == $fornitore['id']) ? 'selected' : '' ?>>
                                        <?= esc($fornitore['ragione_sociale']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_referente">Referente</label>
                            <select class="form-control select2" id="id_referente" name="id_referente">
                                <option value="">Seleziona referente...</option>
                                <?php foreach ($contatti as $contatto): ?>
                                    <option value="<?= $contatto['id'] ?>" <?= (old('id_referente', $ordine['id_referente']) == $contatto['id']) ? 'selected' : '' ?>>
                                        <?= esc($contatto['nome'] . ' ' . $contatto['cognome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="id_progetto">Progetto</label>
                    <select class="form-control select2" id="id_progetto" name="id_progetto">
                        <option value="">Seleziona progetto...</option>
                        <?php foreach ($progetti as $progetto): ?>
                            <option value="<?= $progetto['id'] ?>" <?= (old('id_progetto', $ordine['id_progetto']) == $progetto['id']) ? 'selected' : '' ?>>
                                <?= esc($progetto['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="data_consegna_prevista">Data Consegna Prevista</label>
                    <div class="input-group date" id="dataConsegnaPrevista" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" id="data_consegna_prevista" name="data_consegna_prevista" 
                               data-target="#dataConsegnaPrevista" value="<?= old('data_consegna_prevista', !empty($ordine['data_consegna_prevista']) ? date('d/m/Y', strtotime($ordine['data_consegna_prevista'])) : '') ?>">
                        <div class="input-group-append" data-target="#dataConsegnaPrevista" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="condizioni_pagamento">Condizioni di Pagamento</label>
                            <select class="form-control select2" id="condizioni_pagamento" name="condizioni_pagamento">
                                <option value="">Seleziona condizioni...</option>
                                <option value="Bonifico 30gg" <?= (old('condizioni_pagamento', $ordine['condizioni_pagamento']) == 'Bonifico 30gg') ? 'selected' : '' ?>>Bonifico 30gg</option>
                                <option value="Bonifico 60gg" <?= (old('condizioni_pagamento', $ordine['condizioni_pagamento']) == 'Bonifico 60gg') ? 'selected' : '' ?>>Bonifico 60gg</option>
                                <option value="Bonifico 90gg" <?= (old('condizioni_pagamento', $ordine['condizioni_pagamento']) == 'Bonifico 90gg') ? 'selected' : '' ?>>Bonifico 90gg</option>
                                <option value="Rimessa diretta" <?= (old('condizioni_pagamento', $ordine['condizioni_pagamento']) == 'Rimessa diretta') ? 'selected' : '' ?>>Rimessa diretta</option>
                                <option value="Pagamento anticipato" <?= (old('condizioni_pagamento', $ordine['condizioni_pagamento']) == 'Pagamento anticipato') ? 'selected' : '' ?>>Pagamento anticipato</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="condizioni_consegna">Condizioni di Consegna</label>
                            <input type="text" class="form-control" id="condizioni_consegna" name="condizioni_consegna" 
                                   value="<?= old('condizioni_consegna', $ordine['condizioni_consegna']) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea class="form-control" id="note" name="note" rows="3"><?= old('note', $ordine['note']) ?></textarea>
                </div>
                
                <div class="form-group text-right">
                    <a href="<?= site_url('ordini-materiale/' . $ordine['id']) ?>" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/locale/it.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/additional-methods.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Inizializza Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Imposta la localizzazione italiana di moment.js
    moment.locale('it');
    
    // Funzioni per formattare le date
    function formatDateForDisplay(dateString) {
        if (!dateString) return '';
        try {
            return moment(dateString).format('DD/MM/YYYY');
        } catch (e) {
            console.error('Errore nella formattazione della data:', e);
            return dateString;
        }
    }
    
    // Inizializza i datepicker
    $('#dataOrdine').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: 'it',
        useCurrent: false
    });
    
    $('#dataConsegnaPrevista').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: 'it',
        useCurrent: false
    });
    
    // Assicuriamoci che le date siano visualizzate correttamente
    if ($('#data').val()) {
        $('#dataOrdine').datetimepicker('date', moment($('#data').val(), 'DD/MM/YYYY'));
    }
    
    if ($('#data_consegna_prevista').val()) {
        $('#dataConsegnaPrevista').datetimepicker('date', moment($('#data_consegna_prevista').val(), 'DD/MM/YYYY'));
    }
    
    // Inizializza validazione form
    $('#editOrdineForm').validate({
        rules: {
            oggetto: {
                required: true,
                minlength: 3
            },
            id_anagrafica: {
                required: true
            }
        },
        messages: {
            oggetto: {
                required: "L'oggetto è obbligatorio",
                minlength: "L'oggetto deve essere di almeno {0} caratteri"
            },
            id_anagrafica: {
                required: "Il fornitore è obbligatorio"
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
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    
    // Carica i contatti quando cambia il fornitore
    $('#id_anagrafica').change(function() {
        const idAnagrafica = $(this).val();
        if (idAnagrafica) {
            $.ajax({
                url: '<?= site_url('ordini-materiale/get-contatti-by-anagrafica') ?>',
                type: 'POST',
                data: {
                    id_anagrafica: idAnagrafica,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(data) {
                    let options = '<option value="">Seleziona referente...</option>';
                    $.each(data, function(index, contatto) {
                        options += `<option value="${contatto.id}">${contatto.nome} ${contatto.cognome}</option>`;
                    });
                    $('#id_referente').html(options);
                },
                error: function() {
                    console.error('Errore nel caricamento dei contatti');
                }
            });
        } else {
            $('#id_referente').html('<option value="">Seleziona referente...</option>');
        }
    });
});
</script>
<?= $this->endSection() ?> 
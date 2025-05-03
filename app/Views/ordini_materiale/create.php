<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale') ?>">Ordini di Acquisto</a></li>
<li class="breadcrumb-item active">Nuovo</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <form action="<?= site_url('ordini-materiale/create') ?>" method="post" id="createOrdineForm">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dati Ordine</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="data">Data:</label>
                                <div class="input-group date" id="datepicker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input <?= session('errors.data') ? 'is-invalid' : '' ?>" 
                                           id="data_display" 
                                           data-target="#datepicker" data-toggle="datetimepicker"
                                           value="<?= old('data') ?: date('d/m/Y') ?>">
                                    <input type="hidden" name="data" id="data" 
                                           value="<?= old('data') ?: date('Y-m-d') ?>">
                                    <div class="input-group-append" data-target="#datepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <?php if (session('errors.data')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.data') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="data_consegna_prevista">Data Consegna Prevista:</label>
                                <div class="input-group date" id="data_consegna_prevista_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input <?= session('errors.data_consegna_prevista') ? 'is-invalid' : '' ?>" 
                                           id="data_consegna_prevista_display" 
                                           data-target="#data_consegna_prevista_picker" data-toggle="datetimepicker"
                                           value="<?= old('data_consegna_prevista') ? date('d/m/Y', strtotime(old('data_consegna_prevista'))) : '' ?>">
                                    <input type="hidden" name="data_consegna_prevista" id="data_consegna_prevista" 
                                           value="<?= old('data_consegna_prevista') ?: '' ?>">
                                    <div class="input-group-append" data-target="#data_consegna_prevista_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <?php if (session('errors.data_consegna_prevista')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.data_consegna_prevista') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                            
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_anagrafica">Fornitore: <span class="text-danger">*</span></label>
                                <select name="id_anagrafica" id="id_anagrafica" class="form-control select2 <?= session('errors.id_anagrafica') ? 'is-invalid' : '' ?>" required>
                                    <option value="">- Seleziona Fornitore -</option>
                                    <?php foreach ($fornitori as $fornitore): ?>
                                        <option value="<?= $fornitore['id'] ?>" <?= old('id_anagrafica') == $fornitore['id'] ? 'selected' : '' ?>>
                                            <?= esc($fornitore['ragione_sociale']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_anagrafica')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.id_anagrafica') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id_referente">Referente:</label>
                                <select name="id_referente" id="id_referente" class="form-control select2 <?= session('errors.id_referente') ? 'is-invalid' : '' ?>">
                                    <option value="">- Seleziona Referente -</option>
                                </select>
                                <?php if (session('errors.id_referente')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.id_referente') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_progetto">Progetto:</label>
                                <select name="id_progetto" id="id_progetto" class="form-control select2 <?= session('errors.id_progetto') ? 'is-invalid' : '' ?>">
                                    <option value="">- Seleziona Progetto -</option>
                                    <?php foreach ($progetti as $progetto): ?>
                                        <option value="<?= $progetto['id'] ?>" <?= old('id_progetto') == $progetto['id'] ? 'selected' : '' ?>>
                                            <?= esc($progetto['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_progetto')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.id_progetto') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id_offerta_fornitore">Da Offerta Fornitore:</label>
                                <select name="id_offerta_fornitore" id="id_offerta_fornitore" class="form-control select2 <?= session('errors.id_offerta_fornitore') ? 'is-invalid' : '' ?>">
                                    <option value="">- Seleziona Offerta -</option>
                                    <?php foreach ($offerteFornitore as $offerta): ?>
                                        <option value="<?= $offerta['id'] ?>" 
                                                <?= old('id_offerta_fornitore') == $offerta['id'] ? 'selected' : '' ?> 
                                                data-anagrafica="<?= $offerta['id_anagrafica'] ?>">
                                            <?= esc($offerta['numero']) ?> - <?= esc($offerta['oggetto']) ?> (<?= esc($offerta['nome_fornitore']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_offerta_fornitore')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.id_offerta_fornitore') ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Se selezionato, potrai importare le voci dall'offerta.</small>
                            </div>
                        </div>
                            
                        <div class="form-group">
                            <label for="oggetto">Oggetto: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.oggetto') ? 'is-invalid' : '' ?>" id="oggetto" name="oggetto" placeholder="Oggetto dell'ordine" value="<?= old('oggetto') ?>" required>
                            <?php if (session('errors.oggetto')): ?>
                                <div class="invalid-feedback"><?= session('errors.oggetto') ?></div>
                            <?php endif; ?>
                        </div>
                            
                        <div class="form-group">
                            <label for="descrizione">Descrizione:</label>
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" id="descrizione" name="descrizione" rows="3" placeholder="Descrizione dell'ordine"><?= old('descrizione') ?></textarea>
                            <?php if (session('errors.descrizione')): ?>
                                <div class="invalid-feedback"><?= session('errors.descrizione') ?></div>
                            <?php endif; ?>
                        </div>
                            
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_condizione_pagamento">Condizioni di Pagamento:</label>
                                <select name="id_condizione_pagamento" id="id_condizione_pagamento" class="form-control select2 <?= session('errors.id_condizione_pagamento') ? 'is-invalid' : '' ?>">
                                    <option value="">- Seleziona Condizione -</option>
                                    <?php foreach ($condizioniPagamento as $cp): ?>
                                        <option value="<?= $cp['id'] ?>" <?= old('id_condizione_pagamento') == $cp['id'] ? 'selected' : '' ?>>
                                            <?= esc($cp['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_condizione_pagamento')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.id_condizione_pagamento') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="condizioni_consegna">Condizioni di Consegna:</label>
                                <input type="text" class="form-control <?= session('errors.condizioni_consegna') ? 'is-invalid' : '' ?>" id="condizioni_consegna" name="condizioni_consegna" placeholder="Es: Franco nostro magazzino" value="<?= old('condizioni_consegna') ?>">
                                <?php if (session('errors.condizioni_consegna')): ?>
                                    <div class="invalid-feedback"><?= session('errors.condizioni_consegna') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                            
                        <div class="form-group">
                            <label for="note">Note:</label>
                            <textarea class="form-control <?= session('errors.note') ? 'is-invalid' : '' ?>" id="note" name="note" rows="2" placeholder="Note aggiuntive"><?= old('note') ?></textarea>
                            <?php if (session('errors.note')): ?>
                                <div class="invalid-feedback"><?= session('errors.note') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informazioni aggiuntive</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="riferimento_esterno">Riferimento Esterno:</label>
                            <input type="text" class="form-control <?= session('errors.riferimento_esterno') ? 'is-invalid' : '' ?>" id="riferimento_esterno" name="riferimento_esterno" placeholder="Numero di riferimento esterno" value="<?= old('riferimento_esterno') ?>">
                            <?php if (session('errors.riferimento_esterno')): ?>
                                <div class="invalid-feedback"><?= session('errors.riferimento_esterno') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="indirizzo_consegna">Indirizzo di Consegna:</label>
                            <textarea class="form-control <?= session('errors.indirizzo_consegna') ? 'is-invalid' : '' ?>" id="indirizzo_consegna" name="indirizzo_consegna" rows="3" placeholder="Indirizzo completo di consegna"><?= old('indirizzo_consegna') ?></textarea>
                            <?php if (session('errors.indirizzo_consegna')): ?>
                                <div class="invalid-feedback"><?= session('errors.indirizzo_consegna') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crea Ordine</button>
                <a href="<?= site_url('ordini-materiale') ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script>
$(function () {
    // Inizializza i selettori di data
    $('#datepicker, #data_consegna_prevista_picker').datetimepicker({
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
    
    // Aggiorna i campi nascosti quando cambiano i datepicker
    $('#datepicker').on('change.datetimepicker', function(e) {
        if (e.date) {
            $('#data').val(e.date.format('YYYY-MM-DD'));
        } else {
            $('#data').val('');
        }
    });
    
    $('#data_consegna_prevista_picker').on('change.datetimepicker', function(e) {
        if (e.date) {
            $('#data_consegna_prevista').val(e.date.format('YYYY-MM-DD'));
        } else {
            $('#data_consegna_prevista').val('');
        }
    });

    // Inizializza Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Carica i contatti quando viene selezionato un fornitore
    $('#id_anagrafica').on('change', function() {
        var idAnagrafica = $(this).val();
        console.log("Fornitore selezionato: ID = " + idAnagrafica);
        
        if (idAnagrafica) {
            $.ajax({
                url: '<?= site_url('ordini-materiale/get-contatti-by-anagrafica') ?>',
                type: 'POST',
                data: {
                    id_anagrafica: idAnagrafica,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    var options = '<option value="">- Seleziona Referente -</option>';
                    
                    if (response.success && response.contatti.length > 0) {
                        $.each(response.contatti, function(index, contatto) {
                            var nomeCompleto = contatto.nome + ' ' + contatto.cognome;
                            if (contatto.email) {
                                nomeCompleto += ' (' + contatto.email + ')';
                            }
                            options += '<option value="' + contatto.id + '">' + nomeCompleto + '</option>';
                        });
                    }
                    
                    $('#id_referente').html(options);
                },
                error: function() {
                    alert('Errore durante il caricamento dei contatti');
                }
            });
            
            // Debug: mostra tutte le offerte disponibili
            console.log("Elenco completo offerte disponibili:");
            $('#id_offerta_fornitore option').each(function() {
                if($(this).val() !== "") {
                    console.log("Offerta ID: " + $(this).val() + 
                              ", Fornitore ID: " + $(this).attr('data-anagrafica') + 
                              ", Testo: " + $(this).text());
                }
            });
            
            // Filtra le offerte per il fornitore selezionato
            var count = 0;
            console.log("Filtrando offerte per fornitore ID: " + idAnagrafica);
            
            // SOLUZIONE: Conversione esplicita a numeri e utilizzo di attr() invece di data()
            var idAnagraficaInt = parseInt(idAnagrafica);
            
            $('#id_offerta_fornitore option').each(function() {
                var option = $(this);
                
                if (option.val() === "") {
                    option.show(); // Mostra sempre l'opzione vuota
                    return; // Salta al prossimo ciclo
                }
                
                // Ottieni l'attributo data-anagrafica direttamente come stringa
                var anagraficaAttr = option.attr('data-anagrafica');
                console.log("Offerta: " + option.text() + 
                          " - ID Anagrafica nell'attributo: [" + anagraficaAttr + "]" +
                          " - Tipo: " + typeof anagraficaAttr);
                
                // Confronta come numeri interi
                if (parseInt(anagraficaAttr) === idAnagraficaInt) {
                    console.log("✓ MATCH: ID fornitore corrispondente per offerta: " + option.text());
                    option.show();
                    count++;
                } else {
                    console.log("✗ NO MATCH: ID fornitore NON corrispondente per offerta: " + option.text());
                    option.hide();
                }
            });
            
            // Log per debugging
            console.log("Filtrate " + count + " offerte per il fornitore ID: " + idAnagrafica);
            
            // Reset della selezione
            $('#id_offerta_fornitore').val('').trigger('change');
        } else {
            $('#id_referente').html('<option value="">- Seleziona Referente -</option>');
            $('#id_offerta_fornitore option').show();
        }
    });

    // Validazione form
    $('#createOrdineForm').validate({
        rules: {
            data: {
                required: true
            },
            oggetto: {
                required: true,
                minlength: 3
            },
            id_anagrafica: {
                required: true
            }
        },
        messages: {
            data: {
                required: "Inserisci la data dell'ordine"
            },
            oggetto: {
                required: "Inserisci l'oggetto dell'ordine",
                minlength: "L'oggetto deve essere di almeno {0} caratteri"
            },
            id_anagrafica: {
                required: "Seleziona un fornitore"
            }
        },
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
        }
    });
    
    // Precarica i contatti se il fornitore è già selezionato
    if ($('#id_anagrafica').val()) {
        $('#id_anagrafica').trigger('change');
    }
});
</script>
<?= $this->endSection() ?> 
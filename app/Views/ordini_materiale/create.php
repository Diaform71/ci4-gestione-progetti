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
                <div class="card">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="crea_scadenza" id="crea_scadenza" value="1">
                            <label class="form-check-label font-weight-bold" for="crea_scadenza">
                                Crea automaticamente una scadenza per questo ordine
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="scadenza_details" style="display: none;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="titolo_scadenza">Titolo scadenza:</label>
                                <input type="text" class="form-control" id="titolo_scadenza" name="titolo_scadenza" 
                                       placeholder="Titolo della scadenza" value="Gestione ordine - #">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="data_scadenza">Data scadenza:</label>
                                <div class="input-group date" id="scadenza_datepicker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" 
                                           id="data_scadenza_display" 
                                           data-target="#scadenza_datepicker" data-toggle="datetimepicker"
                                           value="<?= date('d/m/Y', strtotime('+7 days')) ?>">
                                    <input type="hidden" name="data_scadenza" id="data_scadenza" 
                                           value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                    <div class="input-group-append" data-target="#scadenza_datepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="priorita_scadenza">Priorità:</label>
                                <select name="priorita_scadenza" id="priorita_scadenza" class="form-control">
                                    <option value="bassa">Bassa</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="stato_scadenza">Stato:</label>
                                <select name="stato_scadenza" id="stato_scadenza" class="form-control">
                                    <option value="da_iniziare" selected>Da iniziare</option>
                                    <option value="in_corso">In corso</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_utente_assegnato">Assegnata a:</label>
                                <select name="id_utente_assegnato" id="id_utente_assegnato" class="form-control select2">
                                    <option value="">- Seleziona un utente -</option>
                                    <?php 
                                    // Ottieni gli utenti attivi
                                    $utentiModel = new \App\Models\UtentiModel();
                                    $utenti = $utentiModel->where('attivo', 1)->findAll();
                                    foreach ($utenti as $utente): ?>
                                        <option value="<?= $utente['id'] ?>" 
                                            <?= session()->get('utente_id') == $utente['id'] ? 'selected' : '' ?>>
                                            <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="descrizione_scadenza">Descrizione:</label>
                                <textarea class="form-control" id="descrizione_scadenza" name="descrizione_scadenza" 
                                          rows="3">Gestire l'ordine di acquisto e la relativa consegna</textarea>
                            </div>
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
    $('#datepicker, #data_consegna_prevista_picker, #scadenza_datepicker').datetimepicker({
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
    
    $('#scadenza_datepicker').on('change.datetimepicker', function(e) {
        if (e.date) {
            $('#data_scadenza').val(e.date.format('YYYY-MM-DD'));
        } else {
            $('#data_scadenza').val('');
        }
    });

    // Inizializza Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Gestione checkbox creazione scadenza
    $('#crea_scadenza').on('change', function() {
        if ($(this).is(':checked')) {
            $('#scadenza_details').slideDown();
        } else {
            $('#scadenza_details').slideUp();
        }
    });
    
    // Aggiorna il titolo della scadenza quando cambia l'oggetto dell'ordine
    $('#oggetto').on('change keyup', function() {
        var oggetto = $(this).val();
        if (oggetto) {
            $('#titolo_scadenza').val('Gestione ordine - ' + oggetto);
        } else {
            $('#titolo_scadenza').val('Gestione ordine - #');
        }
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
            
            // Ricostruisci le opzioni delle offerte fornitore per il fornitore selezionato
            $('#id_offerta_fornitore').empty().append('<option value="">- Seleziona Offerta -</option>');
            
            // Carica le offerte per il fornitore selezionato
            var idAnagraficaInt = parseInt(idAnagrafica);
            var offerteTrovate = 0;
            
            <?php foreach ($offerteFornitore as $offerta): ?>
                if (idAnagraficaInt === <?= (int)$offerta['id_anagrafica'] ?>) {
                    $('#id_offerta_fornitore').append(
                        '<option value="<?= $offerta['id'] ?>" data-anagrafica="<?= $offerta['id_anagrafica'] ?>">' +
                        '<?= esc($offerta['numero']) ?> - <?= esc($offerta['oggetto']) ?> (<?= esc($offerta['nome_fornitore']) ?>)' +
                        '</option>'
                    );
                    offerteTrovate++;
                }
            <?php endforeach; ?>
            
            console.log("Aggiunte " + offerteTrovate + " offerte per il fornitore ID: " + idAnagrafica);
            
            // Aggiorna il Select2 dopo aver modificato le opzioni
            $('#id_offerta_fornitore').trigger('change.select2');
        } else {
            $('#id_referente').html('<option value="">- Seleziona Referente -</option>');
            
            // Ripristina tutte le offerte
            $('#id_offerta_fornitore').empty().append('<option value="">- Seleziona Offerta -</option>');
            
            <?php foreach ($offerteFornitore as $offerta): ?>
                $('#id_offerta_fornitore').append(
                    '<option value="<?= $offerta['id'] ?>" data-anagrafica="<?= $offerta['id_anagrafica'] ?>">' +
                    '<?= esc($offerta['numero']) ?> - <?= esc($offerta['oggetto']) ?> (<?= esc($offerta['nome_fornitore']) ?>)' +
                    '</option>'
                );
            <?php endforeach; ?>
            
            // Aggiorna il Select2 dopo aver modificato le opzioni
            $('#id_offerta_fornitore').trigger('change.select2');
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
            },
            // Regole per scadenza (solo se checkbox attiva)
            titolo_scadenza: {
                required: function() {
                    return $('#crea_scadenza').is(':checked');
                },
                minlength: 3
            },
            data_scadenza: {
                required: function() {
                    return $('#crea_scadenza').is(':checked');
                }
            },
            id_utente_assegnato: {
                required: function() {
                    return $('#crea_scadenza').is(':checked');
                }
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
            },
            titolo_scadenza: {
                required: "Inserisci il titolo della scadenza",
                minlength: "Il titolo deve essere di almeno {0} caratteri"
            },
            data_scadenza: {
                required: "Inserisci la data della scadenza"
            },
            id_utente_assegnato: {
                required: "Seleziona l'utente a cui assegnare la scadenza"
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
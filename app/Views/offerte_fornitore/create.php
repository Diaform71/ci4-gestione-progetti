<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Nuova Offerta Fornitore<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Nuova Offerta Fornitore<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('offerte-fornitore') ?>">Offerte Fornitore</a></li>
<li class="breadcrumb-item active">Nuova Offerta</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <form action="<?= base_url('offerte-fornitore/create') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dati Offerta</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="numero">Numero Offerta*</label>
                                <input type="text" class="form-control <?= session('errors.numero') ? 'is-invalid' : '' ?>" id="numero" name="numero" value="<?= old('numero') ?: '' ?>" placeholder="Compilazione automatica se vuoto">
                                <?php if (session('errors.numero')): ?>
                                    <div class="invalid-feedback"><?= session('errors.numero') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="data">Data*</label>
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
                                    <div class="invalid-feedback d-block">
                                        <?= session('errors.data') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="oggetto">Oggetto*</label>
                            <input type="text" class="form-control <?= session('errors.oggetto') ? 'is-invalid' : '' ?>" id="oggetto" name="oggetto" value="<?= old('oggetto') ?: '' ?>" required>
                            <?php if (session('errors.oggetto')): ?>
                                <div class="invalid-feedback"><?= session('errors.oggetto') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="descrizione">Descrizione</label>
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" id="descrizione" name="descrizione" rows="3"><?= old('descrizione') ?: '' ?></textarea>
                            <?php if (session('errors.descrizione')): ?>
                                <div class="invalid-feedback"><?= session('errors.descrizione') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_anagrafica">Fornitore*</label>
                                <select class="form-control select2 <?= session('errors.id_anagrafica') ? 'is-invalid' : '' ?>" id="id_anagrafica" name="id_anagrafica" required>
                                    <option value="">-- Seleziona Fornitore --</option>
                                    <?php foreach ($fornitori as $fornitore): ?>
                                        <option value="<?= $fornitore['id'] ?>" <?= (old('id_anagrafica') == $fornitore['id'] || (isset($richiestaData) && $richiestaData['id_anagrafica'] == $fornitore['id'])) ? 'selected' : '' ?>>
                                            <?= esc($fornitore['ragione_sociale']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_anagrafica')): ?>
                                    <div class="invalid-feedback"><?= session('errors.id_anagrafica') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id_referente">Referente</label>
                                <select class="form-control select2 <?= session('errors.id_referente') ? 'is-invalid' : '' ?>" id="id_referente" name="id_referente">
                                    <option value="">-- Seleziona Referente --</option>
                                    <!-- I contatti verranno caricati via AJAX -->
                                </select>
                                <?php if (session('errors.id_referente')): ?>
                                    <div class="invalid-feedback"><?= session('errors.id_referente') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_richiesta_offerta">Richiesta d'Offerta Collegata</label>
                                <select class="form-control select2 <?= session('errors.id_richiesta_offerta') ? 'is-invalid' : '' ?>" id="id_richiesta_offerta" name="id_richiesta_offerta">
                                    <option value="">-- Seleziona Richiesta d'Offerta --</option>
                                    <?php foreach ($richieste as $richiesta): ?>
                                        <option value="<?= $richiesta['id'] ?>" <?= (old('id_richiesta_offerta') == $richiesta['id'] || (isset($richiestaData) && $richiestaData['id'] == $richiesta['id'])) ? 'selected' : '' ?>>
                                            <?= esc($richiesta['numero']) ?> - <?= esc($richiesta['oggetto']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_richiesta_offerta')): ?>
                                    <div class="invalid-feedback"><?= session('errors.id_richiesta_offerta') ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Se selezionato, l'offerta verrà associata a questa richiesta e sarà possibile importare le voci.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id_progetto">Progetto</label>
                                <select class="form-control select2 <?= session('errors.id_progetto') ? 'is-invalid' : '' ?>" id="id_progetto" name="id_progetto">
                                    <option value="">-- Seleziona Progetto --</option>
                                    <?php foreach ($progetti as $progetto): ?>
                                        <option value="<?= $progetto['id'] ?>" <?= (old('id_progetto') == $progetto['id'] || (isset($richiestaData) && $richiestaData['id_progetto'] == $progetto['id'])) ? 'selected' : '' ?>>
                                            <?= esc($progetto['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.id_progetto')): ?>
                                    <div class="invalid-feedback"><?= session('errors.id_progetto') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="valuta">Valuta</label>
                                <select class="form-control select2 <?= session('errors.valuta') ? 'is-invalid' : '' ?>" id="valuta" name="valuta">
                                    <option value="EUR" <?= (old('valuta') == 'EUR' || empty(old('valuta'))) ? 'selected' : '' ?>>EUR - Euro</option>
                                    <option value="USD" <?= old('valuta') == 'USD' ? 'selected' : '' ?>>USD - Dollaro USA</option>
                                    <option value="GBP" <?= old('valuta') == 'GBP' ? 'selected' : '' ?>>GBP - Sterlina Britannica</option>
                                    <option value="CHF" <?= old('valuta') == 'CHF' ? 'selected' : '' ?>>CHF - Franco Svizzero</option>
                                </select>
                                <?php if (session('errors.valuta')): ?>
                                    <div class="invalid-feedback"><?= session('errors.valuta') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control <?= session('errors.note') ? 'is-invalid' : '' ?>" id="note" name="note" rows="3"><?= old('note') ?: '' ?></textarea>
                            <?php if (session('errors.note')): ?>
                                <div class="invalid-feedback"><?= session('errors.note') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Allegati -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Allegati</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="allegati">Carica Allegati</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="allegati" name="allegati[]" multiple>
                                <label class="custom-file-label" for="allegati">Scegli file...</label>
                            </div>
                            <small class="form-text text-muted">Puoi selezionare più file da caricare.</small>
                        </div>
                        <div class="form-group">
                            <label for="descrizione_allegato">Descrizione Allegati</label>
                            <textarea class="form-control" id="descrizione_allegato" name="descrizione_allegato" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salva Offerta</button>
                <a href="<?= base_url('offerte-fornitore') ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inizializza Select2
        $('.select2').select2({
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
        
        // File input
        $('input[type="file"]').on('change', function() {
            var fileNames = [];
            for (var i = 0; i < $(this)[0].files.length; i++) {
                fileNames.push($(this)[0].files[i].name);
            }
            $(this).next('.custom-file-label').html(fileNames.join(', ') || 'Scegli file...');
        });

        // Carica contatti al cambio del fornitore
        $('#id_anagrafica').on('change', function() {
            var idAnagrafica = $(this).val();
            if (idAnagrafica) {
                // Carica i contatti
                $.ajax({
                    url: '<?= base_url('richieste-offerta/get-contatti-by-anagrafica') ?>',
                    type: 'POST',
                    data: {
                        'id_anagrafica': idAnagrafica,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        var select = $('#id_referente');
                        select.empty();
                        select.append('<option value="">-- Seleziona Referente --</option>');

                        if (response.success && response.contatti.length > 0) {
                            $.each(response.contatti, function(i, contatto) {
                                var nomeCompleto = contatto.nome + ' ' + contatto.cognome;
                                if (contatto.email) {
                                    nomeCompleto += ' (' + contatto.email + ')';
                                }
                                select.append('<option value="' + contatto.id + '">' + nomeCompleto + '</option>');
                            });
                        }
                    },
                    error: function() {
                        alert('Errore durante il caricamento dei contatti');
                    }
                });
                
                // Carica le richieste d'offerta filtrate per fornitore
                $.ajax({
                    url: '<?= base_url('richieste-offerta/get-richieste-by-fornitore') ?>',
                    type: 'POST',
                    data: {
                        'id_anagrafica': idAnagrafica,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        var select = $('#id_richiesta_offerta');
                        select.empty();
                        select.append('<option value="">-- Seleziona Richiesta d\'Offerta --</option>');

                        if (response.success && response.richieste.length > 0) {
                            $.each(response.richieste, function(i, richiesta) {
                                var testo = richiesta.numero;
                                if (richiesta.oggetto) {
                                    testo += ' - ' + richiesta.oggetto;
                                }
                                select.append('<option value="' + richiesta.id + '">' + testo + '</option>');
                            });
                        }
                    },
                    error: function() {
                        alert('Errore durante il caricamento delle richieste d\'offerta');
                    }
                });
            } else {
                $('#id_referente').empty().append('<option value="">-- Seleziona Referente --</option>');
                $('#id_richiesta_offerta').empty().append('<option value="">-- Seleziona Richiesta d\'Offerta --</option>');
            }
        });

        // Quando viene selezionata una richiesta d'offerta, carica il fornitore e il referente
        $('#id_richiesta_offerta').on('change', function() {
            var idRichiesta = $(this).val();
            if (idRichiesta) {
                $.ajax({
                    url: '<?= base_url('richieste-offerta') ?>/' + idRichiesta,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.richiesta) {
                            // Imposta il fornitore
                            // $('#id_anagrafica').val(response.richiesta.id_anagrafica).trigger('change');
                            
                            // Dopo aver caricato i contatti, seleziona il referente corretto
                            setTimeout(function() {
                                $('#id_referente').val(response.richiesta.id_referente);
                                $('#id_referente').trigger('change');
                            }, 500);
                            
                            // Imposta il progetto se presente
                            if (response.richiesta.id_progetto) {
                                $('#id_progetto').val(response.richiesta.id_progetto).trigger('change');
                            }
                        }
                    },
                    error: function() {
                        console.error('Errore durante il caricamento dei dati della richiesta');
                    }
                });
            }
        });

        // Precarica i contatti se il fornitore è già selezionato
        if ($('#id_anagrafica').val()) {
            $('#id_anagrafica').trigger('change');
        }
    });
</script>
<?= $this->endSection() ?>
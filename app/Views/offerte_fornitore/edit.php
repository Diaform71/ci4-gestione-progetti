<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Modifica Offerta Fornitore<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modifica Offerta Fornitore<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('offerte-fornitore') ?>">Offerte Fornitore</a></li>
<li class="breadcrumb-item active">Modifica Offerta</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <form action="<?= base_url('offerte-fornitore/update/' . $offerta['id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

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
                                <input type="text" class="form-control <?= session('errors.numero') ? 'is-invalid' : '' ?>" id="numero" name="numero" value="<?= old('numero', $offerta['numero']) ?>" placeholder="Compilazione automatica se vuoto">
                                <?php if (session('errors.numero')): ?>
                                    <div class="invalid-feedback"><?= session('errors.numero') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="data">Data*</label>
                                <div class="input-group date" id="datepicker" data-target-input="nearest">
                                    <?php 
                                    $formatted_date = old('data', $offerta['data']) 
                                        ? date('d/m/Y', strtotime(old('data', $offerta['data']))) 
                                        : date('d/m/Y');
                                    ?>
                                    <input type="text" class="form-control datetimepicker-input <?= session('errors.data') ? 'is-invalid' : '' ?>" 
                                           id="data_display" 
                                           data-target="#datepicker" data-toggle="datetimepicker"
                                           value="<?= $formatted_date ?>">
                                    <input type="hidden" name="data" id="data" 
                                           value="<?= old('data', $offerta['data']) ?: date('Y-m-d') ?>">
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
                            <input type="text" class="form-control <?= session('errors.oggetto') ? 'is-invalid' : '' ?>" id="oggetto" name="oggetto" value="<?= old('oggetto', $offerta['oggetto']) ?>" required>
                            <?php if (session('errors.oggetto')): ?>
                                <div class="invalid-feedback"><?= session('errors.oggetto') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="descrizione">Descrizione</label>
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" id="descrizione" name="descrizione" rows="3"><?= old('descrizione', $offerta['descrizione']) ?></textarea>
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
                                        <option value="<?= $fornitore['id'] ?>" <?= (old('id_anagrafica', $offerta['id_anagrafica']) == $fornitore['id']) ? 'selected' : '' ?>>
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
                                    <?php if(!empty($contatti)): ?>
                                        <?php foreach($contatti as $contatto): ?>
                                            <option value="<?= $contatto['id_contatto'] ?>" <?= (old('id_referente', $offerta['id_referente']) == $contatto['id_contatto']) ? 'selected' : '' ?>>
                                                <?= esc($contatto['nome']) ?> <?= esc($contatto['cognome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                                        <option value="<?= $richiesta['id'] ?>" <?= (old('id_richiesta_offerta', $offerta['id_richiesta_offerta']) == $richiesta['id']) ? 'selected' : '' ?>>
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
                                        <option value="<?= $progetto['id'] ?>" <?= (old('id_progetto', $offerta['id_progetto']) == $progetto['id']) ? 'selected' : '' ?>>
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
                                    <option value="EUR" <?= (old('valuta', $offerta['valuta']) == 'EUR' || empty(old('valuta', $offerta['valuta']))) ? 'selected' : '' ?>>EUR - Euro</option>
                                    <option value="USD" <?= old('valuta', $offerta['valuta']) == 'USD' ? 'selected' : '' ?>>USD - Dollaro USA</option>
                                    <option value="GBP" <?= old('valuta', $offerta['valuta']) == 'GBP' ? 'selected' : '' ?>>GBP - Sterlina Britannica</option>
                                    <option value="CHF" <?= old('valuta', $offerta['valuta']) == 'CHF' ? 'selected' : '' ?>>CHF - Franco Svizzero</option>
                                </select>
                                <?php if (session('errors.valuta')): ?>
                                    <div class="invalid-feedback"><?= session('errors.valuta') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control <?= session('errors.note') ? 'is-invalid' : '' ?>" id="note" name="note" rows="3"><?= old('note', $offerta['note']) ?></textarea>
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
                        <?php if (!empty($allegati)): ?>
                            <div class="mb-3">
                                <h6>Allegati esistenti:</h6>
                                <ul class="list-group">
                                    <?php foreach ($allegati as $allegato): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="<?= base_url('uploads/' . $allegato['file_path']) ?>" target="_blank">
                                            <?= esc($allegato['nome_originale']) ?>
                                        </a>
                                        <a href="<?= base_url('offerte-fornitore/delete-allegato/' . $allegato['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo allegato?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="allegati">Carica Nuovi Allegati</label>
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
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Aggiorna Offerta</button>
                <a href="<?= base_url('offerte-fornitore/view/' . $offerta['id']) ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a>
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
        bsCustomFileInput.init();
        
        // Carica contatti al cambio del fornitore
        $('#id_anagrafica').on('change', function() {
            var idAnagrafica = $(this).val();
            var idReferente = '<?= old('id_referente', $offerta['id_referente']) ?>';
            if (idAnagrafica) {
                // Carica i contatti
                $.ajax({
                    url: '<?= base_url('anagrafiche/get-contatti') ?>/' + idAnagrafica,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#id_referente').empty().append('<option value="">-- Seleziona Referente --</option>');
                        $.each(data, function(key, value) {
                            var selected = (value.id_contatto == idReferente) ? 'selected' : '';
                            $('#id_referente').append('<option value="' + value.id_contatto + '" ' + selected + '>' + value.nome + ' ' + value.cognome + '</option>');
                        });
                    },
                    error: function() {
                        console.error('Errore nel caricamento dei contatti');
                    }
                });
            } else {
                $('#id_referente').empty().append('<option value="">-- Seleziona Referente --</option>');
            }
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Modifica Progetto<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modifica Progetto<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('progetti') ?>">Progetti</a></li>
<li class="breadcrumb-item active">Modifica Progetto</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifica dati del progetto: <?= esc($progetto['nome']) ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($validation) && $validation->getErrors()) : ?>
                        <div class="alert alert-danger">
                            <?= $validation->listErrors() ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('progetti/update/' . $progetto['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <!-- Campo nascosto per id_creato_da -->
                        <input type="hidden" name="id_creato_da" value="<?= $progetto['id_creato_da'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome Progetto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                        value="<?= old('nome', $progetto['nome']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_anagrafica">Cliente</label>
                                    <select class="form-control" id="id_anagrafica" name="id_anagrafica">
                                        <option value="">-- Seleziona Cliente --</option>
                                        <?php foreach ($anagrafiche as $anagrafica) : ?>
                                            <option value="<?= $anagrafica['id'] ?>" 
                                                <?= old('id_anagrafica', $progetto['id_anagrafica']) == $anagrafica['id'] ? 'selected' : '' ?>>
                                                <?= esc($anagrafica['ragione_sociale']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_progetto_padre">Progetto Padre</label>
                                    <select class="form-control" id="id_progetto_padre" name="id_progetto_padre">
                                        <option value="">-- Progetto Principale --</option>
                                        <?php if (!empty($progetti_disponibili)) : ?>
                                            <?php foreach ($progetti_disponibili as $progettoPadre) : ?>
                                                <option value="<?= $progettoPadre['id'] ?>" 
                                                    <?= old('id_progetto_padre', $progetto['id_progetto_padre']) == $progettoPadre['id'] ? 'selected' : '' ?>>
                                                    <?= esc($progettoPadre['nome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <option value="" disabled>Nessun progetto disponibile come padre</option>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        Seleziona un progetto padre se questo è un sottoprogetto. Lascia vuoto se è un progetto principale.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descrizione">Descrizione</label>
                            <textarea class="form-control" id="descrizione" name="descrizione" 
                                rows="3"><?= old('descrizione', $progetto['descrizione']) ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fase_kanban">Fase Kanban <span class="text-danger">*</span></label>
                                    <select class="form-control" id="fase_kanban" name="fase_kanban" required>
                                        <option value="backlog" <?= old('fase_kanban', $progetto['fase_kanban']) == 'backlog' ? 'selected' : '' ?>>Backlog</option>
                                        <option value="da_iniziare" <?= old('fase_kanban', $progetto['fase_kanban']) == 'da_iniziare' ? 'selected' : '' ?>>Da Iniziare</option>
                                        <option value="in_corso" <?= old('fase_kanban', $progetto['fase_kanban']) == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                        <option value="in_revisione" <?= old('fase_kanban', $progetto['fase_kanban']) == 'in_revisione' ? 'selected' : '' ?>>In Revisione</option>
                                        <option value="completato" <?= old('fase_kanban', $progetto['fase_kanban']) == 'completato' ? 'selected' : '' ?>>Completato</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stato">Stato <span class="text-danger">*</span></label>
                                    <select class="form-control" id="stato" name="stato" required>
                                        <option value="in_corso" <?= old('stato', $progetto['stato']) == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                        <option value="completato" <?= old('stato', $progetto['stato']) == 'completato' ? 'selected' : '' ?>>Completato</option>
                                        <option value="sospeso" <?= old('stato', $progetto['stato']) == 'sospeso' ? 'selected' : '' ?>>Sospeso</option>
                                        <option value="annullato" <?= old('stato', $progetto['stato']) == 'annullato' ? 'selected' : '' ?>>Annullato</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_inizio">Data Inizio</label>
                                    <div class="input-group date" id="data_inizio_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" id="data_inizio_display" 
                                            data-target="#data_inizio_picker">
                                        <input type="hidden" name="data_inizio" id="data_inizio" 
                                            value="<?= old('data_inizio', $progetto['data_inizio']) ?>">
                                        <div class="input-group-append" data-target="#data_inizio_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_scadenza">Data Scadenza</label>
                                    <div class="input-group date" id="data_scadenza_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" id="data_scadenza_display" 
                                            data-target="#data_scadenza_picker">
                                        <input type="hidden" name="data_scadenza" id="data_scadenza" 
                                            value="<?= old('data_scadenza', $progetto['data_scadenza']) ?>">
                                        <div class="input-group-append" data-target="#data_scadenza_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_fine">Data Fine</label>
                                    <div class="input-group date" id="data_fine_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" id="data_fine_display" 
                                            data-target="#data_fine_picker">
                                        <input type="hidden" name="data_fine" id="data_fine" 
                                            value="<?= old('data_fine', $progetto['data_fine']) ?>">
                                        <div class="input-group-append" data-target="#data_fine_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_responsabile">Responsabile</label>
                                    <select class="form-control" id="id_responsabile" name="id_responsabile">
                                        <option value="">-- Seleziona Responsabile --</option>
                                        <?php foreach ($utenti as $utente) : ?>
                                            <option value="<?= $utente['id'] ?>" 
                                                <?= old('id_responsabile', $progetto['id_responsabile']) == $utente['id'] ? 'selected' : '' ?>>
                                                <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?> (<?= esc($utente['username']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priorita">Priorità <span class="text-danger">*</span></label>
                                    <select class="form-control" id="priorita" name="priorita" required>
                                        <option value="bassa" <?= old('priorita', $progetto['priorita']) == 'bassa' ? 'selected' : '' ?>>Bassa</option>
                                        <option value="media" <?= old('priorita', $progetto['priorita']) == 'media' ? 'selected' : '' ?>>Media</option>
                                        <option value="alta" <?= old('priorita', $progetto['priorita']) == 'alta' ? 'selected' : '' ?>>Alta</option>
                                        <option value="critica" <?= old('priorita', $progetto['priorita']) == 'critica' ? 'selected' : '' ?>>Critica</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="budget">Budget (€)</label>
                            <input type="number" class="form-control" id="budget" name="budget" step="0.01" min="0" 
                                value="<?= old('budget', $progetto['budget']) ?>">
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Aggiorna Progetto
                                </button>
                                <a href="<?= base_url('progetti/' . $progetto['id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annulla
                                </a>
                                <a href="<?= base_url('progetti/toggle-attivo/' . $progetto['id']) ?>" class="btn <?= $progetto['attivo'] ? 'btn-warning' : 'btn-success' ?>">
                                    <i class="fas <?= $progetto['attivo'] ? 'fa-ban' : 'fa-check' ?>"></i> 
                                    <?= $progetto['attivo'] ? 'Disattiva' : 'Attiva' ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<style>
    .form-group label {
        font-weight: 600;
    }
    
    .text-danger {
        color: #dc3545;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/locale/it.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('plugins/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
$(document).ready(function() {
    // Configurazione comune per i datepicker
    const datePickerConfig = {
        locale: 'it',
        format: 'L',
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
        },
        buttons: {
            showToday: true,
            showClear: true,
            showClose: true
        }
    };

    // Funzione per inizializzare un datepicker con una data esistente
    function initDatePicker(pickerId, hiddenInputId) {
        const $picker = $(pickerId);
        const $hidden = $(hiddenInputId);
        const $display = $picker.find('.datetimepicker-input');
        const existingDate = $hidden.val();

        // Debug dettagliato
        console.log('Inizializzazione datepicker:', {
            pickerId: pickerId,
            hiddenInputId: hiddenInputId,
            existingDate: existingDate,
            displayValue: $display.val()
        });

        // Configura il datepicker
        $picker.datetimepicker(datePickerConfig);
        
        // Se esiste una data valida, inizializza il picker con quella data
        if (existingDate && existingDate !== '0000-00-00') {
            const momentDate = moment(existingDate);
            if (momentDate.isValid()) {
                $picker.datetimepicker('date', momentDate);
                console.log('Data inizializzata:', {
                    field: hiddenInputId,
                    originalDate: existingDate,
                    momentDate: momentDate.format('YYYY-MM-DD'),
                    isValid: momentDate.isValid(),
                    displayValue: $display.val()
                });
            }
        }
        
        // Gestione del cambio data
        $picker.on('change.datetimepicker', function(e) {
            console.log('Evento change triggered per ' + pickerId, {
                hasDate: !!e.date,
                date: e.date ? e.date.format('YYYY-MM-DD') : null,
                previousValue: $hidden.val(),
                displayValue: $display.val()
            });

            if (e.date) {
                const formattedDate = e.date.format('YYYY-MM-DD');
                $hidden.val(formattedDate);
                console.log('Nuova data impostata per ' + hiddenInputId, {
                    formattedDate: formattedDate,
                    hiddenValue: $hidden.val(),
                    displayValue: $display.val()
                });
            } else {
                $hidden.val('');
                console.log('Data rimossa per ' + hiddenInputId);
            }
        });

        // Gestione del clear
        $picker.on('clear.datetimepicker', function(e) {
            $hidden.val('');
            console.log('Data cancellata per ' + hiddenInputId);
        });

        // Gestione della chiusura
        $picker.on('hide.datetimepicker', function(e) {
            const currentDate = $picker.datetimepicker('date');
            if (currentDate) {
                const formattedDate = currentDate.format('YYYY-MM-DD');
                $hidden.val(formattedDate);
                console.log('Data confermata alla chiusura per ' + hiddenInputId, {
                    formattedDate: formattedDate,
                    hiddenValue: $hidden.val(),
                    displayValue: $display.val()
                });
            }
        });
    }

    // Inizializzazione dei datepicker con le date esistenti
    initDatePicker('#data_inizio_picker', '#data_inizio');
    initDatePicker('#data_scadenza_picker', '#data_scadenza');
    initDatePicker('#data_fine_picker', '#data_fine');

    // Gestione dello stato "completato"
    $('#stato').on('change', function() {
        if ($(this).val() === 'completato' && !$('#data_fine').val()) {
            // Imposta la data fine come oggi
            const oggi = moment();
            $('#data_fine_picker').datetimepicker('date', oggi);
            $('#data_fine').val(oggi.format('YYYY-MM-DD'));
            console.log('Data fine impostata automaticamente:', oggi.format('YYYY-MM-DD'));
            
            // Mostra un messaggio
            Swal.fire({
                icon: 'info',
                title: 'Data Fine Aggiornata',
                text: 'La data di fine progetto è stata impostata automaticamente a oggi.',
                confirmButtonText: 'Ok'
            });
        }
    });

    // Debug dei valori al submit del form
    $('form').on('submit', function(e) {
        // e.preventDefault(); // Decommentare per debug
        console.log('Valori dei campi al submit:', {
            data_inizio: {
                hidden: $('#data_inizio').val(),
                display: $('#data_inizio_display').val()
            },
            data_scadenza: {
                hidden: $('#data_scadenza').val(),
                display: $('#data_scadenza_display').val()
            },
            data_fine: {
                hidden: $('#data_fine').val(),
                display: $('#data_fine_display').val()
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 
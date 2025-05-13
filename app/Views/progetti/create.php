<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Nuovo Progetto<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Nuovo Progetto<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('progetti') ?>">Progetti</a></li>
<li class="breadcrumb-item active">Nuovo Progetto</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inserisci i dati del nuovo progetto</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('progetti/create') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <!-- Campo nascosto per id_creato_da -->
                        <input type="hidden" name="id_creato_da" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome Progetto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= session('errors.nome') ? 'is-invalid' : '' ?>" id="nome" name="nome" value="<?= old('nome') ?>">
                                    <?php if (session('errors.nome')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.nome') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_anagrafica">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-control <?= session('errors.id_anagrafica') ? 'is-invalid' : '' ?>" id="id_anagrafica" name="id_anagrafica">
                                        <option value="">-- Seleziona Cliente --</option>
                                        <?php foreach ($anagrafiche as $anagrafica) : ?>
                                            <option value="<?= $anagrafica['id'] ?>" <?= old('id_anagrafica') == $anagrafica['id'] ? 'selected' : '' ?>>
                                                <?= esc($anagrafica['ragione_sociale']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_anagrafica')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_anagrafica') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descrizione">Descrizione</label>
                            <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" id="descrizione" name="descrizione" rows="3"><?= old('descrizione') ?></textarea>
                            <?php if (session('errors.descrizione')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.descrizione') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fase_kanban">Fase Kanban <span class="text-danger">*</span></label>
                                    <select class="form-control <?= session('errors.fase_kanban') ? 'is-invalid' : '' ?>" id="fase_kanban" name="fase_kanban">
                                        <option value="backlog" <?= old('fase_kanban') == 'backlog' ? 'selected' : '' ?>>Backlog</option>
                                        <option value="da_iniziare" <?= old('fase_kanban') == 'da_iniziare' ? 'selected' : '' ?>>Da Iniziare</option>
                                        <option value="in_corso" <?= old('fase_kanban') == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                        <option value="in_revisione" <?= old('fase_kanban') == 'in_revisione' ? 'selected' : '' ?>>In Revisione</option>
                                        <option value="completato" <?= old('fase_kanban') == 'completato' ? 'selected' : '' ?>>Completato</option>
                                    </select>
                                    <?php if (session('errors.fase_kanban')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.fase_kanban') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stato">Stato <span class="text-danger">*</span></label>
                                    <select class="form-control <?= session('errors.stato') ? 'is-invalid' : '' ?>" id="stato" name="stato">
                                        <option value="in_corso" <?= old('stato') == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                        <option value="completato" <?= old('stato') == 'completato' ? 'selected' : '' ?>>Completato</option>
                                        <option value="sospeso" <?= old('stato') == 'sospeso' ? 'selected' : '' ?>>Sospeso</option>
                                        <option value="annullato" <?= old('stato') == 'annullato' ? 'selected' : '' ?>>Annullato</option>
                                    </select>
                                    <?php if (session('errors.stato')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.stato') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_inizio">Data Inizio</label>
                                    <div class="input-group date" id="data_inizio_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input <?= session('errors.data_inizio') ? 'is-invalid' : '' ?>" id="data_inizio_display" 
                                            data-target="#data_inizio_picker" data-toggle="datetimepicker"
                                            value="<?= old('data_inizio') ? date('d/m/Y', strtotime(old('data_inizio'))) : date('d/m/Y') ?>">
                                        <input type="hidden" name="data_inizio" id="data_inizio" 
                                            value="<?= old('data_inizio') ?: date('Y-m-d') ?>">
                                        <div class="input-group-append" data-target="#data_inizio_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <?php if (session('errors.data_inizio')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= session('errors.data_inizio') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_scadenza">Data Scadenza</label>
                                    <div class="input-group date" id="data_scadenza_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input <?= session('errors.data_scadenza') ? 'is-invalid' : '' ?>" id="data_scadenza_display" 
                                            data-target="#data_scadenza_picker" data-toggle="datetimepicker"
                                            value="<?= old('data_scadenza') ? date('d/m/Y', strtotime(old('data_scadenza'))) : '' ?>">
                                        <input type="hidden" name="data_scadenza" id="data_scadenza" 
                                            value="<?= old('data_scadenza') ?>">
                                        <div class="input-group-append" data-target="#data_scadenza_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <?php if (session('errors.data_scadenza')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= session('errors.data_scadenza') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_fine">Data Fine</label>
                                    <div class="input-group date" id="data_fine_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input <?= session('errors.data_fine') ? 'is-invalid' : '' ?>" id="data_fine_display" 
                                            data-target="#data_fine_picker" data-toggle="datetimepicker"
                                            value="<?= old('data_fine') ? date('d/m/Y', strtotime(old('data_fine'))) : '' ?>">
                                        <input type="hidden" name="data_fine" id="data_fine" 
                                            value="<?= old('data_fine') ?>">
                                        <div class="input-group-append" data-target="#data_fine_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <?php if (session('errors.data_fine')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= session('errors.data_fine') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_responsabile">Responsabile</label>
                                    <select class="form-control <?= session('errors.id_responsabile') ? 'is-invalid' : '' ?>" id="id_responsabile" name="id_responsabile">
                                        <option value="">-- Seleziona Responsabile --</option>
                                        <?php foreach ($utenti as $utente) : ?>
                                            <option value="<?= $utente['id'] ?>" <?= old('id_responsabile') == $utente['id'] ? 'selected' : '' ?>>
                                                <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?> (<?= esc($utente['username']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_responsabile')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_responsabile') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priorita">Priorità <span class="text-danger">*</span></label>
                                    <select class="form-control <?= session('errors.priorita') ? 'is-invalid' : '' ?>" id="priorita" name="priorita">
                                        <option value="bassa" <?= old('priorita') == 'bassa' ? 'selected' : '' ?>>Bassa</option>
                                        <option value="media" <?= old('priorita') == 'media' || old('priorita') == '' ? 'selected' : '' ?>>Media</option>
                                        <option value="alta" <?= old('priorita') == 'alta' ? 'selected' : '' ?>>Alta</option>
                                        <option value="critica" <?= old('priorita') == 'critica' ? 'selected' : '' ?>>Critica</option>
                                    </select>
                                    <?php if (session('errors.priorita')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.priorita') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_progetto_padre">Progetto Padre</label>
                                    <select class="form-control <?= session('errors.id_progetto_padre') ? 'is-invalid' : '' ?>" id="id_progetto_padre" name="id_progetto_padre">
                                        <option value="">-- Seleziona Progetto Padre --</option>
                                        <?php foreach ($progetti_disponibili as $progetto) : ?>
                                            <option value="<?= $progetto['id'] ?>" <?= old('id_progetto_padre') == $progetto['id'] ? 'selected' : '' ?>>
                                                <?= esc($progetto['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_progetto_padre')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_progetto_padre') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="budget">Budget (€)</label>
                                    <input type="number" class="form-control <?= session('errors.budget') ? 'is-invalid' : '' ?>" id="budget" name="budget" step="0.01" min="0" value="<?= old('budget') ?>">
                                    <?php if (session('errors.budget')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.budget') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva Progetto
                                </button>
                                <a href="<?= base_url('progetti') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annulla
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
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inizializza select2 per una migliore UX nelle selezioni
        $('select').select2({
            theme: 'bootstrap4',
        });
        
        // Inizializzazione datepicker per le date
        $('#data_inizio_picker, #data_scadenza_picker, #data_fine_picker').datetimepicker({
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
        
        // Aggiorna i campi nascosti con i valori formattati quando cambiano i datepicker
        $('#data_inizio_picker').on('change.datetimepicker', function(e) {
            if (e.date) {
                $('#data_inizio').val(e.date.format('YYYY-MM-DD'));
                
                // Se la data di scadenza non è impostata, aggiungi 30 giorni come default
                if (!$('#data_scadenza').val()) {
                    const dataScadenza = moment(e.date).add(30, 'days');
                    $('#data_scadenza_picker').datetimepicker('date', dataScadenza);
                    $('#data_scadenza').val(dataScadenza.format('YYYY-MM-DD'));
                }
            } else {
                $('#data_inizio').val('');
            }
        });
        
        $('#data_scadenza_picker').on('change.datetimepicker', function(e) {
            if (e.date) {
                $('#data_scadenza').val(e.date.format('YYYY-MM-DD'));
            } else {
                $('#data_scadenza').val('');
            }
        });
        
        $('#data_fine_picker').on('change.datetimepicker', function(e) {
            if (e.date) {
                $('#data_fine').val(e.date.format('YYYY-MM-DD'));
            } else {
                $('#data_fine').val('');
            }
        });
        
        // Se lo stato è "completato", imposta la data di fine a oggi se non impostata
        $('#stato').change(function() {
            if ($(this).val() === 'completato' && !$('#data_fine').val()) {
                const oggi = moment();
                $('#data_fine_picker').datetimepicker('date', oggi);
                $('#data_fine').val(oggi.format('YYYY-MM-DD'));
                
                // Mostra un messaggio
                Swal.fire({
                    icon: 'info',
                    title: 'Data Fine Aggiornata',
                    text: 'La data di fine progetto è stata impostata automaticamente a oggi.',
                    confirmButtonText: 'Ok'
                });
            }
        });
    });
</script>
<?= $this->endSection() ?> 
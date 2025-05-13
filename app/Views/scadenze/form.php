<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Scadenza' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Scadenza' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('scadenze') ?>">Scadenze</a></li>
<li class="breadcrumb-item active"><?= isset($scadenza) ? 'Modifica' : 'Nuova' ?></li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Bootstrap DatePicker CSS -->
<link rel="stylesheet" href="<?= base_url('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
<!-- Select2 CSS -->
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= isset($scadenza) ? 'Modifica' : 'Nuova' ?> Scadenza</h3>
    </div>
    
    <form action="<?= base_url('scadenze/salva') ?>" method="post">
        <?= csrf_field() ?>
        
        <!-- ID scadenza (nascosto) -->
        <?php if (isset($scadenza)): ?>
            <input type="hidden" name="id" value="<?= $scadenza['id'] ?>">
        <?php endif; ?>
        
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="titolo">Titolo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titolo" name="titolo" value="<?= isset($scadenza) ? esc($scadenza['titolo']) : old('titolo') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="priorita">Priorità <span class="text-danger">*</span></label>
                        <select class="form-control" id="priorita" name="priorita" required>
                            <option value="">-- Seleziona --</option>
                            <option value="bassa" <?= (isset($scadenza) && $scadenza['priorita'] == 'bassa') || old('priorita') == 'bassa' ? 'selected' : '' ?>>Bassa</option>
                            <option value="media" <?= (isset($scadenza) && $scadenza['priorita'] == 'media') || old('priorita') == 'media' ? 'selected' : '' ?>>Media</option>
                            <option value="alta" <?= (isset($scadenza) && $scadenza['priorita'] == 'alta') || old('priorita') == 'alta' ? 'selected' : '' ?>>Alta</option>
                            <option value="urgente" <?= (isset($scadenza) && $scadenza['priorita'] == 'urgente') || old('priorita') == 'urgente' ? 'selected' : '' ?>>Urgente</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="stato">Stato <span class="text-danger">*</span></label>
                        <select class="form-control" id="stato" name="stato" required>
                            <option value="">-- Seleziona --</option>
                            <option value="da_iniziare" <?= (isset($scadenza) && $scadenza['stato'] == 'da_iniziare') || old('stato') == 'da_iniziare' ? 'selected' : '' ?>>Da iniziare</option>
                            <option value="in_corso" <?= (isset($scadenza) && $scadenza['stato'] == 'in_corso') || old('stato') == 'in_corso' ? 'selected' : '' ?>>In corso</option>
                            <option value="completata" <?= (isset($scadenza) && $scadenza['stato'] == 'completata') || old('stato') == 'completata' ? 'selected' : '' ?>>Completata</option>
                            <option value="annullata" <?= (isset($scadenza) && $scadenza['stato'] == 'annullata') || old('stato') == 'annullata' ? 'selected' : '' ?>>Annullata</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="descrizione">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"><?= isset($scadenza) ? esc($scadenza['descrizione']) : old('descrizione') ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_progetto">Progetto</label>
                        <select class="form-control select2" id="id_progetto" name="id_progetto">
                            <option value="">-- Nessun progetto --</option>
                            <?php foreach ($progetti as $progetto): ?>
                                <option value="<?= $progetto['id'] ?>" <?= (isset($scadenza) && $scadenza['id_progetto'] == $progetto['id']) || old('id_progetto') == $progetto['id'] ? 'selected' : '' ?>>
                                    <?= esc($progetto['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_attivita">Attività</label>
                        <select class="form-control select2" id="id_attivita" name="id_attivita">
                            <option value="">-- Nessuna attività --</option>
                            <?php foreach ($attivita as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= (isset($scadenza) && $scadenza['id_attivita'] == $a['id']) || old('id_attivita') == $a['id'] ? 'selected' : '' ?>>
                                    <?= esc($a['titolo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_utente_assegnato">Assegnata a <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="id_utente_assegnato" name="id_utente_assegnato" required>
                            <option value="">-- Seleziona --</option>
                            <?php foreach ($utenti as $utente): ?>
                                <option value="<?= $utente['id'] ?>" <?= (isset($scadenza) && $scadenza['id_utente_assegnato'] == $utente['id']) || old('id_utente_assegnato') == $utente['id'] ? 'selected' : '' ?>>
                                    <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_scadenza">Data Scadenza <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker" id="data_scadenza" name="data_scadenza" value="<?= isset($scadenza) ? formatDateToItalian($scadenza['data_scadenza']) : old('data_scadenza') ?>" required>
                            <div class="input-group-append">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_promemoria">Data Promemoria</label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker" id="data_promemoria" name="data_promemoria" value="<?= isset($scadenza) && $scadenza['data_promemoria'] ? formatDateToItalian($scadenza['data_promemoria']) : old('data_promemoria') ?>">
                            <div class="input-group-append">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salva</button>
            <a href="<?= base_url('scadenze') ?>" class="btn btn-default">Annulla</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Bootstrap DatePicker JS -->
<script src="<?= base_url('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('plugins/bootstrap-datepicker/bootstrap-datepicker-it.min.js') ?>"></script>
<!-- Select2 JS -->
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        // Inizializzazione Select2
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        // Inizializzazione DatePicker
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'it',
            autoclose: true,
            todayHighlight: true
        });
        
        // Aggiornamento attività quando cambia il progetto
        $('#id_progetto').change(function() {
            var progettoId = $(this).val();
            if (progettoId) {
                // Aggiorna il selettore di attività 
                $('#id_attivita').empty().append('<option value="">-- Caricamento --</option>');
                
                $.ajax({
                    url: '<?= base_url('attivita/per-progetto') ?>/' + progettoId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#id_attivita').empty().append('<option value="">-- Nessuna attività --</option>');
                        
                        if (response.attivita && response.attivita.length > 0) {
                            $.each(response.attivita, function(i, attivita) {
                                $('#id_attivita').append('<option value="' + attivita.id + '">' + attivita.titolo + '</option>');
                            });
                        }
                    },
                    error: function() {
                        $('#id_attivita').empty().append('<option value="">-- Errore di caricamento --</option>');
                    }
                });
            } else {
                $('#id_attivita').empty().append('<option value="">-- Nessuna attività --</option>');
            }
        });
    });
</script>
<?= $this->endSection() ?> 
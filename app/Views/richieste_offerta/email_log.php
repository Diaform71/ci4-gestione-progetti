<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Storico Email - RDO #<?= $richiesta['numero'] ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>Storico Email - Richiesta d'Offerta #<?= $richiesta['numero'] ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta') ?>">Richieste d'Offerta</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta/' . $richiesta['id']) ?>">RDO #<?= $richiesta['numero'] ?></a></li>
<li class="breadcrumb-item active">Storico Email</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Storico Email</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('richieste-offerta/' . $richiesta['id']) ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Torna alla richiesta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Ora</th>
                                    <th>Destinatario</th>
                                    <th>Oggetto</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($emails)): ?>
                                    <?php foreach ($emails as $email): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($email['data_invio'])) ?></td>
                                            <td title="<?= esc($email['destinatario']) ?>">
                                                <?= strlen($email['destinatario']) > 40 ? substr(esc($email['destinatario']), 0, 37) . '...' : esc($email['destinatario']) ?>
                                            </td>
                                            <td title="<?= esc($email['oggetto']) ?>">
                                                <?= strlen($email['oggetto']) > 50 ? substr(esc($email['oggetto']), 0, 47) . '...' : esc($email['oggetto']) ?>
                                            </td>
                                            <td>
                                                <?php if ($email['stato'] == 'inviato'): ?>
                                                    <span class="badge badge-success">Inviata</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Errore</span>
                                                    <?php if (!empty($email['error_message'])): ?>
                                                        <i class="fas fa-info-circle" title="<?= esc($email['error_message']) ?>"></i>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('richieste-offerta/visualizza-email/' . $email['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Visualizza
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary rispondiEmailBtn" 
                                                        data-id="<?= $email['id'] ?>"
                                                        data-destinatario="<?= esc($email['destinatario']) ?>"
                                                        data-oggetto="<?= esc($email['oggetto']) ?>"
                                                        data-toggle="modal" 
                                                        data-target="#modalRispondiEmail">
                                                    <i class="fas fa-reply"></i> Rispondi
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Nessuna email inviata per questa richiesta d'offerta</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per rispondere alle email -->
<div class="modal fade" id="modalRispondiEmail" tabindex="-1" role="dialog" aria-labelledby="modalRispondiEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRispondiEmailLabel">Rispondi Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formRispondiEmail" action="" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="emailId" name="email_id" value="">
                <?= csrf_field() ?>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="destinatario">Destinatario:</label>
                        <select class="form-control select2-tags" id="destinatario" name="destinatario[]" multiple required>
                            <!-- I destinatari verranno aggiunti dinamicamente -->
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cc">CC (opzionale):</label>
                                <select class="form-control select2-tags" id="cc" name="cc[]" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ccn">CCN (opzionale):</label>
                                <select class="form-control select2-tags" id="ccn" name="ccn[]" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="oggetto">Oggetto:</label>
                        <input type="text" class="form-control" id="oggetto" name="oggetto" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="corpo">Messaggio:</label>
                        <textarea class="form-control summernote" id="corpo" name="corpo"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="includeCitazione" name="include_citazione" value="1" checked>
                            <label class="custom-control-label" for="includeCitazione">Includi citazione dell'email originale</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="allegaPdf" name="allega_pdf" value="1">
                            <label class="custom-control-label" for="allegaPdf">Allega PDF della richiesta d'offerta</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="allegati">Allegati (opzionali):</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="allegati" name="allegati[]" multiple>
                                <label class="custom-file-label" for="allegati">Scegli files...</label>
                            </div>
                        </div>
                        <small class="text-muted">Puoi selezionare più file (max 10MB totali)</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Invia Risposta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Select2 CSS -->
<link href="<?= base_url('plugins/select2/css/select2.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>" rel="stylesheet">
<!-- Summernote CSS -->
<link href="<?= base_url('plugins/summernote/summernote-bs4.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 -->
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('plugins/summernote/lang/summernote-it-IT.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Inizializza Select2
    $('.select2-tags').select2({
        theme: 'bootstrap4',
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: 'Inserisci uno o più indirizzi email...'
    });
    
    // Inizializza Summernote
    $('.summernote').summernote({
        lang: 'it-IT',
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Componi il tuo messaggio qui...'
    });
    
    // Gestione file allegati
    $('#allegati').on('change', function() {
        var fileCount = this.files.length;
        var fileNames = [];
        
        for (var i = 0; i < fileCount; i++) {
            fileNames.push(this.files[i].name);
        }
        
        if (fileCount > 0) {
            $(this).next('.custom-file-label').text(fileCount > 1 ? fileCount + ' files selezionati' : fileNames[0]);
        } else {
            $(this).next('.custom-file-label').text('Scegli files...');
        }
    });
    
    // Gestione del pulsante "Rispondi"
    $('.rispondiEmailBtn').click(function() {
        var emailId = $(this).data('id');
        var destinatario = $(this).data('destinatario');
        var oggetto = $(this).data('oggetto');
        
        // Imposta l'URL del form
        $('#formRispondiEmail').attr('action', '<?= site_url("richieste-offerta/rispondi-email/") ?>' + emailId);
        $('#emailId').val(emailId);
        
        // Prepara l'oggetto con "Re:"
        if (!oggetto.startsWith('Re:')) {
            oggetto = 'Re: ' + oggetto;
        }
        $('#oggetto').val(oggetto);
        
        // Pulisci e poi aggiungi i destinatari
        $('#destinatario').empty();
        var destinatariArray = destinatario.split(',');
        destinatariArray.forEach(function(email) {
            email = email.trim();
            if (email) {
                var option = new Option(email, email, true, true);
                $('#destinatario').append(option);
            }
        });
        $('#destinatario').trigger('change');
        
        // Pulisci il campo corpo
        $('#corpo').summernote('code', '');
    });
    
    // Validazione del form
    $('#formRispondiEmail').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            alert('Verifica i campi obbligatori');
            return false;
        }
        
        var destinatari = $('#destinatario').val();
        if (!destinatari || destinatari.length === 0) {
            e.preventDefault();
            alert('Inserisci almeno un destinatario');
            return false;
        }
        
        if (!$('#oggetto').val().trim()) {
            e.preventDefault();
            alert('L\'oggetto è obbligatorio');
            return false;
        }
        
        var corpo = $('#corpo').summernote('code');
        if (!corpo || corpo.trim() === '') {
            e.preventDefault();
            alert('Il messaggio non può essere vuoto');
            return false;
        }
        
        return true;
    });
});
</script>
<?= $this->endSection() ?> 
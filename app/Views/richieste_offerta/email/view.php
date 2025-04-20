<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Visualizza Email<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Visualizza Email - RDO #<?= $richiesta['numero'] ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta') ?>">Richieste d'Offerta</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta/' . $richiesta['id']) ?>">RDO #<?= $richiesta['numero'] ?></a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta/email-log/' . $richiesta['id']) ?>">Storico Email</a></li>
<li class="breadcrumb-item active">Visualizza Email</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dettagli Email</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('richieste-offerta/email-log/' . $richiesta['id']) ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Torna allo storico
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" id="rispondiBtn"
                                data-id="<?= $email['id'] ?>"
                                data-destinatario="<?= esc($email['destinatario']) ?>"
                                data-oggetto="<?= esc($email['oggetto']) ?>">
                            <i class="fas fa-reply"></i> Rispondi
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <span class="text-muted">Data invio:</span>
                                                <span class="font-weight-bold"><?= date('d/m/Y H:i', strtotime($email['data_invio'])) ?></span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Stato:</span>
                                                <?php if ($email['stato'] == 'inviato'): ?>
                                                    <span class="badge badge-success">Inviata</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Errore</span>
                                                    <?php if (!empty($email['error_message'])): ?>
                                                        <p class="text-danger mt-1"><?= esc($email['error_message']) ?></p>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <span class="text-muted">ID Email:</span>
                                                <span class="font-weight-bold">#<?= $email['id'] ?></span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Mittente:</span>
                                                <span class="font-weight-bold"><?= esc($email['mittente']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Destinatari</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>A:</label>
                                                <p class="text-primary"><?= esc($email['destinatario']) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CC:</label>
                                                <p class="text-primary"><?= !empty($email['cc']) ? esc($email['cc']) : '<span class="text-muted">Nessuno</span>' ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CCN:</label>
                                                <p class="text-primary"><?= !empty($email['ccn']) ? esc($email['ccn']) : '<span class="text-muted">Nessuno</span>' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Oggetto</h5>
                                </div>
                                <div class="card-body">
                                    <h5><?= esc($email['oggetto']) ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Contenuto Email</h5>
                                </div>
                                <div class="card-body">
                                    <div class="email-content-wrapper" style="border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; background-color: #fff;">
                                        <iframe id="emailContentFrame" style="width: 100%; height: 400px; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($email['allegati'])): ?>
                        <?php $allegatiArray = json_decode($email['allegati'], true); ?>
                        <?php if (is_array($allegatiArray) && count($allegatiArray) > 0): ?>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Allegati (<?= count($allegatiArray) ?>)</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <?php foreach($allegatiArray as $allegato): ?>
                                                    <div class="col-md-3 col-sm-6 mb-3">
                                                        <div class="attachment-box">
                                                            <div class="attachment-preview">
                                                                <?php $ext = pathinfo($allegato['nome'], PATHINFO_EXTENSION); ?>
                                                                <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                                    <i class="fas fa-file-image fa-3x text-primary"></i>
                                                                <?php elseif (in_array(strtolower($ext), ['pdf'])): ?>
                                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                                <?php elseif (in_array(strtolower($ext), ['doc', 'docx'])): ?>
                                                                    <i class="fas fa-file-word fa-3x text-primary"></i>
                                                                <?php elseif (in_array(strtolower($ext), ['xls', 'xlsx'])): ?>
                                                                    <i class="fas fa-file-excel fa-3x text-success"></i>
                                                                <?php elseif (in_array(strtolower($ext), ['zip', 'rar'])): ?>
                                                                    <i class="fas fa-file-archive fa-3x text-warning"></i>
                                                                <?php else: ?>
                                                                    <i class="fas fa-file fa-3x text-secondary"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="attachment-info">
                                                                <p class="attachment-name" title="<?= esc($allegato['nome']) ?>">
                                                                    <?= (strlen($allegato['nome']) > 25) ? substr(esc($allegato['nome']), 0, 22) . '...' : esc($allegato['nome']) ?>
                                                                </p>
                                                                <p class="attachment-size text-muted">
                                                                    <?= number_format($allegato['dimensione'] / 1024, 2) ?> KB
                                                                </p>
                                                                <a href="<?= site_url('uploads/email_attachments/' . $allegato['nome_file']) ?>" 
                                                                   class="btn btn-sm btn-primary" download="<?= $allegato['nome'] ?>">
                                                                    <i class="fas fa-download"></i> Scarica
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per rispondere all'email -->
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
<style>
.attachment-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    height: 100%;
    min-height: 170px;
    transition: all 0.3s;
}
.attachment-box:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-color: #adb5bd;
}
.attachment-preview {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    height: 70px;
}
.attachment-info {
    text-align: center;
    width: 100%;
}
.attachment-name {
    font-weight: bold;
    margin-bottom: 5px;
    word-break: break-word;
}
.attachment-size {
    font-size: 0.8rem;
    margin-bottom: 10px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 -->
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('plugins/summernote/lang/summernote-it-IT.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Inizializzazione iframe per contenuto email
    var iframe = document.getElementById('emailContentFrame');
    var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
    iframeDocument.open();
    iframeDocument.write(`
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 10px;
                    margin: 0;
                    line-height: 1.5;
                }
                pre {
                    white-space: pre-wrap;
                    margin: 0;
                    font-family: Arial, sans-serif;
                }
                table { border-collapse: collapse; }
                table, th, td { border: 1px solid #e0e0e0; padding: 5px; }
            </style>
        </head>
        <body>
            <?= $email['corpo'] ?>
        </body>
        </html>
    `);
    iframeDocument.close();
    
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
    $('#rispondiBtn').click(function() {
        var emailId = $(this).data('id');
        var destinatario = $(this).data('destinatario');
        var oggetto = $(this).data('oggetto');
        
        // Prepara e apre il modal
        $('#modalRispondiEmail').modal('show');
        
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
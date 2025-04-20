<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettagli Progetto<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettagli Progetto<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('progetti') ?>">Progetti</a></li>
<li class="breadcrumb-item active">Dettagli</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <?= esc($progetto['nome']) ?>
                                <?php if (!$progetto['attivo']) : ?>
                                    <span class="badge badge-danger">Disattivato</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <a href="<?= base_url('progetti/edit/' . $progetto['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Modifica
                                </a>
                                <a href="<?= base_url('progetti/toggle-attivo/' . $progetto['id']) ?>" class="btn <?= $progetto['attivo'] ? 'btn-warning' : 'btn-success' ?>">
                                    <i class="fas <?= $progetto['attivo'] ? 'fa-ban' : 'fa-check' ?>"></i> 
                                    <?= $progetto['attivo'] ? 'Disattiva' : 'Attiva' ?>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-danger btn-elimina-progetto" data-id="<?= $progetto['id'] ?>" data-nome="<?= esc($progetto['nome']) ?>">
                                    <i class="fas fa-trash"></i> Elimina
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informazioni Generali</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Nome:</th>
                                            <td><?= esc($progetto['nome']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Cliente:</th>
                                            <td>
                                                <?php if (isset($progetto['anagrafica'])) : ?>
                                                    <a href="<?= base_url('anagrafiche/show/' . $progetto['id_anagrafica']) ?>">
                                                        <?= esc($progetto['anagrafica']['ragione_sociale']) ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descrizione:</th>
                                            <td><?= $progetto['descrizione'] ? nl2br(esc($progetto['descrizione'])) : '<span class="text-muted">Non specificata</span>' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Budget:</th>
                                            <td><?= $progetto['budget'] ? number_format($progetto['budget'], 2, ',', '.') . ' €' : '<span class="text-muted">Non specificato</span>' ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Date e Responsabili</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Data Inizio:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_inizio']) && $progetto['data_inizio'] !== '0000-00-00' && strtotime($progetto['data_inizio']) > 0) : ?>
                                                    <?= date('d/m/Y', strtotime($progetto['data_inizio'])) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data Scadenza:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_scadenza']) && $progetto['data_scadenza'] !== '0000-00-00' && strtotime($progetto['data_scadenza']) > 0) : ?>
                                                    <?php
                                                    $scadenza = new DateTime($progetto['data_scadenza']);
                                                    $oggi = new DateTime();
                                                    $isExpired = $oggi > $scadenza;
                                                    $badgeClass = $isExpired ? 'badge-danger' : 'badge-success';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= date('d/m/Y', strtotime($progetto['data_scadenza'])) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data Fine:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_fine']) && $progetto['data_fine'] !== '0000-00-00' && strtotime($progetto['data_fine']) > 0) : ?>
                                                    <?= date('d/m/Y', strtotime($progetto['data_fine'])) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Creato da:</th>
                                            <td>
                                                <?php if (isset($progetto['creatore'])) : ?>
                                                    <?= esc($progetto['creatore']['nome']) ?> <?= esc($progetto['creatore']['cognome']) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Responsabile:</th>
                                            <td>
                                                <?php if (isset($progetto['responsabile'])) : ?>
                                                    <?= esc($progetto['responsabile']['nome']) ?> <?= esc($progetto['responsabile']['cognome']) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">Stato del Progetto</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Fase Kanban</h6>
                                            <?php
                                            $faseClass = '';
                                            switch ($progetto['fase_kanban']) {
                                                case 'backlog':
                                                    $faseClass = 'bg-secondary';
                                                    $faseText = 'Backlog';
                                                    break;
                                                case 'da_iniziare':
                                                    $faseClass = 'bg-info';
                                                    $faseText = 'Da Iniziare';
                                                    break;
                                                case 'in_corso':
                                                    $faseClass = 'bg-primary';
                                                    $faseText = 'In Corso';
                                                    break;
                                                case 'in_revisione':
                                                    $faseClass = 'bg-warning';
                                                    $faseText = 'In Revisione';
                                                    break;
                                                case 'completato':
                                                    $faseClass = 'bg-success';
                                                    $faseText = 'Completato';
                                                    break;
                                                default:
                                                    $faseClass = 'bg-secondary';
                                                    $faseText = ucfirst($progetto['fase_kanban']);
                                            }
                                            ?>
                                            <p><span class="badge <?= $faseClass ?> p-2"><?= $faseText ?></span></p>
                                            
                                            <form action="<?= base_url('progetti/fase-kanban/' . $progetto['id']) ?>" method="post" id="formFaseKanban">
                                                <?= csrf_field() ?>
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" name="fase_kanban" id="changeFaseKanban">
                                                        <option value="backlog" <?= $progetto['fase_kanban'] == 'backlog' ? 'selected' : '' ?>>Backlog</option>
                                                        <option value="da_iniziare" <?= $progetto['fase_kanban'] == 'da_iniziare' ? 'selected' : '' ?>>Da Iniziare</option>
                                                        <option value="in_corso" <?= $progetto['fase_kanban'] == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                                        <option value="in_revisione" <?= $progetto['fase_kanban'] == 'in_revisione' ? 'selected' : '' ?>>In Revisione</option>
                                                        <option value="completato" <?= $progetto['fase_kanban'] == 'completato' ? 'selected' : '' ?>>Completato</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Stato</h6>
                                            <?php
                                            $statoClass = '';
                                            switch ($progetto['stato']) {
                                                case 'in_corso':
                                                    $statoClass = 'bg-primary';
                                                    $statoText = 'In corso';
                                                    break;
                                                case 'completato':
                                                    $statoClass = 'bg-success';
                                                    $statoText = 'Completato';
                                                    break;
                                                case 'sospeso':
                                                    $statoClass = 'bg-warning';
                                                    $statoText = 'Sospeso';
                                                    break;
                                                case 'annullato':
                                                    $statoClass = 'bg-danger';
                                                    $statoText = 'Annullato';
                                                    break;
                                                default:
                                                    $statoClass = 'bg-secondary';
                                                    $statoText = 'Sconosciuto';
                                            }
                                            ?>
                                            <p><span class="badge <?= $statoClass ?> p-2"><?= $statoText ?></span></p>
                                            
                                            <form action="<?= base_url('progetti/stato/' . $progetto['id']) ?>" method="post" id="formStato">
                                                <?= csrf_field() ?>
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" name="stato" id="changeStato">
                                                        <option value="in_corso" <?= $progetto['stato'] == 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                                        <option value="completato" <?= $progetto['stato'] == 'completato' ? 'selected' : '' ?>>Completato</option>
                                                        <option value="sospeso" <?= $progetto['stato'] == 'sospeso' ? 'selected' : '' ?>>Sospeso</option>
                                                        <option value="annullato" <?= $progetto['stato'] == 'annullato' ? 'selected' : '' ?>>Annullato</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <h6 class="mt-3">Priorità</h6>
                                    <?php
                                    $prioritaClass = '';
                                    switch ($progetto['priorita']) {
                                        case 'bassa':
                                            $prioritaClass = 'bg-success';
                                            break;
                                        case 'media':
                                            $prioritaClass = 'bg-info';
                                            break;
                                        case 'alta':
                                            $prioritaClass = 'bg-warning';
                                            break;
                                        case 'critica':
                                            $prioritaClass = 'bg-danger';
                                            break;
                                        default:
                                            $prioritaClass = 'bg-secondary';
                                    }
                                    ?>
                                    <p><span class="badge <?= $prioritaClass ?> p-2"><?= ucfirst($progetto['priorita']) ?></span></p>
                                    
                                    <h6 class="mt-3">Creato il</h6>
                                    <p><?= date('d/m/Y H:i', strtotime($progetto['created_at'])) ?></p>
                                    
                                    <h6 class="mt-3">Ultimo aggiornamento</h6>
                                    <p><?= date('d/m/Y H:i', strtotime($progetto['updated_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progetto Padre -->
    <?php if (!empty($progetto['progetto_padre'])) : ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progetto Padre</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nome:</strong>
                            <a href="<?= base_url('progetti/' . $progetto['progetto_padre']['id']) ?>">
                                <?= esc($progetto['progetto_padre']['nome']) ?>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <strong>Stato:</strong>
                            <span class="badge bg-<?= getStatoBadgeClass($progetto['progetto_padre']['stato']) ?>">
                                <?= ucfirst($progetto['progetto_padre']['stato']) ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fase:</strong>
                            <span class="badge bg-info">
                                <?= ucfirst($progetto['progetto_padre']['fase_kanban']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sottoprogetti -->
    <?php if (!empty($progetto['sottoprogetti'])) : ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sottoprogetti</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Stato</th>
                                    <th>Fase</th>
                                    <th>Scadenza</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($progetto['sottoprogetti'] as $sottoprogetto) : ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('progetti/' . $sottoprogetto['id']) ?>">
                                                <?= esc($sottoprogetto['nome']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getStatoBadgeClass($sottoprogetto['stato']) ?>">
                                                <?= ucfirst($sottoprogetto['stato']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= ucfirst($sottoprogetto['fase_kanban']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= !empty($sottoprogetto['data_scadenza']) ? date('d/m/Y', strtotime($sottoprogetto['data_scadenza'])) : 'N/D' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('progetti/edit/' . $sottoprogetto['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-elimina-sottoprogetto" data-id="<?= $sottoprogetto['id'] ?>" data-nome="<?= esc($sottoprogetto['nome']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Dettagli Progetto -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dettagli Progetto</h3>
            </div>
            <div class="card-body">
            </div>
        </div>
    </div>

    <!-- Gestione Documenti -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Documenti e Allegati</h3>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadDocumentoModal">
                            <i class="fas fa-upload"></i> Carica documento
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(empty($documenti ?? [])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nessun documento caricato per questo progetto.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome file</th>
                                        <th>Tipo</th>
                                        <th>Dimensione</th>
                                        <th>Caricato da</th>
                                        <th>Data caricamento</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documenti as $doc): ?>
                                    <tr>
                                        <td><?= esc($doc['nome_originale']) ?></td>
                                        <td>
                                            <?php
                                            $estensione = pathinfo($doc['nome_originale'], PATHINFO_EXTENSION);
                                            $iconClass = 'fa-file';
                                            
                                            switch(strtolower($estensione)) {
                                                case 'pdf':
                                                    $iconClass = 'fa-file-pdf';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $iconClass = 'fa-file-word';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $iconClass = 'fa-file-excel';
                                                    break;
                                                case 'ppt':
                                                case 'pptx':
                                                    $iconClass = 'fa-file-powerpoint';
                                                    break;
                                                case 'jpg':
                                                case 'jpeg':
                                                case 'png':
                                                case 'gif':
                                                    $iconClass = 'fa-file-image';
                                                    break;
                                                case 'zip':
                                                case 'rar':
                                                    $iconClass = 'fa-file-archive';
                                                    break;
                                                case 'txt':
                                                    $iconClass = 'fa-file-alt';
                                                    break;
                                            }
                                            ?>
                                            <i class="fas <?= $iconClass ?>"></i> .<?= strtoupper($estensione) ?>
                                        </td>
                                        <td><?= formatFileSize($doc['dimensione']) ?></td>
                                        <td><?= esc($doc['utente']['nome'] . ' ' . $doc['utente']['cognome']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('documenti/download/' . $doc['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editDocumentoModal" data-id="<?= $doc['id'] ?>" data-nome="<?= esc($doc['nome_originale']) ?>" data-descrizione="<?= esc($doc['descrizione'] ?? '') ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-elimina-documento" data-id="<?= $doc['id'] ?>" data-nome="<?= esc($doc['nome_originale']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per caricare un nuovo documento -->
<div class="modal fade" id="uploadDocumentoModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentoModalLabel">Carica Nuovo Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('documenti/upload') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id_progetto" value="<?= $progetto['id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Seleziona file</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Scegli file</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Formati supportati: .pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .png, .zip, .rar, .txt (max 20MB)</small>
                    </div>
                    <div class="form-group">
                        <label for="descrizione">Descrizione (opzionale)</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Carica</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per modificare un documento -->
<div class="modal fade" id="editDocumentoModal" tabindex="-1" role="dialog" aria-labelledby="editDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentoModalLabel">Modifica Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('documenti/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_documento" id="edit_id_documento">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nome">Nome file</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_descrizione">Descrizione (opzionale)</label>
                        <textarea class="form-control" id="edit_descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Cambio automatico della fase Kanban
        $('#changeFaseKanban').change(function() {
            $('#formFaseKanban').submit();
        });
        
        // Cambio automatico dello stato
        $('#changeStato').change(function() {
            $('#formStato').submit();
        });
        
        // Script per mostrare il nome del file selezionato nel form di upload
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
        
        // Popola il modal di modifica documento
        $('#editDocumentoModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nome = button.data('nome');
            var descrizione = button.data('descrizione');
            
            var modal = $(this);
            modal.find('#edit_id_documento').val(id);
            modal.find('#edit_nome').val(nome);
            modal.find('#edit_descrizione').val(descrizione);
        });
        
        // Gestione eliminazione documento con SweetAlert2
        $('.btn-elimina-documento').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Vuoi eliminare il documento '" + nome + "'?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('documenti/delete/') ?>' + id;
                }
            });
        });
        
        // Gestione eliminazione progetto con SweetAlert2
        $('.btn-elimina-progetto').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare il progetto '" + nome + "'. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('progetti/delete/') ?>' + id;
                }
            });
        });
        
        // Gestione eliminazione sottoprogetto con SweetAlert2
        $('.btn-elimina-sottoprogetto').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare il sottoprogetto '" + nome + "'. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('progetti/delete/') ?>' + id;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?> 
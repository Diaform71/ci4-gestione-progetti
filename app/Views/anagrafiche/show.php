<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('anagrafiche') ?>">Anagrafiche</a></li>
<li class="breadcrumb-item active">Dettaglio</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Dettaglio Anagrafica</h3>
                        <div>
                            <a href="<?= base_url('anagrafiche/edit/' . $anagrafica['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="<?= base_url('anagrafiche') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Indietro
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($anagrafica['logo'])): ?>
                        <div class="col-md-3 text-center mb-4">
                            <img src="<?= base_url('uploads/logos/' . $anagrafica['logo']) ?>" alt="Logo" class="img-fluid" style="max-height: 150px">
                        </div>
                        <div class="col-md-9">
                        <?php else: ?>
                        <div class="col-md-12">
                        <?php endif; ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h2><?= esc($anagrafica['ragione_sociale']) ?></h2>
                                    <hr>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Indirizzo</span>
                                            <span class="info-box-number text-muted">
                                                <?= !empty($anagrafica['indirizzo']) ? esc($anagrafica['indirizzo']) : '<em>Non specificato</em>' ?>
                                                <?php if (!empty($anagrafica['citta']) || !empty($anagrafica['cap'])): ?>
                                                    <br>
                                                    <?= !empty($anagrafica['cap']) ? esc($anagrafica['cap']) . ' ' : '' ?>
                                                    <?= !empty($anagrafica['citta']) ? esc($anagrafica['citta']) : '' ?>
                                                <?php endif; ?>
                                                <?php if (!empty($anagrafica['nazione'])): ?>
                                                    <br><?= esc($anagrafica['nazione']) ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Contatti</span>
                                            <span class="info-box-number text-muted">
                                                <?php if (!empty($anagrafica['telefono'])): ?>
                                                    <i class="fas fa-phone"></i> <?= esc($anagrafica['telefono']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($anagrafica['fax'])): ?>
                                                    <i class="fas fa-fax"></i> <?= esc($anagrafica['fax']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($anagrafica['email'])): ?>
                                                    <i class="fas fa-envelope"></i> <?= esc($anagrafica['email']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($anagrafica['url'])): ?>
                                                    <i class="fas fa-globe"></i> 
                                                    <a href="<?= prep_url(esc($anagrafica['url'])) ?>" target="_blank">
                                                        <?= esc($anagrafica['url']) ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (empty($anagrafica['telefono']) && empty($anagrafica['fax']) && empty($anagrafica['email']) && empty($anagrafica['url'])): ?>
                                                    <em>Nessun contatto specificato</em>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Dati Fiscali</span>
                                            <span class="info-box-number text-muted">
                                                <?php if (!empty($anagrafica['partita_iva'])): ?>
                                                    <strong>P.IVA:</strong> <?= esc($anagrafica['partita_iva']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($anagrafica['codice_fiscale'])): ?>
                                                    <strong>C.F.:</strong> <?= esc($anagrafica['codice_fiscale']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($anagrafica['sdi'])): ?>
                                                    <strong>Codice SDI:</strong> <?= esc($anagrafica['sdi']) ?><br>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($aliquota_iva)): ?>
                                                    <strong>Aliquota IVA:</strong> <?= esc($aliquota_iva['codice']) ?> - <?= esc($aliquota_iva['descrizione']) ?> (<?= number_format((float)$aliquota_iva['percentuale'], 2, ',', '.') ?>%)<br>
                                                <?php endif; ?>
                                                
                                                <?php if (empty($anagrafica['partita_iva']) && empty($anagrafica['codice_fiscale']) && empty($anagrafica['sdi']) && empty($aliquota_iva)): ?>
                                                    <em>Nessun dato fiscale specificato</em>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-muted">Tipologia</span>
                                            <span class="info-box-number text-muted">
                                                <?php if ($anagrafica['cliente']): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Cliente</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-times"></i> Cliente</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($anagrafica['fornitore']): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Fornitore</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-times"></i> Fornitore</span>
                                                <?php endif; ?>
                                                
                                                <br><br>
                                                
                                                <strong>Stato:</strong>
                                                <?php if ($anagrafica['attivo']): ?>
                                                    <span class="badge badge-success">Attivo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inattivo</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-muted">
                        <small>
                            <strong>Creato il:</strong> <?= date('d/m/Y H:i', strtotime($anagrafica['created_at'])) ?>
                            <?php if ($anagrafica['updated_at'] != $anagrafica['created_at']): ?>
                                | <strong>Aggiornato il:</strong> <?= date('d/m/Y H:i', strtotime($anagrafica['updated_at'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione Contatti Associati -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Contatti Associati</h3>
                    <button type="button" class="btn btn-primary" id="btn-add-contatto">
                        <i class="fas fa-plus"></i> Associa Contatto
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-contatti">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                                <th>Ruolo</th>
                                <th width="80">Principale</th>
                                <th width="100">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="contatti-list">
                            <!-- I dati verranno caricati dinamicamente tramite API -->
                            <tr class="loading-row">
                                <td colspan="7" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Caricamento...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per associare un contatto -->
<div class="modal fade" id="associaContattoModal" tabindex="-1" role="dialog" aria-labelledby="associaContattoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="associaContattoModalLabel">Associa Contatto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-associa-contatto">
                    <div class="form-group">
                        <label for="id_contatto">Contatto *</label>
                        <select class="form-control" id="id_contatto" name="id_contatto" required>
                            <option value="">Seleziona contatto...</option>
                            <!-- Opzioni caricate via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ruolo">Ruolo</label>
                        <input type="text" class="form-control" id="ruolo" name="ruolo" placeholder="Es: Responsabile, Amministratore, Tecnico...">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="principale" name="principale" value="1">
                            <label class="custom-control-label" for="principale">Contatto principale</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea class="form-control" id="note_associazione" name="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="btn-save-associazione">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per modificare l'associazione -->
<div class="modal fade" id="modificaAssociazioneModal" tabindex="-1" role="dialog" aria-labelledby="modificaAssociazioneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modificaAssociazioneModalLabel">Modifica Associazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-modifica-associazione">
                    <input type="hidden" id="edit_id_associazione">
                    <div class="form-group">
                        <label for="edit_contatto">Contatto</label>
                        <input type="text" class="form-control" id="edit_contatto" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_ruolo">Ruolo</label>
                        <input type="text" class="form-control" id="edit_ruolo" name="ruolo">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_principale" name="principale" value="1">
                            <label class="custom-control-label" for="edit_principale">Contatto principale</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_note">Note</label>
                        <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="btn-update-associazione">Salva modifiche</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal di conferma eliminazione -->
<div class="modal fade" id="deleteAssociazioneModal" tabindex="-1" role="dialog" aria-labelledby="deleteAssociazioneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAssociazioneModalLabel">Conferma eliminazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler rimuovere l'associazione con <strong id="delete-contatto-name"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-associazione">Elimina</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        const anagraficaId = <?= $anagrafica['id'] ?>;
        
        // Carica i contatti associati all'anagrafica
        loadContattiAssociati();
        
        // Carica i contatti disponibili per l'associazione
        loadContattiDisponibili();
        
        // Associazione contatto
        $('#btn-add-contatto').on('click', function() {
            $('#associaContattoModal').modal('show');
        });
        
        // Salva associazione
        $('#btn-save-associazione').on('click', function() {
            const idContatto = $('#id_contatto').val();
            const ruolo = $('#ruolo').val();
            const principale = $('#principale').is(':checked') ? 1 : 0;
            const note = $('#note_associazione').val();
            
            if (!idContatto) {
                alert('Seleziona un contatto');
                return;
            }
            
            $.ajax({
                url: '<?= base_url('api/anagrafiche/contatti') ?>',
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    id_anagrafica: anagraficaId,
                    id_contatto: idContatto,
                    ruolo: ruolo,
                    principale: principale,
                    note: note
                }),
                success: function(response) {
                    if (response.success) {
                        $('#associaContattoModal').modal('hide');
                        $('#form-associa-contatto')[0].reset();
                        loadContattiAssociati();
                        showNotification('Successo', response.message, 'success');
                    } else {
                        showNotification('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Modifica associazione
        $(document).on('click', '.btn-edit-associazione', function() {
            const id = $(this).data('id');
            const contatto = $(this).data('contatto');
            const ruolo = $(this).data('ruolo');
            const principale = $(this).data('principale');
            const note = $(this).data('note');
            
            $('#edit_id_associazione').val(id);
            $('#edit_contatto').val(contatto);
            $('#edit_ruolo').val(ruolo);
            $('#edit_principale').prop('checked', principale === 1);
            $('#edit_note').val(note);
            
            $('#modificaAssociazioneModal').modal('show');
        });
        
        // Salva modifiche associazione
        $('#btn-update-associazione').on('click', function() {
            const id = $('#edit_id_associazione').val();
            const ruolo = $('#edit_ruolo').val();
            const principale = $('#edit_principale').is(':checked') ? 1 : 0;
            const note = $('#edit_note').val();
            
            $.ajax({
                url: `<?= base_url('api/anagrafiche/contatti') ?>/${id}`,
                type: 'PUT',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    ruolo: ruolo,
                    principale: principale,
                    note: note
                }),
                success: function(response) {
                    if (response.success) {
                        $('#modificaAssociazioneModal').modal('hide');
                        loadContattiAssociati();
                        showNotification('Successo', response.message, 'success');
                    } else {
                        showNotification('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Eliminazione associazione
        $(document).on('click', '.btn-delete-associazione', function() {
            const id = $(this).data('id');
            const contatto = $(this).data('contatto');
            
            $('#delete-contatto-name').text(contatto);
            $('#btn-confirm-delete-associazione').data('id', id);
            $('#deleteAssociazioneModal').modal('show');
        });
        
        // Conferma eliminazione associazione
        $('#btn-confirm-delete-associazione').on('click', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: `<?= base_url('api/anagrafiche/contatti') ?>/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#deleteAssociazioneModal').modal('hide');
                        loadContattiAssociati();
                        showNotification('Successo', response.message, 'success');
                    } else {
                        showNotification('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Imposta contatto come principale
        $(document).on('click', '.btn-set-principale', function() {
            const idContatto = $(this).data('contatto-id');
            
            $.ajax({
                url: `<?= base_url('api/anagrafiche') ?>/${anagraficaId}/contatti/${idContatto}/principale`,
                type: 'PUT',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadContattiAssociati();
                        showNotification('Successo', response.message, 'success');
                    } else {
                        showNotification('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Funzione per caricare i contatti associati
        function loadContattiAssociati() {
            $.ajax({
                url: `<?= base_url('api/anagrafiche') ?>/${anagraficaId}/contatti`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderContattiAssociati(response.data);
                    } else {
                        showNotification('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('Errore', errorMessage, 'error');
                    $('#contatti-list').html('<tr><td colspan="7" class="text-center">Errore nel caricamento dei dati</td></tr>');
                }
            });
        }
        
        // Funzione per caricare i contatti disponibili
        function loadContattiDisponibili() {
            $.ajax({
                url: '<?= base_url('api/contatti/getContatti') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleziona contatto...</option>';
                        $.each(response.data, function(i, item) {
                            options += `<option value="${item.id}">${item.nome} ${item.cognome}</option>`;
                        });
                        $('#id_contatto').html(options);
                    }
                }
            });
        }
        
        // Funzione per renderizzare i contatti associati
        function renderContattiAssociati(data) {
            let html = '';
            
            if (data.length === 0) {
                html = '<tr><td colspan="7" class="text-center">Nessun contatto associato</td></tr>';
            } else {
                $.each(data, function(i, item) {
                    const principaleIcon = item.principale == 1 ? 
                        '<span class="badge badge-success"><i class="fas fa-check"></i></span>' : 
                        '<button class="btn btn-xs btn-outline-success btn-set-principale" data-contatto-id="' + item.id_contatto + '"><i class="fas fa-star"></i></button>';
                    
                    html += `
                        <tr>
                            <td>${item.nome || '-'}</td>
                            <td>${item.cognome || '-'}</td>
                            <td>${item.email || '-'}</td>
                            <td>${item.telefono || item.cellulare || '-'}</td>
                            <td>${item.ruolo || '-'}</td>
                            <td class="text-center">${principaleIcon}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary btn-edit-associazione" 
                                        data-id="${item.id}" 
                                        data-contatto="${item.nome + ' ' + item.cognome}"
                                        data-ruolo="${item.ruolo || ''}" 
                                        data-principale="${item.principale}" 
                                        data-note="${item.note || ''}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-associazione" 
                                        data-id="${item.id}" 
                                        data-contatto="${item.nome + ' ' + item.cognome}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
            
            $('#contatti-list').html(html);
        }
    });
</script>
<?= $this->endSection() ?> 
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('contatti') ?>">Contatti</a></li>
<li class="breadcrumb-item active">Dettaglio</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Scheda contatto -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <?php if (!empty($contatto['immagine'])): ?>
                            <img class="profile-user-img img-fluid img-circle" 
                                src="<?= base_url('uploads/contatti/' . $contatto['immagine']) ?>" 
                                alt="Foto contatto">
                        <?php else: ?>
                            <img class="profile-user-img img-fluid img-circle" 
                                src="<?= base_url('dist/img/avatar.png') ?>" 
                                alt="Foto predefinita">
                        <?php endif; ?>
                    </div>

                    <h3 class="profile-username text-center"><?= esc($contatto['nome'] . ' ' . $contatto['cognome']) ?></h3>

                    <ul class="list-group list-group-unbordered mb-3">
                        <?php if (!empty($contatto['email'])): ?>
                        <li class="list-group-item">
                            <b><i class="fas fa-envelope mr-1"></i> Email</b> 
                            <a class="float-right" href="mailto:<?= esc($contatto['email']) ?>"><?= esc($contatto['email']) ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($contatto['telefono'])): ?>
                        <li class="list-group-item">
                            <b><i class="fas fa-phone mr-1"></i> Telefono</b> 
                            <span class="float-right"><?= esc($contatto['telefono']) ?></span>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($contatto['interno'])): ?>
                        <li class="list-group-item">
                            <b><i class="fas fa-phone-office mr-1"></i> Interno</b> 
                            <span class="float-right"><?= esc($contatto['interno']) ?></span>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($contatto['cellulare'])): ?>
                        <li class="list-group-item">
                            <b><i class="fas fa-mobile-alt mr-1"></i> Cellulare</b> 
                            <span class="float-right"><?= esc($contatto['cellulare']) ?></span>
                        </li>
                        <?php endif; ?>
                        
                        <li class="list-group-item">
                            <b><i class="fas fa-circle mr-1"></i> Stato</b>
                            <span class="float-right">
                                <?php if ($contatto['attivo']): ?>
                                    <span class="badge badge-success">Attivo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inattivo</span>
                                <?php endif; ?>
                            </span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('contatti') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Indietro
                        </a>
                        <a href="<?= base_url('contatti/edit/' . $contatto['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifica
                        </a>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <?php if (!empty($contatto['note'])): ?>
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Note</h3>
                </div>
                <div class="card-body">
                    <p><?= nl2br(esc($contatto['note'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Anagrafiche associate -->
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Anagrafiche associate</h3>
                    <button type="button" class="btn btn-sm btn-primary float-right" id="btn-add-anagrafica">
                        <i class="fas fa-plus"></i> Associa Anagrafica
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-anagrafiche">
                            <thead>
                                <tr>
                                    <th>Anagrafica</th>
                                    <th>Città</th>
                                    <th>Ruolo</th>
                                    <th width="80">Principale</th>
                                    <th width="100">Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="anagrafiche-list">
                                <!-- I dati verranno caricati dinamicamente tramite API -->
                                <tr class="loading-row">
                                    <td colspan="5" class="text-center">
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
</div>

<!-- Modal per associare un'anagrafica -->
<div class="modal fade" id="associaAnagraficaModal" tabindex="-1" role="dialog" aria-labelledby="associaAnagraficaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="associaAnagraficaModalLabel">Associa Anagrafica</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-associa-anagrafica">
                    <div class="form-group">
                        <label for="id_anagrafica">Anagrafica *</label>
                        <select class="form-control" id="id_anagrafica" name="id_anagrafica" required>
                            <option value="">Seleziona anagrafica...</option>
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
                        <label for="edit_anagrafica">Anagrafica</label>
                        <input type="text" class="form-control" id="edit_anagrafica" readonly>
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
                Sei sicuro di voler rimuovere l'associazione con <strong id="delete-anagrafica-name"></strong>?
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
    // Funzione per mostrare notifiche SweetAlert2
    function showSweetAlert(title, message, type = 'success') {
        Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: 'Ok'
        });
    }
    
    // Verifica se c'è un parametro nella URL per mostrare un messaggio
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showSweetAlert('Successo', decodeURIComponent(urlParams.get('success')), 'success');
        }
        if (urlParams.has('error')) {
            showSweetAlert('Errore', decodeURIComponent(urlParams.get('error')), 'error');
        }
    });

    $(function() {
        const contattoId = <?= $contatto['id'] ?>;
        
        // Carica le anagrafiche associate al contatto
        loadAnagraficheAssociate();
        
        // Carica le anagrafiche disponibili per l'associazione
        loadAnagraficheDisponibili();
        
        // Associazione anagrafica
        $('#btn-add-anagrafica').on('click', function() {
            $('#associaAnagraficaModal').modal('show');
        });
        
        // Salva associazione
        $('#btn-save-associazione').on('click', function() {
            const idAnagrafica = $('#id_anagrafica').val();
            const ruolo = $('#ruolo').val();
            const principale = $('#principale').is(':checked') ? 1 : 0;
            const note = $('#note_associazione').val();
            
            if (!idAnagrafica) {
                showSweetAlert('Attenzione', 'Seleziona un\'anagrafica', 'warning');
                return;
            }
            
            // Visualizza feedback immediato
            const loadingBtn = $(this);
            loadingBtn.html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...').prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('api/anagrafiche/contatti') ?>',
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    id_anagrafica: idAnagrafica,
                    id_contatto: contattoId,
                    ruolo: ruolo,
                    principale: principale,
                    note: note
                }),
                success: function(response) {
                    loadingBtn.html('Salva').prop('disabled', false);
                    
                    if (response.success) {
                        $('#associaAnagraficaModal').modal('hide');
                        $('#form-associa-anagrafica')[0].reset();
                        loadAnagraficheAssociate();
                        showSweetAlert('Successo', response.message, 'success');
                    } else {
                        showSweetAlert('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    loadingBtn.html('Salva').prop('disabled', false);
                    
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showSweetAlert('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Modifica associazione
        $(document).on('click', '.btn-edit-associazione', function() {
            const id = $(this).data('id');
            const anagrafica = $(this).data('anagrafica');
            const ruolo = $(this).data('ruolo');
            const principale = $(this).data('principale');
            const note = $(this).data('note');
            
            $('#edit_id_associazione').val(id);
            $('#edit_anagrafica').val(anagrafica);
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
            
            // Visualizza feedback immediato
            const loadingBtn = $(this);
            loadingBtn.html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...').prop('disabled', true);
            
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
                    loadingBtn.html('Salva modifiche').prop('disabled', false);
                    
                    if (response && response.success) {
                        $('#modificaAssociazioneModal').modal('hide');
                        loadAnagraficheAssociate();
                        
                        // Usa la funzione definita in cima al file
                        showSweetAlert('Successo', response.message || 'Associazione aggiornata con successo', 'success');
                    } else {
                        showSweetAlert('Errore', response ? (response.message || 'Errore durante l\'aggiornamento') : 'Risposta non valida dal server', 'error');
                    }
                },
                error: function(xhr) {
                    loadingBtn.html('Salva modifiche').prop('disabled', false);
                    
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showSweetAlert('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Eliminazione associazione
        $(document).on('click', '.btn-delete-associazione', function() {
            const id = $(this).data('id');
            const anagrafica = $(this).data('anagrafica');
            
            $('#delete-anagrafica-name').text(anagrafica);
            $('#btn-confirm-delete-associazione').data('id', id);
            $('#deleteAssociazioneModal').modal('show');
        });
        
        // Conferma eliminazione associazione
        $('#btn-confirm-delete-associazione').on('click', function() {
            const id = $(this).data('id');
            
            // Visualizza feedback immediato
            const loadingBtn = $(this);
            loadingBtn.html('<i class="fas fa-spinner fa-spin"></i> Eliminazione...').prop('disabled', true);
            
            $.ajax({
                url: `<?= base_url('api/anagrafiche/contatti') ?>/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    loadingBtn.html('Elimina').prop('disabled', false);
                    
                    if (response.success) {
                        $('#deleteAssociazioneModal').modal('hide');
                        loadAnagraficheAssociate();
                        showSweetAlert('Successo', response.message, 'success');
                    } else {
                        showSweetAlert('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    loadingBtn.html('Elimina').prop('disabled', false);
                    
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showSweetAlert('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Imposta contatto come principale
        $(document).on('click', '.btn-set-principale', function() {
            const idAnagrafica = $(this).data('anagrafica-id');
            
            // Visualizza feedback immediato
            const loadingBtn = $(this);
            loadingBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.ajax({
                url: `<?= base_url('api/anagrafiche') ?>/${idAnagrafica}/contatti/${contattoId}/principale`,
                type: 'PUT',
                dataType: 'json',
                success: function(response) {
                    loadingBtn.html('<i class="fas fa-star"></i>').prop('disabled', false);
                    
                    if (response.success) {
                        loadAnagraficheAssociate();
                        showSweetAlert('Successo', response.message, 'success');
                    } else {
                        showSweetAlert('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    loadingBtn.html('<i class="fas fa-star"></i>').prop('disabled', false);
                    
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showSweetAlert('Errore', errorMessage, 'error');
                }
            });
        });
        
        // Funzione per caricare le anagrafiche associate
        function loadAnagraficheAssociate() {
            $.ajax({
                url: `<?= base_url('api/contatti') ?>/${contattoId}/anagrafiche`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderAnagraficheAssociate(response.data);
                    } else {
                        showSweetAlert('Errore', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Si è verificato un errore';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showSweetAlert('Errore', errorMessage, 'error');
                    $('#anagrafiche-list').html('<tr><td colspan="5" class="text-center">Errore nel caricamento dei dati</td></tr>');
                }
            });
        }
        
        // Funzione per caricare le anagrafiche disponibili
        function loadAnagraficheDisponibili() {
            $.ajax({
                url: '<?= base_url('api/anagrafiche/getAnagrafiche') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Seleziona anagrafica...</option>';
                        $.each(response.data, function(i, item) {
                            options += `<option value="${item.id}">${item.ragione_sociale}</option>`;
                        });
                        $('#id_anagrafica').html(options);
                    }
                }
            });
        }
        
        // Funzione per renderizzare le anagrafiche associate
        function renderAnagraficheAssociate(data) {
            let html = '';
            
            if (data.length === 0) {
                html = '<tr><td colspan="5" class="text-center">Nessuna anagrafica associata</td></tr>';
            } else {
                $.each(data, function(i, item) {
                    const principaleIcon = item.principale == 1 ? 
                        '<span class="badge badge-success"><i class="fas fa-check"></i></span>' : 
                        '<button class="btn btn-xs btn-outline-success btn-set-principale" data-anagrafica-id="' + item.id_anagrafica + '"><i class="fas fa-star"></i></button>';
                    
                    html += `
                        <tr>
                            <td>${item.ragione_sociale}</td>
                            <td>${item.citta || '-'}</td>
                            <td>${item.ruolo || '-'}</td>
                            <td class="text-center">${principaleIcon}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary btn-edit-associazione" 
                                        data-id="${item.id}" 
                                        data-anagrafica="${item.ragione_sociale}"
                                        data-ruolo="${item.ruolo || ''}" 
                                        data-principale="${item.principale}" 
                                        data-note="${item.note || ''}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-associazione" 
                                        data-id="${item.id}" 
                                        data-anagrafica="${item.ragione_sociale}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
            
            $('#anagrafiche-list').html(html);
        }
    });
</script>
<?= $this->endSection() ?> 
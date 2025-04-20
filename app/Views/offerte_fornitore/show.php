<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettaglio Offerta Fornitore<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettaglio Offerta Fornitore <?= esc($offerta['numero']) ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('offerte-fornitore') ?>">Offerte Fornitore</a></li>
<li class="breadcrumb-item active">Dettaglio Offerta</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group">
                <a href="<?= site_url('offerte-fornitore/edit/' . $offerta['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifica
                </a>
                
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                    <i class="fas fa-trash"></i> Elimina
                </button>
                
                <a href="<?= site_url('pdf/openOffertaFornitore/' . $offerta['id']) ?>" class="btn btn-info" target="_blank">
                    <i class="fas fa-file-pdf"></i> Genera PDF
                </a>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> Azioni
                    </button>
                    <div class="dropdown-menu">
                        <h6 class="dropdown-header">Gestione Stato</h6>
                        <a class="dropdown-item" href="<?= site_url('offerte-fornitore/cambia-stato/' . $offerta['id']) ?>?stato=ricevuta">Imposta come Ricevuta</a>
                        <a class="dropdown-item" href="<?= site_url('offerte-fornitore/cambia-stato/' . $offerta['id']) ?>?stato=in_valutazione">Imposta In Valutazione</a>
                        <a class="dropdown-item" href="<?= site_url('offerte-fornitore/cambia-stato/' . $offerta['id']) ?>?stato=approvata">Approva Offerta</a>
                        <a class="dropdown-item" href="<?= site_url('offerte-fornitore/cambia-stato/' . $offerta['id']) ?>?stato=rifiutata">Rifiuta Offerta</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Gestione Costi</h6>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#scontoTrasportoModal">
                            Modifica Sconto e Trasporto
                        </a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Gestione Voci</h6>
                        <?php if (!empty($offerta['id_richiesta_offerta']) && count($vociRichiesta) > 0): ?>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importVociModal">
                                Importa Voci da Richiesta
                            </a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addVoceModal">
                            Aggiungi Voce
                        </a>
                    </div>
                </div>
                
                <a href="<?= site_url('offerte-fornitore') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informazioni Offerta -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informazioni Offerta</h3>
                    <div class="card-tools">
                        <span class="badge badge-<?php 
                            switch($offerta['stato']) {
                                case 'ricevuta': echo 'info'; break;
                                case 'in_valutazione': echo 'warning'; break;
                                case 'approvata': echo 'success'; break;
                                case 'rifiutata': echo 'danger'; break;
                                case 'scaduta': echo 'secondary'; break;
                                default: echo 'primary';
                            }
                        ?>">
                            <?php 
                                switch($offerta['stato']) {
                                    case 'ricevuta': echo 'Ricevuta'; break;
                                    case 'in_valutazione': echo 'In Valutazione'; break;
                                    case 'approvata': echo 'Approvata'; break;
                                    case 'rifiutata': echo 'Rifiutata'; break;
                                    case 'scaduta': echo 'Scaduta'; break;
                                    default: echo ucfirst($offerta['stato']);
                                }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Numero:</strong> <?= esc($offerta['numero']) ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($offerta['data'])) ?></p>
                            <p><strong>Oggetto:</strong> <?= esc($offerta['oggetto']) ?></p>
                            <?php if(!empty($offerta['descrizione'])): ?>
                                <p><strong>Descrizione:</strong> <?= esc($offerta['descrizione']) ?></p>
                            <?php endif; ?>
                            <?php if(!empty($offerta['note'])): ?>
                                <p><strong>Note:</strong> <?= nl2br(esc($offerta['note'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Valuta:</strong> <?= esc($offerta['valuta']) ?></p>
                            <p><strong>Importo Totale Voci:</strong> 
                                <?php 
                                $importoVoci = 0;
                                foreach ($voci as $voce) {
                                    $importoVoci += $voce['importo'];
                                }
                                echo number_format($importoVoci, 2, ',', '.') . ' ' . esc($offerta['valuta']);
                                ?>
                            </p>
                            <?php if (!empty($offerta['sconto_totale']) && $offerta['sconto_totale'] > 0): ?>
                                <p><strong>Sconto Totale (<?= number_format($offerta['sconto_totale'], 2, ',', '.') ?>%):</strong> 
                                    <?= number_format($importoVoci * ($offerta['sconto_totale'] / 100), 2, ',', '.') ?> <?= esc($offerta['valuta']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($offerta['sconto_fisso']) && $offerta['sconto_fisso'] > 0): ?>
                                <p><strong>Sconto Fisso:</strong> 
                                    <?= number_format($offerta['sconto_fisso'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($offerta['costo_trasporto']) && $offerta['costo_trasporto'] > 0): ?>
                                <p><strong>Costo Trasporto:</strong> 
                                    <?= number_format($offerta['costo_trasporto'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?>
                                </p>
                            <?php endif; ?>
                            <p><strong>Importo Totale Finale:</strong> <?= number_format($offerta['importo_totale'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?></p>
                            <?php if (!empty($offerta['nome_progetto'])): ?>
                                <p><strong>Progetto:</strong> <?= esc($offerta['nome_progetto']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($offerta['numero_rdo'])): ?>
                                <p>
                                    <strong>Richiesta d'Offerta:</strong> 
                                    <a href="<?= site_url('richieste-offerta/' . $offerta['id_richiesta_offerta']) ?>">
                                        <?= esc($offerta['numero_rdo']) ?> - <?= esc($offerta['oggetto_rdo']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <p><strong>Creato da:</strong> <?= esc($offerta['nome_utente'] . ' ' . $offerta['cognome_utente']) ?></p>
                            <p><strong>Data Ricezione:</strong> <?= date('d/m/Y H:i', strtotime($offerta['data_ricezione'])) ?></p>
                            <?php if(!empty($offerta['data_approvazione'])): ?>
                                <p><strong>Data Approvazione:</strong> <?= date('d/m/Y H:i', strtotime($offerta['data_approvazione'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voci Offerta -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Voci Offerta</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#addVoceModal">
                            <i class="fas fa-plus"></i> Aggiungi Voce
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descrizione</th>
                                    <th>Quantità</th>
                                    <th>Prezzo Unitario</th>
                                    <th>Sconto</th>
                                    <th>Importo</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($voci)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nessuna voce presente</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($voci as $index => $voce): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?= esc($voce['descrizione']) ?>
                                                <?php if (!empty($voce['codice'])): ?>
                                                    <small class="text-muted d-block">Codice: <?= esc($voce['codice']) ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($voce['id_materiale']) && !empty($voce['nome_materiale'])): ?>
                                                    <small class="text-primary d-block">Materiale collegato: <?= esc($voce['nome_materiale']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($voce['quantita'], 2, ',', '.') ?> <?= esc($voce['unita_misura']) ?></td>
                                            <td><?= number_format($voce['prezzo_unitario'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?></td>
                                            <td><?= !empty($voce['sconto']) ? number_format($voce['sconto'], 2, ',', '.') . '%' : '-' ?></td>
                                            <td><?= number_format($voce['importo'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary edit-voce-btn"
                                                            data-id="<?= $voce['id'] ?>"
                                                            data-descrizione="<?= esc($voce['descrizione']) ?>"
                                                            data-codice="<?= esc($voce['codice']) ?>"
                                                            data-quantita="<?= $voce['quantita'] ?>"
                                                            data-unita-misura="<?= esc($voce['unita_misura']) ?>"
                                                            data-prezzo-unitario="<?= $voce['prezzo_unitario'] ?>"
                                                            data-sconto="<?= $voce['sconto'] ?? 0 ?>"
                                                            data-importo="<?= $voce['importo'] ?>"
                                                            data-materiale-id="<?= $voce['id_materiale'] ?? '' ?>"
                                                            data-progetto-id="<?= $voce['id_progetto'] ?? '' ?>"
                                                            data-toggle="modal" data-target="#editVoceModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= site_url('offerte-fornitore/rimuovi-voce/' . $offerta['id'] . '/' . $voce['id']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Sei sicuro di voler eliminare questa voce?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-right">Totale:</th>
                                    <th><?= number_format($offerta['importo_totale'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Fornitore -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fornitore</h3>
                </div>
                <div class="card-body">
                    <h5><?= esc($offerta['nome_fornitore']) ?></h5>
                    <p>
                        <?php if (!empty($offerta['indirizzo'])): ?>
                            <?= esc($offerta['indirizzo']) ?><br>
                            <?= esc($offerta['cap']) ?> <?= esc($offerta['citta']) ?><br>
                            <?= esc($offerta['nazione']) ?><br>
                        <?php endif; ?>
                        
                        <?php if (!empty($offerta['partita_iva']) || !empty($offerta['codice_fiscale'])): ?>
                            <?php if (!empty($offerta['partita_iva'])): ?>P.IVA: <?= esc($offerta['partita_iva']) ?><br><?php endif; ?>
                            <?php if (!empty($offerta['codice_fiscale'])): ?>C.F.: <?= esc($offerta['codice_fiscale']) ?><br><?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($offerta['email']) || !empty($offerta['telefono'])): ?>
                            <?php if (!empty($offerta['email'])): ?>Email: <?= esc($offerta['email']) ?><br><?php endif; ?>
                            <?php if (!empty($offerta['telefono'])): ?>Tel: <?= esc($offerta['telefono']) ?><?php endif; ?>
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!empty($offerta['nome_referente'])): ?>
                        <h6 class="mt-3">Referente</h6>
                        <p>
                            <?= esc($offerta['nome_referente'] . ' ' . $offerta['cognome_referente']) ?><br>
                            <?php if (!empty($offerta['email_referente'])): ?>Email: <?= esc($offerta['email_referente']) ?><br><?php endif; ?>
                            <?php if (!empty($offerta['telefono_referente'])): ?>Tel: <?= esc($offerta['telefono_referente']) ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Allegati -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Allegati</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#addAllegatoModal">
                            <i class="fas fa-plus"></i> Aggiungi Allegato
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($allegati)): ?>
                            <li class="list-group-item text-center">Nessun allegato presente</li>
                        <?php else: ?>
                            <?php foreach ($allegati as $allegato): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="<?= site_url('offerte-fornitore/download-allegato/' . $allegato['id']) ?>">
                                                <i class="fas fa-file mr-2"></i> <?= esc($allegato['nome_file']) ?>
                                            </a>
                                            <?php if (!empty($allegato['descrizione'])): ?>
                                                <small class="text-muted d-block"><?= esc($allegato['descrizione']) ?></small>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                Caricato il <?= date('d/m/Y H:i', strtotime($allegato['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)"
                                               class="btn btn-sm btn-danger delete-allegato-btn"
                                               data-id="<?= $allegato['id'] ?>"
                                               data-nome="<?= esc($allegato['nome_file']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Elimina Offerta -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Conferma Eliminazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare questa offerta fornitore?<br>
                <strong><?= esc($offerta['numero']) ?> - <?= esc($offerta['oggetto']) ?></strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <a href="<?= site_url('offerte-fornitore/delete/' . $offerta['id']) ?>" class="btn btn-danger">Elimina</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Voce -->
<div class="modal fade" id="addVoceModal" tabindex="-1" role="dialog" aria-labelledby="addVoceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVoceModalLabel">Aggiungi Voce</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Nav tabs per scegliere tra ricerca materiale o inserimento manuale -->
                <ul class="nav nav-tabs" id="voceTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="search-tab" data-toggle="tab" href="#search-content" role="tab" aria-controls="search-content" aria-selected="true">
                            <i class="fas fa-search"></i> Cerca Materiale
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual-content" role="tab" aria-controls="manual-content" aria-selected="false">
                            <i class="fas fa-edit"></i> Inserimento Manuale
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="new-material-tab" data-toggle="tab" href="#new-material-content" role="tab" aria-controls="new-material-content" aria-selected="false">
                            <i class="fas fa-plus-circle"></i> Nuovo Materiale
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content pt-3" id="voceTabsContent">
                    <!-- Tab per la ricerca materiale -->
                    <div class="tab-pane fade show active" id="search-content" role="tabpanel" aria-labelledby="search-tab">
                        <!-- Form di ricerca materiali -->
                        <div class="form-group">
                            <label for="searchMateriale">Cerca Materiale:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchMateriale" placeholder="Digita codice o descrizione del materiale...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnCercaMateriale">
                                        <i class="fas fa-search"></i> Cerca
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Inserisci almeno 2 caratteri per avviare la ricerca</small>
                        </div>

                        <!-- Risultati della ricerca -->
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Descrizione</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody id="materialiRisultati">
                                    <tr>
                                        <td colspan="3" class="text-center">Effettua una ricerca per visualizzare i materiali</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab per l'inserimento manuale -->
                    <div class="tab-pane fade" id="manual-content" role="tabpanel" aria-labelledby="manual-tab">
                        <p class="text-muted">Inserisci manualmente i dati della voce se il materiale non è presente nel catalogo.</p>
                    </div>
                    
                    <!-- Tab per il nuovo materiale -->
                    <div class="tab-pane fade" id="new-material-content" role="tabpanel" aria-labelledby="new-material-tab">
                        <p class="text-muted">Inserisci i dati per creare un nuovo materiale nel catalogo.</p>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="new_codice">Codice Materiale*</label>
                                <input type="text" class="form-control" id="new_codice" name="new_codice" required>
                                <small class="form-text text-muted">Codice univoco per identificare il materiale</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="new_produttore">Produttore</label>
                                <input type="text" class="form-control" id="new_produttore" name="new_produttore">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_descrizione">Descrizione Materiale*</label>
                            <textarea class="form-control" id="new_descrizione" name="new_descrizione" rows="2" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_materiale">Tipo Materiale</label>
                            <input type="text" class="form-control" id="new_materiale" name="new_materiale">
                        </div>
                        
                        <div class="form-group">
                            <label>Categoria</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="new_commerciale" name="new_commerciale" value="1">
                                <label class="custom-control-label" for="new_commerciale">Commerciale</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="new_meccanica" name="new_meccanica" value="1">
                                <label class="custom-control-label" for="new_meccanica">Meccanica</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="new_elettrica" name="new_elettrica" value="1">
                                <label class="custom-control-label" for="new_elettrica">Elettrica</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="new_pneumatica" name="new_pneumatica" value="1">
                                <label class="custom-control-label" for="new_pneumatica">Pneumatica</label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Il materiale verrà salvato nel catalogo e collegato a questa voce d'offerta.
                        </div>
                        
                        <button type="button" class="btn btn-primary" id="btnUsaNuovoMateriale">
                            <i class="fas fa-check"></i> Usa Questo Materiale
                        </button>
                    </div>
                </div>
                
                <!-- Form comune per entrambe le modalità -->
                <form action="<?= site_url('offerte-fornitore/aggiungi-voce/' . $offerta['id']) ?>" method="post" id="formAddVoce">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id_materiale" name="id_materiale">
                    <input type="hidden" id="is_new_material" name="is_new_material" value="0">
                    <hr>
                    <div class="form-group">
                        <label for="descrizione">Descrizione*</label>
                        <input type="text" class="form-control" id="descrizione" name="descrizione" required>
                    </div>
                    <div class="form-group">
                        <label for="codice">Codice Articolo</label>
                        <input type="text" class="form-control" id="codice" name="codice">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="quantita">Quantità*</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="quantita" name="quantita" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="unita_misura">Unità di Misura*</label>
                            <select class="form-control" id="unita_misura" name="unita_misura" required>
                                <option value="pz">pz - Pezzi</option>
                                <option value="kg">kg - Chilogrammi</option>
                                <option value="m">m - Metri</option>
                                <option value="m2">m² - Metri quadri</option>
                                <option value="m3">m³ - Metri cubi</option>
                                <option value="h">h - Ore</option>
                                <option value="g">g - Giorni</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prezzo_unitario">Prezzo Unitario*</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="prezzo_unitario" name="prezzo_unitario" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sconto">Sconto (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control" id="sconto" name="sconto" value="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="importo">Importo Totale*</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="importo" name="importo" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id_progetto">Progetto (opzionale)</label>
                        <select class="form-control" id="id_progetto" name="id_progetto">
                            <option value="">Nessun progetto</option>
                            <?php foreach ($progetti as $progetto): ?>
                                <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifica Voce -->
<div class="modal fade" id="editVoceModal" tabindex="-1" role="dialog" aria-labelledby="editVoceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVoceModalLabel">Modifica Voce</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('offerte-fornitore/aggiorna-voce/' . $offerta['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id_voce">
                <input type="hidden" name="id_materiale" id="edit_id_materiale">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_descrizione">Descrizione*</label>
                        <input type="text" class="form-control" id="edit_descrizione" name="descrizione" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_codice">Codice Articolo</label>
                        <input type="text" class="form-control" id="edit_codice" name="codice">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_quantita">Quantità*</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="edit_quantita" name="quantita" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_unita_misura">Unità di Misura*</label>
                            <select class="form-control" id="edit_unita_misura" name="unita_misura" required>
                                <option value="pz">pz - Pezzi</option>
                                <option value="kg">kg - Chilogrammi</option>
                                <option value="m">m - Metri</option>
                                <option value="m2">m² - Metri quadri</option>
                                <option value="m3">m³ - Metri cubi</option>
                                <option value="h">h - Ore</option>
                                <option value="g">g - Giorni</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_prezzo_unitario">Prezzo Unitario*</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_prezzo_unitario" name="prezzo_unitario" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_sconto">Sconto (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control" id="edit_sconto" name="sconto" value="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_importo">Importo Totale*</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_importo" name="importo" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_id_progetto">Progetto (opzionale)</label>
                        <select class="form-control" id="edit_id_progetto" name="id_progetto">
                            <option value="">Nessun progetto</option>
                            <?php foreach ($progetti as $progetto): ?>
                                <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Allegato -->
<div class="modal fade" id="addAllegatoModal" tabindex="-1" role="dialog" aria-labelledby="addAllegatoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAllegatoModalLabel">Aggiungi Allegato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('offerte-fornitore/carica-allegato/' . $offerta['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="allegato">File*</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="allegato" name="allegato" required>
                            <label class="custom-file-label" for="allegato">Scegli file...</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descrizione_allegato">Descrizione</label>
                        <textarea class="form-control" id="descrizione_allegato" name="descrizione" rows="3"></textarea>
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

<!-- Modal Importa Voci da Richiesta -->
<?php if (!empty($offerta['id_richiesta_offerta']) && count($vociRichiesta) > 0): ?>
<div class="modal fade" id="importVociModal" tabindex="-1" role="dialog" aria-labelledby="importVociModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importVociModalLabel">Importa Voci da Richiesta d'Offerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('offerte-fornitore/importa-voci-richiesta/' . $offerta['id']) ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_richiesta_offerta" value="<?= $offerta['id_richiesta_offerta'] ?>">
                <div class="modal-body">
                    <p>Seleziona le voci della richiesta d'offerta da importare:</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Descrizione</th>
                                    <th>Quantità</th>
                                    <th>Unità di Misura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vociRichiesta as $voce): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input voce-checkbox" type="checkbox" name="voci[]" value="<?= $voce['id'] ?>" id="voce<?= $voce['id'] ?>">
                                                <label class="form-check-label" for="voce<?= $voce['id'] ?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <?= esc($voce['descrizione']) ?>
                                            <?php if (!empty($voce['codice'])): ?>
                                                <small class="text-muted d-block">Codice: <?= esc($voce['codice']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format($voce['quantita'], 2, ',', '.') ?></td>
                                        <td><?= esc($voce['unita_misura']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Importa Voci Selezionate</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal per Sconto Totale e Costo Trasporto -->
<div class="modal fade" id="scontoTrasportoModal" tabindex="-1" role="dialog" aria-labelledby="scontoTrasportoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scontoTrasportoModalLabel">Modifica Sconto e Trasporto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('offerte-fornitore/aggiorna-costi/' . $offerta['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sconto_totale">Sconto Totale (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="sconto_totale" name="sconto_totale" value="<?= esc($offerta['sconto_totale'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Sconto percentuale applicato al totale delle voci</small>
                    </div>
                    <div class="form-group">
                        <label for="sconto_fisso">Sconto Fisso</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="sconto_fisso" name="sconto_fisso" value="<?= esc($offerta['sconto_fisso'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Sconto fisso da aggiungere al totale</small>
                    </div>
                    <div class="form-group">
                        <label for="costo_trasporto">Costo Trasporto</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="costo_trasporto" name="costo_trasporto" value="<?= esc($offerta['costo_trasporto'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($offerta['valuta']) ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Costo di spedizione o trasporto da aggiungere al totale</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inizializza Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        // Inizializza plugin per i file input
        bsCustomFileInput.init();
        
        // Gestione selezione/deselezione di tutte le voci nella modale di importazione
        $('#selectAll').change(function() {
            $('.voce-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Calcolo importo al cambio di quantità o prezzo unitario (aggiungi voce)
        $('#quantita, #prezzo_unitario, #sconto').on('input', function() {
            const quantita = parseFloat($('#quantita').val()) || 0;
            const prezzoUnitario = parseFloat($('#prezzo_unitario').val()) || 0;
            const sconto = parseFloat($('#sconto').val()) || 0;
            const importo = (quantita * prezzoUnitario * (1 - sconto / 100)).toFixed(2);
            $('#importo').val(importo);
        });
        
        // Calcolo importo al cambio di quantità o prezzo unitario (modifica voce)
        $('#edit_quantita, #edit_prezzo_unitario, #edit_sconto').on('input', function() {
            const quantita = parseFloat($('#edit_quantita').val()) || 0;
            const prezzoUnitario = parseFloat($('#edit_prezzo_unitario').val()) || 0;
            const sconto = parseFloat($('#edit_sconto').val()) || 0;
            const importo = (quantita * prezzoUnitario * (1 - sconto / 100)).toFixed(2);
            $('#edit_importo').val(importo);
        });
        
        // Popola i campi nella modale di modifica voce
        $('.edit-voce-btn').click(function() {
            const id = $(this).data('id');
            const descrizione = $(this).data('descrizione');
            const codice = $(this).data('codice');
            const quantita = $(this).data('quantita');
            const unitaMisura = $(this).data('unita-misura');
            const prezzoUnitario = $(this).data('prezzo-unitario');
            const sconto = $(this).data('sconto');
            const importo = $(this).data('importo');
            const idMateriale = $(this).data('materiale-id') || '';
            const idProgetto = $(this).data('progetto-id') || '';
            
            $('#edit_id_voce').val(id);
            $('#edit_descrizione').val(descrizione);
            $('#edit_codice').val(codice);
            $('#edit_quantita').val(quantita);
            $('#edit_unita_misura').val(unitaMisura);
            $('#edit_prezzo_unitario').val(prezzoUnitario);
            $('#edit_sconto').val(sconto);
            $('#edit_importo').val(importo);
            $('#edit_id_materiale').val(idMateriale);
            $('#edit_id_progetto').val(idProgetto);
        });

        // Gestione della conferma di eliminazione allegato tramite SweetAlert
        $('.delete-allegato-btn').click(function() {
            const idAllegato = $(this).data('id');
            const nomeAllegato = $(this).data('nome');
            
            Swal.fire({
                title: 'Conferma eliminazione',
                text: 'Sei sicuro di voler eliminare l\'allegato "' + nomeAllegato + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url('offerte-fornitore/delete-allegato/') ?>' + idAllegato;
                }
            });
        });
        
        // Gestione della conferma di cambio stato dell'offerta
        $('.dropdown-menu a[href*="cambia-stato"]').click(function(e) {
            e.preventDefault();
            
            const link = $(this).attr('href');
            const stato = link.split('stato=')[1];
            let statoLabel = '';
            let icon = 'question';
            let confirmButtonColor = '#3085d6';
            
            // Imposta messaggio e colore in base allo stato
            switch(stato) {
                case 'ricevuta':
                    statoLabel = 'Ricevuta';
                    icon = 'info';
                    break;
                case 'in_valutazione':
                    statoLabel = 'In Valutazione';
                    icon = 'info';
                    break;
                case 'approvata':
                    statoLabel = 'Approvata';
                    icon = 'success';
                    confirmButtonColor = '#28a745';
                    break;
                case 'rifiutata':
                    statoLabel = 'Rifiutata';
                    icon = 'warning';
                    confirmButtonColor = '#dc3545';
                    break;
                case 'scaduta':
                    statoLabel = 'Scaduta';
                    icon = 'warning';
                    break;
            }
            
            Swal.fire({
                title: 'Cambio stato',
                text: 'Vuoi impostare lo stato dell\'offerta a "' + statoLabel + '"?',
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sì, cambia stato',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });
        
        // Ricerca materiali
        $('#btnCercaMateriale').click(function() {
            cercaMateriali();
        });

        $('#searchMateriale').keypress(function(e) {
            if (e.which == 13) {
                cercaMateriali();
                e.preventDefault();
            }
        });

        // Funzione per cercare i materiali
        function cercaMateriali() {
            const searchTerm = $('#searchMateriale').val();
            if (searchTerm.length < 2) {
                Swal.fire({
                    title: 'Attenzione',
                    text: 'Inserisci almeno 2 caratteri per la ricerca',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '<?= site_url('materiali/search') ?>',
                type: 'GET',
                data: {
                    term: searchTerm
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#materialiRisultati').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Ricerca in corso...</td></tr>');
                },
                success: function(response) {
                    $('#materialiRisultati').empty();

                    if (response.length === 0) {
                        $('#materialiRisultati').html('<tr><td colspan="3" class="text-center">Nessun materiale trovato</td></tr>');
                        return;
                    }

                    $.each(response, function(index, materiale) {
                        $('#materialiRisultati').append(`
                        <tr>
                            <td>${materiale.codice}</td>
                            <td>${materiale.descrizione}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success selezionaMateriale" 
                                        data-id="${materiale.id}"
                                        data-codice="${materiale.codice}"
                                        data-descrizione="${materiale.descrizione}">
                                    <i class="fas fa-plus"></i> Seleziona
                                </button>
                            </td>
                        </tr>
                    `);
                    });

                    // Evento per selezionare un materiale
                    $('.selezionaMateriale').click(function() {
                        const idMateriale = $(this).data('id');
                        const codice = $(this).data('codice');
                        const descrizione = $(this).data('descrizione');
                        
                        // Imposta i valori nei campi del form
                        $('#id_materiale').val(idMateriale);
                        $('#codice').val(codice);
                        $('#descrizione').val(descrizione);
                        $('#is_new_material').val('0');
                        
                        // Aggiorna l'URL del form per utilizzare l'endpoint standard
                        $('#formAddVoce').attr('action', '<?= site_url('offerte-fornitore/aggiungi-voce/' . $offerta['id']) ?>');
                        
                        // Focus sul campo quantità
                        $('#quantita').focus();
                        
                        // Passa automaticamente alla tab di inserimento manuale
                        $('#manual-tab').tab('show');
                    });
                },
                error: function(xhr, status, error) {
                    $('#materialiRisultati').html('<tr><td colspan="3" class="text-center text-danger">Errore durante la ricerca. Riprova.</td></tr>');
                    console.error(error);
                }
            });
        }
        
        // Gestione del pulsante per utilizzare un nuovo materiale
        $('#btnUsaNuovoMateriale').click(function() {
            // Validazione dei campi obbligatori del nuovo materiale
            const newCodice = $('#new_codice').val();
            const newDescrizione = $('#new_descrizione').val();
            
            if (!newCodice || !newDescrizione) {
                Swal.fire({
                    title: 'Attenzione',
                    text: 'Codice e descrizione del materiale sono campi obbligatori',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Compilazione dei campi del form con i dati del nuovo materiale
            $('#codice').val(newCodice);
            $('#descrizione').val(newDescrizione);
            
            // Indica che si sta inserendo un nuovo materiale
            $('#is_new_material').val('1');
            
            // Aggiorna l'URL del form per utilizzare l'endpoint di creazione materiale
            $('#formAddVoce').attr('action', '<?= site_url('offerte-fornitore/aggiungi-materiale-voce/' . $offerta['id']) ?>');
            
            // Copia i campi nascosti al form principale
            $('#formAddVoce').append(`<input type="hidden" name="new_codice" value="${newCodice}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_descrizione" value="${newDescrizione}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_materiale" value="${$('#new_materiale').val()}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_produttore" value="${$('#new_produttore').val()}">`);
            
            // Gestione checkbox
            $('#formAddVoce').append(`<input type="hidden" name="new_commerciale" value="${$('#new_commerciale').is(':checked') ? '1' : '0'}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_meccanica" value="${$('#new_meccanica').is(':checked') ? '1' : '0'}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_elettrica" value="${$('#new_elettrica').is(':checked') ? '1' : '0'}">`);
            $('#formAddVoce').append(`<input type="hidden" name="new_pneumatica" value="${$('#new_pneumatica').is(':checked') ? '1' : '0'}">`);
            
            // Passa alla tab di inserimento manuale per completare i dettagli della voce
            $('#manual-tab').tab('show');
            
            // Focus sul campo quantità
            $('#quantita').focus();
            
            // Messaggio di conferma all'utente
            Swal.fire({
                title: 'Nuovo materiale pronto',
                text: 'Completa i dettagli della voce (quantità, prezzo, ecc.) e salva per aggiungere il nuovo materiale all\'offerta',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        });
    });
</script>
<?= $this->endSection() ?> 
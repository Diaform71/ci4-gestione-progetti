<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Richieste d'Offerta<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Richieste d'Offerta<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta') ?>">Richieste d'Offerta</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <?= view('layouts/partials/_alert') ?>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="float-right">
                    <?php if ($richiesta['stato'] === 'bozza'): ?>
                        <a href="<?= site_url('richieste-offerta/edit/' . $richiesta['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifica
                        </a>
                    <?php endif; ?>

                    <a href="<?= site_url('pdf/openRDO/' . $richiesta['id']) ?>" class="btn btn-info" target="_blank">
                        <i class="fas fa-file-pdf"></i> Genera PDF
                    </a>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPreviewEmail">
                        <i class="fas fa-envelope"></i> Anteprima Email
                    </button>

                    <?php if (!in_array($richiesta['stato'], ['inviata', 'accettata'])): ?>
                        <a href="javascript:void(0);" class="btn btn-danger btn-elimina-richiesta" data-id="<?= $richiesta['id'] ?>">
                            <i class="fas fa-trash"></i> Elimina
                        </a>
                    <?php endif; ?>

                    <a href="<?= site_url('richieste-offerta') ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Torna all'elenco
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dettagli Richiesta d'Offerta</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Numero</span>
                                        <span class="info-box-number"><?= esc($richiesta['numero']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Data</span>
                                        <span class="info-box-number"><?= date('d/m/Y', strtotime($richiesta['data'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Oggetto:</label>
                            <p class="lead"><?= esc($richiesta['oggetto']) ?></p>
                        </div>

                        <div class="form-group">
                            <label>Descrizione:</label>
                            <div class="p-2 bg-light rounded">
                                <?= nl2br(esc($richiesta['descrizione'] ?? 'Nessuna descrizione disponibile')) ?>
                            </div>
                        </div>

                        <?php if (!empty($richiesta['note'])): ?>
                            <div class="form-group">
                                <label>Note:</label>
                                <div class="p-2 bg-light rounded">
                                    <?= nl2br(esc($richiesta['note'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($richiesta['id_progetto'])): ?>
                            <div class="form-group">
                                <label>Progetto Collegato:</label>
                                <p>
                                    <a href="<?= site_url('progetti/' . $richiesta['id_progetto']) ?>">
                                        <?= esc($richiesta['nome_progetto']) ?>
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sezione Materiali -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Materiali Richiesti</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAggiungiMateriale">
                                <i class="fas fa-plus"></i> Aggiungi Materiale
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Descrizione</th>
                                        <th>Quantità</th>
                                        <th>UM</th>
                                        <th>Progetto</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($voci)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nessun materiale aggiunto a questa richiesta</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($voci as $voce): ?>
                                            <tr>
                                                <td><?= esc($voce['codice']) ?></td>
                                                <td><?= esc($voce['descrizione']) ?></td>
                                                <td><?= esc($voce['quantita']) ?></td>
                                                <td><?= esc($voce['unita_misura']) ?></td>
                                                <td><?= esc($voce['nome_progetto'] ?? '-') ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary editMateriale" 
                                                            data-id="<?= $voce['id'] ?>"
                                                            data-materiale-id="<?= $voce['id_materiale'] ?>"
                                                            data-quantita="<?= $voce['quantita'] ?>"
                                                            data-unita-misura="<?= $voce['unita_misura'] ?>"
                                                            data-progetto-id="<?= $voce['id_progetto'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-rimuovi-materiale" 
                                                       data-id="<?= $voce['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Box per cambio stato -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gestione Stato</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Stato Attuale:
                                    <?php
                                    $badgeClass = 'secondary';
                                    switch ($richiesta['stato']) {
                                        case 'bozza':
                                            $badgeClass = 'warning';
                                            break;
                                        case 'inviata':
                                            $badgeClass = 'primary';
                                            break;
                                        case 'accettata':
                                            $badgeClass = 'success';
                                            break;
                                        case 'rifiutata':
                                            $badgeClass = 'danger';
                                            break;
                                        case 'annullata':
                                            $badgeClass = 'secondary';
                                            break;
                                    }
                                    ?>
                                    <span class="badge badge-<?= $badgeClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $richiesta['stato'])) ?>
                                    </span>
                                </p>

                                <?php if (!empty($richiesta['data_invio'])): ?>
                                    <p>Data Invio: <?= date('d/m/Y H:i', strtotime($richiesta['data_invio'])) ?></p>
                                <?php endif; ?>

                                <?php if (!empty($richiesta['data_accettazione'])): ?>
                                    <p>Data Accettazione: <?= date('d/m/Y H:i', strtotime($richiesta['data_accettazione'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <form action="<?= site_url('richieste-offerta/cambia-stato/' . $richiesta['id']) ?>" method="post">
                                    <?= csrf_field() ?>

                                    <div class="form-group">
                                        <label for="stato">Cambia Stato:</label>
                                        <select class="form-control" id="stato" name="stato">
                                            <option value="bozza" <?= $richiesta['stato'] == 'bozza' ? 'selected' : '' ?>>Bozza</option>
                                            <option value="inviata" <?= $richiesta['stato'] == 'inviata' ? 'selected' : '' ?>>Inviata</option>
                                            <option value="accettata" <?= $richiesta['stato'] == 'accettata' ? 'selected' : '' ?>>Accettata</option>
                                            <option value="rifiutata" <?= $richiesta['stato'] == 'rifiutata' ? 'selected' : '' ?>>Rifiutata</option>
                                            <option value="annullata" <?= $richiesta['stato'] == 'annullata' ? 'selected' : '' ?>>Annullata</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Aggiorna Stato</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Informazioni Fornitore -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informazioni Fornitore</h3>
                    </div>
                    <div class="card-body">
                        <h5><?= esc($richiesta['nome_fornitore']) ?></h5>
                        <address>
                            <?= esc($richiesta['indirizzo'] ?? '') ?><br>
                            <?= esc($richiesta['cap'] ?? '') ?> <?= esc($richiesta['citta'] ?? '') ?> (<?= esc($richiesta['provincia'] ?? '') ?>)<br>
                            <?php if (!empty($richiesta['partita_iva'])): ?>
                                <strong>P.IVA:</strong> <?= esc($richiesta['partita_iva']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($richiesta['codice_fiscale'])): ?>
                                <strong>C.F.:</strong> <?= esc($richiesta['codice_fiscale']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($richiesta['email'])): ?>
                                <strong>Email:</strong> <?= esc($richiesta['email']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($richiesta['telefono'])): ?>
                                <strong>Tel:</strong> <?= esc($richiesta['telefono']) ?><br>
                            <?php endif; ?>
                        </address>

                        <?php if (!empty($richiesta['id_referente'])): ?>
                            <hr>
                            <h6>Referente</h6>
                            <p>
                                <?= esc($richiesta['nome_referente'] . ' ' . $richiesta['cognome_referente']) ?><br>
                                <?php if (!empty($richiesta['email_referente'])): ?>
                                    <strong>Email:</strong> <?= esc($richiesta['email_referente']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($richiesta['telefono_referente'])): ?>
                                    <strong>Tel:</strong> <?= esc($richiesta['telefono_referente']) ?><br>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informazioni Utente -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Creato da</h3>
                    </div>
                    <div class="card-body">
                        <p>
                            <?= esc($richiesta['nome_utente'] . ' ' . $richiesta['cognome_utente']) ?><br>
                            <?php if (!empty($richiesta['email_utente'])): ?>
                                <strong>Email:</strong> <?= esc($richiesta['email_utente']) ?><br>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong>Creato il:</strong> <?= date('d/m/Y H:i', strtotime($richiesta['created_at'])) ?><br>
                            <?php if ($richiesta['updated_at'] != $richiesta['created_at']): ?>
                                <strong>Modificato il:</strong> <?= date('d/m/Y H:i', strtotime($richiesta['updated_at'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Storico Email -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Storico Email</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <?php if (!empty($email_logs) && count($email_logs) > 0): ?>
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Destinatario</th>
                                            <th>Stato</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($email_logs as $log): ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($log['data_invio'])) ?></td>
                                                <td title="<?= esc($log['destinatario']) ?>"><?= mb_substr(esc($log['destinatario']), 0, 15) . (mb_strlen($log['destinatario']) > 15 ? '...' : '') ?></td>
                                                <td>
                                                    <?php if ($log['stato'] == 'inviato'): ?>
                                                        <span class="badge badge-success">Inviata</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Errore</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info visualizzaEmail" data-toggle="modal" data-target="#modalDettaglioEmail" data-id="<?= $log['id'] ?>" data-oggetto="<?= esc($log['oggetto']) ?>" data-corpo="<?= htmlspecialchars($log['corpo']) ?>" data-destinatario="<?= esc($log['destinatario']) ?>" data-cc="<?= esc($log['cc']) ?>" data-ccn="<?= esc($log['ccn']) ?>" data-data="<?= date('d/m/Y H:i', strtotime($log['data_invio'])) ?>" data-stato="<?= esc($log['stato']) ?>" data-allegati="<?= esc($log['allegati'] ?? '[]') ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    Nessuna email inviata per questa richiesta
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal per aggiungere materiale -->
<div class="modal fade" id="modalAggiungiMateriale" tabindex="-1" role="dialog" aria-labelledby="modalAggiungiMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAggiungiMaterialeLabel">Aggiungi Materiale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs per scegliere tra ricerca e nuovo materiale -->
                <ul class="nav nav-tabs" id="materialeTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="search-tab" data-toggle="tab" href="#search-content" role="tab" aria-controls="search-content" aria-selected="true">
                            <i class="fas fa-search"></i> Cerca Esistente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="new-tab" data-toggle="tab" href="#new-content" role="tab" aria-controls="new-content" aria-selected="false">
                            <i class="fas fa-plus"></i> Nuovo Materiale
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content pt-3" id="materialeTabsContent">
                    <!-- Tab per la ricerca -->
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
                        </div>

                        <!-- Risultati della ricerca -->
                        <div id="risultatiRicerca" class="mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialiRisultati">
                                        <tr>
                                            <td colspan="3" class="text-center">Effettua una ricerca per visualizzare i risultati</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab per nuovo materiale -->
                    <div class="tab-pane fade" id="new-content" role="tabpanel" aria-labelledby="new-tab">
                        <form id="formNuovoMateriale" action="<?= site_url('richieste-offerta/aggiungi-nuovo-materiale/' . $richiesta['id']) ?>" method="post" novalidate>
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codice">Codice*:</label>
                                        <input type="text" class="form-control" id="codice" name="codice" required>
                                        <div class="invalid-feedback">
                                            Il codice è obbligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantita">Quantità*:</label>
                                        <input type="number" class="form-control" id="quantitaNuovo" name="quantita" min="0.01" step="0.01" required>
                                        <div class="invalid-feedback">
                                            La quantità deve essere maggiore di zero
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="descrizione">Descrizione*:</label>
                                <textarea class="form-control" id="descrizione" name="descrizione" rows="2" required></textarea>
                                <div class="invalid-feedback">
                                    La descrizione è obbligatoria
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="materiale">Materiale:</label>
                                        <input type="text" class="form-control" id="materiale" name="materiale">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="produttore">Produttore:</label>
                                        <input type="text" class="form-control" id="produttore" name="produttore">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unitaMisuraNuovo">Unità di Misura:</label>
                                        <select class="form-control" id="unitaMisuraNuovo" name="unita_misura">
                                            <option value="pz">pz - Pezzi</option>
                                            <option value="m">m - Metri</option>
                                            <option value="kg">kg - Chilogrammi</option>
                                            <option value="lt">lt - Litri</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idProgettoNuovo">Progetto (opzionale):</label>
                                        <select class="form-control" id="idProgettoNuovo" name="id_progetto">
                                            <option value="">Nessun progetto</option>
                                            <?php if (isset($progetti) && count($progetti) > 0): ?>
                                                <?php foreach ($progetti as $progetto): ?>
                                                    <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Categoria:</label>
                                <div class="d-flex flex-wrap">
                                    <div class="custom-control custom-checkbox mr-3">
                                        <input type="checkbox" class="custom-control-input" id="commerciale" name="commerciale" value="1">
                                        <label class="custom-control-label" for="commerciale">Commerciale</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3">
                                        <input type="checkbox" class="custom-control-input" id="meccanica" name="meccanica" value="1">
                                        <label class="custom-control-label" for="meccanica">Meccanica</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3">
                                        <input type="checkbox" class="custom-control-input" id="elettrica" name="elettrica" value="1">
                                        <label class="custom-control-label" for="elettrica">Elettrica</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="pneumatica" name="pneumatica" value="1">
                                        <label class="custom-control-label" for="pneumatica">Pneumatica</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-success">Salva e Aggiungi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per modificare quantità materiale -->
<div class="modal fade" id="modalModificaMateriale" tabindex="-1" role="dialog" aria-labelledby="modalModificaMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificaMaterialeLabel">Modifica Materiale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formModificaMateriale" action="<?= site_url('richieste-offerta/aggiorna-materiale/' . $richiesta['id']) ?>" method="post" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editIdMateriale" name="id_materiale">
                    <input type="hidden" id="editId" name="id">

                    <div class="form-group">
                        <label for="editQuantita">Quantità:</label>
                        <input type="number" class="form-control" id="editQuantita" name="quantita" min="0.01" step="0.01" required>
                        <div class="invalid-feedback">
                            La quantità deve essere maggiore di zero
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editUnitaMisura">Unità di misura:</label>
                        <select class="form-control" id="editUnitaMisura" name="unita_misura" required>
                            <option value="pz">Pezzi (pz)</option>
                            <option value="kg">Kilogrammi (kg)</option>
                            <option value="g">Grammi (g)</option>
                            <option value="m">Metri (m)</option>
                            <option value="cm">Centimetri (cm)</option>
                            <option value="mm">Millimetri (mm)</option>
                            <option value="l">Litri (l)</option>
                            <option value="ml">Millilitri (ml)</option>
                            <option value="m2">Metri quadri (m²)</option>
                            <option value="m3">Metri cubi (m³)</option>
                            <option value="h">Ore (h)</option>
                            <option value="gg">Giorni (gg)</option>
                            <option value="set">Set</option>
                            <option value="cf">Confezione (cf)</option>
                        </select>
                        <div class="invalid-feedback">
                            Seleziona un'unità di misura
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editIdProgetto">Progetto (opzionale):</label>
                        <select class="form-control" id="editIdProgetto" name="id_progetto">
                            <option value="">Nessun progetto</option>
                            <?php if (isset($progetti) && count($progetti) > 0): ?>
                                <?php foreach ($progetti as $progetto): ?>
                                    <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per aggiungere nuovo materiale -->
<div class="modal fade" id="modalQuantitaMateriale" tabindex="-1" role="dialog" aria-labelledby="modalQuantitaMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQuantitaMaterialeLabel">Specifica Quantità</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAggiungiMateriale" action="<?= site_url('richieste-offerta/aggiungi-materiale/' . $richiesta['id']) ?>" method="post" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="idMateriale" name="id_materiale">

                    <div class="form-group">
                        <label for="quantita">Quantità:</label>
                        <input type="number" class="form-control" id="quantita" name="quantita" min="0.01" step="0.01" required>
                        <div class="invalid-feedback">
                            La quantità deve essere maggiore di zero
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unitaMisura">Unità di misura:</label>
                        <select class="form-control" id="unitaMisura" name="unita_misura" required>
                            <option value="pz">Pezzi (pz)</option>
                            <option value="kg">Kilogrammi (kg)</option>
                            <option value="g">Grammi (g)</option>
                            <option value="m">Metri (m)</option>
                            <option value="cm">Centimetri (cm)</option>
                            <option value="mm">Millimetri (mm)</option>
                            <option value="l">Litri (l)</option>
                            <option value="ml">Millilitri (ml)</option>
                            <option value="m2">Metri quadri (m²)</option>
                            <option value="m3">Metri cubi (m³)</option>
                            <option value="h">Ore (h)</option>
                            <option value="gg">Giorni (gg)</option>
                            <option value="set">Set</option>
                            <option value="cf">Confezione (cf)</option>
                        </select>
                        <div class="invalid-feedback">
                            Seleziona un'unità di misura
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="idProgetto">Progetto (opzionale):</label>
                        <select class="form-control" id="idProgetto" name="id_progetto">
                            <option value="">Nessun progetto</option>
                            <?php if (isset($progetti) && count($progetti) > 0): ?>
                                <?php foreach ($progetti as $progetto): ?>
                                    <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per la preview dell'email -->
<div class="modal fade" id="modalPreviewEmail" tabindex="-1" role="dialog" aria-labelledby="modalPreviewEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewEmailLabel">Anteprima Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formInviaEmail" action="<?= site_url('richieste-offerta/invia-email/' . $richiesta['id']) ?>" method="post" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="templateEmail">Template Email:</label>
                        <div class="input-group">
                            <select class="form-control" id="templateEmail" name="template_id">
                                <option value="">Seleziona un template...</option>
                                <!-- Le opzioni verranno caricate tramite AJAX -->
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="btnRicaricaTemplate">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Seleziona un template per caricare automaticamente oggetto e corpo dell'email</small>
                    </div>

                    <div class="form-group">
                        <label for="emailDestinatario">Destinatario:</label>
                        <select class="form-control select2-tags" id="emailDestinatario" name="destinatario[]" multiple required>
                            <?php if (!empty($contattoPrincipale) && !empty($contattoPrincipale['email'])): ?>
                                <option value="<?= $contattoPrincipale['email'] ?>" selected>
                                    <?= $contattoPrincipale['nome'] ?> <?= $contattoPrincipale['cognome'] ?> (<?= $contattoPrincipale['email'] ?>)
                                </option>
                            <?php endif; ?>

                            <?php if (!empty($contatti)): ?>
                                <?php foreach ($contatti as $contatto): ?>
                                    <?php if (!empty($contatto['email']) && (empty($contattoPrincipale) || $contatto['id'] != $contattoPrincipale['id'])): ?>
                                        <option value="<?= $contatto['email'] ?>">
                                            <?= $contatto['nome'] ?> <?= $contatto['cognome'] ?> (<?= $contatto['email'] ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailCC">CC (opzionale):</label>
                                <select class="form-control select2-tags" id="emailCC" name="cc[]" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailCCN">CCN (opzionale):</label>
                                <select class="form-control select2-tags" id="emailCCN" name="ccn[]" multiple>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="emailOggetto">Oggetto:</label>
                        <input type="text" class="form-control" id="emailOggetto" name="oggetto" value="Richiesta d'Offerta <?= esc($richiesta['numero']) ?> - <?= esc($richiesta['oggetto']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="emailCorpo">Corpo dell'email:</label>
                        <textarea class="form-control summernote" id="emailCorpo" name="corpo" rows="6" required>Gentile <?= esc($richiesta['nome_fornitore']) ?>,

In allegato inviamo la richiesta d'offerta n. <?= esc($richiesta['numero']) ?> relativa a "<?= esc($richiesta['oggetto']) ?>".

Vi preghiamo di inviarci la vostra migliore offerta entro 5 giorni lavorativi.

Cordiali saluti,
<?= esc($richiesta['nome_utente'] . ' ' . $richiesta['cognome_utente']) ?>
</textarea>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Elenco Materiali</h6>
                            <div class="custom-control custom-checkbox float-right">
                                <input type="checkbox" class="custom-control-input" id="mostraTabellaEmail" checked>
                                <label class="custom-control-label" for="mostraTabellaEmail">Mostra tabella materiali</label>
                                <small class="form-text text-muted">Deseleziona se i materiali sono già inclusi nel corpo dell'email con {{materiali}}</small>
                            </div>
                        </div>
                        <div class="card-body p-0" id="tabellaEmailMateriali">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Quantità</th>
                                            <th>Unità</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($voci) && count($voci) > 0): ?>
                                            <?php foreach ($voci as $materiale): ?>
                                                <tr>
                                                    <td><?= esc($materiale['codice']) ?></td>
                                                    <td><?= esc($materiale['descrizione']) ?></td>
                                                    <td><?= esc($materiale['quantita']) ?></td>
                                                    <td><?= esc($materiale['unita_misura']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Nessun materiale associato a questa richiesta</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="includiPDF" name="allega_pdf" value="1" checked>
                            <label class="custom-control-label" for="includiPDF">Includi PDF della richiesta d'offerta</label>
                        </div>
                    </div>

                    <!-- Aggiungiamo un campo nascosto per indicare se la tabella dei materiali è mostrata -->
                    <input type="hidden" name="tabella_materiali" id="tabellaMaterialiField" value="1">

                    <div class="form-group">
                        <label>Allegati aggiuntivi (opzionale):</label>
                        <div id="dropzoneAllegati" class="dropzone">
                            <div class="dz-message needsclick">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i><br>
                                Trascina qui i file o clicca per caricare<br>
                                <span class="note">(Massimo 5 file, max 10MB ciascuno)</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Formati accettati: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, RAR</small>
                    </div>
                    
                    <!-- Campo nascosto per memorizzare gli allegati caricati -->
                    <input type="hidden" name="dropzone_allegati" id="dropzoneAllegatiField" value="">

                    <div id="filePreview" class="mt-2">
                        <!-- Qui verranno mostrati i file selezionati dalla dropzone -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Invia Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Email -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Dettaglio Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Dettagli email -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informazioni Email</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Data invio:</strong> <span id="emailDataInvio"></span></p>
                                        <p><strong>Stato:</strong> <span id="emailStato"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Destinatario:</strong> <span id="emailDestinatarioDettaglio"></span></p>
                                        <div id="emailCCRow" style="display:none">
                                            <p><strong>CC:</strong> <span id="emailCCDettaglio"></span></p>
                                        </div>
                                        <div id="emailCCNRow" style="display:none">
                                            <p><strong>CCN:</strong> <span id="emailCCNDettaglio"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p><strong>Oggetto:</strong> <span id="emailOggettoDettaglio"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corpo email -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Corpo Email</h3>
                            </div>
                            <div class="card-body p-0">
                                <iframe id="emailCorpoFrame" style="width:100%; height:300px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sezione allegati, visibile solo se ci sono allegati -->
                <div class="row mt-3" id="emailAllegatiRow">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Allegati</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="emailAllegatiList">
                                    <!-- Gli allegati verranno aggiunti qui dinamicamente -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Summernote CSS -->
<link href="<?= base_url('plugins/summernote/summernote-bs4.min.css') ?>" rel="stylesheet">
<!-- Select2 CSS -->
<link href="<?= base_url('plugins/select2/css/select2.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" integrity="sha512-WvVX1YO12zmsvTpUQV8s7ZU98DnkaAokcciMZJfnNWyNzm7//QRV61t4aEr0WdIa4pe854QHLTV302vH92FSMw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #006fe6;
        color: #fff;
        padding: 0 10px;
        margin-top: 0.31rem;
    }
    
    /* Stili personalizzati per Dropzone */
    .dropzone {
        border: 2px dashed #0087F7;
        border-radius: 5px;
        background: #F9FBFD;
        min-height: 150px;
        padding: 20px;
        text-align: center;
    }
    
    .dropzone .dz-message {
        margin: 2em 0;
    }
    
    .dropzone .dz-message .note {
        font-size: 0.8em;
        color: #666;
        margin-top: 15px;
        display: block;
    }
    
    .dropzone .dz-preview .dz-success-mark,
    .dropzone .dz-preview .dz-error-mark {
        pointer-events: none;
        opacity: 0;
        z-index: 500;
    }
    
    .dropzone .dz-preview .dz-success-mark svg,
    .dropzone .dz-preview .dz-error-mark svg {
        display: block;
        width: 54px;
        height: 54px;
    }
    
    .dropzone .dz-preview .dz-progress {
        width: 80%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Script JS necessari -->
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- Select2 JS -->
<script src="<?= base_url('plugins/select2/js/select2.min.js') ?>"></script>
<script src="<?= base_url('plugins/select2/js/i18n/it.js') ?>"></script>
<!-- Summernote JS -->
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('plugins/summernote/lang/summernote-it-IT.min.js') ?>"></script>
<!-- Bootstrap Validator -->
<script src="<?= base_url('plugins/bootstrap-validator/bootstrap-validator.js') ?>"></script>

<script>
    $(document).ready(function() {
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
                                        data-dismiss="modal"
                                        data-toggle="modal" 
                                        data-target="#modalQuantitaMateriale">
                                    <i class="fas fa-plus"></i> Seleziona
                                </button>
                            </td>
                        </tr>
                    `);
                    });

                    // Evento per selezionare un materiale
                    $('.selezionaMateriale').click(function() {
                        const idMateriale = $(this).data('id');
                        $('#idMateriale').val(idMateriale);
                        $('#modalAggiungiMateriale').modal('hide');
                        setTimeout(function() {
                            $('#modalQuantitaMateriale').modal('show');
                        }, 500);
                    });
                },
                error: function(xhr, status, error) {
                    $('#materialiRisultati').html('<tr><td colspan="3" class="text-center text-danger">Errore durante la ricerca. Riprova.</td></tr>');
                    console.error(error);
                }
            });
        }

        // Evento per modificare un materiale
        $('.editMateriale').click(function() {
            const id = $(this).data('id');
            const idMateriale = $(this).data('materiale-id');
            const quantita = $(this).data('quantita');
            const unitaMisura = $(this).data('unita-misura');
            const idProgetto = $(this).data('progetto-id');

            $('#editId').val(id);
            $('#editIdMateriale').val(idMateriale);
            $('#editQuantita').val(quantita);
            $('#editUnitaMisura').val(unitaMisura);
            $('#editIdProgetto').val(idProgetto);

            $('#modalModificaMateriale').modal('show');
        });

        // Validazione nativa HTML5 con estensione jQuery
        function validateQuantita(input) {
            const val = parseFloat(input.value);

            if (isNaN(val) || val <= 0) {
                input.setCustomValidity('La quantità deve essere maggiore di zero');
            } else {
                input.setCustomValidity('');
            }
        }

        // Applicazione della validazione al form di aggiunta
        const quantitaInput = document.getElementById('quantita');
        if (quantitaInput) {
            quantitaInput.addEventListener('input', function() {
                validateQuantita(this);
            });
            quantitaInput.addEventListener('invalid', function(e) {
                if (!this.validity.valid) {
                    e.preventDefault();
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">La quantità deve essere maggiore di zero</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        }

        // Applicazione della validazione al form di modifica
        const editQuantitaInput = document.getElementById('editQuantita');
        if (editQuantitaInput) {
            editQuantitaInput.addEventListener('input', function() {
                validateQuantita(this);
            });
            editQuantitaInput.addEventListener('invalid', function(e) {
                if (!this.validity.valid) {
                    e.preventDefault();
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">La quantità deve essere maggiore di zero</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        }
        
        // Validazione per il form di nuovo materiale
        const nuovoMaterialeForm = document.getElementById('formNuovoMateriale');
        if (nuovoMaterialeForm) {
            // Validazione della quantità
            const quantitaNuovoInput = document.getElementById('quantitaNuovo');
            if (quantitaNuovoInput) {
                quantitaNuovoInput.addEventListener('input', function() {
                    validateQuantita(this);
                });
                quantitaNuovoInput.addEventListener('blur', function() {
                    validateQuantita(this);
                });
            }
            
            // Utilizziamo bootstrap-validator per la validazione del form
            $(nuovoMaterialeForm).validator({
                feedback: {
                    success: 'fas fa-check',
                    error: 'fas fa-times'
                }
            }).on('submit', function(e) {
                if (e.isDefaultPrevented()) {
                    // Handle the invalid form
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore di validazione',
                        text: 'Verifica i campi obbligatori e riprova'
                    });
                    return false;
                }
            });
        }

        // Validazione dei form prima del submit
        $('#formAggiungiMateriale').on('submit', function(e) {
            const form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback">Campo richiesto</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                return false;
            }

            return true;
        });

        $('#formModificaMateriale').on('submit', function(e) {
            const form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after('<div class="invalid-feedback">Campo richiesto</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                return false;
            }

            return true;
        });
        
        // Quando cambiamo tab, resetta eventuali messaggi di errore
        $('#materialeTabs a').on('shown.bs.tab', function (e) {
            $('.is-invalid').removeClass('is-invalid');
        });

        // Tooltip per campi obbligatori
        $('[required]').each(function() {
            $(this).closest('.form-group').find('label').append('<span class="text-danger ml-1">*</span>');
        });

        // --- NUOVE FUNZIONALITÀ PER LA GESTIONE DELL'EMAIL ---

        // Inizializzazione di Summernote per il corpo dell'email
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
            callbacks: {
                onImageUpload: function(files) {
                    // Disabilita il caricamento diretto delle immagini
                    Swal.fire({
                        title: 'Attenzione',
                        text: 'Per favore, inserisci le immagini come allegati.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            },
            placeholder: 'Scrivi il corpo dell\'email qui...',
            disableDragAndDrop: true
        });

        // Caricamento template email
        function caricaTemplateEmail() {
            $.ajax({
                url: '<?= site_url('email-templates/get-by-type/RDO') ?>',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#templateEmail').prop('disabled', true);
                    $('#btnRicaricaTemplate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    $('#templateEmail').empty().append('<option value="">Seleziona un template...</option>');

                    if (response.length === 0) {
                        $('#templateEmail').append('<option value="" disabled>Nessun template disponibile</option>');
                    } else {
                        $.each(response, function(index, template) {
                            $('#templateEmail').append(`<option value="${template.id}">${template.nome}</option>`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Errore nel caricamento dei template:', error);
                    Swal.fire({
                        title: 'Errore',
                        text: 'Si è verificato un errore nel caricamento dei template. Riprova.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    $('#templateEmail').prop('disabled', false);
                    $('#btnRicaricaTemplate').prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
                }
            });
        }

        // Carica i template al caricamento della pagina
        caricaTemplateEmail();

        // Ricarica i template quando si preme il pulsante
        $('#btnRicaricaTemplate').click(function() {
            caricaTemplateEmail();
        });

        // Applica il template selezionato
        $('#templateEmail').change(function() {
            const templateId = $(this).val();

            if (!templateId) {
                return;
            }

            $.ajax({
                url: '<?= site_url('email-templates/compila/') ?>' + templateId,
                type: 'POST',
                dataType: 'json',
                data: {
                    id_richiesta: '<?= $richiesta['id'] ?>',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                beforeSend: function() {
                    $('#templateEmail').prop('disabled', true);
                    $('#btnRicaricaTemplate').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $('#emailOggetto').val(response.data.oggetto);
                        $('#emailCorpo').summernote('code', response.data.corpo);
                    } else {
                        Swal.fire({
                            title: 'Errore',
                            text: 'Errore: ' + response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Errore nell\'applicazione del template:', error);
                    Swal.fire({
                        title: 'Errore',
                        text: 'Si è verificato un errore nell\'applicazione del template. Riprova.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    $('#templateEmail').prop('disabled', false);
                    $('#btnRicaricaTemplate').prop('disabled', false);
                }
            });
        });

        // Gestione file allegati
        $('#fileAllegati').on('change', function() {
            const fileCount = this.files.length;
            let totalSize = 0;
            let fileNames = [];

            for (let i = 0; i < fileCount; i++) {
                totalSize += this.files[i].size;
                fileNames.push(this.files[i].name);
            }

            // Controllo dimensione massima (10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB in byte
            if (totalSize > maxSize) {
                Swal.fire({
                    title: 'Attenzione',
                    text: 'La dimensione totale degli allegati supera il limite di 10MB. Riduci la dimensione o il numero di file.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                this.value = '';
                $('.custom-file-label').text('Scegli file...');
                $('#filePreview').empty();
                return;
            }

            // Mostra i nomi dei file selezionati
            if (fileCount > 0) {
                $('.custom-file-label').text(fileCount > 1 ? `${fileCount} file selezionati` : fileNames[0]);

                // Aggiorna la preview dei file
                $('#filePreview').empty();

                const previewList = $('<ul class="list-group list-group-flush"></ul>');
                fileNames.forEach(function(fileName) {
                    previewList.append(`
                    <li class="list-group-item py-2 px-3">
                        <i class="fas fa-file mr-2"></i>
                        ${fileName}
                    </li>
                `);
                });

                $('#filePreview').append(previewList);
            } else {
                $('.custom-file-label').text('Scegli file...');
                $('#filePreview').empty();
            }
        });

        // Validazione del form di invio email
        $('#formInviaEmail').on('submit', function(e) {
            const form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                return false;
            }

            // Verifica che almeno un destinatario sia selezionato
            const destinatari = $('#emailDestinatario').val();
            if (!destinatari || destinatari.length === 0) {
                e.preventDefault();
                $('#emailDestinatario').next('.select2-container').addClass('is-invalid');
                Swal.fire({
                    title: 'Attenzione',
                    text: 'Inserisci almeno un destinatario.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Conferma invio
            Swal.fire({
                title: 'Conferma invio',
                text: 'Sei sicuro di voler inviare questa email?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sì, invia',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    e.preventDefault();
                }
            });
            
            return false;
        });

        // Inizializzazione di Select2 per i destinatari
        $('.select2-tags').select2({
            theme: 'bootstrap4',
            language: 'it',
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: 'Inserisci uno o più indirizzi email...',
            allowClear: true,
            width: '100%'
        });

        // Assicuriamoci che Select2 venga inizializzato quando il modal viene aperto
        $('#modalPreviewEmail').on('shown.bs.modal', function() {
            // Distruggiamo e reinizializziamo Select2 all'apertura del modal per garantire che visualizzi correttamente le opzioni preselezionate
            $('#modalPreviewEmail #emailDestinatario').select2('destroy').select2({
                theme: 'bootstrap4',
                language: 'it',
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: 'Inserisci uno o più indirizzi email...',
                allowClear: true,
                dropdownParent: $('#modalPreviewEmail'),
                width: '100%'
            });

            $('#modalPreviewEmail #emailCC, #modalPreviewEmail #emailCCN').select2({
                theme: 'bootstrap4',
                language: 'it',
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: 'Inserisci uno o più indirizzi email...',
                allowClear: true,
                dropdownParent: $('#modalPreviewEmail'),
                width: '100%'
            });
        });

        // Gestione visualizzazione tabella materiali nell'email
        $('#mostraTabellaEmail').on('change', function() {
            if ($(this).is(':checked')) {
                $('#tabellaEmailMateriali').slideDown();
                $('#tabellaMaterialiField').val('1');
            } else {
                $('#tabellaEmailMateriali').slideUp();
                $('#tabellaMaterialiField').val('0');
            }
        });

        // Quando viene applicato un template, verifica se contiene il segnaposto {{materiali}}
        $('#templateEmail').on('change', function() {
            $.ajax({
                url: '<?= site_url('email-templates/compila/') ?>' + $(this).val(),
                type: 'POST',
                dataType: 'json',
                data: {
                    id_richiesta: '<?= $richiesta['id'] ?>',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Verifica se il corpo del template contiene {{materiali}}
                        if (response.data.corpo.indexOf('{{materiali}}') !== -1) {
                            // Se il template contiene già i materiali, deseleziona la checkbox
                            $('#mostraTabellaEmail').prop('checked', false).trigger('change');
                        }
                    }
                }
            });
        });

        // Forza reinizializzazione del dropdown dopo il caricamento della pagina
        $('.dropdown-toggle').dropdown();

        // Previeni chiusura automatica dei modal quando si clicca all'interno
        $('.modal').on('click', function(e) {
            if ($(e.target).hasClass('modal')) {
                e.stopPropagation();
            }
        });

        // Gestione apertura modal dettaglio email
        $('.visualizzaEmail').on('click', function() {
            var id = $(this).data('id');
            var oggetto = $(this).data('oggetto');
            var corpo = $(this).data('corpo');
            var destinatario = $(this).data('destinatario');
            var cc = $(this).data('cc');
            var ccn = $(this).data('ccn');
            var data = $(this).data('data');
            var stato = $(this).data('stato');
            var allegati = $(this).data('allegati');

            $('#emailDataInvio').text(data);
            $('#emailDestinatarioDettaglio').text(destinatario);
            $('#emailOggettoDettaglio').text(oggetto);

            // Gestione CC
            if (cc && cc !== '') {
                $('#emailCCDettaglio').text(cc);
                $('#emailCCRow').show();
            } else {
                $('#emailCCRow').hide();
            }

            // Gestione CCN
            if (ccn && ccn !== '') {
                $('#emailCCNDettaglio').text(ccn);
                $('#emailCCNRow').show();
            } else {
                $('#emailCCNRow').hide();
            }

            // Gestione stato
            if (stato === 'inviato') {
                $('#emailStato').html('<span class="badge badge-success">Inviata</span>');
            } else {
                $('#emailStato').html('<span class="badge badge-danger">Errore</span>');
            }

            // Visualizza il corpo dell'email in un iframe
            var iframe = document.getElementById('emailCorpoFrame');
            iframe.contentWindow.document.open();
            iframe.contentWindow.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><style>body { font-family: Arial, sans-serif; padding: 10px; }</style></head><body>' + corpo + '</body></html>');
            iframe.contentWindow.document.close();

            // Gestione allegati
            $('#emailAllegatiList').empty();
            if (allegati && allegati !== '') {
                try {
                    // Prima verifichiamo se gli allegati sono già un array (potrebbe essere stato convertito automaticamente)
                    let allegatiArray = [];
                    
                    if (typeof allegati === 'object' && Array.isArray(allegati)) {
                        allegatiArray = allegati;
                    } else {
                        // Rimuoviamo caratteri potenzialmente problematici prima del parsing
                        let allegatiString = allegati.toString().trim();
                        // Aggiunge debug per verificare il formato esatto ricevuto
                        console.log('Allegati formato ricevuto:', allegatiString);
                        
                        // Tenta di pulire la stringa se necessario (rimuove caratteri non validi all'inizio e alla fine)
                        if (allegatiString.startsWith('"') && allegatiString.endsWith('"')) {
                            allegatiString = allegatiString.substring(1, allegatiString.length - 1);
                        }
                        
                        try {
                            allegatiArray = JSON.parse(allegatiString);
                        } catch (parseError) {
                            console.error('Errore nel primo tentativo di parsing:', parseError);
                            
                            // Secondo tentativo: se non è in formato array, proviamo a trattarlo come stringa singola
                            allegatiArray = [allegatiString];
                        }
                    }
                    
                    // Verifica se abbiamo ottenuto un array valido
                    if (allegatiArray && allegatiArray.length > 0) {
                        allegatiArray.forEach(function(allegato) {
                            if (allegato) {
                                $('#emailAllegatiList').append('<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<span><i class="fas fa-paperclip"></i> ' + allegato + '</span>' +
                                    '</li>');
                            }
                        });
                        $('#emailAllegatiRow').show();
                    } else {
                        $('#emailAllegatiRow').hide();
                    }
                } catch (e) {
                    console.error('Errore nel processing degli allegati:', e);
                    $('#emailAllegatiRow').hide();
                }
            } else {
                $('#emailAllegatiRow').hide();
            }
            
            // Mostra il modal
            $('#emailModal').modal('show');
        });

        // Gestione eliminazione richiesta d'offerta
        $('.btn-elimina-richiesta').on('click', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare questa richiesta d'offerta. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= site_url('richieste-offerta/delete/') ?>" + id;
                }
            });
        });
        
        // Gestione eliminazione materiale
        $('.btn-rimuovi-materiale').on('click', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per rimuovere questo materiale dalla richiesta. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, rimuovi',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= site_url('richieste-offerta/rimuovi-materiale/') ?>" + id;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
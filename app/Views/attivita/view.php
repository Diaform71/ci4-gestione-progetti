<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $titolo ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $titolo ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('attivita') ?>">Attività</a></li>
                    <li class="breadcrumb-item active">Dettaglio</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Dettagli attività -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informazioni attività</h3>
                        <div class="card-tools">
                            <?php 
                            $isAdmin = session()->get('is_admin') ?? false;
                            $isCreator = $attivita['id_utente_creatore'] == session()->get('utente_id');
                            $isAssigned = $attivita['id_utente_assegnato'] == session()->get('utente_id');
                            ?>
                            
                            <?php if ($isAdmin || $isCreator): ?>
                            <a href="<?= site_url('attivita/edit/' . $attivita['id']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2><?= esc($attivita['titolo']) ?></h2>
                                
                                <?php 
                                $prioritaClass = 'secondary';
                                switch ($attivita['priorita']) {
                                    case 'alta':
                                        $prioritaClass = 'danger';
                                        break;
                                    case 'media':
                                        $prioritaClass = 'warning';
                                        break;
                                    case 'bassa':
                                        $prioritaClass = 'info';
                                        break;
                                    case 'urgente':
                                        $prioritaClass = 'danger';
                                        break;
                                }
                                
                                $statoClass = 'secondary';
                                switch ($attivita['stato']) {
                                    case 'da_iniziare':
                                        $statoClass = 'warning';
                                        break;
                                    case 'in_corso':
                                        $statoClass = 'info';
                                        break;
                                    case 'in_pausa':
                                        $statoClass = 'secondary';
                                        break;
                                    case 'completata':
                                        $statoClass = 'success';
                                        break;
                                    case 'annullata':
                                        $statoClass = 'danger';
                                        break;
                                }
                                ?>
                                
                                <span class="badge badge-<?= $prioritaClass ?>">
                                    <?= ucfirst(esc($attivita['priorita'])) ?>
                                </span>
                                
                                <span class="badge badge-<?= $statoClass ?>">
                                    <?= str_replace('_', ' ', ucfirst(esc($attivita['stato']))) ?>
                                </span>
                                
                                <?php if ($attivita['stato'] !== 'completata' && $attivita['stato'] !== 'annullata' && 
                                          !empty($attivita['data_scadenza']) && strtotime($attivita['data_scadenza']) < strtotime('today')): ?>
                                <span class="badge badge-danger">In ritardo</span>
                                <?php endif; ?>
                                
                                <hr>
                                
                                <dl class="row">
                                    <dt class="col-sm-4">Progetto:</dt>
                                    <dd class="col-sm-8"><a href="<?= site_url('progetti/' . $attivita['id_progetto']) ?>"><?= esc($attivita['nome_progetto']) ?></a></dd>
                                    
                                    <dt class="col-sm-4">Assegnata a:</dt>
                                    <dd class="col-sm-8">
                                        <?php if (isset($attivita['nome_assegnato'])): ?>
                                            <?= esc($attivita['nome_assegnato'] . ' ' . $attivita['cognome_assegnato']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assegnato</span>
                                        <?php endif; ?>
                                    </dd>
                                    
                                    <dt class="col-sm-4">Creata da:</dt>
                                    <dd class="col-sm-8">
                                        <?php if (isset($attivita['nome_creatore'])): ?>
                                            <?= esc($attivita['nome_creatore'] . ' ' . $attivita['cognome_creatore']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sconosciuto</span>
                                        <?php endif; ?>
                                    </dd>
                                    
                                    <dt class="col-sm-4">Data creazione:</dt>
                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($attivita['data_creazione'])) ?></dd>
                                    
                                    <?php if (!empty($attivita['data_aggiornamento'])): ?>
                                    <dt class="col-sm-4">Ultimo aggiornamento:</dt>
                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($attivita['data_aggiornamento'])) ?></dd>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($attivita['data_scadenza'])): ?>
                                    <dt class="col-sm-4">Scadenza:</dt>
                                    <dd class="col-sm-8"><?= date('d/m/Y', strtotime($attivita['data_scadenza'])) ?></dd>
                                    <?php endif; ?>
                                    
                                    <?php if ($attivita['completata'] && !empty($attivita['completata_il'])): ?>
                                    <dt class="col-sm-4">Completata il:</dt>
                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($attivita['completata_il'])) ?></dd>
                                    <?php endif; ?>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <!-- Azioni -->
                                <?php if ($isAdmin || $isAssigned): ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Cambia stato</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= site_url('attivita/cambiaStato') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $attivita['id'] ?>">
                                            
                                            <div class="form-group">
                                                <select name="stato" class="form-control">
                                                    <option value="da_iniziare" <?= $attivita['stato'] == 'da_iniziare' ? 'selected' : '' ?>>Da iniziare</option>
                                                    <option value="in_corso" <?= $attivita['stato'] == 'in_corso' ? 'selected' : '' ?>>In corso</option>
                                                    <option value="in_pausa" <?= $attivita['stato'] == 'in_pausa' ? 'selected' : '' ?>>In pausa</option>
                                                    <option value="completata" <?= $attivita['stato'] == 'completata' ? 'selected' : '' ?>>Completata</option>
                                                    <option value="annullata" <?= $attivita['stato'] == 'annullata' ? 'selected' : '' ?>>Annullata</option>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">Aggiorna stato</button>
                                        </form>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h4>Descrizione</h4>
                        <div class="callout callout-info">
                            <?php if (!empty($attivita['descrizione'])): ?>
                                <?= nl2br(esc($attivita['descrizione'])) ?>
                            <?php else: ?>
                                <p class="text-muted">Nessuna descrizione disponibile</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Sottoattività -->
                <div class="card" id="sottoattivita">
                    <div class="card-header">
                        <h3 class="card-title">Sottoattività</h3>
                        <div class="card-tools">
                            <?php if ($isAdmin || $isAssigned): ?>
                            <button type="button" class="btn btn-primary btn-sm" id="btnAddSubTask">
                                <i class="fas fa-plus"></i> Aggiungi Sottoattività
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($isAdmin || $isAssigned): ?>
                        <!-- Form per l'aggiunta di sottoattività (nascosto di default) -->
                        <div id="subTaskForm" style="display: none;" class="mb-4">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Nuova sottoattività</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?= site_url('attivita/creaSottoAttivita') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id_attivita" value="<?= $attivita['id'] ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="titolo">Titolo <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="titolo" name="titolo" required placeholder="Inserisci un titolo">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_utente_assegnato">Assegnata a</label>
                                                    <select class="form-control" id="id_utente_assegnato" name="id_utente_assegnato">
                                                        <option value="">-- Seleziona utente --</option>
                                                        <?php foreach ($utenti ?? [] as $utente): ?>
                                                        <option value="<?= $utente['id'] ?>">
                                                            <?= esc($utente['nome'] . ' ' . $utente['cognome']) ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="priorita">Priorità <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="priorita" name="priorita" required>
                                                        <option value="bassa">Bassa</option>
                                                        <option value="media" selected>Media</option>
                                                        <option value="alta">Alta</option>
                                                        <option value="urgente">Urgente</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="stato">Stato <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="stato" name="stato" required>
                                                        <option value="da_iniziare" selected>Da iniziare</option>
                                                        <option value="in_corso">In corso</option>
                                                        <option value="in_pausa">In pausa</option>
                                                        <option value="completata">Completata</option>
                                                        <option value="annullata">Annullata</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data_scadenza">Data scadenza</label>
                                                    <input type="text" class="form-control" id="data_scadenza" name="data_scadenza" placeholder="gg/mm/aaaa">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="descrizione">Descrizione</label>
                                                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3" placeholder="Descrizione della sottoattività"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="text-right">
                                            <button type="button" class="btn btn-default" id="btnCancelSubTask">Annulla</button>
                                            <button type="submit" class="btn btn-primary">Salva</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (empty($attivita['sotto_attivita'])): ?>
                            <p class="text-muted">Nessuna sottoattività definita</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Titolo</th>
                                            <th>Assegnata a</th>
                                            <th>Priorità</th>
                                            <th>Stato</th>
                                            <th>Scadenza</th>
                                            <th style="width: 120px">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attivita['sotto_attivita'] as $subTask): ?>
                                        <tr>
                                            <td><?= esc($subTask['titolo']) ?></td>
                                            <td>
                                                <?php if (isset($subTask['nome_assegnato'])): ?>
                                                    <?= esc($subTask['nome_assegnato'] . ' ' . $subTask['cognome_assegnato']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non assegnato</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $prioritaClass = 'secondary';
                                                switch ($subTask['priorita']) {
                                                    case 'alta':
                                                        $prioritaClass = 'danger';
                                                        break;
                                                    case 'media':
                                                        $prioritaClass = 'warning';
                                                        break;
                                                    case 'bassa':
                                                        $prioritaClass = 'info';
                                                        break;
                                                    case 'urgente':
                                                        $prioritaClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $prioritaClass ?>">
                                                    <?= ucfirst(esc($subTask['priorita'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $statoClass = 'secondary';
                                                switch ($subTask['stato']) {
                                                    case 'da_iniziare':
                                                        $statoClass = 'warning';
                                                        break;
                                                    case 'in_corso':
                                                        $statoClass = 'info';
                                                        break;
                                                    case 'in_pausa':
                                                        $statoClass = 'secondary';
                                                        break;
                                                    case 'completata':
                                                        $statoClass = 'success';
                                                        break;
                                                    case 'annullata':
                                                        $statoClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $statoClass ?>">
                                                    <?= str_replace('_', ' ', ucfirst(esc($subTask['stato']))) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($subTask['data_scadenza'])): ?>
                                                    <?= date('d/m/Y', strtotime($subTask['data_scadenza'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Nessuna</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalDettaglioSottoAttivita<?= $subTask['id'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($isAdmin || $isAssigned || $subTask['id_utente_assegnato'] == session()->get('utente_id')): ?>
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalModificaSottoAttivita<?= $subTask['id'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <a href="<?= site_url('attivita/eliminaSottoAttivita/' . $subTask['id']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Sei sicuro di voler eliminare questa sottoattività?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Modal Dettaglio Sottoattività -->
                                                <div class="modal fade" id="modalDettaglioSottoAttivita<?= $subTask['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDettaglioLabel<?= $subTask['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalDettaglioLabel<?= $subTask['id'] ?>">Dettaglio Sottoattività</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h3><?= esc($subTask['titolo']) ?></h3>
                                                                <dl class="row">
                                                                    <dt class="col-sm-4">Assegnata a:</dt>
                                                                    <dd class="col-sm-8">
                                                                        <?php if (isset($subTask['nome_assegnato'])): ?>
                                                                            <?= esc($subTask['nome_assegnato'] . ' ' . $subTask['cognome_assegnato']) ?>
                                                                        <?php else: ?>
                                                                            <span class="text-muted">Non assegnato</span>
                                                                        <?php endif; ?>
                                                                    </dd>
                                                                    
                                                                    <dt class="col-sm-4">Priorità:</dt>
                                                                    <dd class="col-sm-8">
                                                                        <span class="badge badge-<?= $prioritaClass ?>">
                                                                            <?= ucfirst(esc($subTask['priorita'])) ?>
                                                                        </span>
                                                                    </dd>
                                                                    
                                                                    <dt class="col-sm-4">Stato:</dt>
                                                                    <dd class="col-sm-8">
                                                                        <span class="badge badge-<?= $statoClass ?>">
                                                                            <?= str_replace('_', ' ', ucfirst(esc($subTask['stato']))) ?>
                                                                        </span>
                                                                    </dd>
                                                                    
                                                                    <?php if (!empty($subTask['data_scadenza'])): ?>
                                                                    <dt class="col-sm-4">Scadenza:</dt>
                                                                    <dd class="col-sm-8"><?= date('d/m/Y', strtotime($subTask['data_scadenza'])) ?></dd>
                                                                    <?php endif; ?>
                                                                    
                                                                    <dt class="col-sm-4">Data creazione:</dt>
                                                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($subTask['data_creazione'])) ?></dd>
                                                                    
                                                                    <?php if (!empty($subTask['data_aggiornamento'])): ?>
                                                                    <dt class="col-sm-4">Ultimo aggiornamento:</dt>
                                                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($subTask['data_aggiornamento'])) ?></dd>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if ($subTask['completata'] && !empty($subTask['completata_il'])): ?>
                                                                    <dt class="col-sm-4">Completata il:</dt>
                                                                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($subTask['completata_il'])) ?></dd>
                                                                    <?php endif; ?>
                                                                </dl>
                                                                
                                                                <hr>
                                                                
                                                                <h5>Descrizione</h5>
                                                                <div class="callout callout-info">
                                                                    <?php if (!empty($subTask['descrizione'])): ?>
                                                                        <?= nl2br(esc($subTask['descrizione'])) ?>
                                                                    <?php else: ?>
                                                                        <p class="text-muted">Nessuna descrizione disponibile</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Modifica Sottoattività -->
                                                <?php if ($isAdmin || $isAssigned || $subTask['id_utente_assegnato'] == session()->get('utente_id')): ?>
                                                <div class="modal fade" id="modalModificaSottoAttivita<?= $subTask['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalModificaLabel<?= $subTask['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <form action="<?= site_url('attivita/aggiornaSottoAttivita') ?>" method="post">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="id" value="<?= $subTask['id'] ?>">
                                                                
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="modalModificaLabel<?= $subTask['id'] ?>">Modifica Sottoattività</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="titolo">Titolo</label>
                                                                        <input type="text" class="form-control" id="titolo" name="titolo" value="<?= esc($subTask['titolo']) ?>" required>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="id_utente_assegnato">Assegnata a</label>
                                                                        <select class="form-control" id="id_utente_assegnato" name="id_utente_assegnato">
                                                                            <option value="">-- Seleziona utente --</option>
                                                                            <?php foreach ($utenti ?? [] as $utente): ?>
                                                                            <option value="<?= $utente['id'] ?>" <?= $subTask['id_utente_assegnato'] == $utente['id'] ? 'selected' : '' ?>>
                                                                                <?= esc($utente['nome'] . ' ' . $utente['cognome']) ?>
                                                                            </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="priorita">Priorità</label>
                                                                        <select class="form-control" id="priorita" name="priorita" required>
                                                                            <option value="bassa" <?= $subTask['priorita'] == 'bassa' ? 'selected' : '' ?>>Bassa</option>
                                                                            <option value="media" <?= $subTask['priorita'] == 'media' ? 'selected' : '' ?>>Media</option>
                                                                            <option value="alta" <?= $subTask['priorita'] == 'alta' ? 'selected' : '' ?>>Alta</option>
                                                                            <option value="urgente" <?= $subTask['priorita'] == 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                                                        </select>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="stato">Stato</label>
                                                                        <select class="form-control" id="stato" name="stato" required>
                                                                            <option value="da_iniziare" <?= $subTask['stato'] == 'da_iniziare' ? 'selected' : '' ?>>Da iniziare</option>
                                                                            <option value="in_corso" <?= $subTask['stato'] == 'in_corso' ? 'selected' : '' ?>>In corso</option>
                                                                            <option value="in_pausa" <?= $subTask['stato'] == 'in_pausa' ? 'selected' : '' ?>>In pausa</option>
                                                                            <option value="completata" <?= $subTask['stato'] == 'completata' ? 'selected' : '' ?>>Completata</option>
                                                                            <option value="annullata" <?= $subTask['stato'] == 'annullata' ? 'selected' : '' ?>>Annullata</option>
                                                                        </select>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="data_scadenza">Data scadenza</label>
                                                                        <input type="date" class="form-control" id="data_scadenza" name="data_scadenza" value="<?= !empty($subTask['data_scadenza']) ? date('Y-m-d', strtotime($subTask['data_scadenza'])) : '' ?>">
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="descrizione">Descrizione</label>
                                                                        <textarea class="form-control" id="descrizione" name="descrizione" rows="4"><?= esc($subTask['descrizione']) ?></textarea>
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
                                                <?php endif; ?>
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
            
            <div class="col-md-4">
                <!-- Informazioni aggiuntive, se necessarie -->
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?> 

<?= $this->section('scripts') ?>
<script>
$(function() {
    // Gestione visualizzazione form sottoattività
    $('#btnAddSubTask').on('click', function() {
        $('#subTaskForm').slideDown(300);
        $(this).prop('disabled', true);
    });
    
    $('#btnCancelSubTask').on('click', function() {
        $('#subTaskForm').slideUp(300);
        $('#btnAddSubTask').prop('disabled', false);
        
        // Reset del form
        setTimeout(function() {
            $('form', '#subTaskForm')[0].reset();
        }, 300);
    });
});
</script>
<?= $this->endSection() ?>

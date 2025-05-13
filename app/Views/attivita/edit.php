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
                    <li class="breadcrumb-item active">Modifica</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Modifica Attività</h3>
            </div>
            <div class="card-body">
                <form action="<?= site_url('attivita/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $attivita['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_progetto">Progetto</label>
                                <select name="id_progetto" id="id_progetto" class="form-control" required>
                                    <option value="">-- Seleziona progetto --</option>
                                    <?php foreach ($progetti as $progetto): ?>
                                    <option value="<?= $progetto['id'] ?>" <?= (old('id_progetto', $attivita['id_progetto']) == $progetto['id']) ? 'selected' : '' ?>>
                                        <?= esc($progetto['nome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="id_utente_assegnato">Assegnata a</label>
                                <select name="id_utente_assegnato" id="id_utente_assegnato" class="form-control" required>
                                    <option value="">-- Seleziona utente --</option>
                                    <?php foreach ($utenti as $utente): ?>
                                    <option value="<?= $utente['id'] ?>" <?= (old('id_utente_assegnato', $attivita['id_utente_assegnato']) == $utente['id']) ? 'selected' : '' ?>>
                                        <?= esc($utente['nome'] . ' ' . $utente['cognome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="titolo">Titolo</label>
                                <input type="text" name="titolo" id="titolo" class="form-control" value="<?= old('titolo', $attivita['titolo']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priorita">Priorità</label>
                                <select name="priorita" id="priorita" class="form-control" required>
                                    <option value="bassa" <?= (old('priorita', $attivita['priorita']) == 'bassa') ? 'selected' : '' ?>>Bassa</option>
                                    <option value="media" <?= (old('priorita', $attivita['priorita']) == 'media') ? 'selected' : '' ?>>Media</option>
                                    <option value="alta" <?= (old('priorita', $attivita['priorita']) == 'alta') ? 'selected' : '' ?>>Alta</option>
                                    <option value="urgente" <?= (old('priorita', $attivita['priorita']) == 'urgente') ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="stato">Stato</label>
                                <select name="stato" id="stato" class="form-control" required>
                                    <option value="da_iniziare" <?= (old('stato', $attivita['stato']) == 'da_iniziare') ? 'selected' : '' ?>>Da iniziare</option>
                                    <option value="in_corso" <?= (old('stato', $attivita['stato']) == 'in_corso') ? 'selected' : '' ?>>In corso</option>
                                    <option value="in_pausa" <?= (old('stato', $attivita['stato']) == 'in_pausa') ? 'selected' : '' ?>>In pausa</option>
                                    <option value="completata" <?= (old('stato', $attivita['stato']) == 'completata') ? 'selected' : '' ?>>Completata</option>
                                    <option value="annullata" <?= (old('stato', $attivita['stato']) == 'annullata') ? 'selected' : '' ?>>Annullata</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="data_scadenza">Data scadenza</label>
                                <?php $dataScadenza = !empty($attivita['data_scadenza']) ? date('d/m/Y', strtotime($attivita['data_scadenza'])) : ''; ?>
                                <input type="text" name="data_scadenza" id="data_scadenza" class="form-control" placeholder="gg/mm/aaaa" value="<?= old('data_scadenza', $dataScadenza) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descrizione">Descrizione</label>
                        <textarea name="descrizione" id="descrizione" class="form-control" rows="4"><?= old('descrizione', $attivita['descrizione']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p class="text-muted mb-1">Creata da: 
                                    <?php if (isset($attivita['nome_creatore'])): ?>
                                        <?= esc($attivita['nome_creatore'] . ' ' . $attivita['cognome_creatore']) ?>
                                    <?php else: ?>
                                        <span>Sconosciuto</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-muted mb-1">Data creazione: <?= date('d/m/Y H:i', strtotime($attivita['data_creazione'])) ?></p>
                                <?php if (!empty($attivita['data_aggiornamento'])): ?>
                                <p class="text-muted mb-1">Ultimo aggiornamento: <?= date('d/m/Y H:i', strtotime($attivita['data_aggiornamento'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if ($attivita['completata'] && !empty($attivita['completata_il'])): ?>
                            <div class="alert alert-success">
                                <p class="mb-0">Completata il: <?= date('d/m/Y H:i', strtotime($attivita['completata_il'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Aggiorna</button>
                        <a href="<?= site_url('attivita/view/' . $attivita['id']) ?>" class="btn btn-secondary">Annulla</a>
                        <a href="<?= site_url('attivita/delete/' . $attivita['id']) ?>" class="btn btn-danger float-right" onclick="return confirm('Sei sicuro di voler eliminare questa attività?')">
                            <i class="fas fa-trash"></i> Elimina
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 
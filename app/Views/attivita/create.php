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
                    <li class="breadcrumb-item active">Nuova</li>
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
                <h3 class="card-title">Nuova Attività</h3>
            </div>
            <div class="card-body">
                <form action="<?= site_url('attivita/create') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_progetto">Progetto</label>
                                <select name="id_progetto" id="id_progetto" class="form-control" required>
                                    <option value="">-- Seleziona progetto --</option>
                                    <?php foreach ($progetti as $progetto): ?>
                                    <option value="<?= $progetto['id'] ?>" <?= (old('id_progetto', $id_progetto_selezionato ?? '') == $progetto['id']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $utente['id'] ?>" <?= (old('id_utente_assegnato') == $utente['id']) ? 'selected' : '' ?>>
                                        <?= esc($utente['nome'] . ' ' . $utente['cognome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="titolo">Titolo</label>
                                <input type="text" name="titolo" id="titolo" class="form-control" value="<?= old('titolo') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priorita">Priorità</label>
                                <select name="priorita" id="priorita" class="form-control" required>
                                    <option value="bassa" <?= (old('priorita') == 'bassa') ? 'selected' : '' ?>>Bassa</option>
                                    <option value="media" <?= (old('priorita', 'media') == 'media') ? 'selected' : '' ?>>Media</option>
                                    <option value="alta" <?= (old('priorita') == 'alta') ? 'selected' : '' ?>>Alta</option>
                                    <option value="urgente" <?= (old('priorita') == 'urgente') ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="stato">Stato</label>
                                <select name="stato" id="stato" class="form-control" required>
                                    <option value="da_iniziare" <?= (old('stato', 'da_iniziare') == 'da_iniziare') ? 'selected' : '' ?>>Da iniziare</option>
                                    <option value="in_corso" <?= (old('stato') == 'in_corso') ? 'selected' : '' ?>>In corso</option>
                                    <option value="in_pausa" <?= (old('stato') == 'in_pausa') ? 'selected' : '' ?>>In pausa</option>
                                    <option value="completata" <?= (old('stato') == 'completata') ? 'selected' : '' ?>>Completata</option>
                                    <option value="annullata" <?= (old('stato') == 'annullata') ? 'selected' : '' ?>>Annullata</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="data_scadenza">Data scadenza</label>
                                <input type="text" name="data_scadenza" id="data_scadenza" class="form-control" placeholder="gg/mm/aaaa" value="<?= old('data_scadenza') ? date('d/m/Y', strtotime(old('data_scadenza'))) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descrizione">Descrizione</label>
                        <textarea name="descrizione" id="descrizione" class="form-control" rows="4"><?= old('descrizione') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Salva</button>
                        <a href="<?= site_url('attivita') ?>" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 
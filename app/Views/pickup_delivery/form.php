<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('pickup-delivery') ?>">Pickup & Delivery</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck mr-2"></i>
                        <?= isset($operazione) ? 'Modifica' : 'Nuova' ?> Operazione
                    </h3>
                </div>
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger mx-3 mt-3">
                        <h5><i class="icon fas fa-ban"></i> Errori di validazione!</h5>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php 
                $action = isset($operazione) ? base_url('pickup-delivery/update/' . $operazione['id']) : base_url('pickup-delivery/create');
                ?>
                
                <form action="<?= $action ?>" method="post" id="form-pickup-delivery">
                    <?= csrf_field() ?>
                    
                    <!-- Campo nascosto per utente creatore -->
                    <?php if (!isset($operazione)): ?>
                        <input type="hidden" name="id_utente_creatore" value="<?= session('utente_id') ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Colonna sinistra -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle"></i> Informazioni Generali
                                </h5>
                                
                                <div class="form-group">
                                    <label for="titolo">Titolo *</label>
                                    <input type="text" class="form-control <?= session('errors.titolo') ? 'is-invalid' : '' ?>" 
                                           id="titolo" name="titolo" 
                                           value="<?= old('titolo', isset($operazione) ? $operazione['titolo'] : '') ?>" 
                                           placeholder="Inserisci il titolo dell'operazione">
                                    <?php if (session('errors.titolo')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.titolo') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo">Tipo *</label>
                                            <select class="form-control <?= session('errors.tipo') ? 'is-invalid' : '' ?>" 
                                                    id="tipo" name="tipo">
                                                <option value="">Seleziona tipo...</option>
                                                <option value="ritiro" <?= old('tipo', isset($operazione) ? $operazione['tipo'] : '') === 'ritiro' ? 'selected' : '' ?>>
                                                    <i class="fas fa-truck-loading"></i> Ritiro
                                                </option>
                                                <option value="consegna" <?= old('tipo', isset($operazione) ? $operazione['tipo'] : '') === 'consegna' ? 'selected' : '' ?>>
                                                    <i class="fas fa-truck"></i> Consegna
                                                </option>
                                            </select>
                                            <?php if (session('errors.tipo')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.tipo') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="priorita">Priorità</label>
                                            <select class="form-control <?= session('errors.priorita') ? 'is-invalid' : '' ?>" 
                                                    id="priorita" name="priorita">
                                                <option value="bassa" <?= old('priorita', isset($operazione) ? $operazione['priorita'] : 'normale') === 'bassa' ? 'selected' : '' ?>>Bassa</option>
                                                <option value="normale" <?= old('priorita', isset($operazione) ? $operazione['priorita'] : 'normale') === 'normale' ? 'selected' : '' ?>>Normale</option>
                                                <option value="alta" <?= old('priorita', isset($operazione) ? $operazione['priorita'] : 'normale') === 'alta' ? 'selected' : '' ?>>Alta</option>
                                                <option value="urgente" <?= old('priorita', isset($operazione) ? $operazione['priorita'] : 'normale') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                            </select>
                                            <?php if (session('errors.priorita')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.priorita') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="stato">Stato</label>
                                    <select class="form-control <?= session('errors.stato') ? 'is-invalid' : '' ?>" 
                                            id="stato" name="stato">
                                        <option value="programmata" <?= old('stato', isset($operazione) ? $operazione['stato'] : 'programmata') === 'programmata' ? 'selected' : '' ?>>Programmata</option>
                                        <option value="in_corso" <?= old('stato', isset($operazione) ? $operazione['stato'] : 'programmata') === 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                        <option value="completata" <?= old('stato', isset($operazione) ? $operazione['stato'] : 'programmata') === 'completata' ? 'selected' : '' ?>>Completata</option>
                                        <option value="annullata" <?= old('stato', isset($operazione) ? $operazione['stato'] : 'programmata') === 'annullata' ? 'selected' : '' ?>>Annullata</option>
                                    </select>
                                    <?php if (session('errors.stato')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.stato') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_anagrafica">Anagrafica *</label>
                                    <select class="form-control select2 <?= session('errors.id_anagrafica') ? 'is-invalid' : '' ?>" 
                                            id="id_anagrafica" name="id_anagrafica" style="width: 100%;">
                                        <option value="">Seleziona anagrafica...</option>
                                        <?php foreach ($anagrafiche as $anagrafica): ?>
                                            <option value="<?= $anagrafica['id'] ?>" 
                                                    <?= old('id_anagrafica', isset($operazione) ? $operazione['id_anagrafica'] : '') == $anagrafica['id'] ? 'selected' : '' ?>>
                                                <?= esc($anagrafica['ragione_sociale']) ?>
                                                <?php if ($anagrafica['citta']): ?>
                                                    - <?= esc($anagrafica['citta']) ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.id_anagrafica')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_anagrafica') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_contatto">Contatto</label>
                                    <select class="form-control select2 <?= session('errors.id_contatto') ? 'is-invalid' : '' ?>" 
                                            id="id_contatto" name="id_contatto" style="width: 100%;">
                                        <option value="">Seleziona contatto...</option>
                                    </select>
                                    <?php if (session('errors.id_contatto')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_contatto') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_attivita">Attività Collegata</label>
                                    <select class="form-control select2 <?= session('errors.id_attivita') ? 'is-invalid' : '' ?>" 
                                            id="id_attivita" name="id_attivita" style="width: 100%;">
                                        <option value="">Seleziona attività...</option>
                                        <?php if (isset($attivita)): ?>
                                            <?php foreach ($attivita as $att): ?>
                                                <option value="<?= $att['id'] ?>" 
                                                        <?= old('id_attivita', isset($operazione) ? $operazione['id_attivita'] : '') == $att['id'] ? 'selected' : '' ?>>
                                                    <?= esc($att['titolo']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (session('errors.id_attivita')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_attivita') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_utente_assegnato">Assegnato a</label>
                                    <select class="form-control select2 <?= session('errors.id_utente_assegnato') ? 'is-invalid' : '' ?>" 
                                            id="id_utente_assegnato" name="id_utente_assegnato" style="width: 100%;">
                                        <option value="">Seleziona utente...</option>
                                        <?php if (isset($utenti)): ?>
                                            <?php foreach ($utenti as $utente): ?>
                                                <option value="<?= $utente['id'] ?>" 
                                                        <?= old('id_utente_assegnato', isset($operazione) ? $operazione['id_utente_assegnato'] : '') == $utente['id'] ? 'selected' : '' ?>>
                                                    <?= esc($utente['nome'] . ' ' . $utente['cognome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (session('errors.id_utente_assegnato')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.id_utente_assegnato') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Colonna destra -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-calendar-alt"></i> Date e Orari
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="data_programmata">Data Programmata *</label>
                                            <input type="datetime-local" 
                                                   class="form-control <?= session('errors.data_programmata') ? 'is-invalid' : '' ?>" 
                                                   id="data_programmata" 
                                                   name="data_programmata" 
                                                   lang="it-IT"
                                                   value="<?= old('data_programmata', isset($operazione) ? date('Y-m-d\TH:i', strtotime($operazione['data_programmata'])) : '') ?>">
                                            <?php if (session('errors.data_programmata')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.data_programmata') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="orario_preferito">Orario Preferito</label>
                                            <input type="text" class="form-control <?= session('errors.orario_preferito') ? 'is-invalid' : '' ?>" 
                                                   id="orario_preferito" name="orario_preferito" 
                                                   value="<?= old('orario_preferito', isset($operazione) ? $operazione['orario_preferito'] : '') ?>"
                                                   placeholder="es. 9:00-12:00">
                                            <?php if (session('errors.orario_preferito')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.orario_preferito') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-map-marker-alt"></i> Indirizzo
                                </h5>
                                
                                <div class="form-group">
                                    <label for="indirizzo">Indirizzo *</label>
                                    <textarea class="form-control <?= session('errors.indirizzo') ? 'is-invalid' : '' ?>" 
                                              id="indirizzo" name="indirizzo" rows="2" 
                                              placeholder="Inserisci l'indirizzo completo"><?= old('indirizzo', isset($operazione) ? $operazione['indirizzo'] : '') ?></textarea>
                                    <?php if (session('errors.indirizzo')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.indirizzo') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="citta">Città</label>
                                            <input type="text" class="form-control <?= session('errors.citta') ? 'is-invalid' : '' ?>" 
                                                   id="citta" name="citta" 
                                                   value="<?= old('citta', isset($operazione) ? $operazione['citta'] : '') ?>">
                                            <?php if (session('errors.citta')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.citta') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cap">CAP</label>
                                            <input type="text" class="form-control <?= session('errors.cap') ? 'is-invalid' : '' ?>" 
                                                   id="cap" name="cap" 
                                                   value="<?= old('cap', isset($operazione) ? $operazione['cap'] : '') ?>">
                                            <?php if (session('errors.cap')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.cap') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="provincia">Provincia</label>
                                            <input type="text" class="form-control <?= session('errors.provincia') ? 'is-invalid' : '' ?>" 
                                                   id="provincia" name="provincia" maxlength="2"
                                                   value="<?= old('provincia', isset($operazione) ? $operazione['provincia'] : '') ?>">
                                            <?php if (session('errors.provincia')): ?>
                                                <div class="invalid-feedback">
                                                    <?= session('errors.provincia') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nazione">Nazione</label>
                                    <input type="text" class="form-control <?= session('errors.nazione') ? 'is-invalid' : '' ?>" 
                                           id="nazione" name="nazione" 
                                           value="<?= old('nazione', isset($operazione) ? $operazione['nazione'] : 'Italia') ?>">
                                    <?php if (session('errors.nazione')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.nazione') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sezione contatto -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user"></i> Informazioni Contatto
                                </h5>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nome_contatto">Nome Contatto</label>
                                    <input type="text" class="form-control <?= session('errors.nome_contatto') ? 'is-invalid' : '' ?>" 
                                           id="nome_contatto" name="nome_contatto" 
                                           value="<?= old('nome_contatto', isset($operazione) ? $operazione['nome_contatto'] : '') ?>">
                                    <?php if (session('errors.nome_contatto')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.nome_contatto') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="telefono_contatto">Telefono Contatto</label>
                                    <input type="text" class="form-control <?= session('errors.telefono_contatto') ? 'is-invalid' : '' ?>" 
                                           id="telefono_contatto" name="telefono_contatto" 
                                           value="<?= old('telefono_contatto', isset($operazione) ? $operazione['telefono_contatto'] : '') ?>">
                                    <?php if (session('errors.telefono_contatto')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.telefono_contatto') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_contatto">Email Contatto</label>
                                    <input type="email" class="form-control <?= session('errors.email_contatto') ? 'is-invalid' : '' ?>" 
                                           id="email_contatto" name="email_contatto" 
                                           value="<?= old('email_contatto', isset($operazione) ? $operazione['email_contatto'] : '') ?>">
                                    <?php if (session('errors.email_contatto')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.email_contatto') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sezione descrizione e note -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note"></i> Descrizione e Note
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="descrizione">Descrizione</label>
                                    <textarea class="form-control <?= session('errors.descrizione') ? 'is-invalid' : '' ?>" 
                                              id="descrizione" name="descrizione" rows="4" 
                                              placeholder="Descrizione dettagliata dell'operazione"><?= old('descrizione', isset($operazione) ? $operazione['descrizione'] : '') ?></textarea>
                                    <?php if (session('errors.descrizione')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.descrizione') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control <?= session('errors.note') ? 'is-invalid' : '' ?>" 
                                              id="note" name="note" rows="4" 
                                              placeholder="Note aggiuntive"><?= old('note', isset($operazione) ? $operazione['note'] : '') ?></textarea>
                                    <?php if (session('errors.note')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.note') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sezione DDT e costi -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-invoice"></i> DDT e Costi
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="richiesta_ddt" name="richiesta_ddt" value="1"
                                               <?= old('richiesta_ddt', isset($operazione) ? $operazione['richiesta_ddt'] : 0) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="richiesta_ddt">Richiesta DDT</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="numero_ddt">Numero DDT</label>
                                    <input type="text" class="form-control <?= session('errors.numero_ddt') ? 'is-invalid' : '' ?>" 
                                           id="numero_ddt" name="numero_ddt" 
                                           value="<?= old('numero_ddt', isset($operazione) ? $operazione['numero_ddt'] : '') ?>">
                                    <?php if (session('errors.numero_ddt')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.numero_ddt') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="costo_stimato">Costo Stimato (€)</label>
                                    <input type="number" step="0.01" class="form-control <?= session('errors.costo_stimato') ? 'is-invalid' : '' ?>" 
                                           id="costo_stimato" name="costo_stimato" 
                                           value="<?= old('costo_stimato', isset($operazione) ? $operazione['costo_stimato'] : '') ?>">
                                    <?php if (session('errors.costo_stimato')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.costo_stimato') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="costo_effettivo">Costo Effettivo (€)</label>
                                    <input type="number" step="0.01" class="form-control <?= session('errors.costo_effettivo') ? 'is-invalid' : '' ?>" 
                                           id="costo_effettivo" name="costo_effettivo" 
                                           value="<?= old('costo_effettivo', isset($operazione) ? $operazione['costo_effettivo'] : '') ?>">
                                    <?php if (session('errors.costo_effettivo')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.costo_effettivo') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note_trasportatore">Note per il Trasportatore</label>
                                    <textarea class="form-control <?= session('errors.note_trasportatore') ? 'is-invalid' : '' ?>" 
                                              id="note_trasportatore" name="note_trasportatore" rows="3" 
                                              placeholder="Istruzioni specifiche per il trasportatore"><?= old('note_trasportatore', isset($operazione) ? $operazione['note_trasportatore'] : '') ?></textarea>
                                    <?php if (session('errors.note_trasportatore')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.note_trasportatore') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('pickup-delivery') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?= isset($operazione) ? 'Aggiorna' : 'Salva' ?> Operazione
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<style>
/* Proteggi i campi datetime-local dall'interferenza dei plugin datepicker */
input[type="datetime-local"].no-datepicker {
    background-image: none !important;
    padding-right: 12px !important;
}

input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    opacity: 1;
    cursor: pointer;
}

/* Stile per il placeholder dei campi datetime-local */
input[type="datetime-local"]:invalid {
    color: #999;
}

/* Migliora la visualizzazione su browser che non supportano datetime-local */
input[type="datetime-local"]:not(:focus):not(:valid) {
    color: #999;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>

<script>
$(function() {
    // Proteggi i campi datetime-local dall'interferenza dei plugin datepicker
    $('input[type="datetime-local"]').each(function() {
        $(this).addClass('no-datepicker');
        // Rimuovi eventuali inizializzazioni datepicker
        if ($(this).data('datepicker')) {
            $(this).datepicker('destroy');
        }
        
        // Forza il locale italiano
        $(this).attr('lang', 'it-IT');
        
        // Aggiungi un placeholder per chiarire il formato
        if (!$(this).attr('placeholder')) {
            $(this).attr('placeholder', 'gg/mm/aaaa, hh:mm');
        }
        
        // Aggiungi un tooltip per spiegare il formato
        $(this).attr('title', 'Formato: giorno/mese/anno, ora:minuti (es. 15/06/2025, 14:30)');
    });
    
    // Funzione per verificare il supporto datetime-local
    function supportsDatetimeLocal() {
        const input = document.createElement('input');
        input.type = 'datetime-local';
        return input.type === 'datetime-local';
    }
    
    // Se il browser non supporta datetime-local, mostra un messaggio
    if (!supportsDatetimeLocal()) {
        console.warn('Il browser non supporta completamente datetime-local. Il formato potrebbe variare.');
        
        // Aggiungi un messaggio informativo
        $('input[type="datetime-local"]').after(
            '<small class="form-text text-muted">Nota: Il formato della data potrebbe variare in base al browser utilizzato.</small>'
        );
    }
    
    // Inizializzazione Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: function() {
            return $(this).data('placeholder');
        }
    });
    
    // Caricamento contatti quando cambia l'anagrafica
    $('#id_anagrafica').on('change', function() {
        const anagraficaId = $(this).val();
        const $contattoSelect = $('#id_contatto');
        
        // Reset contatti
        $contattoSelect.empty().append('<option value="">Seleziona contatto...</option>');
        
        if (anagraficaId) {
            $.ajax({
                url: `<?= base_url('pickup-delivery/getContatti') ?>/${anagraficaId}`,
                method: 'GET',
                success: function(contatti) {
                    if (contatti && contatti.length > 0) {
                        contatti.forEach(function(contatto) {
                            $contattoSelect.append(
                                `<option value="${contatto.id}">${contatto.nome} - ${contatto.telefono || contatto.email || ''}</option>`
                            );
                        });
                    }
                },
                error: function() {
                    console.error('Errore nel caricamento dei contatti');
                }
            });
        }
    });
    
    // Auto-popolamento dati contatto quando viene selezionato un contatto dal database
    $('#id_contatto').on('change', function() {
        const contattoId = $(this).val();
        
        if (contattoId) {
            // Qui potresti fare una chiamata AJAX per ottenere i dettagli del contatto
            // e popolare automaticamente i campi nome_contatto, telefono_contatto, email_contatto
        }
    });
    
    // Validazione form
    $('#form-pickup-delivery').on('submit', function(e) {
        let isValid = true;
        
        // Controllo campi obbligatori
        const requiredFields = ['titolo', 'tipo', 'id_anagrafica', 'data_programmata', 'indirizzo'];
        
        requiredFields.forEach(function(field) {
            const $field = $(`#${field}`);
            if (!$field.val().trim()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Compila tutti i campi obbligatori');
        }
    });
    
    // Rimuovi classe invalid quando l'utente inizia a digitare
    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
<?= $this->endSection() ?> 
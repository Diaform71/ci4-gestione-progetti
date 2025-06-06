<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= esc($operazione['titolo']) ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
            color: #7f8c8d;
        }
        
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .priority-badge, .status-badge, .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .priority-bassa { background-color: #6c757d; color: white; }
        .priority-normale { background-color: #17a2b8; color: white; }
        .priority-alta { background-color: #ffc107; color: black; }
        .priority-urgente { background-color: #dc3545; color: white; }
        
        .status-programmata { background-color: #007bff; color: white; }
        .status-in_corso { background-color: #ffc107; color: black; }
        .status-completata { background-color: #28a745; color: white; }
        .status-annullata { background-color: #dc3545; color: white; }
        
        .type-ritiro { background-color: #17a2b8; color: white; }
        .type-consegna { background-color: #007bff; color: white; }
        
        .signature-section {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .signature-box {
            border: 1px solid #333;
            height: 80px;
            margin-top: 10px;
            position: relative;
        }
        
        .signature-label {
            position: absolute;
            bottom: 5px;
            left: 10px;
            font-size: 10px;
            color: #666;
        }
        
        .notes-section {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        
        .print-info {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .no-print {
            margin-bottom: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <!-- Pulsanti di controllo (non stampati) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn">
            🖨️ Stampa
        </button>
        <a href="<?= base_url('pickup-delivery/show/' . $operazione['id']) ?>" class="btn btn-secondary">
            ← Torna ai Dettagli
        </a>
        <a href="<?= base_url('pickup-delivery') ?>" class="btn btn-secondary">
            📋 Lista Operazioni
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>PROMEMORIA FATTORINO</h1>
        <h2><?= esc($operazione['titolo']) ?></h2>
    </div>

    <!-- Informazioni Operazione -->
    <div class="info-section">
        <h3>📋 Dettagli Operazione</h3>
        <div class="info-row">
            <div class="info-label">ID Operazione:</div>
            <div class="info-value"><strong>#<?= $operazione['id'] ?></strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tipo:</div>
            <div class="info-value">
                <span class="type-badge type-<?= $operazione['tipo'] ?>">
                    <?= $operazione['tipo'] === 'ritiro' ? '📦 RITIRO' : '🚚 CONSEGNA' ?>
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Stato:</div>
            <div class="info-value">
                <span class="status-badge status-<?= $operazione['stato'] ?>">
                    <?= strtoupper(str_replace('_', ' ', $operazione['stato'])) ?>
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Priorità:</div>
            <div class="info-value">
                <span class="priority-badge priority-<?= $operazione['priorita'] ?>">
                    <?= strtoupper($operazione['priorita']) ?>
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Data Programmata:</div>
            <div class="info-value">
                <strong><?= date('d/m/Y H:i', strtotime($operazione['data_programmata'])) ?></strong>
                <?php if (!empty($operazione['orario_preferito'])): ?>
                    <br><small>Orario preferito: <?= esc($operazione['orario_preferito']) ?></small>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($operazione['id_utente_assegnato'] && $utente_assegnato): ?>
        <div class="info-row">
            <div class="info-label">Assegnato a:</div>
            <div class="info-value"><?= esc($utente_assegnato['nome'] . ' ' . $utente_assegnato['cognome']) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Informazioni Cliente -->
    <div class="info-section">
        <h3>🏢 Cliente/Fornitore</h3>
        <div class="info-row">
            <div class="info-label">Ragione Sociale:</div>
            <div class="info-value"><strong><?= esc($anagrafica['ragione_sociale']) ?></strong></div>
        </div>
        <?php if ($contatto): ?>
        <div class="info-row">
            <div class="info-label">Contatto:</div>
            <div class="info-value"><?= esc($contatto['nome'] . ' ' . $contatto['cognome']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($operazione['nome_contatto'])): ?>
        <div class="info-row">
            <div class="info-label">Nome Contatto:</div>
            <div class="info-value"><?= esc($operazione['nome_contatto']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($operazione['telefono_contatto'])): ?>
        <div class="info-row">
            <div class="info-label">Telefono:</div>
            <div class="info-value"><strong><?= esc($operazione['telefono_contatto']) ?></strong></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($operazione['email_contatto'])): ?>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value"><?= esc($operazione['email_contatto']) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Indirizzo -->
    <div class="info-section">
        <h3>📍 Indirizzo</h3>
        <div class="info-row">
            <div class="info-label">Indirizzo:</div>
            <div class="info-value"><strong><?= esc($operazione['indirizzo']) ?></strong></div>
        </div>
        <?php if (!empty($operazione['citta'])): ?>
        <div class="info-row">
            <div class="info-label">Città:</div>
            <div class="info-value"><?= esc($operazione['citta']) ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">CAP:</div>
            <div class="info-value"><?= esc($operazione['cap']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Provincia:</div>
            <div class="info-value"><?= esc($operazione['provincia']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Nazione:</div>
            <div class="info-value"><?= esc($operazione['nazione']) ?></div>
        </div>
    </div>

    <!-- Descrizione e Note -->
    <?php if (!empty($operazione['descrizione']) || !empty($operazione['note']) || !empty($operazione['note_trasportatore'])): ?>
    <div class="info-section">
        <h3>📝 Note e Descrizione</h3>
        <?php if (!empty($operazione['descrizione'])): ?>
        <div class="info-row">
            <div class="info-label">Descrizione:</div>
            <div class="info-value"><?= nl2br(esc($operazione['descrizione'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($operazione['note'])): ?>
        <div class="info-row">
            <div class="info-label">Note:</div>
            <div class="info-value"><?= nl2br(esc($operazione['note'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($operazione['note_trasportatore'])): ?>
        <div class="notes-section">
            <strong>⚠️ ISTRUZIONI SPECIALI PER IL TRASPORTATORE:</strong><br>
            <?= nl2br(esc($operazione['note_trasportatore'])) ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Informazioni DDT -->
    <?php if ($operazione['richiesta_ddt'] || !empty($operazione['numero_ddt'])): ?>
    <div class="info-section">
        <h3>📄 Informazioni DDT</h3>
        <div class="info-row">
            <div class="info-label">Richiesta DDT:</div>
            <div class="info-value"><?= $operazione['richiesta_ddt'] ? '✅ SÌ' : '❌ NO' ?></div>
        </div>
        <?php if (!empty($operazione['numero_ddt'])): ?>
        <div class="info-row">
            <div class="info-label">Numero DDT:</div>
            <div class="info-value"><strong><?= esc($operazione['numero_ddt']) ?></strong></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Sezione Firme -->
    <div class="signature-section">
        <h3>✍️ Firme e Conferme</h3>
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <strong>Firma Fattorino:</strong>
                <div class="signature-box">
                    <div class="signature-label">Data e Ora: _______________</div>
                </div>
            </div>
            <div style="flex: 1;">
                <strong>Firma Cliente:</strong>
                <div class="signature-box">
                    <div class="signature-label">Nome: _______________</div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <strong>Note Aggiuntive:</strong>
            <div style="border: 1px solid #333; height: 60px; margin-top: 10px;"></div>
        </div>
    </div>

    <!-- Informazioni di stampa -->
    <div class="print-info">
        Stampato il <?= date('d/m/Y H:i') ?> | 
        Operazione #<?= $operazione['id'] ?> | 
        Creato da: <?= esc($utente_creatore['nome'] . ' ' . $utente_creatore['cognome']) ?>
    </div>
</body>
</html> 
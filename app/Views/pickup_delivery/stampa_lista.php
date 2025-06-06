<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 15px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #2c3e50;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #7f8c8d;
        }
        
        .filters-info {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .operations-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .operations-table th,
        .operations-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        
        .operations-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
        }
        
        .operations-table td {
            font-size: 9px;
        }
        
        .priority-badge, .status-badge, .type-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 8px;
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
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .signature-box {
            border: 1px solid #333;
            height: 60px;
            margin-top: 10px;
            position: relative;
        }
        
        .signature-label {
            position: absolute;
            bottom: 5px;
            left: 10px;
            font-size: 9px;
            color: #666;
        }
        
        .print-info {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
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
        
        .summary-box {
            background-color: #e9ecef;
            border: 1px solid #adb5bd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .address-cell {
            max-width: 120px;
            word-wrap: break-word;
        }
        
        .notes-cell {
            max-width: 100px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <!-- Pulsanti di controllo (non stampati) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn">
            🖨️ Stampa Lista
        </button>
        <a href="<?= base_url('pickup-delivery') ?>" class="btn btn-secondary">
            ← Torna alla Lista
        </a>
        <a href="<?= base_url('pickup-delivery/calendario') ?>" class="btn btn-secondary">
            📅 Calendario
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>LISTA OPERAZIONI FATTORINO</h1>
        <h2>
            <?php if ($data_inizio === $data_fine): ?>
                <?= date('d/m/Y', strtotime($data_inizio)) ?>
            <?php else: ?>
                Dal <?= date('d/m/Y', strtotime($data_inizio)) ?> al <?= date('d/m/Y', strtotime($data_fine)) ?>
            <?php endif; ?>
        </h2>
    </div>

    <!-- Informazioni Filtri -->
    <div class="filters-info">
        <strong>Filtri applicati:</strong>
        Periodo: <?= date('d/m/Y', strtotime($data_inizio)) ?> - <?= date('d/m/Y', strtotime($data_fine)) ?>
        <?php if ($filtri['tipo'] && $filtri['tipo'] !== 'tutti'): ?>
            | Tipo: <?= ucfirst($filtri['tipo']) ?>
        <?php endif; ?>
        <?php if ($filtri['stato'] && $filtri['stato'] !== 'tutti'): ?>
            | Stato: <?= ucfirst(str_replace('_', ' ', $filtri['stato'])) ?>
        <?php endif; ?>
        <?php if ($filtri['utente'] && $filtri['utente'] !== 'tutti'): ?>
            | Utente: ID <?= $filtri['utente'] ?>
        <?php endif; ?>
    </div>

    <!-- Riepilogo -->
    <div class="summary-box">
        <strong>Riepilogo:</strong> 
        Totale operazioni: <?= count($operazioni) ?>
        <?php 
        $ritiri = array_filter($operazioni, fn($op) => $op['tipo'] === 'ritiro');
        $consegne = array_filter($operazioni, fn($op) => $op['tipo'] === 'consegna');
        $programmata = array_filter($operazioni, fn($op) => $op['stato'] === 'programmata');
        $in_corso = array_filter($operazioni, fn($op) => $op['stato'] === 'in_corso');
        ?>
        | Ritiri: <?= count($ritiri) ?>
        | Consegne: <?= count($consegne) ?>
        | Programmate: <?= count($programmata) ?>
        | In corso: <?= count($in_corso) ?>
    </div>

    <!-- Tabella Operazioni -->
    <?php if (empty($operazioni)): ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <h3>Nessuna operazione trovata per i filtri selezionati</h3>
        </div>
    <?php else: ?>
        <table class="operations-table">
            <thead>
                <tr>
                    <th width="30">#</th>
                    <th width="40">Tipo</th>
                    <th width="80">Data/Ora</th>
                    <th width="120">Cliente</th>
                    <th width="120">Indirizzo</th>
                    <th width="80">Contatto</th>
                    <th width="50">Priorità</th>
                    <th width="50">Stato</th>
                    <th width="100">Note</th>
                    <th width="60">Firma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($operazioni as $operazione): ?>
                    <tr>
                        <td><strong><?= $operazione['id'] ?></strong></td>
                        <td>
                            <span class="type-badge type-<?= $operazione['tipo'] ?>">
                                <?= $operazione['tipo'] === 'ritiro' ? 'RIT' : 'CON' ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= date('d/m H:i', strtotime($operazione['data_programmata'])) ?></strong>
                            <?php if (!empty($operazione['orario_preferito'])): ?>
                                <br><small><?= esc($operazione['orario_preferito']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= esc($operazione['ragione_sociale']) ?></strong>
                            <?php if (!empty($operazione['nome_contatto'])): ?>
                                <br><small><?= esc($operazione['nome_contatto']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="address-cell">
                            <?= esc($operazione['indirizzo']) ?>
                            <?php if (!empty($operazione['citta'])): ?>
                                <br><?= esc($operazione['citta']) ?>
                            <?php endif; ?>
                            <?php if (!empty($operazione['cap'])): ?>
                                <?= esc($operazione['cap']) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($operazione['telefono_contatto'])): ?>
                                <strong><?= esc($operazione['telefono_contatto']) ?></strong>
                            <?php endif; ?>
                            <?php if (!empty($operazione['email_contatto'])): ?>
                                <br><small><?= esc($operazione['email_contatto']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="priority-badge priority-<?= $operazione['priorita'] ?>">
                                <?= strtoupper(substr($operazione['priorita'], 0, 3)) ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $operazione['stato'] ?>">
                                <?= strtoupper(substr(str_replace('_', '', $operazione['stato']), 0, 4)) ?>
                            </span>
                        </td>
                        <td class="notes-cell">
                            <?php if ($operazione['richiesta_ddt']): ?>
                                <strong>DDT</strong><br>
                            <?php endif; ?>
                            <?php if (!empty($operazione['note_trasportatore'])): ?>
                                <small><?= esc(substr($operazione['note_trasportatore'], 0, 50)) ?><?= strlen($operazione['note_trasportatore']) > 50 ? '...' : '' ?></small>
                            <?php elseif (!empty($operazione['descrizione'])): ?>
                                <small><?= esc(substr($operazione['descrizione'], 0, 50)) ?><?= strlen($operazione['descrizione']) > 50 ? '...' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="border: 1px solid #333; height: 30px;"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Sezione Firme -->
    <div class="signature-section">
        <h3>✍️ Conferme Fattorino</h3>
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <strong>Firma Fattorino:</strong>
                <div class="signature-box">
                    <div class="signature-label">Nome: _______________</div>
                </div>
            </div>
            <div style="flex: 1;">
                <strong>Data e Ora Consegna Lista:</strong>
                <div class="signature-box">
                    <div class="signature-label">Data: _______________</div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <strong>Note Generali:</strong>
            <div style="border: 1px solid #333; height: 50px; margin-top: 10px;"></div>
        </div>
    </div>

    <!-- Informazioni di stampa -->
    <div class="print-info">
        Stampato il <?= date('d/m/Y H:i') ?> | 
        Totale operazioni: <?= count($operazioni) ?> | 
        Periodo: <?= date('d/m/Y', strtotime($data_inizio)) ?> - <?= date('d/m/Y', strtotime($data_fine)) ?>
    </div>
</body>
</html> 
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Verifica Requisiti</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Verifica dei requisiti di sistema</h5>
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Requisito</th>
                                    <th>Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PHP >= 7.4.0</td>
                                    <td>
                                        <?php if ($requirements['php_version']): ?>
                                            <span class="text-success">✓ OK (<?= PHP_VERSION ?>)</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non soddisfatto (<?= PHP_VERSION ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estensione cURL</td>
                                    <td>
                                        <?php if ($requirements['curl']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non installata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estensione Intl</td>
                                    <td>
                                        <?php if ($requirements['intl']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non installata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estensione JSON</td>
                                    <td>
                                        <?php if ($requirements['json']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non installata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estensione mbstring</td>
                                    <td>
                                        <?php if ($requirements['mbstring']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non installata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estensione XML</td>
                                    <td>
                                        <?php if ($requirements['xml']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non installata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>File .env scrivibile</td>
                                    <td>
                                        <?php if ($requirements['writable_env']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non scrivibile</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cartella writable/ scrivibile</td>
                                    <td>
                                        <?php if ($requirements['writable_writepath']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non scrivibile</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cartella uploads/ scrivibile</td>
                                    <td>
                                        <?php if ($requirements['writable_uploadpath']): ?>
                                            <span class="text-success">✓ OK</span>
                                        <?php else: ?>
                                            <span class="text-danger">✗ Non scrivibile</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="<?= site_url('install') ?>" class="btn btn-secondary">Indietro</a>
                            <?php if ($requirements_satisfied): ?>
                                <a href="<?= site_url('install/database') ?>" class="btn btn-primary">Avanti</a>
                            <?php else: ?>
                                <button class="btn btn-danger" disabled>Requisiti non soddisfatti</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
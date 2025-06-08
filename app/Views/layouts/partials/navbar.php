<nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="<?= base_url() ?>" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Link Impostazioni -->
                <li class="nav-item">
                    <a href="<?= site_url('impostazioni/utente') ?>" class="nav-link" title="Impostazioni personali">
                        <i class="fas fa-cog"></i>
                    </a>
                </li>
                <!-- Dropdown Utente -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle"></i>
                        <?php 
                        $nome = trim(session('utente_nome') ?? '');
                        $cognome = trim(session('utente_cognome') ?? '');
                        $username = session('utente_username');
                        
                        if (!empty($nome) && !empty($cognome)): ?>
                            <span class="d-none d-md-inline-block ml-1">
                                <?= esc($nome) ?> <?= esc($cognome) ?>
                            </span>
                        <?php elseif (!empty($username)): ?>
                            <span class="d-none d-md-inline-block ml-1"><?= esc($username) ?></span>
                        <?php else: ?>
                            <span class="d-none d-md-inline-block ml-1">Utente</span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <?php if (session()->has('utente_logged_in')): ?>
                            <span class="dropdown-header">
                                <?php 
                                $nome = trim(session('utente_nome') ?? '');
                                $cognome = trim(session('utente_cognome') ?? '');
                                $username = session('utente_username');
                                
                                if (!empty($nome) && !empty($cognome)): ?>
                                    <?= esc($nome) ?> <?= esc($cognome) ?>
                                <?php else: ?>
                                    <?= esc($username) ?>
                                <?php endif; ?>
                            </span>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('profilo') ?>" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Il mio profilo
                            </a>
                            <a href="<?= base_url('cambio-password') ?>" class="dropdown-item">
                                <i class="fas fa-key mr-2"></i> Cambio password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= site_url('impostazioni/utente') ?>" class="dropdown-item">
                                <i class="fas fa-cog mr-2"></i> Impostazioni personali
                            </a>
                            <?php if (session()->has('is_admin') && session('is_admin')): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= site_url('impostazioni') ?>" class="dropdown-item">
                                <i class="fas fa-cogs mr-2"></i> Impostazioni sistema
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('logout') ?>" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Disconnetti
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="dropdown-item">
                                <i class="fas fa-sign-in-alt mr-2"></i> Accedi
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
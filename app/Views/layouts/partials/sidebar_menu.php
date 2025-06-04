<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-address-book"></i>
                <p>
                    Anagrafiche
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('anagrafiche') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Gestione Anagrafiche</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('contatti') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Contatti</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-project-diagram"></i>
                <p>
                    Progetti
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('progetti') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Tutti i Progetti</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('progetti/kanban') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Vista Kanban</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('progetti/in-scadenza') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>In Scadenza</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('progetti/new') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Nuovo Progetto</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tasks"></i>
                <p>
                    Attività
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('attivita') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Tutte le Attività</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('attivita/new') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Nuova Attività</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>
                    Scadenze
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('scadenze') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Tutte le Scadenze</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('scadenze/inScadenza') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>In Scadenza</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('scadenze/scadute') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Scadute</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('scadenze/nuovo') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Nuova Scadenza</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>
                    Acquisti
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Richieste d'Offerta
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('richieste-offerta') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Lista Richieste</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('richieste-offerta/new') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Nuova Richiesta</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Offerte Fornitori
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('offerte-fornitore') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Lista Offerte</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('offerte-fornitore/new') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Nuova Offerta</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Ordini d'Acquisto
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('ordini-materiale') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Lista Ordini</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('ordini-materiale/new') ?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p>Nuovo Ordine</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        <!-- Materiali -->
        <li class="nav-item">
            <a href="<?= base_url('materiali') ?>" class="nav-link">
                <i class="nav-icon fas fa-boxes"></i>
                <p>Materiali</p>
            </a>
        </li>

        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>
                    Opzioni
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="<?= base_url('aliquote-iva') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Aliquote IVA</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('cambio-password') ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Cambio Password</p>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Sezione Amministrazione (solo admin) -->
        <?php if (session()->has('is_admin') && session('is_admin')): ?>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-shield-alt"></i>
                    <p>
                        Amministrazione
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= base_url('condizioni-pagamento') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Condizioni Pagamento</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                Gestione Utenti
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('utenti') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Lista Utenti</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('utenti/new') ?>" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Nuovo Utente</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('email-templates') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Template Email</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="<?= base_url('logout') ?>" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>
                    Logout
                </p>
            </a>
        </li>
    </ul>
</nav>
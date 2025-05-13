                    <div class="form-group">
                        <label for="id_anagrafica">Cliente</label>
                        <select name="id_anagrafica" id="id_anagrafica" class="form-control">
                            <option value="">Seleziona un cliente...</option>
                            <?php foreach ($anagrafiche as $anagrafica) : ?>
                                <option value="<?= $anagrafica['id'] ?>" <?= isset($progetto) && $progetto['id_anagrafica'] == $anagrafica['id'] ? 'selected' : '' ?>>
                                    <?= esc($anagrafica['ragione_sociale']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_progetto_padre">Progetto Padre</label>
                        <select name="id_progetto_padre" id="id_progetto_padre" class="form-control">
                            <option value="">Nessun progetto padre (Progetto principale)</option>
                            <?php foreach ($progetti_disponibili as $prog) : ?>
                                <?php if ((!isset($progetto) || $prog['id'] != $progetto['id']) && empty($prog['id_progetto_padre'])) : ?>
                                    <option value="<?= $prog['id'] ?>" <?= isset($progetto) && $progetto['id_progetto_padre'] == $prog['id'] ? 'selected' : '' ?>>
                                        <?= esc($prog['nome']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Seleziona un progetto padre per creare un sottoprogetto. Lascia vuoto per creare un progetto principale.</small>
                    </div>

                    <div class="form-group">
                        <label for="id_responsabile">Responsabile</label>
                        <select name="id_responsabile" id="id_responsabile" class="form-control">
                            <option value="">Seleziona un responsabile...</option> 
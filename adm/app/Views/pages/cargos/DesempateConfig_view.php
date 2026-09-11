<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><?= esc($titulo) ?></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= base_url("Dashboard") ?>">Home</a></li>
						<li class="breadcrumb-item active">Config Desempate</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">Selecione o Cargo</h3>
						</div>
						<div class="card-body">
							<form method="get" action="<?= base_url('CargosDesempateConfig') ?>">
								<div class="form-group">
									<label for="cargo">Cargo:</label>
									<select class="form-control" name="cargo" id="cargo" onchange="this.form.submit()">
										<option value="">-- Selecione --</option>
										<?php foreach ($cargos as $cargo): ?>
											<option value="<?= $cargo->pk_id_cargo ?>" <?= ($cargoSelecionado && $cargoSelecionado->pk_id_cargo == $cargo->pk_id_cargo) ? 'selected' : '' ?>>
												<?= esc($cargo->ds_nome_cargo) ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php if ($cargoSelecionado): ?>
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Criterios de Desempate - <?= esc($cargoSelecionado->ds_nome_cargo) ?></h3>
							</div>
							<div class="card-body">
								<?php if (empty($configuracoes)): ?>
									<div class="alert alert-info">
										<i class="fas fa-info-circle"></i> Nenhum criterio configurado. Use o formulario abaixo para adicionar.
									</div>
								<?php else: ?>
									<div class="table-responsive">
										<table class="table table-bordered table-striped table-hover">
											<thead>
												<tr>
													<th style="width: 60px;">Ordem</th>
													<th>Tipo</th>
													<th>Descricao</th>
													<th>Referencia</th>
													<th style="width: 120px;">Direcao</th>
													<th style="width: 160px;">Acoes</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($configuracoes as $i => $config): ?>
													<tr data-id="<?= $config->idDesempate ?>">
														<td class="text-center"><strong><?= $config->ordem ?></strong></td>
														<td><span class="badge badge-info"><?= esc($config->tipoCriterio) ?></span></td>
														<td><?= esc($config->descricao) ?></td>
														<td><?= $config->idReferencia ? 'ID: ' . $config->idReferencia : '<em class="text-muted">Nao aplicavel</em>' ?></td>
														<td><span class="badge badge-<?= $config->direcao === 'DESC' ? 'success' : 'warning' ?>">
															<?= $config->direcao === 'DESC' ? 'Maior primeiro' : 'Menor primeiro' ?>
														</span></td>
														<td>
															<?php if ($i > 0): ?>
																<button class="btn btn-sm btn-secondary" title="Subir" onclick="moverCriterio(<?= $config->idDesempate ?>, 'subir')">
																	<i class="fas fa-arrow-up"></i>
																</button>
															<?php else: ?>
																<button class="btn btn-sm btn-secondary" disabled title="Subir">
																	<i class="fas fa-arrow-up"></i>
																</button>
															<?php endif; ?>
															<?php if ($i < count($configuracoes) - 1): ?>
																<button class="btn btn-sm btn-secondary" title="Descer" onclick="moverCriterio(<?= $config->idDesempate ?>, 'descer')">
																	<i class="fas fa-arrow-down"></i>
																</button>
															<?php else: ?>
																<button class="btn btn-sm btn-secondary" disabled title="Descer">
																	<i class="fas fa-arrow-down"></i>
																</button>
															<?php endif; ?>
															<button class="btn btn-sm btn-primary" title="Editar" onclick="editarCriterio(<?= $config->idDesempate ?>)">
																<i class="fas fa-edit"></i>
															</button>
															<button class="btn btn-sm btn-danger" title="Excluir" onclick="excluirCriterio(<?= $config->idDesempate ?>)">
																<i class="fas fa-trash"></i>
															</button>
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
				</div>

				<div class="row">
					<div class="col-12">
						<div class="card card-success">
							<div class="card-header">
								<h3 class="card-title"><i class="fas fa-plus"></i> Adicionar Criterio</h3>
							</div>
							<div class="card-body">
								<form id="form-desempate">
									<input type="hidden" name="fk_id_cargo" value="<?= $cargoSelecionado->pk_id_cargo ?>">
									<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

									<div class="row">
										<div class="col-md-1">
											<div class="form-group">
												<label>Ordem</label>
												<input type="number" class="form-control" name="ds_ordem" value="<?= count($configuracoes) + 1 ?>" min="1" required>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label>Tipo de Criterio <span class="text-danger">*</span></label>
												<select class="form-control" name="ds_tipo_criterio" id="tipo-criterio" required onchange="atualizarReferencia()">
													<option value="">-- Selecione --</option>
													<option value="PONTUACAO_TOTAL">Pontuacao Total</option>
													<option value="PONTUACAO_EXPERIENCIAS">Total Experiencias</option>
													<option value="PONTUACAO_ESCOLARIDADES">Total Escolaridades</option>
													<option value="PONTUACAO_CRITERIOS_ADICIONAIS">Total Criterios Adicionais</option>
													<option value="PONTUACAO_CURSOS_APERFEICOAMENTOS">Total Aperfeicoamentos</option>
													<option value="PONTUACAO_GRADUACAO">Total Graduacao</option>
													<option value="PONTUACAO_POS_GRADUACAO">Total Pos-Graduacao</option>
													<option value="PONTUACAO_MESTRADO">Total Mestrado</option>
													<option value="PONTUACAO_DOUTORADO">Total Doutorado</option>
													<option value="EXPERIENCIA_ESPECIFICA">Experiencia Especifica</option>
													<option value="ESCOLARIDADE_ESPECIFICA">Escolaridade Especifica</option>
													<option value="CRITERIO_ADICIONAL">Criterio Adicional Especifico</option>
													<option value="APERFEICOAMENTO_ESPECIFICO">Aperfeicoamento Especifico</option>
													<option value="IDADE">Idade (mais velho primeiro)</option>
													<option value="PNE">PNE (possui deficiencia)</option>
												</select>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label>Direcao <span class="text-danger">*</span></label>
												<select class="form-control" name="ds_direcao" id="ds_direcao" required>
													<option value="DESC">Maior primeiro</option>
													<option value="ASC">Menor primeiro</option>
												</select>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label>Descricao <span class="text-danger">*</span></label>
												<input type="text" class="form-control" name="ds_descricao" id="ds_descricao" placeholder="Ex: Total de Pontos" required>
											</div>
										</div>

										<div class="col-md-3" id="campo-referencia-container" style="display: none;">
											<div class="form-group">
												<label>Referencia <span class="text-danger">*</span></label>
												<select class="form-control" name="fk_id_referencia" id="fk_id_referencia">
													<option value="">-- Selecione --</option>
												</select>
											</div>
										</div>
									</div>

									<div class="row mt-2">
										<div class="col-12">
											<button type="submit" class="btn btn-success">
												<i class="fas fa-plus"></i> Adicionar Criterio
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>

<!-- Modal de Edicao -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<h5 class="modal-title" id="modalEditarLabel">Editar Criterio de Desempate</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="form-editar">
				<div class="modal-body">
					<input type="hidden" name="pk_id_desempate" id="edit_id_desempate">
					<input type="hidden" name="fk_id_cargo" value="<?= $cargoSelecionado->pk_id_cargo ?? '' ?>">
					<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Ordem</label>
								<input type="number" class="form-control" name="ds_ordem" id="edit_ordem" min="1" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Tipo de Criterio <span class="text-danger">*</span></label>
								<select class="form-control" name="ds_tipo_criterio" id="edit_tipo_criterio" required onchange="atualizarReferenciaEdicao()">
									<option value="">-- Selecione --</option>
									<option value="PONTUACAO_TOTAL">Pontuacao Total</option>
									<option value="PONTUACAO_EXPERIENCIAS">Total Experiencias</option>
									<option value="PONTUACAO_ESCOLARIDADES">Total Escolaridades</option>
									<option value="PONTUACAO_CRITERIOS_ADICIONAIS">Total Criterios Adicionais</option>
									<option value="PONTUACAO_CURSOS_APERFEICOAMENTOS">Total Aperfeicoamentos</option>
									<option value="PONTUACAO_GRADUACAO">Total Graduacao</option>
									<option value="PONTUACAO_POS_GRADUACAO">Total Pos-Graduacao</option>
									<option value="PONTUACAO_MESTRADO">Total Mestrado</option>
									<option value="PONTUACAO_DOUTORADO">Total Doutorado</option>
									<option value="EXPERIENCIA_ESPECIFICA">Experiencia Especifica</option>
									<option value="ESCOLARIDADE_ESPECIFICA">Escolaridade Especifica</option>
									<option value="CRITERIO_ADICIONAL">Criterio Adicional Especifico</option>
									<option value="APERFEICOAMENTO_ESPECIFICO">Aperfeicoamento Especifico</option>
									<option value="IDADE">Idade (mais velho primeiro)</option>
									<option value="PNE">PNE (possui deficiencia)</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Direcao <span class="text-danger">*</span></label>
								<select class="form-control" name="ds_direcao" id="edit_direcao" required>
									<option value="DESC">Maior primeiro</option>
									<option value="ASC">Menor primeiro</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Descricao <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="ds_descricao" id="edit_descricao" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6" id="edit-referencia-container" style="display: none;">
							<div class="form-group">
								<label>Referencia <span class="text-danger">*</span></label>
								<select class="form-control" name="fk_id_referencia" id="edit_referencia">
									<option value="">-- Selecione --</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Salvar Alteracoes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
const referencias = {
    experiencias: <?= json_encode($referencias['experiencias'] ?? []) ?>,
    escolaridades: <?= json_encode($referencias['escolaridades'] ?? []) ?>,
    criterios: <?= json_encode($referencias['criterios'] ?? []) ?>,
    aperfeicoamentos: <?= json_encode($referencias['aperfeicoamentos'] ?? []) ?>
};

const configsExistentes = <?= json_encode(array_map(function($c) {
    return [
        'idDesempate' => $c->idDesempate,
        'ordem' => $c->ordem,
        'tipoCriterio' => $c->tipoCriterio,
        'idReferencia' => $c->idReferencia,
        'direcao' => $c->direcao,
        'descricao' => $c->descricao,
    ];
}, $configuracoes ?? [])) ?>;

function atualizarReferencia() {
    const tipoCriterio = document.getElementById('tipo-criterio').value;
    const container = document.getElementById('campo-referencia-container');
    const select = document.getElementById('fk_id_referencia');

    select.innerHTML = '<option value="">-- Selecione --</option>';

    const mapaTipos = {
        'EXPERIENCIA_ESPECIFICA':     { dados: referencias.experiencias,     campoId: 'fk_id_experiencia',     campoNome: 'ds_nome_experiencia' },
        'ESCOLARIDADE_ESPECIFICA':    { dados: referencias.escolaridades,    campoId: 'fk_id_escolaridade',    campoNome: 'ds_nome_escolaridade' },
        'CRITERIO_ADICIONAL':         { dados: referencias.criterios,        campoId: 'fk_id_criterio',       campoNome: 'ds_nome_criterio' },
        'APERFEICOAMENTO_ESPECIFICO': { dados: referencias.aperfeicoamentos, campoId: 'fk_id_curso',          campoNome: 'ds_nome_curso' }
    };

    const config = mapaTipos[tipoCriterio];

    if (config && config.dados.length > 0) {
        container.style.display = 'block';
        select.required = true;

        config.dados.forEach(function(item) {
            const option = document.createElement('option');
            option.value = item[config.campoId];
            option.textContent = item[config.campoNome];
            select.appendChild(option);
        });
    } else if (config) {
        container.style.display = 'block';
        select.required = true;
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Nenhum item cadastrado para este cargo';
        select.appendChild(option);
    } else {
        container.style.display = 'none';
        select.required = false;
        select.value = '';
    }
}

function atualizarReferenciaEdicao(valorSelecionado = null) {
    const tipoCriterio = document.getElementById('edit_tipo_criterio').value;
    const container = document.getElementById('edit-referencia-container');
    const select = document.getElementById('edit_referencia');

    select.innerHTML = '<option value="">-- Selecione --</option>';

    const mapaTipos = {
        'EXPERIENCIA_ESPECIFICA':     { dados: referencias.experiencias,     campoId: 'fk_id_experiencia',     campoNome: 'ds_nome_experiencia' },
        'ESCOLARIDADE_ESPECIFICA':    { dados: referencias.escolaridades,    campoId: 'fk_id_escolaridade',    campoNome: 'ds_nome_escolaridade' },
        'CRITERIO_ADICIONAL':         { dados: referencias.criterios,        campoId: 'fk_id_criterio',       campoNome: 'ds_nome_criterio' },
        'APERFEICOAMENTO_ESPECIFICO': { dados: referencias.aperfeicoamentos, campoId: 'fk_id_curso',          campoNome: 'ds_nome_curso' }
    };

    const config = mapaTipos[tipoCriterio];

    if (config && config.dados.length > 0) {
        container.style.display = 'block';
        select.required = true;

        config.dados.forEach(function(item) {
            const option = document.createElement('option');
            option.value = item[config.campoId];
            option.textContent = item[config.campoNome];
            if (valorSelecionado && String(item[config.campoId]) === String(valorSelecionado)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    } else if (config) {
        container.style.display = 'block';
        select.required = true;
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Nenhum item cadastrado para este cargo';
        select.appendChild(option);
    } else {
        container.style.display = 'none';
        select.required = false;
        select.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    atualizarReferencia();
});

document.getElementById('form-desempate')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const tipoCriterio = document.getElementById('tipo-criterio').value;
    const referencia = document.getElementById('fk_id_referencia').value;
    const precisaReferencia = ['EXPERIENCIA_ESPECIFICA', 'ESCOLARIDADE_ESPECIFICA', 'CRITERIO_ADICIONAL', 'APERFEICOAMENTO_ESPECIFICO'];

    if (precisaReferencia.includes(tipoCriterio) && !referencia) {
        alert('Por favor, selecione uma referencia para este tipo de criterio.');
        return;
    }

    try {
        const response = await fetch('<?= base_url("CargosDesempateConfig/salvar") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (jsonErr) {
            console.error('Resposta nao e JSON:', text);
            alert('Erro no servidor. Verifique o console.');
            return;
        }

        if (data.success) {
            alert('Criterio salvo com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        alert('Erro na requisicao: ' + error.message);
    }
});

function excluirCriterio(idDesempate) {
    if (!confirm('Deseja realmente excluir este criterio?')) return;

    fetch('<?= base_url("CargosDesempateConfig/excluir") ?>/' + idDesempate, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            alert('Erro no servidor. Verifique o console.');
            console.error(text);
            return;
        }
        if (data.success) {
            alert('Criterio excluido com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        alert('Erro na requisicao: ' + error.message);
    });
}

function editarCriterio(idDesempate) {
    const config = configsExistentes.find(c => String(c.idDesempate) === String(idDesempate));
    if (!config) {
        alert('Criterio nao encontrado.');
        return;
    }

    document.getElementById('edit_id_desempate').value = config.idDesempate;
    document.getElementById('edit_ordem').value = config.ordem;
    document.getElementById('edit_tipo_criterio').value = config.tipoCriterio;
    document.getElementById('edit_direcao').value = config.direcao;
    document.getElementById('edit_descricao').value = config.descricao;

    atualizarReferenciaEdicao(config.idReferencia);

    $('#modalEditar').modal('show');
}

document.getElementById('form-editar')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const tipoCriterio = document.getElementById('edit_tipo_criterio').value;
    const referencia = document.getElementById('edit_referencia').value;
    const precisaReferencia = ['EXPERIENCIA_ESPECIFICA', 'ESCOLARIDADE_ESPECIFICA', 'CRITERIO_ADICIONAL', 'APERFEICOAMENTO_ESPECIFICO'];

    if (precisaReferencia.includes(tipoCriterio) && !referencia) {
        alert('Por favor, selecione uma referencia para este tipo de criterio.');
        return;
    }

    try {
        const response = await fetch('<?= base_url("CargosDesempateConfig/salvar") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (jsonErr) {
            console.error('Resposta nao e JSON:', text);
            alert('Erro no servidor. Verifique o console.');
            return;
        }

        if (data.success) {
            alert('Criterio atualizado com sucesso!');
            $('#modalEditar').modal('hide');
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        alert('Erro na requisicao: ' + error.message);
    }
});

function moverCriterio(idDesempate, direcao) {
    if (!confirm('Deseja mover este criterio para ' + (direcao === 'subir' ? 'cima' : 'baixo') + '?')) return;

    fetch('<?= base_url("CargosDesempateConfig/mover") ?>/' + idDesempate + '?direcao=' + direcao, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            alert('Erro no servidor. Verifique o console.');
            console.error(text);
            return;
        }
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        alert('Erro na requisicao: ' + error.message);
    });
}
</script>

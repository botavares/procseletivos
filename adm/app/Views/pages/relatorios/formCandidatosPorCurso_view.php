<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-sm-12">

                    <div class="card card-outline card-primary shadow-sm">

                        <!-- HEADER -->
                        <div class="card-header d-flex justify-content-between align-items-center">
							<div class="mb-2 col-md-12">
								<h3 class="card-title m-0">
									<i class="fas fa-file-alt mr-2"></i>
									<?= esc($titulo) ?>
								</h3>
							</div>
                        </div>
						<!-- TOOLBOX -->
						<div class="card-toolbox mt-2 mb-2 d-flex flex-row ">
							<div class="mb-2 ml-1 mr-2">
								<a href="<?= esc($urlVoltar) ?>"
								class="btn btn-warning btn-sm">
									<i class="fas fa-arrow-left mr-1"></i> Voltar
								</a>
							</div>
							<!-- COLOCAR AQUI OUTROS BOTÕES SE PRECISAR UTILIZANDO A FORMATAÇÃO DO BOTOÂ DE CIMA-->
						</div>
                        <!-- ALERTA DE ERROS -->
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('error') as $erro): ?>
                                        <li><?= esc($erro) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>

                        <!-- BODY -->
                        <div class="card-body">
                            <form id="formCursos"
                                  method="POST"
                                  action="<?= url_to('Relatorios.relatorioCandidatosPorCurso.post') ?>"
                                  target="_blank"
                                  class="form-horizontal">

                                <?= csrf_field() ?>

                                <div class="form-group row">
                                    <label for="select-cursos" class="col-sm-3 col-form-label">
                                        Curso <span class="text-danger">*</span>
                                    </label>

                                    <div class="col-sm-9">
                                        <select id="select-cursos"
                                                name="pk_id_curso"
                                                class="form-control"
                                                required>
                                            <option value="">Selecione um curso</option>

                                            <?php foreach ($cursos as $curso): ?>
                                                <option value="<?= $curso->pk_id_curso ?>"
                                                    <?= old('pk_id_curso') == $curso->pk_id_curso ? 'selected' : '' ?>>
                                                    <?= esc($curso->ds_nome_curso) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>

                                        <small class="text-danger">
                                            <?= session()->getFlashdata('errors')['pk_id_curso'] ?? '' ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="text-right mt-4">
                                    <button type="reset" class="btn btn-secondary mr-2">
                                        <i class="fas fa-eraser"></i> Limpar
                                    </button>

                                    <button type="submit" class="btn btn-success btnRelatorio">
                                        <i class="fas fa-print"></i> Imprimir
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

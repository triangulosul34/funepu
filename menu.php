<?php include('verifica.php'); ?>
<div data-active-color="white" data-background-color="primary" data-image="app-assets/img/sidebar-bg/01.jpg" class="app-sidebar">
    <div class="sidebar-header">
        <div class="logo clearfix"><a href="index.php" class="logo-text float-left">
                <div class="logo-img"><img src="app-assets/img/gallery/logo_funepu.png" width='140px' /></div>
            </a></div>
    </div>
    <div class="sidebar-content">
        <div class="nav-container">
            <ul id="main-menu-navigation" data-menu="menu-navigation" data-scroll-to-active="true" class="navigation navigation-main">
                <?php  ?>
                <li class="has-sub nav-item">
                    <a href="#"><i class="fas fa-briefcase-medical"></i><span class="menu-title">ATENDIMENTOS</span></a>
                    <ul class="menu-content">
                        <?php if ($perfil == '06' or $perfil == '08') { ?>
                            <li class="is-shown">
                                <a href="monitor_triagem.php" class="menu-item">TRIAGEM</a>
                            </li>
                        <?php }
                        if ($perfil == '06' or $perfil == '03' or $perfil == '08') { ?>
                            <li class="is-shown">
                                <a href="monitor_medico.php" class="menu-item">ATENDIMENTO MEDICO</a>
                            </li>
                        <?php } ?>
                        <li class="is-shown">
                            <a href="atendimentos.php" class="menu-item">ATENDIMENTOS</a>
                        </li>
                        <?php
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                            <li class="is-shown">
                                <a href="atendimentoretroativo.php" class="menu-item">ATEND. RETROATIVO</a>
                            </li>
                        <?php
                        }
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                            <li class="is-shown">
                                <a href="resultado_exames.php" class="menu-item">RESULTADO DE EXAMES</a>
                            </li>
                        <?php }
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                            <li class="is-shown">
                                <a href="evolucoes.php" class="menu-item">EVOLUCAO DIARIA</a>
                            </li>
                        <?php } ?>
                        <li class="is-shown">
                            <a href="evolucoesmp.php" class="menu-item">ASSISTENCIA SOCIAL</a>
                        </li>
                        <?php
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                            <li class="is-shown">
                                <a href="internacoes.php" class="menu-item">SOLIC. INTERNACAO</a>
                            </li>
                        <?php }
                        if ($perfil == '06' or $perfil == '03') { ?>
                            <li class="is-shown">
                                <a href="selformapac.php" class="menu-item">SOLICITAÇÃO APAC</a>
                            </li>

                        <?php } ?>
                    </ul>
                </li>
                <?php
                if ($perfil == '06' or $perfil == '04' or $perfil == '01') { ?>
                    <li class="has-sub nav-item">
                        <a href="#"><i class="fas fa-user-plus"></i><span class="menu-title">CADASTRO</span></a>
                        <ul class="menu-content">
                            <?php
                            if ($perfil == '06' or $perfil == '04' or $perfil == '01') { ?>
                                <li class="is-shown">
                                    <a href="clientes.php" class="menu-item">PACIENTES</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="medicos.php" class="menu-item">MEDICOS/LAUDADORES</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="colaboradores.php" class="menu-item">COLABORADORES</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="procedimentos.php" class="menu-item">PROCEDIMENTOS</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php }
                if ($perfil == '06' or $perfil == '04') { ?>
                    <li class="has-sub nav-item">
                        <a href="#"><i class="fas fa-tasks"></i><span class="menu-title">GESTÃO</span></a>
                        <ul class="menu-content">
                            <li class="is-shown">
                                <a href="contpermanencia.php" class="menu-item">CONTROLE DE PERMANÊNCIA</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatoriodiario.php" class="menu-item">REL. DIARIO</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioatendimento.php" class="menu-item">REL. DE ATENDIMENTOS</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioatendimentodemanda.php" class="menu-item">REL. DE DEMANDA</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioatendimentoclassificacao.php" class="menu-item">REL. DE CLASS</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioatendimentosmod.php" class="menu-item">REL. DE EX. MODALIDADE</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioatendimentosexames.php" class="menu-item">REL. DE EXAMES</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorio_grafico.php" class="menu-item">REL. GRAFICO</a>
                            </li>
                            <li class="is-shown">
                                <a href="relatorioproducaomed.php" class="menu-item">PRODUÇÃO MEDICA</a>
                            </li>
                            <li class="is-shown">
                                <a href="presencamedica.php" class="menu-item">PRESENÇA MEDICA</a>
                            </li>
                            <li class="is-shown">
                                <a href="acoes_usuario.php" class="menu-item">LOGS</a>
                            </li>
                            <li class="has-sub nav-item">
                                <a href="#"><span class="menu-title">FORMULARIOS</span></a>
                                <ul class="menu-content">
                                    <li class="is-shown">
                                        <a href="../sb/formularios/fluxograma.pdf" class="menu-item">FLUXOGRAMA</a>
                                    </li>
                                    <li class="is-shown">
                                        <a href="../sb/formularios/formularioacidentetrabalho.pdf" class="menu-item">ACIDENTE TRABALHO</a>
                                    </li>
                                    <li class="is-shown">
                                        <a href="../sb/formularios/termoconsentimento.pdf" class="menu-item">TER.
                                            CONSENTIMENTO</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php }
                if ($perfil == '06' or $perfil == '08' or $perfil == '04') { ?>
                    <li class="has-sub nav-item"><a href="#"><i class="fas fa-tv"></i><span class="menu-title">MONITORAMENTO</span></a>
                        <ul class="menu-content">
                            <?php
                            if ($perfil == '06' or $perfil == '08' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="triagemRecepcao.php" class="menu-item">AGUARD. TRIAGEM</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '08' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="atendimentoRecepcao.php" class="menu-item">AGUARD. ATENDIMENTO</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="monitorAtendMedicos.php" class="menu-item">EM ATENDIMENTO</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php }
                if ($perfil == '06' or $perfil == '03' or $perfil == '05' or $perfil == '04') { ?>
                    <li class="has-sub nav-item">
                        <a href="#"><i class="fas fa-chart-pie"></i><span class="menu-title">PAINÉIS</span></a>
                        <ul class="menu-content">
                            <?php
                            if ($perfil == '06' or $perfil == '05' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="painel_us.php" class="menu-item">PAINEL US</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '05' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="painel_rx.php" class="menu-item">PAINEL RX</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '05' or $perfil == '04') { ?>
                                <li class="is-shown">
                                    <a href="painel_ecg.php" class="menu-item">PAINEL ECG</a>
                                </li>
                            <?php }
                            if ($perfil == '06' or $perfil == '04') { ?>
                                <li class="has-sub nav-item">
                                    <a href="#"><span class="menu-title">ATENDIMENTOS</span></a>
                                    <ul class="menu-content">
                                    <?php }
                                if ($perfil == '06' or $perfil == '04') { ?>
                                        <li class="is-shown">
                                            <a href="painel_at_adulto.php" class="menu-item">ADULTO</a>
                                        </li>
                                    <?php }
                                if ($perfil == '06' or $perfil == '04') { ?>
                                        <li class="is-shown">
                                            <a href="painel_at_odontologico.php" class="menu-item">ODONTOLOGICO</a>
                                        </li>
                                    <?php }
                                if ($perfil == '06' or $perfil == '04') { ?>
                                        <li class="is-shown">
                                            <a href="painel_at_ortopedia.php" class="menu-item">ORTOPEDIA</a>
                                        </li>
                                    <?php }
                                if ($perfil == '06' or $perfil == '08' or $perfil == '03' or $perfil == '04') { ?>
                                        <li class="is-shown">
                                            <a href="painel_at_ala_vermelha.php" class="menu-item">ALA VERMELHA</a>
                                        </li>
                                        <li class="is-shown">
                                            <a href="painel_permanencia.php" class="menu-item">PERMANÊNCIA</a>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </li>
                        </ul>
                    </li>
                <?php } ?>
                <li class="has-sub nav-item">
                    <a href="#"><i class="fas fa-file-alt"></i><span class="menu-title">FICHAS DE FORM.</span></a>
                    <ul class="menu-content">
                        <li class="is-shown">
                            <a href="sindrome_gripal.php" class="menu-item">FICHA SÍNDROME GRIPAL</a>
                        </li>
                    </ul>
                </li>
                <?php if ($perfil == '07' or $perfil == '06') { ?>
                    <li class="nav-item">
                        <a href="pedidos.php"><i class="fas fa-file-prescription"></i><span class="menu-title">LAUDOS</span></a>
                    </li>
                <?php }
                if ($perfil != '08' && $perfil != '03') { ?>
                    <li class="nav-item">
                        <a href="farmacia.php"><i class="fas fa-pills"></i><span class="menu-title">FARMACIA</span></a>
                    </li>
                <?php }
                if ($perfil == '14' or $perfil == '06') { ?>
                    <li class="nav-item">
                        <a href="novopedido.php"><i class="fas fa-clinic-medical"></i><span class="menu-title">SOLICITAR
                                EXAMES</span></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="row" style="position: absolute;bottom: 5%;left: 18%;z-index: 2;">
        <div class="col-7">
            <img src="app-assets/img/gallery/logotsul.png" width="150px" height="130px">
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<?php include('verifica.php'); ?>
<div data-active-color="white" data-background-color="primary" data-image="../app-assets/img/sidebar-bg/01.jpg"
    class="app-sidebar">
    <div class="sidebar-header">
        <div class="logo clearfix"><a href="index.php" class="logo-text float-left">
                <div class="logo-img"><img src="../app-assets/img/gallery/logo_funepu.png" width='140px' /></div>
            </a></div>
    </div>
    <div class="sidebar-content">
        <div class="nav-container">
            <ul id="main-menu-navigation" data-menu="menu-navigation" data-scroll-to-active="true"
                class="navigation navigation-main">
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
                            <a href="#" class="menu-item">ATENDIMENTOS</a>
                        </li>
                        <?php
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                        <li class="is-shown">
                            <a href="atendimentoretroativo.php" class="menu-item">ATEND. RETROATIVO</a>
                        </li>
                        <?php
                        }
                        //if ($perfil == '06' or $perfil == '08' or $perfil == '03') {
                        ?>
                        <li class="is-shown">
                            <a href="pedidos.php" class="menu-item">RESULTADO DE EXAMES</a>
                        </li>
                        <?php //}
                        if ($perfil == '06' or $perfil == '08' or $perfil == '03') { ?>
                        <li class="is-shown">
                            <a href="evolucoes.php" class="menu-item">EVOLUCAO DIARIA</a>
                        </li>
                        <?php } ?>
                        
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
                
        </div>
    </div>
    <div class="row" style="position: absolute;bottom: 5%;left: 18%;z-index: 2;">
        <div class="col-7">
            <img src="../app-assets/img/gallery/logotsul.png" width="150px" height="130px">
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="tsul" content="tsul">
    <meta name="keywords" content="tsul">
    <meta name="author" content="TSUL">
    <title>FUNEPU | Pagina Padrao</title>
    <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <!--load all styles -->
    <script language="Javascript">
        function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('endereco').value = ("");
            document.getElementById('bairro').value = ("");
            document.getElementById('cidade').value = ("");
            document.getElementById('uf').value = ("");
        }

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                //Atualiza os campos com os valores.
                document.getElementById('endereco').value = (conteudo.logradouro);
                document.getElementById('end_bairro').value = (conteudo.bairro);
                document.getElementById('end_cidade').value = (conteudo.localidade);
                document.getElementById('end_uf').value = (conteudo.uf);
            } //end if.
            else {
                //CEP não Encontrado.
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
        }

        function pesquisacep(valor) {

            //Nova variável "cep" somente com dígitos.
            var cep = valor.replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if (validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    document.getElementById('endereco').value = "...";
                    document.getElementById('end_bairro').value = "...";
                    document.getElementById('end_cidade').value = "...";
                    document.getElementById('end_uf').value = "...";

                    //Cria um elemento javascript.
                    var script = document.createElement('script');

                    //Sincroniza com o callback.
                    script.src = '//viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

                    //Insere script no documento e carrega o conteúdo.
                    document.body.appendChild(script);

                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        };


        function calcularIdade() {

            dataNasc = document.getElementById("dt_nascimento").value;


            var dataAtual = new Date();

            //alert(" teste " + dataAtual);
            //Ex: Tue Mar 27 2012 09:43:24

            var anoAtual = dataAtual.getFullYear();
            //alert(" teste " + anoAtual);
            //teste 2012

            var anoNascParts = dataNasc.split('/');


            var diaNasc = anoNascParts[0];
            //alert("dia é..:" + diaNasc);

            var mesNasc = anoNascParts[1];
            var anoNasc = anoNascParts[2];


            var idade = anoAtual - anoNasc;


            var mesAtual = dataAtual.getMonth() + 1;


            //se mês atual for menor que o nascimento,não faz aniversario ainda.
            if (mesAtual < mesNasc) {
                idade--;
            } else {
                //se tiver no mes do nasc,verificar o dia
                if (mesAtual <= mesNasc) {
                    if (dataAtual.getDay() < diaNasc) {
                        //se a data atual for menor que o dia de nascimento,quer dizer que ele ainda não fez aniversario
                        idade--;
                    }

                }

            }

            document.getElementById("idade").value = idade;





        }

        function showpagtos() {
            conv = document.getElementById("conveniox").value;
            dvd = 'S';
            d4 = 'S';
            abre = 'N';
            if (document.getElementById("dvd").checked) {
                dvd = 'S';
            } else {
                dvd = 'N';
            }
            if (document.getElementById("d4").checked) {
                d4 = 'S';
            } else {
                d4 = 'N';
            }

            div = conv.substring(0, 1);

            if ((dvd == "S")) {
                abre = 'S';
            }
            if ((d4 == "S")) {
                abre = 'S';
            }
            if ((div == "P")) {
                abre = 'S';
            }
            if ((abre == "S")) {
                document.getElementById("pagamentos").style.display = 'block';
                document.getElementById("carteirinha").value = '';
                document.getElementById("carteirinha").readOnly = true;
                document.getElementById("nroguia").value = '';
                document.getElementById("nroguia").readOnly = true;
            }
            if (abre == "N") {
                document.getElementById("pagamentos").style.display = 'none';
                document.getElementById("carteirinha").readOnly = false;
                document.getElementById("nroguia").readOnly = false;
            }
        }

        function showenfermaria(div) {
            div = div.substring(0, 1);
            if (div == "E") {
                document.getElementById("enfermaria").value = '';
                document.getElementById("enfermaria").readOnly = true;
                document.getElementById("leito").value = '';
                document.getElementById("leito").readOnly = true;
            }
            if (div == "I") {
                document.getElementById("enfermaria").readOnly = false;
                document.getElementById("leito").readOnly = false;
            }
        }

        function SomenteNumero(e) {
            var tecla = (window.event) ? event.keyCode : e.which;
            if ((tecla > 47 && tecla < 58)) return true;
            else {
                if (tecla == 8 || tecla == 0) return true;
                else return false;
            }
        }

        function showDiv(div) {
            if (div == "DD") {
                document.getElementById("bandeira").style.display = 'none';
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("num_cheque").style.display = 'none';
            }
            if (div == "CD") {
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("bandeira").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
            if (div == "CC") {
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("bandeira").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
            if (div == "CH") {
                document.getElementById("bandeira").style.display = 'none';
                document.getElementById("banco_num").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
        }
    </script>
    <style>
        .invisivel {
            display: none;
        }

        .visivel {
            visibility: visible;
        }
    </style>
    <script>
        function buscar_agenda(data) {
            var estado = $('#modalidade').val();
            if (estado) {
                var url = 'ajax_buscar_agenda.php?data=' + data + '&modalidade=' + estado;
                $.get(url, function(dataReturn) {
                    $('#load_agenda').html(dataReturn);
                });
            }
        }

        function buscar_sgrupos() {
            var estado = $('#cod_id').val();
            var conv = $('#conveniox').val();
            if (estado) {
                var url = 'ajax_buscar_proc.php?estado=' + estado + '&convenio=' + conv;
                $.get(url, function(dataReturn) {
                    $('#load_cidades').html(dataReturn);
                });
            }
        }


        function doConfirm(id) {

            var ok = confirm("Confirma a exclusao?<?php echo $transacao; ?>")
            if (ok) {

                if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.location = "agendaexame.php?data=<?php echo date('Y-m-d'); ?>";
                        window.location.reload()
                    }
                }

                xmlhttp.open("GET", "apagaagendatemp.php?id=<?php echo $transacao; ?>");
                xmlhttp.send();
            }
        }

        function aConf(mes) {
            alertify.confirm(mes, function(e) {
                return e;
            });
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }

        function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e) {
            var sep = 0;
            var key = '';
            var i = j = 0;
            var len = len2 = 0;
            var strCheck = '0123456789';
            var aux = aux2 = '';
            var whichCode = (window.Event) ? e.which : e.keyCode;
            if (whichCode == 13) return true;
            key = String.fromCharCode(whichCode); // Valor para o código da Chave
            if (strCheck.indexOf(key) == -1) return false; // Chave inválida
            len = objTextBox.value.length;
            for (i = 0; i < len; i++)
                if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
            aux = '';
            for (; i < len; i++)
                if (strCheck.indexOf(objTextBox.value.charAt(i)) != -1) aux += objTextBox.value.charAt(i);
            aux += key;
            len = aux.length;
            if (len == 0) objTextBox.value = '';
            if (len == 1) objTextBox.value = '0' + SeparadorDecimal + '0' + aux;
            if (len == 2) objTextBox.value = '0' + SeparadorDecimal + aux;
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += SeparadorMilesimo;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
                objTextBox.value = '';
                len2 = aux2.length;
                for (i = len2 - 1; i >= 0; i--)
                    objTextBox.value += aux2.charAt(i);
                objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
            }
            return false;
        }

        function busca_tabela() {
            var conv = $('#conveniox').val();
            if (conv) {
                var url = 'tabelas.php?convenio=' + conv;
                $.get(url, function(dataReturn) {
                    $('#procedimento').load(dataReturn);
                });
            }
        }


        function mascaratel(campo) {
            campo.value = campo.value.replace(/[^\d]/g, '')
                .replace(/^(\d\d)(\d)/, '($1) $2')
                .replace(/(\d{4})(\d)/, '$1-$2');
            if (campo.value.length > 14) campo.value = stop;
            else stop = campo.value;
        }


        function mascara(str) {
            // Caso passe de 14 caracteres será formatado como CNPJ 
            if (str.value.length > 14)
                str.value = cnpj(str.value);
            // Caso contrário como CPF
            else
                str.value = cpf(str.value);
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        // Funcao de formatacao CPF
        function cpf(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valoralor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o terceiro e o quarto digito
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um ponto entre o terceiro e o quarto dígitos 
            // desta vez para o segundo bloco      
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um hifen entre o terceiro e o quarto dígitos
            valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");
            return valor;
        }

        // Funcao de formatacao CNPJ
        function cnpj(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o segundo e o terceiro dígitos
            valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");

            // Adiciona um ponto entre o quinto e o sexto dígitos
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");

            // Adiciona uma barra entre o oitavaloro e o nono dígitos
            valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");

            // Adiciona um hífen depois do bloco de quatro dígitos
            valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            return valor;
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }
    </script>


</head>
<style>
    hr {
        color: #12A1A6;
        background-color: #12A1A6;
        margin-top: 0px;
        margin-bottom: 0px;
        height: 4px;
        width: 300px;
        margin-left: 0px;
        border-top-width: 0px;
    }
</style>

<body class="pace-done" cz-shortcut-listen="true">
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <?php include('menu.php'); ?>
    <?php include('header.php'); ?>
    <div class="main-panel">
        <div class="main-content">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- <div class="card-header" style="display: flex;align-items: center;justify-content: space-between; background: #00777a"> -->

                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="card-title">
                                                    <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Página Padrão
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CORPO DA PAGINA -->
                        <div class="card-content">
                            <div class="col-12">
                                <div class="card-content">

                                    <form method="post" name='pedido' id='pedido' autocomplete="off">
                                        <div id="dados-paciente-div">
                                            <div class="col-12 text-center">
                                                <h4 class="form-section-center"><i class="ft-user"></i> Identificação do Paciente</h4>
                                                <!-- <h3 class="title" align="center">Identificação do Paciente</h3> -->
                                                <hr style="margin: auto;width: 350px">
                                            </div>

                                            <!-- DADOS PACIENTE -->
                                            <div class="row mt-4">
                                                <div class="row">
                                                    <div class="col-1">
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <div>
                                                                    <?php
                                                                    if ($imagem == "") {
                                                                        echo "<img id=\"blah\" src=\"app-assets/img/gallery/user-transp.png\"  alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('popcam/index.html', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=700'); return false;\">";
                                                                    } else {
                                                                        echo "<img id=\"blah\" src=\"app-assets/img/gallery/user-transp.png\"         alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('popcam/index.html', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=700'); return false;\">";
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-3">
                                                            <div class="col-2">
                                                                <!-- <label class="control-label">
                                                            <font color='red'>Paciente</font>
                                                        </label> -->
                                                                <div class="input-group ">
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-primary" onClick="window.open('poppac.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;" style="margin-left: 50px">
                                                                            <i class="fa fa-search" aria-hidden="true"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-11">
                                                    <div class="row">
                                                        <input type="hidden" class="form-control" name="prontuario" id="prontuario" placeholder="Paciente..." value='<?php echo $prontuario; ?>' readonly>
                                                        <input type="hidden" class="form-control" name="imagem" id="imagem" value="<?php echo $imagem; ?>" value='<?php echo $imagem; ?>' readonly>

                                                        <div class="col-sm-6">
                                                            <label class="control-label">Nome </label> <input type="text" name="nome" id="nome" class="form-control" style="font-weight: bold;" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)">
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <label class="control-label">Nome Social</label> <input type="text" name="nome_social" id="nome_social" class="form-control" value="<?php echo $nome_social; ?>" style="font-weight: bold;" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <label class="control-label">Nome do Acompanhante</label> <input type="text" name="nome_acompanhante" id="nome_acompanhante" class="form-control" value="<?php echo $nome_acompanhante; ?>" style="font-weight: bold;" onkeyup="maiuscula(this)">
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label class="control-label">
                                                                <font color='red'>CNS</font>
                                                            </label> <input type="text" name="cns" id="cns" class="form-control" value="<?php echo $cns; ?>" onkeypress='return SomenteNumero(event)'>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Nascimento</label> <input type="text" name="dt_nascimento" id="dt_nascimento" class="form-control" value="<?php echo $dt_nascimento; ?>" OnKeyPress="formatar('##/##/####', this)" OnBlur="calcularIdade(this.value)">
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <label class="control-label">Idade</label> <input type="text" name="idade" id="idade" class="form-control" value="<?php echo $idade; ?>" readonly>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <label class="control-label">Sexo</label> <input type="text" name="sexo" id="sexo" class="form-control" value="<?php echo $sexo; ?>" onkeyup="maiuscula(this)"> <input type="hidden" name="pendencia" id="pendencia" class="form-control" value="<?php echo $pendencia; ?>" readonly>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <label class="control-label">CPF</label> <input type="text" name="cpf" OnKeyPress="formatar('###.###.###-##', this)" maxlength="14" id="cpf" class="form-control" value="<?php echo $cpf; ?>">
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <label class="control-label">RG</label> <input type="text" name="rg" id="rg" class="form-control" value="<?php echo $identidade; ?>">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Expedição</label> <input type="text" name="org_expeditor" id="org_expeditor" class="form-control" value="<?php echo $org_expeditor; ?>"><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label class="control-label">
                                                        <font color='red'>Telefone</font>
                                                    </label> <input type="text" name="telefone" class="form-control" value="<?php echo $telefone; ?>" OnKeyPress="formatar('##-########', this)" maxlength="11">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">Celular</label> <input type="text" name="celular" id="celular" class="form-control" value="<?php echo $celular; ?>" OnKeyPress="formatar('##-#########', this)" maxlength="12">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="control-label">
                                                        <font color='red'>Nome da Mae</font>
                                                    </label> <input type="text" name="nomeMae" class="form-control" value="<?php echo $nomeMae; ?>" onkeyup="maiuscula(this)">
                                                </div>


                                                <div class="col-sm-4">
                                                    <label class="control-label">
                                                        <font color='red'>Origem</font>
                                                    </label>
                                                    <select class="form-control" name="origem" id="origem" onChange="showenfermaria(this.value)">
                                                        <option value=""></option>;
                                                        <?php
                                                        include('conexao.php');
                                                        $stmt = "Select * from tipo_origem where situacao='0' order by atendimento";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        while ($row = pg_fetch_object($sth)) {
                                                            echo "<option value=\"" . $row->tipo_id . "\"";
                                                            if ($row->tipo_id == $origem) {
                                                                echo "selected";
                                                            }
                                                            echo ">" . $row->origem . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-3 justify-content-center">
                                                <div class="custom-control custom-checkbox ">
                                                    <input type="checkbox" class="custom-control-input" name="coronavirus" id="coronavirus" value='CM' <?php if ($coronavirus == 1) echo "checked"; ?>>
                                                    <label class="custom-control-label" style="font-size: 10pt" for="coronavirus">Problema Respirátorio</label>
                                                </div>
                                            </div>

                                            <!-- <div class="col-11 text-center mt-3">
                                                        <h4 class="form-section-center"><i class="far fa-map"></i> Endereço do Paciente</h4>
                                                        <hr style="margin: auto;width: 350px">
                                                    </div> -->

                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label class="control-label">CEP</label> <input type="text" name="end_cep" id="end_cep" class="form-control" maxlength="9" value="<?php echo $cep; ?>" OnKeyPress="formatar('#####-###', this)" onblur="pesquisacep(this.value);">
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="control-label">Endereço</label> <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo $enderecox; ?>">
                                                </div>

                                                <div class="col-sm-1">
                                                    <label class="control-label">Numero</label> <input type="text" name="end_num" id="end_num" class="form-control" value="<?php echo $end_numero; ?>">
                                                </div>

                                                <div class="col-sm-4">
                                                    <label class="control-label">Complemento</label> <input type="text" name="end_comp" id="end_comp" class="form-control" value="<?php echo $complemento; ?>">
                                                </div>
                                            </div>

                                            <!-- <hr> -->

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label class="control-label">Bairro</label> <input type="text" name="end_bairro" id="end_bairro" class="form-control" value="<?php echo $bairro; ?>">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="control-label">Cidade</label> <input type="text" name="end_cidade" id="end_cidade" class="form-control" value="<?php echo $cidade; ?>">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">UF</label>
                                                    <input type="text" name="end_uf" id="end_uf" class="form-control" value="<?php echo $estado; ?>" maxlength="2" onkeyup="maiuscula(this)">
                                                </div>

                                            </div>

                                        </div>
                                        <!-- <hr style="width: 100%; color: gray; height: 1px; background-color: gray;" /> -->
                                        <input id="usuario-autorizado" name="usuario-autorizado" type="hidden" />
                                        <input id="senha-autorizado" name="senha-autorizado" type="hidden" />

                                        <div class="col-md-12 mt-3" align="center">
                                            <div class="form-group">
                                                <input type='submit' name='gravar' class="btn btn-primary" value='Gravar' onclick="return validar()">
                                                <input type='submit' name='xcancelar' class="btn btn-danger" value='Cancelar'>
                                            </div>
                                        </div>


                                        <!-- FINAL DADOS PACIENTE -->
                                        <?php
                                        if ($transacao != "") {
                                            include('conexao.php');
                                            $stmt = "SELECT count(*) as qtde FROM arquivos_documentos where transacao=$transacao";
                                            $sth = pg_query($stmt) or die($stmt);
                                            $row = pg_fetch_object($sth);
                                            if ($row->qtde > 0) {
                                                echo '<div class="col-sm-12">';
                                                echo '<h3 class="page-title" align="center">Anexos</h3>';
                                                echo '<hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />';
                                                echo '<div class="col-sm-12" id="anexos"';
                                                echo '<div class="col-md-12">';
                                                echo '<div class="form-group">';
                                                echo '<table class="table table-hover table-striped width-full">';
                                                echo '<thead><tr>';
                                                echo "<th width='15%'>Data</th><th width='30%'>Tipo</th><th width='20%'>Descricao</th><th width='25%'>Usuario</th><th width='10%'>Açao<th>";
                                                echo '</tr></thead><tbody>';
                                                $x = 0;
                                                include('conexao.php');
                                                $stmt = "SELECT a.tipo_doc_id, a.descricao, a.data_arquivo, a.usuario, a.arquivo, b.descricao as tipo 
                                                            FROM arquivos_documentos a, tipo_documentos b where a.tipo_doc_id=b.tipo_doc_id and transacao=$transacao and arquivo is not null 
                                                            order by data_arquivo ";
                                                $sth = pg_query($stmt) or die($stmt);
                                                while ($row = pg_fetch_object($sth)) {
                                                    $x = $x + 1;
                                                    echo "<tr>";
                                                    echo "<td>" . inverteData($row->data_arquivo) . "</td>";
                                                    echo "<td>" . $row->tipo . "</td>";
                                                    echo "<td>" . $row->descricao . "</td>";
                                                    echo "<td>" . $row->usuario . "</td>";
                                                    echo "<td><a href='imagens/documentos/" . $row->arquivo . "' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                    echo "</tr>";
                                                    $total_recebido = $total_recebido + $row->valor;
                                                }
                                                echo "</tbody></table>";
                                                echo "</div>";
                                                echo "</div>";
                                                echo "<br>";
                                                echo "</div>";
                                                echo "</div>";
                                            }
                                        }

                                        ?>
                                </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php include('footer.php'); ?>
    <!-- </div> -->

    <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/chartist.min.js" type="text/javascript"></script>
    <script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/customizer.js" type="text/javascript"></script>
    <script src="app-assets/js/dashboard1.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <script src="app-assets/js/scripts.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script>
        $(document).ready(function() {
            $('#pedido').on('focus', ':input', function() {
                $(this).attr('autocomplete', 'off');
            });
        });

        $("#procedimento").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
        });
        $(document).ready(function() {
            $('#procedimento').chosen.change(function() {
                var cbo = document.getElementById("procedimento");
                var codigotabela = cbo.options[cbo.selectedIndex].text;
                document.getElementById("codigo").value = codigotabela.substring(0, 8);
                document.getElementById("qtde").value = '01';


            });
        });


        // Autenticacao da pendÃªncia do paciente
        $("#autoriza-pendencia-bt").click(function() {

            var oUsuarioAutentica = $("#usuario-autentica").val();
            var oSenhaAutentica = $("#senha-autentica").val();

            $.post("autentica-usuario.php", {
                    usuario: oUsuarioAutentica,
                    senha: oSenhaAutentica
                }, function(data) {

                }).done(function(data) {

                    if (data == "1") {

                        var oNome = $("#nome");
                        var sHtmlI = '<i title="PendÃªncia autorizada!" class="icon fa-check has-warning" aria-hidden="true" style="margin-left: 1em; cursor:pointer; font-size: 1.2em;"></i>';

                        sweetAlert("UsuÃ¡rio autenticado com sucesso!", "", "success");

                        oNome.closest("div").find("label").after(sHtmlI)

                        $("#pendencia-modal-i").off("click");

                        $("#usuario-autorizado").val(oUsuarioAutentica);
                        $("#senha-autorizado").val(oSenhaAutentica);

                    } else if (data == "0") {
                        sweetAlert("Falha na autenticacao!", "Tente novamente.", "error");
                    }
                })
                .fail(function() {
                    sweetAlert("Falha na autenticacao!", "Tente novamente.", "error");
                });
        });

        // Reseta os campos do paciente
        function resetaDadosPac() {

            $("#dados-paciente-div").find(":text, :hidden").prop("readonly", false);
            $("#dados-paciente-div").find(":text, :hidden").val("");
            $("#nome, #cpf").closest("div").removeClass("has-warning");
            $("#nome").closest("div").find("i").remove();
            $("#usuario-autorizado").val("");
            $("#senha-autorizado").val("");
        }

        //$("#dados-paciente-div").find(":text, :hidden").prop("readonly", true);

        // Desabilida a tecla enter em autorizar pendÃªncia (bug)
        $("#usuario-autentica, #senha-autentica").keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

        function valida() {

            var nome = $("#nome").val();
            var dt_nascimento = $("#dt_nascimento").val();
            var endereco = $("#endereco").val();
            var numero = $("#numero").val();
            var bairro = $("#bairro").val();
            var cidade = $("#cidade").val();
            var estado = $("#estado").val();
            var cep = $("#cep").val();
            var cns = $("#cns").val();
            var dt_solicitacao = $("#dt_solicitacao").val();
            var solicitante = $("#solicitante").val();
            var nomesolicitante = $("#nomesolicitante").val();
            var convenio = $("#convenio").val();
            //var tipoconv      = convenio.substr(0, 1);
            var carteirinha = $("#carteirinha").val();
            var nomeMae = $("#nomeMae").val();

            if (nome == "") {
                pedido.nome.focus();
                sweetAlert("Dados incompletos!", "Insira um Nome", "error");
                return false;
            } else if (nomeMae == "") {
                pedido.nomeMae.focus();
                sweetAlert("Dados incompletos!", "Insira o Nome da Mãe do Paciente", "error");
                return false;
            } else if (dt_nascimento == "") {
                pedido.dt_nascimento.focus();
                sweetAlert("Dados incompletos!", "Insira uma Data de Nascimento", "error");
                return false;
            } else if (endereco == "") {
                pedido.endereco.focus();
                sweetAlert("Dados incompletos!", "Insira um Endereço", "error");
                return false;
            } else if (numero == "") {
                pedido.end_num.focus();
                sweetAlert("Dados incompletos!", "Insira o número no endereço", "error");
                return false;
            } else if (bairro == "") {
                pedido.end_bairro.focus();
                sweetAlert("Dados incompletos!", "Informe o bairro no endereço", "error");
                return false;
            } else if (cidade == "") {
                pedido.end_cidade.focus();
                sweetAlert("Dados incompletos!", "Informe a Cidade no endereço", "error");
                return false;
            } else if (estado == "") {
                pedido.end_uf.focus();
                sweetAlert("Dados incompletos!", "Informe a UF no endereço", "error");
                return false;
            } else if (solicitante == "") {
                pedido.solicitante.focus();
                sweetAlert("Dados incompletos!", "Informe o Solicitante", "error");
                return false;
            } else if (nomesolicitante == "") {
                pedido.nomesolicitante.focus();
                sweetAlert("Dados incompletos!", "Informe o Nome do Solicitante", "error");
                return false;
            } else if (convenio == "") {
                pedido.carteirinha.focus();
                sweetAlert("Dados incompletos!", "Informe o Convênio da Solicitação", "error");
                return false;
            } else {
                $("#loading-hover").css("z-index", "1");
                return true;
            }

        }
    </script>
</body>

</html>
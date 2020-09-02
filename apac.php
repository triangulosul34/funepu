<?php
require("../vendor/autoload.php");
include('conexao.php');

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $pessoa_id  = $_GET['prontuario'];
    $apac_id    = $_GET['apac_id'];
    include('conexao.php');
    $stmt = "SELECT * FROM pessoas WHERE pessoa_id=$pessoa_id";
    $sth = pg_query($stmt) or die($stmt);
    $row = pg_fetch_object($sth);

    $nome           = $row->nome;
    $sexo           = $row->sexo;
    $dt_nasc        = inverteData($row->dt_nasc);
    $cns            = $row->num_carteira_convenio;
    $cpf            = $row->cpf;
    $identidade     = $row->identidade;
    $cep            = $row->cep;
    $bairro         = $row->bairro;
    $numero         = $row->numero;
    $complemento    = $row->complemento;
    $cidade         = $row->cidade;
    $uf             = $row->estado;
    $rua            = $row->endereco;
    $celular        = $row->celular;
    $nome_mae       = $row->nome_mae;
    $paciente       = $row->paciente;
    $desc_sexo      = "";


    if ($sexo == 'F') {
        $desc_sexo = "Feminino";
    }
    if ($sexo == 'M') {
        $desc_sexo = "Masculino";
    }
    if ($sexo == '') {
        $desc_sexo = "Omitido";
    }

    include('conexao.php');
    $stmt = "SELECT d.cpf, a.*, b.sigtap, c.descricao as desc_cid, b.descricao  from apacs_solicitadas a 
    left join procedimentos b on a.procedimento_id = b.procedimento_id
    left join cid10 c on a.cid10 = c.cid
    left join pessoas d on d.num_conselho_reg = a.crm
    where apac_id=$apac_id";
    $sth = pg_query($stmt) or die($stmt);
    $row = pg_fetch_object($sth);

    $cod_proc       = $row->procedimento_id;
    $crm            = $row->crm;
    $solicitante    = $row->med_solicitante;
    $cid            = $row->cid10;
    $desc_cid       = $row->desc_cid;
    $justificativa  = $row->justificativa;
    $solicitante    = $row->med_solicitante;
    $data           = inverteData($row->data_solicitacao);
    $sigtap         = $row->sigtap;
    $descricao      = $row->descricao;
    $raca_cor       = $row->raca_cor;
    $cpf_medico      = $row->cpf;

    // include('conexao.php');
    // $stmt = "SELECT nome, cpf  from pessoas 
    // where tipo_pessoa='Medico Laudador'";
    // $sth = pg_query($stmt) or die($stmt);
    // $row = pg_fetch_object($sth);


}

require_once('fpdf/fpdf.php');

// require_once('../fpdi/fpdi.php');
// require_once('../fpdi/fpdf_tpl.php');

// // Original file with multiple pages 
// $fullPathToFile = "/var/www/html/sb/teste.pdf";

// class PDF extends FPDI {

//     var $_tplIdx;

//     function Header() {

//         global $fullPathToFile;

//         if (is_null($this->_tplIdx)) {

//             // THIS IS WHERE YOU GET THE NUMBER OF PAGES
//             $this->numPages = $this->setSourceFile($fullPathToFile);
//             $this->_tplIdx = $this->importPage(1);

//         }
//         $this->useTemplate($this->_tplIdx, 0, 0,200);

//     }

//     function Footer() {}

// }

// // initiate PDF
// $pdf = new PDF();

// // add a page
// $pdf->AddPage();


// // The new content
// $pdf->SetFont("Arial", "", 9);
// $pdf->Text(12,35,"Sistema Nacional de Saude");
// $pdf->Text(156,35,"CNES");
// $pdf->Text(12,48,$nome);
// $pdf->Text(160,48,utf8_decode($id));
// $pdf->Text(12,56,$cns);
// $pdf->Text(104,56,$dt_nasc);
// $pdf->Text(140,56,$sexo);
// $pdf->Text(165,55,$cor);
// $pdf->Text(12,64,"$nome_mae");
// $pdf->Text(144,64,$celular);
// $pdf->Text(12,72,"Nome Responsavel paciente");
// $pdf->Text(144,72,$celular2);
// $pdf->Text(12,79,utf8_decode("Rua: ". $rua. "      Nº " .$numero. "       Bairro: ". $bairro ));
// $pdf->Text(12,87,utf8_decode($cidade));
// $pdf->Text(117,87,"Cod Municipio");
// $pdf->Text(145,87,utf8_decode($uf));
// $pdf->Text(157,87,utf8_encode($cep));
// $pdf->Text(12,102,$cod_proc);
// $pdf->Text(72,102,"Nome Procedimento1");
// $pdf->Text(167,102,"QTD1");
// $pdf->Text(12,111,"Cod Procedimento2");
// $pdf->Text(72,110,"Nome Procedimento2");
// $pdf->Text(167,110,"QTD2");
// $pdf->Text(12,121,"Cod Procedimento3");
// $pdf->Text(72,121,"Nome Procedimento3");
// $pdf->Text(167,121,"QTD3");
// $pdf->Text(12,140,"Descricao diagnostico");
// $pdf->Text(112,140,$cid);
// $pdf->Text(132,140,"Cid secun.");
// $pdf->Text(155,140,"Causas asso.");
// $pdf->SetXY(12, 165);
// $pdf->MultiCell(170,4,utf8_decode($anannese),0);
// $pdf->SetXY(12, 145);
// $pdf->MultiCell(170,4,utf8_decode($exames),0);
// $pdf->SetXY(12, 184);
// $pdf->MultiCell(170,4,utf8_decode($justificativa),0);
// $pdf->Text(13,212,utf8_decode($solicitante));
// $pdf->Text(100,212,utf8_decode($data));
// $pdf->Text(18,220,utf8_decode("CPF/CNS"));
// $pdf->Text(50,220,utf8_decode("Numero do documento (cns/cpf)"));
// $pdf->Text(13,235,utf8_decode("Nome do profissional autorizador"));
// $pdf->Text(96,235,utf8_decode("Cod. org. emissor"));
// $pdf->Text(18,246,utf8_decode("CPF/CNS"));
// $pdf->Text(50,246,utf8_decode("Numero do documento (cns/cpf)"));
// $pdf->Text(128,235,utf8_decode("Numero da autorização"));
// $pdf->Text(13,257,utf8_decode("DT autorização"));
// $pdf->Text(130,257,utf8_decode("PERIODO a PERIODO"));
// $pdf->Text(13,270,utf8_decode("Nome do estabelecimento da autorização"));
// $pdf->Text(150,270,utf8_decode("CNES"));



// // THIS PUTS THE REMAINDER OF THE PAGES IN
// if($pdf->numPages>1) {
//     for($i=2;$i<=$pdf->numPages;$i++) {
//         //$pdf->endPage();
//         $pdf->_tplIdx = $pdf->importPage($i);
//         $pdf->AddPage();
//     }
// }

// //show the PDF in page
// //$pdf->Output();

// // or Output the file as forced download
// $pdf->Output();
$pdf = new FPDF();

$pdf->AddPage();
$pdf->Image('app-assets/img/pages/laudo_apacx.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
$pdf->SetFont("Arial", "", 9);
$pdf->Text(18, 42, utf8_decode("UPA ") . UNIDADE_CONFIG);
$pdf->Text(170, 42, ("2164817"));
$pdf->Text(18, 56, $nome);
$pdf->Text(173, 56, $pessoa_id);
$pdf->Text(18, 63, $cns);
$pdf->Text(113, 63, $dt_nasc);
$pdf->Text(140, 63, utf8_decode($desc_sexo));
$pdf->Text(158, 63, utf8_decode("")); //etnia
$pdf->Text(180, 63, utf8_decode($raca_cor));
$pdf->Text(18, 72, $nome_mae);
$pdf->Text(153, 72, $celular);
$pdf->Text(18, 80, ""); //nome responsavel
$pdf->Text(153, 80, $celular2);
$pdf->Text(18, 87, utf8_decode("Rua: " . $rua . "      Nº " . $numero . "       Bairro: " . $bairro));
$pdf->Text(18, 94, utf8_decode($cidade));
$pdf->Text(126, 94, utf8_decode("")); //cod municipio
$pdf->Text(153, 94, utf8_decode($uf));
$pdf->Text(168, 94, utf8_decode($cep));
$pdf->Text(18, 109, utf8_decode($sigtap));
$pdf->Text(78, 109, substr(utf8_decode($descricao), 0, 48));
$pdf->Text(178, 109, utf8_decode(" 01"));
$pdf->Text(18, 124, utf8_decode("")); //procedimento secundario
$pdf->Text(80, 124, utf8_decode("")); //nome procedimento secundario
$pdf->Text(181, 124, utf8_decode("")); //QTD procedimento secundario
$pdf->Text(18, 174, substr(utf8_decode($desc_cid), 0, 50));
$pdf->Text(123, 174, utf8_decode($cid));
$pdf->SetXY(18, 180);
$pdf->MultiCell(170, 4, utf8_decode($justificativa), 0);
$pdf->Text(18, 219, utf8_decode($solicitante));
$pdf->Text(110, 219, utf8_decode($data));
$pdf->Text(32, 227, utf8_decode("CPF"));
$pdf->Text(84, 227, utf8_decode($cpf_medico));

$pdf->Output();

<?php
phpinfo();
define('DB_HOST', "10.0.31.60");
define('DB_USER', "sifrelatorio");
define('DB_PASSWORD', "S1frelat_12");
define('DB_NAME', "SIF");
define('DB_DRIVER', "sqlsrv");

require_once "Conexao.php";

try {

    $db = Conexao::getConnection();

    $sql = "
SELECT DISTINCT 
dbo.RoteiroItemInspecao.Idn_Insp,
CONVERT( VARCHAR, Dat_Inic, 103 ) AS Dat_InicBR

FROM
dbo.RoteiroItemInspecao
INNER JOIN dbo.Inspecao ON dbo.RoteiroItemInspecao.Idn_Insp = dbo.Inspecao.Idn_Insp
INNER JOIN dbo.Roteiro ON dbo.Roteiro.Idn_Rote = dbo.RoteiroItemInspecao.Idn_Rote
WHERE
dbo.RoteiroItemInspecao.Idn_Item IN (2260, 2261, 2262, 2277, 2350)
ORDER by Dat_InicBR DESC 
    ";
    $query = $db->query($sql);
    $rii = $query->fetchAll();

} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

function resultado($sql, $db)
{
    $query = $db->query($sql);
    $row = $query->fetchAll();
    return $row;
}

function respostas($r){
    switch ($r) {
        case 'S':
            return "SIM";
            break;
        case 'N':
            return "NÃO";
            break;
        case 'A':
            return "NÃO SE APLICA";
            break;
        case 'V':
            return "NÃO VERIFICADO";
            break;
    }

}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Relatório de Ações Fiscais em Áreas Públicas</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <div class="alert alert-primary" role="alert">
        <h3>Relatório de Ações Fiscais em Áreas Públicas</h3>
    </div>

    <table class="table table-striped table-hover table-sm" border=1>
        <tr>
            <th>ID VIST</th>
            <th>DATA VIST.</th>
            <th>2260 - COD SMAGEA</th>
            <th>2262 - CONSTATADA INVASAO</th>
            <th>2261 - ESTAGIO INVASAO</th>
            <th>2277 - TIPO CONSTR</th>
        </tr>
        <?php
        foreach ($rii as $row) {
            $resu_codsmagea = null;
            $resu_invasao = null;
            $resu_estagio = null;
            $resu_tipo_constr = null;
            $codsmagea = null;
            $invasao = null;
            $estagio = null;
            $tipo_constr = null;
            $ind_insp = $row['Idn_Insp'];
            $data_vist = $row['Dat_InicBR'];

            //COD_SMAGEA
            $sql2 = "
            SELECT DISTINCT 
            Des_Resu_Item,
            Resu_Rote_Insp,
            Idn_Item
            FROM
            RoteiroItemInspecao
            where Idn_Insp = " . $ind_insp . " and Idn_Item in (2260,2350)
            ";

            $resultado = resultado($sql2, $db);

            foreach ($resultado as $row2) {

                $codsmagea = $row2['Des_Resu_Item'];
                $idn_item = $row2['Idn_Item'];

                if ($row2['Resu_Rote_Insp']) {
                    $resu_codsmagea = $row2['Resu_Rote_Insp'];
                } else {
                    $resu_codsmagea = null;
                }

            }


            //CONSTATADA_INVASAO

            $sql = "
            SELECT DISTINCT 
            Des_Resu_Item,
            Resu_Rote_Insp
            FROM
            RoteiroItemInspecao
            where Idn_Insp = " . $ind_insp . " and Idn_Item = 2262";

            $resultado = resultado($sql, $db);

            foreach ($resultado as $row2) {

                $invasao = $row2['Des_Resu_Item'];

                if ($row2['Resu_Rote_Insp']) {
                    $resu_invasao = $row2['Resu_Rote_Insp'];
                } else {
                    $resu_invasao = null;
                }

            }


            //ESTAGIO_INVASAO
            $sql = "
            SELECT DISTINCT 
            Des_Resu_Item,
            Resu_Rote_Insp
            FROM
            RoteiroItemInspecao
            where Idn_Insp = " . $ind_insp . " and Idn_Item = 2261";

            $resultado = resultado($sql, $db);

            foreach ($resultado as $row2) {

                $estagio = $row2['Des_Resu_Item'];

                if ($row2['Resu_Rote_Insp']) {
                    $resu_estagio = $row2['Resu_Rote_Insp'];
                } else {
                    $resu_estagio = null;
                }

            }

            //TIPO_CONSTR
            $sql = "
            SELECT DISTINCT 
            Des_Resu_Item,
            Resu_Rote_Insp
            FROM
            RoteiroItemInspecao
            where Idn_Insp = " . $ind_insp . " and Idn_Item = 2277";

            $resultado = resultado($sql, $db);

            foreach ($resultado as $row2) {

                $tipo_constr = $row2['Des_Resu_Item'];

                if ($row2['Resu_Rote_Insp']) {
                    $resu_tipo_constr = $row2['Resu_Rote_Insp'];
                } else {
                    $resu_tipo_constr = null;
                }

            }

            $url = "http://sif-piloto.pbh.gov.br/RelatorioVistoriaDetalhada.php?Idn_Insp=$ind_insp";

            echo '<tr>';
            echo "<td><a href='$url' target='_blank'>$ind_insp</a></td>";
            echo "<td>".$data_vist."</td>";
            echo '<td>' . respostas($resu_codsmagea) . ' # ' . $codsmagea .'</td>';
            echo '<td>' . respostas($resu_invasao). ' # ' . $invasao . '</td>';
            echo '<td>' . respostas($resu_estagio) . ' # ' . $estagio . '</td>';
            echo '<td>' . respostas($resu_tipo_constr) . ' # ' . $tipo_constr . '</td>';
            echo '</tr>';


        }

        ?>
    </table>
    <?php
     echo $sql2;
    ?>
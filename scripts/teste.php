<?php
define('DB_HOST', "10.0.31.60");
define('DB_USER', "sifrelatorio");
define('DB_PASSWORD', "S1frelat_12");
define('DB_NAME', "SIF");
define('DB_DRIVER', "sqlsrv");

require_once "Conexao.php";


    $db = Conexao::getConnection();

    $sql = "
            SELECT DISTINCT  
            Idn_Insp
            FROM
            RoteiroItemInspecao
            where Idn_Item IN (2260, 2261, 2262, 2277) and Idn_Insp = 526886
            
    ";

    $query = $db->query($sql);
    $rii = $query->fetch();
    echo $rii['Idn_Insp'];
?>
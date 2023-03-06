<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    require_once 'functions.php';
    require_once 'config.php';
    require_once 'vendor/autoload.php';

    include('./vendor/autoload.php');

    use PhpOffice\PhpSpreadsheet\IOFactory;

    $objSpreadsheet = IOFactory::load('OhtomiTest' . date('Y_m_d') . '.xlsx'); // ファイル名を指定
    $objSheet = $objSpreadsheet->getSheet(0); // 読み込むシートを指定
    
    // ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
    $strRange = $objSheet->calculateWorksheetDimension();
    
    // ワークシートの全てのデータ取得（配列データとして）
    $arrData = $objSheet->rangeToArray($strRange);
    $rowcount = count($arrData);

    echo "<pre>";
    print_r($arrData);
    echo "</pre>";

    $deletesql = 'TRUNCATE table tbl_fruits';
    $rs = connectDb()->query($deletesql);

    for($i=1; $i<$rowcount; $i++){
        if($arrData[$i][3]==null){
            $insertsql = "insert into tbl_fruits(fruits) values ('{$arrData[$i][1]}')";
        }else{
            $insertsql = "insert into tbl_fruits(id,fruits,modified,created,flag) values ({$arrData[$i][0]},'{$arrData[$i][1]}','{$arrData[$i][2]}','{$arrData[$i][3]}',{$arrData[$i][4]})";
        }
        $result = connectDb()->query($insertsql);
    }
?>

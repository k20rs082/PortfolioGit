<?php
    // エラー関連
    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    // DB接続
    require_once 'functions.php';
    require_once 'config.php';
    require_once 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $sql = 'SELECT * FROM tbl_fruits';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row= connectDb()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = connectDb()->query($sql)->rowCount();

    $sheet->setCellValue('A1', 'id');
    $sheet->setCellValue('B1', 'fruits');
    $sheet->setCellValue('C1', 'modified');
    $sheet->setCellValue('D1', 'created');
    $sheet->setCellValue('E1', 'flag');

    for($i=0; $i<$rowCount; $i++){
        $setNum = $i + 2;
        $sheet->setCellValue('A'.$setNum, $row[$i]['id']);
        $sheet->setCellValue('B'.$setNum, $row[$i]['fruits']);
        $sheet->setCellValue('C'.$setNum, $row[$i]['modified']);
        $sheet->setCellValue('D'.$setNum, $row[$i]['created']);
        $sheet->setCellValue('E'.$setNum, $row[$i]['flag']);
    }

    // Excelファイルをブラウザからダウンロードする
    $fileName = 'OhtomiTest' . date('Y_m_d') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
    ob_end_clean();
    header("Content-Disposition: attachment; filename=\"{$fileName}\"");
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>

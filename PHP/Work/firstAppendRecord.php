<?php
// エラー検知とメモリ設定
ini_set("display_errors", 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');

// ライブラリ(DB用とphpspreadsheet)
require_once '../functions.php';
require_once '../config.php';
require_once '../vendor/autoload.php';
require_once '../column.php';

// Excel読む用
$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
// コンソール出力用
require_once "../consoleClass.php";
$console = new Console();
// 利用するExcelファイル
$serverName = '../excel/empty.xlsx';
// ダウンロードするファイルの名前
$filename = "courseRecord.xlsx";
// 設定曜日
if(isset($_POST["calendar"])) {
    $date = $_POST["calendar"];
    
    // 書き込むファイル生成
    $spreadSheet = $Reader->load($serverName);
    $workSheet = $spreadSheet->getActiveSheet();
    
    // 休み関係マスターから取得
    $vacationType = vacation();
    
    // 書き込むデータ取得開始
    // tbl_shiftから年月日でデータを取得
    $sql = "SELECT * FROM tbl_shift WHERE schedule ='" . $date . "' AND flag = 0";
    $query = connectDb()->query($sql);
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = $query->rowCount();
    
    // 取得データ分まわす
    for ($i = 0; $i < $rowCount; $i++) {
        // コースを二次元配列に格納
        for ($j = 0; $j < 10; $j++) {
            $course[$i][$j] = $row[$i]['course' . $j + 1];
        }
        // 社員番号取得
        $rowEmployeeCode = $row[$i]['employeeCode'];
        $rowteamName = $row[$i]['team'];
        
        // コースと同じ構造にする
        for ($j = 0; $j < 10; $j++) {
            $employeeCode[$i][$j] = $rowEmployeeCode;
            $team[$i][$j] = $rowteamName;
        }
    }
    
    // 社員名取得
    $employeeName = getEmployeeName($employeeCode);
    
    // 書き込みフェーズ
    $workSheet->setCellValueByColumnAndRow(1, 1, $date . " 00：00～23：59");
    
    $cellCount = 4;
    for ($i = 0; $i < count($course); $i++) {
        for ($j = 0; $j < 10; $j++) {
            if ($course[$i][$j] != null && preg_match('/^[0-9]+$/', $course[$i][$j])) {
                
                $workSheet->setCellValueByColumnAndRow(1, $cellCount, $cellCount - 3);
                
                $workSheet->setCellValueByColumnAndRow(2, $cellCount, $course[$i][$j]);
                // コース関連書き込み
                writeCourseInfo($workSheet, 2, $cellCount);
                
                $workSheet->setCellValueByColumnAndRow(5, $cellCount, $team[$i][$j]);
                $workSheet->setCellValueByColumnAndRow(6, $cellCount, $employeeCode[$i][$j]);
                $workSheet->setCellValueByColumnAndRow(7, $cellCount, $employeeName[$i][$j]);
                $cellCount += 1;
            }
            // 休日判定用
            for ($k = 0; $k < count($vacationType); $k++) {
                if ($course[$i][$j] == $vacationType[$k]) {
                    $workSheet->setCellValueByColumnAndRow(1, $cellCount, $cellCount - 3);
                    $workSheet->setCellValueByColumnAndRow(129, $cellCount, $course[$i][$j]);
                    $workSheet->setCellValueByColumnAndRow(5, $cellCount, $team[$i][$j]);
                    $workSheet->setCellValueByColumnAndRow(6, $cellCount, $employeeCode[$i][$j]);
                    $workSheet->setCellValueByColumnAndRow(7, $cellCount, $employeeName[$i][$j]);
                    $cellCount += 1;
                }
            }
        }
    }
    
    // ダウンロード関連コード
    dounload($spreadSheet, $fileName);
    
}
    
// メソッドまとめ

function writeCourseInfo($workSheet, $startColum, $startRow)
{
    $courseId = $workSheet->getCellByColumnAndRow($startColum, $startRow)->getValue();
    // print($courseId);
    $sql = "SELECT * FROM tbl_course_master WHERE courseId = '" . $courseId . "'AND flag = 0";
    $query = connectDb()->query($sql);
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = $query->rowCount();
    if ($rowCount == null) return;
    
    $workSheet->setCellValueByColumnAndRow($startColum + 1, $startRow, $row[0]['courseName']);
    $workSheet->setCellValueByColumnAndRow($startColum + 6, $startRow, $row[0]['carId']);
    // 車両情報取得
    writeCar($workSheet, $startColum, $startRow, $row[0]['carId']);
    
    $startTimeInt = changeIntTime($row[0]['startTime']);
    $workSheet->setCellValueByColumnAndRow($startColum + 9, $startRow, $startTimeInt);
    $endTimeInt = changeIntTime($row[0]['endTime']);
    $workSheet->setCellValueByColumnAndRow($startColum + 23, $startRow, $endTimeInt);
    $workTimeInt = changeIntTime($row[0]['workTime']);
    $workSheet->setCellValueByColumnAndRow($startColum + 128, $startRow, $workTimeInt);
    $midnightTimeInt = changeIntTime($row[0]['midnightTime']);
    $workSheet->setCellValueByColumnAndRow($startColum + 129, $startRow, $midnightTimeInt);
    
    $sql = "SELECT highwayId AS roadId, highwayName AS roadName FROM tbl_highway_master WHERE flag = 0 UNION SELECT tollroadId, tollroadName flag FROM tbl_tollroad_master WHERE flag = 0;";
    $query = connectDb()->query($sql);
    $roadInfo = $query->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;

    for ($i = 1; $i < 11; $i++) {
        
        $workSheet->setCellValueByColumnAndRow(enterId + $count * 5, $startRow, $row[0]['highwayEnterId' . $i]);
        $workSheet->setCellValueByColumnAndRow(exitId + $count * 5, $startRow, $row[0]['highwayExitId' . $i]);
        $workSheet->setCellValueByColumnAndRow(highwayFee + $count * 5, $startRow, $row[0]['highwayFare' . $i]);
        $workSheet->setCellValueByColumnAndRow(tollId + $count * 5, $startRow, $row[0]['tollRoadId' . $i]);
        $workSheet->setCellValueByColumnAndRow(tollCount + $count * 4, $startRow, $row[0]['tollRoadCount' . $i]);
        $workSheet->setCellValueByColumnAndRow(tollFee + $count * 4, $startRow, $row[0]['tollRoadFee' . $i]);
        
        // 名称取得
        foreach($roadInfo as $roadInfoRow) {
            if($row[0]['highwayEnterId' . $i] == $roadInfoRow['roadId']) {
                $workSheet->setCellValueByColumnAndRow(enterName + $count * 5, $startRow, $roadInfoRow['roadName']);
            }
            if($row[0]['highwayExitId' . $i] == $roadInfoRow['roadId']) {
                $workSheet->setCellValueByColumnAndRow(exitName + $count * 5, $startRow, $roadInfoRow['roadName']);
            }
            if($row[0]['tollRoadId' . $i] == $roadInfoRow['roadId']) {
                $workSheet->setCellValueByColumnAndRow(tollName + $count * 4, $startRow, $roadInfoRow['roadName']);
            }
        }
        $count += 1;
    }
}

function writeCar($workSheet, $startColum, $startRow, $carId)
{
    $sql = "SELECT * FROM tbl_car_master WHERE carId = '" . $carId . "'AND flag = 0";
    $query = connectDb()->query($sql);
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = $query->rowCount();
    if ($rowCount == null) return;
    
    $workSheet->setCellValueByColumnAndRow($startColum + 7, $startRow, $row[0]['carNumber1']);
    $workSheet->setCellValueByColumnAndRow($startColum + 8, $startRow, $row[0]['carType']);
}

function vacation()
{
    // "定休", "有休", "半有給", "特休", "欠勤", "休職", "公出"
    $sql = "SELECT * FROM tbl_vacation_type_master WHERE flag = 0";
    $typeQuery = connectDb()->query($sql);
    $row = $typeQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $rowCount = $typeQuery->rowCount();
    
    $vacationTypeIndex = 0;
    for ($i = 0; $i < $rowCount; $i++) {
        $vacationType[$vacationTypeIndex] = $row[$i]['vacationType'];
        $vacationTypeIndex += 1;
    }
    return $vacationType;
}

// 時間を数字に変える
function changeIntTime($time)
{
    $timeData = explode(":", $time);
    $min = $timeData[0] * 60;
    $min += $timeData[1];
    return $min / 1440;
}

//社員名取得
function getEmployeeName($employeeCode)
{
    for ($i = 0; $i < count($employeeCode); $i++) {
        for ($j = 0; $j < 10; $j++) {
            // 社員名取得
            $sql = "SELECT * FROM tbl_employee_master WHERE employeeCode = '" . $employeeCode[$i][$j] . "'AND flag = 0";
            $query = connectDb()->query($sql);
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            // 社員の名前もコースと同じ構造
            $employeeName[$i][$j] = $row[0]['employeeName'];
        }
    }
    return $employeeName;
}

// ダウンロード関連コード
function dounload($spreadSheet, $fileName)
{
    // ダウンロード関連コード
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadSheet, 'Xlsx');
    $fileName = "Record.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    ob_end_clean();
    header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($fileName));
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}


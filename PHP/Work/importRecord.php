<?php
// エラー検知とメモリ設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
set_time_limit(0);

require_once '../functions.php';
require_once '../config.php';
require_once '../vendor/autoload.php';
require_once 'importFunctions.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate as Coord;

if(isset($_POST["Import"])) {

  $allowedFileType = [
    'application/vnd.ms-excel',
    'text/xlsx',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
  ];

  if(in_array($_FILES["file"]["type"], $allowedFileType)) {
    //クライアントからのリクエストでアップロードされたファイルの保存場所を変更する際に使用する
    //パーミッション設定のエラー(定番)
    //Warning: move_uploaded_file(uploads/test.xlsx): failed to open stream: No such file or directory in /var/www/html/tamai/index.php on line 78
    //Warning: move_uploaded_file(): Unable to move '/tmp/phpJ1fNG9' to 'uploads/test.xlsx' in /var/www/html/tamai/index.php on line 78

    $targetPath = '../uploads/' . $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

    $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadSheet = $Reader->load($targetPath);
    $workSheet = $spreadSheet->setActiveSheetIndex(6);
    $lastRow = $workSheet->getHighestRow();
    $lastColumnValue = 134;
    $rowCount = 1;
    // ファイル名から日付取得
    $schedule = substr($targetPath, 17, 10);
    $schedule = str_replace('_', '-', $schedule);
    // date型にしてからbindValueしないとエラー
    $schedule = date('Y-m-d', strtotime($schedule));
    
    $importColumnsArray = array('id', ':courseRecordId', 'courseName', ':increaseCourseId', ':team', ':employeeId', 'employeeName', ':carId', 
    'carRank', 'carNumber', ':startTime', ':startCheckTime', ':startCheckTimeMemo', ':checkTimeCardStart', ':instructionMemo', 
    ':checkCarStart', ':useAlcoholCheckMachineStart', ':checkLicense', ':alcoholCheckStart', ':healthCondition', ':temperatureStart', ':checkHairstyle', 
    ':sleepConditions', ':startCheckMethod', ':startRollCallerId', ':endTime', ':endCheckTime', ':endCheckTimeMemo', ':checkCardEnd', 
    ':checkCarEnd', ':useAlcoholCheckMachineEnd', ':alcoholCheckEnd', ':endCheckMethod', ':endRollCallerId', ':reportEnd', ':memo1', ':memo2', 
    ':memo3', ':memo4', 'highwayEnterId1', 'highwayEnterName1', 'highwayExitId1', 'highwayExitName1', ':highwayToll1', 'highwayEnterId2', 
    'highwayEnterName2', 'highwayExitId2', 'highwayExitName2', ':highwayToll2', 'highwayEnterId3', 'highwayEnterName3', 'highwayExitId3',
    'highwayExitName3', ':highwayToll3', 'highwayEnterId4', 'highwayEnterName4', 'highwayExitId4', 'highwayExitName4', ':highwayToll4',
    'highwayEnterId5', 'highwayEnterName5', 'highwayExitId5', 'highwayExitName5', ':highwayToll5', 'highwayEnterId6', 'highwayEnterName6', 'highwayExitId6',
    'highwayExitName6', ':highwayToll6', 'highwayEnterId7', 'highwayEnterName7', 'highwayExitId7', 'highwayExitName7', ':highwayToll7',
    'highwayEnterId8', 'highwayEnterName8', 'highwayExitId8', 'highwayExitName8', ':highwayToll8', 'highwayEnterId9', 'highwayEnterName9',
    'highwayExitId9', 'highwayExitName9', ':highwayToll9', 'highwayEnterId10', 'highwayEnterName10', 'highwayExitId10', 'highwayExitName10', ':highwayToll10',
    'tollroadId1', 'tollroadName1', ':countTollroad1', ':tollroadToll1', 'tollroadId2', 'tollroadName2', ':countTollroad2', ':tollroadToll2',
    'tollroadId3', 'tollroadName3', ':countTollroad3', ':tollroadToll3', 'tollroadId4', 'tollroadName4', ':countTollroad4', ':tollroadToll4',
    'tollroadId5', 'tollroadName5', ':countTollroad5', ':tollroadToll5', 'tollroadId6', 'tollroadName6', ':countTollroad6', ':tollroadToll6',
    'tollroadId7', 'tollroadName7', ':countTollroad7', ':tollroadToll7', 'tollroadId8', 'tollroadName8', ':countTollroad8', ':tollroadToll8',
    'tollroadId9', 'tollroadName9', ':countTollroad9', ':tollroadToll9', 'tollroadId10', 'tollroadName10', ':countTollroad10', ':tollroadToll10',
    ':drivingDistance' , ':workingTime', ':midnightTime', ':cancel', 'schedule');
    // メインの処理
    for($i = 2; $i <= $lastRow; $i++) {
      for($j = 1; $j <= $lastColumnValue; $j++) {
        if($workSheet->getCellByColumnAndRow($j,$i)->getOldCalculatedValue() !== NULL) {
          $courseData[$j-1] = $workSheet->getCellByColumnAndRow($j,$i)->getOldCalculatedValue();
          if($courseData[0] == "0") {
            $lastRow = $rowCount;
            break 2;
          }
          // time取得
          if($j == 11 || $j == 12 || $j == 26 || $j == 27 || $j == 131 || $j == 132) {
            // stringからintに変換
            $time = $schedule.' '.$courseData[$j-1];
            $time = (strtotime($time));
            $courseData[$j-1] = date('H:i:s', $time);
          } 
        } else {
          $courseData[$j-1] = NULL;
        }
        $rowCount += 1;
      }
      $sql = "INSERT INTO tbl_course_record
      (team, employeeId, schedule, courseRecordId, increaseCourseId, carId, startTime, startCheckTime, startCheckTimeMemo, checkTimeCardStart, instructionMemo, 
      checkCarStart, useAlcoholCheckMachineStart, checkLicense, alcoholCheckStart, healthCondition, temperatureStart, checkHairstyle, sleepConditions, 
      startCheckMethod, startRollCallerId, endTime, endCheckTime, endCheckTimeMemo, checkCardEnd, checkCarEnd, useAlcoholCheckMachineEnd, alcoholCheckEnd, 
      endCheckMethod, endRollCallerId, reportEnd, memo1, memo2, memo3, memo4, highwayEnterPatternId, highwayExitPatternId, highwayToll1, highwayToll2, highwayToll3, 
      highwayToll4, highwayToll5, highwayToll6, highwayToll7, highwayToll8, highwayToll9, highwayToll10, tollroadPatternId, countTollroad1, countTollroad2, 
      countTollroad3, countTollroad4, countTollroad5, countTollroad6, countTollroad7, countTollroad8, countTollroad9, countTollroad10, tollroadToll1, 
      tollroadToll2, tollroadToll3, tollroadToll4, tollroadToll5, tollroadToll6, tollroadToll7, tollroadToll8, tollroadToll9, tollroadToll10, 
      drivingDistance, workingTime, midnightTime, cancel)
      VALUES
      (:team, :employeeId, :schedule, :courseRecordId, :increaseCourseId, :carId, :startTime, :startCheckTime, :startCheckTimeMemo, :checkTimeCardStart, 
      :instructionMemo, :checkCarStart, :useAlcoholCheckMachineStart, :checkLicense, :alcoholCheckStart, :healthCondition, :temperatureStart, :checkHairstyle, 
      :sleepConditions, :startCheckMethod, :startRollCallerId, :endTime, :endCheckTime, :endCheckTimeMemo, :checkCardEnd, :checkCarEnd, :useAlcoholCheckMachineEnd, 
      :alcoholCheckEnd, :endCheckMethod, :endRollCallerId, :reportEnd, :memo1, :memo2, :memo3, :memo4, :highwayEnterPatternId, :highwayExitPatternId, :highwayToll1, 
      :highwayToll2, :highwayToll3, :highwayToll4, :highwayToll5, :highwayToll6, :highwayToll7, :highwayToll8, :highwayToll9, :highwayToll10, :tollroadPatternId, 
      :countTollroad1, :countTollroad2, :countTollroad3, :countTollroad4, :countTollroad5, :countTollroad6, :countTollroad7, :countTollroad8, :countTollroad9, 
      :countTollroad10, :tollroadToll1, :tollroadToll2, :tollroadToll3, :tollroadToll4, :tollroadToll5, :tollroadToll6, :tollroadToll7, :tollroadToll8, 
      :tollroadToll9, :tollroadToll10, :drivingDistance, :workingTime, :midnightTime, :cancel);";
  
      $stmt = connectDb()->prepare($sql);
      $stmt->bindValue(':schedule', $schedule);
      
      for($j = 0; $j < count($importColumnsArray); $j++) {
        if(false !== strpos($importColumnsArray[$j], 'highwayEnterId') || false !== strpos($importColumnsArray[$j], 'highwayExitId') || false !== strpos($importColumnsArray[$j], 'tollroadId')) {
          // patternId
          if($courseData[$j] === "") {
            ${$importColumnsArray[$j]} = "NULL";
          } else {
            ${$importColumnsArray[$j]} = intval($courseData[$j]);
          }
        } else if(false !== strpos($importColumnsArray[$j], ':')) {
          // プレースホルダ
          if($courseData[$j] === "") {
            $stmt->bindValue($importColumnsArray[$j], "NULL", PDO::PARAM_NULL);
          } else {
            $stmt->bindValue($importColumnsArray[$j], $courseData[$j]);
          }
        }
      }

      $highwayEnterIdArray = setIdArray($highwayEnterId1, $highwayEnterId2, $highwayEnterId3, $highwayEnterId4, $highwayEnterId5,
                                      $highwayEnterId6, $highwayEnterId7, $highwayEnterId8, $highwayEnterId9, $highwayEnterId10);
      $highwayEnterPatternId = getPatternId($highwayEnterIdArray);
      $stmt->bindValue(':highwayEnterPatternId', intval($highwayEnterPatternId["id"]));

      $highwayExitIdArray = setIdArray($highwayExitId1, $highwayExitId2, $highwayExitId3, $highwayExitId4, $highwayExitId5,
                                    $highwayExitId6, $highwayExitId7, $highwayExitId8, $highwayExitId9, $highwayExitId10);
      $highwayExitPatternId = getPatternId($highwayExitIdArray);
      $stmt->bindValue(':highwayExitPatternId', intval($highwayExitPatternId["id"]));

      $tollroadIdArray = setIdArray($tollroadId1, $tollroadId2, $tollroadId3, $tollroadId4, $tollroadId5,
                                  $tollroadId6, $tollroadId7, $tollroadId8, $tollroadId9, $tollroadId10);
      $tollroadPatternId = getPatternId($tollroadIdArray);
      $stmt->bindValue(':tollroadPatternId', intval($tollroadPatternId["id"]));
      
      $insertId = $stmt->execute();

      if(! empty($insertId)) {
        $type = "success";
        $message = "Excel Data Imported into the Database";
        echo $type;
        echo "<br />";
        echo $message;
        echo "<br />";
      } else {
        $type = "error";
        $message = "Problem in Importing Excel Data";
        echo $type;
        echo "<br />";
        echo $message;
        echo "<br />";
      }
    
    }
  }
} else {
  $type = "error";
  $message = "Invalid File Type. Upload Excel File.";
  // echo $type;
  // echo "<br />";
  echo $message;
  echo "<br />";
}
?>

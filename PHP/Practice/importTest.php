<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    require_once 'functions.php';
    require_once 'config.php';
    require_once 'vendor/autoload.php';

    include('./vendor/autoload.php');

    use PhpOffice\PhpSpreadsheet\IOFactory;

    $objSpreadsheet = IOFactory::load('KousokuYuuryou' . '.xlsx'); // ファイル名を指定
    $objSheet = $objSpreadsheet->getSheet(0); // 読み込むシートを指定

    // ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
    $strRange = $objSheet->calculateWorksheetDimension();

    // ワークシートの全てのデータ取得（配列データとして）
    $arrData = $objSheet->rangeToArray($strRange);
    $rowcount = count($arrData);

    echo "<pre>";
    print_r($arrData);
    echo "</pre>";

    for($i=1; $i<$rowcount; $i++){
      // nullのままだとエラー出るから'null'代入
      for($t=0; $t<10; $t++){
        if($arrData[$i][$t]==null){
          $arrData[$i][$t]='null';
        }
      }
      // updateだとflagしか変えて意味あるとこなそうだからflagでテスト
      // 今回はupdateだめだったけどメモ
      // $updatesql = "update tbl_pattern_course
      //               set flag = 1
      //               where
      //                 case
      //                   when pattern1 is null then pattern1 is null and pattern2 is null and pattern3 is null and pattern4 is null and pattern5 is null and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern2 is null then pattern1 = {$arrData[$i][0]} and pattern2 is null and pattern3 is null and pattern4 is null and pattern5 is null and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern3 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 is null and pattern4 is null and pattern5 is null and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern4 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 is null and pattern5 is null and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern5 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 is null and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern6 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 is null and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern7 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 = {$arrData[$i][5]} and pattern7 is null and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern8 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 = {$arrData[$i][5]} and pattern7 = {$arrData[$i][6]} and pattern8 is null and pattern9 is null and pattern10 is null
      //                   when pattern9 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 = {$arrData[$i][5]} and pattern7 = {$arrData[$i][6]} and pattern8 = {$arrData[$i][7]} and pattern9 is null and pattern10 is null
      //                   when pattern10 is null then pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 = {$arrData[$i][5]} and pattern7 = {$arrData[$i][6]} and pattern8 = {$arrData[$i][7]} and pattern9 = {$arrData[$i][8]} and pattern10 is null
      //                   else pattern1 = {$arrData[$i][0]} and pattern2 = {$arrData[$i][1]} and pattern3 = {$arrData[$i][2]} and pattern4 = {$arrData[$i][3]} and pattern5 = {$arrData[$i][4]} and
      //                   pattern6 = {$arrData[$i][5]} and pattern7 = {$arrData[$i][6]} and pattern8 = {$arrData[$i][7]} and pattern9 = {$arrData[$i][8]} and pattern10 = {$arrData[$i][9]}
      //                 end";
      // $result = connectDb()->query($updatesql);
      // $insertsql = "insert into tbl_pattern_course(pattern1,pattern2,pattern3,pattern4,pattern5,pattern6,pattern7,pattern8,pattern9,pattern10) values
      // ({$arrData[$i][0]},{$arrData[$i][1]},{$arrData[$i][2]},{$arrData[$i][3]},{$arrData[$i][4]},
      // {$arrData[$i][5]},{$arrData[$i][6]},{$arrData[$i][7]},{$arrData[$i][8]},{$arrData[$i][9]})";
      $sql = "insert into tbl_pattern_course
        (pattern1,pattern2,pattern3,pattern4,pattern5,pattern6,pattern7,pattern8,pattern9,pattern10)
        select {$arrData[$i][0]},{$arrData[$i][1]},{$arrData[$i][2]},{$arrData[$i][3]},{$arrData[$i][4]},
        {$arrData[$i][5]},{$arrData[$i][6]},{$arrData[$i][7]},{$arrData[$i][8]},{$arrData[$i][9]}
        where not exists (
            select * from tbl_pattern_course
            where (pattern1 = {$arrData[$i][0]} or (pattern1 is null and {$arrData[$i][0]} is null))
            and   (pattern2 = {$arrData[$i][1]} or (pattern2 is null and {$arrData[$i][1]} is null))
            and   (pattern3 = {$arrData[$i][2]} or (pattern3 is null and {$arrData[$i][2]} is null))
            and   (pattern4 = {$arrData[$i][3]} or (pattern4 is null and {$arrData[$i][3]} is null))
            and   (pattern5 = {$arrData[$i][4]} or (pattern5 is null and {$arrData[$i][4]} is null))
            and   (pattern6 = {$arrData[$i][5]} or (pattern6 is null and {$arrData[$i][5]} is null))
            and   (pattern7 = {$arrData[$i][6]} or (pattern7 is null and {$arrData[$i][6]} is null))
            and   (pattern8 = {$arrData[$i][7]} or (pattern8 is null and {$arrData[$i][7]} is null))
            and   (pattern9 = {$arrData[$i][8]} or (pattern9 is null and {$arrData[$i][8]} is null))
            and   (pattern10 = {$arrData[$i][9]} or (pattern10 is null and {$arrData[$i][9]} is null))
        );
        insert into tbl_record_pattern_test (patternId)
        select id from tbl_pattern_course
        where (pattern1 = {$arrData[$i][0]} or (pattern1 is null and {$arrData[$i][0]} is null))
        and   (pattern2 = {$arrData[$i][1]} or (pattern2 is null and {$arrData[$i][1]} is null))
        and   (pattern3 = {$arrData[$i][2]} or (pattern3 is null and {$arrData[$i][2]} is null))
        and   (pattern4 = {$arrData[$i][3]} or (pattern4 is null and {$arrData[$i][3]} is null))
        and   (pattern5 = {$arrData[$i][4]} or (pattern5 is null and {$arrData[$i][4]} is null))
        and   (pattern6 = {$arrData[$i][5]} or (pattern6 is null and {$arrData[$i][5]} is null))
        and   (pattern7 = {$arrData[$i][6]} or (pattern7 is null and {$arrData[$i][6]} is null))
        and   (pattern8 = {$arrData[$i][7]} or (pattern8 is null and {$arrData[$i][7]} is null))
        and   (pattern9 = {$arrData[$i][8]} or (pattern9 is null and {$arrData[$i][8]} is null))
        and   (pattern10 = {$arrData[$i][9]} or (pattern10 is null and {$arrData[$i][9]} is null));
        ";
      $result = connectDb()->query($sql);
    }
?>

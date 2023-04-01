<?php
    function setIdArray($id1, $id2, $id3, $id4, $id5, $id6, $id7, $id8, $id9, $id10) {
        $patternData = Array();
        for($i = 1; $i <= 10; $i++) {
            $patternData[$i] = ${'id'.$i};
        }
        return $patternData;
    }

    function getPatternId($idArray) {
        $behindWhereString = '';
        for($i = 1; $i <= 10; $i++) {
            $behindWhereString .= " (pattern{$i} = {$idArray[$i]} OR (pattern{$i} IS NULL AND {$idArray[$i]} IS NULL))";
            if($i < 10) {
                $behindWhereString .= " AND";
            }
        }
        $sql = "INSERT INTO tbl_pattern_course
        (pattern1,pattern2,pattern3,pattern4,pattern5,pattern6,pattern7,pattern8,pattern9,pattern10)
        SELECT {$idArray[1]}, {$idArray[2]}, {$idArray[3]}, {$idArray[4]}, {$idArray[5]},
               {$idArray[6]}, {$idArray[7]}, {$idArray[8]}, {$idArray[9]}, {$idArray[10]}
        WHERE NOT EXISTS (
            SELECT * FROM tbl_pattern_course
            WHERE {$behindWhereString}
        );";
        $query = connectDb()->query($sql);

        $sql = "SELECT id FROM tbl_pattern_course
        WHERE {$behindWhereString};";
        $query = connectDb()->query($sql);
        $patternId = $query->fetch(PDO::FETCH_ASSOC);
        return $patternId;
    }
?>

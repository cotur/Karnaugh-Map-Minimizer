<?php
/**
 * Created by PhpStorm.
 * User: ahmet
 * Date: 7.12.2017
 * Time: 13:43
 */
error_reporting(0);
require "classes/Karnaugh.php";


    $kTable = new Karnaugh(4,"min",$_POST["numbs"]);
    $minTerms = array();
    for($i = 0;$i<count($_POST["numbs"]);$i++){
        $buff = "";
        $cont = false;
        foreach ($kTable->truthTable as $q){
            if($cont){
                $buff.= ",";
            }
            $buff.=$q[$_POST["numbs"][$i]];
            $cont = true;
        }
        $minTerms[] = $buff;
    }
    ?>

    <table id="karnaughTable" border="1" cellpadding="5" cellspacing="0" align="center">
        <?php


        $TABLE = array();
        for($i = 0;$i<=count($kTable->karnRows);$i++){
            echo '<tr>';
            for($a = 0;$a<=count($kTable->karnCols);$a++){
                if($i == 0 && $a == 0){
                    echo '<td align="center">#</td>';
                }elseif ($a == 0){
                    // ROW
                    $display = "";
                    $val = explode(",",$kTable->karnRows[$i-1]);
                    for($q = 0;$q<count($val);$q++){
                        if($val[$q] == 0){
                            $dec = "overline";
                        }else{
                            $dec = "none";
                        }
                        $display .= '<span style="text-decoration: '.$dec.'">'.$kTable->rowValues[$q].'</span>';
                    }
                    echo '<td align="center">'.$display.'</td>';
                }elseif($i == 0){
                    // COLUMN
                    $display = "";
                    $val = explode(",",$kTable->karnCols[$a-1]);
                    for($q = 0;$q<count($val);$q++){
                        if($val[$q] == 0){
                            $dec = "overline";
                        }else{
                            $dec = "none";
                        }
                        $display .= '<span style="text-decoration: '.$dec.'">'.$kTable->colValues[$q].'</span>';
                    }
                    echo '<td align="center">'.$display.'</td>';
                }else{
                    // TABLEEEE
                    $val = $kTable->karnRows[$i-1].','.$kTable->karnCols[$a-1];


                    echo '<td align="center" class="kTrows" i="'.($i-1).'" a="'.($a-1).'">';
                    if(in_array($val,$minTerms)){
                        $TABLE[$i-1][$a-1] = 1;
                        echo '<font color="black">1</font>';
                    }else{
                        $TABLE[$i-1][$a-1] = 0;
                        echo '<font color="#cccccc">0</font>';
                    }
                    echo'</td>';
                }
            }
            echo '</tr>';
        }

        function getFunc($table,$komsular = array(),$info = 16,$dontAdd = array()){
            if($info == 16){
                // fullTable

                $neighborhood = true;
                for($i=0;$i<4;$i++){
                    for($a=0;$a<4;$a++){
                        if($table[$i][$a] != 1){
                            $neighborhood = false;
                            break;
                        }
                    }
                }
                if($neighborhood){
                    return 1;
                }else{
                    return getFunc($table,$komsular,8);
                }
            }elseif ($info == 8){
                // 8 de işler değişiyor.


                // SATIRLAR
                //  , 0x1 => A' , 1x2 => B , 2x3 => A
                // SUTUNLAR
                // 3x0 => D' , 0x1 => C' , 1x2 => D , 2x3 => C



                for($i=0;$i<4;$i++){
                    $komsulukSatir = true;
                    $komsulukSutun = true;

                    $ssOnceki = ($i - 1) == -1 ? 3 : $i -1;

                    for($a=0;$a<4;$a++){
                        // SATIR;
                        if($table[$ssOnceki][$a] != 1 || $table[$i][$a] != 1){

                            $komsulukSatir = false;
                        }
                        // SÜTUN
                        if($table[$a][$ssOnceki] != 1 || $table[$a][$i] != 1){
                            $komsulukSutun = false;
                        }
                    }


                    if($komsulukSatir){
                        $komsular["satir"]["8"][] = $ssOnceki."x".$i;
                    }
                    if($komsulukSutun){
                        $komsular["sutun"]["8"][] = $ssOnceki."x".$i;
                    }
                }
                return getFunc($table,$komsular,4);

            }elseif($info == 4){
                // 4 te biraz daha karmaşıklaşıyor

                // SATIRLAR VE SUTUNLARDA 4 LU ARAMA
                for($i=0;$i<4;$i++){
                    $komsulukSatir = true;
                    $komsulukSutun = true;
                    for($a=0;$a<4;$a++){
                        // SATIR
                        if($table[$i][$a] != 1){
                            $komsulukSatir = false;
                        }
                        // SUTUN
                        if($table[$a][$i] != 1){
                            $komsulukSutun = false;
                        }
                    }
                    if($komsulukSatir){
                        $komsular["satir"]["4"][] = (string)$i;
                    }

                    if($komsulukSutun){
                        $komsular["sutun"]["4"][] = (string)$i;
                    }
                }

                // KARESEL 4 LÜ ARAMA

                for($i=0;$i<4;$i++){
                    for($a =0 ;$a<4;$a++){
                        $saOnceki = ($i - 1) == -1 ? 3 : $i -1;
                        $suOnceki = ($a - 1) == -1 ? 3 : $a -1;

                        if($table[$saOnceki][$suOnceki] == 1 && $table[$saOnceki][$a] == 1 && $table[$i][$suOnceki] == 1 && $table[$i][$a] == 1){
                            $komsular["karesel"][] = $saOnceki."-".$i.'x'.$suOnceki.'-'.$a;
                        }
                    }
                }

                return getFunc($table,$komsular,2);
            }elseif ($info == 2){
                // 2 Lİ KOMSU ARAMA

                for($i=0;$i<4;$i++){
                    for($a =0 ;$a<4;$a++){
                        $saOnceki = ($i - 1) == -1 ? 3 : $i -1;
                        $suOnceki = ($a - 1) == -1 ? 3 : $a -1;

                        // SATIR
                        if($table[$i][$suOnceki] == 1 && $table[$i][$a] == 1){
                            $komsular["satir"]["2"][] = $i.'-'.$suOnceki.'x'.$a;
                        }
                        if($table[$saOnceki][$a] == 1 && $table[$i][$a] == 1){
                            $komsular["sutun"]["2"][] = $a.'-'.$saOnceki.'x'.$i;
                        }
                    }
                }

                return getFunc($table,$komsular,1);
            }else{
                // TÜM HÜCRELERİ EKLEMECE
                for($i=0;$i<4;$i++){
                    for($a=0;$a<4;$a++){
                        if($table[$i][$a] == 1){
                            $komsular["hucreler"][] = $i."x".$a;
                        }
                    }
                }

                return $komsular;
            }
        }



        ?>
    </table>

    <?php

    function createFunc($array){

        // ÖNCE HÜCRELERİ YOK ET
        // SATIRLAR
        for($i=0;$i<count($array["satir"]["2"]);$i++){
            $veri = explode("-",$array["satir"]["2"][$i]);

            $veri2 = explode("x",$veri[1]);

            foreach($veri2 as $val){

                $value = $veri[0].'x'.$val;
                $key = array_search($value, $array["hucreler"]);

                unset($array["hucreler"][$key]);
            }
        }
        // SUTUNLAR
        for($i=0;$i<count($array["sutun"]["2"]);$i++){
            $veri = explode("-",$array["sutun"]["2"][$i]);

            $veri2 = explode("x",$veri[1]);


            foreach($veri2 as $val){

                $value = $val.'x'.$veri[0];
                $key = array_search($value, $array["hucreler"]);

                unset($array["hucreler"][$key]);
            }
        }

        // SONRA 2 Lİ KOMSULARI YOK ET

        // SATIR
        for($i=0;$i<count($array["satir"]["4"]);$i++){

            foreach ($array["satir"]["2"] as $key => $val){
                $qq = explode("-",$val);
                if($qq[0] == $array["satir"]["4"][$i]){
                    unset($array["satir"]["2"][$key]);
                }
            }
        }
        // SUTUN
        for($i=0;$i<count($array["sutun"]["4"]);$i++){

            foreach ($array["sutun"]["2"] as $key => $val){
                $qq = explode("-",$val);
                if($qq[0] == $array["sutun"]["4"][$i]){
                    unset($array["sutun"]["2"][$key]);
                }
            }
        }

        // KARESEL
        foreach($array["karesel"] as $val){
            $value = explode("x",$val);

            $satirlar = $value[0];
            $sutunlar = $value[1];

            // satırları yok et
            foreach (explode("-",$satirlar) as $satir){
                $degerimiz = $satir."-".str_replace("-","x",$sutunlar);
                if (($key = array_search($degerimiz, $array["satir"]["2"])) !== false) {
                    unset($array["satir"]["2"][$key]);
                }
            }
            // sutunları yok et
            foreach (explode("-",$sutunlar) as $sutun){
                $degerimiz = $sutun."-".str_replace("-","x",$satirlar);
                if (($key = array_search($degerimiz, $array["sutun"]["2"])) !== false) {
                    unset($array["sutun"]["2"][$key]);
                }
            }


        }

        // 4 LÜ KOMŞULARI YOK ET

        if(count($array["satir"]["8"]) > 0){
            foreach($array["satir"]["8"] as $val){
                $val = explode("x",$val);

                foreach ($val as $q){
                    if (($key = array_search($q, $array["satir"]["4"])) !== false) {
                        unset($array["satir"]["4"][$key]);
                    }
                }

                foreach ($array["karesel"] as $key2 => $val2){
                    $val2 = explode("x",$val2);
                    if($val2[0] == $val[0]."-".$val[1]){
                        unset($array["karesel"][$key2]);
                    }
                }
            }
        }

        if(count($array["sutun"]["8"]) > 0){
            foreach($array["sutun"]["8"] as $val){
                $val = explode("x",$val);

                foreach ($val as $q){
                    if (($key = array_search($q, $array["sutun"]["4"])) !== false) {
                        unset($array["sutun"]["4"][$key]);
                    }
                }

                foreach ($array["karesel"] as $key2 => $val2){
                    $val2 = explode("x",$val2);
                    if($val2[1] == $val[0]."-".$val[1]){
                        unset($array["karesel"][$key2]);
                    }
                }
            }
        }


        return $array;

    }


    // BÖLGE HARİTASI
    $bolgeler = array("satir" => array("8" => array("3x0" => "B'","0x1" => "A'","1x2" => "B","2x3" => "A",),"4" => array("0" => "A'.B'","1" => "A'.B","2" => "A.B","3" => "A.B'",),"2" => array("0-3x0" => "A'.B'.D'","0-0x1" => "A'.B'.C'","0-1x2" => "A'.B'.D","0-2x3" => "A'.B'.C","1-3x0" => "A'.B.D'","1-0x1" => "A'.B.C'","1-1x2" => "A'.B.D","1-2x3" => "A'.B.C","2-3x0" => "A.B.D'","2-0x1" => "A.B.C'","2-1x2" => "A.B.D","2-2x3" => "A.B.C","3-3x0" => "A.B'.D'","3-0x1" => "A.B'.C'","3-1x2" => "A.B'.D","3-2x3" => "A.B'.C",)),"sutun" => array("8" => array("3x0" => "D'","0x1" => "C'","1x2" => "D","2x3" => "C",),"4" => array("0" => "C'.D'","1" => "C'.D","2" => "C.D","3" => "C.D'",),"2" => array("0-3x0" => "B'.C'.D'","0-0x1" => "A'.C'.D'","0-1x2" => "B.C'.D'","0-2x3" => "A.C'.D'","1-3x0" => "B'.C'.D","1-0x1" => "A'.C'.D","1-1x2" => "B.C'.D","1-2x3" => "A.C'.D","2-3x0" => "B'.C.D","2-0x1" => "A'.C.D","2-1x2" => "B.C.D","2-2x3" => "A.C.D","3-3x0" => "B'.C.D'","3-0x1" => "A'.C.D'","3-1x2" => "B.C.D'","3-2x3" => "A.C.D'",)),"karesel" => array("3-0x3-0" => "B'.D'","3-0x0-1" => "B'.C'","3-0x1-2" => "B'.D","3-0x2-3" => "B'.C","0-1x3-0" => "A'.D'","0-1x0-1" => "A'.C'","0-1x1-2" => "A'.D","0-1x2-3" => "A'.C","1-2x3-0" => "B.D'","1-2x0-1" => "B.C'","1-2x1-2" => "B.D","1-2x2-3" => "B.C","2-3x3-0" => "A.D'","2-3x0-1" => "A.C'","2-3x1-2" => "A.D","2-3x2-3" => "A.C"),"hucreler" => array("0x0" => "A'.B'.C'.D'","0x1" => "A'.B'.C'.D","0x2" => "A'.B'.C.D","0x3" => "A'.B'.C.D'","1x0" => "A'.B.C'.D'","1x1" => "A'.B.C'.D","1x2" => "A'.B.C.D","1x3" => "A.B.C.D'","2x0" => "A.B.C'.D'","2x1" => "A.B.C'.D","2x2" => "A.B.C.D","2x3" => "A.B.C.D'","3x0" => "A.B'.C'.D'","3x1" => "A.B'.C'.D","3x2" => "A.B'.C.D","3x3" => "A.B'.C.D'",)
    );





    $komsular = createFunc(getFunc($TABLE));

    function overline($string){
        $string = str_replace("A'",'<span style="text-decoration: overline">A</span>',$string);
        $string = str_replace("B'",'<span style="text-decoration: overline">B</span>',$string);
        $string = str_replace("C'",'<span style="text-decoration: overline">C</span>',$string);
        $string = str_replace("D'",'<span style="text-decoration: overline">D</span>',$string);
        return $string;
    }

    $func = "";
    if($komsular == 1){
        $func = 1;
    }elseif (empty($komsular)){
        $func = 0;
    }else{
        foreach ($komsular as $key => $komsuluk){
            if($key == "satir" || $key == "sutun"){
                foreach ($komsuluk as $key2 => $komsu2){

                    foreach ($komsu2 as $deger){
                        $func .= overline($bolgeler[$key][$key2][$deger])." + ";
                    }

                }
            }elseif ($key == "karesel" || $key == "hucreler"){
                foreach ($komsuluk as $komsu){
                    $func .= overline($bolgeler[$key][$komsu])." + ";
                }
            }
        }
    }

    echo '<span style="display: block;margin-top: 10px;font-weight: bold">F : '.rtrim($func," + ").'</span>';
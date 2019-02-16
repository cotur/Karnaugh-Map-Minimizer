<?php
/**
 * Created by PhpStorm.
 * User: ahmet
 * Date: 15.11.2017
 * Time: 23:15
 */

class Karnaugh
{

    private $letters = array( "A" ,"B","C","D","E","F","G","H","J","K","L","M","N","O","P","Q","R","S","T","V","W","X","Y","Z");

    private $n;
    public $limit;
    private $values = array();
    public $rowValues = array();
    public $colValues = array();
    public $truthTable = array();
    private $kTable = array();
    public $karnRows = array();
    public $karnCols = array();
    public $function;
    private $type;
    private $numbs;

    public function __construct($n = 2,$type,$numbs)
    {
        $this->n = $n;
        $this->limit = pow(2,$n);
        $this->fillValues();
        $this->truthTable = $this->getTruthTable();

        $this->getKarnCols();
        $this->getKarnRows();

        $this->type = $type;
        $this->numbs = $numbs;

        $this->fillKTable();
    }

    private function fillKTable(){
        for($i=0;$i<count($this->rowValues);$i++){
            for($a=0;$a<count($this->colValues);$a++){
                $this->kTable[$i][$a] = 0;
            }
        }
    }

    private function getKarnRows($lastChanged = null){

        if(count($this->rowValues) == 1){
            $this->karnRows = array("0","1");
        }else{

            if(empty($this->karnRows)){
                $val = "";
                for($i=0;$i<count($this->rowValues);$i++){
                    if($i == 0){$valEk = "0";}else{$valEk = ",0";}
                    $val .= $valEk;
                }
                $this->karnRows[] = $val;
                $this->getKarnRows();

            }elseif(count($this->karnRows) == pow(2,count($this->rowValues))){
                return;
            }else{
                for($i = count($this->rowValues)-1 ; $i >= 0 ; $i--){
                    if($lastChanged == $i){
                        continue;
                    }else{
                        $newVal = explode(",",end($this->karnRows));
                        $newVal[$i] = $newVal[$i] == 0 ? 1 : 0;
                        $buffer = "";
                        foreach ($newVal as $q){
                            if(strlen($buffer) != 0){
                                $buffer .= ",".$q;
                            }else{
                                $buffer .= $q;
                            }
                        }

                        if(!in_array($buffer,$this->karnRows)){
                            $this->karnRows[] = $buffer;
                            $this->getKarnRows($i);
                        }
                    }
                }
            }
        }




    }

    private function getKarnCols($lastChanged = null){

        if(count($this->colValues) == 1){
            $this->karnCols = array("0","1");
        }else{

            if(empty($this->karnCols)){
                $val = "";
                for($i=0;$i<count($this->colValues);$i++){
                    if($i == 0){$valEk = "0";}else{$valEk = ",0";}
                    $val .= $valEk;
                }
                $this->karnCols[] = $val;
                $this->getKarnCols();

            }elseif(count($this->karnCols) == pow(2,count($this->colValues))){
                return;
            }else{
                for($i = count($this->colValues) - 1;$i>=0;$i--){
                    if($lastChanged == $i){
                        continue;
                    }else{
                        $newVal = explode(",",end($this->karnCols));
                        $newVal[$i] = $newVal[$i] == 0 ? 1 : 0;
                        $buffer = "";
                        foreach ($newVal as $q){
                            if(strlen($buffer) != 0){
                                $buffer .= ",".$q;
                            }else{
                                $buffer .= $q;
                            }
                        }

                        if(!in_array($buffer,$this->karnCols)){
                            $this->karnCols[] = $buffer;
                            $this->getKarnCols($i);
                        }
                    }
                }
            }
        }




    }

    private function fillValues(){
        for($i = 0; $i < $this->n; $i++){
            $this->values[$i] = $this->letters[$i];
            if($i < ceil($this->n/2)){
                $this->rowValues[] = $this->letters[$i];
            }else{
                $this->colValues[] = $this->letters[$i];
            }
        }
    }

    private function getTruthTable($args = null){


        $addingNum = array();
        $status = array();
        $changeNum = array();
        $truthTable = array();

        for($i=0;$i<$this->n;$i++){
            $status[] = false;
            if($i == 0){
                $addingNum[] = $this->limit / 2;
                $changeNum[] = $addingNum[$i];
            }else{
                $addingNum[] = $addingNum[$i-1] / 2;
                $changeNum[] = $addingNum[$i];
            }
        }



        //echo '<table class="truthTable" cellpadding="5" cellspacing="0" border="1"><tr><td align="center">#</td>';
        //foreach ($values as $q){
        //    echo '<td align="center">'.$q.'</td>';
        //}
        //echo '</tr>';

        for($i=0;$i<$this->limit;$i++){

            //echo '<tr bgcolor="'.($i%2 == 0 ? '#cccccc':'white').'"><td align="center">'.$i.'</td>';

            for($a=0;$a<$this->n;$a++){
                if($changeNum[$a] == $i){
                    $status[$a] = $status[$a] == true ? false : true;
                    $changeNum[$a] += $addingNum[$a];
                }
                if($status[$a] == false){$val = 0; $color = "red";}else{$val = 1; $color="green";}
                $truthTable[$this->values[$a]][] = $val;
                //echo '<td align="center"><font color="'.$color.'">'.$val.'</font></td>';
            }



        }

        //echo '</table>';

        return $truthTable;

    }

    public function printTruthTable(){
        ?>
        <table border="1" class="TruthTable" cellspacing="0" cellpadding="5">
            <tr>
                <?php
                foreach ($this->truthTable as $key => $val){
                    echo '<td align="center">'.$key.'</td>';

                }
                ?>
                <td align="center" style="border-left: 2px solid black;font-weight: normal;">f</td>
                <td align="center" style="border-left: 2px solid black;font-weight: normal;">Min</td>
            </tr>
            <?php
            if($this->type == "max"){
                $zo = 0;
                $oz = 1;
            }else{
                $zo = 1;
                $oz = 0;
            }
            for($i=0;$i<$this->limit;$i++){
                echo '<tr bgcolor="'.($i%2 == 0 ? "gainsboro":null).'">';
                foreach ($this->truthTable as $key => $val){
                    echo '<td align="center">'.$val[$i].'</td>';
                }

                if(in_array($i,$this->numbs)){
                    echo '<td bgcolor="white" align="center" class="fNumbs'.$i.'" style="border-left: 2px solid black;font-weight: normal;">'.$zo.'</td>';
                }else{
                    echo '<td bgcolor="white" align="center" class="fNumbs'.$i.'" style="border-left: 2px solid black;font-weight: normal;">'.$oz.'</td>';
                }
                echo '<td align="center" style="border-left: 2px solid black;font-weight: normal;"><input type="checkbox" class="numbsCheck" name="numbs[]" value="'.$i.'"></td>';
                echo '</tr>';
            }
            ?>
        </table>
        <?php
    }

}
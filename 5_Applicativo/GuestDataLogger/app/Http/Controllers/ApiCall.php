<?php
 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings;

class ApiCall extends Controller
{

    /**
     * This function return data with the right format declared in the public/conf.ini file.
     * 
     * @return json 
     */
    public function getData(){
        $file_path = Settings::getFilePath();
       
        if($file_path != null){

            $pick_up_time = Settings::getPickUpTime();
            $intervals = Settings::getIntervals();
            $data = ApiCall::fetchData($file_path);
            
            if($data == null){
                $response['error'] = "Path file is null";

                return response()->json($response);
            }
             
            $data = ApiCall::compactData($data, $intervals, $pick_up_time);
        }else{
            $response['error'] = "Path file is null";

            return response()->json($response);
        }
        $response['data'] = $data;
        return response()->json($response);
    }

    /**
     * @param Array $data an array containing data that has to be filtered and compacted.
     * @param Array $intervalls this array may contains the minimum date and maximum date are taken into account.
     * @param Integer $pick_up_time this parameter declare every how many seconds to compact the data passed. 
     * 
     * @return Array 
     */

    private function compactData($data, $intervals, $pick_up_time){
        date_default_timezone_set('Europe/Zurich');
       
        $min = str_replace("T"," ",date(DATE_ATOM,mktime(0,0,0,0,0,0)));
        $min = explode("+",$min)[0];
        $max = date("Y-m-d H:i:s");

        $compactDate = array();
        $time_data = array();
        $value_data = array();

        if(count($intervals) == 2){
            $isNull = false;
            
            foreach($intervals as $interv){
                if($interv == null){
                    $isNull = true;
                }

                if(!$isNull){
                    $min = $intervals["MIN"];
                    $max = $intervals["MAX"];
                }
            }

        }else if(count($intervals) == 1){
            if(array_key_exists('MIN', $intervals)){
                $min =  $intervals["MIN"];
            }else if(array_key_exists('MAX', $intervals)){
                $max =  $intervals["MAX"];
            }
        }
 
        $min_val = strtotime($min);
        $max_val = strtotime($max);
        foreach($data as $d){ 
            $time_data = $d[0];
            $value_data = $d[1];
            $time_input = explode(".",$time_data)[0]; 
            $date_input = getDate(strtotime($time_input))[0]; 
            if($date_input >= $min_val && $date_input <= $max_val){
                
                $compactDate[] = [$time_input,$value_data];
            }
        }
        
        if( count($compactDate) > 0){

            $data_old = $compactDate[0];
            
            $final_data = array();
            $values_data = array();
              

            for($i = 0 ; $i < count($compactDate); $i++){
                
                if($pick_up_time == null ){
                    $old_day = explode(" ",$data_old[0])[0];
                    $day = explode(" ",$compactDate[$i][0])[0];
                    //echo "($old_day)($day)-";
                    if( $day == $old_day){
                        $values_data[] = $compactDate[$i][1];   
                    } else {
                        if($data_old != $compactDate[0]){
                            $values_data[] = $compactDate[$i-2][1];
                        }
                        $final_data[] = [explode(" ",$data_old[0])[0], ApiCall::statistics($values_data)];
                       
                        $data_old = $compactDate[$i];
                        $values_data = array();

                    }
                }else{
                    
                    $old_second = strtotime($data_old[0]);
                    $second = strtotime($compactDate[$i][0]) ;
                    
                    if( intval($second) < intval($old_second) + $pick_up_time){
                        $values_data[] = $compactDate[$i][1];   
                    } else {
                        if($data_old != $compactDate[0]){
                            $values_data[] = $compactDate[$i-2][1];
                        }
                        
                        $final_data[] = [$data_old[0], ApiCall::statistics($values_data)];
                        $data_old = $compactDate[$i];
                        $values_data = array();
                        
                    }
                }
            }
            
            return $final_data;
            
        }else{
            
            return $compactDate;
            
        }
        

    }


    /**
     * This function returns the statistics of people detected using the passed array as source data.
     * 
     * @param Array $array_number this array contains all data with only persons count.
     * 
     * @return Integer  
    */
    private function statistics($array_number){
        return round(array_sum($array_number)/count($array_number));
    }

    private function fetchData($file_name){
        return ApiCall::readRawFile(base_path("public\\data\\".$file_name));
    }

    private function readRawFile($path){
        $arrayData = array();
        if(file_exists($path) && $path != null){
            $arr = array_map('str_getcsv', file($path));
            foreach($arr as $conf){
                $arrayData[] = explode(";",$conf[0]);
            }
            return $arrayData;
        }
        return null;
    }
}   

<?php
 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
class Settings extends Controller
{
    public static $file_conf = "conf.ini";
    
    /**
     * This function it's used to change settings.
     * 
     * @param Request $request it's the post request sended from the settings form.
     * 
     * @return View
     */
    public function writeSettings(Request $request)
    {
        if(!file_exists(Settings::$file_conf)){
            $myfile = fopen(Settings::$file_conf, "w");
        }
        //print_r($request->minInterval);
        if($request->checkMin && $request->minInterval != null){
            $this::writeConf("min",$this::sanizeDate($request->minInterval));
        }else{
            $this::deleteConf("min");
        }

        if($request->checkMax && $request->maxInterval != null){
            $this::writeConf("max",$this::sanizeDate($request->maxInterval));
        }else{
            $this::deleteConf("max");
        }

        if($request->checkTime && $request->time != null){
            $this::writeConf("interval",$request->time);
        }else{
            $this::deleteConf("interval");
        }
        $this::writeConf("file",$request->file);
        return view("home")->with("success", "Settings changed");
    }


    /**
     * This function it's used to sanize and format date.
     * 
     * @param String $date it's the date that has to be sanized.
     * 
     * @return String
     */
    private function sanizeDate($date){
        $date = str_replace("T"," ",$date);
        $date = explode(" ",$date);
        if(strlen($date[1]) == 5){
            $date[1] .= ":00";
        }
        return $date[0]." ".$date[1];
    }

    
    /**
     * This function it's used to delete a configurazion from the file.
     * 
     * @param String $name the configuration to delete.
     */
    private function deleteConf($name){

        if(file_exists(Settings::$file_conf)){
            $arr = array_map('str_getcsv', file(Settings::$file_conf));
            $lineNumber = 0;
            foreach($arr as $conf){
                if(str_starts_with($conf[0],$name)){
                    $contents = file(Settings::$file_conf);

                    $contents[$lineNumber] = "";
            
                    file_put_contents(Settings::$file_conf, implode('',$contents));
            
                    return;
                }
                $lineNumber++;
            }
        }
    }

    /**
     * This function it's used to write the configuration.
     * 
     * @param String $name the name of the configuration to change.
     * @param String $value the value of the configurazion to change.
     */
    private function writeConf($name,$value){
        
        if(file_exists(Settings::$file_conf)){
            $arr = array_map('str_getcsv', file(Settings::$file_conf));
            $lineNumber = 0;
            foreach($arr as $conf){
                if(str_starts_with($conf[0],$name)){
                    $contents = file(Settings::$file_conf);

                    $contents[$lineNumber] = "$name=$value\n";
            
                    file_put_contents(Settings::$file_conf, implode('',$contents));
            
                    return;
                }
                $lineNumber++;
            }
            $fp = fopen(Settings::$file_conf, 'a');
            fwrite($fp, "\n$name=$value\n");  
            fclose($fp); 
        }

    }


    /**
     * This function returns all the configurations.
     * 
     * @return json
     */
    public function getSettings(){
        $file_path = Settings::getFilePath();
        $pick_up_time = Settings::getPickUpTime();
        $intervals = Settings::getIntervals();
        $files = Settings::getDirFiles();
        
        $response['file'] = $file_path;
        $response['files'] = $files;
        $response['time'] = $pick_up_time;
        if(count($intervals) == 0){
            $response['minInterval'] = null;
            $response['maxInterval'] = null;
        }else if(count($intervals) == 1){
            if(array_key_exists('MIN', $intervals)){
                $response['minInterval'] =  $intervals["MIN"];
            }else if(array_key_exists('MAX', $intervals)){
                $response['maxInterval'] =  $intervals["MAX"];
            }
        }else{
            $response['minInterval'] =  $intervals["MIN"];
            $response['maxInterval'] =  $intervals["MAX"];
        }
        return response()->json($response);
    }

    /**
     * This function returns an array of all the files into public/data/ .
     * 
     * @return Array 
     */
    public static function getDirFiles(){  
        $arrFiles = array();
        $handle = opendir('./data/');
        
        if ($handle) {
            while (($entry = readdir($handle)) !== FALSE) {
                if(!str_starts_with($entry, ".") && str_ends_with($entry, ".txt")){
                    $arrFiles[] = $entry;
                }
            }
        }
        closedir($handle);
        return $arrFiles;
    }


    /**
     * This function return the value of the configuration name paassed.
     * 
     * @param String $string the name of the configuration.
     * 
     * @return String 
     */
    private static function getConf($string){
        if(file_exists(Settings::$file_conf)){
            $arr = array_map('str_getcsv', file(Settings::$file_conf));
            foreach($arr as $conf){
                if(str_starts_with($conf[0],$string)){
                    return explode("=",$conf[0])[1];
                }
            }
        }
        return null;
    }


    /**
     * This function returns the relative path of the file from where the data are taken.
     * 
     * @return String 
     */
    public static function getFilePath(){
        return Settings::getConf("file=");
    }
    

    /**
     * This function returns an array with the minimum and the maximum time range of data taken.
     * 
     * @return Array
     */
    public static function getIntervals(){
        $confs = array();
        $min = Settings::getConf("min=");
        $max = Settings::getConf("max=");
        if($min){
            $confs["MIN"] = $min; 
        }
        if($max){
            $confs["MAX"] = $max; 
        }
        return $confs;
    }


    /**
     * This function returns the interval time of how many seconds to compact the data.
     * 
     * @return String
     */
    public static function getPickUpTime(){
        return Settings::getConf("interval=");
    }

}
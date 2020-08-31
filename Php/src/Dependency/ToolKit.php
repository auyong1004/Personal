<?php 

namespace App\Dependency;

use ZipArchive;
 
class ToolKit{
    

    /**
     * creates a compressed zip file 
    */
    function createZip($files = array(), $destination = '', $overwrite = false)
    {
        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        //vars
        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            $nextprocess=true;
        }
        //if we have good files...
        if ($nextprocess) {
            //create the archive
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach ($files as $file) {
                //cycle through each file
                foreach ($files as $file) {
                    //make sure the file exists
                    //url
                    if (strpos($file, 'http')!==false) {
                        $file_headers = @get_headers($file);
                        if ($file_headers[0] == 'HTTP/1.0 404 Not Found') {
                            //echo "The file $filename does not exist";
                        }
                        if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found') {
                            //echo "The file $filename does not exist, and I got redirected to a custom 404 page..";
                        } else {
                            //download
                            $download_file = file_get_contents($file);
                               #add it to the zip
                            $zip->addFromString(basename($file), $download_file);
                        }
                    } 
                    //inline directory
                    else {
                        //add it to the zip
                        $zip->addFile($file, $file);
                    }
                }
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        
            //close the zip -- done!
            $zip->close();
        
            //check to make sure the file exists
            return file_exists($destination);
        } else {
            return false;
        }
    }

     /**
     * 3D array to single 2D 
     */
     public static function convert2D($Arr,$key){
        $LocalArr=Array();
        foreach($Arr as $row){
            array_push($LocalArr,$row[$key]);
        }
        return $LocalArr;
    }
    /**
     * Pagination function
     *
     * @param integer $page
     * @param string $limit
     * @return void
     */
    public static function page($page=0,$limit="10"){
        $offset=$page*$limit;
        return "$offset , $limit";
    }
    //return default error message
    public static function errorReturn($msg='No Record'){
        return [
            'status'=>false,
            'data'=>[],
            'msg'=>$msg
        ];
    }

    //format parameter
    public static function formatPara($Para,$Default=[]){
        
        foreach($Default as $column =>$value){
            $Default[$column]=isset($Para[$column])?$Para[$column]:'';
        }
        return $Default;
    
    }
    public static function formatSearch($para,$default=[]){

        foreach($default as $key=>$value){
            $default[$key]=isset($para[$key])?$para[$key]:null;
        }
        return $default;
    }

    public static function quoteString($arr){
        return implode(',',array_map(function ($row){
            return "'".$row."'";
        },explode(',',$arr)));
    }
 
}

?>
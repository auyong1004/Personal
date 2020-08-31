<?php 

namespace App\Dependency;
use ZipArchive;

class FileService{
    private $activities;
    private $msg;

    function __construct(){
    
    }
    function __destruct (){}
    
    public function info($fileName){
        $fileProp=[
            'lastAccess'=>\fileatime($fileName),
            'type'=>filetype($fileName),
            'size'=>filesize($fileName)
        ] ;

        return $fileProp;
    }
    public function check($fileName){
        return \file_exists($fileName);
    }
    public function read($fileName){
        $fileContent='';

        if($this->check($fileName)){
            if(\filesize("$fileName")>0){
                $fileLink = fopen("$fileName", "r");
                $fileContent= fread($fileLink,filesize("$fileName"));
                fclose($fileLink);
            }
   
        }else{
            $fileLink=fopen("$fileName","w");
            \fwrite($fileLink,'[]');

            \fclose($fileLink);
        }
        
        return $fileContent;
    }
    public function write($fileName,$fileContents){

        $fileLink=\fopen("$fileName","w");
        \fwrite($fileLink,$fileContents);
        \fclose($fileLink);
    }

    
    public function delete($fileName){
        return \unlink($fileName);   
    }

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


}
?>
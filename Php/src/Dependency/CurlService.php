<?php 

namespace App\Dependency;
class CurlService{
    private $activities;
    private $msg;

    function __construct(){
    }
    function __destruct (){}

    function get($url="",$options){
        if($url=="")return false;
        $channel=curl_init();
        $channelResponse;
        curl_setopt($channel, CURLOPT_URL, $url); 
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        $channelResponse=curl_exec($channel);
        curl_close($channel);
        return $channelResponse;
        
    }
    function postUrlencode($url,$input){

    }
    function postRaw($url="",$options,$input){
        if($url=="")return false;
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL,$url);
        curl_setopt($channel, CURLOPT_POST, 1);
        curl_setopt($channel, CURLOPT_POSTFIELDS,$input);
        curl_setopt($channel, CURLOPT_HTTPHEADER,$options); 
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);

        $channelResponse = curl_exec($channel);

        curl_close ($channel);
        return $channelResponse;


    }
}



?>
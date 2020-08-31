<?php 
namespace App\Dependency;
class MysqlService{

    private $sql_default=[];
    private $conn;
    private $activities=[];

    private $msg="none";
    private $output;

    /**
     * Initialize the service
     * @param Array('username'=>'','password'=>'','database'=>'','host'=>'')
    */
    function __construct($setting="default"){
        //default value to access database

        $this->sql_default = array(
            'username'                     => 'root',
            'password'                     => 'P@ssw0rd',
            'database'                     => 'ALEX',
            'host'                         => 'localhost');
        
        if (is_array($setting) and $setting!="default") {
            foreach (array_keys($setting) as $keyname) {
                $this->sql_default [$keyname]=$setting[$keyname];
            }
        }

        $temp_default=$this->sql_default ;
        $this->conn=\mysqli_connect($temp_default['host'], $temp_default['username'], $temp_default['password'], $temp_default['database']);

        if(!$this->conn){
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }

        $this->addActivities('init class');

    }
    /**
     * * to execute(select,update,delete,create)
     *   select=(MYSQLI_ASSOC)
     * * status:pending
     * 
     */
    function queryOps($sql, $action = "select")
    {            
        $call=mysqli_query($this->conn,$sql);
        if($call){

            if($action=="select"){
                $data= array();
                while ($row=mysqli_fetch_assoc($call)) {
                    array_push($data, $row);
                }
                $this->output=$data;

            }else{
                $this->output=true;
            }
        }else{
            $this->msg=mysqli_error($this->conn);
            $this->output=false;
        }

        $this->addActivities("$action statement",$sql);
        return $this->output;

    }
    //count total row
    /**
     * * Count total result 
     * * paramter(query)
     * * status:pending

     */
     function countOps($sql)
    {               
        $call=mysqli_query($this->conn,$sql);
        if($call){
            $this->output=mysqli_num_rows($call);
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }
        
        $this->addActivities("count row",$sql);
        return $this->output;
    }
    /**
     * method to change user 
     * * status:pending
     */
    function switchOps($setting){
        
        $sql_default=$this->sql_default;
        if (is_array($setting)or $setting!=null) {
            foreach (array_keys($setting) as $keyname) {
                $sql_default[$keyname]=$setting[$keyname];
            }
        } 
        $call=mysqli_change_user($conn, $sql_default['username'], $sql_default['password'], $sql_default['database']);

        if($call){
            $this->output=true;
            $this->conn=$call;
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }

        $this->addActivities("change user");
        return $this->output;
    }
    /**
     * catching all field from your query
     * paramater(query)
     * * status:pending

     */
    function fieldCatchOps($sql){
        $call=mysqli_query($this->conn,$sql);

        if($call){
            $this->output=mysqli_fetch_fields($call);
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }
        $this->addActivities("get column field",$sql);

        return $this->output;
    }
    /**
     * * prevent Apostrophe error
     * * parameter  $String=null
     * * status:pending

     */
    function escapeStrOps($string){
        $call=\mysqli_real_escape_string($this->conn,strval($string));

        if($call){
            $this->output=$call;
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }

        $this->addActivities("escape string");
        return $this->output;
    }

        /**
     * Undocumented function
     * Return number of affected rows
     * @return void
     */
     function affectedRowOps(){
        return \mysqli_affected_rows($this->conn);
    }

    /**
     *  Get Inserted Last ID
     */
     function getLastID(){
        return mysqli_insert_id($this->conn);

    }
    /**
     * * prevent Apostrophe error
     * * Parameter  $arr=[]
     * * status:pending

    */
    function escapeStrArrOps($arr){
        if(is_array($arr)){
            foreach(array_keys($arr) as $key){
                $arr[$key]=(is_array($arr[$key]))?$this->escapeStrArrOps($arr[$key]):$this->escapeStrOps($arr[$key]);
                
            }
            $this->output=$arr;
        }
        $this->addActivities("escape string arr");

        return $this->output;
    }
    /**
     * Change Database
     * * $dbname:string databasename
     * * status:pending

     */    
    function changeDbOps($database=null){
        $call=mysqli_select_db($this->conn,$database);
        if($call){
            $this->output=true;
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }
        $this->addActivities("change database");
        return $this->output;
    }
    
    /**
     * Get all define info
     * * status:done
     */
    function infoOps(){
        try{
           $this->output= [
            'clientInfo'=>\mysqli_get_client_info(),
            'clientstats'=>\mysqli_get_client_stats(),
            'clientversion'=>\mysqli_get_client_version(),
            'connectionStats'=>\mysqli_get_connection_stats($this->conn),
            'hostInfo'=>\mysqli_get_host_info($this->conn),
            'protoInfo'=>\mysqli_get_proto_info($this->conn),
            'serverInfo'=>\mysqli_get_server_info($this->conn),
           ];

        }catch(Exception $e){
            $this->msg=$e;
            $this->output=false;
        }
        $this->addActivities("get info");
        return $this->output;
    }
    /**
     * * Change Character Set
     * * parameter char=null
     * * status:pending

     */
    function charSetOps($char){
        $call=\mysqli_set_charset($this->conn,$char);

        if($call){
            $this->output=true;

        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }

        $this->addActivities("set char");
        return $this->output;
    }
    /**
     *  Get Inserted Last ID
     * * status:pending
     */
    function getInsertedId(){
        $call=\mysqli_set_charset($this->conn,$char);

        if($call){
            $this->output=$call;
        }else{
            $this->output=false;
            $this->msg=mysqli_error($this->conn);
        }
        return $this->output;
    }

    /**
     * close database connection
     * * status:done
     * @return void
     */
    function shutdown(){
        \mysqli_close($this->conn);
    }

    /**
     * get activite 
     * * status:done
     * @return activities
     */
    function activiteLog(){
        return $this->activities;
    }
    /**
     * get error log
     * * status:done
     * 
     */
    function errorLog(){
        $this->output=mysqli_error_list($this->conn);
        $this->addActivities("get error histories");
        return $this->output;
    }

    protected function addActivities($activityName="",$sql=""){
        array_push($this->activities,['description'=>$activityName,'time'=>date("Y-m-d h:i:sa"),'query'=>$sql]);
    }
    
}


?>
<?php

class hmacauth {
    private $noisyPrint;
    private $initOK;
    private $dbcon;
    private $table;
    function __construct($mysqldb, $tableName, $noisy=false) {
        $this->noisyPrint = $noisy;
        if(!$this->noisyPrint) ob_start();
        $this->initOK = false;
        if(!$mysqldb) {
            echo "No mysql db available\n";
            return;
        }
        $sql = "select count(*) from `$tableName`";
        $res = @mysql_query($sql, $mysqldb);
        if(!$res) {
            echo "Mysql DB error: ". mysql_error()."\n";
            return;
        }
        else {
            $this->initOK = true;
            $this->dbcon = $mysqldb;
            $this->table = $tableName;
            echo "Init OK.\n";
        }
        if(!$this->noisyPrint) ob_end_clean();
    }
    function __destruct() {

    }

    /*
        return empty string when failed,
        return auth token when succeeded.
    */
    public function step1GetAuthToken($userName) {
        if(!$this->initOK) return '';
        if(!$this->noisyPrint) ob_start();
        $randToken = $this->generateAuthToken();
        $sql = "select `user_name`, `user_passwd_key` from `".$this->table."`";
        $res = @mysql_query($sql, $this->dbcon);
        if($res && @mysql_num_rows($res)==1) { // userName exists and is unique
            $sqlu = "update `{$this->table}` set `user_passwd_key`='$randToken' where `user_name`='$userName'";
            $resu = @mysql_query($sqlu, $this->dbcon);
            if($resu) {
                echo "Auth token generated.\n";
            }
            else {
                echo "Get auth token failed.\n";
                $randToken = '';
            }
        }
        else {
            echo "User name: '$userName' does not exist.\n";
            $randToken = '';
        }
        if(!$this->noisyPrint) ob_end_clean();
        return $randToken;
    }

    /*
        return empty string when failed,
        return access token when succeeded.
    */
    public function step2Auth($userName, $authToken, $authHash) {
        if(!$this->initOK) return '';
        $ret = '';
        if(!$this->noisyPrint) ob_start();
        $sql = "select `user_passwd_hash` from {$this->table} where `user_name`='$userName' 
                and  `user_passwd_key`='$authToken'";
        $res = @mysql_query($sql, $this->dbcon);
        if($res && @mysql_num_rows($res)==1) {
            $row = @mysql_fetch_row($res);
            $passwd_hash = $row[0];
            $goodHash = $this->calculateAuthHash($authToken, $passwd_hash);
            echo "Good Hash: ".$goodHash. "\n";
            if($goodHash == $authHash) {
                echo "Auth OK.\n";
                $accToken = $this->generateAccessToken();
                $newAuthToken = $this->generateAuthToken();
                @date_default_timezone_set('Asia/Shanghai');
                $curTime = @time();
                $expTime = $curTime + 3*24*3600; // one week
                $expTimeStr = @date('Y-m-d H:i:s', $expTime);
                // generate & write the Access Hash, expire time
                // *** change authToken to ANOTHER random one
                $sqlu = "update {$this->table} set user_passwd_access_token='$accToken', 
                         user_passwd_key = '$newAuthToken', 
                         user_auth_expire_time = '$expTimeStr', 
                         user_auth_last_time = now()  
                         where 
                         `user_name`='$userName'  
                         and `user_passwd_key` = '$authToken' ";
                $resu = @mysql_query($sqlu, $this->dbcon);
                if($resu) {
                    $ret = $accToken;
                }
            }
            else {
                echo "Auth Failed.\n";
            }
        }
        else {
            echo "Illegal user name or auth token.\n";
        }

        if(!$this->noisyPrint) ob_end_clean();
        return $ret;
    }

    private function calculateAuthHash($authToken, $passwd_hash) {
        $baseStr = $authToken . "-plus-" . $passwd_hash ;
        $hash = hash('md5', $baseStr);
        //$hash = hash('sha256', $baseStr); // sha256 maybe too complicated for frontend
        return $hash;
    }
    private function generateAuthToken($length = 10) {
        $base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($base);
        $randToken = '';
        $randTokenLen = $length;
        for($i=0; $i<$randTokenLen; $i++) {
            $x = rand(0, $len-1);
            $randToken .= $base[$x];
        }
        return $randToken;
    }
    private function generateAccessToken($length = 24) {
        $base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        $len = strlen($base);
        $accToken = '';
        $accTokenLen = $length;
        for($i=0; $i<$accTokenLen; $i++) {
            $x = rand(0, $len-1);
            $accToken .= $base[$x];
        }
        return $accToken;
    }
};

$db = @mysql_connect('localhost', 'root', 'i,robot');
@mysql_select_db('tempdb', $db);
$a = new hmacauth($db, 'hmacauth', true);

//$at = $a->step1GetAuthToken('abc');
$at = '8SpbUO1kB5';
echo "Auth token: $at\n";
$act = $a->step2Auth('abc', $at, '977fb73fe16864218a042e77af396374');
echo "Access token: $act\n";
?>

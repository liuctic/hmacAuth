<?php
require_once(DIRNAME(__FILE__).'/hmacauth.class.php');

$db = @mysql_connect('localhost', 'root', 'i,robot');
@mysql_select_db('tempdb', $db);
$a = new hmacauth($db, 'hmacauth', true);

//This is what the client get
$userName = 'test';
$passwd = '123456';

//Step1: client ask for an auth token (username should be provided)
$at = $a->step1GetAuthToken($userName);
echo "Auth token: $at\n";

//Step1.5: client calculate auth hash
$authHash = md5( $at . "-plus-" . md5($passwd) );

//Step2: client send username, auth token and auth hash to server
// IF correct, client will get an access Token
$act = $a->step2Auth($userName, $at, $authHash);
echo "Access token: $act\n";


//Client can use access token to get login info.
$user = $a->getAuthUser($act);
var_dump($user);

?>

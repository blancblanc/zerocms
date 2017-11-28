<?php

include('system/config.php');
include('system/db_connect.php');

//routes for multiple pages
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
echo $request_uri[0] . "<br>";
var_dump($request_uri);
echo "<br>";

//looks like this isn't working right in valet...
$baseurl  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$baseurl .= $_SERVER['SERVER_NAME'];
if (strpos($baseurl, 'localhost') !== false) {
    $baseurl .= ":".$_SERVER['SERVER_PORT'];
}
//$baseurl .= htmlspecialchars($_SERVER['REQUEST_URI']);
$cleanuri = explode('?', $_SERVER['REQUEST_URI'], 2);
$baseurl .= htmlspecialchars($cleanuri[0]);

echo $baseurl."<br>";
echo $cleanuri[0]."<br>";
echo htmlentities($_SERVER['REQUEST_URI']);

//routing
// switch ($request_uri[0]){
//     // Home page
//     case '/':
//         require 'views/base.php';
//         break;
//     case '/cms':
//         header("Location:/cms");
//         exit();
//         break;
//     // Everything else
//     default:
//         header('HTTP/1.0 404 Not Found');
//         require 'views/404.php';
//         break;
// }

//for testing
// if(!isset($_SESSION['user'])){
//     //no? do users even exist yet?
//     try{
//         $db = new PDO($dsn);
//         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         $numberofusers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
//         $db = NULL;
//     }
//     catch(PDOException $e){
//         $_SESSION['sessionalert'] = "loginfail";
//         header("Location: ".$baseurl);
//         exit();
//     }

//     if($numberofusers < 1){
//         include('cms/partials/startup.php');
//     }
//     else{
//         include('partials/auth.php');
//     }
// }
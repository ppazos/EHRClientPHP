<?php

$resultType = 'json';
//$base_url = 'http://localhost:8090/ehr/rest/';
$base_url = 'http://cabolabs-ehrserver.rhcloud.com/ehr/rest/';

function login($user, $pass, $org)
{
   global $base_url;
   $url = $base_url .'login';
    
   $data = array('format' => 'json', 'username' => $user, 'password' => $pass, 'organization' => $org);

   // use key 'http' even if you send the request to https://...
   $options = array(
      'http' => array(
         'method'  => 'POST',
      )
   );
   
   // Because of GET data goes in URL
   $result = @file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   
   if ($result == FALSE)
   {
      header('HTTP/1.1 401 Unauthorized');
      $res = array('error'=>'could not connect to server or user credentials are not valid');
      $result = json_encode($res);
   }
   
   return $result;
}

function getIdorg($token, $username, $orgnumber)
{
   global $base_url;
   $url = $base_url .'profile/' . $username;
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $token));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

   $obj = json_decode( $result, true );
   
   foreach ($obj["organizations"] as $item)
   {
      if ($item["number"] == $orgnumber)
      {
         $result = $item["uid"];
         break;
      }
   }
   return $result;
}


function listQueries($token)
{
   global $base_url;
   $url = $base_url .'queries';
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $token));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   return $result;
}

function listEHRs($token)
{
   global $base_url;
   $url = $base_url .'ehrs';
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $token));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   return $result;
}

function getPatients($token)
{
   global $base_url;
   $url = $base_url .'patients';
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET', 'header' => 'Authorization: Bearer ' . $token));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   
   return $result;
}

function executeQuery($token, $queryUID, $orgID, $ehrUID)
{
   global $base_url;
   $url = $base_url .'queries/'. $queryUID .'/execute';
   $data = array('format'=>'json', 'organizationUid'=>$orgID, 'ehrUid'=>$ehrUID);

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $token));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   
   //print_r( $http_response_header );
   
   global $resultType;
   
   foreach ($http_response_header as $header)
   {
      if ($header == 'Content-Type: text/xml;charset=UTF-8') {$resultType = 'xml';}
   }
   
   return $result;
}

// main
//print_r($_REQUEST); // Variables de GET y POST
if (!isset($_REQUEST['op'])) header($_SERVER['SERVER_PROTOCOL'] . ' op should be present', true, 500);

$result = '';
switch ($_REQUEST['op'])
{
   case 'login':
      $result = login($_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['org']);
   break;
   case 'getIdorg':
      $result = getIdorg($_REQUEST['tk'], $_REQUEST['username'], $_REQUEST['orgnumber']);
   break;
   case 'listQueries':
      $result = listQueries($_REQUEST['tk']);
   break;
   case 'listEHRs':
      $result = listEHRs($_REQUEST['tk']);
   break;
   case 'getPatients':
       $result = getPatients($_REQUEST['tk']);
   break;
   case 'executeQuery':
       $result = executeQuery($_REQUEST['tk'], $_REQUEST['query'], $_REQUEST['org'], $_REQUEST['ehr']);
   break;
}


if ($resultType == 'xml') header('Content-type: text/xml');
else header('Content-type: application/json');

echo $result;

?>
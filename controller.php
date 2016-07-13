<?php

$resultType = 'json';

function listQueries()
{
   $url = 'http://localhost:8090/ehr/rest/queryList';
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array(
      'http' => array(
         'method'  => 'GET',
         //'content' => http_build_query($data), // solo para post
         //'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
      )
   );
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   return $result;
}

function listEHRs()
{
   $url = 'http://localhost:8090/ehr/rest/ehrList';
   $data = array('format' => 'json');

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET'));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   return $result;
}
function getPatient($uid)
{
   $url = 'http://localhost:8090/ehr/rest/getPatient';
   $data = array('format' => 'json', 'uid' => $uid);

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET'));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   return $result;
}

function executeQuery($queryUID, $ehrUID)
{
   $url = 'http://localhost:8090/ehr/rest/query';
   $data = array('format'=>'json', 'queryUid'=>$queryUID, 'ehrId'=>$ehrUID);

   // use key 'http' even if you send the request to https://...
   $options = array('http'=>array('method'=>'GET'));
   
   // Because of GET data goes in URL
   $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
   
   //print_r( $http_response_header );
   
   global $resultType;
   
   foreach ($http_response_header as $header)
   {
      if ($header == 'Content-Type: text/xml;charset=UTF-8') $resultType = 'xml';
   }
   
   return $result;
}

// main
//print_r($_REQUEST); // Variables de GET y POST
if (!isset($_REQUEST['op'])) header($_SERVER['SERVER_PROTOCOL'] . ' op should be present', true, 500);

$result = '';
switch ($_REQUEST['op'])
{
   case 'listQueries':
      $result = listQueries();
   break;
   case 'listEHRs':
      $result = listEHRs();
   break;
   case 'getPatient':
      $result = getPatient($_REQUEST['uid']);
   break;
   case 'executeQuery':
      $result = executeQuery($_REQUEST['query'], $_REQUEST['ehr']);
   break;
}


if ($resultType == 'xml') header('Content-type: text/xml');
else header('Content-type: application/json');

echo $result;

?>
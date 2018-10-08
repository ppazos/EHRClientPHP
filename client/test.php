<?php

include ('ehrserver_client.php');

$ehrserver = new EHRServer('http://localhost:8090/ehr/api/v1/');
$res = $ehrserver->login('admin','admin','123456');
//echo $res->token;
print_r($ehrserver->get_ehrs());

?>

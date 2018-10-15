<?php

include ('ehrserver_client.php');

$ehrserver = new EHRServer('http://localhost:8090/ehr/api/v1/');
$res = $ehrserver->login('admin','admin','123456');

//echo $res->token;
$ehrs = $ehrserver->get_ehrs();

$queries = $ehrserver->get_queries();
$results = $ehrserver->execute_query($queries->queries[1]->uid);
$templates = $ehrserver->get_templates();
$compositions = $ehrserver->get_compositions($ehrs->ehrs[0]->uid);
$document = $ehrserver->get_composition($compositions->result[0]->uid);
$contributions = $ehrserver->get_contributions($ehrs->ehrs[0]->uid);

?>

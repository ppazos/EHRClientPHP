<?php

include ('ehrserver_client.php');

//$ehrserver = new EHRServer('http://localhost:8090/ehr/api/v1/');
//$res = $ehrserver->login('admin','admin','123456');
$ehrserver = new EHRServer('http://server001.cloudehrserver.com/api/v1/');
$ehrserver->set_token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImFwaWtleWZndXpoZ3hjdnd5cXR0Ym5pdmd6aWdvZG1tZWhscWlibHF1ZGtlbHZkc3dkZGdkb3FvIiwiZXh0cmFkYXRhIjp7Im9yZ2FuaXphdGlvbiI6IjAzMjUyMiIsIm9yZ191aWQiOiI3OWEwZjVmYS1hM2IxLTQ0OTgtOWNhYS05ZjhkZDFhMzM5MmUifSwiaXNzdWVkX2F0IjoiMjAxOC0xMC0xNVQwMzoyNDozOS40NTQtMDI6MDAifQ==.BZGGhgpnv6oOUiZR1QeZF6ZETfJjPWSbzJ6pLxF1KQs=');

//echo $res->token;
$ehrs = $ehrserver->get_ehrs();

$queries = $ehrserver->get_queries();

if (!empty((array)$queries->queries))
{
  $results = $ehrserver->execute_query($queries->queries[0]->uid);
}

$templates = $ehrserver->get_templates();

if (!empty((array)$ehrs->ehrs))
{
  $compositions = $ehrserver->get_compositions($ehrs->ehrs[0]->uid);
  $document = $ehrserver->get_composition($compositions->result[0]->uid);
  $contributions = $ehrserver->get_contributions($ehrs->ehrs[0]->uid);
}

print_r($templates);

?>

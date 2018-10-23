<?php

include ('ehrserver_client.php');

//$ehrserver = new EHRServer('http://localhost:8090/ehr/api/v1/');
//$res = $ehrserver->login('admin','admin','123456');
$ehrserver = new EHRServer('http://server001.cloudehrserver.com/api/v1/');
$ehrserver->set_token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImFwaWtleWZndXpoZ3hjdnd5cXR0Ym5pdmd6aWdvZG1tZWhscWlibHF1ZGtlbHZkc3dkZGdkb3FvIiwiZXh0cmFkYXRhIjp7Im9yZ2FuaXphdGlvbiI6IjAzMjUyMiIsIm9yZ191aWQiOiI3OWEwZjVmYS1hM2IxLTQ0OTgtOWNhYS05ZjhkZDFhMzM5MmUifSwiaXNzdWVkX2F0IjoiMjAxOC0xMC0xNVQwMzoyNDozOS40NTQtMDI6MDAifQ==.BZGGhgpnv6oOUiZR1QeZF6ZETfJjPWSbzJ6pLxF1KQs=');
//$ehrserver->set_token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImFwaWtleXhjd3FsaXlyZXJxbXlndmR6dnZmZWhranRpd3N0bXNkaXhyeGhtdHJqaWhrdXp0dnFuIiwiZXh0cmFkYXRhIjp7Im9yZ2FuaXphdGlvbiI6IjcyMzcyMiIsIm9yZ191aWQiOiI1NzkxOTk5MS1mYWExLTQ0YzQtODM2ZC1kYTgyY2I4MjkwZGMifSwiaXNzdWVkX2F0IjoiMjAxOC0wOS0xOVQyMDo1NjowOC4wNzktMDM6MDAifQ==.1OyxehbGZtm6vTIfhw1mWbj7M/lUFelsOXlaRhkgqdU=');

?>

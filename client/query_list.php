<?php

include ('ehrserver_client.php');

//$ehrserver = new EHRServer('http://localhost:8090/ehr/api/v1/');
//$res = $ehrserver->login('admin','admin','123456');
$ehrserver = new EHRServer('http://server001.cloudehrserver.com/api/v1/');
$ehrserver->set_token('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImFwaWtleWZndXpoZ3hjdnd5cXR0Ym5pdmd6aWdvZG1tZWhscWlibHF1ZGtlbHZkc3dkZGdkb3FvIiwiZXh0cmFkYXRhIjp7Im9yZ2FuaXphdGlvbiI6IjAzMjUyMiIsIm9yZ191aWQiOiI3OWEwZjVmYS1hM2IxLTQ0OTgtOWNhYS05ZjhkZDFhMzM5MmUifSwiaXNzdWVkX2F0IjoiMjAxOC0xMC0xNVQwMzoyNDozOS40NTQtMDI6MDAifQ==.BZGGhgpnv6oOUiZR1QeZF6ZETfJjPWSbzJ6pLxF1KQs=');

//echo $res->token;
$queries = $ehrserver->get_queries();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Queries</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
  </head>

  <body>
    <?php include('top.php'); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include('menu.php'); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Queries</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
              <div class="btn-group mr-2">
                <button class="btn btn-sm btn-outline-secondary">Share</button>
                <button class="btn btn-sm btn-outline-secondary">Export</button>
              </div>
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle mr-2">
                <span data-feather="calendar"></span>
                This week
              </button>
            </div>
          </div>

          <table class="table">
           <thead>
             <tr>
               <th scope="col">#</th>
               <th scope="col">UID</th>
               <th scope="col">Date</th>
               <th scope="col">Org</th>
             </tr>
           </thead>
           <tbody>
             <?php foreach($queries->queries as $i=>$query): ?>
               <tr>
                 <th scope="row"><?=$i+1?></th>
                 <td><?=$query->uid?></td>
                 <td><?=$query->name?></td>
                 <td><?=$query->type?></td>
               </tr>
             <?php endforeach; ?>
           </tbody>
         </table>

         <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-center">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#">Next</a>
            </li>
          </ul>
        </nav>

        </main>
      </div>
    </div>


    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace()
    </script>
  </body>
</html>

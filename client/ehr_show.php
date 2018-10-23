<?php

include ('connection.php');

$contributions = $ehrserver->get_contributions($_GET['uid']);
$documents = $ehrserver->get_compositions($_GET['uid']);
$templates = $ehrserver->get_templates();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>EHR Details</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>

    <style>
      #documents tbody tr, #templates tbody tr {
        cursor: pointer;
      }
    </style>
  </head>

  <body>
    <?php include('top.php'); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include('menu.php'); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">EHR Details</h1>
          </div>

          <h3>Contributions for EHR</h3>

          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">UID</th>
                <th scope="col">Date</th>
                <th scope="col">Committer</th>
                <th scope="col">Documents</th>
              </tr>
            </thead>
            <tbody>
             <?php foreach($contributions->contributions as $i=>$contribution): ?>
                <tr data-uid="<?=$contribution->uid?>">
                  <th scope="row"><?=$i+1?></th>
                  <td><?=$contribution->uid?></td>
                  <td><?=$contribution->audit->timeCommitted?></td>
                  <td><?=$contribution->audit->committer->name?></td>
                  <td><?=count($contribution->versions)?></td>
                </tr>
             <?php endforeach; ?>
            </tbody>
          </table>

          <h3>Documents in EHR</h3>

          <table class="table" id="documents">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">UID</th>
                <th scope="col">Date</th>
                <th scope="col">Patient</th>
                <th scope="col">Type</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($documents->result as $i=>$compo): ?>
                <tr data-uid="<?=$compo->uid?>">
                  <th scope="row"><?=$i+1?></th>
                  <td><?=$compo->uid?></td>
                  <td><?=$compo->startTime?></td>
                  <td><?=$compo->subjectId?></td>
                  <td><?=$compo->templateId?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <h3>Templates</h3>

          <table class="table" id="templates">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">UID</th>
                <th scope="col">Lang</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($templates as $i=>$template): ?>
                <tr data-uid="<?=$template['uid']?>" data-template-id="<?=$template['templateId']?>" data-ehr-uid="<?=$_GET['uid']?>">
                  <th scope="row"><?=$i+1?></th>
                  <td><?=$template['templateId']?></td>
                  <td><?=$template['concept']?></td>
                  <td><?=$template['uid']?></td>
                  <td><?=$template['language']?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

        </main>
      </div>
    </div>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace();

      $(document).ready(function($) {
        $("#documents tbody tr").click(function() {
          //window.document.location = $(this).data("href");
          console.log($(this).data('uid'));
          window.document.location = 'compo_show.php?uid='+ $(this).data('uid');
        });
      });

      $(document).ready(function($) {
        $("#templates tbody tr").click(function() {
          //window.document.location = $(this).data("href");
          console.log($(this).data('uid'));
          window.document.location = 'compo_create_'+ $(this).data('template-id') +'.php?ehr_uid='+ $(this).data('ehr-uid');
        });
      });
    </script>
  </body>
</html>

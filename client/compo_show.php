<?php

include ('connection.php');

$compo = $ehrserver->get_composition($_GET['uid'], 'xml');

$xml = new DOMDocument;
$xml->loadXML($compo); // with load() doesnt work should be loadXML!

$xsl = new DOMDocument;
$xsl->substituteEntities = true;
$xsl->load('openEHR_RMtoHTML.xsl');

$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

/*
$domTranObj = $proc->transformToDoc($xml);
$domHtmlText = $domTranObj->saveHTML();
$html_fragment = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $domTranObj->saveHTML()));
echo $html_fragment;
*/

$html = $proc->transformToXML($xml);

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Document details</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>

    <style>
      table tbody tr {
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
            <h1 class="h2">Document details</h1>
          </div>

          <div class="row">
            <div class="col">
              <?=$html?>
            </div>
          </div>

          <?php
          //echo $html;
          //echo $compo;
          //getElementsByTagName('data')
          //print_r( $xml->childNodes );

          //print_r( $xml->getElementsByTagNameNS('http://schemas.openehr.org/v1', '*') );
          ?>
        </main>
      </div>
    </div>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace();
    </script>
  </body>
</html>

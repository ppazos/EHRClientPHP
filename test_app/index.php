<!doctype html>
<html>
  <head>
    <title>EHRServer Client PHP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">

    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <style type='text/css' media='screen'>
      html, body {
        padding-top: 0px;
        height: 100%;
        font-family: tahoma;
      }
      .panel-body {
        padding-bottom: 0;
      }
      .form-group {
        text-align: left;
      }
      label {
        color: #000;
      }
      pre {
        font-size: 1em;
        font-family: courier;
      }
      form {
        padding: 15px;
      }
      .login-panel {
        margin-top: 20%;
      }
      .alert {
        top: 10px;
        position: absolute;
        display: none;
        width: 80%;
        z-index: 1000;
      }
    </style>
    <script src="jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="xml_utils.js" type="text/javascript"></script>
    <script src="highcharts.js" type="text/javascript"></script>
  </head>
  <body>
    <div class="container">

      <div class="alert alert-danger" role="alert"></div>

      <div id="pnlLogin" style="display:none">
        <div class="row">
           <div class="col-md-4 col-md-offset-4">
             <div class="login-panel panel panel-default">
               <div class="panel-heading">
                 <h3 class="panel-title">Please Sign In</h3>
               </div>
               <div class="panel-body">
                 <form action="" method="post" id="frmLogin">
                   <fieldset >
                     <div class="form-group">
                       <label for="username">Username</label>
                       <input type="text" required="true" name="username" id="username" class='form-control' value="" />
                     </div>
                     <div class="form-group">
                       <label for="password">Password</label>
                       <input type="password" required="true"  id="password" class='form-control' value="" />
                     </div>
                     <div class="form-group">
                       <label for="orgnumber">Organization</label>
                       <input type="text" required="true" id="orgnumber" name="orgnumber" class='form-control' value=""/>
                     </div>
                   </fieldset>
                   <fieldset>
                     <div class="form-group">
                       <input id="btnLogin" name="btnLogin" type="submit" value="Login" class="btn btn-primary btn-lg">
                     </div>
                   </fieldset>
                 </form>
               </div>
             </div>
           </div>
        </div>
      </div>
      <div id="pnlResultados" style="display:none; min-height: 100%; height: 100%">
        <div class="row" style="min-height: 100%; height: 100%">
          <div class="col-sm-2 form-group" style="background-color:lavender; min-height: 100%; height: 100%">
            <form method="post" id="query_form">
              <div>
                <h3>Queries</h3>
                <select name="query" class="form-control" size="6"></select>
              </div>
              <br>
              <div>
                <h3>EHRs</h3>
                <select name="ehr" class="form-control" size="6"></select>
              </div>
              <br>
              <div>
                <input type="submit" name="Get data" class="btn btn-default btn-lg"/>
              </div>
            </form>
          </div>
          <div class="col-sm-10" style="background-color:lavenderblush; min-height: 100%; height: 100%">
            <div style="min-height: 100%; height: 100%">
              <h3>Results</h3>
              <div id="chartContainer"></div>
              <div id="results"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">

      //Global var
      //var orgId;
      var token;
      var timeout = 3000;

      // If the token exists, show de login form. Else, load the index.
      token = localStorage.getItem("token");
      if (token === null)
      {
         $("#pnlLogin").show();
         $("#pnlResultados").hide();

      }
      else
      {
         CargarIndice();
      }

      //Show the index, hide the login and retrieve username and orgnumber.
      //Get id organization, list Queries, list EHRs, get Patients,
      function CargarIndice()
      {
         $("#pnlLogin").hide();
         $("#pnlResultados").show();

         username = localStorage.getItem("username");
         orgnumber = localStorage.getItem("orgnumber");

         // Get id organizations from profile
         /*
         $.ajax({
            url: "controller.php",
            data: {op: "getIdorg", tk: token, username: username, orgnumber: orgnumber},
            dataType: 'text',
            type: "GET"
         })
         .done(function (data)
         {
            orgId = data;
         })
         .fail(function (data)
         {
            $('.alert').text(data.error).show();
            window.setTimeout(function () { $('.alert').hide(); }, timeout);
         });
         */

         // Get queries from EHRServer
         $.ajax({
            url: "controller.php",
            data: {op: "listQueries", tk: token},
            type: "GET"
         })
         .done(function (json)
         {
            console.log('queries', json);
            for (i in json.queries)
            {
               query = json.queries[i];
               $('[name=query]').append('<option value="' + query.uid + '" data-group="' + query.group + '">' + query.name + '</option>');
            }
         })
         .fail(function (json)
         {
            $('.alert').text(json.error).show();
            window.setTimeout(function () { $('.alert').hide(); }, timeout);
         });

         // To be used inside the callback, if ehr.ehrId is used directly, all
         // the options will have the same ehrId, the last procesed in the form
         // because ajax is async.
         var patientEHR = {};

         // Get ehrs from EHRServer
         $.ajax({
            url: "controller.php",
            data: {op: "listEHRs", tk: token},
            type: "GET"
         })
         .done(function (json)
         {
            //console.log(json);

            //for (i in json.ehrs)
            //{
            //    ehr = json.ehrs[i];
            //console.log(ehr);

            // To be used inside the callback
            //patientEHR[ehr.subjectUid] = ehr.uid;
            //}

            console.log('ehrs', json);
            for (j = 0; j < json.ehrs.length; j++)
            {
               $('[name=ehr]').append('<option value="' + json.ehrs[j].uid + '">' + json.ehrs[j].uid + '</option>');
            }
         })
         .fail(function (json)
         {
            $('.alert').text(json.error).show();
            window.setTimeout(function () { $('.alert').hide(); }, timeout);
         });
      }

      //Submit form login.
      //If the user is correct, save the token, username and orgnumber in localstorage.
      //Then, show de index.
      $("#frmLogin").submit(function (e) {

        e.preventDefault();

        // Login user
        var user = $("#username").val();
        var pass = $("#password").val();
        var org = $("#orgnumber").val();

        if ($.trim(user).length > 0 && $.trim(org).length > 0 && $.trim(pass).length > 0)
        {
            var dataString = "user=" + user + "&pass=" + pass + "&org=" + org + "&op=login";
            $("msgAlerta").hide();
            $.ajax({
               url: "controller.php",
               data: dataString,
               type: "POST",
               cache: false,
               dataType: "json"
            })
            .done(function (data)
            {
               console.log(data);
               if (data === null)
               {
                  $('.alert').text("Invalid token").show();
                  window.setTimeout(function () { $('.alert').hide(); }, timeout);
               }
               else
               {
                  localStorage.setItem("token", data["token"]);
                  token = data["token"];
                  localStorage.setItem("username", user);
                  localStorage.setItem("orgnumber", org);

                  CargarIndice();
               }
            })
            .fail(function (resp, error, text)
            {
               $('.alert').text(resp.responseJSON.error).show();
               window.setTimeout(function () { $('.alert').hide(); }, timeout);
            });
        }
      });


      // Query
      $('#query_form').submit(function (evn) {

         evn.preventDefault();

         data = {op: "executeQuery", tk: token};
         $($(this).serializeArray()).each(function (i, obj) // Adds form data to ajax request
         {
            data[obj.name] = obj.value;
         });

         $.ajax({
            url: "controller.php",
            data: data,
            type: "GET"
         })
         .done(function (result, status, xhr) {

            console.log("query results", result);

            // result can be xml or json !
            var ct = xhr.getResponseHeader("content-type") || "";

            $('#results').empty();

            if (ct.indexOf('xml') > -1) // XML result
            {
               var pre = $('#results').append('<pre></pre>').children()[0];
               $(pre).text(formatXml(xmlToString(result)));
            }
            else if (ct.indexOf('json') > -1) // JSON result
            {
               var pre = $('#results').append('<pre></pre>').children()[0];
               $(pre).text(JSON.stringify(result, undefined, 2));

               // Render only if query is json and grouped by path
               if ($('option:selected', '[name=query]').data('group') == 'path')
               {
                  render(result);
               }
            }
         })
         .fail(function (json)
         {
            $('.alert').text(json.responseJSON.error).show();
            window.setTimeout(function () { $('.alert').hide(); }, timeout);
         });
      });

      var render = function (data)
      {
         var series = []; // Data to sent to Highcharts

         var xAxisLabels = [];
         var firstRound = true;
         $.each(data, function (path, dviseries) {

            console.log('path y dviseries', path, dviseries);

            // avoids processing timing info
            if (path == 'timing') return true;

            /**
             * Estructura:
             *   { name: 'John', data: [5, 7, 3] }
             *
             *   o si quiero mostrar una etiqueta en el punto:
             *   { name: 'John', data: [{name:'punto', color:'#XXX', y:5},{..},{..}] }
             */
            var serie = {name: dviseries.name, data: []};

            console.log("dviseries serie", dviseries.serie);

            // FIXME: cuidado, esto es solo para DvQuantity!!!!!
            $.each(dviseries.serie, function (ii, dvi) { // dvi {date, magnitude, units}

               //console.log('ii y dvi', ii, dvi);

               // Get dates from first serie, that will be the labels for xAxis
               if (firstRound)
               {
                   d = new Date(dvi.date);
                   xAxisLabels.push(d.getFullYear() + '/' + d.getMonth() + '/' + d.getDate()); // format the date to display
               }

               // FIXME: el valor depende del tipo de dato, y para graficar se necesitan ordinales
               // TODO: ver si se pueden graficar textos y fechas
               // TODO: prevenir internar graficar tipos de datos que no se pueden graficar
               //serie.data.push( dvi.magnitude );

               // para que la etiqueta muestre las unidades
               point = {name: dvi.magnitude + ' ' + dvi.units, y: dvi.magnitude}
               serie.data.push(point);

            });

            series.push(serie);

            if (firstRound)
               firstRound = false;
         });

         console.log('data', data);
         console.log('series', series);
         console.log('xAxisLabels', xAxisLabels);
         renderchart(series, xAxisLabels);
      }

      var renderchart = function (series, xAxisLabels)
      {
         chart = new Highcharts.Chart({
            chart: {
               renderTo: 'chartContainer',
               type: 'line',
               zoomType: 'x' // lo deja hacer zoom en el eje x, y o ambos: xy
            },
              /* depende de lo que este graficando!
               title: {
               text: 'Blood Pressure' // TODO: obtener del arquetipo+path en la ontologia del arquetipo
               },
               */
            xAxis: {
               categories: xAxisLabels
            },
            /* depende de lo que este graficando!
               yAxis: {
               title: {
               text: 'Blood Pressure mmHg' // TODO: obtener del arquetipo
               }
               },
            */
            plotOptions: {
               line: {
                  dataLabels: {
                     enabled: true
                  }
               }
            },
            series: series
         });
      };
    </script>
  </body>
</html>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      html, body { /* Needed for the table to be height=100% */
        height: 100%;
        font-size: 1.4em;
        font-family: tahoma;
      }
      body {
        margin: 0;
        padding: 0;
      }
      pre {
        font-size: 0.4em;
        font-family: courier;
      }
      table {
        width: 100%;
        height: 100%;
      }
      td {
        background-color: #D2CBAC;
        padding: 0;
      }
      td > div { /* this is to add a scroll on the right side of the table, overflow cant be applied on TDs */
        overflow: auto;
        height: 100%;
        padding: 10px;
      }
      tr td:first-child {
        padding: 10px;
        width: 240px;
        background-color: #4C5FA5;
      }
      select {
        width: 240px;
      }
    </style>
    <script src="jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="xml_utils.js" type="text/javascript"></script>
    <script src="highcharts.js" type="text/javascript"></script>
  </head>
  <body>
    <table cellspacing="0" cellpadding="10">
      <tr>
        <td valign="top">
          <form method="post" id="query_form">
            Queries<br/>
            <select name="query" size="6"></select>
            <br/>
            Patients<br/>
            <select name="ehr" size="6"></select>
            <br/>
            <input type="submit" name="Get data" />
          </form>
        </td>
        <td valign="top">
          <div>
            Results
            <div id="chartContainer"></div>
            <div id="results"></div>
          </div>
        </td>
      </tr>
    </table>
    
    <script>
      // Get queries from EHRServer
      $.ajax({
        url: "controller.php",
        data: { op: "listQueries"},
        type: "GET"
      })
      .done(function(json) { 

        //console.log(json);
        
        for (i in json.queries)
        {
          query = json.queries[i];
          $('[name=query]').append('<option value="'+query.uid+'" data-group="'+ query.group +'">'+query.name+'</option>');
        }
      })
      .fail(function(json) {

        console.log(json);
      });
      
      // To be used inside the callback, if ehr.ehrId is used directly, all
      // the options will have the same ehrId, the last procesed in the form
      // because ajax is async.
      var patientEHR = {};
      
      // Get ehrs from EHRServer
      $.ajax({
        url: "controller.php",
        data: { op: "listEHRs"},
        type: "GET"
      })
      .done(function(json) {

        //console.log(json);
        
        for (i in json.ehrs)
        {
          ehr = json.ehrs[i];
          //console.log(ehr);
          
          // To be used inside the callback
          patientEHR[ehr.subjectUid] = ehr.ehrId;
          
          // Get patient from EHR
          $.ajax({ url: "controller.php", data: { op: "getPatient", uid: ehr.subjectUid }, type: "GET" })
          .done(function(patient) {

            //console.log(patient);
            //$('[name=ehr]').append('<option value="'+ehr.ehrId+'">'+ patient.firstName +' '+ patient.lastName +'</option>');
            $('[name=ehr]').append('<option value="'+patientEHR[patient.uid]+'">'+ patient.firstName +' '+ patient.lastName +'</option>');
          });
        }
      })
      .fail(function(json) {

        console.log(json);
      });
      
      // Query
      $('#query_form').submit(function (evn) {
      
        evn.preventDefault();
      
        data = {op: "executeQuery"};
        $($(this).serializeArray()).each( function(i, obj) { // Adds form data to ajax request
        
          data[obj.name] = obj.value;
        });
        
        //console.log( data );
      
        $.ajax({ url: "controller.php", data: data, type: "GET" })
        .done(function(result, status, xhr) {

           //console.log(result);
           
           // result can be xml or json !
           var ct = xhr.getResponseHeader("content-type") || "";
           
           //console.log(ct); // text/xml or application/json
           
           if (ct.indexOf('xml') > -1) { // XML result

              var pre = $('#results').append('<pre></pre>').children()[0];
              $(pre).text( formatXml( xmlToString(result) ) );
           }
           else if (ct.indexOf('json') > -1) { // JSON result

              var pre = $('#results').append('<pre></pre>').children()[0];
              $(pre).text( JSON.stringify(result, undefined, 2) );
              
              // Render only if query is json and grouped by path
              if ( $('option:selected', '[name=query]').data('group') == 'path' )
              {
                 render(result);
              }
           } 
        })
        .fail(function(json) {

           alert('fail');
           console.log(json);
        });
      });
      
      var render = function (data)
      {
         var series = []; // Data to sent to Highcharts
         
         var xAxisLabels = [];
         var firstRound = true;
         $.each( data, function(path, dviseries) {
         
           //console.log('path y dviseries', path, dviseries);
           
           /**
            * Estructura:
            *   { name: 'John', data: [5, 7, 3] }
            *
            *   o si quiero mostrar una etiqueta en el punto:
            *   { name: 'John', data: [{name:'punto', color:'#XXX', y:5},{..},{..}] }
            */
           var serie = { name: dviseries.name, data: [] };
        

           // FIXME: cuidado, esto es solo para DvQuantity!!!!!
           $.each( dviseries.serie, function(ii, dvi) { // dvi {date, magnitude, units}
            
             //console.log('ii y dvi', ii, dvi);
             
             // Get dates from first serie, that will be the labels for xAxis
             if (firstRound)
             {
               d = new Date(dvi.date);
               xAxisLabels.push(d.getFullYear()+'/'+d.getMonth()+'/'+d.getDate()); // format the date to display
             }
             
             // FIXME: el valor depende del tipo de dato, y para graficar se necesitan ordinales
             // TODO: ver si se pueden graficar textos y fechas
             // TODO: prevenir internar graficar tipos de datos que no se pueden graficar
             //serie.data.push( dvi.magnitude );
             
             // para que la etiqueta muestre las unidades
             point = {name: dvi.magnitude+' '+dvi.units, y: dvi.magnitude}
             serie.data.push(point);

           });
           
           series.push(serie);
           
           if (firstRound) firstRound = false;
         });
         
         
         console.log('data', data);
         console.log('series', series);
         console.log('xAxisLabels', xAxisLabels);
         renderchart(series, xAxisLabels);
      }
      
      var renderchart = function(series, xAxisLabels)
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
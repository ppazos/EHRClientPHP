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
    <script src="jquery-1.10.2.min.js"></script>
    <script src="xml_utils.js"></script>
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

        console.log(json);
        
        for (i in json.queries)
        {
          query = json.queries[i];
          $('[name=query]').append('<option value="'+query.uid+'">'+query.name+'</option>');
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

        console.log(json);
        
        for (i in json.ehrs)
        {
          ehr = json.ehrs[i];
          console.log(ehr);
          
          // To be used inside the callback
          patientEHR[ehr.subjectUid] = ehr.ehrId;
          
          // Get patient from EHR
          $.ajax({ url: "controller.php", data: { op: "getPatient", uid: ehr.subjectUid }, type: "GET" })
          .done(function(patient) {

            console.log(patient);
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
           } 

        })
        .fail(function(json) {

           alert('fail');
           console.log(json);
        });
      
        
      });
    </script>
  </body>
</html>
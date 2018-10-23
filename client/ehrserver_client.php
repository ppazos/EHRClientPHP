<?php

class EHRServer {

  private $token = null;
  private $base_url = null;

  function __construct($base_url)
  {
    $this->base_url = $base_url;
  }

  function login($username, $password, $organization)
  {
    $url = $this->base_url .'login';
    $data = array('format' => 'json', 'username' => $username, 'password' => $password, 'organization' => $organization);

    // use key 'http' even if you send the request to https://...
    $options = array(
       'http' => array(
          'method'  => 'POST'
       )
    );

    // Because of GET data goes in URL
    $result = @file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    if ($result == false)
    {
       header('HTTP/1.1 401 Unauthorized');
       $res = array('error'=>'could not connect to server or user credentials are not valid');
       return $res;
    }

    $json = json_decode( $result );

    $this->token = $json->token;

    return $json;
  }

  function set_token($token)
  {
    $this->token = $token;
  }

  function is_authenticated()
  {
    return !is_null($this->token);
  }

  function get_ehrs($max = 20, $offset = 0)
  {
    $url = $this->base_url .'ehrs';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset);

    // use key 'http' even if you send the request to https://...
    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    // Because of GET data goes in URL
    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
  }

  function create_ehr($subject_uid)
  {
    $url = $this->base_url .'ehrs';
    $data = array('format' => 'json', 'subjectUid' => $subject_uid);

    // use key 'http' even if you send the request to https://...
    $options = array('http'=>array('method'=>'POST', 'header' => "Authorization: Bearer " . $this->token));


    // Because of GET data goes in URL
    $result = @file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    if ($result == false)
    {
       header('HTTP/1.1 401 Unauthorized');
       $res = array('error'=>'could not connect to server or user credentials are not valid');
       return $res;
    }

    return json_decode( $result );
  }

  function get_queries($max = 20, $offset = 0)
  {
    $url = $this->base_url .'queries';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset);
    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    // Because of GET data goes in URL
    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));
    return json_decode($result);
  }

  function execute_query($uid, $ehr_uid = null, $date_from = null, $date_to = null)
  {
    $url = $this->base_url .'queries/'. $uid .'/execute';
    $data = array('format'=>'json');

    if (!is_null($ehr_uid)) $data['ehrUid'] = $ehr_uid;

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result, true);
  }

  function get_templates($max = 20, $offset = 0)
  {
    $url = $this->base_url .'templates';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    // Because of GET data goes in URL
    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result, true);
  }

  function get_template($uid)
  {
    $url = $this->base_url .'templates/'. $uid;
    //$data = array('format'=>'json'); // for now is only retrieving XML...
    $data = array();

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    // Because of GET data goes in URL
    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return $result; // json_decode($result); // for now is only retrieving XML...
  }

  function get_compositions($ehr_uid, $max = 20, $offset = 0)
  {
    $url = $this->base_url .'compositions';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset, 'ehrUid'=>$ehr_uid);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
  }

  function get_composition($uid, $format)
  {
    $url = $this->base_url .'compositions/'.$uid;
    $data = array('format'=>$format);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    if ($format == 'json')
      return json_decode($result);
    else
      return $result;
  }

  function commit_composition($composition, $ehr_uid, $committer, $system)
  {
    $committer = rawurlencode($committer);
    $system = rawurlencode($system);
    $url = $this->base_url .'ehrs/'. $ehr_uid .'/compositions?auditCommitter='. $committer .'&auditSystemId='. $system;

    //echo $url;

    //$data = array('format' => 'xml', 'auditCommitter' => $committer, 'auditSystemId' => $system);

    /* got 500 from multipart request, the server doesn support multipart

    org.springframework.web.multipart.commons.CommonsMultipartFile cannot be cast to java.lang.String. Stacktrace follows:
java.lang.ClassCastException: org.springframework.web.multipart.commons.CommonsMultipartFile cannot be cast to java.lang.String
	at com.cabolabs.ehrserver.api.RestController.commit(RestController.groovy:279)
	at grails.plugin.cache.web.filter.PageFragmentCachingFilter.doFilter(PageFragmentCachingFilter.java:198)
	at grails.plugin.cache.web.filter.AbstractFilter.doFilter(AbstractFilter.java:63)
	at grails.plugin.springsecurity.web.filter.GrailsAnonymousAuthenticationFilter.doFilter(GrailsAnonymousAuthenticationFilter.java:53)
	at grails.plugin.springsecurity.web.authentication.RequestHolderAuthenticationFilter.doFilter(RequestHolderAuthenticationFilter.java:53)
	at grails.plugin.springsecurity.web.authentication.logout.MutableLogoutFilter.doFilter(MutableLogoutFilter.java:62)
	at com.brandseye.cors.CorsFilter.doFilter(CorsFilter.java:82)
	at java.util.concurrent.ThreadPoolExecutor.runWorker(ThreadPoolExecutor.java:1149)
	at java.util.concurrent.ThreadPoolExecutor$Worker.run(ThreadPoolExecutor.java:624)
	at java.lang.Thread.run(Thread.java:748)


    define('MULTIPART_BOUNDARY', '--------------------------'.microtime(true));
    $header = "Authorization: Bearer " . $this->token ."\r\n".
              'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;

    define('FORM_FIELD', 'uploaded_file');
    $content =  "--".MULTIPART_BOUNDARY."\r\n".
            "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"versions\"\r\n".
            "Content-Type: application/xml\r\n\r\n".
            $composition."\r\n";

    // add some POST fields to the request too: $_POST['foo'] = 'bar'
    $content .= "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; auditCommitter=\"$committer\"\r\n\r\n".
                "bar\r\n";
    $content .= "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; auditSystemId=\"$system\"\r\n\r\n".
                "bar\r\n";

    // signal end of request (note the trailing "--")
    $content .= "--".MULTIPART_BOUNDARY."--\r\n";

    // use key 'http' even if you send the request to https://...
    $options = array(
      'http'=>array(
        'method'=>'POST',
        'header' => $header,
        'content' => $content
      )
    );


    // Because of GET data goes in URL
    $result = @file_get_contents($url, false, stream_context_create($options));
    var_dump($http_response_header);

    if ($result == false)
    {
       header('HTTP/1.1 401 Unauthorized');
       $res = array('error'=>'could not connect to server or user credentials are not valid');
       return $res;
    }
    */

    // trying curl

    //echo '<script>console.log("'. $composition .'");</script>';

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_POST => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $composition, //http_build_query($data),
      CURLOPT_HTTPHEADER => array(
        "authorization: Bearer $this->token",
        "content-type: application/xml"
      )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err)
    {
      echo "cURL Error #:" . $err;
    }
    else
    {
      /*
      <commitResult>
      	<type>AA</type>
      	<message>Versions successfully committed to EHR e5661ca0-7e1b-4e10-abac-74011a446e2b</message>
      	<versions>
      		<version>
      			<uid>91b3f2dd-2d17-4781-9e98-7b29e63a3107::my.emr::1</uid>
      			<precedingVersionUid />
      			<lifecycleState>532</lifecycleState>
      			<commitAudit>
      				<timeCommitted>2018-10-12 20:34:02</timeCommitted>
      				<committer>
      					<namespace>local</namespace>
      					<type>PERSON</type>
      					<value />
      					<name>Dr. Yamamoto</name>
      				</committer>
      				<systemId>CABOLABS_EHR</systemId>
      				<changeType>CREATION</changeType>
      			</commitAudit>
      			<data>
      				<uid>5f994a0e-71aa-457f-9b32-7150107d48c1</uid>
      				<category>event</category>
      				<startTime>2018-10-12 20:34:01</startTime>
      				<subjectId>6538b9f1-b9b9-449c-9e74-844e6d938715</subjectId>
      				<ehrUid>e5661ca0-7e1b-4e10-abac-74011a446e2b</ehrUid>
      				<templateId>documento_actividad_fisica.es.v1</templateId>
      				<archetypeId>openEHR-EHR-COMPOSITION.documento_actividad_fisica.v1</archetypeId>
      				<lastVersion>true</lastVersion>
      				<organizationUid>79a0f5fa-a3b1-4498-9caa-9f8dd1a3392e</organizationUid>
      				<parent>91b3f2dd-2d17-4781-9e98-7b29e63a3107::my.emr::1</parent>
      			</data>
      		</version>
      	</versions>
      </commitResult>
      */
      //echo $response;
    }

    return simplexml_load_string($response);
  }

  function get_contributions($ehr_uid, $max = 20, $offset = 0)
  {
    $url = $this->base_url .'ehrs/'. $ehr_uid .'/contributions';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
  }
}

?>

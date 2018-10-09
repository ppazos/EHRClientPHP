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

    return json_decode($result);
  }

  function get_templates($max = 20, $offset = 0)
  {
    $url = $this->base_url .'templates';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    // Because of GET data goes in URL
    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
  }

  function get_compositions($ehr_uid, $max = 20, $offset = 0)
  {
    $url = $this->base_url .'compositions';
    $data = array('format'=>'json', 'max'=>$max, 'offset'=>$offset, 'ehrUid'=>$ehr_uid);

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
  }

  function get_composition($uid)
  {
    $url = $this->base_url .'compositions/'.$uid;
    $data = array('format'=>'json');

    $options = array('http'=>array('method'=>'GET', 'header' => "Authorization: Bearer " . $this->token));

    $result = file_get_contents($url.'?'.http_build_query($data), false, stream_context_create($options));

    return json_decode($result);
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

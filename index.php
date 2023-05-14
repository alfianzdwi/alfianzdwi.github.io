<?php

try 
{
 if(post == "POST")
 {
     HttpClient httpClient = new DefaultHttpClient();
     HttpPost httpPost = new HttpPost(loginUrl);
     httpPost.setEntity(new UrlEncodedFormEntity(para));
     httpPost.setHeader("User-Agent","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.10240 ");
     httpPost.addHeader("Cookie", "__test=THE_CONTENT_OF_YOUR_COOKIE_HERE; expires=Thu, 31-Dec-37 23:55:55 GMT; path=/");
     HttpResponse httpResponse = httpClient.execute(httpPost);
     HttpEntity httpEntity = httpResponse.getEntity();
     is = httpEntity.getContent();
}
else if(post == "GET")
{
    HttpClient httpClient = new DefaultHttpClient();
    String paramString = URLEncodedUtils.format(para, "utf-8");
    loginUrl += "?" + paramString;
    HttpGet httpGet = new HttpGet(loginUrl);
    httpGet.addHeader("Cookie", "__test=THE_CONTENT_OF_YOUR_COOKIE_HERE; expires=Thu, 31-Dec-37 23:55:55 GMT; path=/");
    HttpResponse httpResponse = httpClient.execute(httpGet);
    HttpEntity httpEntity = httpResponse.getEntity();
    is = httpEntity.getContent();
}
}
// Set your server key (Note: Server key for sandbox and production mode are different)
$server_key = 'SB-Mid-server-Tm9bCNJ7bl976oskgH4Vtxk9';
// Set true for production, set false for sandbox
$is_production = false;

$api_url = $is_production ? 
  'https://app.midtrans.com/snap/v1/transactions' : 
  'https://app.sandbox.midtrans.com/snap/v1/transactions';


// Check if request doesn't contains `/charge` in the url/path, display 404
if( !strpos($_SERVER['REQUEST_URI'], '/charge') ) {
  http_response_code(404); 
  echo "wrong path, make sure it's `/charge`"; exit();
}
// Check if method is not HTTP POST, display 404
if( $_SERVER['REQUEST_METHOD'] !== 'POST'){
  http_response_code(404);
  echo "Page not found or wrong HTTP request method is used"; exit();
}

// get the HTTP POST body of the request
$request_body = file_get_contents('php://input');
// set response's content type as JSON
header('Content-Type: application/json');
// call charge API using request body passed by mobile SDK
$charge_result = chargeAPI($api_url, $server_key, $request_body);
// set the response http status code
http_response_code($charge_result['http_code']);
// then print out the response body
echo $charge_result['body'];

/**
 * call charge API using Curl
 * @param string  $api_url
 * @param string  $server_key
 * @param string  $request_body
 */
function chargeAPI($api_url, $server_key, $request_body){
  $ch = curl_init();
  $curl_options = array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    // Add header to the request, including Authorization generated from server key
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Basic ' . base64_encode($server_key . ':')
    ),
    CURLOPT_POSTFIELDS => $request_body
  );
  curl_setopt_array($ch, $curl_options);
  $result = array(
    'body' => curl_exec($ch),
    'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
  );
  return $result;
  exit();
}

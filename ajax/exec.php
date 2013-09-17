<?

/* autoload */
function __autoload($name) {
    require_once "../lib/$name.php";
}

ini_set('memory_limit', '128M');

define("DUMP_DIR", $_SERVER['DOCUMENT_ROOT']."/dump");
define("DUMP_URI", "http://".$_SERVER['HTTP_HOST']."/dump");


if(count($_POST)) {
	
	$url = $_POST['url'];
	$auth_un = $_POST['auth_un'];
	$auth_pw = $_POST['auth_pw'];
	$req_vals = (isset($_POST['req']))?$_POST['req']:array();
	
	$h = new HttpRequest($url, $req_vals, $auth_un, $auth_pw);
	$h->execute();
		
	$json = array(
		'request' => array(
			'requestLine' => array(
				'method' => $h->request->request_line->method,
				'uri' => $h->request->request_line->uri,
				'httpVersion' => $h->request->request_line->http_version),
			'headers' => $h->request->headers->getAllFields(),
			'body' => nl2br(htmlspecialchars($h->request->body))
		),
		'response' => array(
			'statusLine' => array(
				'httpVersion' => $h->response->status_line->http_version,
				'code' => $h->response->status_line->code,
				'phrase' => $h->response->status_line->phrase),
			'headers' => $h->response->headers->getAllFields(),
			'body' => nl2br(htmlspecialchars($h->response->body)),
			'type' => "text"
		)
	);
	
	
	$id = time();
	
	//content-type handling
	switch($h->response->headers->getValue("Content-Type")) {
		case "image/gif":
		case "image/jpeg":
		case "image/jpg":
		case "image/pjpeg":
		case "image/png":
		case "image/tiff":
			if(strlen($h->response->body) > 0) {
				file_put_contents(DUMP_DIR."/$id", $h->response->body);
				
				$filename = "$id".image_type_to_extension(exif_imagetype(DUMP_DIR."/$id"));
				
				rename(DUMP_DIR."/$id", DUMP_DIR."/$filename");
				$json['response']['type'] = "image";
				$json['response']['image']['path'] = DUMP_URI."/$filename";
			}
	}
	
	echo json_encode($json)."\n";
	
}
?>
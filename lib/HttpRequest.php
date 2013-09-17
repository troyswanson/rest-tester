<?php

class HttpRequestField {
	protected $name;
	protected $value;
	
	public function __construct($name, $value = "") {
		$this->name = $name;
		$this->value = $value;
	}
	
	public function __toString() {
		return urlencode($this->name)."=".urlencode($this->value);
	}
	
	public function __get($name) {
        return $this->$name;
	}
}

class HttpHeaders {
	protected $fields = array();
	
	public function __construct($headers) {
		foreach($headers as $h) {
			$this->fields[] = new HttpHeaderField(substr($h, 0, strpos($h, ":")), trim(substr($h, strpos($h, ":")+1)));
		}
	}
	
	public function __get($name) {
        return $this->$name;
	}
	
	
	public function getAllFields() {
		$fields = array();
		
		foreach($this->fields as $f) {
			$fields[] = array('name' => $f->name, 'value' => $f->value);
		}
		
		return $fields;
	}
	
	public function getValue($name) {
		$matches = array();
		
		foreach($this->fields as $f) {
			if($f->name == $name) {
				$matches[] = $f->value;
			}
		}
		
		$c = count($matches);
		if($c > 1) {
			return $matches;
		} elseif($c == 1) {
			return $matches[0];
		} else {
			return NULL;
		}
	}
}

class HttpHeaderField {
	protected $name;
	protected $value;
	
	public function __construct($name, $value = "") {
		$this->name = $name;
		$this->value = $value;
	}
	
	public function __get($name) {
        return $this->$name;
	}
}

class HttpRequestLine {
	protected $method;
	protected $uri;
	protected $http_version;
	
	public function __construct($line) {
		list($this->method, $this->uri, $this->http_version) = sscanf($line, "%s %s %s");
	}
	
	public function __get($name) {
        return $this->$name;
	}
}

class HttpRequestMessage {
	protected $message;
	protected $request_line;
	protected $headers;
	protected $body = "";
	
	public function __construct($header, $body = "") {
		$message = $header.$body;

		//save raw message
		$this->message = $message;
		
		$lines = explode("\r\n", ltrim($message));
		
		$this->request_line = new HttpRequestLine($lines[0]);
		$this->headers = new HttpHeaders(array_slice($lines, 1, array_search("", $lines)-1));
		$this->body = implode("\r\n", array_slice($lines, array_search("", $lines)+1));
	}
	
	public function __toString() {
		return $this->message;
	}
	
	public function __get($name) {
        return $this->$name;
	}
}

class HttpStatusLine {
	protected $http_version;
	protected $code;
	protected $phrase;
	
	public function __construct($line) {
		list($this->http_version, $this->code, $this->phrase) = sscanf($line, "%s %s %[^$]s");
	}
	
	public function __get($name) {
        return $this->$name;
	}
}

class HttpResponseMessage {
	protected $message;
	protected $status_line;
	protected $headers;
	protected $body = "";
	
	public function __construct($message) {
		//save raw message
		$this->message = $message;
		
		$lines = explode("\r\n", ltrim($message));
		
		$this->status_line = new HttpStatusLine($lines[0]);
		
		//var_dump(array_slice($lines, 1, array_search("", $lines)-1));
		
		$this->headers = new HttpHeaders(array_slice($lines, 1, array_search("", $lines)-1));
		$this->body = implode("\r\n", array_slice($lines, array_search("", $lines)+1));
	}
	
	public function __get($name) {
        return $this->$name;
	}
	
	public function __toString() {
		return $this->message;
	}
}

class HttpRequest {
	protected $ch;
	protected $url;
	protected $auth_un = "";
	protected $auth_pw = "";
	protected $get_fields = array();
	
	protected $post_data = "";
	
	protected $post_fields = array();
	protected $http_method = "";
	
	public $response;
	public $request;
	
	public function __construct($url, $req_vals = array(), $auth_un = "", $auth_pw = "", $auth_method = "") {
		
		//declare curl resource
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->ch, CURLOPT_HEADER, TRUE);
		curl_setopt($this->ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, FALSE);
		
		//set url
		$this->url = $url;
		
		//set request values
		if(isset($req_vals['name'])) {
			$c = count($req_vals['name']);
			
			for($i=0; $i<$c; $i++) {
				
				if(empty($req_vals['name'][$i])) { continue; }
				
				switch($req_vals['type'][$i]) {
					case "GET":
						$this->addGetField($req_vals['name'][$i], $req_vals['value'][$i]);
						break;
					case "POST":
						$this->addPostField($req_vals['name'][$i], $req_vals['value'][$i]);
						break;
					case "COOKIE":
						
						break;
					case "Off":
						break;
				}
			}
		}
		
		//http auth
		if(!empty($auth_un) && !empty($auth_pw)) {
			$this->addAuthentication($auth_un, $auth_pw);
		}
		
	}
	
	public function setHttpMethod($method) {
		$this->method = $method;
	}
	
	public function addAuthentication($un, $pw) {
		$this->auth_un = $un;
		$this->auth_pw = $pw;
	}
	
	public function addGetField($name, $value = "") {
		$this->get_fields[] = new HttpRequestField($name, $value);
		return end($this->get_fields);
	}
	
	public function addPostField($name, $value = "") {
		$this->post_fields[] = new HttpRequestField($name, $value);
		return end($this->post_fields);
	}
	
	public function execute() {
		$exec_url = $this->url.((count($this->get_fields))?"?".implode("&", $this->get_fields):"");
		curl_setopt($this->ch, CURLOPT_URL, $exec_url);
		
		if(count($this->post_fields)) {
			curl_setopt($this->ch, CURLOPT_POST, TRUE);
			
			$this->post_data = implode("&", $this->post_fields);
			
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->post_data);
		}
		
		if(!empty($this->auth_un) && !empty($this->auth_pw)) {
			curl_setopt($this->ch, CURLOPT_USERPWD, $this->auth_un.":".$this->auth_pw);
		}
		
		if(!empty($this->method)) {
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method);
		}
		
		$this->response = new HttpResponseMessage(curl_exec($this->ch));
		$this->request = new HttpRequestMessage(curl_getinfo($this->ch, CURLINFO_HEADER_OUT), $this->post_data);
		
		return TRUE;
	}
}

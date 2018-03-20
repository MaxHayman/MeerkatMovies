<?php

/*
 *	Meerkat Movies Rest API Client
 *  Author: Max Hayman <maxhayman@maxhayman.co.uk>
 */

namespace MeerkatMovies;

require_once('config.php');

class Code {

	private $code;
	private $film_edicode;
	private $film_name;
	
	public function __construct($code, $film_edicode, $film_name = "") {
		$this->code = $code;
		$this->film_edicode = $film_edicode;
		$this->film_name = $film_name;
	}

	public function check() {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'CHECK');
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $this->code);
		$cinema = $xml->addChild('cinema');
		$cinema->addAttribute('edicode', Config::cinema_edicode);

		if(Config::cinema_name) {
			$cinema->addAttribute('name', Config::cinema_name);
		}

		$film = $xml->addChild('film');

		$film->addAttribute('edicode', $this->film_edicode);

		if($this->film_name) {
			$film->addAttribute('name', $this->film_name);
		}

		$response = $this->request($xml);

		if(!$response || $response['type'] != "CHECK") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "VALID";
	}

	public function lock() {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'CHECKANDLOCK');
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $this->code);
		$cinema = $xml->addChild('cinema');
		$cinema->addAttribute('edicode', Config::cinema_edicode);
		
		if(Config::cinema_name) {
			$cinema->addAttribute('name', Config::cinema_name);
		}

		$film = $xml->addChild('film');

		$film->addAttribute('edicode', $this->film_edicode);

		if($this->film_name) {
			$film->addAttribute('name', $this->film_name);
		}

		$response = $this->request($xml);

		if(!$response || $response['type'] != "CHECKANDLOCK") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "VALID";
	}

	public function release() {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'PAYMENTRESULT');
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $this->code);
		$payment = $xml->addChild('payment');
		$payment->addAttribute('status', 'INVALID');

		$response = $this->request($xml);

		if(!$response || $response['type'] != "PAYMENTRESULT") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "OK";
	}

	public function commit() {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'PAYMENTRESULT');
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $this->code);
		$payment = $xml->addChild('payment');
		$payment->addAttribute('status', 'VALID');

		$response = $this->request($xml);

		if(!$response || $response['type'] != "PAYMENTRESULT") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "OK";
	}

	private function request($xml) {

		$ch = curl_init();

		$options = array( 
		    CURLOPT_RETURNTRANSFER => true,		     
		    CURLOPT_URL => Config::url ,
		    CURLOPT_SSLCERT => Config::cert_file ,
		    CURLOPT_SSLCERTPASSWD => Config::cert_password ,
			CURLOPT_POSTFIELDS => $xml->asXML() ,
			
		);

		curl_setopt_array($ch , $options);

		$output = curl_exec($ch);
 
		if(!$output) {
		    error_log("MeerkatMovies: " . curl_error($ch));
		    return false;
		} else {
		    return new \SimpleXMLElement($output);
		}
	}
}
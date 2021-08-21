<?php
/*
 *	Meerkat Movies Rest API Client
 *  Author: Max Hayman <maxhayman@maxhayman.co.uk>
 */

namespace MeerkatMovies;

class MeerkatMovies {
	
	private $endpoint;
	private $certificateFile;
	private $certificatePassword;
	private $cinemaEdiCode;
	private $cinemaName;
	
	public function __construct($endpoint, $certificateFile, $certificatePassword, $cinemaEdiCode, $cinemaName = null) {
        $this->endpoint = $endpoint;
		$this->certificateFile = $certificateFile;
		$this->certificatePassword = $certificatePassword;
		$this->cinemaEdiCode = $cinemaEdiCode;
		$this->cinemaName = $cinemaName;		
	}
	
	public function check($code) {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'CHECK');
		
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $code->getCode());
		
		// Cinema Data
		$cinema = $xml->addChild('cinema');		
		$cinema->addAttribute('edicode', $this->cinemaEdiCode);
		
		if($this->cinemaName != null) {
			$cinema->addAttribute('name', $this->cinemaName);
		}
		
		// Film Data
		$film = $xml->addChild('film');
		$film->addAttribute('edicode', $code->getFilmEdiCode());
		
		if($code->getFilmName() != null) {
			$film->addAttribute('name', $code->getFilmName());
		}

		$response = $this->request($xml);

		if(!$response || $response['type'] != "CHECK") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "VALID";
	}
	
	public function lock($code) {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'CHECKANDLOCK');
		
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $code->getCode());
		
		// Cinema Data
		$cinema = $xml->addChild('cinema');		
		$cinema->addAttribute('edicode', $this->cinemaEdiCode);
		
		if($this->cinemaName != null) {
			$cinema->addAttribute('name', $this->cinemaName);
		}
		
		// Film Data
		$film = $xml->addChild('film');
		$film->addAttribute('edicode', $code->getFilmEdiCode());
		
		if($code->getFilmName() != null) {
			$film->addAttribute('name', $code->getFilmName());
		}

		$response = $this->request($xml);

		if(!$response || $response['type'] != "CHECKANDLOCK") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "VALID";
	}
	
	public function release($code) {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'PAYMENTRESULT');
		
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $code->getCodE());
		
		$payment = $xml->addChild('payment');
		$payment->addAttribute('status', 'INVALID');

		$response = $this->request($xml);

		if(!$response || $response['type'] != "PAYMENTRESULT") {
			error_log("Meerkat Invalid Response");
			return false;
		}

		return $response->response['status'] == "OK";
	}
	
	public function commit($code) {

		$xml = new \SimpleXMLElement("<xmlrequest></xmlrequest>");
		$xml->addAttribute('type', 'PAYMENTRESULT');
		
		$pin = $xml->addChild('pin');
		$pin->addAttribute('number', $code->getCode());
		
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
		    CURLOPT_URL => $this->endpoint,
		    CURLOPT_SSLCERT => $this->certificateFile,
		    CURLOPT_SSLCERTPASSWD => $this->certificatePassword,
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

class Code {

	private $code;
	private $filmEdiCode;
	private $filmName;
	
	public function __construct($code, $filmEdiCode, $filmName = null) {
		$this->code = $code;
		$this->fileEdiCode = $filmEdiCode;
		$this->filmName = $filmName;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function getFilmEdiCode() {
		return $this->filmEdiCode;
	}
	
	public function getFilmName() {
		return $this->filmName;
	}
}
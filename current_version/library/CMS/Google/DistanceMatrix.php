<?php

class CMS_Google_DistanceMatrix {
	
	private $_baseUri 	= 'https://maps.googleapis.com/maps/api/distancematrix/json?';
	
	private $_sensor 	= 'false';
	private $_mode 		= 'driving';
	private $_language	= 'fr-FR';
	
	private $_origin;
	private $_destination;
	
	private $_response;
	private $_distance;
	private $_duration;
	private $_error;
	
	public function setOrigin($value){
		$this->_origin = $this->sanitizeAddress($value);
	}
	public function setDestination($value){
		$this->_destination = $this->sanitizeAddress($value);
	}
	
	private function sanitizeAddress($value){
		$value = str_replace('|', ' ', $value);
		
		return $value;
	}
	
	public function execRequest(){
		if(!isset($this->_response) && !$this->hasError()){
			
			$params = array(
					'origins' 		=> $this->_origin,
					'destinations' 	=> $this->_destination,
					//'langage'		=> $this->_language,
					//'mode'			=> $this->_mode,
					'sensor'		=> $this->_sensor
			);
			
			$url = $this->_baseUri . http_build_query($params);
			
			$json = @file_get_contents($url);
			
			if(!$json){
				$this->_error = 'NO RESPONSE';
				return;
			}
			
			$response = @json_decode($json, true);
			
			if(!$response){
				$this->_error = 'INVALID JSON';
				return;
			}
			
			if($response['status'] != 'OK'){
				$this->_error = $response['status'];
				return;
			}
			if($response['rows'][0][elements][0]['status'] != 'OK'){
				$this->_error = $response['rows'][0][elements][0]['status'];
				return;
			}
			
			$this->_response = $response;
		}
	}
	
	public function getResponse(){
		
		$this->execRequest();
		
		return $this->_response;
	}
	
	public function getDistance(){
		
		if(!isset($this->_distance)){
			$this->execRequest();
			
			if($this->hasError())
				return null;
			
			$this->_distance = $this->_response['rows'][0][elements][0]['distance']['value'];
		}
		
		return $this->_distance;
	}
	
	public function getDuration(){
		
		if(!isset($this->_duration)){
			$this->execRequest();
			
			if($this->hasError())
				return null;
			
			$this->_duration = $this->_response['rows'][0][elements][0]['duration']['value'];
		}
		
		return $this->_duration;
	}
	
	public function hasError(){
		if(!isset($this->_response))
			return null;
		else if(isset($this->_error))
			return true;
		
		return false;
	}
	
	public function getError(){
		if(isset($this->_error))
			return $this->_error;
	
		return null;
	}
	
}
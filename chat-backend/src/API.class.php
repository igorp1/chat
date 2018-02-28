<?php

class API{

	public $endpoints = array();

	private $_server;
	private $_request;

	//====== PUBLIC METHODS ======//
	/*
		Call to process a given a request through the API
	*/
    public function processRequest(array $request, array $server){
		$this->_server = $server;
		$this->_request = $request;
 
	    $this->routeRequest($server["REQUEST_URI"]);
	}

	/*
		Call to register an APIModule on this API.
	*/
    public function registerModule(APIModule $apiModule){
        $this->endpoints = array_merge($this->endpoints, $apiModule->getPathMaps());
    }
	
	/*
		Call to whitelist a given list of origins
	*/
	public function setCrossOriginWhiteList(array $allowedOrigins){
        $header = "Access-Control-Allow-Origin: " . implode(",", $allowedOrigins);
		header($header, false);
		header("Access-Control-Allow-Headers: Content-Type");
    }

	//====== PRIVATE METHODS ======//
	/*
		Routes a given request to an API endpoint	
	*/
    private function routeRequest(string $rqstUri){

		$route = explode("/api", $rqstUri)[1];      
		$endpoint = $this->getEndpoint($route);

		if($endpoint === null){ $this->sendError(404, "The route was not found."); }

		$this->checkOptions($endpoint);
		$params = [];
		$params = $this->getUriParams($endpoint, $route);
		$params['json'] = json_decode( file_get_contents('php://input') );
		$this->sendResponse($endpoint->func->call($endpoint, $params));
	}
	
	/*
		Return an endpoint for a given route.
	*/
	private function getEndpoint(string $uri){
		
		$endpoint = null;
		$matchScore = 0;
		foreach($this->endpoints as $routePath => $currentEndpoint){

			if( count(array_diff(explode("/", $routePath), [""])) !== count(array_diff(explode("/", $uri), [""])) ){ 
				continue; 
			}
			$currentMatchScore = $this->matchRoute($routePath, $uri); 
			if( $currentMatchScore > $matchScore){
				$endpoint = $currentEndpoint;
				$matchScore = $currentMatchScore;
			}
		}
		return $endpoint;
	}

	/*
		Checks is a given route matches 
	*/
	private function matchRoute(string $route, string $uri){
		$routeMap = array_diff(explode("/", $route), [""]);
		$routeMapKeys = array_keys($routeMap);
		$uriMap = array_diff(explode("/", $uri), [""]);
		$uriMapKeys = array_keys($uriMap);

		$matchCount = 0;
		for($i = 0; $i < count($routeMap); $i++){
			if( $routeMap[$routeMapKeys[$i]][0] === ":" ){ continue; }
			if( $routeMap[$routeMapKeys[$i]] !== $uriMap[$uriMapKeys[$i]] ){ return 0; }
			$matchCount++;
		}
		return $matchCount;
	}

	/*
		Get prams from URI
	*/
	private function getUriParams(APIEndpoint $endpoint, string $uri){
		$routeMap = array_diff(explode("/", $endpoint->route), [""]);
		$routeMapKeys = array_keys($routeMap);
		$uriMap = array_diff(explode("/", $uri), [""]);
		$uriMapKeys = array_keys($uriMap);

		$params = [];
		for($i = 0; $i < count($routeMap); $i++){
			if( $routeMap[$routeMapKeys[$i]][0] === ":" ){ 
				$paramName = substr($routeMap[$routeMapKeys[$i]], 1);
				$params[$paramName] = $uriMap[$uriMapKeys[$i]];
			}
		}
		return $params;

	} 

	/*
		Does any necessary checks for the given $endpoint options
	*/
	private function checkOptions(APIEndpoint $endpoint){
		foreach($endpoint->options as $optionName => $optionValue){
			switch($optionName){
				case 'method':
					if($this->_server['REQUEST_METHOD'] === 'OPTIONS' ){
						break;
					}
					if($this->_server['REQUEST_METHOD'] !== $optionValue ){
						$this->sendError(400, "This request is not using the right method.");
					} 
					break;
				default:
					throw new Exception("The given option is not known for {$endpoint->route}");
			}
		}
	}

	/*
		Sends the response back to the client as a json
	*/
	private function sendResponse(array $responseObj, bool $asJson=true){
		$this->setResponseHeaders();
		echo $asJson ? json_encode($responseObj) : $responseObj;
	}

	/*
		Sets header for the response
	*/
	private function setResponseHeaders(){
		header("Content-Type: application/json");
	}
	
	/*
		Sends an error response to the user
	*/
	private function sendError(int $status_code, string $msg){
		http_response_code($status_code);
		echo $msg;
		exit();
	}

}


class APIModule{

	public $endpoints;
	
	public $routePrefix;

    /*
        Default contructor
    */
    public function __construct(string $moduleRoutePrefix) { 
		$this->endpoints = array();
		$this->routePrefix = $moduleRoutePrefix;
    }    

    /*
        Call to register endpoints on this module
    */
    public function registerEndpoint(string $route, Closure $func, array $options=[]){
		$this->endpoints[] = new APIEndpoint($this->routePrefix . $route, $func, $options);
	}

	/*
        Call to register endpoints on this module
    */
    public function registerReadyEndpoint(APIEndpoint $endpoint){
		$endpoint->route = $this->routePrefix . $endpoint->route;
		$this->endpoints[] = $endpoint;
	}

	/*
		Returns all registered paths and associated endpoints in a dictionary
	*/
	public function getPathMaps(){
		$allPathMaps = [];
		foreach( $this->endpoints as $endpoint ){
			$currentMap = $endpoint->route;
			$allPathMaps[$currentMap] = $endpoint;
		}
		return $allPathMaps;
	}

}


class APIEndpoint {

    public $route; // String
    public $func; // Closure
    public $options; // Array

    function __construct(string $route, Closure $func, array $options=[]){
        $this->route = $route;
        $this->func = $func;
        $this->options = $options;
    }

}
<?php

/**
 * Restrict the set of URLs that can be accessed via particular host names
 *
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
class RestrictedCmsHostFilter implements RequestFilter {

	public $patterns;

	public $allowedHosts;
	
	public $redirectTarget = '';
	
	public $responseInfo;
	
	public $forceSsl = true;
	
	public $applyOnCli = false;
	
	public function postRequest(\SS_HTTPRequest $request, \SS_HTTPResponse $response, \DataModel $model) {
		if (strlen($this->redirectTarget)) {
			$response->redirect($this->redirectTarget);
		} else if (strlen($this->responseInfo)) {
			list($code, $description) = explode(' ', $this->responseInfo, 2);
			$response->setStatusDescription($description);
			$response->setStatusCode($code);
			$response->setBody($description);
		}
	}

	public function preRequest(\SS_HTTPRequest $request, \Session $session, \DataModel $model) {
		if (Director::isDev() || !$this->patterns || !$this->allowedHosts) {
			return;
		}
		
		if (!$this->applyOnCli && PHP_SAPI == 'cli') {
			return;
		}

		$url = $request->getURL();

		// Uses the client supplied HTTP HOST. To protect against forgery, 
        // _ONLY_ the hostnames that should be served from this server should be
        // configured as keys in the allowedHosts entry. 
		$currentHost = $_SERVER['HTTP_HOST'];
		
		// if a pattern matches, it means we need to check whether this host is 
        // allowed to serve one of these matching
		foreach ($this->patterns as $pattern) {
            $pattern = "{" . $pattern . "}i";
			if (preg_match($pattern, $url)) {
                if (isset($this->allowedHosts[$currentHost]) && $currentHost == $this->allowedHosts[$currentHost]) {
                    // all good, let the request onwards. 
                    return;
                }

                // otherwise, check if there's a redirect needed
				$target = '';
				foreach (array($currentHost, '*') as $check) {
					if (isset($this->allowedHosts[$check])) {
						$target = $this->allowedHosts[$check];
						break;
					}
				}
                
                // if nothing explicit, 404 instead
                if (!strlen($target)) {
                    $target = '404';
                }
                
				if (strlen($target)) {
					if (is_numeric($target)) {
						$exception = new SS_HTTPResponse_Exception('', $target);
						$message = $exception->getResponse()->getStatusDescription();
						$this->responseInfo = "$target $message";
						$request->setUrl('restrictedcmshandler');
					} else {
						$protocol = $this->forceSsl || (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') ? 'https' : 'http';
						$this->redirectTarget = "$protocol://$target/$url";
						$request->setUrl('restrictedcmshandler');
					}
				}
			}
		}
	}
}

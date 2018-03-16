<?php

/**
 *	When using an alternate URL for the CMS, this causes emails that have been generated from within the CMS to output the alternate URL for links.
 *	Essentially, that causes issues when the alternate URL is completely blocked for a user.
 *	This mailer replaces the alternate URL with the correct URL prior to sending.
 */

class RestrictedCMSMailer extends Mailer {

	// The mailer to actually serve the email.

	public $to_mailer;

	// What to replace.

	public $replace_body;

	public $with;

	public function sendPlain($to, $from, $subject, $plainContent, $attachedFiles = array(), $customHeaders = array()) {

		$plainContent = preg_replace("%{$this->replace_body}(?!\/admin)%", $this->with, $plainContent);
		return $this->to_mailer->sendPlain($to, $from, $subject, $plainContent, $attachedFiles, $customHeaders);
	}

	public function sendHTML($to, $from, $subject, $htmlContent, $attachedFiles = array(), $customHeaders = array(), $plainContent = '') {

		$htmlContent = preg_replace("%{$this->replace_body}(?!\/admin)%", $this->with, $htmlContent);
		return $this->to_mailer->sendHTML($to, $from, $subject, $htmlContent, $attachedFiles, $customHeaders, $plainContent);
	}

	// This is so "always from" or anything specific to the mailer can be bound correctly.

	public function __set($key, $value) {

		$this->to_mailer->$key = $value;
	}

	public function __call($function, $arguments) {

		call_user_func_array(array($this->to_mailer, $function), $arguments);
	}

}

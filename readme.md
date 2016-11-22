# Restricted CMS 

> A SilverStripe CMS module that will force users to a different hostname when hitting protected URLs.

## Requirements

* SilverStripe 3.2.x

## Getting Started

* Place the module under your root project directory.
* `/dev/build`
* Create a restrictedcms.yml file in your project's _config/ folder, with config
  similar to that found in docs/en/restrictedcms.yml.sample

## Overview

The restricted CMS module works by intercepting incoming requests to the CMS 
and comparing them against a set of patterns for particular hosts, to determine
whether they should be allowed to be served by the current server. 

This provides two layers of protection

* Verifying the HTTP_HOST presented by the end user is allowed to be served
  by 'this' server
* Allowing requests to particular URLs to be redirected to different hostnames,
  which in turn may be behind a firewall somewhere. 


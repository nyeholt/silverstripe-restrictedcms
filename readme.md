# Restricted CMS 

> A SilverStripe CMS module that will force users to a different hostname when hitting protected URLs.

## Requirements

* SilverStripe 3.2.x

## Getting Started

* Place the module under your root project directory.
* `/dev/build`
* Create a restrictedcms.yml file in your project \_config/ folder, with config
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


## Specific environment configuration

If you have a separate authoring instance (ie cms.mysite.com) to your main site
(eg www.mysite.com), you can specify configuration that is loaded just for the
specific environment. Using the sample restrictedcms.yml.sample file, the 
config blocks for each environment can be included / excluded by making use of
the ENV\_TYPE\_FRONTEND or ENV\_TYPE\_BACKEND defines from your 
_ss_environment.php in the respective environments. 

As SilverStripe's configuration layer only allows the swapping based on 
`define`d properties, you may need a fragment like the following if you're 
setting these properties via environment variables from your webserver

```
foreach (array('ENV_PROD', 'ENV_TEST', 'ENV_TYPE_FRONTEND', 'ENV_TYPE_BACKEND') as $envVar) {
    if (getenv($envVar)) {
        define($envVar, 1);
    }
}
```

 

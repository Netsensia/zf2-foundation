## Netsensia Foundation Classes for Zend Framework 2 Applications

This is the hosted packagist module that is used in the [https://github.com/Netsensia/netsensia-zf2-skeleton](Netsensia ZF2 Skeleton application)

Cloning that skeleton is the easiest way to get started.  You can then use Composer to keep up-to-date with the latest changes.

Alternatively, you may want to install from scratch in your own skeleton application, in which case something like this will work:

### Add to composer.json

	"require" : {
		"netsensia/zf2-foundation" : "dev-master"
	},

### Copy config files

  cp vendor/netsensia/zf2-foundation/config/netsensia.local.php.dist config/autoload/netsensia.local.php
  cp vendor/cloud-solutions/zend-sentry.global.php.dist config/autoload/zend-sentry.global.php

### Prepare example view scripts

  cp vendor/netsensia/zf2-foundation/config/index.phtml.dist module/Application/view/application/index/index.phtml
  cp vendor/netsensia/zf2-foundation/config/layout.phtml.dist module/Application/view/application/layout/layout.phtml
  
  
  

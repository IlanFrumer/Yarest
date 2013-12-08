# Yarest
[![Build Status](https://travis-ci.org/IlanFrumer/Yarest.png?branch=develop)](https://travis-ci.org/IlanFrumer/Yarest)

### Installaion

Install via [composer](http://getcomposer.org/):

composer require yarest/yarest

### System Requirements

- **PHP >= 5.3.0**

### Basic usage

Require composer's autoloader

  require_once 'vendor/autoload.php';

Instantiate a Yarest application:

  $app = new \Yarest\App();

Define a route:
  
  $route = $app->route('/api/*', 'Api');

Define route callbacks:

  $route->before(function ($resource) {
  
  });

  $route->inject(function ($resource) {
  
  });

  $route->after(function ($resource) {
  
  });

Dispatch the route:

  $app->run()->headers()->body();

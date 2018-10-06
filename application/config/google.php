<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Google API Configuration
| -------------------------------------------------------------------
|  client_id         string   Your Google API Client ID.
|  client_secret     string   Your Google API Client secret.
|  redirect_uri      string   URL to redirect back to after login.
|  application_name  string   Your Google application name.
|  api_key           string   Developer key.
|  scopes            string   Specify scopes
*/
$config['google']['client_id']        = '367630037663-23ijmpn6s0sv91441vl3ee2l53q2rtot.apps.googleusercontent.com';//Google_API_Client_ID
$config['google']['client_secret']    = 'YXj41Nzo5wmVikFm1BrAFCu0';//Google_API_Client_Secret
$config['google']['redirect_uri']     = 'http://localhost/assetsapi/assetsapi/google';//'https://assetwatch-318c2.firebaseapp.com/__/auth/handler';
$config['google']['application_name'] = 'Login to assetsapi';//Login to CodexWorld.com
$config['google']['api_key']          = '';
$config['google']['scopes']           = array();

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'auth/login';
//$route['adminpanel/register'] = "adminpanel/register";
$route['404_override'] = '';

$route['register'] = "/auth/register";
$route['auth/login'] = "/auth/login";
$route['login'] = "/auth/login";
$route['logout'] = "/auth/logout";
$route['profile'] = "/profile";
$route['index'] = "/auth/index";
$route['dashboard'] = "/auth/index";
$route['owner'] = "/user/owner";
$route['agent'] = "/user/agent";
$route['tenant'] = "/user/tenant";
$route['property'] = "/property";
$route['profileupdate/:any'] = "/profile/profileupdate";

$route['blog'] = "/blog";
$route['blog/add'] = "/blog/add";
$route['blog/edit/:any'] = "/blog/edit";
$route['blog/view/:any'] = "/blog/view";
$route['blog/delete/:any'] = "/blog/tdelete";

$route['testimonial'] = "/testimonial";
$route['testimonial/add'] = "/testimonial/add";
$route['testimonial/edit/:any'] = "/testimonial/edit";
$route['testimonial/view/:any'] = "/testimonial/view";
$route['testimonial/delete/:any'] = "/testimonial/tdelete";

$route['advertisement'] = "/advertisment";
$route['advertisement/add'] = "/advertisment/add";
$route['advertisement/edit/:any'] = "/advertisment/edit";
$route['advertisement/view/:any'] = "/advertisment/view";
$route['advertisement/delete/:any'] = "/advertisment/tdelete";

$route['plan'] = "/advertisment";
$route['plan/add'] = "/plan/add";
$route['plan/edit/:any'] = "/plan/edit";
$route['plan/view/:any'] = "/plan/view";
$route['plan/delete/:any'] = "/plan/tdelete";


$route['settings'] = "/settings";
$route['plan'] = "/plan";
$route['portalcontent'] = "/settings/portal_content";
$route['portalcontent/add'] = "/settings/portal_content_add";
$route['portalcontent/edit/:any'] = "/settings/portal_content_edit";
$route['portalcontent/delete/:any'] = "/settings/deletePortalData";

$route['features']="/plan/feature_list";
$route['feature/edit/:any'] = "/plan/feature_edit";
//$route['feature/delete/:any'] = "/plan/feature_delete";

$route['featuremapper']="/plan/featuremapper";
$route['featuremapper/add'] = "/plan/featuremapper_add";
$route['featuremapper/edit/:any'] = "/plan/featuremapper_edit";
$route['featuremapper/delete/:any'] = "/plan/featuremapper_delete";

// $route['propertyform']="/agreementform/propertyform";
// $route['propertyform/add'] = "/agreementform/propertyform_add";
// $route['propertyform/edit/:any'] = "/agreementform/propertyform_edit";
// $route['propertyform/delete/:any'] = "/agreementform/propertyform_delete";

$route['agreementform']="/agreementform/agreementform";
$route['agreementform/add'] = "/agreementform/signatureform_add";
$route['agreementform/edit/:any'] = "/agreementform/signatureform_edit";
$route['agreementform/delete/:any'] = "/agreementform/signatureform_delete";

$route['translate_uri_dashes'] = FALSE;

$route['contactinfo'] = "/contactinfo";
$route['contactinfo/add'] = "/contactinfo/add";
$route['contactinfo/edit/:any'] = "/contactinfo/edit";
$route['contactinfo/delete/:any'] = "/contactinfo/delete";
$route['agent_review'] = "/user/agent_review";
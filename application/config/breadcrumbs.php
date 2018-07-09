<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| BREADCRUMB CONFIG
| -------------------------------------------------------------------
| This file will contain some breadcrumbs' settings.
|
| $config['crumb_divider']		The string used to divide the crumbs
| $config['tag_open'] 			The opening tag for breadcrumb's holder.
| $config['tag_close'] 			The closing tag for breadcrumb's holder.
| $config['crumb_open'] 		The opening tag for breadcrumb's holder.
| $config['crumb_close'] 		The closing tag for breadcrumb's holder.
|
| Defaults provided for twitter bootstrap 2.0
*/



$config['default'] = array(
  'tag_open' => '<ol class="breadcrumb">',
  'tag_close' => '</ol>',
  'crumb_divider' => '',
  'crumb_open' => '<li>',
  'crumb_close' => '</li>',
  'crumb_first_open' => '<li>',
  'crumb_first_close' => '</li>',
  'crumb_last_open' => '<li class="active">',
  'crumb_last_close' => '</>',

);






/* End of file breadcrumbs.php */
/* Location: ./application/config/breadcrumbs.php */

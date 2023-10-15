<?php 
/**
 *  @package MyPlugin
 *  
 */

/*
Plugin Name: Message Plugin 
Plugin URI: 
Description: A plugin that will display a message on the shopping cart page
Version: 1.0.0  
Author URI:
License: 
Text Domain: message
*/

defined( 'ABSPATH' ) or die();

file_exists(dirname(__FILE__).'/vendor/autoload.php') ? require_once dirname(__FILE__).'/vendor/autoload.php':die();

use Cartmessage\Message;

$message = new Message();

// backend
if ( is_admin() ){
    $message->register_admin();
}
// front end 
if (! is_admin()){
    $message->register_front();
    $message->create();
}








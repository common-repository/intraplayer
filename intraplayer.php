<?php
/**
 * @package Intraplayer
**/

/*
Plugin Name: Intraplayer
Plugin URI: http://intraplayer.com/en/wp-plugin.html
Description: Plugin for WordPress
Version: 1.0.0
Author: NickDiesel
Author URI: http://intraplayer.com/en/wp-plugin.html
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);*/

define('INTRAPLAYER_PLUGIN_VERSION', '1.0.0');
define('INTRAPLAYER_PLUGIN_NAME', 'Intraplayer');
define('INTRAPLAYER_PLUGIN_PREFIX', 'intraplayer_');
define('INTRAPLAYER_PLUGIN_DIR', dirname(__FILE__).'/');
define('INTRAPLAYER_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

define('INTRAPLAYER_PLUGIN_EXT_SCRIPT', 'http://intraplayer.com/engine/wp-plugin.php');

// Make sure we don't expose any info if called directly
if(preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF']))
	die('You are not allowed to call this page directly.');

// Class Intraplayer	
if(!class_exists(INTRAPLAYER_PLUGIN_NAME))
{
	class Intraplayer 
	{
		private $pages = array();
		private $errors = array();
		
		// constructor
		function Intraplayer()
		{
			// activate plugin
			register_activation_hook(__FILE__, array(&$this, 'activate'));
			 
			// deactivate plugin
			register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
			 
			// uninstall plugin
			register_uninstall_hook(__FILE__, array(&$this, 'uninstall'));
			
			if(is_admin())
			{
				// create menu
				add_action('admin_menu', array(&$this, 'admin_menu'));
			} 
			else 
			{
				// add short code
				add_shortcode(INTRAPLAYER_PLUGIN_PREFIX.'embed_code', array(&$this, 'embed_code_handler'));
			}
		}
		
		// activate plugin
		function activate()
		{
			// add options
			if(!($list_iframe_settings = $this->process_options('get', array('list_iframe_settings'))))
			{
				if($list_iframe_settings = $this->curl('GET', 'list_iframe_settings'))
				{
					// arrays
					$this->process_options('add', array('list_iframe_settings' => $list_iframe_settings));
					
					// options
					$iframe_design = array_shift(array_keys($list_iframe_settings['designs']));
					$iframe_size = array_shift(array_keys($list_iframe_settings['sizes']));
					$this->process_options('add', array('api_key' => '', 'embed_data' => '', 'iframe_design' => $iframe_design, 'iframe_size' => $iframe_size));
				}
			}
		}
		
		// deactivate plugin
		function deactivate()
		{
			// delete options
			$this->process_options('delete', array('list_iframe_settings', 'api_key', 'embed_data', 'iframe_design', 'iframe_size'));
		}
		 
		// uninstall plugin
		function uninstall()
		{
			// delete options
			$this->process_options('delete', array('list_iframe_settings', 'api_key', 'embed_data', 'iframe_design', 'iframe_size'));
		}
		
		// add or update / delete / get options
		function process_options($action, $options = array())
		{
			if(is_array($options) && !empty($options))
			{
				$fl = 0;
				$get_options = array();
				
				foreach($options as $k => $v)
				{
					switch($action)
					{
						case 'add':
								if(add_option(INTRAPLAYER_PLUGIN_PREFIX.$k, $v, '', 'yes'))
									$fl++;
							break;
							
						case 'update':
								if(get_option(INTRAPLAYER_PLUGIN_PREFIX.$k) != $v)  
								{
    								if(update_option(INTRAPLAYER_PLUGIN_PREFIX.$k, $v))
    									$fl++;
								}
								else 
								{
									if(add_option(INTRAPLAYER_PLUGIN_PREFIX.$k, $v, '', 'yes'))
										$fl++;
								}
							break;	
							
						case 'delete':
								if(delete_option(INTRAPLAYER_PLUGIN_PREFIX.$v))
    								$fl++;
							break;
							
						case 'get':
								$get_options[$v] = get_option(INTRAPLAYER_PLUGIN_PREFIX.$v);
							break;	
					}
				}
				
				return $action !== 'get' ? $fl === count($options) : (count($get_options) == 1 ? array_shift($get_options) : $get_options);
			}
			
			return false;
		}
		
		// menu
		function admin_menu()
		{
			global $plugin_page;
			
			// add menu
			$this->pages['intraplayer-settings'] = add_menu_page('Intraplayer Settings', 'Intraplayer', 'manage_options', 'intraplayer-settings', array(&$this, 'admin_page_settings'));
			
			// add submenu
			$this->pages['intraplayer-about-us'] = add_submenu_page('intraplayer-settings', 'About Us', 'About Us', 'manage_options', 'intraplayer-about-us', array(&$this, 'admin_page_about_us'));
			
			// LOG
			/*if($handle = @fopen(INTRAPLAYER_PLUGIN_DIR.'admin-menu.txt', 'a+'))
			{
				@fwrite($handle, 'Good'."\n");
				@fwrite($handle, 'Pages: '.implode(', ', $this->pages)."\n");
				@fwrite($handle, 'Plugin Page: '.$plugin_page."\n");
				@fclose($handle);
			}*/
			
			// add styles / scripts
			/*foreach($this->pages as $k => $v)
				if($plugin_page == $k)
					//add_action('admin_print_scripts-'.$v, array(&$this, 'admin_print_scripts'));
					add_action('admin_enqueue_scripts', array(&$this, 'admin_print_scripts'));*/
		}
		
		// styles / scripts
		function admin_print_scripts()
		{
			// register / add style
			/*wp_register_style(INTRAPLAYER_PLUGIN_PREFIX.'admin_css', INTRAPLAYER_PLUGIN_DIR_URL.'css/admin.css');
			wp_enqueue_style(INTRAPLAYER_PLUGIN_PREFIX.'admin_css');
			
			// register / add script
			wp_register_script(INTRAPLAYER_PLUGIN_PREFIX.'admin_js', INTRAPLAYER_PLUGIN_DIR_URL.'js/admin.js', array('jquery'));
			wp_enqueue_script(INTRAPLAYER_PLUGIN_PREFIX.'admin_js');*/
		}
		
		// pages
		function admin_page_settings()
		{
			@include_once(INTRAPLAYER_PLUGIN_DIR.'admin/header.php');
			@include_once(INTRAPLAYER_PLUGIN_DIR.'admin/page-settings.php');
		}
		
		function admin_page_about_us()
		{
			/*@include_once(INTRAPLAYER_PLUGIN_DIR.'admin/header.php');*/
			@include_once(INTRAPLAYER_PLUGIN_DIR.'admin/page-about-us.php');
		}
		
		// put / get content
		function curl($action, $logic, $params = array(), $files = array())
		{
			$response = '';
			
			if($action && $logic)
			{
				
				$post = array();
				$post['action'] = $action;
				$post['logic'] = $logic;
				
				if(is_array($params) && !empty($params))
					$post['params'] = serialize($params);
					
				if($curl = curl_init(INTRAPLAYER_PLUGIN_EXT_SCRIPT))
				{
					// files data
					if(is_array($files) && !empty($files))
						foreach($files as $key => $file)
							$post[$key] = '@'.$file['tmp_name'].';type='.$file['type'];
					//$post = array_merge($post, $files);
					
					curl_setopt($curl, CURLOPT_HEADER, 0);
					curl_setopt($curl, CURLOPT_VERBOSE, 0);
					curl_setopt($curl, CURLOPT_POST, 1);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
					
					$response = curl_exec($curl);
					
					curl_close($curl);
				}
				else
				{
					$this->add_error('CURL_INIT', 'curl support is not enabled');
					
					$data = '';
					$boundary = '---------------------'.substr(md5(rand(0, 32000)), 0, 10);
					
					// post data
					foreach($post as $key => $val)
					{
						$data .= '--'.$boundary."\n";
						$data .= 'Content-Disposition: form-data; name="'.$key.'"'."\n\n".$val."\n";
					}
					
					// files data
					foreach($files as $key => $file)
					{
						$file_contents = file_get_contents($file['tmp_name']);
						
						$data .= '--'.$boundary."\n";
						$data .= 'Content-Disposition: form-data; name="'.$key.'"; filename="'.$file['name'].'"'."\n";
						$data .= 'Content-Type: '.$file['type']."\n";
						$data .= 'Content-Length: '.filesize($file['tmp_name'])."\n";
						$data .= 'Content-Transfer-Encoding: binary'."\n\n";
						$data .= $file_contents."\n";
					}
					
					$data .= '--'.$boundary."\n";
					
					$params = array(
						'http' => array(
							'method' => 'POST',
							'header' => 'Content-Type: multipart/form-data; boundary='.$boundary,
							'content' => $data
						)
					);
					
					if($fp = @fopen(INTRAPLAYER_PLUGIN_EXT_SCRIPT, 'rb', false, stream_context_create($params)))
					{
						if(($response = @stream_get_contents($fp)) === false)
							$this->add_error('FOPEN_RESPONSE', 'Problem reading data from '.INTRAPLAYER_PLUGIN_EXT_SCRIPT.($php_errormsg ? ' ('.$php_errormsg.')' : ''));
					}
					else
						$this->add_error('FOPEN', 'Problem with '.INTRAPLAYER_PLUGIN_EXT_SCRIPT.($php_errormsg ? ' ('.$php_errormsg.')' : ''));
				}
			}
			
			return $response !== false && $response !== '' ? unserialize($response) : $response; 
		}
		
		// shortcode
		function embed_code_handler($atts, $content = null)
		{
			if(!defined('INTRAPLAYER_EMBED_CODE_HANDLER'))
			{
				$options = $this->process_options('get', array('api_key', 'embed_data'));
				if($options['api_key'] && $options['embed_data']['code'])
				{
					define('INTRAPLAYER_EMBED_CODE_HANDLER', true);
					return $options['embed_data']['code'];
				}
			}
			
			return '';
		}
		
		// error
		function add_error($name, $str)
		{
			$this->errors[$name] = $str;
		}
		
		// redirect
		function redirect($url)
		{
			$url = $url ? $url : $_SERVER['HTTP_REFERER'];
			
			if(empty($this->errors))
			{
				wp_redirect($url);
				die('<script type="text/javascript">window.location="'.$url.'"</script>');
			}
		}
	}
}

$intraplayer = new Intraplayer();
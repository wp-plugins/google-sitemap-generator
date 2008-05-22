<?php

/*
 $Id$

 Google XML Sitemaps Generator for WordPress
 ==============================================================================
 
 This generator will create a sitemaps.org compliant sitemap of your WordPress blog.
 Currently homepage, posts, static pages, categories, archives and author pages are supported.
 
 The priority of a post depends on its comments. You can choose the way the priority
 is calculated in the options screen.
 
 Feel free to visit my website under www.arnebrachhold.de!

 For aditional details like installation instructions, please check the readme.txt and documentation.txt files.
 
 Have fun! 
   Arne


 Info for WordPress:
 ==============================================================================
 Plugin Name: Google XML Sitemaps 
 Plugin URI: http://www.arnebrachhold.de/redir/sitemap-home/
 Description: This plugin will generate a sitemaps.org compatible sitemap of your WordPress blog which is supported by Ask.com, Google, MSN Search and YAHOO. <a href="options-general.php?page=sitemap.php">Configuration Page</a>
 Version: 3.1
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de/
*/

class GoogleSitemapGeneratorLoader {
	function Enable() {
		
		//Register the sitemap creator to wordpress...
		add_action('admin_menu', array('GoogleSitemapGeneratorLoader', 'RegisterAdminPage'));

		//Existing posts gets deleted
		add_action('delete_post', array('GoogleSitemapGeneratorLoader', 'CallCheckForAutoBuild'),9999,1);
			
		//Existing post gets published
		add_action('publish_post', array('GoogleSitemapGeneratorLoader', 'CallCheckForAutoBuild'),9999,1); 
			
		//WP Cron hook
		add_action('sm_build_cron', array('GoogleSitemapGeneratorLoader', 'CallBuildSitemap'),1,0);	
		
		if(!empty($_GET["sm_command"]) && !empty($_GET["sm_key"])) {
			GoogleSitemapGeneratorLoader::CallCheckForManualBuild();			
		}
	}

	function RegisterAdminPage() {
		
		if (function_exists('add_options_page')) {
			add_options_page(__('XML-Sitemap Generator','sitemap'), __('XML-Sitemap','sitemap'), 10, basename(__FILE__), array('GoogleSitemapGeneratorLoader','CallHtmlShowOptionsPage'));	
		}
	}
	
	function CallHtmlShowOptionsPage() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {	
			$gs = GoogleSitemapGenerator::GetInstance();
			$gs->HtmlShowOptionsPage();
		}
	}
	
	function CallCheckForAutoBuild($args) {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {	
			$gs = GoogleSitemapGenerator::GetInstance();
			$gs->CheckForAutoBuild($args);
		}
	}
	
	function CallBuildSitemap() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {	
			$gs = GoogleSitemapGenerator::GetInstance();
			$gs->BuildSitemap();
		}
	}
	
	function CallCheckForManualBuild() {
			if(GoogleSitemapGeneratorLoader::LoadPlugin()) {	
			$gs = GoogleSitemapGenerator::GetInstance();
			$gs->CheckForManualBuild();
		}	
	}
	
	function LoadPlugin() {
		
		if(!class_exists("GoogleSitemapGenerator")) {
			
			$path = trailingslashit(dirname(__FILE__));
			
			if(!file_exists( $path . 'sitemap-core.php')) return false;
			require_once($path. 'sitemap-core.php');
		} 

		GoogleSitemapGenerator::Enable();	
		return true;	
	}
	
	function GetBaseName() {
		return plugin_basename(__FILE__);	
	}
	
	function GetPluginFile() {
		return __FILE__;	
	}
	
	function GetVersion() {
		if(!function_exists('get_plugin_data')) {
			if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) require_once(ABSPATH . 'wp-admin/includes/plugin.php'); //2.3+
			else if(file_exists(ABSPATH . 'wp-admin/admin-functions.php')) require_once(ABSPATH . 'wp-admin/admin-functions.php'); //2.1
			else return "0.ERROR";
		}
		$data = get_plugin_data(__FILE__);	
		return $data['Version'];
	}
}

if(defined('ABSPATH') && defined('WPINC')) {
	add_action("init",array("GoogleSitemapGeneratorLoader","Enable"),1000,0);
}
?>
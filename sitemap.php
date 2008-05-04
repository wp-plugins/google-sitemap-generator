<?php

/*
 $Id: sitemap.php 36758 2008-03-30 00:14:52Z arnee $

 Info for WordPress:
 ==============================================================================
 Plugin Name: Google XML Sitemaps 
 Plugin URI: http://www.arnebrachhold.de/redir/sitemap-home/
 Description: This plugin will generate a sitemaps.org compatible sitemap of your WordPress blog which is supported by Ask.com, Google, MSN Search and YAHOO. <a href="options-general.php?page=sitemap.php">Configuration Page</a>
 Version: 3.1b1
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
			add_options_page(__('XML-Sitemap Generator','sitemap'), __('XML-Sitemap','sitemap'), 'administrator', basename(__FILE__), array('GoogleSitemapGeneratorLoader','CallHtmlShowOptionsPage'));	
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
}

if(defined('ABSPATH') && defined('WPINC')) {
	add_action("init",array("GoogleSitemapGeneratorLoader","Enable"),1000,0);
}
?>
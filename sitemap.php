<?php

/*
 $Id$

 Google XML Sitemaps Generator for WordPress
 ==============================================================================
 
 This generator will create a sitemaps.org compliant sitemap of your WordPress blog.

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
 Description: This plugin will generate a special XML sitemap which will help search engines like Google, Yahoo, Bing and Ask.com to better index your blog.
 Version: 4.0alpha5
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de/
 Text Domain: sitemap
 Domain Path: /lang/
 
*/

/**
 * Loader class for the Google Sitemap Generator
 *
 * This class takes care of the sitemap plugin and tries to load the different parts as late as possible.
 * On normal requests, only this small class is loaded. When the sitemap needs to be rebuild, the generator itself is loaded.
 * The last stage is the user interface which is loaded when the administration page is requested.
 *
 * @author Arne Brachhold
 * @package sitemap
 */
class GoogleSitemapGeneratorLoader {
	/**
	 * Enabled the sitemap plugin with registering all required hooks
	 *
	 * @uses add_action Adds actions for admin menu, executing pings and handling robots.txr
	 * @uses add_filter Adds filtes for admin menu icon and contexual help
	 * @uses GoogleSitemapGeneratorLoader::SetupRewriteHooks() Registeres the rewrite hooks
	 * @uses GoogleSitemapGeneratorLoader::CallShowPingResult() Shows the ping result on request
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite() Writes rewrite rules the first time
	 */
	function Enable() {
		
		//Check minimum WP requirements, which is 2.8 at the moment.
		global $wp_version;
		if(version_compare($wp_version,"2.8","<")) {
			add_action('admin_notices',  array('GoogleSitemapGeneratorLoader', 'AddVersionError'));
			return;
		}
		
		//Register the sitemap creator to wordpress...
		add_action('admin_menu', array('GoogleSitemapGeneratorLoader', 'RegisterAdminPage'));
		
		//Nice icon for Admin Menu (requires Ozh Admin Drop Down Plugin)
		add_filter('ozh_adminmenu_icon', array('GoogleSitemapGeneratorLoader', 'RegisterAdminIcon'));
				
		//Additional links on the plugin page
		add_filter('plugin_row_meta', array('GoogleSitemapGeneratorLoader', 'RegisterPluginLinks'),10,2);

		//Existing page was published
		add_action('do_pings', array('GoogleSitemapGeneratorLoader', 'CallSendPing'),9999,1);
		
		//Robots.txt request
		add_action('do_robots', array('GoogleSitemapGeneratorLoader', 'CallDoRobots'),100,0);
		
		//Help topics for context sensitive help
		add_filter('contextual_help_list', array('GoogleSitemapGeneratorLoader', 'CallHtmlShowHelpList'),9999,2);
	
		//Set up hooks for adding permalinks, query vars
		GoogleSitemapGeneratorLoader::SetupRewriteHooks();
		
		//Check if the result of a ping request should be shown
		if(!empty($_GET["sm_ping_service"])) {
			GoogleSitemapGeneratorLoader::CallShowPingResult();
		}
		
		//Fix rewrite rules if not already done on activation hook. This happens on network activation for example.
		if(get_option("sm_rewrite_done", null) != "v1") {
			GoogleSitemapGeneratorLoader::ActivateRewrite();
		}
	}
	
	/**
	 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
	 * @since 4.0
	 */
	function AddVersionError() {
		echo "<div id='sm-version-error' class='error fade'><p><strong>".__('Your WordPress version is too old for XML Sitemaps.','sitemap')."</strong><br /> ".sprintf(__('Unfortunately this release of Google XML Sitemaps requires at least WordPress 2.8. Update to the latest version of WordPress to use this plugin. Otherwise go to <a href="%1$s">active plugins</a> and deactivate the Google XML Sitemaps plugin to make this message disappear. You can download an older version of this plugin on the plugin website.','sitemap'), "plugins.php?plugin_status=active")."</p></div>";
	}
	
	/**
	 * Adds the filters for wp rewrite and query vars handling
	 *
	 * @since 4.0
	 * @uses add_filter()
	 */
	function SetupRewriteHooks() {
		add_filter('query_vars', array('GoogleSitemapGeneratorLoader', 'RegisterQueryVars'),1,1);
		
		add_filter('rewrite_rules_array', array('GoogleSitemapGeneratorLoader', 'AddRewriteRules'),1,1);
		
		add_filter('template_redirect', array('GoogleSitemapGeneratorLoader', 'DoTemplateRedirect'),1,0);
	}
	
	/**
	 * Register the plugin specific "xml_sitemap" query var
	 *
	 * @since 4.0
	 * @param $vars Array Array of existing query_vars
	 * @return Array An aarray containing the new query vars
	 */
	function RegisterQueryVars($vars) {
	    array_push($vars, 'xml_sitemap');
	    return $vars;
	}
	
	/**
	 * Registers the plugin specific rewrite rules
	 *
	 * @since 4.0
	 * @param $vars  Array Array of existing rewrite rules
	 * @return Array An aarray containing the new rewrite rules
	 */
	function AddRewriteRules($rules){
		$newrules = array();
		$newrules['sitemap-?([a-zA-Z0-9\-_]+)?\.xml$'] = 'index.php?xml_sitemap=params=$matches[1]';
		$newrules['sitemap-?([a-zA-Z0-9\-_]+)?\.xml\.gz$'] = 'index.php?xml_sitemap=params=$matches[1];zip=true';
		
		return $newrules + $rules;
	}
	
	/**
	 * Handles the plugin output on template redirection if the xml_sitemap query var is present.
	 *
	 * @since 4.0
	 * @global $wp_query  The WordPress query object
	 */
	function DoTemplateRedirect(){
		global $wp_query;
		if(!empty($wp_query->query_vars["xml_sitemap"])) {
			GoogleSitemapGeneratorLoader::CallShowSitemap($wp_query->query_vars["xml_sitemap"]);
		}
	}
	
	/**
	 * Handled the plugin activation on installation
	 *
	 * @uses GoogleSitemapGeneratorLoader::ActivateRewrite
	 * @since 4.0
	 */
	function ActivatePlugin() {
		GoogleSitemapGeneratorLoader::ActivateRewrite();
	}
	
	/**
	 * Sets up the rewrite rules and flushes them
	 *
	 * @since 4.0
	 * @global $wp_rewrite WP_Rewrite
	 * @uses WP_Rewrite::flush_rules()
	 * @uses GoogleSitemapGeneratorLoader::SetupRewriteHooks()
	 */
	function ActivateRewrite() {
		global $wp_rewrite;
		GoogleSitemapGeneratorLoader::SetupRewriteHooks();
		$wp_rewrite->flush_rules(false);
		update_option("sm_rewrite_done","v1");
	}
	
	/**
	 * Registers the plugin in the admin menu system
	 *
	 * @uses add_options_page()
	 */
	function RegisterAdminPage() {
		if (function_exists('add_options_page')) {
			add_options_page(__('XML-Sitemap Generator','sitemap'), __('XML-Sitemap','sitemap'), 'level_10', GoogleSitemapGeneratorLoader::GetBaseName(), array('GoogleSitemapGeneratorLoader','CallHtmlShowOptionsPage'));
		}
	}
	
	/**
	 * Returns a nice icon for the Ozh Admin Menu if the {@param $hook} equals to the sitemap plugin
	 *
	 * @param string $hook The hook to compare
	 * @return string The path to the icon
	 */
	function RegisterAdminIcon($hook) {
		if ( $hook == GoogleSitemapGeneratorLoader::GetBaseName() && function_exists('plugins_url')) {
			return plugins_url('img/icon-arne.gif',GoogleSitemapGeneratorLoader::GetBaseName());
		}
		return $hook;
	}
	
	/**
	 * Registers additional links for the sitemap plugin on the WP plugin configuration page
	 *
	 * Registers the links if the $file param equals to the sitemap plugin
	 * @param $links Array An array with the existing links
	 * @param $file string The file to compare to
	 */
	function RegisterPluginLinks($links, $file) {
		$base = GoogleSitemapGeneratorLoader::GetBaseName();
		if ($file == $base) {
			$links[] = '<a href="options-general.php?page=' . GoogleSitemapGeneratorLoader::GetBaseName() .'">' . __('Settings','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-faq/">' . __('FAQ','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-support/">' . __('Support','sitemap') . '</a>';
			$links[] = '<a href="http://www.arnebrachhold.de/redir/sitemap-plist-donate/">' . __('Donate','sitemap') . '</a>';
		}
		return $links;
	}
	
	/**
	 * Invokes the HtmlShowOptionsPage method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::HtmlShowOptionsPage()
	 */
	function CallHtmlShowOptionsPage() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			$gs = &GoogleSitemapGenerator::GetInstance();
			$gs->HtmlShowOptionsPage();
		}
	}
	
	/**
	 * Invokes the ShowPingResult method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowPingResult()
	 */
	function CallShowPingResult() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			$gs = &GoogleSitemapGenerator::GetInstance();
			$gs->ShowPingResult();
		}
	}
	
	/**
	 * Invokes the ShowPingResult method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::SendPing()
	 */
	function CallSendPing() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			$gs = &GoogleSitemapGenerator::GetInstance();
			$gs->SendPing();
		}
	}
	
	/**
	 * Invokes the ShowSitemap method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::ShowSitemap()
	 */
	function CallShowSitemap($options) {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			$gs = &GoogleSitemapGenerator::GetInstance();
			$gs->ShowSitemap($options);
		}
	}
	
	/**
	 * Invokes the DoRobots method of the generator
	 * @uses GoogleSitemapGeneratorLoader::LoadPlugin()
	 * @uses GoogleSitemapGenerator::DoRobots()
	 */
	function CallDoRobots() {
		if(GoogleSitemapGeneratorLoader::LoadPlugin()) {
			$gs = &GoogleSitemapGenerator::GetInstance();
			$gs->DoRobots();
		}
	}
	
	/**
	 * Displays the help links in the upper Help Section of WordPress
	 *
	 * @param $filterVal Array The existing links
	 * @param $screen Object The current screen object
	 * @return Array The new links
	 */
	function CallHtmlShowHelpList($filterVal,$screen) {

		$id = get_plugin_page_hookname(GoogleSitemapGeneratorLoader::GetBaseName(),'options-general.php');
		
		//WP 3.0 passes a screen object instead of a string
		if(is_object($screen)) $screen = $screen->id;
		
		if($screen == $id) {
			$links = array(
				__('Plugin Homepage','sitemap')=>'http://www.arnebrachhold.de/redir/sitemap-help-home/',
				__('My Sitemaps FAQ','sitemap')=>'http://www.arnebrachhold.de/redir/sitemap-help-faq/'
			);
			
			$filterVal[$id] = '';
			
			$i=0;
			foreach($links AS $text=>$url) {
				$filterVal[$id].='<a href="' . $url . '">' . $text . '</a>' . ($i < (count($links)-1)?' | ':'') ;
				$i++;
			}
		}
		return $filterVal;
	}
	

	/**
	 * Loads the actual generator class and tries to raise the memory and time limits if not already done by WP
	 *
	 * @uses GoogleSitemapGenerator::Enable()
	 * @return boolean true if run successfully
	 */
	function LoadPlugin() {
			
		if(!class_exists("GoogleSitemapGenerator")) {
			
			$path = trailingslashit(dirname(__FILE__));
			
			if(!file_exists( $path . 'sitemap-core.php')) return false;
			require_once($path. 'sitemap-core.php');
		}

		GoogleSitemapGenerator::Enable();
		return true;
	}
	
	/**
	 * Returns the plugin basename of the plugin (using __FILE__)
	 *
	 * @return string The plugin basename, "sitemap" for example
	 */
	function GetBaseName() {
		return plugin_basename(__FILE__);
	}
	
	/**
	 * Returns the name of this loader script, using __FILE__
	 *
	 * @return string The __FILE__ value of this loader script
	 */
	function GetPluginFile() {
		return __FILE__;
	}
	
	/**
	 * Returns the plugin version
	 *
	 * Uses the WP API to get the meta data from the top of this file (comment)
	 *
	 * @return string The version like 3.1.1
	 */
	function GetVersion() {
		if(!isset($GLOBALS["sm_version"])) {
			if(!function_exists('get_plugin_data')) {
				if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) require_once(ABSPATH . 'wp-admin/includes/plugin.php');
				else return "0.ERROR";
			}
			$data = get_plugin_data(__FILE__, false, false);
			$GLOBALS["sm_version"] = $data['Version'];
		}
		return $GLOBALS["sm_version"];
	}
}

//Enable the plugin for the init hook, but only if WP is loaded. Calling this php file directly will do nothing.
if(defined('ABSPATH') && defined('WPINC')) {
	add_action("init",array("GoogleSitemapGeneratorLoader","Enable"),1000,0);
	register_activation_hook(__FILE__, array('GoogleSitemapGeneratorLoader', 'ActivatePlugin'));
}


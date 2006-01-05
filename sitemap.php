<?php 
/*
 
 $Id$

 Google Sitemap Generator for WordPress
 ==============================================================================
 
 This generator will create a google compliant sitemap of your WordPress blog.
 Currently homepage, posts, static pages, categories and archives are supported.
 
 The priority of a post depends on its comments. You can choose the way the priority
 is calculated in the options screen.
 
 Feel free to visit my website under www.arnebrachhold.de or contact me at
 himself [at] arnebrachhold [dot] de
 
 Have fun! 
   Arne
   
   
 Installation:
 ==============================================================================
 1. Upload the full directory into your wp-content/plugins directory
 2. Make your blog directory writeable OR create two files called sitemap.xml 
    and sitemap.xml.gz and make them writeable via CHMOD In most cases, your blog directory is already writeable.
 2. Activate it in the Plugin options
 3. Edit or publish a post or click on Rebuild Sitemap on the Sitemap Administration Interface
 
 
 Info for WordPress:
 ==============================================================================
 Plugin Name: Google Sitemaps
 Plugin URI: http://www.arnebrachhold.de/redir/sitemap-home/
 Description: This generator will create a Google compliant sitemap of your WordPress blog. <a href="options-general.php?page=sitemap.php">Configuration Page</a>
 Version: 3.0b
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de/
 
 
 Contributors:
 ==============================================================================
 Basic Idea             Michael Nguyen      http://www.socialpatterns.com/
 SQL Improvements       Rodney Shupe        http://www.shupe.ca/
 Japanse Lang. File     Hirosama            http://hiromasa.zone.ne.jp/
 Spanish lang. File     César Gómez Martín  http://www.cesargomez.org/
 Italian lang. File     Stefano Aglietti    http://wordpress-it.it/
 Trad.Chinese  File     Kirin Lin           http://kirin-lin.idv.tw/
 Simpl.Chinese File     june6               http://www.june6.cn/
 Swedish Lang. File     Tobias Bergius      http://tobiasbergius.se/
 Ping Code Template 1   James               http://www.adlards.com/
 Ping Code Template 2   John                http://www.jonasblog.com/
 Bug Report             Brad                http://h3h.net/
 Bug Report             Christian Aust      http://publicvoidblog.de/
 
 Code, Documentation, Hosting and all other Stuff:
                        Arne Brachhold      http://www.arnebrachhold.de/
 
 Thanks to all contributors and bug reporters! :)
 
 
 Release History:
 ==============================================================================
 2005-06-05     1.0     First release
 2005-06-05     1.1     Added archive support
 2005-06-05     1.2     Added category support
 2005-06-05     2.0a    Beta: Real Plugin! Static file generation, Admin UI
 2005-06-05     2.0     Various fixes, more help, more comments, configurable filename
 2005-06-07     2.01    Fixed 2 Bugs: 147 is now _e(strval($i)); instead of _e($i); 344 uses a full < ?php instead of < ?
                        Thanks to Christian Aust for reporting this :)
 2005-06-07     2.1     Correct usage of last modification date for cats and archives  (thx to Rodney Shupe (http://www.shupe.ca/))
                        Added support for .gz generation
                        Fixed bug which ignored different post/page priorities
                        Should support now different wordpress/admin directories
 2005-06-07     2.11    Fixed bug with hardcoded table table names instead of the $wpd vars
 2005-06-07     2.12    Changed SQL Statement of the categories to get it work on MySQL 3 
 2005-06-08     2.2     Added language file support:
                        - Japanese Language Files and code modifications by hiromasa <webmaster [at] hiromasa [dot] zone [dot] ne [dot] jp> http://hiromasa.zone.ne.jp/
                        - German Language File by Arne Brachhold (http://www.arnebrachhold.de)
 2005-06-14     2.5     Added support for external pages
                        Added support for Google Ping
                        Added the minimum Post Priority option
                        Added Spanish Language File by César Gómez Martín (http://www.cesargomez.org/)
                        Added Italian Language File by Stefano Aglietti (http://wordpress-it.it/)
                        Added Traditional Chine Language File by Kirin Lin (http://kirin-lin.idv.tw/)
 2005-07-03     2.6     Added support to store the files at a custom location
                        Changed the home URL to have a slash at the end
                        Required admin-functions.php so the script will work with external calls, wp-mail for example
                        Added support for other plugins to add content to the sitemap via add_filter()
 2005-07-20     2.7     Fixed wrong date format in additional pages
                        Added Simplified Chinese Language Files by june6 (http://www.june6.cn/)
                        Added Swedish Language File by Tobias Bergius (http://tobiasbergius.se/)
 2005-09-04     3.0     Added different priority calculation modes and introduced an API to create custom ones
                        Added support to use the Popularity Contest plugin by Alex King to calculate post priority
                        Added Button to restore default configuration
                        Added several links to homepage and support
                        Added option to exclude password protected posts
                        Added function to start sitemap creation via GET and a secret key
                        Posts and pages marked for publish with a date in the future won't be included
                        Improved compatiblity with other plugins
                        Improved speed and optimized settings handling
                        Improved user-interface
                        Recoded plugin architecture which is now fully OOP


 Maybe Todo:
 ==============================================================================
 - Your wishes :)
 
 
 License:
 ==============================================================================
 Copyright 2005  ARNE BRACHHOLD  (email : himself [a|t] arnebrachhold [dot] de)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
 Developer Documentation
 ==============================================================================
 
 Adding other pages to the sitemap via other plugins
 
  This plugin uses the action system of WordPress to allow other plugins
  to add urls to the sitemap. Simply add your function with add_action to
  the list and the plugin will execute yours every time the sitemap is build.
  Use the AddUrl method to add your content.
  
  Sample:
  function your_pages($generatorObject) {
	$generatorObject->AddUrl("http://blog.uri/tags/hello/",time(),"daily",0.5);
  }
  add_action("sm_buildmap","your_pages");
  
  Parameters:
  - The URL to the page
  - The last modified data, as a UNIX timestamp (optional)
  - The Change Frequency (daily, hourly, weekly and so on) (optional)
  - The priority 0.0 to 1.0 (optional)
 
 ===============================================
 
 Adding additional PriorityProviders
 
  This plugin uses several classes to calculate the post priority.
  You can register your own provider and choose it at the options screen.
  
  Your class has to extend the GoogleSitemapGeneratorPrioProviderBase class
  which has a default constructor and a method called GetPostPriority
  which you can override.
  
  Look at the GoogleSitemapGeneratorPrioByPopularityContestProvider class
  for an example.
  
  To register your provider to the sitemap generator, use the following filter:
  
  add_filter("sm_add_prio_provider","AddMyProvider");
  
  Your function could look like this:
  
  function AddMyProvider($providers) {
	array_push($providers,"MyProviderClass");
	return $providers;
  }
  
  Note that you have to return the modified list!  
   
 ===============================================  
    
 About the pages storage:
 
  Every external page is represented in a instance of the GoogleSitemapGeneratorPage class.
  I use an array to store them in the WordPress options table. Note
  that if you want to serialize a class, it must be available BEFORE you
  call unserialize(). So it's very important to set the autoload property
  of the option to false.
  
 =============================================== 
  
 About the pages editor:
 
  To store modifications to the pages without using session variables,
  i restore the state of the modifications in hidden fields. Based on
  these, the array with the pages from the database gets overwritten.
  It's very important that you call the sm_apply_pages function on 
  every request if modifications to the pages should be saved. If
  you dont't all changes will be lost. (So works the "Reset Changes" button)
  
 =============================================== 
 
 Misc:
  
 All other methods are commented with phpDoc style.
 The "#region" tags and "#type $example example_class" comments are helpers which 
 may be used by your editor.  #region gives the ability to create custom code 
 folding areas, #type are type definitions for auto-complete.
*/

//Enable for dev! Good code doesn't generate any notices...
//error_reporting(E_ALL);
//ini_set("display_errors",1);

/**
 * Represents entry in the logfile
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */
class GoogleSitemapGeneratorLogEntry {
	/**
	 * @var int $_level The level of this log entry. 0 = notice, 1 = warning, 2 = error
	 * @access private
	 */
	var $_level;
	
	/**
	 * @var int $_timestamp The UNIX timestamp when this error/warning/notice happened
	 * @access private
	 */
	var $_timestamp;
	
	/**
	 * @var string $_msg The log message
	 * @access private
	 */
	var $_msg;
	
	/**
	 * Initializes a new log entry
	 * 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param string $msg The message
	 * @param int $timestamp The UNIX timestamp when the notice/error/warning happened
	 * @param int $level The level of this log entry. 0 = notice, 1 = warning, 2 = error
	 */
	function GoogleSitemapGeneratorLogEntry($msg,$timestamp=0,$level=0) {
		$this->_msg=$msg;
		$this->_timestamp = (is_int($timestamp) && $timestamp>0?$timestamp:time());
		$this->_level = ($level>=0 && $level<=2?$level:0);
	}
	
	/**
	 * Returns the level of this log entry. 0 = notice, 1 = warning, 2 = error
	 *
	 * @return string The level 
	 */
	function GetLevel() {
		return $this->_level;	
	}
	
	/**
	 * Returns the message of this log entry. 
	 *
	 * @return string The message 
	 */
	function GetMessage() {
		return $this->_msg;	
	}
	
	/**
	 * Returns the UNIX timestamp of this log entry. 
	 *
	 * @return string The UNIX timestamp 
	 */
	function GetTimestamp() {
		return $this->_timestamp;	
	}
	
	/**
	 * Returns the color of this log entry. notice -> Green, warning -> orange, error -> red 
	 *
	 * @return string The color 
	 */
	function GetColor() {
		$color=null;
		switch($this->_level) {
			case 0:
				$color = "green";
				break;
			case 1:
				$color = "orange";
				break;
			case 2:
				$color = "red";
				break;
		}	
		
		return $color;
	}
	
	/**
	 * Returns the log entry as a colored, formatted list element (li)
	 *
	 * @return string The log entry
	 */
	function GetHTML() {
		return "<li style=\"color:" . $this->GetColor() . "\">" . date(get_option("date_format") . " " . get_option("time_format") ,$this->_timestamp) . ": " . $this->_msg . "</li>";		
	}
}

/**
 * Represents an item in the page list
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */
class GoogleSitemapGeneratorPage {
	
	/**
	 * @var string $_url Sets the URL or the relative path to the blog dir of the page
	 * @access private
	 */
	var $_url;
	
	/**
	 * @var float $_priority Sets the priority of this page
	 * @access private
	 */
	var $_priority;
	
	/**
	 * @var string $_changeFreq Sets the chanfe frequency of the page. I want Enums!
	 * @access private
	 */
	var $_changeFreq;
	
	/**
	 * @var int $_lastMod Sets the lastMod date as a UNIX timestamp. 
	 * @access private
	 */
	var $_lastMod;	
	
	/**
	 * Initialize a new page object
	 * 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param bool $enabled Should this page be included in thesitemap
	 * @param string $url The URL or path of the file
	 * @param float $priority The Priority of the page 0.0 to 1.0
	 * @param string $changeFreq The change frequency like daily, hourly, weekly
	 * @param int $lastMod The last mod date as a unix timestamp
	 */
	function GoogleSitemapGeneratorPage($url="",$priority=0.0,$changeFreq="never",$lastMod=0) {
		$this->SetUrl($url);
		$this->SetProprity($priority);
		$this->SetChangeFreq($changeFreq);
		$this->SetLastMod($lastMod);
	}
	
	/**
	 * Returns the URL of the page
	 *
	 * @return string The URL
	 */
	function GetUrl() {
		return $this->_url;	
	}
	
	/**
	 * Sets the URL of the page
	 *
	 * @param string $url The new URL
	 */
	function SetUrl($url) {
		$this->_url=(string) $url;				
	}
	
	/**
	 * Returns the priority of this page
	 *
	 * @return float the priority, from 0.0 to 1.0
	 */
	function GetPriority() {
		return $this->_priority;		
	}
	
	/**
	 * Sets the priority of the page
	 *
	 * @param float $priority The new priority from 0.1 to 1.0
	 */
	function SetProprity($priority) {
		$this->_priority=floatval($priority);	
	}
	
	/**
	 * Returns the change frequency of the page
	 *
	 * @return string The change frequncy like hourly, weekly, monthly etc.
	 */
	function GetChangeFreq() {
		return $this->_changeFreq;		
	}
	
	/**
	 * Sets the change frequency of the page
	 *
	 * @param string $changeFreq The new change frequency
	 */
	function SetChangeFreq($changeFreq) {
		$this->_changeFreq=(string) $changeFreq;	
	}
	
	/**
	 * Returns the last mod of the page
	 *
	 * @return int The lastmod value in seconds
	 */
	function GetLastMod() {
		return $this->_lastMod;	
	}
	
	/**
	 * Sets the last mod of the page
	 *
	 * @param int $lastMod The lastmod of the page
	 */
	function SetLastMod($lastMod) {
		$this->_lastMod=intval($lastMod);
	}	
	
	function Render() {
		
		$s="";
		$s.= "\t<url>\n";
		$s.= "\t\t<loc>" . $this->_url . "</loc>\n";
		if($this->_lastMod>0) $s.= "\t\t<lastmod>" . date('Y-m-d\TH:i:s+00:00',$this->_lastMod) . "</lastmod>\n";
		if(!empty($this->_changeFreq)) $s.= "\t\t<changefreq>" . $this->_changeFreq . "</changefreq>\n";	
		if($this->_priority!==false && $this->_priority!=="") $s.= "\t\t<priority>" . $this->_priority . "</priority>\n";
		$s.= "\t</url>\n";	
		return $s;
	}							
}

class GoogleSitemapGeneratorXmlEntry {
	
	var $_xml;
	
	function GoogleSitemapGeneratorXmlEntry($xml) {
		$this->_xml = $xml;	
	}
	
	function Render() {
		return $this->_xml;	
	}			
}

class GoogleSitemapGeneratorDebugEntry extends GoogleSitemapGeneratorXmlEntry {
		
	function Render() {
		return "<!-- " . $this->_xml . " -->";	
	}			
}

/**
 * Base class for all priority providers
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */		
class GoogleSitemapGeneratorPrioProviderBase {
	
	/**
	 * @var int $_totalComments The total number of comments of all posts
	 * @access protected
	 */
	var $_totalComments=0;
	
	/**
	 * @var int $_totalComments The total number of posts
	 * @access protected
	 */
	var $_totalPosts=0;
	
	/**
	 * Returns the (translated) name of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated name
	*/
	function GetName() {
		return "";
	}
	
	/**
	 * Returns the (translated) description of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated description
	*/
	function GetDescription() {
		return "";	
	}
	
	/**
	 * Initializes a new priority provider
	 *
	 * @param $totalComments int The total number of comments of all posts
	 * @param $totalPosts int The total number of posts 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GoogleSitemapGeneratorPrioProviderBase($totalComments,$totalPosts) {
		$this->_totalComments=$totalComments;
		$this->_totalPosts=$totalPosts;
		
	}	
	
	/**
	 * Returns the priority for a specified post
	 *
	 * @param $postID int The ID of the post
	 * @param $commentCount int The number of comments for this post
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return int The calculated priority
	*/
	function GetPostPriority($postID,$commentCount) {
		return 0;
	}	
}

/**
 * Priority Provider which calculates the priority based on the number of comments
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */		
class GoogleSitemapGeneratorPrioByCountProvider extends GoogleSitemapGeneratorPrioProviderBase {
	
	/**
	 * Returns the (translated) name of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated name
	*/
	function GetName() {
		return __("Comment Count",'sitemap');
	}
	
	/**
	 * Returns the (translated) description of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated description
	*/
	function GetDescription() {
		return __("Uses the number of comments of the post to calculate the priority",'sitemap');	
	}
	
	/**
	 * Initializes a new priority provider which calculates the post priority based on the number of comments
	 *
	 * @param $totalComments int The total number of comments of all posts
	 * @param $totalPosts int The total number of posts 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GoogleSitemapGeneratorPrioByCountProvider($totalComments,$totalPosts) {
		parent::GoogleSitemapGeneratorPrioProviderBase($totalComments,$totalPosts);	
	}
	
	/**
	 * Returns the priority for a specified post
	 *
	 * @param $postID int The ID of the post
	 * @param $commentCount int The number of comments for this post
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return int The calculated priority
	*/
	function GetPostPriority($postID,$commentCount) {
		$prio=0;
		if($this->_totalComments>0 && $commentCount>0) {
			$prio = round(($commentCount*100/$this->_totalComments)/100,1);				
		} else {
			$prio = 0;	
		}
		return $prio;
	}			
}

/**
 * Priority Provider which calculates the priority based on the average number of comments
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */	
class GoogleSitemapGeneratorPrioByAverageProvider extends GoogleSitemapGeneratorPrioProviderBase {
	
	/**
	 * @var int $_average The average number of comments per post
	 * @access protected
	 */
	var $_average=0.0;
	
	/**
	 * Returns the (translated) name of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated name
	*/
	function GetName() {
		return __("Comment Average",'sitemap');
	}
	
	/**
	 * Returns the (translated) description of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated description
	*/
	function GetDescription() {
		return __("Uses the average comment count to calculate the priority",'sitemap');	
	}
	
	/**
	 * Initializes a new priority provider which calculates the post priority based on the average number of comments
	 *
	 * @param $totalComments int The total number of comments of all posts
	 * @param $totalPosts int The total number of posts 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GoogleSitemapGeneratorPrioByAverageProvider($totalComments,$totalPosts) {
		parent::GoogleSitemapGeneratorPrioProviderBase($totalComments,$totalPosts);
		
		if($this->_totalComments>0 && $this->_totalPosts>0) {
			$this->_average= (double) $this->_totalComments / $this->_totalPosts;
		}
	}
	
	/**
	 * Returns the priority for a specified post
	 *
	 * @param $postID int The ID of the post
	 * @param $commentCount int The number of comments for this post
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return int The calculated priority
	*/
	function GetPostPriority($postID,$commentCount) {
		$prio = 0;
		//Do not divide by zero!
		if($this->_average==0) {
			if($commentCount>0)	$prio = 1;		
			else $prio = 0;
		} else {
			$prio = $commentCount/$this->_average;	
			if($prio>1) $prio = 1;
			else if($prio<0) $prio = 0;
		}

		return $prio;
	}
} 

/**
 * Priority Provider which calculates the priority based on the popularity by the PopularityContest Plugin
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @package sitemap
 * @since 3.0
 */	
class GoogleSitemapGeneratorPrioByPopularityContestProvider extends GoogleSitemapGeneratorPrioProviderBase {
	
	/**
	 * Returns the (translated) name of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated name
	*/
	function GetName() {
		return __("Popularity Contest",'sitemap');	
	}
	
	/**
	 * Returns the (translated) description of this priority provider
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The translated description
	*/
	function GetDescription() {
		return str_replace("%4","index.php?page=popularity-contest.php",str_replace("%3","options-general.php?page=popularity-contest.php",str_replace("%2","http://www.alexking.org/",str_replace("%1","http://www.alexking.org/index.php?content=software/wordpress/content.php",__("Uses the activated <a href=\"%1\">Popularity Contest Plugin</a> from <a href=\"%2\">Alex King</a>. See <a href=\"%3\">Settings</a> and <a href=\"%4\">Most Popular Posts</a>",'sitemap')))));
	}
	
	/**
	 * Initializes a new priority provider which calculates the post priority based on the popularity by the PopularityContest Plugin
	 *
	 * @param $totalComments int The total number of comments of all posts
	 * @param $totalPosts int The total number of posts 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GoogleSitemapGeneratorPrioByPopularityContestProvider($totalComments,$totalPosts) {
		parent::GoogleSitemapGeneratorPrioProviderBase($totalComments,$totalPosts);
	}
	
	/**
	 * Returns the priority for a specified post
	 *
	 * @param $postID int The ID of the post
	 * @param $commentCount int The number of comments for this post
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return int The calculated priority
	*/
	function GetPostPriority($postID,$commentCount) {
		//$akpc is the global instance of the Popularity Contest Plugin
		global $akpc;
		
		$res=0;
		//Better check if its there
		if(!empty($akpc) && is_object($akpc)) {
			//Is the method we rely on available?
			if(method_exists($akpc,"get_post_rank")) {
				//popresult comes as a percent value
				$popresult=$akpc->get_post_rank($postID);
				if(!empty($popresult) && strpos($popresult,"%")!==false) {
					//We need to parse it to get the priority as an int (percent)
					$matches=null;
					preg_match("/([0-9]{1,3})\%/si",$popresult,$matches);
					if(!empty($matches) && is_array($matches) && count($matches)==2) {
						//Divide it so 100% = 1, 10% = 0.1
						$res=round(intval($matches[1])/100,1);							
					}
				}
			}
		}
		return $res;
	}	
}	

/**
 * Class to generate a Google Sitemaps compliant sitemap of a WordPress blog.
 * 
 * @package sitemap
 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
 * @since 3.0
*/
class GoogleSitemapGenerator {
	
	/**
	 * @var Version of the generator
	*/
	var $_version = "3.0b";
	
	/**
	 * @var string The full path to the blog directory
	 */
	var $_homePath = "";
	
	/**
	 * @var array The unserialized array with the stored options
	 */
	var $_options = array();
	
	/**
	 * @var array The saved additional pages
	 */
	var $_pages = array();
	
	/**
	 * @var array A list of available freuency names
	 */
	var $_freqNames = array();
	
	/**
	 * @var array A list of class names which my be called for priority calculation
	 */
	var $_prioProviders = array();
	
	/**
	 * @var bool True if init complete (options loaded etc)
	 */
	var $_initiated = false;
	
	/**
	 * @var string Messages for the user
	 */	
	var $_log = null;
	
	/**
	 * @var string Holds the last error if one occurs when writing the files
	 */	
	var $_lastError=null;
	
	/**
	 * @var array Contains the elements of the sitemap
	 */	
	var $_content = array();
	
	/**
	 * Returns the path to the blog directory
	 * 
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return string The full path to the blog directory
	*/
	function GetHomePath() {
		$res="";
		//Check if we are in the admin area -> get_home_path() is avaiable
		if(function_exists("get_home_path")) {
			$res = get_home_path();	
		} else {
			//get_home_path() is not available, but we can't include the admin
			//libraries because many plugins check for the "check_admin_referer"
			//function to detect if you are on an admin page. So we have to copy
			//the get_home_path function in our own...
			$home = get_settings('home');
			$home_path="";
			if ( $home != '' && $home != get_settings('siteurl') ) {
				$home_path = parse_url($home);
				$home_path = $home_path['path'];
				$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"]);
				$home_path = trailingslashit($root . $home_path);
			} else {
				$home_path = ABSPATH;
			}
			$res = $home_path;
		}
		return $res;
	}	
	
	/**
	 * Sets up the default configuration
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function InitOptions() {
		
		$this->_options=array();
		$this->_options["sm_b_prio_provider"]="GoogleSitemapGeneratorPrioByCountProvider";			//Provider for automatic priority calculation
		$this->_options["sm_b_filename"]="sitemap.xml";		//Name of the Sitemap file
		$this->_options["sm_b_debug"]=false;				//Write debug messages in the xml file
		$this->_options["sm_b_xml"]=true;					//Create a .xml file
		$this->_options["sm_b_gzip"]=true;					//Create a gzipped .xml file(.gz) file
		$this->_options["sm_b_ping"]=true;					//Auto ping Google
		$this->_options["sm_b_manual_enabled"]=false;		//Allow manual creation of the sitemap via GET request
		$this->_options["sm_b_auto_enabled"]=true;			//Rebuild sitemap when content is changed
		$this->_options["sm_b_manual_key"]=md5(microtime());//The secret key to build the sitemap via GET request

		$this->_options["sm_b_location_mode"]="auto";		//Mode of location, auto or manual
		$this->_options["sm_b_filename_manual"]="";			//Manuel filename
		$this->_options["sm_b_fileurl_manual"]="";			//Manuel fileurl

		$this->_options["sm_in_home"]=true;					//Include homepage
		$this->_options["sm_in_posts"]=true;				//Include posts
		$this->_options["sm_in_prot_posts"]=false;			//Include protected posts
		$this->_options["sm_in_pages"]=true;				//Include static pages
		$this->_options["sm_in_cats"]=true;					//Include categories
		$this->_options["sm_in_arch"]=true;					//Include archives

		$this->_options["sm_cf_home"]="daily";				//Change frequency of the homepage
		$this->_options["sm_cf_posts"]="monthly";			//Change frequency of posts
		$this->_options["sm_cf_pages"]="weekly";			//Change frequency of static pages
		$this->_options["sm_cf_cats"]="weekly";				//Change frequency of categories
		$this->_options["sm_cf_arch_curr"]="daily";			//Change frequency of the current archive (this month)
		$this->_options["sm_cf_arch_old"]="yearly";			//Change frequency of older archives

		$this->_options["sm_pr_home"]=1.0;					//Priority of the homepage
		$this->_options["sm_pr_posts"]=0.7;					//Priority of posts (if auto prio is disabled)
		$this->_options["sm_pr_posts_min"]=0.1;				//Minimum Priority of posts, even if autocalc is enabled
		$this->_options["sm_pr_pages"]=0.6;					//Priority of static pages
		$this->_options["sm_pr_cats"]=0.5;					//Priority of categories
		$this->_options["sm_pr_arch"]=0.5;					//Priority of archives	
	}
	
	/**
	 * Loads the configuration from the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function LoadOptions() {
		
		$this->InitOptions();
		
		//First init default values, then overwrite it with stored values so we can add default
		//values with an update which get stored by the next edit.
		$storedoptions=get_option("sm_options");
		if($storedoptions && is_array($storedoptions)) {
			foreach($storedoptions AS $k=>$v) {
				$this->_options[$k]=$v;	
			}
		} else update_option("sm_options",$this->_options); //First time use, store default values
	}
	
	/**
	 * Initializes a new Google Sitemap Generator
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GoogleSitemapGenerator() {
		
		$this->_freqNames = array("always", "hourly", "daily", "weekly", "monthly", "yearly","never");
		$this->_prioProviders = array();
		$this->_homePath = $this->GetHomePath();
	}
	
	/**
	 * Returns the version of the generator
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return int The version
	*/
	function GetVersion() {
		return $this->_version;
	}
	
	/**
	 * Returns all parent classes of a class
	 *
	 * @param $className string The name of the class
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return array An array which contains the names of the parent classes
	*/
	function GetParentClasses($classname) {
		$parent = get_parent_class($classname);
		$parents = array();
		if (!empty($parent)) {
			$parents = $this->GetParentClasses($parent);
			$parents[] = strtolower($parent);
		}
		return $parents;
	}
	
	/**
	 * Returns if a class is a subclass of another class
	 *
	 * @param $className string The name of the class
	 * @param $$parentName string The name of the parent class
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return bool true if the given class is a subclass of the other one
	*/
	function IsSubclassOf($className, $parentName) {
		
		$className = strtolower($className);
		$parentName = strtolower($parentName);
		
		if(empty($className) || empty($parentName) || !class_exists($className) || !class_exists($parentName)) return false;
		
		$parents=$this->GetParentClasses($className);
		
		return in_array($parentName,$parents);	
	}
	
	/**
	 * Adds a message to the logfile
	 *
	 * @param $msg string The message to add
	 * @param $level int The message level
	 * @param $save bool Save the messages back to the db
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return GoogleSitemapGeneratorLogEntry The new log entry as an object
	*/
	function &AddLog($msg,$level=0,$save=true) {
		
		$this->GetLog();
		
		$e = $this->_log;
		$x=array();
		
		$x[] = new GoogleSitemapGeneratorLogEntry($msg,time(),$level);
		for($i=0; $i<count($e); $i++) {
			$x[]=$e[$i];	
		}
		
		$this->_log = $x;
		
		if($save) $this->SaveLog();
		
		return $x[0];
	}
	
	/**
	 * Retrieves the logs from the database and adds the option field if it doesn't exist
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function GetLog() {
		if($this->_log === null) {
			$this->_log = get_option("sm_log");			
		}
		if(!is_array($this->_log) || $this->_log===null) {
			$this->_log=array();	
			add_option("sm_log",$this->_log,"Logfile of the Google Sitemap Generator",false);
		}
		return $this->_log;
	}
	
	/**
	 * Saves the log back to the db
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function SaveLog() {
		update_option("sm_log",$this->_log);	
	}
	
	/**
	 * Clears the logfile
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function ClearLog() {
		$this->_log = array();
		$this->SaveLog();				
	}
	
	/**
	 * Loads up the configuration and validates the prioity providers
	 *
	 * This method is only called if the sitemaps needs to be build or the admin page is displayed.
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function Initate() {
		if(!$this->_initiated) {
			
			//Loading language file...
			//load_plugin_textdomain('sitemap');
			//Hmm, doesn't work if the plugin file has its own directory.
			//Let's make it our way... load_plugin_textdomain() searches only in the wp-content/plugins dir.
			$currentLocale = get_locale();
			if(!empty($currentLocale)) {
				$moFile = dirname(__FILE__) . "/sitemap-" . $currentLocale . ".mo";
				if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('sitemap', $moFile);
			}
			
			$this->LoadOptions();
			$this->LoadPages();
				
			add_filter("sm_add_prio_provider",array(&$this, 'AddDefaultPrioProviders'));
				
			$r = apply_filters("sm_add_prio_provider",$this->_prioProviders);
			
			if($r != null) $this->_prioProviders = $r;		
				
			$this->ValidatePrioProviders();
			
			$this->_initiated = true;
		}
	}
	
	/**
	 * Enables the Google Sitemap Generator and registers the WordPress hooks
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function Enable() {
		
		if(!isset($GLOBALS["sm_instance"])) {			

			$GLOBALS["sm_instance"]=new GoogleSitemapGenerator();

			//Register the sitemap creator to wordpress...
			add_action('admin_menu', array(&$GLOBALS["sm_instance"], 'RegisterAdminPage'));
			
			//Manual Hook via GET
			add_action('init', array(&$GLOBALS["sm_instance"], 'CheckForManualBuild'));

			//Register to various events... @WordPress Dev Team: I wish me a 'public_content_changed' action :)
			
			//If a new post gets saved
			add_action('save_post', array(&$GLOBALS["sm_instance"], 'CheckForAutoBuild'));

			//Existing post gets edited
			add_action('edit_post', array(&$GLOBALS["sm_instance"], 'CheckForAutoBuild')); 

			//Existing posts gets deleted
			add_action('delete_post', array(&$GLOBALS["sm_instance"], 'CheckForAutoBuild'));
		}
	}
	
	/**
	 * Checks if sitemap building after content changed is enabled and rebuild the sitemap
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function CheckForAutoBuild() {
		$this->Initate();
		if($this->GetOption("b_auto_enabled")===true) {
			$this->BuildSitemap();	
		}
	}
	
	/**
	 * Checks if the rebuild request was send and starts to rebuilt the sitemap
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function CheckForManualBuild() {
		if(!empty($_GET["sm_command"]) && !empty($_GET["sm_key"])) {
			$this->Initate();
			if($this->GetOption("b_manual_enabled")===true && $_GET["sm_command"]=="build" && $_GET["sm_key"]==$this->GetOption("b_manual_key")) {
				$this->BuildSitemap();	
				exit;
			}	
		}
	}
	
	/**
	 * Validates all given Priority Providers by checking them for required methods and existence
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function ValidatePrioProviders() {
		$validProviders=array();
		
		for($i=0; $i<count($this->_prioProviders); $i++) {
			if(class_exists($this->_prioProviders[$i])) {
				if($this->IsSubclassOf($this->_prioProviders[$i],"GoogleSitemapGeneratorPrioProviderBase")) {
					array_push($validProviders,$this->_prioProviders[$i]);
				}
			}
		}
		$this->_prioProviders=$validProviders;
		
		if(!$this->GetOption("b_prio_provider")) {
			if(!in_array($this->GetOption("b_prio_provider"),$this->_prioProviders,true)) {
				$this->SetOption("b_prio_provider","");	
			}
		}
	}

	/**
	 * Adds the default Priority Providers to the provider list
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function AddDefaultPrioProviders($providers) {
		array_push($providers,"GoogleSitemapGeneratorPrioByCountProvider");
		array_push($providers,"GoogleSitemapGeneratorPrioByAverageProvider");
		if(class_exists("ak_popularity_contest")) {
			array_push($providers,"GoogleSitemapGeneratorPrioByPopularityContestProvider");	
		}
		return $providers;	
	}
	
	/**
	 * Loads the stored pages from the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	*/
	function LoadPages() {
		global $wpdb;
		
		$needsUpdate=false;
		
		$pagesString=$wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'sm_cpages'");
		
		//Class sm_page was renamed with 3.0 -> rename it in serialized value for compatibility
		if(!empty($pagesString) && strpos($pagesString,"sm_page")!==false) {
			$pagesString = str_replace("O:7:\"sm_page\"","O:26:\"GoogleSitemapGeneratorPage\"",$pagesString);
			$needsUpdate=true;
		}
		
		if(!empty($pagesString)) {
			$storedpages=unserialize($pagesString);
			$this->_pages=$storedpages;
		} else {
			$this->_pages=array();
			//Add the option, Note the autoload=false because when the autoload happens, our class GoogleSitemapGeneratorPage doesn't exist
			add_option("sm_cpages",$this->_pages,"Storage for custom pages of the sitemap plugin",false);	
		}	
		
		if($needsUpdate) {
			$this->SavePages();
		}
	}
	
	/**
	 * Saved the additional pages back to the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return true on success
	*/
	function SavePages() {
		return update_option("sm_cpages",$this->_pages);
	}
	
	
	/**
	 * Returns the URL for the sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The URL to the Sitemap file
	*/
	function GetXmlUrl($forceAuto=false) {
		
		if(!$forceAuto && $this->GetOption("b_location_mode")=="manual") {
			return $this->GetOption("b_fileurl_manual");
		} else {
			return trailingslashit(get_bloginfo('siteurl')). $this->GetOption("b_filename");
		}
	}

	/**
	 * Returns the URL for the gzipped sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The URL to the gzipped Sitemap file
	*/
	function GetZipUrl($forceAuto=false) {
		return $this->GetXmlUrl($forceAuto) . ".gz";	
	}
	
	/**
	 * Returns the file system path to the sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The file system path;
	*/
	function GetXmlPath($forceAuto=false) {		
		if(!$forceAuto && $this->GetOption("b_location_mode")=="manual") {
			return $this->GetOption("b_filename_manual");		
		} else {
			return $this->GetHomePath()  . $this->GetOption("b_filename");
		}
	}
	
	/**
	 * Returns the file system path to the gzipped sitemap file
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param bool $forceAuto Force the return value to the autodetected value.
	 * @return The file system path;
	*/
	function GetZipPath($forceAuto=false) {
		return $this->GetXmlPath($forceAuto) . ".gz";	
	}
	
	/**
	 * Returns the option value for the given key
	 * Alias for getOption
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $key string The Configuration Key
	 * @return mixed The value
	*/
	function Go($key) {
		return $this->getOption($key);
	}

	/**
	 * Returns the option value for the given key
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $key string The Configuration Key
	 * @return mixed The value
	 */
	function GetOption($key) {
		if(strpos($key,"sm_")!==0) $key="sm_" . $key;
		if(array_key_exists($key,$this->_options)) {
			return $this->_options[$key];	
		} else return null;
	}
	
	/**
	 * Sets an option to a new value
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $key string The configuration key
	 * @param $value mixed The new object
	 */
	function SetOption($key,$value) {
		if(strstr($key,"sm_")!=0) $key="sm_" . $key;
		
		$this->_options[$key]=$value;	
	}
	
	/**
	 * Saves the options back to the database
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return bool true on success
	 */
	function SaveOptions() {
		return update_option("sm_options",$this->_options);		
	}
	
	/**
	 * Retrieves the number of comments of a post in a asso. array
	 * The key is the postID, the value the number of comments
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return array An array with postIDs and their comment count
	 */
	function GetComments() {
		global $wpdb;
		$comments=array();

		//Query comments and add them into the array
		$commentRes=$wpdb->get_results("SELECT `comment_post_ID` as `post_id`, COUNT(comment_ID) as `comment_count` FROM `" . $wpdb->comments . "` WHERE `comment_approved`='1' GROUP BY `comment_post_ID`");
		if($commentRes) {
			foreach($commentRes as $comment) {
				$comments[$comment->post_id]=$comment->comment_count;
			}	
		}
		return $comments;
	}
	
	/**
	 * Calculates the full number of comments from an sm_getComments() generated array
	 * 
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>	
	 * @param $comments array The Array with posts and c0mment count
	 * @see sm_getComments
	 * @return The full number of comments
	 */ 
	function GetCommentCount($comments) {
		$commentCount=0;
		foreach($comments AS $k=>$v) {
			$commentCount+=$v;	
		}	
		return $commentCount;
	}	
	
	/**
	 * Removes an element of an array and reorders the indexes
	 * 
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param array $array The array with the values
	 * @param object $indice The key which vallue should be removed
	 * @return array The modified array
	 */
	function ArrayRemove ($array, $indice) {
		if (array_key_exists($indice, $array)) {
			$temp = $array[0];
			$array[0] = $array[$indice];
			$array[$indice] = $temp;
			array_shift($array);

			for ($i = 0 ; $i < $indice ; $i++)
			{
				$dummy = $array[$i];
				$array[$i] = $temp;
				$temp = $dummy;
			}
		}
		return $array;
	} 
	
	/**
	 * Adds a url to the sitemap. You can use this method or call AddElement directly.
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>  
	 * @param $loc string The location (url) of the page
	 * @param $lastMod int The last Modification time as a UNIX timestamp
	 * @param $changeFreq string The change frequenty of the page, Valid values are "always", "hourly", "daily", "weekly", "monthly", "yearly" and "never".
	 * @param $priorty float The priority of the page, between 0.0 and 1.0
	 * @see AddElement
	 * @return string The URL node
	 */
	function AddUrl($loc,$lastMod=0,$changeFreq="monthly",$priority=0.5) {
		$page = new GoogleSitemapGeneratorPage($loc,$priority,$changeFreq,$lastMod);
		
		$this->AddElement($page);
	}
	
	function AddElement($page) {
		if(empty($page)) return;
		
		$this->_content[] = $page;
	}
	
	/**
	 * Builds the sitemap and writes it into a xml file.
	 * 
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return array An array with messages such as failed writes etc.
	 */
	function BuildSitemap() {
		
		$this->Initate();
		
		global $wpdb;
		
		//$this->AddElement(new GoogleSitemapGeneratorXmlEntry());
		
		//Return messages to the user in frontend
		$messages=array();
		
		//Debug mode?
		$debug=$this->GetOption("b_debug");
		
		//Content of the XML file
		$this->AddElement(new GoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
		
		//WordPress powered... and me! :D
		$this->AddElement(new GoogleSitemapGeneratorDebugEntry("generator=\"wordpress/" . get_bloginfo('version') . "\""));
		$this->AddElement(new GoogleSitemapGeneratorDebugEntry("sitemap-generator-url=\"http://www.arnebrachhold.de\" sitemap-generator-version=\"" . $this->GetVersion() . "\""));
		$this->AddElement(new GoogleSitemapGeneratorDebugEntry("generated-on=\"" . date(get_option("date_format") . " " . get_option("time_format")) . "\""));
		
		//All comments as an asso. Array (postID=>commentCount)
		$comments=($this->GetOption("b_prio_provider")!=""?$this->GetComments():array());
		
		//Full number of comments
		$commentCount=(count($comments)>0?$this->GetCommentCount($comments):0);
		
		if($debug && $this->GetOption("b_prio_provider")!="") {
			$this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Total comment count: " . $commentCount));	
		}
		
		//Go XML!
		$this->AddElement(new GoogleSitemapGeneratorXmlEntry('<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'));
		
		//Add the home page (WITH a slash!)
		if($this->GetOption("in_home")) {
			$this->AddUrl(trailingslashit(get_bloginfo('url')),$this->GetTimestampFromMySql(get_lastpostmodified('GMT')),$this->GetOption("cf_home"),$this->GetOption("pr_home"));
		}
		
		//Add the posts
		if($this->GetOption("in_posts")) {
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Start Postings"));

			//Retrieve all posts and static pages (if enabled)
			$postRes=$wpdb->get_results("SELECT `ID` ,`post_modified`, `post_date`, `post_status` FROM `" . $wpdb->posts . "` WHERE post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "' AND (post_status = 'publish' " . ($this->GetOption("in_pages")?"OR post_status='static'":"") . ") " . ($this->GetOption("sm_in_prot_posts")===false?"AND post_password=''":"") . " ORDER BY post_modified DESC");

			$minPrio=$this->GetOption("pr_posts_min");
			
			if($postRes) {
				//Count of all posts
				$postCount=count($postRes);
				
				#type $prioProvider GoogleSitemapGeneratorPrioProviderBase
				$prioProvider=NULL;
				
				if($this->GetOption("b_prio_provider")!="") {
					$providerClass=$this->GetOption("b_prio_provider");
					$prioProvider = new $providerClass($commentCount,$postCount);
				}

				//Cycle through all posts and add them
				foreach($postRes as $post) {
					//Default Priority if auto calc is disabled
					$prio=0;
					if($post->post_status=="static") {
						//Priority for static pages
						$prio=$this->GetOption("pr_pages");
					} else {
						//Priority for normal posts
						$prio=$this->GetOption("pr_posts");
					}
					
					//If priority calc is enabled, calc (but only for posts, not pages)!
					if($this->GetOption("b_prio_provider")!="" && $post->post_status!="static") {
						
						if($prioProvider!==NULL) {				
							//Comment count for this post
							$cmtcnt=(array_key_exists($post->ID,$comments)?$comments[$post->ID]:0);
							$prio=$prioProvider->GetPostPriority($post->ID,$cmtcnt);
						}
						
						if($debug) {
							$this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Priority report of postID " . $post->ID . ": Comments: " . $cmtcnt . " of " . $commentCount . " = " . $prio . " points"));						
						}
					}	
					
					if($post->post_status!="static" && !empty($minPrio) && $prio<$minPrio) {
						$prio=$minPrio;
					}
					
					//Add it
					$this->AddUrl(get_permalink($post->ID),$this->GetTimestampFromMySql((!empty($post->post_modified) && $post->post_modified!='0000-00-00 00:00:00'?$post->post_modified:$post->post_date)),$this->GetOption(($post->post_status=="static"?"sm_cf_posts":"sm_cf_pages")),$prio);
				}
			}
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: End Postings"));
		}
		
		//Add the cats
		if($this->GetOption("in_cats")) {
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Start Cats"));
			
			//Add Categories... Big thanx to Rodney Shupe (http://www.shupe.ca) for the SQL
			$catsRes=$wpdb->get_results("SELECT cat_ID AS ID, MAX(post_modified) AS last_mod FROM `" . $wpdb->posts . "` p LEFT JOIN `" . $wpdb->post2cat . "` pc ON p.ID = pc.post_id LEFT JOIN `" . $wpdb->categories . "` c ON pc.category_id = c.cat_ID WHERE post_status = 'publish' GROUP BY cat_ID");
			if($catsRes) {
				foreach($catsRes as $cat) {
					if($cat && $cat->ID && $cat->ID>0) {
						if($debug) if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Cat-ID:" . $cat->ID)); 	
						$this->AddUrl(get_category_link($cat->ID),$this->GetTimestampFromMySql($cat->last_mod),$this->GetOption("cf_cats"),$this->GetOption("pr_cats"));
					}
				}	
			}
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: End Cats"));	
		}
		//Add the archives
		if($this->GetOption("in_arch")) {
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Start Archive"));
			$now = current_time('mysql');
			//Add archives...  Big thanx to Rodney Shupe (http://www.shupe.ca) for the SQL
			$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, MAX(post_date) as last_mod, count(ID) as posts FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");
			if ($arcresults) {
				foreach ($arcresults as $arcresult) {
					
					$url  = get_month_link($arcresult->year,   $arcresult->month);
					$changeFreq="";
					
					//Archive is the current one
					if($arcresult->month==date("n") && $arcresult->year==date("Y")) {
						$changeFreq=$this->GetOption("cf_arch_curr");	
					} else { // Archive is older
						$changeFreq=$this->GetOption("cf_arch_old");	
					}
					
					$this->AddUrl($url,$this->GetTimestampFromMySql($arcresult->last_mod),$changeFreq,$this->GetOption("pr_arch"));				
				}
			}
			if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: End Archive")); 	
		}
		
		//Add the custom pages
		if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Start Custom Pages"));
		if($this->_pages && is_array($this->_pages) && count($this->_pages)>0) {
			//#type $page GoogleSitemapGeneratorPage
			foreach($this->_pages AS $page) {
				$this->AddUrl($page->GetUrl(),$page->getLastMod(),$page->getChangeFreq(),$page->getPriority());
			}	
		}
		
		if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: End Custom Pages"));
		
		if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: Start additional URLs"));
		
		do_action("sm_buildmap",&$this);
		
		if($debug) $this->AddElement(new GoogleSitemapGeneratorDebugEntry("Debug: End additional URLs"));
		
		$this->AddElement(new GoogleSitemapGeneratorXmlEntry("</urlset>"));
		
		$s="";
		$c = count($this->_content);
		for($i =0; $i<$c; $i++) {
			$s.=$this->_content[$i]->Render() . "\n";	
		}
		
		$pingUrl="";
		
		$oldHandler = set_error_handler(array(&$this,"TrackError"));
		
		//Write normal sitemap file
		if($this->GetOption("b_xml")) {
			$fileName = $this->GetXmlPath();
			$f=@fopen($fileName,"w");
			if($f) {
				if(fwrite($f,$s)) {
					$pingUrl=$this->GetXmlUrl();
					$messages[] = $this->AddLog(__("Successfully built sitemap file:",'sitemap') . "<br />" . "- " .  __("URL:",'sitemap') . " <a href=\"" . $pingUrl . "\">" . $pingUrl . "</a><br />- " . __("Path:",'sitemap') . " " . $fileName,0,false);
					
				}
				fclose($f);	
			} else {
				$messages[] = $this->AddLog(str_replace("%s",$fileName,__("Could not write into %s",'sitemap'). ($this->_lastError!==null?": " . $this->_lastError:"")),2,false);
			}
		}
		
		//Write gzipped sitemap file
		if($this->GetOption("b_gzip")===true && function_exists("gzencode")) {
			$fileName = $this->GetZipPath();
			$f=@fopen($fileName,"w");
			if($f) {
				if(fwrite($f,gzencode($s))) {
					$pingUrl=$this->GetZipUrl();
					$messages[] = $this->AddLog(__("Successfully built gzipped sitemap file:",'sitemap') . "<br />" . "- " .  __("URL:",'sitemap') . " <a href=\"" . $pingUrl . "\">" . $pingUrl . "</a><br />- " . __("Path:",'sitemap') . " " . $fileName,0,false);
				}
				fclose($f);	
			} else {
				$messages[] = $this->AddLog(str_replace("%s",$fileName,__("Could not write into %s",'sitemap'). ($this->_lastError!==null?": " . $this->_lastError:"")),2,false);
			}
		}
		
		//Ping Google
		if($this->GetOption("b_ping") && $pingUrl!="") {
			$pingUrl="http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($pingUrl);
			$pingres=@wp_remote_fopen($pingUrl);

			if($pingres==NULL || $pingres===false) {
				$messages[] = $this->AddLog(str_replace("%s","<a href=\"$pingUrl\">$pingUrl</a>",__("Could not ping to Google at %s",'sitemap') . ($this->_lastError!==null?": " . $this->_lastError:"")),1,false);
			} else {
				$messages[] = $this->AddLog(str_replace("%s","<a href=\"$pingUrl\">$pingUrl</a>",__("Successfully pinged Google at %s",'sitemap')),0,false);
			}
		}
		
		if(count($messages)>0) {
			$this->SaveLog();
		}
		
		if($oldHandler!==null) restore_error_handler();
		
		//done...
		return $messages;
	}
	
	/**
	 * Tracks the last error (gets called by PHP)
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 */
	function TrackError($log_level, $log_text, $error_file, $error_line) {
		$this->_lastError = $log_text;		
	}
	
	/**
	 * Adds the options page in the admin menu
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 */
	function RegisterAdminPage() {
		if (function_exists('add_options_page')) {
			add_options_page(__('Sitemap Generator','sitemap'), __('Sitemap','sitemap'), 8, basename(__FILE__), array(&$this,'HtmlShowOptionsPage'));	
		}
	}
	
	/**
	 * Echos option fields for an select field containing the valid change frequencies
	 * 
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $currentVal The value which should be selected
	 * @return all valid change frequencies as html option fields 
	 */
	function HtmlGetFreqNames($currentVal) {
		foreach($this->_freqNames AS $v) {
			echo "<option value=\"$v\" " . $this->HtmlGetSelected($v,$currentVal) .">";
			echo ucfirst(__($v,'sitemap'));
			echo "</option>";	
		}
	}
	
	/**
	 * Echos option fields for an select field containing the valid priorities (0- 1.0)
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $currentVal string The value which should be selected
	 * @return 0.0 - 1.0 as html option fields 
	 */
	function HtmlGetPriorityValues($currentVal) {
		$currentVal=(float) $currentVal;
		for($i=0.0; $i<=1.0; $i+=0.1) {
			echo "<option value=\"$i\" " . $this->HtmlGetSelected("$i","$currentVal") .">";
			_e(strval($i));
			echo "</option>";	
		}	
	}
	
	/**
	 * Returns the checked attribute if the given values match
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return The checked attribute if the given values match, an empty string if not
	 */
	function HtmlGetChecked($val,$equals) {
		if($val==$equals) return $this->HtmlGetAttribute("checked");
		else return "";		
	}
	
	/**
	 * Returns the selected attribute if the given values match
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return The selected attribute if the given values match, an empty string if not
	 */
	function HtmlGetSelected($val,$equals) {
		if($val==$equals) return $this->HtmlGetAttribute("selected");
		else return "";		
	}
	
	/**
	 * Returns an formatted attribute. If the value is NULL, the name will be used.
	 *
	 * @since 3.0
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @param $attr string The attribute name
	 * @param $value string The attribute value
	 * @return The formatted attribute
	 */
	function HtmlGetAttribute($attr,$value=NULL) {
		if($value==NULL) $value=$attr;
		return " " . $attr . "\"" . $value . "\" ";	
	}
	
	/**
	 * Returns an array with GoogleSitemapGeneratorPage objects which is generated from POST values
	 *
	 * @since 3.0
	 * @see GoogleSitemapGeneratorPage
	 * @access private
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 * @return array An array with GoogleSitemapGeneratorPage objects
	 */
	function HtmlApplyPages() {
		// Array with all page URLs
		$pages_ur=(!isset($_POST["sm_pages_ur"]) || !is_array($_POST["sm_pages_ur"])?array():$_POST["sm_pages_ur"]);
		
		//Array with all priorities
		$pages_pr=(!isset($_POST["sm_pages_pr"]) || !is_array($_POST["sm_pages_pr"])?array():$_POST["sm_pages_pr"]);
		
		//Array with all change frequencies
		$pages_cf=(!isset($_POST["sm_pages_cf"]) || !is_array($_POST["sm_pages_cf"])?array():$_POST["sm_pages_cf"]);
		
		//Array with all lastmods
		$pages_lm=(!isset($_POST["sm_pages_lm"]) || !is_array($_POST["sm_pages_lm"])?array():$_POST["sm_pages_lm"]);

		//Array where the new pages are stored
		$pages=array();
		
		//Loop through all defined pages and set their properties into an object
		if(isset($_POST["sm_pages_mark"]) && is_array($_POST["sm_pages_mark"])) {
			for($i=0; $i<count($_POST["sm_pages_mark"]); $i++) {
				//Create new object
				$p=new GoogleSitemapGeneratorPage();
				if(substr($pages_ur[$i],0,4)=="www.") $pages_ur[$i]="http://" . $pages_ur[$i];
				$p->SetUrl($pages_ur[$i]);
				$p->SetProprity($pages_pr[$i]);
				$p->SetChangeFreq($pages_cf[$i]);
				//Try to parse last modified, if -1 (note ===) automatic will be used (0)
				$lm=(!empty($pages_lm[$i])?strtotime($pages_lm[$i],time()):-1);
				if($lm===-1) $p->setLastMod(0);
				else $p->setLastMod($lm);
				
				//Add it to the array
				array_push($pages,$p);
			}					
		}	
		return $pages;
	}
	
	function GetTimestampFromMySql($mysqlDateTime) {
		list($date, $hours) = split(' ', $mysqlDateTime);
		list($year,$month,$day) = split('-',$date);
		list($hour,$min,$sec) = split(':',$hours);
		return mktime($hour, $min, $sec, $month, $day, $year);
	}
	
	/**
	 * Displays the option page
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold <himself [at] arnebrachhold [dot] de>
	 */
	function HtmlShowOptionsPage() {
		
		$this->Initate();
			
		//All output should go in this var which get printed at the end
		$message="";
		
		if(!empty($_POST["sm_rebuild"])) { //Pressed Button: Rebuild Sitemap
			$msg = $this->BuildSitemap();
			
			if($msg && is_array($msg)) {
				foreach($msg AS $ms) {
					$message.=$ms->GetMessage() . "<br />";	
				}
			}
		} else if (!empty($_POST['sm_update'])) { //Pressed Button: Update Config		
			foreach($this->_options as $k=>$v) {
				//Check vor values and convert them into their types, based on the category they are in
				if(!isset($_POST[$k])) $_POST[$k]=""; // Empty string will get false on 2bool and 0 on 2float
				
				//Options of the category "Basic Settings" are boolean, except the filename and the autoprio provider
				if(substr($k,0,5)=="sm_b_") {					
					if($k=="sm_b_filename" || $k=="sm_b_fileurl_manual" || $k=="sm_b_filename_manual" || $k=="sm_b_prio_provider" || $k=="sm_b_manual_key") $this->_options[$k]=(string) $_POST[$k];
					else if($k=="sm_b_location_mode") {
						$tmp=(string) $_POST[$k];
						$tmp=strtolower($tmp);
						if($tmp=="auto" || $tmp="manual") $this->_options[$k]=$tmp;
						else $this->_options[$k]="auto";								
					} else $this->_options[$k]=(bool) $_POST[$k];	
				//Options of the category "Includes" are boolean
				} else if(substr($k,0,6)=="sm_in_") {
					$this->_options[$k]=(bool) $_POST[$k];		
				//Options of the category "Change frequencies" are string
				} else if(substr($k,0,6)=="sm_cf_") {
					$this->_options[$k]=(string) $_POST[$k];		
				//Options of the category "Priorities" are float
				} else if(substr($k,0,6)=="sm_pr_") {
					$this->_options[$k]=(float) $_POST[$k];		
				}
			}
			if($this->SaveOptions()) $message.=__('Configuration updated', 'sitemap');
			else $message.=__('Error', 'sitemap');
			
		} else if(!empty($_POST["sm_pages_new"])) { //Pressed Button: New Page
			
			//Apply page changes from POST
			$this->_pages=$this->HtmlApplyPages();
			
			//Add a new page to the array with default values
			$p=new GoogleSitemapGeneratorPage("",0.0,"never",0);
			array_push($this->_pages,$p);
			$message.=__('A new page was added. Click on &quot;Save page changes&quot; to save your changes.','sitemap');
		
		} else if(!empty($_POST["sm_pages_save"])) { //Pressed Button: Save pages	
			
			//Apply page changes from POST
			$this->_pages=$this->HtmlApplyPages();
			
			if($this->SavePages()) $message.=__("Pages saved",'sitemap');
			
		} else if(!empty($_POST["sm_pages_del"])) { //Pressed Button: Delete page
			
			//Apply page changes from POST
			$this->_pages=$this->HtmlApplyPages();
		
			//the selected page is stored in value of the radio button
			$i=intval($_POST["sm_pages_action"]);
			
			//Remove the page from the array
			$this->_pages = $this->ArrayRemove($this->_pages,$i);
			
			$message.=__('The page was deleted. Click on &quot;Save page changes&quot; to save your changes.','sitemap');
		
		} else if(!empty($_POST["sm_pages_undo"])) { //Pressed Button: Clear page Changes
			
			//In all other page changes, we do the sm_apply_pages functions. Here we don't, so we got the original settings from the db
			
			$message.=__('Your changes have been cleared.','sitemap');
		} else if(!empty($_POST["sm_clear_log"])) { //Pressed Button: Clear Log {
			$this->ClearLog();
			
		} else if(!empty($_POST["sm_reset_config"])) { //Pressed Button: Reset Config
			$this->InitOptions();
			$this->SaveOptions();
			
			$message.=__('The default configuration was restored.','sitemap');
		}
		
		//Print out the message to the user, if any
		if($message!="") {
			?>
			<div class="updated"><strong><p><?php
			echo $message;
			?></p></strong></div><?php
		}
		?>
		
		<script type="text/javascript" src="<?php echo $_SERVER["REQUEST_URI"] . "&res={E852E31E-EC63-4d3e-ACF0-FC212326F06D}"; ?>"></script>
		<style type="text/css">
		.sm_warning:hover {
			background: #ce0000;
			color: #fff;
		}
		
		a.sm_button {
			padding:4px;
			border:1px gray solid;
			padding-left:25px;
			background-repeat:no-repeat;
			background-position:5px 50%;		
		}
		
		a.sm_button:hover {
			background-color:whitesmoke;	
			position:relative;
			top:1px;
			left:1px;
		}
		

		a.sm_donatePayPal {
			background-image:url(<?php echo $_SERVER["REQUEST_URI"] . "&res={8C0BAD8C-77FA-4842-956E-CDEF7635F2C7}"; ?>);
		}
		a.sm_donateAmazon {
			background-image:url(<?php echo $_SERVER["REQUEST_URI"] . "&res={9866EAFC-3F85-44df-8A72-4CD1566E2D4F}"; ?>);
		}
		
		a.sm_pluginHome {
			background-image:url(<?php echo $_SERVER["REQUEST_URI"] . "&res={AD59B831-BF3D-49b1-A649-9DD8EDA1798A}"; ?>);
		}
		
		a.sm_pluginList {
			background-image:url(<?php echo $_SERVER["REQUEST_URI"] . "&res={FFA3E2B1-D2B1-4c66-B8A4-5F6E7D8781F2}"; ?>);
		}
		
		a.sm_pluginSupport {
			background-image:url(<?php echo $_SERVER["REQUEST_URI"] . "&res={234C74C9-3DF4-4ae2-A12E-C157C67059D8}"; ?>);	
		}

		</style>
		
		<div class="wrap" id="sm_div">
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<h2><?php _e('Google Sitemap Generator for WordPress', 'sitemap'); echo " " . $this->GetVersion() ?> </h2>
				


				<p style="text-align:right">
					<a class="sm_button sm_pluginHome"    href="http://www.arnebrachhold.de/redir/sitemap-home/">Plugin Homepage</a> 
					<a class="sm_button sm_pluginList"    href="http://www.arnebrachhold.de/redir/sitemap-list/">Notify List</a>
					<a class="sm_button sm_pluginSupport" href="http://www.arnebrachhold.de/redir/sitemap-support/">Support Forum</a>
					<a class="sm_button sm_donatePayPal"  href="http://www.arnebrachhold.de/redir/sitemap-paypal/">Donate with PayPal</a> 
					<a class="sm_button sm_donateAmazon"  href="http://www.arnebrachhold.de/redir/sitemap-amazon/">My Amazon Wish List</a>
				</p>
				
				<?php $log = $this->GetLog(); ?>

				<!-- Rebuild Area -->
				<fieldset id="sm_rebuild" class="options">
					<legend><?php _e('Manual rebuild / Log', 'sitemap') ?></legend>
					<?php if(count($log)>0) { ?>
					<div style="float:left;  width:25%; margin-right:10px;">
					<?php } else { ?>
					<div>
					<?php } ?>
					<p>
					<?php _e('If you want to build the sitemap without editing a post, click on here!', 'sitemap') ?><br /><br />
					<input type="submit" id="sm_rebuild" class="button" name="sm_rebuild" Value="<?php _e('Rebuild Sitemap','sitemap'); ?>" /><br /><br />
					<?php if(count($log)>0) { ?>
					<input type="submit" id="sm_clear_log" class="button" name="sm_clear_log" Value="<?php _e('Clear Log','sitemap'); ?>" />
					<?php } ?>
					</p>
					</div>
					<?php if(count($log)>0) { ?>
					<div style="overflow:auto; height:160px;">
					<?php
						for($i=0; $i<count($log); $i++) {
							echo $log[$i]->GetHTML() . "<br />";	
						}
					
					?>
					</div>
					<?php } ?>
				</fieldset>
				
				<!-- Pages area -->
				<fieldset id="sm_pages"  class="options">
					<legend><?php _e('Additional pages', 'sitemap') ?></legend>
					<div>
					<?php 
					_e('Here you can specify files or URLs which should be included in the sitemap, but do not belong to your Blog/WordPress.<br />For example, if your domain is www.foo.com and your blog is located on www.foo.com/blog you might want to include your homepage at www.foo.com','sitemap');
					echo "<ul><li>";
					echo "<strong>" . __('Note','sitemap'). "</strong>: ";
					_e("If your blog is in a subdirectory and you want to add pages which are NOT in the blog directory or beneath, you MUST place your sitemap file in the root directory (Look at the &quot;Location of your sitemap file&quot; section on this page)!",'sitemap');
					echo "</li><li>";
					echo "<strong>" . __('URL to the page','sitemap'). "</strong>: ";
					_e("Enter the URL to the page. Examples: http://www.foo.com/index.html or www.foo.com/home ",'sitemap');
					echo "</li><li>";
					echo "<strong>" . __('Priority','sitemap') . "</strong>: ";
					_e("Choose the priority of the page relative to the other pages. For example, your homepage might have a higher priority than your imprint.",'sitemap');
					echo "</li><li>";
					echo "<strong>" . __('Last Changed','sitemap'). "</strong>: ";
					_e("Enter the date of the last change as YYYY-MM-DD (2005-12-31 for example) (optional).",'sitemap');
					
					echo "</li></ul>";
					?>
					<table width="100%" cellpadding="3" cellspacing="3"> 
						<tr>
							<th scope="col"><?php _e('URL to the page','sitemap'); ?></th>
							<th scope="col"><?php _e('Priority','sitemap'); ?></th>
							<th scope="col"><?php _e('Change Frequency','sitemap'); ?></th>
							<th scope="col"><?php _e('Last Changed','sitemap'); ?></th>
							<th scope="col"><?php _e('#','sitemap'); ?></th>
						</tr>					
						<?php
							if(count($this->_pages)>0) {
								$class="";
								for($i=0; $i<count($this->_pages); $i++) {
									$v=&$this->_pages[$i];
									
									//#type $v sm_page
									$class = ('alternate' == $class) ? '' : 'alternate';
									echo "<input type=\"hidden\" name=\"sm_pages_mark[$i]\" value=\"true\" />";
									echo "<tr class=\"$class\">";
									echo "<td><input type=\"textbox\" name=\"sm_pages_ur[$i]\" style=\"width:95%\" value=\"" . $v->getUrl() . "\" /></td>";
									echo "<td width=\"150\"><select name=\"sm_pages_pr[$i]\" style=\"width:95%\">";
									echo $this->HtmlGetPriorityValues($v->getPriority());
									echo "</select></td>";
									echo "<td width=\"150\"><select name=\"sm_pages_cf[$i]\" style=\"width:95%\">";
									echo $this->HtmlGetFreqNames($v->getChangeFreq());
									echo "</select></td>";
									echo "<td width=\"150\"><input type=\"textbox\" name=\"sm_pages_lm[$i]\" style=\"width:95%\" value=\"" . ($v->getLastMod()>0?date("Y-m-d",$v->getLastMod()):"") . "\" /></td>";
									echo "<td width=\"5\"><input type=\"radio\" name=\"sm_pages_action\" value=\"$i\" /></td>";
									echo "</tr>";																
								}
							} else {
								?><tr> 
									<td colspan="5" align="center"><?php _e('No pages defined.','sitemap') ?></td> 
								</tr><?php
							}
						?>
					</table>
					<div>
						<div style="float:left; width:70%">
							<input type="submit" name="sm_pages_new" value="<?php _e("Add new page",'sitemap'); ?>" />									
							<input type="submit" name="sm_pages_save" value="<?php _e("Save page changes",'sitemap'); ?>" />
							<input type="submit" name="sm_pages_undo" value="<?php _e("Undo all page changes",'sitemap'); ?>" />
						</div>
						<div style="width:30%; text-align:right; float:left;">
							<input type="submit" class="sm_warning" name="sm_pages_del" value="<?php _e("Delete marked page",'sitemap'); ?>" />
						</div>
					</div>
					</div>
				</fieldset>
				
				<!-- Basic Options -->
				<fieldset id="sm_basic_options"  class="options">
					<legend><?php _e('Basic Options', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_b_auto_enabled">
								<input type="checkbox" id="sm_b_auto_enabled" name="sm_b_auto_enabled" <?php echo ($this->GetOption("sm_b_auto_enabled")==true?"checked=\"checked\"":""); ?> />
								<?php _e('Rebuild sitemap if you change the content of your blog', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_manual_enabled">
								<input type="hidden" name="sm_b_manual_key" value="<?php echo $this->GetOption("b_manual_key"); ?>" />
								<input type="checkbox" id="sm_b_manual_enabled" name="sm_b_manual_enabled" <?php echo ($this->GetOption("b_manual_enabled")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Enable manual sitemap building via GET Request', 'sitemap') ?>
							</label>
							<a href="javascript:void(document.getElementById('sm_manual_help').style.display='');">[?]</a>
							<span id="sm_manual_help" style="display:none;"><br />
							<?php echo str_replace("%1",trailingslashit(get_bloginfo('siteurl')) . "?sm_command=build&sm_key=" . $this->GetOption("b_manual_key"),__('This will allow you to refresh your sitemap if an external tool wrote into the WordPress database without using the WordPress API. Use the following URL to start the process: <a href="%1">%1</a> Please check the logfile above to see if sitemap was successfully built.', 'sitemap')); ?>
							</span>
						</li>
						<li>
							<label for="sm_b_xml">
								<input type="checkbox" id="sm_b_xml" name="sm_b_xml" <?php echo ($this->GetOption("b_xml")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Write a normal XML file (your filename)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_gzip">
								<input type="checkbox" id="sm_b_gzip" name="sm_b_gzip" <?php if(function_exists("gzencode")) { echo ($this->GetOption("b_gzip")==true?"checked=\"checked\"":""); } else echo "disabled=\"disabled\"";  ?> />
								<?php _e('Write a gzipped file (your filename + .gz)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_ping">
								<input type="checkbox" id="sm_b_ping" name="sm_b_ping" <?php echo ($this->GetOption("b_ping")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Auto-Ping Google Sitemaps', 'sitemap') ?><br />
								<?php _e('This option will automatically tell Google about changes.','sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_debug">
								<input type="checkbox" id="sm_b_debug" name="sm_b_debug" <?php echo ($this->GetOption("b_debug")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Write debug comments', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				
				<!-- AutoPrio Options -->
				<fieldset id="sm_postprio"  class="options">
					<legend><?php _e('Post Priority', 'sitemap') ?></legend>
					<div>
					<p><?php _e('Please select how the priority of each post should be calculated:', 'sitemap') ?></p>
					<ul>
						<li><p><input type="radio" name="sm_b_prio_provider" id="sm_b_prio_provider__0" value="" <?php echo $this->HtmlGetChecked($this->GetOption("b_prio_provider"),"") ?> /> <label for="sm_b_prio_provider__0"><?php _e('Do not use automatic priority calculation', 'sitemap') ?></label><br /><?php _e('All posts will have the same priority which is defined in &quot;Priorities&quot;', 'sitemap') ?></p></li>
						<?php
						for($i=0; $i<count($this->_prioProviders); $i++) {
							echo "<li><p><input type=\"radio\" id=\"sm_b_prio_provider_$i\" name=\"sm_b_prio_provider\" value=\"" . $this->_prioProviders[$i] . "\" " .  $this->HtmlGetChecked($this->GetOption("b_prio_provider"),$this->_prioProviders[$i]) . " /> <label for=\"sm_b_prio_provider_$i\">" . call_user_func(array(&$this->_prioProviders[$i], 'getName'))  . "</label><br />" .  call_user_func(array(&$this->_prioProviders[$i], 'getDescription')) . "</p></li>";
						}
						?>
					</ul>
					</div>
				</fieldset>
				
				<!-- Location Options -->
				<fieldset id="sm_location"  class="options">
					<legend><?php _e('Location of your sitemap file', 'sitemap') ?></legend>
					<div>
					<fieldset id="sm_location_auto">
						<legend><label for="sm_location_useauto"><input type="radio" id="sm_location_useauto" name="sm_b_location_mode" value="auto" <?php echo ($this->GetOption("b_location_mode")=="auto"?"checked=\"checked\"":"") ?> /> <?php _e('Automatic location','sitemap') ?></label></legend>
						<ul>
							<li>
								<label for="sm_b_filename">
									<?php _e('Filename of the sitemap file', 'sitemap') ?>
									<input type="text" id="sm_b_filename" name="sm_b_filename" value="<?php echo $this->GetOption("b_filename"); ?>" />
								</label><br />
								<?php _e('Detected Path', 'sitemap') ?>: <?php echo $this->getXmlPath(true); ?><br /><?php _e('Detected URL', 'sitemap') ?>: <a href="<?php echo $this->getXmlUrl(true); ?>"><?php echo $this->getXmlUrl(true); ?></a>
							</li>
						</ul>
					</fieldset>
					
					<p><?php _e('OR','sitemap'); ?></p>
					
					<fieldset id="sm_location_manual">
						<legend><label for="sm_location_usemanual"><input type="radio" id="sm_location_usemanual" name="sm_b_location_mode" value="manual" <?php echo ($this->GetOption("b_location_mode")=="manual"?"checked=\"checked\"":"") ?>  /> <?php _e('Custom location','sitemap') ?></label></legend>
						<ul>
							<li>
								<label for="sm_b_filename_manual">
									<?php _e('Absolute or relative path to the sitemap file, including name.','sitemap');
									echo "<br />";
									_e('Example','sitemap');
									echo ": /var/www/htdocs/wordpress/sitemap.xml"; ?><br />
									<input style="width:300px;" type="text" id="sm_b_filename_manual" name="sm_b_filename_manual" value="<?php echo (!$this->GetOption("b_filename_manual")?$this->getXmlPath():$this->GetOption("b_filename_manual")); ?>" />
								</label>
							</li>
							<li>
								<label for="sm_b_fileurl_manual">
									<?php _e('Complete URL to the sitemap file, including name.','sitemap');
									echo "<br />";
									_e('Example','sitemap');
									echo ": http://www.yourdomain.com/sitemap.xml"; ?><br />
									<input style="width:300px;" type="text" id="sm_b_fileurl_manual" name="sm_b_fileurl_manual" value="<?php echo (!$this->GetOption("b_fileurl_manual")?$this->getXmlUrl():$this->GetOption("b_fileurl_manual")); ?>" />
								</label>
							</li>
						</ul>
					</fieldset>
					</div>
				</fieldset>
				
				<!-- Includes -->	
				<fieldset id="sm_includes"  class="options">
					<legend><?php _e('Sitemap Content', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_in_home">
								<input type="checkbox" id="sm_in_home" name="sm_in_home"  <?php echo ($this->GetOption("in_home")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_posts">
								<input type="checkbox" id="sm_in_posts" name="sm_in_posts"  <?php echo ($this->GetOption("in_posts")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include posts', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_prot_posts">
								<input type="checkbox" id="sm_in_prot_posts" name="sm_in_prot_posts"  <?php echo ($this->GetOption("sm_in_prot_posts")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include protected posts', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_pages">
								<input type="checkbox" id="sm_in_pages" name="sm_in_pages"  <?php echo ($this->GetOption("in_pages")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include static pages', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_cats">
								<input type="checkbox" id="sm_in_cats" name="sm_in_cats"  <?php echo ($this->GetOption("in_cats")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_arch">
								<input type="checkbox" id="sm_in_arch" name="sm_in_arch"  <?php echo ($this->GetOption("in_arch")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include archives', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				
				<!-- Change frequencies -->
				<fieldset id="sm_change_frequencies"  class="options">
					<legend><?php _e('Change frequencies', 'sitemap') ?></legend>
					<div>
					<p>
					<b><?php _e('Note', 'sitemap') ?>:</b> 
					<?php _e('Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. It is also likely that crawlers will periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.', 'sitemap') ?>
					</p>
					<ul>
						<li>
							<label for="sm_cf_home">
								<select id="sm_cf_home" name="sm_cf_home"><?php $this->HtmlGetFreqNames($this->GetOption("cf_home")); ?></select> 
								<?php _e('Homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_posts">
								<select id="sm_cf_posts" name="sm_cf_posts"><?php $this->HtmlGetFreqNames($this->GetOption("cf_posts")); ?></select> 
								<?php _e('Posts', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_pages">
								<select id="sm_cf_pages" name="sm_cf_pages"><?php $this->HtmlGetFreqNames($this->GetOption("cf_pages")); ?></select> 
								<?php _e('Static pages', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_cats">
								<select id="sm_cf_cats" name="sm_cf_cats"><?php $this->HtmlGetFreqNames($this->GetOption("cf_cats")); ?></select> 
								<?php _e('Categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_arch_curr">
								<select id="sm_cf_arch_curr" name="sm_cf_arch_curr"><?php $this->HtmlGetFreqNames($this->GetOption("cf_arch_curr")); ?></select> 
								<?php _e('The current archive of this month (Should be the same like your homepage)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_arch_old">
								<select id="sm_cf_arch_old" name="sm_cf_arch_old"><?php $this->HtmlGetFreqNames($this->GetOption("cf_arch_old")); ?></select> 
								<?php _e('Older archives (Changes only if you edit an old post)', 'sitemap') ?>
							</label>
						</li>
					</ul>
					</div>
				</fieldset>
				
				<!-- Priorities -->				
				<fieldset id="sm_priorities"  class="options">
					<legend><?php _e('Priorities', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_pr_home">
								<select id="sm_pr_home" name="sm_pr_home"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_home")); ?></select> 
								<?php _e('Homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_posts">
								<select id="sm_pr_posts" name="sm_pr_posts"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_posts")); ?></select> 
								<?php _e('Posts (If auto calculation is disabled)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_posts_min">
								<select id="sm_pr_posts_min" name="sm_pr_posts_min"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_posts_min")); ?></select> 
								<?php _e('Minimum post priority (Even if auto calculation is enabled)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_pages">
								<select id="sm_pr_pages" name="sm_pr_pages"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_pages")); ?></select> 
								<?php _e('Static pages', 'sitemap'); ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_cats">
								<select id="sm_pr_cats" name="sm_pr_cats"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_cats")); ?></select> 
								<?php _e('Categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_arch">
								<select id="sm_pr_arch" name="sm_pr_arch"><?php $this->HtmlGetPriorityValues($this->GetOption("pr_arch")); ?></select> 
								<?php _e('Archives', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				<p class="submit">
					<input type="submit" name="sm_update" value="<?php _e('Update options', 'sitemap'); ?>" />
					<input type="submit" onclick='return confirm("Do you really want to reset your configuration?");' class="sm_warning" name="sm_reset_config" value="<?php _e('Reset options', 'sitemap'); ?>" />
				</p>
				<script type="text/javascript">
					function sm_gencode(doSelect) {
						var url="";
						var title="";
						if(document.getElementById('sm_codegen_file').checked) {
							url="<?php echo $this->GetXmlUrl(); ?>";
							title="XML-Sitemap";
						} else {
							url="http://www.arnebrachhold.de/2005/06/05/google-sitemaps-generator-v2-final";
							title="Google Sitemap Generator for Wordpress Plugin";
						}
						
						document.getElementById('sm_codegen_code').value='<a href="' + url + '"><img border="0" src="<?php echo trailingslashit(get_bloginfo('siteurl'));  ?>?res={7428F989-4DE9-4a97-AFF8-9E7E4B2E5BA9}" title="' + title + '" alt="' + title + '" /></a>';
						document.getElementById('sm_codegen_preview').innerHTML = document.getElementById('sm_codegen_code').value;
						
						if(doSelect) {
							document.getElementById('sm_codegen_code').select();
							document.getElementById('sm_codegen_code').focus();
						}
						
					}
				</script>
				<fieldset id="sm_xmlsitemap" class="options">
					<legend><?php _e('XML-Sitemap Button', 'sitemap'); ?></legend>
					<p>
						<?php _e('If you want to show your visitors that you support the XML-Sitemap format or want to link to the plugin homepage, insert the following code into your sidebar:', 'sitemap'); ?>
						<table border="0" cellpadding="2">
							<tr>
								<td><input type="radio" name="sm_codegen_choose" id="sm_codegen_file" checked="checked" onclick="sm_gencode(true);"> <label for="sm_codegen_file"><?php _e('Link to your sitemap file', 'sitemap'); ?></label></td>
								<td rowspan="2">
									<textarea readonly="readonly" name="sm_codegen_code" id="sm_codegen_code" style="width:500px; height:100px;"></textarea>
								</td>
								<td rowspan="2"><div id="sm_codegen_preview"></div></td>
							</tr>
							<tr>
								<td><input type="radio" name="sm_codegen_choose" id="sm_codegen_home" onclick="sm_gencode(true);"> <label for="sm_codegen_home"><?php _e('Link to the plugin homepage', 'sitemap'); ?></label></td>								
							</tr>
						</table>
					</p>
				</fieldset>
			</form>
		</div> 
		<script type="text/javascript">
		sm_addLinks();	
		sm_gencode();
		</script>
		<?php
	}
}


//Check if ABSPATH and WPINC is defined, this is done in wp-settings.php
//If not defined, we can't guarante that all required functions are available.
if(defined("ABSPATH") && defined("WPINC")) {
	GoogleSitemapGenerator::Enable();	
}


//Embedded resources
if(isset($_GET["res"]) && !empty($_GET["res"])) {
	
	#region Images
	//Paypal
	if($_GET["res"]=="{8C0BAD8C-77FA-4842-956E-CDEF7635F2C7}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhEAAQAMQQANbe5sjT3gAzZpGmvOTp7v///629zYSctK28zbvI1ZKnvfH094SbtHaQrHaRrJ+xxf///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAHoAxAALAAAAAAQABAAQAVZICSOZDkGQqGuBTqUSMqqiTACwqIKgGkKwCDwVRIodkDVwjYSMFSNoCNQIgSugZ6vBMBetSYCj0UwCA6lgyy4YoqWASRAZSYNZNaA+VxM7B5bEA9CboGGIyEAOw==");	
		exit;
	}
	
	//Amazon
	if($_GET["res"]=="{9866EAFC-3F85-44df-8A72-4CD1566E2D4F}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhEAAQAMQGADc1NVBOTmloaERCQl1bW9rZ2f///7Szs8rKyubm5nZ0dJuamvPy8qinp31gOLeDOuWgPPGnPM6SO05DNoKBgY+NjXFZN8OLOpRuOWZSN0M8NdqZO1pKNqB1OYhnOOrBhSwAAAAAEAAQAEAFeSAgBsIgnqiYGOxaAM4DRZE0AcOCMOyS/hxLR0KLPCaciEOEMCQKLMVPRIgCetPsaYLRXCKaIm3TuZ08mhMByjpoAQhE4GrwZaGMBnYq6OkNL1kBCwsUABUDFhcXGDIRGVMTDl8QExsSABcbYjQXkABDIh8XHQ5mWiEAOw==");
		exit;
	}
	
	//Homepage
	if($_GET["res"]=="{AD59B831-BF3D-49b1-A649-9DD8EDA1798A}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhEAAQAPc6AKG82qK72aG62KC52KK52Z+32aG526C315631aK52KG71qC516O62aO82qC42qO63KW83KK72p+41p+415+62KG42J+51KS72qG62aK526S726O62qW616O92KW52qC41KK61qG51Z230p611aS31ae826O71aa516a72KO416i816K61KO41aC40p+306G21aW41qe62KK30qS51qG20aq+2am61qi51aa61aW51LHD2aq52Ka306690qi20aa606O402R7nae40rW70bK817C606u507C70a651a6406m506+71a2916KwzVVwm1Fokqa30ai40au51Ka20Ke1z6m50qy40qa11Ke30ae41F9xlVlym1Zsk0tsl6O30qS1z6W20qK2z6W1z6O3z6O0zqq60am1z5+uy110ll1zmlNvl09rk1h0nKO00KW20KC0zV52kFV1m1l0n150nVRwl150m1FzmU9slmp+n560zJ+zzKGyzmF0kk5rlVJymV11mVtzmVhvm0xpkWh4nFJulmR0mGqTwW2Sv2ySv2uOuFtym1pxmldzm2V3nV1xllBvm1hxmlhwkmR4m1x0mGRzmmCHsmCKtF+FtGSGs1p/rFeBq1d8qVZ7qFt9q1h/qlN8qll5qlV3pFJ0oU90oFFzoVWAq1V9rlWArVZ/rVeCr1Z/q1h+rVR+rleArlN+q1d9rFB6rFN7rFF8qVR6q1F6qFN5qFJ9qlB7qFJ7p1J7qVR6qVJ4pwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAAEAAQAAAI/wABBBAwgECAAgYEHBCAIMEABQsYBGjg4AGECAskTABAoYIECwYYXMAgIIMGAQwWCFC5geGCBBwYdKjgAcMHECBCiBiRgACJEgxMMDiBIgUHFSZWsGjhQsELGByiwoghI8aJGTBQ0ADxoYaNGzhsqMiBQ8eOGzx6+PgBJAgPIUOIFDFyZAiSJEqWMGni5AmUKFKmUKliJcqUK1iyaNnCpYuXL2DCiBlDpowQM2fQpFGzhk0bN2DefMkCJ84WOXPo1LFzB0+bPHra7OHTx88fQH0CCRpEqJChQ4gSKVrEqJGjR5DSRJI0iVIlS5cwZdK0iVMnT59AhRI1ilQpU6dQpSBCpWoVK1anWrlileoVrFiyZtGqRcuVrVu0cNHKpWtXQAA7");
		exit;
	}
	
	//List
	if($_GET["res"]=="{FFA3E2B1-D2B1-4c66-B8A4-5F6E7D8781F2}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhEAAQAJECAAAAAP///////wAAACH5BAHoAwIALAAAAAAQABAAAAIrlI+py50Ao4QIhosvPSD07HmVln3ASIYaqn0rB4InHGOz4da3MPW7AwwKCwA7");
		exit;
	}
	
	//Support
	if($_GET["res"]=="{234C74C9-3DF4-4ae2-A12E-C157C67059D8}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhEAANALMPAFV3uxFBoCJOpzNcrd3k8Yigz3eSyczW62aFwgAzmURptJmt1qq73bvJ5O7x+P///yH5BAHoAw8ALAAAAAAQAA0AAARaEAQxSBHUvHcGfkhybEeCbBuREFyibUqAPguAeuiSMPe46Y1NSEEazBwJmyMASDhAi9lD4igYGonC8jnDLgQsyuIkfQQCxEchEfBJDbtUwljmCGYKXn3P7z8iADs=");
		exit;
	}
	
	//XML Sitemap:
	if($_GET["res"]=="{7428F989-4DE9-4a97-AFF8-9E7E4B2E5BA9}") {
		header("Content-Type: image/gif");
		echo base64_decode("R0lGODlhUAAPAJEBAGZmZv////9mAImOeSwAAAAAUAAPAAACn4SPqcvtD0+YtNqLs968myCE4kiWYTCk6sq27gu7wWfWNRrn+s7OAGgLinC8orFHOw2VTOBwRUnhogMq8Yol5nxOAcg58QK7XpW2ak6bUROpGw3fJpfLL/O5Vset6Km1vcM1Ync3RnIGd6W39zZlJFgYKQa2pvhWBWjpGBc4J2SDeCQq9/MpFDqa2gJpeqP6CsPVMUtba4sRkau7y6tQAAA7");	
		exit;
	}
	#endregion
	
	#region JavaScript Stuff
	if($_GET["res"]=="{E852E31E-EC63-4d3e-ACF0-FC212326F06D}") {
		echo <<<EOT
		/**
		 * Sets a cookie with defined name, value and expire
		 *
		 * @param string name The name of the cookie
		 * @param string value The value of the cookie
		 * @param string expires The expire date of the cookie
		 */
		function sm_setCookie(name,value,expires) {
			document.cookie = name + "=" + value + "; expires=" + expires;
		}

		/**
		 * Returns the value of a specified cookie
		 *
		 * @param string name The ame of the cookie
		*/
		function sm_getCookie(name) {
			var val="";
			if(document.cookie) {
				var cookies=document.cookie.split('; ');
				for(var i=0; i < cookies.length; i++) {
					var cookie=cookies[i].split('=');
					if(cookie[0]==name) {
						val=cookie[1];
						break;
					}
					
				}
			}
			return val;
		}

		/**
		 * Returns if an element is in the array
		 *
		 * @param object obj The needle
		 * @return True if found, false if not
		*/
		Array.prototype.in_array = function ( obj ) {
			var len = this.length;
			for ( var x = 0 ; x < len ; x++ ) {
				if ( this[x] == obj ) return true;
			}
			return false;
		}

		/**
		 * @var An array with the hidden regions 
		 */
		var sm_hiddenRegions=new Array();

		/**
		 * Saves the hidden regions to the cookie
		 */
		function sm_saveRegionState() {
			var ser=sm_hiddenRegions.join('/');
			
			var exp = new Date();
			var year  = exp.getTime() + (365 * 24 * 60 * 60 * 1000);
			exp.setTime(year);
			
			sm_setCookie('sm_regions',ser,exp);																
		}

		/**
		 * Loads the hidden regions from the cookie
		 */
		function sm_loadRegionState() {
			var regionData=sm_getCookie('sm_regions');
			if(regionData) sm_hiddenRegions=regionData.split('/');
			else sm_hiddenRegions=new Array();
		}

		/**
		 * Marks a region as hidden and saves the state.
		 *
		 * @param string id The ID of the FieldSet
		 */
		function sm_hideRegion(id) {
			if(!sm_hiddenRegions.in_array(id)) sm_hiddenRegions.push(id);
			sm_saveRegionState();
		}

		/**
		 * Marks a region as viisble and saves the state.
		 * 
		 * @param string id The ID of the FieldSet
		 */
		function sm_showRegion(id) {
			var new_arr=new Array();
			for(var i=0; i<sm_hiddenRegions.length; i++) {
				if(sm_hiddenRegions[i]!=id) {
					new_arr.push(sm_hiddenRegions[i]);
				}
			}
			sm_hiddenRegions=new_arr;
			sm_saveRegionState();
			
		}

		/**
		 * Toogles the visibility of a FieldSet
		 * 
		 * @param object link The Link wich was clicked
		 */
		function sm_toogleFieldSetContent(link) {

			var set=(link.parentElement?link.parentElement.parentElement:link.parentNode.parentNode);
			var subs=(set.children?set.children:set.childNodes);
			
			for(var i=0; i<subs.length; i++) {
				var first=subs[i];
				if((first.nodeName && (first.nodeName=='LEGEND' || first.nodeType!=1)) || (first.tagName && first.tagName=='LEGEND'))  {
				
				} else {
					if(first.style.display=='none') {
						first.style.display='';
						link.innerHTML='[-]';
						sm_showRegion(set.id);
					} else {
						first.style.display='none';
						link.innerHTML='[+]';
						sm_hideRegion(set.id);
					}
				}

			}
		}

		//Load the state into the array
		sm_loadRegionState();


		function sm_addLinks() {
			var main_div=document.getElementById('sm_div');
			var sets=main_div.getElementsByTagName('FIELDSET');
			for(var i=0; i<sets.length; i++) {
				var legends=sets[i].getElementsByTagName('LEGEND');
				if(legends.length>0) {

					var link = document.createElement('a');
					link.setAttribute('href', 'javascript:void(0);');
					link.innerHTML='[-]';
					link.style.marginLeft='2px';
					legends[0].insertBefore(link, null);
					
					link.onclick = function() {
						sm_toogleFieldSetContent(this);
					}
					
					if(sm_hiddenRegions.in_array(sets[i].id)) {
						sm_toogleFieldSetContent(link);
					}				
				}
			}
		}	
EOT;
	exit;
	}
	#endregion
}
?>
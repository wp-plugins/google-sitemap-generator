<?php 
/*
 $Id$

 Sitemap Generator for WordPress
 ==============================================================================
 
 This generator will create a google compliant sitemap of your WordPress blog.
 Currently homepage, posts, static pages, categories and archives are supported.
 
 The priority of a post depends on its comments. More comments, higher priority!
 
 Feel free to visit my website under www.arnebrachhold.de or contact me at
 himself@arnebrachhold.de
 
 
 Info for WordPress:
 ==============================================================================
 Plugin Name: Google Sitemaps
 Plugin URI: http://www.arnebrachhold.de/2005/06/05/google-sitemaps-generator-v2-final
 Description: This generator will create a Google compliant sitemap of your WordPress blog.
 Version: 2.5
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de
 
 
 Contributors:
 ==============================================================================
 Basic Idea 			Michael Nguyen		http://www.socialpatterns.com/
 SQL Improvements		Rodney Shupe		http://www.shupe.ca
 Japanse Lang. File		Hirosama			http://hiromasa.zone.ne.jp
 Spanish lang. File		César Gómez Martín	http://www.cesargomez.org
 Ping Code Template 1	Ben					http://www.adlards.com/
 Ping Code Template	2	John				http://www.jonasblog.com/
 Bug Report				Brad				http://h3h.net/
 Bug Report				Christian Aust		http://publicvoidblog.de/
 
 Code, Documentation, Hosting and all other Stuff:
						Arne Brachhold		http://www.arnebrachhold.de		
 
 Thanks to all contributors and bug reporters! :)
 
 
 Release History:
 ==============================================================================
 2005-06-05		1.0		First release
 2005-06-05		1.1		Added archive support
 2005-06-05		1.2		Added category support
 2005-06-05		0.2		Beta: Real Plugin! Static file generation, Admin UI
 2005-06-05		2.0		Various fixes, more help, more comments, configurable filename
 2005-06-07		2.01	Fixed 2 Bugs: 147 is now _e(strval($i)); instead of _e($i); 344 uses a full < ?php instead of < ?
						Thanks to Christian Aust for reporting this :)
 2005-06-07		2.1		Correct usage of last modification date for cats and archives  (thx to Rodney Shupe (http://www.shupe.ca))
						Added support for .gz generation
						Fixed bug which ignored different post/page priorities
						Should support now different wordpress/admin directories
 2005-06-07		2.11	Fixed bug with hardcoded table table names instead of the $wpd vars
 2005-06-07		2.12	Changed SQL Statement of the categories to get it work on MySQL 3 
 2005-06-08		2.2		Added language file support:
						- Japanese Language Files and code modifications by hiromasa <webmaster@hiromasa.zone.ne.jp> http://hiromasa.zone.ne.jp
						- German Language File by Arne Brachhold <himself@arnebrachhold.de>
 2005-06-14		2.5		Added support for external pages
						Added support for Google Ping
						Added the minimum Post Priority option
						Added Spanish Language File by César Gómez Martín (http://www.cesargomez.org)
	
 Maybe Todo:
 ==============================================================================
 - Autogenerate priority of categories (by postcount?)
 - Better priority calculator
 - Your wishes :)
 
 License:
 ==============================================================================
 Copyright 2005  ARNE BRACHHOLD  (email : himself@arnebrachhold.de)

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
 About the pages storage:
  Every external page is represented in a instance of the sm_page class.
  I use an array to store them in the WordPress options table. Note
  that if you want to serialize a class, it must be available BEFORE you
  call unserialize(). So it's very important to set the autoload property
  of the option to false.
  
 About the pages editor:
  To store modifications to the pages without using session variables,
  i restore the sate of the modifications in hidden fields. Based on
  these, the array with the pages from the database gets overwritten.
  It's very important that you call the sm_apply_pages function on 
  every request if modifications to the pages should be saved. If
  you dont't all changes will be lost. (So works the Reset Changes button)
  
 All other methods are commented with phpDoc style.
 The "#region" tags and "#type $example example_class" comments are helpers which 
 may be used by your editor.  #region gives the ability to create custom code 
 folding areas, #type are type definitions for auto-complete.
 
*/

//Enable for dev
//error_reporting(E_ALL);

/******** Needed classes ********/

#region class sm_page
if(!class_exists("sm_page")) {
	/**
	 * Represents an item in the page list
	 * @author Arne Brachhold <himself@arnebrachhold.de>
	 * @since 2005-06-12
	 */
	class sm_page {
		/**
		 * @var bool $_enabled Sets if page is enabled ans hould be listed in the sitemap
		 * @access private
		 */
		var $_enabled;
		
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
		 * @var int $_lastMod Sets the lastMod of the page as a php timestamp. 
		 * @access private
		 */
		var $_lastMod;	
		
		/**
		 * Initialize a new page object
		 * 
		 * @param bool $enabled bool Should this page be included in thesitemap
		 * @param string $url The URL or path of the file
		 * @param float $priority The Priority of the page 0.0 to 1.0
		 * @param string $changeFreq The change frequency like daily, hourly, weekly
		 * @param int $lastMod The last mod date as a php timestamp
		 */
		function sm_page($enabled=false,$url="",$priority=0.0,$changeFreq="never",$lastMod=0) {
			$this->setEnabled($enabled);
			$this->setUrl($url);
			$this->setProprity($priority);
			$this->setChangeFreq($changeFreq);
			$this->setLastMod($lastMod);
		}

		
		/**
		 * Returns if the page should be included in the sitemap
		 * @return bool 
		 */
		function getEnabled() {
			return $this->_enabled;			
		}
		
		/**
		 * Sets if the page should be included in the sitemap
		 * @param bool $enabled value;
		 */
		function setEnabled($enabled) {
			$this->_enabled=(bool) $enabled;	
		}
		
		/**
		 * Returns the URL of the page
		 * @return string The URL
		 */
		function getUrl() {
			return $this->_url;	
		}
		
		/**
		 * Sets the URL of the page
		 * @param string $url The new URL
		 */
		function setUrl($url) {
			$this->_url=(string) $url;				
		}
		
		/**
		 * Returns the priority of this page
		 * @return float the priority, from 0.0 to 1.0
		 */
		function getPriority() {
			return $this->_priority;		
		}
		
		/**
		 * Sets the priority of the page
		 * @param float $priority The new priority from 0.1 to 1.0
		 */
		function setProprity($priority) {
			$this->_priority=floatval($priority);	
		}
		
		/**
		 * Returns the change frequency of the page
		 * @return string The change frequncy like hourly, weekly, monthly etc.
		 */
		function getChangeFreq() {
			return $this->_changeFreq;		
		}
		
		/**
		 * Sets the change frequency of the page
		 * @param string $changeFreq The new change frequency
		 */
		function setChangeFreq($changeFreq) {
			$this->_changeFreq=(string) $changeFreq;	
		}
		
		/**
		 * Returns the last mod of the page
		 * @return int The lastmod value in seconds
		 */
		function getLastMod() {
			return $this->_lastMod;	
		}
		
		/**
		 * Sets the last mod of the page
		 * @param int $lastMod The lastmod of the page
		 */
		function setLastMod($lastMod) {
			$this->_lastMod=intval($lastMod);			
		}								
	}
}
#endregion

#region Default configuration values
$sm_options=array();
$sm_options["sm_b_auto_prio"]=true;				//Use automatic priority calculation
$sm_options["sm_b_filename"]="sitemap.xml";		//Name of the Sitemap file
$sm_options["sm_b_debug"]=true;					//Write debug messages in the xml file
$sm_options["sm_b_xml"]=true;					//Create a .xml file
$sm_options["sm_b_gzip"]=true;					//Create a gzipped .xml file(.gz) file
$sm_options["sm_b_ping"]=true;					//Auto ping Google

$sm_options["sm_in_home"]=true;					//Include homepage
$sm_options["sm_in_posts"]=true;				//Include posts
$sm_options["sm_in_pages"]=true;				//Include static pages
$sm_options["sm_in_cats"]=true;					//Include categories
$sm_options["sm_in_arch"]=true;					//Include archives

$sm_options["sm_cf_home"]="daily";				//Change frequency of the homepage
$sm_options["sm_cf_posts"]="monthly";			//Change frequency of posts
$sm_options["sm_cf_pages"]="weekly";			//Change frequency of static pages
$sm_options["sm_cf_cats"]="weekly";				//Change frequency of categories
$sm_options["sm_cf_arch_curr"]="daily";			//Change frequency of the current archive (this month)
$sm_options["sm_cf_arch_old"]="yearly";			//Change frequency of older archives

$sm_options["sm_pr_home"]=1.0;					//Priority of the homepage
$sm_options["sm_pr_posts"]=0.7;					//Priority of posts (if auto prio is disabled)
$sm_options["sm_pr_posts_min"]=0.1;				//Minimum Priority of posts, even if autocalc is enabled
$sm_options["sm_pr_pages"]=0.6;					//Priority of static pages
$sm_options["sm_pr_cats"]=0.5;					//Priority of categories
$sm_options["sm_pr_arch"]=0.5;					//Priority of archives
#endregion

#region Load configuration
//Addition sites
$sm_pages=array();

//First init default values, then overwrite it with stored values so we can add default
//values with an update which get stored by the next edit.
$sm_storedoptions=get_option("sm_options");
if($sm_storedoptions) {
	foreach($sm_storedoptions AS $k=>$v) {
		$sm_options[$k]=$v;	
	}
} else update_option("sm_options",$sm_options); //First time use, store default values

$sm_storedpages=get_option("sm_cpages");

if($sm_storedpages) {
	$sm_pages=$sm_storedpages;
} else {
	//Add the option, Note the autoload=false because when the autoload happens, our class sm_page doesn't exist
	add_option("sm_cpages",$sm_pages,"Storage for custom pages of the sitemap plugin",false);
}

/**
 * @var array Contains all valid values for change frequency
 */
$sm_freq_names=array("always", "hourly", "daily", "weekly", "monthly", "yearly","never");
#endregion

/******** Path and URL functions ********/

#region sm_getXmlUrl
if(!function_exists("sm_getXmlUrl")) {
	/**
	* Returns the URL for the sitemap file
	*
	* @return The URL to the Sitemap file
	*/
	function sm_getXmlUrl() {
		//URL comes without /
		return get_bloginfo('siteurl') . "/" . sm_go("sm_b_filename");
	}
}
#endregion

#region sm_getZipUrl
if(!function_exists("sm_getZipUrl")) {
	/**
	* Returns the URL for the gzipped sitemap file
	*
	* @return The URL to the gzipped Sitemap file
	*/
	function sm_getZipUrl() {
		return sm_getXmlUrl() . ".gz";	
	}
}
#endregion

#region sm_getXmlPath
if(!function_exists("sm_getXmlPath")) {
	/**
	* Returns the file system path to the sitemap file
	*
	* @return The file system path;
	*/
	function sm_getXmlPath() {
		//ABSPATH has a slash
		return get_home_path()  . sm_go("sm_b_filename");	
	}
}
#endregion

#region sm_getZipPath
if(!function_exists("sm_getZipPath")) {
	/**
	* Returns the file system path to the gzipped sitemap file
	*
	* @return The file system path;
	*/
	function sm_getZipPath() {
		return sm_getXmlPath() . ".gz";	
	}
}
#endregion

/******** Helper functions ********/

#region sm_go
if(!function_exists("sm_go")) {
	/**
	* Returns the option value for the given key
	*
	* @param $key string The Configuration Key
	* @return mixed The value
	*/
	function sm_go($key) {
		global $sm_options;
		return $sm_options[$key];	
	}
}
#endregion

#region sm_freq_names
if(!function_exists("sm_freq_names")) {
	/**
	* Echos option fields for an select field containing the valid change frequencies
	* 
	* @param $currentVal The value which should be selected
	* @return all valid change frequencies as html option fields 
	*/
	function sm_freq_names($currentVal) {
		global $sm_freq_names;
		foreach($sm_freq_names AS $v) {
			echo "<option value=\"$v\" " . ($v==$currentVal?"selected=\"selected\"":"") .">";
			echo ucfirst(__($v,'sitemap'));
			echo "</option>";	
		}
	}
}
#endregion

#region sm_prio_names
if(!function_exists("sm_prio_names")) {
	/**
	* Echos option fields for an select field containing the valid priorities (0- 1.0)
	*
	* @param $currentVal string The value which should be selected
	* @return 0.0 - 1.0 as html option fields 
	*/
	function sm_prio_names($currentVal) {
		$currentVal=(float) $currentVal;
		for($i=0.0; $i<=1.0; $i+=0.1) {
			echo "<option value=\"$i\" " . ("$i" == "$currentVal"?"selected=\"selected\"":"") .">";
			_e(strval($i));
			echo "</option>";	
		}	
	}
}
#endregion

/******** Admin Page functions ********/

#region sm_apply_pages
if(!function_exists("sm_apply_pages")) {
	/**
	* This method will create new page objcts based on the input fields int he POST request
	*/
	function sm_apply_pages() {;
		// Array with all page URLs
		$sm_pages_ur=(!isset($_POST["sm_pages_ur"]) || !is_array($_POST["sm_pages_ur"])?array():$_POST["sm_pages_ur"]);
		
		//Array with all priorities
		$sm_pages_pr=(!isset($_POST["sm_pages_pr"]) || !is_array($_POST["sm_pages_pr"])?array():$_POST["sm_pages_pr"]);
		
		//Array with all change frequencies
		$sm_pages_cf=(!isset($_POST["sm_pages_cf"]) || !is_array($_POST["sm_pages_cf"])?array():$_POST["sm_pages_cf"]);
		
		//Array with all lastmods
		$sm_pages_lm=(!isset($_POST["sm_pages_lm"]) || !is_array($_POST["sm_pages_lm"])?array():$_POST["sm_pages_lm"]);

		//Array where the new pages are stored
		$pages=array();
		
		//Loop through all defined pages and set their properties into an object
		if(isset($_POST["sm_pages_mark"]) && is_array($_POST["sm_pages_mark"])) {
			for($i=0; $i<count($_POST["sm_pages_mark"]); $i++) {
				//Create new object
				$p=new sm_page();
				if(substr($sm_pages_ur[$i],0,4)=="www.") $sm_pages_ur[$i]="http://" . $sm_pages_ur[$i];
				$p->setUrl($sm_pages_ur[$i]);
				$p->setProprity($sm_pages_pr[$i]);
				$p->setChangeFreq($sm_pages_cf[$i]);
				//Try to parse last modified, if -1 (note ===) automatic will be used (0)
				$lm=(!empty($sm_pages_lm[$i])?strtotime($sm_pages_lm[$i],time()):-1);
				if($lm===-1) $p->setLastMod(0);
				else $p->setLastMod($lm);
				
				//Add it to the array
				$pages[count($pages)]=$p;
			}					
		}	
		//Return it, cause I don't care about PHP4 references...
		return $pages;
	}
}
#endregion

#region sm_array_remove
if(!function_exists("sm_array_remove")) {
	/**
	* Removes an element of an array and reorders the indexes
	* 
	* @param array $array The array with the values
	* @param object $indice The key which vallue should be removed
	* @return array The modified array
	*/
	function sm_array_remove ($array, $indice) {
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
}
#endregion

#region sm_options_page
if(!function_exists("sm_options_page")) {
	/**
	* Generated the admin option page and saves the configuration
	*/
	function sm_options_page() {
			//#type $sm_options array
			global $sm_options;
			//#type $sm_pages array
			global $sm_pages;
			
			//All output should go in this var which get printed at the end
			$message="";
			
			//Pressed Button: Rebuild Sitemap
			if(!empty($_POST["doRebuild"])) {
				$msg = sm_buildSitemap();
				
				if($msg && is_array($msg)) {
					foreach($msg AS $ms) {
						$message.=$ms . "<br /><br />";	
					}
				}
			}
			//Pressed Button: Update Config
    		else if (!empty($_POST['info_update'])) {		
				foreach($sm_options as $k=>$v) {
					//Check vor values and convert them into their types, based on the category they are in
					if(!isset($_POST[$k])) $_POST[$k]=""; // Empty string will get false on 2bool and 0 on 2float
					
					//Options of the category "Basic Settings" are boolean, except the filename
					if(substr($k,0,5)=="sm_b_") {
						if($k=="sm_b_filename") $sm_options[$k]=(string) $_POST[$k];
						else $sm_options[$k]=(bool) $_POST[$k];	
					//Options of the category "Includes" are boolean
					} else if(substr($k,0,6)=="sm_in_") {
						$sm_options[$k]=(bool) $_POST[$k];		
					//Options of the category "Change frequencies" are string
					} else if(substr($k,0,6)=="sm_cf_") {
						$sm_options[$k]=(string) $_POST[$k];		
					//Options of the category "Priorities" are float
					} else if(substr($k,0,6)=="sm_pr_") {
						$sm_options[$k]=(float) $_POST[$k];		
					}
				}
			
				if(update_option("sm_options",$sm_options)) $message.=__('Configuration updated', 'sitemap');
				else $message.=__('Error', 'sitemap');
				
			//Pressed Button: New Page
			} else if(!empty($_POST["sm_pages_new"])) {
				
				//Apply page changes from POST
				$sm_pages=sm_apply_pages();
				
				//Add a new page to the array with default values
				$p=new sm_page(true,"",0.0,"never",0);
				$sm_pages[count($sm_pages)]=$p;	
				$message.=__('A new page was added. Click on &quot;Save page changes&quot; to save your changes.','sitemap');
			
			//Pressed Button: Save pages	
			} else if(!empty($_POST["sm_pages_save"])) {
				
				//Apply page changes from POST
				$sm_pages=sm_apply_pages();
				
				//Store in the database
				if(update_option("sm_cpages",$sm_pages)) $message.=__("Pages saved",'sitemap');		
				else $message.=__("Error while saving pages",'sitemap');							
				
			//Pressed Button: Delete page
			} else if(!empty($_POST["sm_pages_del"])) {
				
				//Apply page changes from POST
				$sm_pages=sm_apply_pages();
			
				//the selected page is stored in value of the radio button
				$i=intval($_POST["sm_pages_action"]);
				
				//Remove the page from the array
				$sm_pages= sm_array_remove($sm_pages,$i);
				$message.=__('The page was deleted. Click on &quot;Save page changes&quot; to save your changes.','sitemap');
				
			//Pressed Button: Clear page Changes
			} else if(!empty($_POST["sm_pages_undo"])) {
				
				//In all other page changes, we do the sm_apply_pages functions. Here we don't, so we got the original settings from the db
				
				$message.=__('You changes have been cleared.','sitemap');
			}
			
			//Print out the message to the user, if any
			if($message!="") {
				?>
				<div class="updated"><strong><p><?php
				echo $message;
				?></p></strong></div><?php
			}
			?>

		<div class=wrap>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<h2><?php _e('Sitemap Generator', 'sitemap') ?> 2.5</h2>
				
				<!-- Rebuild Area -->
				<fieldset name="sm_rebuild" class="options">
					<legend><?php _e('Manual rebuild', 'sitemap') ?></legend>
					<?php _e('If you want to build the sitemap without editing a post, click on here!', 'sitemap') ?><br />
					<input type="submit" id="doRebuild" name="doRebuild" Value="<?php _e('Rebuild Sitemap','sitemap'); ?>" />	
				</fieldset>
				
				<!-- Pages area -->
				<fieldset name="sm_pages"  class="options">
					<legend><?php _e('Additional pages', 'sitemap') ?></legend>
					<?php 
					_e("Here you can specify files or URLs which should be included in the sitemap, but don't belong to your Blog/WordPress.<br />For example, if your domain is www.foo.com and your blog is located on www.foo.com/blog you might want to include your homepage at www.foo.com",'sitemap');
					echo "<ul><li>";
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
							if(count($sm_pages)>0) {
								$class="";
								for($i=0; $i<count($sm_pages); $i++) {
									$v=$sm_pages[$i];
									//#type $v sm_page
									$class = ('alternate' == $class) ? '' : 'alternate';
									echo "<input type=\"hidden\" name=\"sm_pages_mark[$i]\" value=\"true\" />";
									echo "<tr class=\"$class\">";
									echo "<td><input type=\"textbox\" name=\"sm_pages_ur[$i]\" style=\"width:95%\" value=\"" . $v->getUrl() . "\" /></td>";
									echo "<td width=\"150\"><select name=\"sm_pages_pr[$i]\" style=\"width:95%\">";
									echo sm_prio_names($v->getPriority());
									echo "</select></td>";
									echo "<td width=\"150\"><select name=\"sm_pages_cf[$i]\" style=\"width:95%\">";
									echo sm_freq_names($v->getChangeFreq());
									echo "</select></td>";
									echo "<td width=\"150\"><input type=\"textbox\" name=\"sm_pages_lm[$i]\" style=\"width:95%\" value=\"" . ($v->getLastMod()>0?date("Y-d-m",$v->getLastMod()):"") . "\" /></td>";
									echo "<td width=\"5\"><input type=\"radio\" name=\"sm_pages_action\" value=\"$i\" /></td>";
									echo "</tr>";																
								}
							} else {
								?><tr> 
									<td colspan="5"><?php _e('No pages defined.','sitemap') ?></td> 
								</tr><?php
							}
						?>
					</table>
					<div>
						<div style="float:left; width:50%">
							<input type="submit" name="sm_pages_new" value="<?php _e("Add new page",'sitemap'); ?>" />									
							<input type="submit" name="sm_pages_save" value="<?php _e("Save page changes",'sitemap'); ?>" />
							<input type="submit" name="sm_pages_undo" value="<?php _e("Undo all page changes",'sitemap'); ?>" />
						</div>
						<div style="width:50%; text-align:right; float:left;">
							<input type="submit" name="sm_pages_del" value="<?php _e("Delete marked page",'sitemap'); ?>" />
						</div>
					</div>
				</fieldset>
				
				<!-- Basic Options -->
				<fieldset name="sm_basic_options"  class="options">
					<legend><?php _e('Basic Options', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_b_auto_prio">
								<input type="checkbox" id="sm_b_auto_prio" name="sm_b_auto_prio" <?php echo (sm_go("sm_b_auto_prio")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Enable automatic priority calculation for posts based on comment count', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_debug">
								<input type="checkbox" id="sm_b_debug" name="sm_b_debug" <?php echo (sm_go("sm_b_debug")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Write debug comments', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_b_filename">
								<?php _e('Filename of the sitemap file', 'sitemap') ?>
								<input type="text" id="sm_b_filename" name="sm_b_filename" value="<?php echo sm_go("sm_b_filename"); ?>" />
							</label>
						</li>
						<li>
							<label for="sm_b_xml">
								<input type="checkbox" id="sm_b_xml" name="sm_b_xml" <?php echo (sm_go("sm_b_xml")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Write a normal XML file (your filename)', 'sitemap') ?>
							</label>
							<br /><?php _e('Current Path', 'sitemap') ?>: <?php echo sm_getXmlPath(); ?><br /><?php _e('Current URL', 'sitemap') ?>: <a href="<?php echo sm_getXmlUrl(); ?>"><?php echo sm_getXmlUrl(); ?></a>
						</li>
						<li>
							<label for="sm_b_gzip">
								<input type="checkbox" id="sm_b_gzip" name="sm_b_gzip" <?php if(function_exists("gzencode")) { echo (sm_go("sm_b_gzip")==true?"checked=\"checked\"":""); } else echo "disabled=\"disabled\"";  ?> />
								<?php _e('Write a gzipped file (your filename + .gz)', 'sitemap') ?>
							</label>
							<br /><?php _e('Current Path', 'sitemap') ?>: <?php echo sm_getZipPath(); ?><br /><?php _e('Current URL', 'sitemap') ?>: <a href="<?php echo sm_getZipUrl(); ?>"><?php echo sm_getZipUrl(); ?></a>
						</li>
						<li>
							<label for="sm_b_ping">
								<input type="checkbox" id="sm_b_ping" name="sm_b_ping" <?php echo (sm_go("sm_b_ping")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Auto-Ping Google Sitemaps', 'sitemap') ?><br />
								<?php _e('This option will automatically tell Google about changes.','sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				
				<!-- Includes -->	
				<fieldset name="sm_includes"  class="options">
					<legend><?php _e('Includings', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_in_home">
								<input type="checkbox" id="sm_in_home" name="sm_in_home"  <?php echo (sm_go("sm_in_home")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_posts">
								<input type="checkbox" id="sm_in_posts" name="sm_in_posts"  <?php echo (sm_go("sm_in_posts")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include posts', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_pages">
								<input type="checkbox" id="sm_in_pages" name="sm_in_pages"  <?php echo (sm_go("sm_in_pages")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include static pages', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_cats">
								<input type="checkbox" id="sm_in_cats" name="sm_in_cats"  <?php echo (sm_go("sm_in_cats")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_in_arch">
								<input type="checkbox" id="sm_in_arch" name="sm_in_arch"  <?php echo (sm_go("sm_in_arch")==true?"checked=\"checked\"":"") ?> />
								<?php _e('Include archives', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				
				<!-- Change frequencies -->
				<fieldset name="sm_change_frequencies"  class="options">
					<legend><?php _e('Change frequencies', 'sitemap') ?></legend>
					<b><?php _e('Note', 'sitemap') ?>:</b> 
					<?php _e('Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. It is also likely that crawlers will periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.', 'sitemap') ?>
					<ul>
						<li>
							<label for="sm_cf_home">
								<select id="sm_cf_home" name="sm_cf_home"><?php sm_freq_names(sm_go("sm_cf_home")); ?></select> 
								<?php _e('Homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_posts">
								<select id="sm_cf_posts" name="sm_cf_posts"><?php sm_freq_names(sm_go("sm_cf_posts")); ?></select> 
								<?php _e('Posts', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_pages">
								<select id="sm_cf_pages" name="sm_cf_pages"><?php sm_freq_names(sm_go("sm_cf_pages")); ?></select> 
								<?php _e('Static pages', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_cats">
								<select id="sm_cf_cats" name="sm_cf_cats"><?php sm_freq_names(sm_go("sm_cf_cats")); ?></select> 
								<?php _e('Categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_arch_curr">
								<select id="sm_cf_arch_curr" name="sm_cf_arch_curr"><?php sm_freq_names(sm_go("sm_cf_arch_curr")); ?></select> 
								<?php _e('The current archive of this month (Should be the same like your homepage)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_cf_arch_old">
								<select id="sm_cf_arch_old" name="sm_cf_arch_old"><?php sm_freq_names(sm_go("sm_cf_arch_old")); ?></select> 
								<?php _e('Older archives (Changes only if you edit an old post)', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				
				<!-- Priorities -->				
				<fieldset name="sm_priorities"  class="options">
					<legend><?php _e('Priorities', 'sitemap') ?></legend>
					<ul>
						<li>
							<label for="sm_pr_home">
								<select id="sm_pr_home" name="sm_pr_home"><?php sm_prio_names(sm_go("sm_pr_home")); ?></select> 
								<?php _e('Homepage', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_posts">
								<select id="sm_pr_posts" name="sm_pr_posts"><?php sm_prio_names(sm_go("sm_pr_posts")); ?></select> 
								<?php _e('Posts (If auto calculation is disabled)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_posts_min">
								<select id="sm_pr_posts_min" name="sm_pr_posts_min"><?php sm_prio_names(sm_go("sm_pr_posts_min")); ?></select> 
								<?php _e('Minimum post priority (Even if auto calculation is enabled)', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_pages">
								<select id="sm_pr_pages" name="sm_pr_pages"><?php sm_prio_names(sm_go("sm_pr_pages")); ?></select> 
								<?php _e('Static pages', 'sitemap'); ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_cats">
								<select id="sm_pr_cats" name="sm_pr_cats"><?php sm_prio_names(sm_go("sm_pr_cats")); ?></select> 
								<?php _e('Categories', 'sitemap') ?>
							</label>
						</li>
						<li>
							<label for="sm_pr_arch">
								<select id="sm_pr_arch" name="sm_pr_arch"><?php sm_prio_names(sm_go("sm_pr_arch")); ?></select> 
								<?php _e('Archives', 'sitemap') ?>
							</label>
						</li>
					</ul>
				</fieldset>
				<div class="submit"><input type="submit" name="info_update" value="<?php _e('Update options', 'sitemap') ?>" /></div>
				
				<fieldset class="options">
					<legend><?php _e('Informations and support', 'sitemap') ?></legend>
					<p><?php echo str_replace("%s","<a href=\"http://www.arnebrachhold.de/2005/06/05/google-sitemaps-generator-v2-final\">http://www.arnebrachhold.de/2005/06/05/google-sitemaps-generator-v2-final</a>",__("Check %s for updates and comment there if you have any problems / questions / suggestions.",'sitemap')); ?></p>
				</fieldset>
			</form>
		</div> <?php
	}
}
#endregion

#region sm_reg_admin  
if(!function_exists("sm_reg_admin")) {
	/**
	* Add the options page in the admin menu
	*/
	function sm_reg_admin() {
		if (function_exists('add_options_page')) {
			add_options_page('Sitemap Generator', 'Sitemap', 8, basename(__FILE__), 'sm_options_page');	
		}
	}
}
#endregion

/******** Sitemap Builder Helper functions ********/

#region sm_addUrl
if(!function_exists("sm_addUrl")) {
	/**
	Adds a url to the sitemap
	 
	@param $loc string The location (url) of the page
	@param $lastMod string THe last Modification time in ISO 8601 format
	@param $changeFreq string The change frequenty of the page, Valid values are "always", "hourly", "daily", "weekly", "monthly", "yearly" and "never".
	@param $priorty float The priority of the page, between 0.0 and 1.0
	 
	@return string The URL node
	*/
	function sm_addUrl($loc,$lastMod,$changeFreq="monthly",$priority=0.5) {
		global $sm_freq_names;
		$s="";
		$s.= "\t<url>\n";
		$s.= "\t\t<loc>$loc</loc>\n";
		if(!empty($lastMod) && $lastMod!="0000-00-00T00:00:00+00:00") $s.= "\t\t<lastmod>$lastMod</lastmod>\n";
		if(!empty($changeFreq) && in_array($changeFreq,$sm_freq_names)) $s.= "\t\t<changefreq>$changeFreq</changefreq>\n";	
		if($priority!==false && $priority!=="") $s.= "\t\t<priority>$priority</priority>\n";
		$s.= "\t</url>\n";	
		return $s;
	}
}
#endregion

#region sm_getComments
if(!function_exists("sm_getComments")) {
	/**
	* Retrieves the number of comments of a post in a asso. array
	* The key is the postID, the value the number of comments
	*
	* @return array An array with postIDs and their comment count
	*/
	function sm_getComments() {
		global $wpdb;
		$comments=array();

		//Query comments and add them into the array
		$commentRes=$wpdb->get_results("SELECT `comment_post_ID` as `post_id`, COUNT(comment_ID) as `comment_count`, comment_approved FROM `" . $wpdb->comments . "` GROUP BY `comment_post_ID`");
		if($commentRes) {
			foreach($commentRes as $comment) {
				$comments[$comment->post_id]=$comment->comment_count;
			}	
		}
		return $comments;
	}
}
#endregion

#region sm_countComments
if(!function_exists("sm_countComments")) {
	/**
	* Calculates the full number of comments from an sm_getComments() generated array
	* 
	* @param $comments array The Array with posts and c0mment count
	* @see sm_getComments
	* @return The full number of comments
	*/ 
	function sm_countComments($comments) {
		$commentCount=0;
		foreach($comments AS $k=>$v) {
			$commentCount+=$v;	
		}	
		return $commentCount;
	}
}
#endregion

#region sm_buildSitemap
if(!function_exists("sm_buildSitemap")) {
	/**
	Builds the sitemap and writes it into a xml file.
	
	@return array An array with messages such as failed writes etc.
	*/
	function sm_buildSitemap() {
		global $wpdb, $sm_pages;
		
		//Return messages to the user in frontend
		$messages=array();
		
		//Debug mode?
		$debug=sm_go("sm_b_debug");
		
		//Content of the XML file
		$s='<?xml version="1.0" encoding="UTF-8"' . '?' . '>'. "\n";
		
		//WordPress powered... and me! :D
		$s.="<!-- generator=\"wordpress/" . get_bloginfo('version') . "\" -->\n";
		$s.="<!-- sitemap-generator-url=\"http://www.arnebrachhold.de\" sitemap-generator-version=\"2.5\"  -->\n";
		
		//All comments as an asso. Array (postID=>commentCount)
		$comments=(sm_go("sm_b_auto_prio")?sm_getComments():array());
		
		//Full number of comments
		$commentCount=sm_countComments($comments);
		
		if($debug && sm_go("sm_b_auto_prio")) {
			$s.="<!-- Debug: Total comment count: " . $commentCount . " -->\n";		
		}
		
		//Go XML!
		$s.='<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'. "\n";
		
		//Add the home page
		if(sm_go("sm_in_home")) {
			$s.=sm_addUrl(get_bloginfo('url'),mysql2date('Y-m-d\TH:i:s+00:00', get_lastpostmodified('GMT'), false),sm_go("sm_cf_home"),sm_go("sm_pr_home"));
		}
		
		//Add the posts
		if(sm_go("sm_in_posts")) {
			if($debug) $s.="<!-- Debug: Start Postings -->\n";	

			//Retrieve all posts and static pages (if enabled)
			$postRes=$wpdb->get_results("SELECT `ID` ,`post_modified`, `post_date`, `post_status` FROM `" . $wpdb->posts . "` WHERE post_status = 'publish' " . (sm_go("sm_in_pages")?"OR post_status='static'":"") . " ORDER BY post_modified DESC");

			$minPrio=sm_go("sm_pr_posts_min");
			
			if($postRes) {
				//Count of all posts
				$postCount=count($postRes);

				//Cycle through all posts and add them
				foreach($postRes as $post) {
					//Default Priority if auto calc is disabled
					$prio=0;
					if($post->post_status=="static") {
						//Priority for static pages
						$prio=sm_go("sm_pr_pages");
					} else {
						//Priority for normal posts
						$prio=sm_go("sm_pr_posts");
					}
					
					//If priority calc is enabled, calc (but only for posts)!
					if(sm_go("sm_b_auto_prio") && $post->post_status!="static") {
						//Comment count for this post
						$cmtcnt=(array_key_exists($post->ID,$comments)?$comments[$post->ID]:0);
						
						//Percentage of comments for this post
						$prio=($cmtcnt>0&&$commentCount>0?round(($cmtcnt*100/$commentCount)/100,1):0);
						
						if($debug) {
							$s.="<!-- Debug: Priority report of postID " . $post->ID . ": Comments: " . $cmtcnt . " of " . $commentCount . " = " . $prio . " points -->\n"; 						
						}
					}	
					
					if($post->post_status!="static" && !empty($minPrio) && $prio<$minPrio) {
						$prio=sm_go("sm_pr_posts_min");
					}
					
					//Add it
					$s.=sm_addUrl(get_permalink($post->ID),mysql2date('Y-m-d\TH:i:s+00:00', (!empty($post->post_modified) && $post->post_modified!='0000-00-00 00:00:00'?$post->post_modified:$post->post_date), false),sm_go(($post->post_status=="static"?"sm_cf_posts":"sm_cf_pages")),$prio);
				}
			}
			if($debug) $s.="<!-- Debug: End Postings -->\n";	
		}
		
		//Add the cats
		if(sm_go("sm_in_cats")) {
			if($debug) $s.="<!-- Debug: Start Cats -->\n";	
			
			//Add Categories... Big thanx to Rodney Shupe (http://www.shupe.ca) for the SQL
			//$catsRes=$wpdb->get_results("SELECT cat_ID AS ID FROM $wpdb->categories");
			$catsRes=$wpdb->get_results("SELECT cat_ID AS ID, MAX(post_modified) AS last_mod FROM `" . $wpdb->posts . "` p LEFT JOIN `" . $wpdb->post2cat . "` pc ON p.ID = pc.post_id LEFT JOIN `" . $wpdb->categories . "` c ON pc.category_id = c.cat_ID WHERE post_status = 'publish' GROUP BY cat_ID");
			if($catsRes) {
				foreach($catsRes as $cat) {
					$s.=sm_addUrl(get_category_link($cat->ID),mysql2date('Y-m-d\TH:i:s+00:00', $cat->last_mod, false),sm_go("sm_cf_cats"),sm_go("sm_pr_cats"));
				}	
			}
			if($debug) $s.="<!-- Debug: End Cats -->\n";	
		}
		//Add the archives
		if(sm_go("sm_in_arch")) {
			if($debug) $s.="<!-- Debug: Start Archive -->\n";	
			$now = current_time('mysql');
			//Add archives...  Big thanx to Rodney Shupe (http://www.shupe.ca) for the SQL
			$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, MAX(post_date) as last_mod, count(ID) as posts FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");
			//$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts WHERE post_date < '$now' AND post_status = 'publish' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");
			if ($arcresults) {
				foreach ($arcresults as $arcresult) {
					
					$url  = get_month_link($arcresult->year,   $arcresult->month);
					$changeFreq="";
					
					//Archive is the current one
					if($arcresult->month==date("n") && $arcresult->year==date("Y")) {
						$changeFreq=sm_go("sm_cf_arch_curr");	
					} else { // Archive is older
						$changeFreq=sm_go("sm_cf_arch_old");	
					}
					
					$s.=sm_addUrl($url,mysql2date('Y-m-d\TH:i:s+00:00', $arcresult->last_mod, false),$changeFreq,sm_go("sm_pr_arch"));				
				}
			}
			if($debug) $s.="<!-- Debug: End Archive -->\n";	
		}
		
		//Add the custom pages
		if($debug) $s.="<!-- Debug: Start Custom Pages -->\n";	
		if($sm_pages && is_array($sm_pages) && count($sm_pages)>0) {
			//#type $page sm_page
			foreach($sm_pages AS $page) {
				$s.=sm_addUrl($page->GetUrl(),($page->getLastMod()>0?date('Y-m-d\TH:i:s+00:00',$page->getLastMod()):0),$page->getChangeFreq(),$page->getPriority());
			}	
		}
		if($debug) $s.="<!-- Debug: End Custom Pages -->\n";	
		
		$s.="</urlset>";
		
		$pingUrl="";
		
		//Write normal sitemap file
		if(sm_go("sm_b_xml")) {
			$fileName = sm_getXmlPath();
			$f=@fopen($fileName,"w");
			if($f) {
				if(fwrite($f,$s)) {
					$pingUrl=sm_getXmlUrl();
					$messages[count($messages)]=__("Successfully built sitemap file:",'sitemap') . "<br />" . "- " .  __("URL:",'sitemap') . " <a href=\"" . sm_getXmlUrl() . "\">" . sm_getXmlUrl() . "</a><br />- " . __("Path:",'sitemap') . " " . sm_getXmlPath();
				}
				fclose($f);	
			} else {
				$messages[count($messages)]=str_replace("%s",sm_getXmlPath(),__("Could not write into %s",'sitemap'));
			}
		}
		
		//Write gzipped sitemap file
		if(sm_go("sm_b_gzip")===true && function_exists("gzencode")) {
			$fileName = sm_getZipPath();
			$f=@fopen($fileName,"w");
			if($f) {
				if(fwrite($f,gzencode($s))) {
					$pingUrl=sm_getZipUrl();
					$messages[count($messages)]=__("Successfully built gzipped sitemap file:",'sitemap') . "<br />" . "- " .  __("URL:",'sitemap') . " <a href=\"" . sm_getZipUrl() . "\">" . sm_getZipUrl() . "</a><br />- " . __("Path:",'sitemap') . " " . sm_getZipPath();
				}
				fclose($f);	
			} else {
				$messages[count($messages)]=str_replace("%s",sm_getZipPath(),__("Could not write into %s",'sitemap'));
			}
		}
		
		//Ping Google
		if(sm_go("sm_b_ping") && $pingUrl!="") {
			$pingUrl="http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($pingUrl);
			$pingres=@wp_remote_fopen($pingUrl);

			if($pingres==NULL || $pingres===false) {
				$messages[count($messages)]=str_replace("%s","<a href=\"$pingUrl\">$pingUrl</a>",__("Could not ping to Google at %s",'sitemap'));			
			} else {
				$messages[count($messages)]=str_replace("%s","<a href=\"$pingUrl\">$pingUrl</a>",__("Successfully pinged Google at %s",'sitemap'));
			}
		}
		
		//done...
		return $messages;
	}
}
#endregion

/******** Other Stuff ********/

#region Register to WordPress API
//Loading language file...
//load_plugin_textdomain('sitemap');
//Hmm, doesn't work if the plugin file has its own directory.
//Let's make it our way... load_plugin_textdomain() searches only in the wp-content/plugins dir.
$sm_locale = get_locale();
$sm_mofile = dirname(__FILE__) . "/sitemap-$sm_locale.mo";
load_textdomain('sitemap', $sm_mofile);

//Register the sitemap creator to wordpress...
add_action('admin_menu', 'sm_reg_admin');

//Register to various events... @WordPress Dev Team: I wish me a 'public_content_changed' action :)

//If a new post gets published
add_action('publish_post', 'sm_buildSitemap');

//Existing post gets edited (published or not)
add_action('edit_post', 'sm_buildSitemap'); 

//Existing posts gets deleted (published or not)
add_action('delete_post', 'sm_buildSitemap');
#endregion

?>
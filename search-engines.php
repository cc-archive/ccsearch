<?php
/*
* Creative Commons has made the contents of this file
* available under a CC-GNU-GPL license:
*
* http://creativecommons.org/licenses/GPL/2.0/
*
* A copy of the full license can be found as part of this
* distribution in the file LICENSE.TXT.
* 
* You may use the ccHost software in accordance with the
* terms of that license. You agree that you are solely 
* responsible for your use of the ccHost software and you
* represent and warrant to Creative Commons that your use
* of the ccHost software will comply with the CC-GNU-GPL.
*
* $Id: cc-language.php 4284 2006-09-20 19:12:03Z fourstones $
*
*/

/**
* //TODO: fix these
* @package cchost
* @subpackage lang
*/

/**
 *
 
 TODO: "fix" blip.tv's algorithm.  it's ported correctly, but i think it has a bug:
 case: ($comm = true, $deriv = false) returns same as case: (both true)
 they return 1,6,7,2
 --latest: i think i fixed it, but i should have someoen double-check this.
 original algorithm from search.js:
		case "blip":
			rights = "license=1,6,7"; // by,by-sa,pd
			if (!id('comm').checked && !id('deriv').checked) {
				rights += ",2,3,4,5"; // by-nd,by-nc-nd,by-nc-,by-nc-sa
			} else if (id('comm').checked) {
				rights += ",2"; // by-nd
			} else { // deriv must be checked
				rights += ",4,5"; // by-nc,by-nc-sa
			}
			break;
 
 
 * TODO: fill these in
 * This is the short Description for the Class
 *
 * This is the long description for the Class
 *
 * TODO: update this
 * @author		Karl Heinz Marbaise <khmarbaise@gmx.de>
 * @copyright	(c) 2003 by Karl Heinz Marbaise
 * @version		$Id$
 * @package		Package
 * @subpackage	SubPackage
 * @see			??
 */




//NOTE TO SELF:
//define a var here (or a const, whatever)
//and then set $_engineList to point to a it _within the constructor_
//okay, i tried like 3 times to do this.  no luck.  php is being annoying.
/*
    $DEFAULT_ENGINE_LIST = array(
            'google' => "GoogleSearch",
            'yahoo' => "YahooSearch",
            'flickr' => "FlickrSearch",
            'blip' => "BlipSearch",
            'jamendo' => "JamendoSearch",
            'spin' => "SpinSearch"
        );
*/

define('DEFAULT_ENGINE', 'google');



class SetOfSearchEngines
{
    /**
     * This is a CC Language object (or preferably a reference to one).
     * @var		mixed
     * @access	private
     */
     
     /*
     const DEFAULT_ENGINE_LIST = array(
            'google' => new GoogleSearch(),
            'yahoo' => new YahooSearch(),
            'flickr' => new FlickrSearch(),
            'blip' => new BlipSearch(),
            'jamendo' => new JamendoSearch(),
            'spin' => new SpinSearch()
        );
    */
    
    var $_cc_lang;
    
    var $_original_language;

    //array of references to individual SearchEngine objects
    var $_engineList;
    
    public $_current_engine; //reference to an engine object
    //used for these things:
    //*figure out which query constructing algorithm to use
    //*figure out which radio select to put as selected in the display
    
    function SetOfSearchEngines($cc_lang){
        $this->_cc_lang = $cc_lang;
        $this->_original_language = $cc_lang->GetLanguage(); 
        if(DEBUG) echo "<p>Current language: " . $this->_original_language . "</p>\n";
        
        
        //$this->_engineList = $DEFAULT_ENGINE_LIST;
        /*
        $this->_engineList = array(
            new GoogleSearch(),
            new YahooSearch(),
            new FlickrSearch(),
            new BlipSearch(),
            new JamendoSearch(),
            new SpinSearch()
        );
        */
        
        
        $this->_engineList = array(
            'google' => new GoogleSearch(),
            'yahoo' => new YahooSearch(),
            'flickr' => new FlickrSearch(),
            'blip' => new BlipSearch(),
            'jamendo' => new JamendoSearch(),
            'spin' => new SpinSearch()
        );
        $this->_current_engine = $this->_engineList[DEFAULT_ENGINE];
        
        //$this->_engineList = $defaultEngineList;
    }
    
    function setCurrentEngine($choice_from_post){
        //$this->_current_engine = "default"
        //TODO: create default case
        if(!$choice_from_post){
        
        }
        foreach($this->_engineList as $engineID => $engineObj){
            if($choice_from_post == $engineID){
                 $this->_current_engine = $engineObj;
            }
        }
    }

    function showSelectRadios(){
        foreach($this->_engineList as $engineID => $engineObj){
                $engineObj->showSelectRadio(($engineObj == $this->_current_engine));
        }
    }

} //end SetOfSearchEngines


class SearchEngine{
    var $_id;
    var $_human_readable_name;
    var $_image;
    var $_search_type;
    var $_image_is_png;
    
    function SearchEngine($id, $human_readable_name, $search_type, $image, $image_is_png){
        $this->_id = $id;
        $this->_human_readable_name = $human_readable_name;
        $this->_image = $image;
        $this->_search_type = $search_type;
        $this->_image_is_png = $image_is_png;
    }

    function createQueryString(){}
    
    function showSelectRadio($checked){
        //moved the actual function body to be with the rest of the html output (currently index.php)
        showEngineRadio($this->_id, $checked, $this->_image, $this->_image_is_png, $this->_search_type);
        
    /*
        ?>
            
            <input type="radio" name="engine" id="<?php echo $this->_id ?>" value="<?php echo $this->_id ?>" class="inactive" <?php if($checked) echo 'checked="checked"' ?>/><label for="<?php echo $this->_id ?>"><img src="<?php echo $this->_image ?>" border="0" <?php if ($this->_image_is_png) echo 'class="png"' ?> alt="<?php echo _($this->_search_type) ?>" /></label>
            
            
        <?php
    */
    }

}












class GoogleSearch extends SearchEngine{
    var $_id = "google";
    var $_human_readable_name = "Google";
    var $_search_type = "Web Search";
    var $_image = "images/cc-google.gif";
    var $_image_is_png = false;
    
    function GoogleSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
        //.-(cc_noncommercial|cc_nonderived)
        $rights = "as_rights=(cc_publicdomain|cc_attribute|cc_sharealike";
        if(!$comm){
            $rights .= "|cc_noncommercial";
        }
        if(!$deriv){
            $rights .= "|cc_nonderived";
        }
        $rights .= ")";
        
        if($comm || $deriv){
            $rights .= ".-(";
		    if ($comm) {
			    $rights .= "cc_noncommercial";
		    }
		    if ($deriv) {
			    if($comm) $rights .= "|";
			    $rights .= "cc_nonderived";
		    }
            $rights .= ")";
        }
        $url = 'http://google.com/search?' . $rights . '&q=' . $query;
        
        return $url;
    
    }

}


class YahooSearch extends SearchEngine{
    var $_id = "yahoo";
    var $_human_readable_name = "Yahoo";
    var $_search_type = "Web Search";
    var $_image = "images/cc-yahoo.gif";
    var $_image_is_png = false;
    
    function YahooSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
            if($comm || $deriv){
			    $rights = "&";
			    if ($comm) {
				    $rights .= "ccs=c&";
			    }
			    if ($deriv) {
				    $rights .= "ccs=e";
			    }
			}
			
			$url = 'http://search.yahoo.com/search?cc=1&p=' . $query . $rights;
			
			return $url;
    }

}

class JamendoSearch extends SearchEngine{
    var $_id = "jamendo";
    var $_human_readable_name = "Jamendo";
    var $_search_type = "Music Search";
    var $_image = "images/cc-jamendo.png";
    var $_image_is_png = true;
    
    function JamendoSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
        //handle rights
        $rights = "";
		//note: apparently they don't check the values of these vars, they just check to see if they're defined
		//so uncommenting the else's will cause jamendo to think you always want derivs and commercial
		if ($deriv) {
			$rights .= "license_minrights_d=on&";
		}
		/*else{
			rights += "license_minrights_d=off&";
		}*/
		if ($comm) {
			$rights .= "license_minrights_c=on";
		}
		/*else{
			rights += "license_minrights_c=off";
		}*/
        
	    $url ='http://www.jamendo.com/tag/' . $query . '?' . $rights . '&location_country=all&order=rating_desc';
	    return $url;
    
    }

}


class FlickrSearch extends SearchEngine{
    var $_id = "flickr";
    var $_human_readable_name = "Flickr";
    var $_search_type = "Image Search";
    var $_image = "images/cc-flickr.png";
    var $_image_is_png = true;
    
    function FlickrSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
    
    
        if($comm || $deriv){
			$rights = "l=";
			if ($comm) {
				$rights .= "comm";
			}
			if ($deriv) {
				$rights .= "deriv";
			}
        }
        else{
            $rights = "l=cc";
        }

        $url = 'http://flickr.com/search/?' . $rights . '&q=' . $query;
		return $url;
    }

}





class BlipSearch extends SearchEngine{
    var $_id = "blip";
    var $_human_readable_name = "Blip.tv";
    var $_search_type = "Video Search";
    var $_image = "images/cc-blip.png";
    var $_image_is_png = true;
    
    function BlipSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
		$rights = "license=1,6,7"; // by,by-sa,pd
		if (!$comm && !$deriv) {
			$rights .= ",2,3,4,5"; // by-nd,by-nc-nd,by-nc-,by-nc-sa
		} else if ($comm && !$deriv) {
			$rights .= ",2"; // by-nd
		} else if(!$comm && $deriv){ // deriv must be checked
			$rights .= ",4,5"; // by-nc,by-nc-sa
		}
		//else: case: both true
		//we just leave it at by, by-sa, pd
		
	    $url = 'http://blip.tv/posts/view/?q=' . $query . '&section=/posts/view&sort=popularity&' . $rights;
	    return $url;
    
    }
    /*
http://blip.tv/posts/view/?q=flowers&section=/posts/view&sort=popularity&license=1,6,7,2
http://blip.tv/posts/view/?q=flowers&section=/posts/view&sort=popularity&license=1,6,7,2
    */

}


class SpinSearch extends SearchEngine{
    var $_id = "spin";
    var $_human_readable_name = "Spin Xpress";
    var $_search_type = "Media Search";
    var $_image = "images/cc-spinxpress.png";
    var $_image_is_png = true;
    
    function SpinSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
		$rights = "_license=";
		if(!$comm && !$deriv) {
			$rights .= "11"; // by-nd,by-nc-nd,by-nc-,by-nc-sa
		} else if ($comm && !$deriv) {
			$rights .= "8"; // by-nd
		} else if (!$comm && $deriv) {
			$rights .= "9";
		} else { 
			$rights .= "10"; // by-nc,by-nc-sa
		}
		
		$url = 'http://www.spinxpress.com/getmedia' . $rights . '_searchwords=' . $query;
        
        return $url;
    }

}


/*
id="blip" class="inactive"><a href="#" onclick="setEngine('blip')" title="<?php echo _('Video Search') ?>"><img src="images/cc-blip.png" border="0" class="png" width="42" height="20" alt="<?php echo _('blip.tv') ?>" /></a></li>

id="owlmm" class="inactive"><a href="#" onclick="setEngine('owlmm')" title="<?php echo _('Music Search') ?>"><img src="images/cc-owlmm.png" border="0" class="png" /></a></li>

id="spin" class="inactive"><a href="#" onclick="setEngine('spin')" title="<?php echo _('Media Search') ?>"><img src="images/cc-spinxpress.png" border="0" class="png" /></a></li>

id="jamendo" class="inactive"><a href="#" onclick="setEngine('jamendo')" title="<?php echo _('Music Search') ?>"><img src="images/cc-jamendo.png" border="0" class="png" alt="<?php echo _('jamendo') ?>" /></a></li>

id="ccmixter" class="inactive"><a href="#" onclick="setEngine('ccmixter')" title="<?php echo _('Music Search') ?>"><img src="images/cc-ccmixter.png" border="0" class="png" alt="<?php echo _('ccMixter') ?>" /></a></li>

id="openclipart" class="inactive"><a href="#" onclick="setEngine('openclipart')" title="<?php echo _('Clip Art Search') ?>"><img src="#" border="0" class="png" alt="<?php echo _('Open Clip Art Library') ?>" /></a></li>
*/


?>

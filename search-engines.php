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


/*

 TODO: "fix" blip.tv's algorithm.  it's ported correctly, but i think it has a bug:
 case: ($comm = true, $deriv = false) returns same as case: (both true)
 they return 1,6,7,2
 --latest: i think i fixed it, but i should have someone double-check this.
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
*/

/**
* @package cchost
* @subpackage lang
*/

/**
 *
 * This is the short Description for the Class
 *
 * This is the long description for the Class
 *
 * @author		Karl Heinz Marbaise <khmarbaise@gmx.de>
 * @copyright	(c) 2003 by Karl Heinz Marbaise
 * @version		$Id$
 * @package		Package
 * @subpackage	SubPackage
 * @see			??
 */




//TODO:
//define a var here (or a const, whatever)
//and then set $_engineList to point to a it _within the constructor_
//okay, i tried like 3 times to do this.  no luck.  php is being annoying.
//perhaps the best solution to this issue is to make all the search engine classes totally static,
//never instantiating objects of them.  this would probably be better design, because i'm not using any instance variables.
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

//the search engine that will be selected when the user sees the form for the first time
//should be the "$id" of a search engine
//we're going to assume that this is a member of SetOfSearchEngines->_engineList, so make sure that it is
define('DEFAULT_ENGINE', 'google');




//class that holds a set of search engines (actually, an array of references to searchEngine objects)
//index.php instantiates one object of this class
//it is used to display the radio input options in the search form
//it's also used to access searchEngine objects in order to build query strings
class SetOfSearchEngines
{
    //This is a CC Language object (or preferably a reference to one).
    var $_cc_lang;
    
    var $_original_language;
    
    //array of references to individual SearchEngine objects
    //populated in the constructor
    var $_engineList;
    
    //reference to an engine object
    //used for these things:
    //*figure out which query constructing algorithm to use
    //*figure out which radio select to put as selected in the displayed form
    public $_current_engine;
    
    //constructor
    //populate all the important instance variables
    function SetOfSearchEngines($cc_lang){
        $this->_cc_lang = $cc_lang;
        $this->_original_language = $cc_lang->GetLanguage(); 
        if(DEBUG) echo "<p>Current language: " . $this->_original_language . "</p>\n";
        
        //if i could figure out how to define the default engine list in a constant, i would do this here:
        //$this->_engineList = $DEFAULT_ENGINE_LIST;
        //since i can't, i'll just populate it by hand
        $this->_engineList = array(
            'google' => new GoogleSearch(),
            'yahoo' => new YahooSearch(),
            'flickr' => new FlickrSearch(),
            'blip' => new BlipSearch(),
            'jamendo' => new JamendoSearch(),
            'spin' => new SpinSearch()
        );
        
        //set the default engine
        $this->_current_engine = $this->grabFromEngineList(DEFAULT_ENGINE);
        
    }
    
    //helper function to grab a reference to an engine object from the engine list
    //this is useful for two reasons:
    //*allows us to handle the case where we're trying to get something from the list which isn't there
    //**give them a default value
    //*abstracts the data structure holding the engine list (currently an array)
    function grabFromEngineList($engineID){
        //if it's in the array, just return it
        if(isset($this->_engineList[$engineID]))
            return $this->_engineList[$engineID];
        //if it's not, give them the default value
        else
            if(DEBUG) echo "grabFromEngineList: failed to grab '$engineID' from the engine list, so using a default value";
            return $this->_engineList[DEFAULT_ENGINE];
    }
        
    //set the current engine (no matter what it was previously)
    //input: an engine ID (string)
    function setCurrentEngine($choice_from_post){
        $this->_current_engine = $this->grabFromEngineList($choice_from_post);
    }
    
    //show a radio input button for each search engine
    //used in the search form
    //through a seemingly absurd amount of modularity, this calls a function in SearchEngine,
    //which calls a function in index.php
    //the function in index.php (showEngineRadio()) contains the actual markup.
    //this way, all the markup is in one file (yay)
    function showSelectRadios(){
        foreach($this->_engineList as $engineID => $engineObj){
                $engineObj->showSelectRadio(($engineObj == $this->_current_engine));
        }
    }

} //end SetOfSearchEngines


//abstract class for a search engine
//this acts mostly as an interface that each search engine implements
class SearchEngine{
    //each search engine has its own values for each of these
    //they are more like constants than they are variables
    //(code could probably be updated to better reflect this)
    //(the individual search engine classes could perhaps be static)
    var $_id;
    var $_human_readable_name;
    var $_image;
    var $_search_type;
    var $_image_is_png; //boolean that's true 
    
    function SearchEngine($id, $human_readable_name, $search_type, $image, $image_is_png){
        $this->_id = $id;
        $this->_human_readable_name = $human_readable_name;
        $this->_image = $image;
        $this->_search_type = $search_type;
        $this->_image_is_png = $image_is_png;
    }

    //implemented in each individual search engine
    //given a query, and other form inputs, computes the url that the browser redirects to
    //ie: http://www.google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike).-(cc_noncommercial|cc_nonderived)&q=funtime
    function createQueryString(){}
    
    //show a radio input button for a search engine
    //includes image, 
    //used in the search form
    function showSelectRadio($checked){
        //moved the actual function body to be with the rest of the html output (currently index.php)
        showEngineRadio($this->_id, $this->_human_readable_name, $checked, $this->_image, $this->_image_is_png, $this->_search_type);
    }

} //end SearchEngine








//--------------------------------------------------
//BEGIN INDIVIDUAL SEARCH ENGINE CLASSES------------
//--------------------------------------------------



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


class OwlSearch extends SearchEngine{
    var $_id = "owl";
    var $_human_readable_name = "Owl Music Search";
    var $_search_type = "Music Search";
    var $_image = "images/cc-owlmm.png";
    var $_image_is_png = true;
    
    function OwlSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
        if($comm || $deriv){
	        $rights = "license_type=";
		    if ($comm) {
			    $rights .= "comm";
		    }
		    if ($deriv) {
			    $rights .= "deriv";
		    }
		}
		else{
		    $rights = "license_type=cc";
		}
        $url = 'http://www.owlmm.com/?query_source=CC&' . $rights . '&q=' . $query;
        return $url;
    }

}



class CCMixterSearch extends SearchEngine{
    var $_id = "ccmixter";
    var $_human_readable_name = "ccMixter";
    var $_search_type = "Music Search";
    var $_image = "images/cc-ccmixter.png";
    var $_image_is_png = true;
    
    function CCMixterSearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
	
		$rights = "";
		// everything on ccmixter permits derivs
		if ($comm) {
			$rights .= "+attribution";
		}
	    $url = 'http://ccmixter.org/media/tags/' . $query . $rights;
        
        return $url;
    }

}

class OCASearch extends SearchEngine{
    var $_id = "oca";
    var $_human_readable_name = "Open Clip Art Library";
    var $_search_type = "Clip Art Search";
    var $_image = "";
    var $_image_is_png = true;
    
    function OCASearch(){
        // start by calling parent (aka super) constructor
        // it's just good practice
        parent::SearchEngine($this->_id, $this->_human_readable_name, $this->_search_type, $this->_image, $this->_image_is_png);
    }

    function createQueryString($deriv, $comm, $query){
        $rights = "+publicdomain";
        // everything on ocal is pd 
		$url = 'http://openclipart.org/cchost/media/tags/' . $query . $rights;
        
        return $url;
    }

}


//--------------------------------------------------
////ENDINDIVIDUAL SEARCH ENGINE CLASSES------------
//--------------------------------------------------

?>

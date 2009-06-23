<?php
/**
 * Creative Commons has made the contents of this file
 * available under a CC-GNU-GPL license:
 *
 * http://creativecommons.org/licenses/GPL/2.0/
 *
 * A copy of the full license can be found as part of this
 * distribution in the file COPYING.
 * 
 * You may use the ccSearch software in accordance with the
 * terms of that license. You agree that you are solely 
 * responsible for your use of the ccSearch software and you
 * represent and warrant to Creative Commons that your use
 * of the ccSearch software will comply with the CC-GNU-GPL.
 *
 * Copyright 2006, Creative Commons.
 *
 * $Id: index.php 4296 2006-09-21 01:45:18Z kidproto $
 *
 * Creative Commons Search Interface
 *
 */

//TODO: figure out if i can get rid of the <img id ="stat" thing in the footer
//this might be some kind of ie hack?

$use_i18n = true;

require_once('cc-defines.php');
require_once('cc-language.php');
require_once('cc-language-ui.php');
require_once('search-engines.php');

//note: cookie gets re-set every time the page is visited.
//meaning, the cookie lasts forever as long as they keep coming back within the time defined below
define(COOKIE_LIFETIME, 2592000); // 2592000 = 60*60*24*30 = 30 days (~1 month)

//start sessions
session_start();

//i18n -- TRANSLATION STUFF-------------------
if ($use_i18n) {

  // This nastiness handles session storage 
  $cc_lang = &$_SESSION['lang'];
  /*if (DEBUG) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
  } */

  if ( ! isset($cc_lang) || isset($_REQUEST['update'] ) ) {
    $cc_lang = new CCLanguage($_REQUEST['lang']);
      if (DEBUG) echo "<p>created new object</p>";
  }
  else 
  {
      if (DEBUG) echo "<p>Using session language</p>";

      if ( isset($_REQUEST['localepref']) ) {
          $cc_lang->SetLocalePref($_REQUEST['localepref']);
          if (DEBUG) echo "<p>set new locale pref</p>";
      }

      if ( isset($_REQUEST['lang']) ) {
          $cc_lang->SetLanguage($_REQUEST['lang']);
          if (DEBUG) echo "<p>set new language</p>";
      }
  }

  $cc_lang->Init();
  $cc_lang_help = 
      new CCLanguageUIHelp(
          "http://translate.creativecommons.org/projects/ccsearch",
          _("Help Translate"));
  $cc_lang_selector = 
      new CCLanguageUISelector(&$cc_lang, 
                               "<div id=\"language_selector\">", 
                               $cc_lang_help->get('_text') . "</div>");

}

//init the object holding the search engines
$engines = new SetOfSearchEngines($cc_lang);

//associative array that holds the search info
//maybe add language here later? (if we plan to pass the language to the search provider, which we may not)
$search['query'];
$search['engine']; //an engine 'id'.  so it's a string, not a reference to an object
$search['deriv'];
$search['comm'];

//the default values (what the form will look like when someone visits the page for the first time)
$search_default['query'] = _("Enter Search query");
$search_default['engine'] = DEFAULT_ENGINE; //a default engine is define()d in search-engines.php
$search_default['deriv'] = true;
$search_default['comm'] = true;

//returns the associative array described above
//favors sessions over cookies (more reliable, and continue to work when cookies are off)
function getSearchFromCookieAndSession(){
    $search['query'];
    $search['engine'];
    if($_SESSION['search']){
        $search = $_SESSION['search'];
    }
    else if($_COOKIE['QueryAndEngine']){
        $search = unserialize($_COOKIE['QueryAndEngine']);
    }
    else{
        $search = NULL;
    }
    return $search;
}

//same as above, but just gets you the engine value
//returns an engine id (string)
function getEngineFromCookieAndSession(){
    if(isset($_SESSION['search']['engine'])){
        return $_SESSION['search']['engine'];
    }
    else if($_COOKIE['QueryAndEngine']){
        $fromCookie = unserialize($_COOKIE['QueryAndEngine']);
        return $fromCookie['engine'];
    }
    else{
        return false;
    }
}

//save the $search info (query, engine, and other form values) in cookie and session
//we might want to call this after getting the info from a mix of post and session and cookie
//which seems redundant, but makes sure that the info is the same across the board
function setQueryAndEngineInCookieAndSession($search){
    $_SESSION['search'] = $search;
    setCookie("QueryAndEngine", serialize($search), COOKIE_LIFETIME);
}

//takes a search (query, engine, other form values [comm, deriv])
//redirects user to the corresponding engine, passing the query in the url
//uses $engines->_current_engine->createQueryString() to construct the url
function sendThemOnTheirWay($search){
    global $engines;
    $engines->setCurrentEngine($search['engine']);
    $queryStr = $engines->_current_engine->createQueryString($search['deriv'], $search['comm'], $search['query']);
    header("Location: " . $queryStr);
    exit();
}

//simple helper function that grabs the useful search info from post and saves it in a nice array
function grabSearchFromPost(){
    $search['query'] = $_POST['q'];
    $search['deriv'] = $_POST['deriv'];
    $search['comm'] = $_POST['comm'];
    $search['engine'] = $_POST['engine'];
    return $search;
}

//MAIN LOGIC-------------------------------------------------------
//if they submitted a search query
if(isset($_POST['q'])){
    $search = grabSearchFromPost($engines);
    if(!$_POST['engine']){
        //they must have come from the firefox search bar.
        //first, see if they have a search engine in a cookie or session
        //if they do, send them on their way.  if not, give them the webpage
        //either way, save all the info we can gather to $search, so the form is auto-completed for them
        $search['engine'] = getEngineFromCookieAndSession();
        
        setQueryAndEngineInCookieAndSession($search);
        if($search['engine']){
            sendThemOnTheirWay($search);
        }
    }
    else if($_POST['q'] != ''){
        //if they did submit a query, then we should probably be sending them on their way
        setQueryAndEngineInCookieAndSession($search);
        sendThemOnTheirWay($search);
    }
    else{
        //else: if they posted an empty string
        //then they wanted to get the form
        //(they may have been looking to change their default search engine)
        $search['engine'] = getEngineFromCookieAndSession();
    }
}
//if they didn't submit a search query
else{
    //if they've been here before, and we can get some useful data from their cookie/session
    //populate the form with that
    if($search = getSearchFromCookieAndSession()){
        $engines->setCurrentEngine($search['engine']);
        setQueryAndEngineInCookieAndSession($search);
    }
    //if this is their first time here, fill in the form with default values
    else{
        $search = $search_default;
    }
}


//displays the radio button and corresponding label for a single search engine
//called from within search-engines.php
function showEngineRadio($id, $checked, $image, $image_is_png, $search_type){
    ?>
        <li id="<?php echo $id ?>_li" class="inactive"><input type="radio" name="engine" id="<?php echo $id ?>" value="<?php echo $id ?>" <?php if($checked) echo 'checked="checked"' ?>/><label for="<?php echo $id ?>" class="engineLabel"><img src="<?php echo $image ?>" border="0" <?php if ($image_is_png) echo 'class="png"' ?> alt="<?php echo _($search_type) ?>" /> (<?php echo $search_type ?>)</label></li>
    <?php
}







?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<? if ($use_i18n) { ?>
<html lang="<?php echo $cc_lang->get('_language_xml') ?>" xml:lang="<?php echo $cc_lang->get('_language_xml') ?>" xmlns="http://www.w3.org/1999/xhtml">
<? } else { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? } ?>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title><?php echo _('Creative Commons') . " " . _('Search') ?></title>
    <meta name="keywords" content="creativecommons, ccsearch, search, 
                                   engine, searchengine, license, find" />
    <meta name="description" content="A Creative Commons-based search
                                      search engine of search engines." />
    <meta name="robots" content="index, follow" />
    <script type="text/javascript">
    /* <![CDATA[ */
    var d = "<?php echo _('Enter search query');?>";
    /* ]]> */
    </script>
    <script type="text/javascript" src="search.js"></script>
    <style type="text/css" media="screen">
      @import "search.css";
    </style>
    <link rel="stylesheet" type="text/css" media="screen" href="http://creativecommons.org/includes/progress.css" />
    <!--[if IE]><link rel="stylesheet" type="text/css" media="screen" href="search-ie.css" /><![endif]-->
    
  </head>
  <body onload="setupQuery()">
    <div id="ff-box"><div id="thanks"><?php echo sprintf(_('Thanks for using CC Search via %sFirefox%s.'), '<a href="http://spreadfirefox.com/">', '</a>') ?></div></div>
    <div id="header-box">
      <div id="header">
        <div id="title">
	  <a href="./"><img src="images/cc-search.png" alt="ccSearch" width="183" height="52" border="0" class="png" /></a>
	  <div id="title-by"><?php echo _('by <a href="http://creativecommons.org/">Creative Commons</a>'); ?></div>
	</div>
        
      </div>
    </div>
    <div id="results-box">
      <div id="options">
        <a href="http://wiki.creativecommons.org/CcSearch" title="<?php echo _('Understand your search results') ?>">
          <!-- info icon from: http://www.famfamfam.com/lab/icons/silk/ (cc-by 2.5)  -->
          <img src="images/information.png" id="subNFO" border="0" class="png" width="16" height="16" alt="<?php echo _('What is this?')?>" />
          <?php echo _('What is this?') ?>
        </a>
        &nbsp;&nbsp;
        <a href="http://wiki.creativecommons.org/Content_Curators" title="<?php echo _('Browse directories of licensed images, sounds, videos and more') ?>">
          <img src="images/cc.png" id="subCC" border="0" class="png" width="16" height="16" alt="<?php echo _('Content Directories') ?>" />
          <?php echo _('Content Directories') ?></a>
      </div>
      
      
      <form id ="ccSearchForm" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
      <fieldset id="ccSearchForm-MainFieldset">
	  <fieldset id="everythingButTabs">
	  <fieldset id="searchAndGo">
            <input type="text" name="q" id="q" class="inactive" size="35" onclick="wakeQuery()" onblur="resetQuery()" value="<?php echo $search['query'] ?>"/>
            <input type="submit" name="some_name" value="<?php echo _('go'); ?>" id="qsubmit" />
	  </fieldset>
  	  <fieldset id="engineList"> 
	  
	  <legend>Search Engine</legend>
	  <ul id="engines">
	    <?php $engines->showSelectRadios(); ?>
	  </ul>
	  </fieldset>
	  <fieldset id="comm_deriv">
	  <legend>Rights</legend>
       <p>
              <input type="checkbox" name="comm" value="1" id="comm" <?php if($search['comm']) echo 'checked="checked"' ?>/>
              <label for="comm"><?php echo _('Search for works I can use for commercial purposes.') ?></label>
       </p>
       <p>
              <input type="checkbox" name="deriv" value="1" id="deriv" <?php if($search['deriv']) echo 'checked="checked"' ?>/>
              <label for="deriv"><?php echo _('Search for works I can modify, adapt, or build upon.') ?></label>
	   </p>
	</fieldset>
	</fieldset>
	</fieldset>
	</form>
    </div>
    
    <div id="footer">
      <div><a href="http://creativecommons.org/"><?php echo _('Creative Commons') ?></a> | <a href="http://creativecommons.org/contact"><?php echo _('Contact') ?></a> <!--<img id ="stat" src="transparent.gif?init"/>--> | <a href="http://support.creativecommons.org/"><?php echo _('Support CC'); ?></a> |     <?php if ($use_i18n) $cc_lang_selector->output(); ?></div>
      <div>
<p>
search.creativecommons.org is not a search engine, but rather offers
convenient access to search services provided by other independent
organizations.  CC has no control over the results that are returned.
Certain sites, such as Flickr, should reliably return only CC-licensed
results.  This is because the licensing of every item at Flickr is
stored in a database and can be reliably determined.  However, for
others, Google, for example, non-CC-licensed works may be returned.
Presently, searching the web at large for CC-licensed works is not a
perfect science.   Just as regular web searches are not guaranteed to
return the things you want or expect, neither will a search for
CC-licensed works always return CC licensed material.   Do not assume
that everything returned by this search portal is under a CC license.
You should always follow the link(s) to the works you are interested
in and verify that they do indeed appear to be marked as being under a
CC license.  Since there is no registration to use a CC license, and
no such registry exists, CC has no way to determine what is and isn't
under a CC license.  If you are in doubt you should contact the
copyright holder directly, or try to contact the site where you found
the content.
   </p>   </div>


    </div>
    <!--<?php /*-->
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
    <script type="text/javascript">_uacct = "UA-2010376-3";  urchinTracker(); </script>
    <!--*/ ?>-->

  </body>
</html>

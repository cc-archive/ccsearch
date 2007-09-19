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

require_once('cc-defines.php');
require_once('cc-language.php');
require_once('cc-language-ui.php');

session_start();

// This nastiness handles session storage 
$cc_lang = &$_SESSION['lang'];
if (DEBUG) {
  echo "<pre>";
  print_r($_SESSION);
  echo "</pre>";
}

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

//$cc_lang->DebugLanguages();
//echo "<h4>" . $_REQUEST['lang'] . "</h4>";
//echo "<pre>";
//print_r($cc_lang_selector);
//print_r($_COOKIE);
//print_r($_REQUEST);
//echo "</pre>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $cc_lang->get('_language_xml') ?>" xml:lang="<?php echo $cc_lang->get('_language_xml') ?>" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title><?php echo _('Creative Commons') . " " . _('Search') ?></title>
    <meta name="keywords" content="creativecommons, ccsearch, search, 
                                   engine, searchengine, license, find" />
    <meta name="description" content="A Creative Commons-based search
                                      search engine of search engines." />
    <meta name="robots" content="index, follow" />
    <script type="text/javascript" src="search.js"></script>
    <style type="text/css" media="screen">
      @import "search.css";
    </style>
    <!--[if IE]><link rel="stylesheet" type="text/css" media="screen" href="search-ie.css" /><![endif]-->
    
  </head>
  <body onload="setupQuery()">
    <div id="ff-box"><div id="thanks"><?php echo sprintf(_('Thanks for using CC Search via %sFirefox%s.'), '<a href="http://spreadfirefox.com/">', '</a>') ?></div></div>
    <div id="header-box">
      <div id="header">
        <div id="title">
	  <a href="./"><img src="images/cc-search.png" alt="ccSearch" width="183" height="52" border="0" class="png" /></a>
	  <div id="title-by">by <a href="http://creativecommons.org/">Creative Commons</a></div>
	</div>
        <form onsubmit="return doSearch()">
          <div id="left">
            <input type="text" name="q" id="q" class="inactive" size="35" onclick="wakeQuery()" onblur="resetQuery()"/>
            <input type="submit" name="some_name" value="go" id="qsubmit" />
	    
          </div>
          <div id="right">
	    <div>
              <input type="checkbox" name="comm" value="" id="comm" />
              <label for="comm"><?php echo _('Search for works I can use for commercial purposes.') ?></label><br/>
	    </div>
	    <div>
              <input type="checkbox" name="deriv" value="" id="deriv" />
              <label for="deriv"><?php echo _('Search for works I can modify, adapt, or build upon.') ?></label><br/>
	    </div>
	  </div>
	</form>
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
        &nbsp;&nbsp;
        <a href="#" onclick="breakOut(); return false;" title="<?php echo _('Only show search results') ?>">
          <img src="images/break.png" id="subBreak" border="0" class="png" width="12" height="12" alt="<?php echo _('Remove Frame') ?>" />
          <?php echo _('Remove Frame') ?></a>
      </div>
      <span style="float: right"><a href="http://creativecommons.org/support/"><img src="http://creativecommons.org/images/support/2006/spread-3.gif" border="0"/></a></span>
      <div id="menu">
	<ul class="tabs">
          <li id="google" class="inactive"><a href="#" onclick="setEngine('google')" title="<?php echo _('Web Search') ?>"><img src="images/cc-google.gif" class="google" border="0" alt="<?php echo _('Google') ?>" /></a></li>
          <li id="yahoo"  class="inactive"><a href="#" onclick="setEngine('yahoo')" title="<?php echo _('Web Search') ?>"><img src="images/cc-yahoo.gif" border="0" alt="<?php echo _('Yahoo') ?>" /></a></li>
          <li id="flickr" class="inactive"><a href="#" onclick="setEngine('flickr')" title="<?php echo _('Image Search') ?>"><img src="images/cc-flickr.png" border="0" class="png" width="48" height="18" alt="<?php echo _('flickr') ?>" /></a></li>
          <li id="blip" class="inactive"><a href="#" onclick="setEngine('blip')" title="<?php echo _('Video Search') ?>"><img src="images/cc-blip.png" border="0" class="png" width="42" height="20" alt="<?php echo _('blip.tv') ?>" /></a></li>
	  <li id="owlmm" class="inactive"><a href="#" onclick="setEngine('owlmm')" title="<?php echo _('Music Search') ?>"><img src="images/cc-owlmm.png" border="0" class="png" /></a></li>
	  <li id="spin" class="inactive"><a href="#" onclick="setEngine('spin')" title="<?php echo _('Media Search') ?>"><img src="images/cc-spinxpress.png" border="0" class="png" /></a></li>
	  <!--
          <li id="jamendo" class="inactive"><a href="#" onclick="setEngine('jamendo')" title="<?php echo _('Music Search') ?>"><img src="images/cc-jamendo.png" border="0" class="png" alt="<?php echo _('jamendo') ?>" /></a></li>
          <li id="ccmixter" class="inactive"><a href="#" onclick="setEngine('ccmixter')" title="<?php echo _('Music Search') ?>"><img src="images/cc-ccmixter.png" border="0" class="png" alt="<?php echo _('ccMixter') ?>" /></a></li>
          <li id="openclipart" class="inactive"><a href="#" onclick="setEngine('openclipart')" title="<?php echo _('Clip Art Search') ?>"><img src="#" border="0" class="png" alt="<?php echo _('Open Clip Art Library') ?>" /></a></li>
	  -->
	</ul>
      </div>
      
      <iframe id="results" name="results" frameborder="0" border="0"></iframe>
    </div>
    
    <div id="footer">
      <a href="http://creativecommons.org/"><?php echo _('Creative Commons') ?></a> | <a href="http://creativecommons.org/contact"><?php echo _('Contact') ?></a> <img id ="stat" src="transparent.gif?init"/> |     <?php $cc_lang_selector->output(); ?>

    </div>
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
    <script type="text/javascript">_uacct = "UA-2010376-3";  urchinTracker(); </script>

  </body>
</html>
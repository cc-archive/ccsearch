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






class SearchTabs
{
    /**
     * This is a CC Language object (or preferably a reference to one).
     * @var		mixed
     * @access	private
     */
     
    var $_cc_lang;


    function SearchTabs($cc_lang){
        $this->_cc_lang = $cc_lang;
        $this->_original_language = $cc_lang->GetLanguage(); 
        if(DEBUG) echo "<p>Current language: " . $this->_original_language . "</p>\n";
    }

    function show(){
    ?>

	    <ul class="tabs">
              <li id="google" class="inactive"><a href="#" onclick="setEngine('google')" title="<?php echo _('Web Search') ?>"><img src="images/cc-google.gif" class="google" border="0" alt="<?php echo _('Google') ?>" /></a></li>
              <li id="yahoo"  class="inactive"><a href="#" onclick="setEngine('yahoo')" title="<?php echo _('Web Search') ?>"><img src="images/cc-yahoo.gif" border="0" alt="<?php echo _('Yahoo') ?>" /></a></li>
              <li id="flickr" class="inactive"><a href="#" onclick="setEngine('flickr')" title="<?php echo _('Image Search') ?>"><img src="images/cc-flickr.png" border="0" class="png" width="48" height="18" alt="<?php echo _('flickr') ?>" /></a></li>
              <li id="blip" class="inactive"><a href="#" onclick="setEngine('blip')" title="<?php echo _('Video Search') ?>"><img src="images/cc-blip.png" border="0" class="png" width="42" height="20" alt="<?php echo _('blip.tv') ?>" /></a></li>
	      <li id="owlmm" class="inactive"><a href="#" onclick="setEngine('owlmm')" title="<?php echo _('Music Search') ?>"><img src="images/cc-owlmm.png" border="0" class="png" /></a></li>
	      <li id="spin" class="inactive"><a href="#" onclick="setEngine('spin')" title="<?php echo _('Media Search') ?>"><img src="images/cc-spinxpress.png" border="0" class="png" /></a></li>

<?php if($this->_original_language == "en_US"){ ?>
              <li id="jamendo" class="inactive"><a href="#" onclick="setEngine('jamendo')" title="<?php echo _('Music Search') ?>"><img src="images/cc-jamendo.png" border="0" class="png" alt="<?php echo _('jamendo') ?>" /></a></li>
	      <!--          <li id="ccmixter" class="inactive"><a href="#" onclick="setEngine('ccmixter')" title="<?php echo _('Music Search') ?>"><img src="images/cc-ccmixter.png" border="0" class="png" alt="<?php echo _('ccMixter') ?>" /></a></li>
              <li id="openclipart" class="inactive"><a href="#" onclick="setEngine('openclipart')" title="<?php echo _('Clip Art Search') ?>"><img src="#" border="0" class="png" alt="<?php echo _('Open Clip Art Library') ?>" /></a></li>
	      -->
<?php } ?>
	    </ul>
    <?php
    }








}

?>

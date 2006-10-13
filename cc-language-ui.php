<?
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
class CCLanguageUI 
{
    /**
     * This is a CC Language object (or preferably a reference to one).
     * @var		mixed
     * @access	private
     */
    var $_cc_lang;
    
    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @param    array $cc_lang reference to a cc_lang object.
    * @access	public
    */
    function CCLanguageUI ($cc_lang) 
    {
        $this->_cc_lang = $cc_lang;
    }



    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @return	mixed	 Description
    * @access	public
    * @see		??
    */
    function output () {}

} // end of CCLanguageUI class


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
class CCLanguageUISelector extends CCLanguageUI
{
    /**
     * This is storage for our UI items by storage.
     * @var		array
     * @access	private
     */
    var $_selector;


    /**
     * Should we use a label or not.
     * @var		bool
     * @access	private
     */
    var $_use_label;

    /**
     * Shall we or shall we not autoload language changes.
     * @var		bool
     * @access	private
     */
    var $_use_autoload;

    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @access	public
    */
    function CCLanguageUISelector ($cc_lang) 
    {
        // no super keyword?
        parent::CCLanguageUI($cc_lang);

        $this->_use_label = true;
        $this->_use_autoload = false;

        if ( $this->_use_label )
            $this->_selector .= 
                "<label for=\"lang\">" . _('Language') . "</label> ";

        if ( $this->_use_autoload )
            $onrelease_text = " onchange=updateLanguage();";

        $this->_selector .= "<select name=\"lang\" id=\"lang\"$onrelease_text>";
        foreach ( $this->_cc_lang->getPossibleLanguages() as $key => $value )
        {
            $selected_text = "";
            if ($this->_cc_lang->getLanguage() == $key )
                $selected_text = " selected=\"selected\"";
            $this->_selector .= 
                "<option value=\"$value\"$selected_text>$key</option>\n";
        }
        $this->_selector .= "</select>\n";

/*d
<select id="lang" name="lang" class="">
<option value="de_DE"  >de_DE</option>
<option value="it_IT"  >it_IT</option>
<option value="pt_BR"  >pt_BR</option>
<option value="zh_CN"  >zh_CN</option>
<option value="zh_TW"  >zh_TW</option>
<option value="default"  >default</option>
<option value="en_US"  selected="selected"  >en_US</option>
<option value="autodetect"  >autodetect</option>
</select>
*/
        
    }
    
    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @return	object	 Description
    * @access	public
    * @see		??
    */
    function outputHeader () 
    {
        if ( ! $this->_use_autoload )
            return;

        echo "<script></script>\n";
    }
    
    /**
    *
    * This is the short Description for the Function
    *
    * This is the long description for the Class
    *
    * @return	mixed	 Description
    * @access	public
    * @see		??
    */
    function output () 
    {
        echo $this->_selector;
    }
}

?>

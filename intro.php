<?php
//language stuff--------------
$use_i18n = true;

require_once('cc-defines.php');
require_once('cc-language.php');
require_once('cc-language-ui.php');

if ($use_i18n) {
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
                               $cc_lang_help->get('_text') . "</div>", true, false);

}

//$cc_lang->DebugLanguages();
//echo "<h4>" . $_REQUEST['lang'] . "</h4>";
//echo "<pre>";
//print_r($cc_lang_selector);
//print_r($_COOKIE);
//print_r($_REQUEST);
//echo "</pre>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo _('ccSearch Intro') ?></title>
	<!-- Date: 2008-04-09 -->
	
	<style type="text/css" media="screen">
	html{

font-size:62.5%;
	}
	body{
-x-system-font:none;
color:#333;
font-family:Arial,sans-serif;
font-size-adjust:none;
font-stretch:normal;
font-style:normal;
font-variant:normal;
font-weight:normal;
line-height:1.5em;
font-size: 1.5em;
padding: 10px 100px 20px 100px;
	}
p {
line-height:1.5em;
margin-bottom:10px;
}
em{
text-decoration: none;
font-weight: bold;
font-size: 1.1em;
font-style: normal;
}
#thefineprint{
	margin-bottom: 30px;
}
.aboutcc{
	margin-left: 45%;
}

#aboutcc_vid{
	width: 43%;
	float: left;
}

p{
background-color: #EDEDED;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
border-radius: 5px;
padding: 5px 10px 5px 10px;
}


h2{
 color: #808080;
}
	</style>
	
</head>
<body>

<!--
<h1>Search for Creative Commons Licensed Material</h1>
-->

<h2><?php echo _('Enter a Search Query Above To Get Started') ?></h2>

<p id="thefineprint">
<?php echo _('Please note that search.creativecommons.org is <em>not a search engine</em>, but rather offers convenient access to search services provided by other independent organizations. CC has no control over the results that are returned. <em>Do not assume that the results displayed in this search portal are under a CC license</em>. You should always verify that the work is actually under a CC license by following the link. Since there is no registration to use a CC license, CC has no way to determine what has and hasn\'t been placed under the terms of a CC license. If you are in doubt you should contact the copyright holder directly, or try to contact the site where you found the content.') ?>
</p>

<h2><?php echo _('About Creative Commons') ?></h2>

<embed src="http://blip.tv/play/gpxSkdkzg9ky" type="application/x-shockwave-flash" height="270" allowscriptaccess="always" allowfullscreen="true" id="aboutcc_vid"></embed> 
<p class="aboutcc">
<?php echo _('Creative Commons is a <em>nonprofit</em> corporation dedicated to making it easier for people to share and build upon the work of others, consistent with the rules of copyright.') ?>
</p>
<p class="aboutcc">
<?php echo _('We provide <em>free</em> licenses and other legal tools to mark creative work with the freedom the creator wants it to carry, so others can share, remix, use commercially, or any combination thereof.') ?>
</p>
<p class="aboutcc">
<?php echo _('We rely on the generous support of forward-thinkers like you to help us continue the work we do. Consider <a href="https://support.creativecommons.org/donate?utm_source=search&utm_medium=intro&utm_campaign=fall2009" target="_parent">donating to our annual campaign</a>, today!'); ?>
</p>

</body>
</html>

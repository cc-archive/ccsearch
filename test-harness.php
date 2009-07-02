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
 This is a test harness for use with ccSearch
 Use it to make sure that everythin is working as expected.
    Reasons things might break:
        *One of the search engines has changed their URL structure
        *Someone has made a change to the code which has caused it to break

 Directions: Make sure DEBUG is on (set in cc-defines.php).
   Then, visit this page.  The output should be self-explanatory.

 Note: the abstraction in this file is probably overkill, but it might prove useful.
 You can change just a couple lines and be able to run this from the command line
    or embed it more nicely in a web page.  or something.

*/


require_once('cc-defines.php');
require_once('cc-language.php');
require_once('cc-language-ui.php');
require_once('search-engines.php');

$errCount = 0; //the number of tests we have failed

//only do stuff if we're in debug mode
if(!DEBUG){
    echo "DEBUG mode must be on in order to run the test harness";
    exit();
}

//-----
//functions to abstract the test harness
//-----

//hopelessly anal abstraction of text output
//but hey, if we wanted to output to a file or something, this would simplify things
function outPut($str){
    echo $str;
}
//similarly anal abstraction of line breaks.
//in html, we want <br />, but in other contexts we probably don't
function lineBreak($count = 1){
    if($count < 0){
        return false;
    }
    while($count){
        outPut("\n<br />");
        $count--;
    }
}

//function to run a test
function runTest($testFunc, $testName, &$errCount){
    outPut("Running test: " . $testName . "...");
    $result = call_user_func($testFunc);
    if($result == true){
        outPut("passed");
        lineBreak();
    }
    else{
        outPut("FAILED!");
        lineBreak();
        $errCount++;
    }
}

//run this at the beginning of testing
function start_testing(&$errCount){
    $errCount = 0;
    outPut("Test harness for ccSearch");
    lineBreak();
    outPut("<code>");
}

//run this at the end of testing
function finish_testing($errCount){
    outPut("All Done!");
    outPut("</code>");
    lineBreak();
    if($errCount > 0){
        outPut($errCount . " TESTS WERE FAILED!");
    }
    else{
        outPut("ALL TESTS PASSED!");
    }
}




//----
//test functions!
//----
/*
//copy/paste this into a function just before the return of a failed test to see what's up.
lineBreak();
echo $should_be;
lineBreak();
echo $result;

*/

//--------------------------------------
//GOOGLE--------------------------------
//--------------------------------------

//case: $deriv==false && $comm==false
function google_search_1(){
    $should_be = "http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike|cc_noncommercial|cc_nonderived)&q=TEST";
    $mySE = new GoogleSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function google_search_2(){
    $should_be = "http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike|cc_nonderived).-(cc_noncommercial)&q=TEST";
    $mySE = new GoogleSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function google_search_3(){
    $should_be = "http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike).-(cc_noncommercial|cc_nonderived)&q=TEST";
    $mySE = new GoogleSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function google_search_4(){
    $should_be = "http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike|cc_noncommercial).-(cc_nonderived)&q=TEST";
    $mySE = new GoogleSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}


//--------------------------------------
//YAHOO--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function yahoo_search_1(){
    $should_be = "http://search.yahoo.com/search?cc=1&p=TEST";
    $mySE = new YahooSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function yahoo_search_2(){
    $should_be = "http://search.yahoo.com/search?cc=1&p=TEST&ccs=c&";
    $mySE = new YahooSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function yahoo_search_3(){
    $should_be = "http://search.yahoo.com/search?cc=1&p=TEST&ccs=c&ccs=e";
    $mySE = new YahooSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function yahoo_search_4(){
    $should_be = "http://search.yahoo.com/search?cc=1&p=TEST&ccs=e";
    $mySE = new YahooSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}

//--------------------------------------
//FLICKR--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function flickr_search_1(){
    $should_be = "http://flickr.com/search/?l=cc&q=TEST";
    $mySE = new FlickrSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function flickr_search_2(){
    $should_be = "http://flickr.com/search/?l=comm&q=TEST";
    $mySE = new FlickrSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function flickr_search_3(){
    $should_be = "http://flickr.com/search/?l=commderiv&q=TEST";
    $mySE = new FlickrSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function flickr_search_4(){
    $should_be = "http://flickr.com/search/?l=deriv&q=TEST";
    $mySE = new FlickrSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}

//--------------------------------------
//JAMENDO--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function jamendo_search_1(){
    $should_be = "http://www.flickr.com/search/?l=cc&q=TEST";
    $mySE = new JamendoSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function jamendo_search_2(){
    $should_be = "http://www.flickr.com/search/?l=comm&q=TEST";
    $mySE = new JamendoSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function jamendo_search_3(){
    $should_be = "http://www.flickr.com/search/?l=commderiv&q=TEST";
    $mySE = new JamendoSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function jamendo_search_4(){
    $should_be = "http://www.flickr.com/search/?l=deriv&q=TEST";
    $mySE = new JamendoSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}



//--------------------------------------
//OWL--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function owl_search_1(){
    $should_be = "http://www.owlmm.com/?query_source=CC&license_type=cc&q=TEST";
    $mySE = new OwlSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function owl_search_2(){
    $should_be = "http://www.owlmm.com/?query_source=CC&license_type=comm&q=TEST";
    $mySE = new OwlSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function owl_search_3(){
    $should_be = "http://www.owlmm.com/?query_source=CC&license_type=commderiv&q=TEST";
    $mySE = new OwlSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function owl_search_4(){
    $should_be = "http://www.owlmm.com/?query_source=CC&license_type=deriv&q=TEST";
    $mySE = new OwlSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}


//--------------------------------------
//Spin--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function spin_search_1(){
    $should_be = "http://www.spinxpress.com/getmedia_license=11_searchwords=TEST";
    $mySE = new SpinSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function spin_search_2(){
    $should_be = "http://www.spinxpress.com/getmedia_license=8_searchwords=TEST";
    $mySE = new SpinSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function spin_search_3(){
    $should_be = "http://www.spinxpress.com/getmedia_license=10_searchwords=TEST";
    $mySE = new SpinSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function spin_search_4(){
    $should_be = "http://www.spinxpress.com/getmedia_license=9_searchwords=TEST";
    $mySE = new SpinSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}

//--------------------------------------
//BLIP--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function blip_search_1(){
    $should_be = "http://blip.tv/posts/view/?q=TEST&section=/posts/view&sort=popularity&license=1,6,7,2,3,4,5";
    $mySE = new BlipSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function blip_search_2(){
    $should_be = "http://blip.tv/posts/view/?q=TEST&section=/posts/view&sort=popularity&license=1,6,7,2";
    $mySE = new BlipSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function blip_search_3(){
    $should_be = "http://blip.tv/posts/view/?q=TEST&section=/posts/view&sort=popularity&license=1,6,7,2";
    $mySE = new BlipSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function blip_search_4(){
    $should_be = "http://blip.tv/posts/view/?q=TEST&section=/posts/view&sort=popularity&license=1,6,7,4,5";
    $mySE = new BlipSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}




//--------------------------------------
//CCMIXTER--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function ccmixter_search_1(){
    $should_be = "http://ccmixter.org/media/tags/TEST";
    $mySE = new CCMixterSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function ccmixter_search_2(){
    $should_be = "http://ccmixter.org/media/tags/TEST+attribution";
    $mySE = new CCMixterSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function ccmixter_search_3(){
    $should_be = "http://ccmixter.org/media/tags/TEST+attribution";
    $mySE = new CCMixterSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function ccmixter_search_4(){
    $should_be = "http://ccmixter.org/media/tags/TEST";
    $mySE = new CCMixterSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}




//--------------------------------------
//OPEN CLIP ART--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function oca_search_1(){
    $should_be = "http://openclipart.org/cchost/media/tags/TEST+publicdomain";
    $mySE = new OCASearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function oca_search_2(){
    $should_be = "http://openclipart.org/cchost/media/tags/TEST+publicdomain";
    $mySE = new OCASearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function oca_search_3(){
    $should_be = "http://openclipart.org/cchost/media/tags/TEST+publicdomain";
    $mySE = new OCASearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function oca_search_4(){
    $should_be = "http://openclipart.org/cchost/media/tags/TEST+publicdomain";
    $mySE = new OCASearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}






//--------------------------------------
//Y! Image Search--------------------------------
//--------------------------------------
//case: $deriv==false && $comm==false
function oca_search_1(){
    $should_be = "http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1";
    $mySE = new YahooImageSearch();
    $result = $mySE->createQueryString(false, false, "TEST");
    return ($result == $should_be);
}

//case: $deriv==false && $comm==true
function oca_search_2(){
    $should_be = "http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC2";
    $mySE = new YahooImageSearch();
    $result = $mySE->createQueryString(false, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==true
function oca_search_3(){
    $should_be = "http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC2&cc=CC3";
    $mySE = new YahooImageSearch();
    $result = $mySE->createQueryString(true, true, "TEST");
    return ($result == $should_be);
}

//case: $deriv==true && $comm==false
function oca_search_4(){
    $should_be = "http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC3";
    $mySE = new YahooImageSearch();
    $result = $mySE->createQueryString(true, false, "TEST");
    return ($result == $should_be);
}


/*
http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1x=wrt&y=Search

http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC2x=wrt&y=Search

http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC2&cc=CC3x=wrt&y=Search

http://images.search.yahoo.com/search/images?&p=TEST&vs=&cc=CC1&cc=CC3x=wrt&y=Search

x=wrt&y=Search

&cc=CC1&cc=CC2

&cc=CC1&cc=CC2&cc=CC3

&cc=CC1&cc=CC3


http://images.search.yahoo.com/search/images;_ylt=A0S020vxPEFKOvgA4bqJzbkF?p=TEST&fr=&ei=utf-8&x=wrt&y=Search

http://images.search.yahoo.com/search/images?_adv_prop=images&x=op&fr=&_bcrumb=LFHo8ZLN%2FWl&ei=utf-8&va=&vp=&vo=TEST&ve=&custom=600x400&vst=0&vs=&cc=CC1&cc=CC3&vm=p

http://images.search.yahoo.com/search/images?_adv_prop=images&x=op&fr=&_bcrumb=LFHo8ZLN%2FWl&ei=utf-8&va=&vp=&vo=TEST&ve=&custom=600x400&vst=0&vs=&cc=CC1&cc=CC2&cc=CC3&vm=p

http://images.search.yahoo.com/search/images?_adv_prop=images&x=op&fr=&_bcrumb=LFHo8ZLN%2FWl&ei=utf-8&va=&vp=&vo=TEST&ve=&custom=600x400&vst=0&vs=&cc=CC1&cc=CC2&vm=p


*/


//----
//alright fools, let's do this!
//----
$errCount;
start_testing(&$errCount);

runTest("google_search_1", 'Google Search: $deriv==false && $comm==false', &$errCount);
runTest("google_search_2", 'Google Search: $deriv==false && $comm==true', &$errCount);
runTest("google_search_3", 'Google Search: $deriv==true && $comm==true', &$errCount);
runTest("google_search_4", 'Google Search: $deriv==true && $comm==false', &$errCount);

runTest("yahoo_search_1", 'Yahoo Search: $deriv==false && $comm==false', &$errCount);
runTest("yahoo_search_2", 'Yahoo Search: $deriv==false && $comm==true', &$errCount);
runTest("yahoo_search_3", 'Yahoo Search: $deriv==true && $comm==true', &$errCount);
runTest("yahoo_search_4", 'Yahoo Search: $deriv==true && $comm==false', &$errCount);

runTest("flickr_search_1", 'Flickr Search: $deriv==false && $comm==false', &$errCount);
runTest("flickr_search_2", 'Flickr Search: $deriv==false && $comm==true', &$errCount);
runTest("flickr_search_3", 'Flickr Search: $deriv==true && $comm==true', &$errCount);
runTest("flickr_search_4", 'Flickr Search: $deriv==true && $comm==false', &$errCount);

runTest("blip_search_1", 'Blip Search: $deriv==false && $comm==false', &$errCount);
runTest("blip_search_2", 'Blip Search: $deriv==false && $comm==true', &$errCount);
runTest("blip_search_3", 'Blip Search: $deriv==true && $comm==true', &$errCount);
runTest("blip_search_4", 'Blip Search: $deriv==true && $comm==false', &$errCount);

runTest("jamendo_search_1", 'Jamendo Search: $deriv==false && $comm==false', &$errCount);
runTest("jamendo_search_2", 'Jamendo Search: $deriv==false && $comm==true', &$errCount);
runTest("jamendo_search_3", 'Jamendo Search: $deriv==true && $comm==true', &$errCount);
runTest("jamendo_search_4", 'Jamendo Search: $deriv==true && $comm==false', &$errCount);

runTest("spin_search_1", 'Spin Search: $deriv==false && $comm==false', &$errCount);
runTest("spin_search_2", 'Spin Search: $deriv==false && $comm==true', &$errCount);
runTest("spin_search_3", 'Spin Search: $deriv==true && $comm==true', &$errCount);
runTest("spin_search_4", 'Spin Search: $deriv==true && $comm==false', &$errCount);

runTest("owl_search_1", 'Owl Search: $deriv==false && $comm==false', &$errCount);
runTest("owl_search_2", 'Owl Search: $deriv==false && $comm==true', &$errCount);
runTest("owl_search_3", 'Owl Search: $deriv==true && $comm==true', &$errCount);
runTest("owl_search_4", 'Owl Search: $deriv==true && $comm==false', &$errCount);

runTest("ccmixter_search_1", 'CCMixter Search: $deriv==false && $comm==false', &$errCount);
runTest("ccmixter_search_2", 'CCMixter Search: $deriv==false && $comm==true', &$errCount);
runTest("ccmixter_search_3", 'CCMixter Search: $deriv==true && $comm==true', &$errCount);
runTest("ccmixter_search_4", 'CCMixter Search: $deriv==true && $comm==false', &$errCount);

runTest("oca_search_1", 'Open Clip Art Search: $deriv==false && $comm==false', &$errCount);
runTest("oca_search_2", 'Open Clip Art Search: $deriv==false && $comm==true', &$errCount);
runTest("oca_search_3", 'Open Clip Art Search: $deriv==true && $comm==true', &$errCount);
runTest("oca_search_4", 'Open Clip Art Search: $deriv==true && $comm==false', &$errCount);

finish_testing($errCount);


?>

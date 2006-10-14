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
 * TODO: Have to figure out how to do gettext stuff with javascript.
 */

var d = "Enter search query";
var engines = ["google", "yahoo", "flickr", "blip", "jamendo", "ccmixter", 
               "openclipart" ];
var engine = "";
var rights = "";
var url = "";
var lang = "";
var lang_xml = "";
var lang_short = "";

var refresh = false;

// NOTE: set this to your test environment if not main installation
// var domain = ".creativecommons.org";
var domain = "ccsearch.localhost";

// mmm, cookies...
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
////

// function by Pete Freitag (pete@cfdev.com)
function getQueryStrVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == variable) {
      return pair[1];
    }
  }
	return null;
}

// don't need an entire framework just for this
function id(i) { return document.getElementById(i); }

// initialise app
function setupQuery() {
	var query = id("q");
	var qs = getQueryStrVariable('q');
	var moz = getQueryStrVariable('sourceid');
	var e = getQueryStrVariable('engine');
	var docom = getQueryStrVariable('commercial');
	var doder = getQueryStrVariable('derivatives');

	// display firefox branding 
	if (moz == "Mozilla-search") {
		id('ff-box').style.display = "block";
	}
	
	// grab cookie and setup default engine
	getEngine();
	if (e) setEngine (e);

	// keep the results iframe fully in the browser window
	resizeResults();
	window.onresize = function() { resizeResults(); }
	
	// set commercial + derivative checkboxes
	if (docom) id('comm').checked = true;
	if (doder) id('deriv').checked = true;
	
	query.value = qs;
	
	if ((query.value == "") || (query.value == "null")) {
		query.value = d;
	} else if (query.value != d){
		query.className = "active";
		
		// since there's query data...
		doSearch();
	}
}

// bell
function wakeQuery() {
	var query = id('q');
	
	if (query.value == d) {
		query.value = "";
		query.className = "active";
	}
}

// whistle
function resetQuery() {
	var query = id('q');
	
	if (query.value == "") {
		query.className = "inactive";
		query.value = d;
	}
}

function setEngine(e) {
	var previous = engine;
	
	engine = e;
	try { id(previous).className="inactive"; } catch(err) {}
	id(engine).className="active";
	
	var d = new Date();
	d.setFullYear(2020,0,1);
	setCookie('ccsearch', engine, d, '/', domain);

    // updateLanguage();

	doSearch();
}

function getEngine() {
	engine = getCookie('ccsearch');
	
	if (engine == null)
		setEngine(engines[Math.floor(Math.random() * engines.length)]);
	
	id(engine).className = "active";
}

function updateLanguage () {

    var lang_temp = getQueryStrVariable('lang');

    if ( "" != lang_temp )
        lang_temp = getCookie('lang');

    if ( lang_temp != id('lang').value || "" == lang_temp )
        lang_new = id('lang').value;
    else
        lang_new = lang_temp;

    // give us the xml preferred lang string
    lang_xml_new = lang_new.replace("_", "-");

    // yahoo does some two letter and others 4 letters
    lang_short_new = lang_new.substr(0, 2);


    if ( lang_new == lang && lang_xml_new == lang_xml && 
         lang_short_new == lang_short && lang_temp == lang_new)
        return;

    // alert("updateLanguage: " + lang_new + " <-> " + lang);

    lang = lang_new;
    lang_xml = lang_xml_new;
    lang_short = lang_short_new;

	var d = new Date();
	d.setFullYear(2020,0,1);
    setCookie('lang', lang, d, '/', domain );

    // alert("updateLanguage: " + lang_new + " <-> " + lang);


    // document.write(lang + " " + lang_xml + " " + lang_short);

    // need to reload the top part of the browser for php
    var query = id('q');
    if ((query.value.length > 0) && (query.className == "active"))
        window.location.href = unescape(window.location.pathname) + 
                               '?q=' + query.value + '&lang=' + lang;
        // refresh = true;
}

// build advanced search query strings
// each engine has vastly different ways to do this. :/
function modRights() {
	
	switch (engine) {
		case "google":
			//.-(cc_noncommercial|cc_nonderived)
			rights = ".-(";
			
			if (id('comm').checked) {
				rights += "cc_noncommercial";
			}
			if (id('deriv').checked) {
				(id('comm').checked) ? rights += "|" : null;
				rights += "cc_nonderived";
			}
			
			rights += ")";
			break;
			
		case "yahoo":
			rights = "&";
			if (id('comm').checked) {
				rights += "ccs=c&";
			}
			if (id('deriv').checked) {
				rights += "ccs=e";
			}
			break;
			
		case "flickr":
			rights = "l=";
			if (id('comm').checked) {
				rights += "comm";
			}
			if (id('deriv').checked) {
				rights += "deriv";
			}
			break;
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
		case "jamendo":
			rights = "cani=";
			if (id('deriv').checked) {
				rights += "d";
			}
			if (id('comm').checked) {
				rights += "c";
			}
			break;
		case "ccmixter":
			rights = "";
			// everything on ccmixter permits derivs
			if (id('comm').checked) {
				rights += "+attribution";
			}
			break;
		case "openclipart":
			rights = "+publicdomain";
			// everything on ocal is pd 
			/* if (id('comm').checked) {
				rights += "+attribution";
			} */
			break;
	}
	if (rights.length < 5) rights = "";
	
}

// "main logic", no turning back.
function doSearch() {
	var query = id("q");
	url = "";
    // updateLanguage();
	
	// search only if there is something to search with
	if ((query.value.length > 0) && (query.className == "active")) {
		// set up rights string, works if user hits "go" or a tab. 
		modRights();
		
		switch (engine) {
			case "openclipart":
				url = 'http://openclipart.org/cchost/media/tags/' + 
                      query.value + rights;
				break;
                
			case "ccmixter":
				url = 'http://ccmixter.org/media/tags/' + query.value + rights;
				break;
				
			case "jamendo":
				url = 'http://www.jamendo.com/us/?p=music&tag=' + query.value + '&l=all&o=rating_desc&' + rights + '&aclass=2+3';
				break;
				
			case "blip":
				url = 'http://blip.tv/posts/view/?search=' + query.value + '&section=/posts/view&sort=popularity&' + rights;
				break;
				
			case "flickr":
				url = 'http://flickr.com/search/?' + ((rights.length > 2) ? rights : "l=cc") + '&q=' + query.value;
				break;
				
			case "yahoo":
				url = 'http://search.yahoo.com/search?cc=1&p=' + query.value + rights;
				if (lang_xml != null) 
                    url += '&x=op&fl=1&ei=UTF-8&vl=lang_' + lang_xml;
				break;
				
			case "google":
			default:
				url = 'http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike' + 
						((id('comm').checked) ? "" : "|cc_noncommercial") + ((id('deriv').checked) ? "" : "|cc_nonderived") + ')' + 
							rights + '&q=' + query.value; 
				if (lang_xml != null) url += '&hl=' + lang_xml;
				break;
		}
		//frames['results'].location.href = str;
        /* if ( refresh ) {
            // alert( window.location.pathname );
            window.location.href = unescape(window.location.pathname) + 
                                     '?q=' + query.value;
        } else { */
            window.results.location.href = url;

        // }
        document.getElementById('stat').setAttribute('src','transparent.gif?engine='+engine+'&comm='+id('comm').checked+'&deriv='+id('deriv').checked+'&q='+query.value);	
    }
	return false;
}

// keep results iframe as big as window
function resizeResults() {
	var results = id('results');
	var height = 0;
	var heightMinus = 150;
	
	// get height of window
	if (window.innerHeight) {
		height = window.innerHeight - 18;
	} else if (document.documentElement && document.documentElement.clientHeight) {
		height = document.documentElement.clientHeight;
		heightMinus = 165;
	} else if (document.body && document.body.clientHeight) {
		height = document.body.clientHeight;
	}
	
	results.style.height = Math.round(height - heightMinus) + "px";
}

function showFox() {
	id('thanks').style.display = "block";
}

function hideFox() {
	id('thanks').style.display = "none";
}

function clickFox() {
//	top.location = "http://spreadfirefox.com";
}

function breakOut() {
	if (url.length > 10) window.location = url;
}

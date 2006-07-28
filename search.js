/*
 * Creative Commons Search Interface
 * 1.0 - 2006-07
 * 
 */

var d = "Enter search query";
var engines = ["google", "yahoo", "flickr"];
var engine = "";
var rights = "";
var url = "";

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
	setCookie('ccsearch', engine, d, '/', '.creativecommons.org');
	
	doSearch();
}

function getEngine() {
	engine = getCookie('ccsearch');
	
	if (engine == null)
		setEngine(engines[Math.floor(Math.random() * engines.length)]);
	
	id(engine).className = "active";
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
	}
	if (rights.length < 5) rights = "";
	
}

// "main logic", no turning back.
function doSearch() {
	var query = id("q");
	url = "";
	
	// search only if there is something to search with
	if ((query.value.length > 0) && (query.className == "active")) {
		// set up rights string, works if user hits "go" or a tab. 
		modRights();
		
		switch (engine) {
			case "flickr":
				url = 'http://flickr.com/search/?' + ((rights.length > 2) ? rights : "l=cc") + '&q=' + query.value;
				break;
				
			case "yahoo":
				url = 'http://search.yahoo.com/search?cc=1&p=' + query.value + rights;
				break;
				
			case "google":
			default:
				url = 'http://google.com/search?as_rights=(cc_publicdomain|cc_attribute|cc_sharealike' + 
						((id('comm').checked) ? "" : "|cc_noncommercial") + ((id('deriv').checked) ? "" : "|cc_nonderived") + ')' + 
							rights + '&q=' + query.value; 
				break;
		}
		//frames['results'].location.href = str;
		window.results.location.href = url;
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
	top.location = "http://spreadfirefox.com";
}

function breakOut() {
	if (url.length > 10) window.location = url;
}

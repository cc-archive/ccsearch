/*
 * Creative Commons Search Interface
 * 1.0 - 2006-07
 * 
 */

var lang = "";
/*
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
*/
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

	// display firefox branding 
	if (moz == "Mozilla-search") {
		id('ff-box').style.display = "block";
	}
	
	lang = getQueryStrVariable('lang');
	
	query.value = qs;
	
	if ((query.value == "") || (query.value == "null")) {
		query.value = d;
	} else if (query.value != d){
		query.className = "active";
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

/*
function setEngine(e) {
	var previous = engine;


//    var query = id("q");
//    if ( query.className == "inactive" ) {
//        query.value = "flowers";
//        query.className = "active";
//    }

	engine = e;
	try { id(previous + "_li").className="inactive"; } catch(err) {}
	id(engine + "_li").className="active";
	
	var d = new Date();
	d.setFullYear(2020,0,1);
	setCookie('ccsearch', engine, d, '/', '.creativecommons.org');
	
	doSearch();
}
*/



/*
// keep results iframe as big as window
function resizeResults() {
	var results = id('results');
	var height = 0;
	var heightMinus = 200;
	
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
*/
function showFox() {
	id('thanks').style.display = "block";
}

function hideFox() {
	id('thanks').style.display = "none";
}

function clickFox() {
//	top.location = "http://spreadfirefox.com";
}

function grabOriginalLanguage() {
    return document.getElementsByTagName('html')[0].lang.replace('-', '_');
}

function onLanguageChange() {
    /* get value of the language */
    var lang_chosen = grabChosenLanguage();
    if (lang_chosen != grabOriginalLanguage()) {
	/* do something useful with that */
	var new_loc = location.href.split('?')[0];
	new_loc = new_loc.split('#')[0]; /* Remove spurious "#" */
	new_loc = new_loc + '?request=update&lang=' + lang_chosen;
	
	window.location = new_loc;
    }
}

function grabChosenLanguage() {
    var select_box = document.getElementById('lang');
    for (var i = 0 ; i < select_box.childNodes.length ; i++) { 
	var select_child = select_box.childNodes[i];
	if (select_child.nodeType == 1) { 
	    if (select_child.selected) {
		return select_child.value;
	    }
	}
    }
    return null;
}
	

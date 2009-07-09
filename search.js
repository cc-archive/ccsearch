/*
 * Creative Commons Search Interface
 * 1.0 - 2006-07
 * 
 */

var lang = "";

// don't need an entire framework just for this
function id(i) { return document.getElementById(i); }

/* these 3 functions just handle the clever auto-clear stuff*/
// note: var d, the default input text, is defined in the head of index.php
// this way, we can use php to translate it (clever, eh?)

// initialise app
function setupQuery() {
	var query = id("q");
	
	lang = getQueryStrVariable('lang');
	
   //focus on the query input field
   query.focus();
	
	if ((query.value == "") || (query.value == "null")) {
	    //case: there is no query in the query input field
	} else if (query.value != d){
	    //case: there is a query, and it's not just the default text
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

/*language stuff*/

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


/* we don't use this anymore, because all query processing is done in php
** this is probably for the better
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
*/

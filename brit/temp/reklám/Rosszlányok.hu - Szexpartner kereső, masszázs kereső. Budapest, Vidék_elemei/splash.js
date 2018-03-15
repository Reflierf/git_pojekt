var preloadFlag = false;
var contentLanguage = "hun";
var stopAnim = 0;
var selectCategoryTxt = "";
var extendedCookie = 0;

function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function preloadImages() {
	if (document.images) {
		index_cs1_over = newImage("./grafika/new_splash/TRAVI_csempe.png");
		index_cs2_over = newImage("./grafika/new_splash/COUPLES_csempe.png");
		index_cs3_over = newImage("./grafika/new_splash/BOYS_csempe.png");
		index_cs4_over = newImage("./grafika/new_splash/VIDEK_csempe.png");
		index_cs5_over = newImage("./grafika/new_splash/SP_csempe.png");
		index_cs6_over = newImage("./grafika/new_splash/MA_csempe.png");
		index_cs7_over = newImage("./grafika/new_splash/DO_csempe.png");
		index_cs8_over = newImage("./grafika/new_splash/LOGIN_csempe.png");
		index_cs9_over = newImage("./grafika/new_splash/STAT_csempe.png");
		index_cs10_over = newImage("./grafika/new_splash/STAT_csempe_inactive.png");

		index_b1_over = newImage("./grafika/new_splash/red_btn-over.png");
		index_b2_over = newImage("./grafika/new_splash/gold_btn-over.png");
		index_b3_over = newImage("./grafika/new_splash/blue_btn-over.png");
		index_b4_over = newImage("./grafika/new_splash/black_btn-over.png");
		index_b5_over = newImage("./grafika/new_splash/pink_btn-over.png");

		preloadFlag = true;
	}
}

setContentLanguage = function( language, noCheckOver ) {
	if ( !noCheckOver ) checkOver = false;
	if ( contentLanguage == language && selectCategoryTxt != "" && !noCheckOver) {
		//&& $('o18').checked == true
		return false;
	}
	if ( language == "hun" || language == "eng" || language == "deu" ) {
		contentLanguage = language;
		rolloverLang( "hun", "out" );
		rolloverLang( "eng", "out" );
		rolloverLang( "deu", "out" );
		//if ( !noCheckOver ) check18chk();

		new Ajax.Updater(
			'categories',
			'index_categories.php?lang=' + language,
			{asynchronous: true, evalScripts: true});
	}
}

check18chk = function() {
	$('o18').checked = true;
	stopAnim = 1;
	o18checked();
}

rolloverLang = function( lang, action ) {
	if ( action == "over" ) {
		$('flag_' + lang).src = './grafika/new_splash/flag_' + lang + '_over.gif';
		//$('ot' + lang).className = 'overAgeTextOver';
	}
	else {
		if ( lang != contentLanguage ) {
			$('flag_' + lang).src = './grafika/new_splash/flag_' + lang + '.gif';
			//$('ot' + lang).className = 'overAgeText';
		}
	}
}

chkButton = function( extend ) {
	extendedCookie = extend;
	check18chk();
}

o18checked = function() {
	if ( $('o18').checked && $('coverDiv')) {
		stopAnim = 1;
		over18GET = 1;
		// TODO: ez vmi miatt error-t ad ha nincs vizsgalva
		if ( $('statCategories') )
			$('statCategories').style.display = "";
		new Effect.Fade( 'coverDiv', {duration:0.7} );
		//new Effect.Fade( 'notLogin', {duration:0.7} );
		new Effect.Fade( 'lower_decription', {duration:0.7} );
		new Effect.Fade( 'SPandMA', {duration:0.7} );
	}
	else if ( $('coverDiv') ) {
		over18GET = 0;
		extendedCookie = 0;
		$('o18').checked = false;
		new Effect.Appear( 'coverDiv', {duration:0.7, afterFinish: function() {
				$('coverHelp').style.display = "";
			}
		} );
		//new Effect.Appear( 'notLogin', {duration:0.7} );
		new Effect.Appear( 'lower_decription', {duration:0.7} );
		new Effect.Appear( 'SPandMA', {duration:0.7} );
	}
	new Ajax.Updater(
		'ajaxJunk',
		'_cookieManagement.php?action=over18&aValue=' + over18GET + '&overMoreDays=' + extendedCookie,
		{asynchronous: true, evalScripts: true});

}

spashAnimation = function() {
	setTimeout( function(){
		$('statCategories').style.display = "";
		if ( stopAnim == 0 ) {
			stopAnim = 1;
			setTimeout( function() {
				//$('coverHelp').style.display = "none";
			}, 4500);
		}
	}, 2500);

	if ( stopAnim == 0 && $('coverDiv')) {
		new Effect.Appear( 'coverDiv', {duration:1, afterFinish: function() {
				$('o18').onclick = function() { stopAnim = 1;};
				$('o18').onclick = o18checked;
				$('coverHelp').style.display = "";
		}} );
	}
	/*
	if ( $('notLogin') )
		new Effect.Appear( 'notLogin', {duration:0.7} );
	*/
	if ( $('lower_decription') )
		new Effect.Appear( 'lower_decription', {duration:0.7} );
	if ( $('SPandMA') )
		new Effect.Appear( 'SPandMA', {duration:0.7} );
}

changeButtonCommon = function( id, roll, imagename ) {
	if ( !imagename || "" == imagename ) {
		imagename = "red_btn";
	}
	if ( roll == "" ) {
		newImage = "url(./grafika/new_splash/" + imagename + ".png)";
	}
	else {
		newImage = "url(./grafika/new_splash/" + imagename + "-over.png)";
	}
	$(id).style.backgroundImage = newImage;
}

mkPass = function(id, defText, newType) {
	if ( $(id) ) {
		if ( newType == "text" && $(id).value == "" ) {
			$(id).value = defText;
		}
		if ( newType == "password" && $(id).value == defText ) {
			$(id).value = "";
		}
	}
}

overTile = function( id, bg, inactiveColor, activeColor ) {
	if ( $(id) ) {
		if ( !inactiveColor || "" == inactiveColor ) {
			inactiveColor = "#d2cbcb";
		}
		if ( !activeColor || "" == activeColor ) {
			activeColor = "white";
		}
		$(id).style.backgroundImage = 'url(./grafika/new_splash/' + bg + '.png)';
		var inactivate = ( bg.indexOf( "_inactive") !== -1 );
		var mys = $$('#' + id + ' div.cBrown');
		if ( id == "videk_tile" ) {
			if ( inactivate ) {
				$('citiesTable').className = "hiddenCitiesTable";
			}
			else {
				$('citiesTable').className = "CitiesTable";
			}
		}
		if ( id == "login_tile" ) {
			if ( $('toFgrBtnDiv') ) {
				if ( inactivate ) {
					$('toFgrBtnDiv').style.display = 'none';
				}
				else {
					$('toFgrBtnDiv').style.display = '';
				}
			}
		}
		if ( id == "sp_tile" ) {
			if ( inactivate ) {
				$('spbp').style.color = "#826565";
				$('spbpk').style.color = "#826565";
			}
			else {
				$('spbp').style.color = "white";
				$('spbpk').style.color = "white";
			}
		}
		if ( id == "ma_tile" ) {
			if ( inactivate ) {
				changeMATile( 'out' );
				$('mabp').style.color = "#DE408B";
				$('mabpk').style.color = "#DE408B";
			}
			else {
				changeMATile( 'over' );
				$('mabp').style.color = "#fe479e";
				$('mabpk').style.color = "#fe479e";
			}
		}

		if ( id == "boys_tile" ) {
			if ( inactivate ) {
				$('boysTable').className = "hiddenCitiesTable";
			}
			else {
				$('boysTable').className = "CitiesTable";
			}
		}

		mys.each(function(item) {
			if ( inactivate ) {
				item.style.color = inactiveColor;
			}
			else {
				item.style.color = activeColor;
			}
		});
		var mys = $$('#' + id + ' div.tileBtn');
		mys.each(function(item) {
			if ( inactivate ) {
				item.style.display = 'none';
			}
			else {
				item.style.display = '';
			}
		});
	}
}

mkforgotten = function( defaultText, defaultNick ) {
	if ( $('lmail').value == defaultText ) {
		$('lmail').value = "";
	}
	if ( $('flname').value.toLowerCase() == defaultNick.toLowerCase() ) {
		$('flname').value = "";
	}
	$('forgotten').submit();
}

logoutOutsite = function() {
	new Ajax.Updater(
		'categories',
		'index_categories.php?logout=1',
		{asynchronous: true, evalScripts: true});
}
/*
cover18 = function() {
	if ( $('o18').checked ) {
		check18chk();
	}
}
*/
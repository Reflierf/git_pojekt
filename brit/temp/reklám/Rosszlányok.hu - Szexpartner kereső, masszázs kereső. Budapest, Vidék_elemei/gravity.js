function inArray( needle, haystack ) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}
function is_numeric ( mixed_var ) {
	return ( typeof( mixed_var ) === 'number' || typeof( mixed_var ) === 'string' ) && mixed_var !== '' && !isNaN( mixed_var );
}
function sendGravityEvent( eventType, userId, parameters ) {
	var allowedTypes = new Array( "VIEW", "SEARCH", "LETTER_SEND", "ADD_TO_FAVORITES", "REC_CLICK", "LIKE", "SHARE", "RATING", "REMOVE_FROM_FAVORITES" );
	_gravity = _gravity || [];

	 if ( inArray( eventType, allowedTypes ) && parameters != null && typeof( parameters ) === 'object' ) {
	 	if ( is_numeric( userId ) ) {
	 		_gravity.push({type: 'set', userId: userId});
	 		//console.log("_gravity.push({type: 'set', userId: \"" + userId + "\"});");
	 	}
	 	if ( eventType == "SEARCH" ) {
			parameters.type = 'event';
			parameters.eventType = eventType;
 			_gravity.push(parameters);
			//console.log("_gravity.push(" + JSON.stringify( parameters ) + ");");
	 	}
	 	else {
	 		if ( is_numeric( parameters.itemId ) ) {
	 			var myItem = parameters.itemId;
		 		if ( eventType == "RATING" && is_numeric( parameters.rating_number ) ) {
		 			_gravity.push({type : 'event', eventType: eventType, itemId: parameters.itemId, rating_number: parameters.rating_number});
					//console.log("_gravity.push({type : 'event', eventType: \"" + eventType + "\", itemId: \"" + parameters.itemId + "\", rating_number: \"" + parameters.rating_number + "\"});");
		 		}
		 		else if ( eventType == "REC_CLICK" ) {
		 			_gravity.push({type : 'event', eventType: eventType, itemId: parameters.itemId, recommendationId: parameters.recomId});//, position: parameters.position
					//console.log("_gravity.push({type : 'event', eventType: \"" + eventType + "\", itemId: \"" + parameters.itemId + "\", recommendationId: \"" + parameters.recomId + "\"});");//, position: \"" + parameters.position + "\"
		 		}
		 		else if ( eventType != "RATING" && eventType != "REC_CLICK" ) {
					_gravity.push({type : 'event', eventType: eventType, itemId: parameters.itemId});
		 			//console.log("_gravity.push({type : 'event', eventType: \"" + eventType + "\", itemId: \"" + parameters.itemId + "\"});");
	 			}
 			}
	 	}
 	}
 }
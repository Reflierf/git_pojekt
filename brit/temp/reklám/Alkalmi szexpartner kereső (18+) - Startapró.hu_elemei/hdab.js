    function getHdabResponse(url,id) 
    { 
	url = 'http://hdab.p24.hu/index.php/advert/ShowNaked?hash=' + url;
        var httpRequest; 
        if (window.XMLHttpRequest) 
        { 
            try { httpRequest = new XMLHttpRequest(); } 
            catch(e) {} 
        } 
        else if (window.ActiveXObject) 
        { 
            try { httpRequest = new ActiveXObject("Msxml2.XMLHTTP"); } 
            catch(e) 
            { 
                try { httpRequest = new ActiveXObject("Microsoft.XMLHTTP"); } 
                catch(e) {} 
            } 
        } 
        if(! httpRequest) 
        { 
            return false; 
        } 
        httpRequest.onreadystatechange = function() { putHdabResponse(httpRequest,id); }; 
        httpRequest.open('GET',url,true); 
        httpRequest.send(''); 
    }	

    function putHdabResponse(content,id) 
    { 
        try 
        { 
            if (content.readyState == 4) 
            { 
                if(content.status == 200) {
		    document.getElementById(id).outerHTML = content.responseText;
		} 
            } 
        } 
        catch(error) {
	} 
    }
    
    function renderHdab() {
	var eles = document.getElementsByTagName("div");
	for(var i = 0; i < eles.length; i++) {
	    if(eles[i].id.indexOf('hdab_') == 0) {
		getHdabResponse(eles[i].id.substring(5),eles[i].id)
	    }
    	}
    };

    if (typeof hdabOnAdverticum !== 'undefined' && hdabOnAdverticum) {
	window.onGoa3Invocation = function(response, pageIID) { renderHdab() };
    } else {
	renderHdab();
    }

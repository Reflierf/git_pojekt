
if(screen.width > 1024)  {
  document.write('<link rel="stylesheet" type="text/css" href="munka2.css">');
}

if(screen.width <=1024)  {
  document.write('<link rel="stylesheet" type="text/css" href="munka.css">');
}

   var colh;
     function popap(ablak,name,url)
     {
          ablak=window.open(url,name,"height=490,width=460,top=0,left=0,");
	 }	   

   var cold;
     function popup(ablak,name,url)
     {
          ablak=window.open(url,name,"height=620,width=560,top=0,left=0");
	 }	   

	
	
	var css;
     function cssnyit(win,name,url)
     {
          win=window.open(url,name,"height=600,width=600,top=0,left=0, scrollbars=1, resizable=1");
	 }	   


     var nyomtat;
     function nyomtatnyit(win,name,url)
     {
          win=window.open(url,name,"height=600,width=630,top=0,left=0, scrollbars=1, resizable=1");
	 }	   


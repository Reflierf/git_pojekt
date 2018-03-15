;(function($,f,g,h){var i={storage:{uid:1,did:1},init:function(){$(".gdrts-rating-block, .gdrts-rating-list").each(function(){i.common.process(this)});$(".gdrts-dynamic-block").each(function(){i.dynamic.process(this)});i.common.methods()},live:function(){$(g).on("click",".gdrts-toggle-distribution",function(e){e.preventDefault();var a=$(this).hasClass("gdrts-toggle-open");if(a){$(this).removeClass("gdrts-toggle-open");$(this).html($(this).data("show"));$(".gdrts-rating-distribution",$(this).closest(".gdrts-rating-block")).slideUp()}else{$(this).addClass("gdrts-toggle-open");$(this).html($(this).data("hide"));$(".gdrts-rating-distribution",$(this).closest(".gdrts-rating-block")).slideDown()}})},dynamic:{process:function(a){var b=JSON.parse($('.gdrts-rating-data',$(a)).html());b.did=i.storage.did;$(a).attr("id","gdrts-dynamic-id-"+i.storage.did).addClass("gdrts-dynamic-loading");i.storage.did++;var c={todo:"dynamic",did:b.did,meta:b};i.remote.call(c,i.dynamic.load,i.remote.error)},load:function(a){var b=$(a.render).hide();$("#gdrts-dynamic-id-"+a.did).fadeOut(150,function(){$(this).replaceWith(b);b.fadeIn(300,function(){i.common.process(this);if($(this).hasClass("gdrts-method-stars-rating")){i.stars_rating_single.process($(".gdrts-stars-rating",this))}})})}},common:{process:function(a){var b=JSON.parse($('.gdrts-rating-data',$(a)).html());b.uid=i.storage.uid;$(a).attr("id","gdrts-unique-id-"+i.storage.uid).data("rating",b);i.storage.uid++},methods:function(){i.stars_rating_single.init();i.stars_rating_list.init()},style:function(a,b){var c=".gdrts-with-font.gdrts-font-"+b.font+".gdrts-stars-length-"+b.length,rule=c+" .gdrts-stars-empty::before, "+c+" .gdrts-stars-active::before, "+c+" .gdrts-stars-current::before { "+"content: \""+b.content+"\"; }";$("<style type=\"text/css\">\r\n"+rule+"\r\n\r\n</style>").appendTo("head")}},remote:{url:function(){return gdrts_rating_data.url+"?action="+gdrts_rating_data.handler},call:function(a,b,c){$.ajax({url:this.url(),type:"post",dataType:"json",data:{req:JSON.stringify(a)},success:b,error:c})},error:function(a,b,c){if(a.status===0){alert('No internet connection, please verify network settings.')}else if(a.status===404){alert('Error 404: Requested page not found.')}else if(a.status===500){alert('Error 500: Internal Server Error.')}else if(b==='timeout'){alert('Request timed out.')}else if(b==='abort'){alert('Request aborted.')}else{alert('Uncaught Error: '+c)}}},stars_rating_single:{_b:function(a){return $(a).closest(".gdrts-rating-block.gdrts-method-stars-rating")},_d:function(a){return this._b(a).data("rating")},init:function(){$(".gdrts-rating-block .gdrts-stars-rating").each(function(){i.stars_rating_single.process(this)})},call:function(a,b){var c=this._d(a),args={todo:"vote",method:"stars-rating",item:c.item.item_id,nonce:c.item.nonce,render:c.render,uid:c.uid,meta:{value:b,max:c.stars.max}};i.remote.call(args,i.stars_rating_single.voted,i.remote.error)},voted:function(a){var b=$(a.render).hide();$("#gdrts-unique-id-"+a.uid).fadeOut(150,function(){$(this).replaceWith(b);b.fadeIn(300,function(){i.common.process(this);i.stars_rating_single.process($(".gdrts-stars-rating",this))})})},process:function(b){var c=i.stars_rating_single._d(b).stars,labels=i.stars_rating_single._d(b).labels;if($(b).hasClass("gdrts-with-font")){var d=c.name+c.max,obj={font:c.name,length:c.max,content:Array(c.max+1).join(c.char)};i.common.style(d,obj)}if($(b).hasClass("gdrts-state-active")){$(".gdrts-stars-empty",b).mouseleave(function(e){if($(this).hasClass("gdrts-vote-saving"))return;$(b).data("selected",0).attr("title","");$(".gdrts-stars-active",this).width(0)});$(".gdrts-stars-empty",b).mousemove(function(e){if($(this).hasClass("gdrts-vote-saving"))return;var a=$(this).offset(),width=$(this).width(),star=width/c.max,res=c.resolution,step=res*(star/100),x=e.pageX-a.left,parts=Math.ceil(x/step),current=parseFloat((parts*(res/100)).toFixed(2)),lid=Math.ceil(current*1),label=labels[lid-1],active=parts*step;$(b).data("selected",current).attr("title",current+": "+label);$(".gdrts-stars-active",this).width(active)});$(".gdrts-stars-empty",b).click(function(e){e.preventDefault();if($(this).hasClass("gdrts-vote-saving"))return;var a=$(b).data("selected");$(this).addClass("gdrts-vote-saving");i.stars_rating_single._b(this).addClass("gdrts-vote-saving");i.stars_rating_single.call(b,a)})}if(c.responsive){$(f).bind("load resize orientationchange",{el:b,data:c},i.stars_shared.responsive);i.stars_shared._r({el:b,data:c})}}},stars_rating_list:{_b:function(a){return $(a).closest(".gdrts-rating-list.gdrts-method-stars-rating")},_d:function(a){return this._b(a).data("rating")},init:function(){$(".gdrts-rating-list .gdrts-stars-rating").each(function(){i.stars_rating_list.process(this)})},process:function(a){var b=i.stars_rating_list._d(a).stars;if($(a).hasClass("gdrts-with-font")){var c=b.name+b.max,obj={font:b.name,length:b.max,content:Array(b.max+1).join(b.char)};i.common.style(c,obj)}if(b.responsive){$(f).bind("load resize orientationchange",{el:a,data:b},i.stars_shared.responsive);i.stars_shared._r({el:a,data:b})}}},stars_shared:{responsive:function(e){i.stars_shared._r(e.data)},_r:function(a){var b=a.el,available=$(b).parent().width(),new_size=Math.floor(available/a.data.max);new_size=new_size>a.data.size?a.data.size:new_size;if(a.data.type==="font"){$(".gdrts-stars-empty",b).css("font-size",new_size+"px").css("line-height",new_size+"px");$(b).css("line-height",new_size+"px").css("height",new_size+"px")}else if(a.data.type==="image"){$(".gdrts-stars-empty, .gdrts-stars-active, .gdrts-stars-current",b).css("background-size",new_size+"px");$(b).css("height",new_size+"px").css("width",a.data.max*new_size+"px")}}}};f.wp=f.wp||{};f.wp.gdrts={core:i};f.wp.gdrts.core.init();f.wp.gdrts.core.live()})(jQuery,window,document);

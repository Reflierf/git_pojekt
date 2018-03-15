(function() {
	//"deregister" TrimPath from global ns
	var TrimPath;
	var multiline;
	var grJSON3;
	var privatescope = {};
	//hide AMD module loaders in our scope 
	var module = undefined;
	var define = undefined;

window.undefined = window.undefined;

/**
 * The outermost namespace 'GravityRD'. It defines the inheritance and visibility, and contains some common utility functions.
 */
var GravityRD = {
	config : {},
	apply : function(obj, conf, overwrite) {
		if(obj && conf && typeof conf == 'object'){
			for(var k in conf){
				//TODO
				if (overwrite === undefined || overwrite === true) {
					obj[k] = conf[k];
				} else if (obj[k] === undefined) {
					obj[k] = conf[k];
				}
			}
		}
		return obj;
	},

	override : function(origclass, override) {
		GravityRD.apply(origclass.prototype, override);
	},
	
	extend: function(sp, overrides) {
		var sb = overrides.constructor != Object.prototype.constructor ? overrides.constructor : function(){sp.apply(this, arguments);};
		var F = function(){}, sbp, spp = sp.prototype;

		F.prototype = spp;
		sbp = sb.prototype = new F();
		sbp.constructor=sb;
		sb.superclass=spp;
		
		if(spp.constructor == Object.prototype.constructor){spp.constructor=sp;}
		
		sb.override = function(o){GravityRD.override(sb, o);};
		sbp.superclass  = (function(){return spp;});
		sbp.override = function(o){for(var m in o){this[m] = o[m];}};

		GravityRD.override(sb, overrides);
		
		sb.extend = function(o){return GravityRD.extend(sb, o);};

		//Handle visibility
		if (overrides.__public__ != undefined || spp.__public__ != undefined) {
			var _public = [];
			if (overrides.__public__ != undefined) {	
				var remove = {};
				for(var i=0; i<overrides.__public__.length;i++) {
					name = overrides.__public__[i];
					if(name.charAt(0) === '-') {
						name = name.substring(1);
						remove[name] = true;
					} else {
						_public.push(name);
						remove[name] = true;
					}
				}
				if (spp.__public__ != undefined) {
					for(var i=0; i<spp.__public__.length;i++) {
						name = spp.__public__[i];
						if(!remove[name])
							_public.push(name);
					}
				}
			} else {
				_public = spp.__public__;
			}
			sbp.__public__ = _public;
		}
		
		return sb;
	},
   
        
	bind : function(func, scope) {
        	return function() {
            		return func.apply(scope, arguments);
		};
	},

	namespace : function(ns) {
		var domains = ns.split(".");
		var p = eval(domains[0]) || {};
		for(var i=1; i< domains.length; i++) {
			p[domains[i]] = p[domains[i]] || {};
			p = p[domains[i]];
		}
		return p;
	},
		
	register : function(ns, name, module) {
		ns = this.namespace(ns);
		if (typeof(module["__interface__"]) == 'function')
			ns[name] = module.__interface__();
		else
			ns[name] = module;
	},
	
	isObject: function(o) {
		if(o == null)
			return false;
		return Object.prototype.toString.apply(o) === '[object Object]';
	},
	
	isArray: function(o) {
		if(o == null)
			return false;
		return Object.prototype.toString.apply(o) === '[object Array]';
	},
	
	isFunction: function(o) {
		if(o == null)
			return false;
		return typeof(o) === 'function';
	},
	
	isString: function(o) {
		return typeof(o) === 'string';
	},
	
	isNull: function(o) {
		return o === null;	
	},
	
	isUndefined: function(o) {
		return o === undefined;
	},

	each : function(input , fn, scope) {
		if (!input)
			return;
		if (!this.isString(input) && typeof input.length == 'number') {
			for (var i=0; i < input.length; i++) {
				fn.call(scope||this, input[i], i);
			}
		} else {
			fn.call(scope||this, input);
		}
	},

	map: function(obj, iterator, scope) {
		var results = [];
		if (obj == null) return results;
		this.each(obj, function(value, index, list) {
			results[results.length] = iterator.call(scope, value, index, list);
		});
		return results;
	},

	obj: function(list, values) {
		if (list == null) return {};
		var result = {};
		for (var i = 0, l = list.length; i < l; i++) {
			if (values) {
				result[list[i]] = values[i];
			} else {
				result[list[i][0]] = list[i][1];
			}
		}
		return result;
	},
	
	keys: function(object) {
		var ret = [];
		for (k in object) {
			if(object.hasOwnProperty(k))
				ret.push(k);
		}
		return ret;
	},
	
	/* only for namevalues in an object */
	hash: function(nv) {
		var na = GravityRD.keys(nv);		
		na.sort(function(a,b) {return a<b ? -1 : (a>b? +1 : 0); });				
		var input = "";
		for (var i=0; i<na.length;i++) {
			var val = nv[na[i]];
			input += na[i]+":"+ (GravityRD.isArray(val) ? val.join(",") : val)+";";
		}
		hash=(function(e){for(var r=0,i=0;i<e.length;i++)r=(r<<5)-r+e.charCodeAt(i),r&=r;return r})(input);
		return hash;
	},
 
	init : function(conf, buildSHA) {
		GravityRD.Typecheck.argumentChecker("GravityRD.init", arguments)
			.notNull(0).notUndefined(0).isObject(0);
		conf["buildSHA"] = buildSHA;
		GravityRD.Core.setServer(conf);
	},
	
	/**
     * The framework is started through the call of this function. It starts the worker, if already not present, and registers the callback interface.
	 */
	start : function() {
		GravityRD.Log.timeStamp("GR:start()");
		if (GravityRD.Worker.start()) {
			
			if(GravityRD.Core.getConfig()["mode"] === 'DEVELOP') {
				GravityRD.Log.info("Develop mode enabled. Please don't forget to turn it off.");
			}

			if(GravityRD.Core.debug("api")) {
				window["GR"] = this;

				window["GR"].sd = function() {
					console.log("click", unescape(window.localStorage.getItem("gr_click")));
					console.log("click_ts",unescape(window.localStorage.getItem("gr_click_ts")));
					console.log("recmap",unescape(window.localStorage.getItem("gr_recmap")));
					console.log("recmap_ts",unescape(window.localStorage.getItem("gr_recmap_ts")));
					console.log("recmapkeys",unescape(window.localStorage.getItem("gr_recmapkeys")));
					console.log("recmapkeys_ts",unescape(window.localStorage.getItem("gr_recmapkeys_ts")));
					console.log("event",unescape(window.localStorage.getItem("gr_event")));
					console.log("track",unescape(window.localStorage.getItem("gr_track")));
					console.log("cart",unescape(window.localStorage.getItem("gr_cart")));
				}; 
				window["GR"].sr = function() {
					window.localStorage.removeItem("gr_click");
					window.localStorage.removeItem("gr_click_ts");
					window.localStorage.removeItem("gr_recmap");
					window.localStorage.removeItem("gr_recmap_ts");
					window.localStorage.removeItem("gr_recmapkeys");
					window.localStorage.removeItem("gr_recmapkeys_ts");
					window.localStorage.removeItem("gr_event");
					window.localStorage.removeItem("gr_track");
					window.localStorage.removeItem("gr_cart");
					return window.localStorage;
				};
			}

			//register public interface
			window["GravityRD"] = {

				validationError:function(o) {
					GravityRD.Log.validationError(o);		
				},

				setGeneratedCookie: function(cid) {
					GravityRD.Core.Cookie.setGeneratedCookie(cid);
				},

				responseCallback: function(result) {
					GravityRD.Core.useResultCB(result);
				},
				
				searchCallback: function(name, data) {
					if (GravityRD.Search != undefined) {
						GravityRD.Search.callback(name, data);
					}
				},
				
				multiline: function() {
					return multiline.apply(this, arguments);
				}
			};
		}
	}
};

/** Basic class for API modules. */
GravityRD.Base = GravityRD.extend(Object, {
	__public__ : [],
	__interface__: function() {
		var _private = this;
		var ret = {};
		for(var idx=0; idx < this.__public__.length; idx++) {
			var name = this.__public__[idx];
			ret[name] = (function(name) {
				return function() {
					return _private[name].apply(_private, arguments);
				}
			})(name)
		}
		return ret;
	}
});

/** Basic class for API related exceptions */
GravityRD.Exc = GravityRD.extend(Object, {

	APIUSAGE : 1,
	VALIDATION: 2,
	
	constructor: function(type, msg) {
		this.type = type;
		this.msg = msg;
	},
	
	toString: function() {
		return this.msg;
	}
});

/*global $:false*/
(function () {
	'use strict';
	var javascriptEscape = function (s) {
		return String(s).replace(/\\/g, "\\\\").replace(/\0/g, "\\x00").replace(/"/g, "\\x22").replace(/'/g, "\\x27").replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/&/g, "\\x26").replace(/</g, "\\x3c").replace(/>/g, "\\x3e").replace(/-/g, "\\x2d");
	};
	/** default modifiers for trimpath rendering */
	var modifiers = {
		"html": function (s) {
			return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
		},
		"javascript": javascriptEscape,
		"js": javascriptEscape,
		"truncate": function (s, l, d) {
			var ret = s;
			if (s.length > l) {
				ret = s.substring(0, l) + d
			}
			return ret;
		}
	};
	/**
	 * GravityRD.Core API class.
	 *
	 * It contains the core functionality of the JS framework. Documented at function level.
	 */
	var GravityRD_Core = GravityRD.Base.extend({
		__public__: ['process', 'set', 'setServer', 'useResultCB', 'errorCB', 'saveParams', 'addListener', 'getConfig', 'getRequestNumber', 'setRecId', 'debug', 'debugMask', 'setGlobalNameValue', 'addGlobalNameValue', 'getGlobalNameValue', 'search', 'onUsageError'],
		/** Possible configuration options */
		serverConfigDefault: {
			/** loading the framework is disabled on this domain (usually set by overlay configurations */
			disabled: false,
			/** git sha of the build, set by the build process */
			buildSHA: 'notset',
			/** the backend endpoint server */
			targetServer: "saas.gravityrd.com",
			/** should the current cookie propagated to the retargeting server */
			retargetingTrack: false,
			/** backend endpoint of retargeting server */
			retargetingServer: "api.worldgravity.com",
			/** id of the customer */
			partnerId: null,
			/** developement mode, PROD: production (silent), DEVEL: developement (verbose logging) */
			mode: 'PROD',
			/** debug mask for special debug options */
			debug: {
				api: true,
				perf: true,
				tracking: true,
				postrender: true,
				log: true,
				hud: true,
				routing: true,
				callback: true
			},
			/** track events and send an event history with each request */
			trackEvents: false,
			/** maximum size of collected events */
			trackEventsMaxSize: 20,
			/** specify which evants and namevalues are collected */
			trackEventsFilter: {
				'VIEW': []
			},
			/** experimental, notused: try to track the status of the cart */
			trackCart: false,
			/** experimental: try to track pageleave 'events' */
			trackPageLeaveEvent: false,
			/** add the referrer url to each event */
			trackReferrer: false,
			/** add document.location to each event */
			trackLocation: false,
			/** generate and add a session identifier to each event */
			trackSession: false,
			/** generate and add a page identifier to each event */
			trackPage: false,
			/** is the html5 localstorage allowed to use to gather tracking information. it should be disabled on sites which have several subdomains */
			sessionStorage: true,
			/** should the exceptions thrown at the client logged back to the server */
			logExceptions: true,
			/** is the recid <-> itemid registration is allowed for the client. usefull in cases of mixed backend/frontend integration */
			enableRegisterRecId: false,
			/** the cookie path for all the tracking cookies */
			cookiePath: '/',
			/** the cookie domain used for all the tracking cookies */
			cookieDomain: null,
			/** should the framework generate a random seed with each request */
			addRandomSeed: false,
			/** how often should the random seed change (in seconds). usefull if displaying ad-like recommendations in several iframes.*/
			randomSeedExpire: 10,
			/** should event sending deferred in case there is no recommendation request waiting in the queue. don't touch this setting, except if you know what you are doing */
			deferEvents: true,
			/** in case events are deferred, how many millisecs to wait before sending the event */
			deferEventsTime: 15000,
			/** expiration time (in seconds) for the the recid <--> itemid internal mapping */
			recClickExpire: 0,
			/** how many items to store in the recid <--> itemid mapping */
			recClickTrackMax: 10,
			/** strings that look like null values wrongly passed, and possibly indicate api usage error*/
			nullStrings: ['null', 'NULL', 'nil', '0'],
			/** namevalues which probably should not used (they are the special parameter of set requests) */
			wrongNameValues: ['userId', 'cookieId'],
			/** list of server side settings the client can override */
			allowOverride: ['mode', 'sessionStorage'],
			/** is customconfig enabled: 'disabled', 'enabled', 'demo' */
			customConfig: 'enabled',
			/** in case of customconfig is in demo mode, which key activates the demo mode */
			customConfigDemoKey: 'F8', //currently only funcion keys are supported
			/** configuration options related to event routing. useUser: should the current userid sent with the routed events*/
			routingGlobal: {
				useUser: false
			},
			/** force protocol to use when making requests */
			forceProtocol: false,
			/** make userid setting persistent: false, true, 'session' */
			persistUserId: false,
			nameValueMaxSize: 1024
		},
		serverConfig: null,
		/** Configuration options which can be set from the client side. You can enable additional settings to be editable at client side by specifiying allowOverride setting in the server side config.*/
		globalConfig: {
			userId: null,
			cookieName: "gr_reco",
			cookieId: null,
			retargeting: false,
			useJsGeneratedCookie: true
		},
		/** the number of requests made by the framework */
		requestNumber: 0,
		/** params for the previous requests. this array can be indexed by i = 0..requestNumber -1 */
		parmsHistory: [],
		/** parameters for the actual request to make */
		params: null,
		/** internal handle for debouncer timer */
		handle: null,
		/** handles fw initialization */
		firstCall: true,
		/** map which stores the registered global namevalues */
		globalNameValues: {},
		/** active listeners */
		listeners: [],
		/**
		 * Add a global namevalue. If it exist, they will be merged together into an array.
		 * Global namevalues are appended to all the events and recommendationrequests made.
		 */
		addGlobalNameValue: function (key, val, overwrite, persist) {
			
			if (key === 'userId') { // treated specially
				this.set({
					userId: "" + val
				});
				return;
			}
			
			if (this.globalNameValues[key] === undefined || overwrite === true) {
				this.globalNameValues[key] = [];
			} else if (!GravityRD.isArray(this.globalNameValues[key])) {
				var tmp = this.globalNameValues[key];
				this.globalNameValues[key] = [];
				if (tmp !== undefined && tmp !== null) this.globalNameValues[key].push(tmp);
			}
			
			GravityRD.each(val, function (item) {
				if (item !== null && item !== undefined) {
					var ms = this.getConfig()['nameValueMaxSize'];
					var v = ""+item;
					if (v !== undefined && v !== null && v.length > ms) {
						v = v.substring(0, ms);
						GravityRD.Log.debug("Truncating namevalue '" + key + "', because it exceeds size limit " + ms);
					}
					this.globalNameValues[key].push(v);
				}
			}, this);
			
			if (this.globalNameValues[key].length == 1) {
				this.globalNameValues[key] = this.globalNameValues[key][0];
			} else if (this.globalNameValues[key].length == 0) {
				delete this.globalNameValues[key];
			}
			
			GravityRD.Log.debug("Adding global nameValue '" + key + "':'" + val + "', overwrite=" + overwrite + ", persist=" + persist, this.globalNameValues[key]);
			if (persist === "session") {
				GravityRD.Core.Cookie.addSessionNameValue(key, this.globalNameValues[key]);
			} else if (!!persist) {
				GravityRD.Core.Cookie.addPersistentNameValue(key, this.globalNameValues[key]);
			}
		},
		/** Overwrite a global namevalue, with the current value specified. */
		setGlobalNameValue: function (key, val, persist) {
			this.addGlobalNameValue(key, val, true, persist);
		},
		/** Query a global namevalue */
		getGlobalNameValue: function (key) {
			return this.globalNameValues[key];
		},
		/** Register a listener for the core events */
		addListener: function (listener) {
			this.listeners.push(listener);
		},
		constructor: function () {
			this.initParams();
		},
		/** Setup server side configuration. Overlay configs are evaluated here. */
		setServer: function (rd) {
			if (this.serverConfig == null) {
				this.serverConfig = {};
				GravityRD.apply(this.serverConfig, this.serverConfigDefault);
				GravityRD.apply(this.serverConfig, rd);
				var scriptTags = document.getElementsByTagName('script');
				for (var k = 0; k < scriptTags.length; k++) {
					var mt = (scriptTags[k].src || "").match(/\/([^/]*)\/gr_reco.(.min)?\.js/);
					if (mt) {
						var src = scriptTags[k].src;
						var match = src.match(/:\/\/([^/]*)/);
						var m = match[1].replace(/local\./, "");
						var kk = GravityRD.keys(rd.overlays || {});
						for (var i = 0; i < kk.length; i++) {
							var key = kk[i];
							if (m.match(new RegExp('.*' + key + '.*'))) {
								GravityRD.apply(this.serverConfig, rd.overlays[key]);
							}
						}
						if (!rd.partnerId) {
							this.serverConfig.partnerId = mt[1];
						}
						if (!rd.targetServer) {
							this.serverConfig.targetServer = m;
						}
						if (!rd.retargetingServer) {
							this.serverConfig.retargetingServer = mt[1] + ".worldgravity.com";
						}
						break;
					}
				}
			}
		},
		/** Client issued 'set' typed push requests are redirected here. */
		set: function (rd) {
			var ao = this.getConfig()["allowOverride"] || [];
			for (var k in rd) {
				if (!rd.hasOwnProperty(k)) {
					continue;
				}
				if (k === 'type') continue;
				if (k === 'mode' && GravityRD.Core.debug("log")) {
					this.globalConfig['mode'] = 'DEVELOP';
					this.serverConfig['mode'] = 'DEVELOP';
					continue;
				}
				if (this.globalConfig[k] !== undefined) {
					if (!GravityRD.isUndefined(rd.userId) && rd.userId !== this.globalConfig["userId"] && this.parmsHistory.length != 0) {
						GravityRD.Log.debug("The userId has changed: emptying buffers.");
						this.scheduleRequest(-1);
						this.request();
					}
					this.globalConfig[k] = rd[k];
					if (k == 'userId') {
						var persist = this.getConfig()['persistUserId'];
						if ("session" === persist) {
							GravityRD.Core.Cookie.addSessionNameValue(k, this.globalConfig[k]);
						} else if (!!persist) {
							GravityRD.Core.Cookie.addPersistentNameValue(k, this.globalConfig[k]);
						}
					}
				} else {
					var found = false;
					for (var j = 0; j < ao.length; j++) {
						if (ao[j] === k) {
							this.globalConfig[k] = rd[k];
							found = true;
							break;
						}
					}
					if (!found) {
						GravityRD.Log.debug("Attribute '" + k + "' treated as global nameValue");
						this.setGlobalNameValue(k, rd[k]);
					}
				}
			}
		},
		/** Return the actual request number */
		getRequestNumber: function () {
			return this.requestNumber;
		},
		/** Client issued 'event' typed pushrequests are redirected here */
		addEvent: function (rd) {
			rd.recId = rd.recommendationId || rd.recId || undefined;
			rd.recommendationId = undefined;
			if (rd.recId == undefined) {
				var rid;
				if (rd.eventType === 'REC_CLICK') {
					rid = GravityRD.Core.Cookie.getRecId(rd.itemId);
					if (rid != null) {
						GravityRD.Log.debug("Adding recId '" + rid + "' for event '" + rd.eventType + "', item '" + rd.itemId + "'");
						GravityRD.Core.Cookie.registerRecClickId(rd.itemId, rid);
						rd.recId = rid;
					}
				} else {
					rid = GravityRD.Core.Cookie.getRecClickId(rd.itemId);
					if (rid != null) {
						GravityRD.Log.debug("Adding recClickId '" + rid + "' for event '" + rd.eventType + "', item '" + rd.itemId + "'");
						rd.recId = rid;
					}
				}
			}
			this.addLocationAndReferrer("event", rd);
			GravityRD.Core.Cookie.trackEvents(rd);
			this.params.events.push(rd);
			GravityRD.each(this.listeners, function (listener) {
				if (GravityRD.isFunction(listener.onEvent)) {
					listener.onEvent.call(this, rd);
				}
			}, this);
		},
		/** Add the document.location and the referrer as a namevalue in case it is specified in the config, to the underlying request */
		addLocationAndReferrer: function (type, rd) {
			var config = GravityRD.Core.getConfig();
			var trackReferrer = config["trackReferrer"] || {};
			if (trackReferrer === true || trackReferrer[type] === true) {
				if (document.referrer != undefined && document.referrer != null) {
					GravityRD.Log.debug("Adding referrer '" + document.referrer + "' for " + type, rd);
					rd.referrer = document.referrer;
				}
			}
			var trackLocation = config["trackLocation"] || {};
			if (trackLocation === true || trackReferrer[type] === true) {
				if (document.location.href !== undefined && document.location.href != null) {
					GravityRD.Log.debug("Adding location '" + document.location.href + "' for " + type, rd);
					rd.location = document.location.href;
				}
			}
		},
		/** Add random seed to the recommendation request in case it is specified in the config */
		addRandomSeed: function (rd) {
			if (GravityRD.Core.getConfig()["addRandomSeed"]) {
				rd.GravityRandomSeed = GravityRD.Core.Cookie.getRandomSeed();
			}
		},
		/** Query if a special debugging mode is enabled */
		debug: function (key) {
			var debugGlobal = this.getConfig()["debugGlobal"] || [];
			var debugConfig = this.getConfig()["debug"];
			var ret = this.debugMask[key] || debugGlobal[key] || false;
			if (debugConfig === true) return ret;
			else return !!debugConfig[key] && ret;
		},
		/** Set debugmask for special debug modes */
		debugMask: function (mask) {
			this.debugMask = mask;
		},
		/** Client issued 'recommendation' typed pushrequests are redirected here */
		addRecommendationRequest: function (rd) {
			this.checkTemplating(rd);
			if (rd.resultNames == undefined && rd.templating != undefined && !rd.templating.serverSide) {
				rd.resultNames = this.parseTemplateVariables(rd.templating.template);
				GravityRD.Log.debug("Autogenerating resultNames", rd.resultNames);
			}
			rd.recommendationIndex = this.params.recommendationRequests.length;
			if (rd.groupId !== undefined) {
				if (rd.groupSeq == undefined) {
					GravityRD.Log.usageError("GR-023", rd.groupId);
				}
				var index = -1;
				for (var i = 0; i < this.params.recommendationRequests.length; i++) {
					if (this.params.recommendationRequests[i].groupId == rd.groupId) {
						index = i;
						break;
					}
				}
				if (index == -1) {
					if (rd.groupSize == undefined) {
						GravityRD.Log.usageError("GR-024", rd.groupId);
					}
					if (rd.groupSize < 1) {
						GravityRD.Log.usageError("GR-026", rd.groupId, rd.groupSize);
					}
					if (rd.groupSize == 1) {
						rd.__completed = true;
					}
				} else {
					var last = this.params.recommendationRequests[index];
					var size = last.groupSize;
					if (rd.groupSize != undefined) {
						if (rd.groupSize !== size) {
							GravityRD.Log.usageError("GR-027", rd.groupId, rd.groupSize, size);
						}
					}
					var seq = 1;
					while (last.__next !== undefined && last.__next !== null) {
						seq = seq + 1;
						last = last.__next;
					}
					if (seq + 1 == rd.groupSeq) {
						last.__next = rd;
						if (seq + 1 == size) {
							last = this.params.recommendationRequests[index];
							last.__completed = true;
							while (last.__next !== undefined && last.__next !== null) {
								last = last.__next;
								last.__completed = true;
							}
						}
					} else {
						GravityRD.Log.usageError("GR-025", rd.groupId, rd.groupSeq, seq + 1);
					}
				}
			}
			this.addLocationAndReferrer("recommendation", rd);
			this.addRandomSeed(rd);
			this.params.recommendationRequests.push(rd);
			GravityRD.each(this.listeners, function (listener) {
				if (GravityRD.isFunction(listener.onRecommendation)) {
					listener.onRecommendation.call(this, rd);
				}
			}, this);
		},
		/** Client issued 'explanation' typed pushrequests are redirected here */
		addExplanationRequest: function (rd) {
			this.checkTemplating(rd);
			if (rd.resultNames == undefined && rd.templating != undefined) {
				rd.resultNames = this.parseTemplateVariables(rd.templating.template);
				GravityRD.Log.debug("Autogenerating resultNames", rd.resultNames);
			}
			rd.explanationIndex = this.params.explanationRequests.length;
			this.addLocationAndReferrer("explanation", rd);
			this.addRandomSeed(rd);
			this.params.explanationRequests.push(rd);
			GravityRD.each(this.listeners, function (listener) {
				if (GravityRD.isFunction(listener.onExplanation)) {
					listener.onExplanation.call(this, rd);
				}
			}, this);
		},
		/** Check if templating parameters are correctly specified  */
		checkTemplating: function (rd) {
			if (rd.templating === undefined && rd.callback === undefined) {
				GravityRD.Log.usageError("GR-012");
			}
			// checking for targetElementId
			if (rd.templating !== undefined) {
				rd.templating.serverSide = false;
				if (rd.templating.templateElementId != undefined) {
					if (rd.templating.template !== undefined) {
						GravityRD.Log.usageError("GR-013");
					}
					var element = document.getElementById(rd.templating.templateElementId);
					if (element != null) {
						rd.templating.template = element.value;
						if (typeof (templateContent) === "undefined") {
							rd.templating.template = element.innerHTML;
						}
					}
				} else if (rd.templating.templateId !== undefined && rd.templating.template === undefined) {
					GravityRD.Log.debug("Server side templating");
					rd.templating.serverSide = true;
				} else {
					if (rd.templating.template === undefined) {
						GravityRD.Log.usageError("GR-014");
					}
				}
			}
			if (rd.templating && rd.templating.template === undefined && rd.templating.templateId === undefined) {
				GravityRD.Log.usageError("GR-015");
			}
			if (rd.templating && rd.templating.template !== undefined && rd.templating.templateId !== undefined) {
				GravityRD.Log.usageError("GR-030");
			}
		},
		/** Query the actual (merged) config */
		getConfig: function () {
			var ret = {};
			GravityRD.apply(ret, this.globalConfig);
			GravityRD.apply(ret, this.serverConfig);
			var ao = (this.serverConfig.allowOverride || []);
			for (var i = 0; i < ao.length; i++) {
				if (this.globalConfig[ao[i]] != undefined) ret[ao[i]] = this.globalConfig[ao[i]];
			}
			return ret;
		},
		/** Calling this function will create a backend request from the built up params. */
		request: function () {
			var dequeue = GravityRD.Core.Cookie.dequeueEvents() || [];
			var uid = this.globalConfig['userId'];
			if (dequeue.length > 0 && dequeue[0].__userId !== uid) {
				GravityRD.Log.debug("Warning: 'uid' and 'euid' is different, possible misconfiguration issue. uid:" + uid + ", euid:" + dequeue[0].__userId);
				var oldParams = this.params;
				this.initParams(null, dequeue);
				GravityRD.Core.Request.createRequest(this.getConfig(), this.params);
				this.params = oldParams;
				GravityRD.Core.Request.createRequest(this.getConfig(), this.params);
			} else {
				this.params.events = dequeue.concat(this.params.events);
				GravityRD.Core.Request.createRequest(this.getConfig(), this.params);
			}
		},
		/**
		 * This function schedules the #request() function to run in the specified milliseconds. 0 indicates to run immediatelly after the call stack got empty. -1 indicated to clear the pending timeout.
		 */
		scheduleRequest: function (ms) {
			if (this.handle != null) {
				window.clearTimeout(this.handle);
			}
			if (ms >= 0) {
				window.setTimeout(GravityRD.bind(this.request, this), ms);
			}
		},
		/** This function is called by the worker to process all the push-requests in the queue  */
		process: function (queue, force) {
			GravityRD.Log.group("Processing", true);
			var submit = this.firstCall;
			this.firstCall = false;
			submit |= (force || false);
			var config = GravityRD.Core.getConfig();
			for (var l = 0; l < queue.length; l++) {
				var command = queue[l];
				if (!(command.type === "set" || command.type === "fakeexception")) {
					GravityRD.apply(command, this.globalNameValues, false);
					command.__userId = this.globalConfig["userId"];
				}
				GravityRD.Typecheck.pushObjectChecker(command).validate();
				if (command.type === "recommendation") {
					GravityRD.Log.timeStamp("GR:RD");
					this.addRecommendationRequest(command);
					submit = true;
				} else if (command.type === "explanation") {
					this.addExplanationRequest(command);
					submit = true;
				} else if (command.type === "event") {
					this.addEvent(command);
				} else if (command.type === "set") {
					this.set(command);
				} else if (command.type === "fakeexception") {
					config = GravityRD.Core.getConfig();
					var ep = GravityRD.Core.Request.getEndpoint(config.retargeting, "JSServlet4");
					GravityRD.Core.Request.scriptTagRequest(ep, "ec=fake");
				} else if (command.type === 'register-recid') {
					if (config["enableRegisterRecId"] === true) {
						for (var i = 0; i < command.itemId.length; i++) {
							GravityRD.Core.Cookie.registerRecId(command.itemId[i], command.recId);
						}
					} else {
						GravityRD.Log.usageError("GR-019");
					}
				} else if (command.type === 'domready') {
					GravityRD.CustomConfig.domready(command.apply, true);
				} else if (command.type === 'customconfig') {
					GravityRD.Log.timeStamp("GR:CC push");
					window.setTimeout((function (c) {
						return function () {
							GravityRD.Log.timeStamp("GR:CC");
							GravityRD.Log.time("GR:CC");
							GravityRD.CustomConfig.set(c.config, c.environment, true);
							GravityRD.Log.timeEnd("GR:CC");
						}
					})(command), 0);
				} else if (command.type === 'search') {
					GravityRD.Search.install(command);
					submit = false;
				}
				else {
					GravityRD.Log.error("Invalid type specified for push object!", queue);
				}
			}
			GravityRD.Log.groupEnd();
			if (config["deferEvents"] === false || submit /* || this.parmsHistory.length == 0 */) {
				GravityRD.Log.debug("Immediate submit");
				this.scheduleRequest(-1);
				this.request();
			} else {
				GravityRD.Log.debug("Defered submit");
				GravityRD.Core.Cookie.queueEvents(this.params.events);
				this.params.events = [];
				this.scheduleRequest(config["deferEventsTime"]);
			}
		},
		/** Initializes the internal structures of a new request */
		initParams: function (o, e) {
			this.params = {
				events: e || [],
				requestNameValues: [],
				recommendationRequests: o || [],
				explanationRequests: [],
				templateData: {}
			};
			for (var i = 0; i < this.params.recommendationRequests.length; i++) {
				this.params.recommendationRequests[i].recommendationIndex = i;
			}
		},
		/** Saves the current internal params of a backend request into a history array. We will need them when processing callbacks from the server */
		saveParams: function (o) {
			GravityRD.Log.debug("Remaining", o);
			this.parmsHistory.push(this.params);
			this.initParams(o);
			this.requestNumber++;
		},
		/**
		 * Callbacks from the server are redirected to this function. It is responsible to render all the templates specified, and call the registered client-callback functions.
		 */
		useResultCB: function (result) {
			GravityRD.Core.Request.useResultCB(result);
			GravityRD.Log.debug("useResultCB", result);
			if (result && result.recommendationWrappers) {
				var perfKey = "GR:RD CB(" + result.requestNumber+")";
				GravityRD.Log.timeStamp(perfKey)
				GravityRD.Log.time(perfKey)
				if (!!this.parmsHistory[result.requestNumber]) {
					var resultPerf = this.parmsHistory[result.requestNumber].__perf;
					resultPerf.end();
					GravityRD.Log.debug("PERF", resultPerf.get(), true);
				}
				for (i = 0; i < result.recommendationWrappers.length; i++) {
					var recIndex = result.recommendationWrappers[i].recommendationIndex;
					var recRequest = this.parmsHistory[result.requestNumber].recommendationRequests[recIndex];
					var recommendation = result.recommendationWrappers[i].recommendation;
					recId = recommendation.recommendationId;
					recRequest.__rt_time = (+new Date()) - recRequest.__stag_time;
					if (this.debug("stagtime")) {
						this.addEvent({
							type: 'event',
							eventType: 'TRACK',
							rtTime: '' + recRequest.__rt_time
						});
					}
					var perf = GravityRD.Perf.create("rec://" + recRequest.scenarioId + "/" + result.requestNumber + "/" + recIndex);
					if (recommendation.items !== undefined) {
						(function () {
							var savedrec = recommendation;
							var savedrecid = recId;
							var savedURI = "rec://" + recRequest.scenarioId + "/" + result.requestNumber + "/" + recIndex;
							window.setTimeout(function () {
								GravityRD.Log.group("Registering of recids for " + savedURI, true);
								for (var j = 0; j < savedrec.items.length; j++) {
									GravityRD.Core.Cookie.registerRecId(savedrec.items[j].itemid, savedrecid);
								}
								GravityRD.Log.groupEnd();
							}, 0);
						})();
					}
					perf.phase("register recid");
					if (recRequest.templating !== undefined) {
						if (!recRequest.templating.serverSide) {
							targetId = recRequest.templating.targetElementId;
							target = document.getElementById(targetId);
							recommendation.templateData = recRequest.templating.templateData;
							recommendation.template = recRequest.templating.template;
							recommendation._MODIFIERS = recRequest.templating.modifiers || {};
							GravityRD.apply(recommendation._MODIFIERS, modifiers);
							recommendation.template = recommendation.template.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&");
							try {
								resultHTML = TrimPath.parseTemplate(recommendation.template).process(recommendation, {
									throwExceptions: true
								});
								if (recRequest.templating.replace === true) {
									var wrapper = document.createElement('div');
									wrapper.innerHTML = resultHTML;
									while (wrapper.children.length !== 0) {
										target.parentNode.insertBefore(wrapper.children[0], target);
									}
									target.parentNode.removeChild(target);
								} else {
									target.innerHTML = resultHTML;
								}
							} catch (e) {
								GravityRD.Log.usageError("GR-028", e);
							}
							/*
							 var tags = target.getElementsByTagName('*');
							 for(var j=0; j<tags.length; j++) {
							 var cls = tags[j].attributes["class"];
							 if (cls != undefined) {
							 if((" " + cls.nodeValue + " ").indexOf(" " + "gr_click" + " ") != -1 ) {
							 tags[j].onclick =  function(event) {
							 var itemId = event.target.attributes["data-itemid"].value;

							 GravityRD.Log.debug("Item ("+itemId+") click event");
							 _gravity.push({type:'event', eventType: 'REC_CLICK', itemId:itemId});
							 }
							 }
							 }
							 }
							 */
							perf.phase("templating");
							if (GravityRD.Core.debug("perf")) {
								var sheet = document.createElement('style');
								sheet.innerHTML = ".gr_tooltip::after {background: rgba(0, 0, 0, 0.7);border-radius: 4px 4px 4px 4px;box-shadow: 2px 2px 16px rgba(0, 0, 0, 0.5);" + "color: #FFF;content: attr(data-gr_tooltip);margin-top: -24px;padding: 3px 7px; position: absolute; opacity: 1; visibility: visible;" + "transition: all 0.7s ease-in-out;}";
								document.body.appendChild(sheet);
								var info = "N: " + perf.name + ",Rt: " + recRequest.__rt_time + ",Rr: " + perf.getPhases()["register recid"] + ",Te: " + perf.getPhases()["templating"] + ",Ca: " + perf.getPhases()["callback"];
								target.className = (target.className || "") + " gr_tooltip";
								target.setAttribute("data-gr_tooltip", info);
							}
						} else if (recommendation.content !== undefined && recRequest.templating.serverSide) {
							// we have a server side generated template
							// todo throw error if null?
							var targetElement = document.getElementById(recRequest.templating.targetElementId);
							if (targetElement !== undefined) {
								targetElement.innerHTML = recommendation.content;
							}
						}
					}
					if (recRequest.callback !== undefined) {
						try {
							if (GravityRD.Core.debug("callback")) {
								GravityRD.Log.debug("Evaluating callback", recRequest.callback);
							}
							recRequest.callback(recommendation, recRequest.templating);
						} catch (e) {
							GravityRD.Log.error("Exception during callback evaluation (recommendation) ", e);
						}
					}
					perf.phase("callback");
					perf.end();
					GravityRD.Log.debug(perf.name, perf.get());
					GravityRD.each(this.listeners, function (listener) {
						if (GravityRD.isFunction(listener.onRecommendationResponse)) {
							listener.onRecommendationResponse.call(this, recRequest, recommendation);
						}
					}, this);
				}
				GravityRD.Log.timeEnd(perfKey)
			}
			if (result && result.explanationWrappers) {
				for (var i = 0; i < result.explanationWrappers.length; i++) {
					var expIndex = result.explanationWrappers[i].explanationIndex;
					var expRequest = this.parmsHistory[result.requestNumber].explanationRequests[expIndex];
					var explanation = result.explanationWrappers[i].explanation;
					var recId = explanation.recommendationId;
					if (expRequest.templating !== undefined) {
						var targetId = expRequest.templating.targetElementId;
						var target = document.getElementById(targetId);
						explanation.templateData = expRequest.templating.templateData;
						explanation.template = expRequest.templating.template;
						explanation._MODIFIERS = expRequest.templating.modifiers || {};
						GravityRD.apply(explanation._MODIFIERS, modifiers);
						explanation.template = explanation.template.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&");
						var resultHTML = TrimPath.parseTemplate(explanation.template).process(explanation);
						if (expRequest.callback !== undefined) {
							try {
								expRequest.callback(explanation, expRequest.templating);
							} catch (e) {
								GravityRD.Log.error("Exception during callback evaluation (explanation)", e);
							}
						}
					} else {
						try {
							expRequest.callback(explanation);
						} catch (e) {
							GravityRD.Log.error("Exception during callback evaluation (explanation)", e);
						}
					}
					//TODO add Explanation callback
				}
			}
		},
		errorCB: function(rn, rri, eri) {
		    if (rri !== null) {
			for (var i = 0; i < rri.length; i++) {
			    var recIndex = rri[i]
			    var recRequest = this.parmsHistory[rn].recommendationRequests[recIndex];
			    if (recRequest.error !== undefined) {
				try {
					recRequest.error(recRequest.templating);
				} catch (e) {
					GravityRD.Log.error("Exception during error callback evaluation (recommendation) ", e);
				}
			    }
			}
		    }
		    if (eri !== null) {
			for (var i = 0; i < eri.length; i++) {
			    var expIndex = eri[i]
			    var expRequest = this.parmsHistory[rn].explanationRequests[expIndex];
			    if (expRequest.error !== undefined) {
				try {
					expRequest.error(expRequest.templating);
				} catch (e) {
					GravityRD.Log.error("Exception during error callback evaluation (explanation) ", e);
				}
			    }
			}
		    }
		},
		/**
		 * In case of templating based recommendation requests, if resultNameValues is not specified, it tries to parse the namevalues from the specified template.
		 * Because it doesn't parse the template correcty, just uses some heuristics, it can be wrong in some cases.
		 */
		parseTemplateVariables: function (input) {
			var map = {};
			var idx;
			while ((idx = input.indexOf("${")) != -1) {
				input = input.substring(idx + 2);
				var end = input.indexOf("}");
				if (end != -1) {
					var parName = input.substring(0, end);
					if (parName.indexOf("templateData.") == 0) {
						continue;
					}
					if (parName.indexOf("|") != -1) {
						parName = parName.substring(0, parName.indexOf("|"));
					}
					if (parName.indexOf(".") != -1) {
						parName = parName.substring(parName.indexOf(".") + 1);
						map[parName] = true;
					}
				}
			}
			var ret = [];
			for (var k in map) {
				ret.push(k);
			}
			return ret;
		},
		onUsageError: function (msg) {
			GravityRD.each(this.listeners, function (listener) {
				if (GravityRD.isFunction(listener.onUsageError)) {
					listener.onUsageError.call(this, msg);
				}
			}, this);
		}
	});
	GravityRD.register("GravityRD", "Core", new GravityRD_Core());
})();
(function() {
	
	/** JSON serializer wrapper. It depends on the JSON3 library. */
	var JSONSerializer = new GravityRD.extend(Object, { 
		decodeValue : function(v){
			var o = null;
			try { o =  grJSON3.parse(unescape(v)) } catch(e) {}
			return o;
		},
		encodeValue : function(v){
			return escape(grJSON3.stringify(v));
		}
	});
	
	/** 
	 * Simple keyvalue abstraction layer on top of the browser's 'localStorage',
	 * Note: localstorage is strictly bounded to the document location
	 */ 
	var LocalSessionStore = GravityRD.extend(Object, {
		set : function (name, value) {
			localStorage.setItem(name, value);
		},
		get : function (name) {
			return localStorage.getItem(name);
		}
	});

	/** 
	 * Simple keyvalue abstraction implemented by browser cookies 
	 * Note: information accessibility is based on the configured 'cookiePath'
	 */
	var CookieSessionStore = GravityRD.extend(Object, {
		set : function (name, value) {
			var config = GravityRD.Core.getConfig();
			var cookie = name + "=" + value + ";path="+config['cookiePath'];
			var cookieDomainVal = GravityRD.Core.Cookie.resolveCookieDomain(config, document.location.host);
			if(cookieDomainVal !== null) {
				cookie += ";domain=" + cookieDomainVal;
			}	
			document.cookie = cookie;
		},

		get : function (name) {
			var cookie = document.cookie;
			if (cookie.length > 0) {
				var start = cookie.indexOf(name + "=");
				if (start !== -1) {
					start = start + name.length + 1;
					var c_end = cookie.indexOf(";", start);
					if (c_end === -1) {
						c_end = cookie.length;
					}
					return cookie.substring(start, c_end);
				}
			}
			return null;
		}
	});
	
	/**
	 * Simple provider object for SessionStore. It chooses between CookieSessionStore and LocalSessionStore, based on the settings and the availability of browser functions.  
	 */
	var SessionStoreProvider = GravityRD.extend(Object, {
		instance: null, 
		provide: function() {
			if (this.instance === null) {
				var config = GravityRD.Core.getConfig();
				var localStoreEnabled = false;
				try {
					localStoreEnabled = !GravityRD.isUndefined(window.localStorage) && config["sessionStorage"] === true;
				} catch(e) {}
				if(localStoreEnabled) {
					try {
						window.localStorage.setItem("gr_localstoretest", "test");
						window.localStorage.removeItem("gr_localstoretest");
					} catch(e) {
						localStoreEnabled = false;
					}
				} 
				if(localStoreEnabled) {
					this.instance = new LocalSessionStore();
				} else {
					this.instance = new CookieSessionStore();
				}
			}
			return this.instance;
		}	
	});

	/**
	 *  Data stored at the client's side. 
	 */
	var ClientObject = GravityRD.Base.extend({
		cookieName : null,
		handle : null,
		storageProvider: new SessionStoreProvider(),
		serializer: new JSONSerializer(),

		constructor : function(cookieName) {
			this.cookieName = cookieName;
		}, 

		getValue:function(key) {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName);
			var cacheObj = this.serializer.decodeValue(cookieRawVal) || {};
			return cacheObj[key];		
		},

		getAllValues:function() {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName);
			var cacheObj = this.serializer.decodeValue(cookieRawVal) || {};
			return cacheObj;		
		},

		setAllValues:function(values) {
			this.storageProvider.provide().set(this.cookieName, this.serializer.encodeValue(values), 0);
		},
	
		setValue:function(key, value) {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName) || {};
			var cacheObj = (cookieRawVal !== null) ? (this.serializer.decodeValue(cookieRawVal) || {}) : {};
			cacheObj[key] = value;
			this.storageProvider.provide().set(this.cookieName, this.serializer.encodeValue(cacheObj), 0);
		},
		
		removeValue: function(key) {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName) || {};
			var cacheObj = (cookieRawVal !== null) ? (this.serializer.decodeValue(cookieRawVal) || {}) : {};
			delete cacheObj[key];
			this.storageProvider.provide().set(this.cookieName, this.serializer.encodeValue(cacheObj), 0);
		},

		push : function(item, maxsize) {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName);
			var cacheObj = (cookieRawVal !== null) ? (this.serializer.decodeValue(cookieRawVal) || []) : [];
			cacheObj.push(item);

			if(maxsize !== undefined) {
				if(cacheObj.length > maxsize) {
					cacheObj = cacheObj.slice(cacheObj.length-maxsize);
				}
			}
			this.storageProvider.provide().set(this.cookieName, this.serializer.encodeValue(cacheObj), 0);
		},
		
		get : function() {
			var cookieRawVal = this.storageProvider.provide().get(this.cookieName);
			var cacheObj = (cookieRawVal !== null) ? this.serializer.decodeValue(cookieRawVal) : null;
			return cacheObj;		
		}
	});

	/**
	 *  Data stored at the client's side. 
	 */
	var ClientSessionObject = ClientObject.extend({
		storageProvider: {
			instance: null, 
			provide: function() {
				if (this.instance == null) {
					this.instance = new CookieSessionStore();
				}
				return this.instance;
			}
		}
	});


	/**
	 *  Data stored at the client's side with some expiration date. 
	 */
	var ExpiringClientObject = GravityRD.extend(Object, {
		values: null,
		times: null,
		handle : null, 
		
		constructor: function(name) {
			ExpiringClientObject.superclass.constructor(name);
			this.times = new ClientObject(name+"_ts");
			this.values = new ClientObject(name);
		},
		
		getValue:function(key) {
			window.setTimeout(GravityRD.bind(this.clearOldValues, this), 0);
			var d = +new Date();
			kd = this.times.getValue(key);
			if(this.timeout() === 0 || kd + this.timeout() > d) {
				return this.values.getValue(key);		
			} else {
				GravityRD.Log.debug("Removing key from "+this.cookieName+", because it expired: " + key + " (request)");
				this.values.removeValue(key);
				this.times.removeValue(key);
				return null;
			}
		},
	
		setValue:function(key, value) {
			window.setTimeout(GravityRD.bind(this.clearOldValues, this), 0);
			this.values.setValue(key, value);
			this.times.setValue(key, +new Date());
		},
		
		clearOldValues: function() {
		
			var maxsize = this.maxSize();
			var d = +new Date() - this.timeout();
			var vall = this.values.getAllValues();
			var tall = this.times.getAllValues();
			var tlen = 0;
			var kmin = null, karg = null;
			for(var k in tall) {
				if(this.timeout() !== 0 && tall[k] < d) {
					GravityRD.Log.debug("Removing key from "+this.cookieName+", because it expired: " + k + " (thread)");
					delete tall[k];
					delete vall[k];
				} else {
					tlen++;
					if(kmin === null || tall[k] < kmin) {
						kmin = tall[k];
						karg = k;
					}
				}
			}
			
			if(tlen > maxsize && karg !== null) {
				GravityRD.Log.debug("Removing key from "+this.cookieName+", because it exceeds size limit: " + karg + " (thread)");
				delete tall[karg];
				delete vall[karg];
			}
			
			this.values.setAllValues(vall);
			this.times.setAllValues(tall);
		},
		
		getKeys: function() {

			var ret = [];
			for(var key in this.values.getAllValues()) {
				ret.push(key);			
			}
			return ret;
		},

		timeout: function() {
			return 0;
		},
		
		maxSize: function() {
			return 100;
		}
	});
 
	/** 
	 * This subclass of ExpiringClientObject represent the recid <-> id mapping for REC_CLICK events.
	 */
	var RecClickMapping = ExpiringClientObject.extend({
		timeout: function() {
			return (GravityRD.Core.getConfig()["recClickExpire"]  || 0)*1000;
		},
		maxSize: function() {
			return GravityRD.Core.getConfig()["recClickTrackMax"] || 20;
		}
	});
	
	/**
	 * This subclass of ClientObject is responsible for persistent tracking of cart contents. 
	 * Note: we never used this class in this way.
	 */
	var Cart = ClientObject.extend({
		serializer : new JSONSerializer(),
		
		getItems: function() {
			return this.getAllValues();
		},
		
		addItem: function(itemId, quantity) {
			var count = this.getValue(itemId) || 0;
			if(quantity  !== undefined)
				count += quantity;
			else
				count++;
			this.setValue(itemId, count);
		}, 

		removeItem: function(itemId, quantity) {
			var count = this.getValue(itemId) || 0;
			if(quantity  !== undefined)
				count -= quantity;
			else
				count --;
			if(count > 0)
				this.setValue(itemId, count);
			else
				this.removeValue(itemId);
		},

		removeItems: function(itemId) {
			this.removeValue(itemId);
		}
	});

	/**
	 * Simple implementation of an 'in-memory object', which has the same interface as ClientObject.
	 */
	var InMemoryObject = GravityRD.Base.extend({
 		storage: {}, 

 		getValue: function(key) {
 			return this.storage[key];
 		},

 		setValue: function(key, value) {
 			return this.storage[key] = value;
 		},

		getKeys: function() {
			var ret = [];
			for(var key in this.storage) {
				ret.push(key);			
			}
			return ret;
		}
	});
	
	/**
	 * This class represents the recid <-> id mapping for recommendations on the current page. It is implemented as a wrapper of the the InMemoryObject class.
	 */
	var RecIdMapping = GravityRD.Base.extend({
		local: new InMemoryObject(),

		registerRecId: function (itemId, recId){
			GravityRD.Log.debug("RegisterRecId for '" + itemId+"': '"+recId+"'");
			this.local.setValue(itemId, recId);
		},
		getRecId: function(itemId) {
			var ret = this.local.getValue(itemId)||null;
			GravityRD.Log.debug("GetRecId for '" + itemId+"': '"+ret+"'");
			return ret;
		}		
	});

	/** 
	 * This class is a holder for a random seed object, which is constant for some seconds.
	 * It is usefull for clients who integrate through iframes. 
	 */
	var RandomSeed = ExpiringClientObject.extend({
		timeout: function() {
			return (GravityRD.Core.getConfig()["randomSeedExpire"]  || 10)*1000;
		},
		maxSize: function() {
			return 1;
		},
		getRandomSeed: function() {
			var rs = this.getValue("_");
			if (rs === null) {
				rs = Math.floor(Math.random()*4294967296);// 2^^32
				this.setValue("_", rs);
			}
			return rs;
		}
	});
 
	/**
	 * GravityRD.Core.Cookie API class.
	 * Responsible for:
	 * - session cookie handling
	 * - page cookie handling
	 * - tracking cookie handling
	 * - debug cookie handling
	 * - provide random seed
	 * - serialize and deserialize internal queues of the framework
	 * - recid <--> id mappings for click events, and for showed recommendation
	 */
	var GravityRD_Core_Cookie = GravityRD.Base.extend({
 		__public__: ['initSessionCookie','initPageCookie', 'getDebugCookie','setDebugCookie','getGeneratedCookie','setGeneratedCookie', 'registerRecId', 'getRecId', 'trackEvents', 'getTracking', 'getLastViewedItem', 'resolveCookieDomain', 'queueEvents', 'dequeueEvents', 'registerRecClickId', 'getRecClickId', 'getCookie', 'setCookie', 'retarget', 'randHexWord', 'getRandomSeed', 'addPersistentNameValue', 'getPersistentNameValues', 'addSessionNameValue', 'getSessionNameValues'],
 		queueEventsTrack : new ClientObject("gr_track"),
 		randomSeed : new RandomSeed("gr_rs"),
 
		initSessionCookie: function() {
			if (GravityRD.Core.getConfig()["trackSession"] === true) {
				var cookieName = "gr_session";
				var sid = GravityRD.Core.Cookie.getCookie(cookieName);
				if (sid === null) {
					sid = (+new Date().getTime()).toString(16)+"-"+this.createRandomString();
					GravityRD.Core.Cookie.setCookie(cookieName, sid, false);
				}
				GravityRD.Core.setGlobalNameValue('sessionId',sid);
			}
		},
 
		initPageCookie: function() {
			if (GravityRD.Core.getConfig()["trackPage"] === true) {
				var cookieName = "gr_page";
				var sid = GravityRD.Core.Cookie.getCookie(cookieName);
				if (sid === null) { sid = 0; } else {sid = sid*1+1};
				GravityRD.Core.Cookie.setCookie(cookieName, ""+sid, false);
				GravityRD.Core.setGlobalNameValue('pageId',sid + "-"+this.randHexWord());
			}
		},

		getRandomSeed: function() {
			return this.randomSeed.getRandomSeed();
		},
		
		getDebugCookie : function() {
			var serializer = new JSONSerializer();
			var grdebug = this.getCookie('gr_debug');
			var json = serializer.decodeValue(grdebug) || {};
			return json;
		},

		setDebugCookie : function(json) {
			var serializer = new JSONSerializer();
			var enc = serializer.encodeValue(json);
			this.setCookie('gr_debug', enc);
		},
		
		queueEvents : function(x,key, overwrite) {
			var k = key || 'events';			
 			var queue = (!!overwrite) ? [] : (this.queueEventsTrack.getValue(k) || []);
 			var nqueue = queue.concat(x);
 			GravityRD.Log.debug("queueEvents ["+k+"]", nqueue); 
 			this.queueEventsTrack.setValue(k, nqueue);
  		},	
 
 		dequeueEvents : function(key) {
			var k = key || 'events';			
  			var ret = this.queueEventsTrack.getValue(k);
  			this.queueEventsTrack.setValue(k, []);
 			GravityRD.Log.debug("dequeueEvents ["+k+"]", ret); 
  			return ret;
 		},

		resolveCookieDomain: function(config, host) {
			var cookieDomainVal = null;
			if(config["cookieDomain"] !== null) {
				var cookieDomain = config["cookieDomain"];
				var cookieDomainVal = null;
				var host = host.replace(/:\d+/,'');

				if(Object.prototype.toString.apply(cookieDomain) === '[object Array]') {
					for(var i=0; i<cookieDomain.length; i++) {
						if(host.indexOf(cookieDomain[i]) > -1) {
							cookieDomainVal = cookieDomain[i];
							break;
						}
					}
					if (cookieDomainVal === null) {	
						GravityRD.Log.error("Can't resolve cookiedomain for: " + host);
					}
				} else if (typeof(cookieDomain) === 'function') {
					cookieDomainVal = cookieDomain(host);
				} else if (typeof(cookieDomain) === 'string') {
					var m = cookieDomain.match(/level-(\d+)/);
					if (cookieDomain.match(/level-(\d+)/) !== null) {
						var level = +m[1];
						var ld = host.split('.');
						var li = ld.length;
						while(--li >= 0 && --level >= 0) {
							if (cookieDomainVal === null) 
								cookieDomainVal = ld[li];
							else
								cookieDomainVal = ld[li]+'.'+cookieDomainVal;
						}
					} else {
						if (cookieDomain.indexOf('.') > -1) {
							cookieDomainVal = cookieDomain;
						} else {
							GravityRD.Log.error("Unrecognized cookieDomain!");
						}
					}
				}
			}
			return cookieDomainVal;
		},

		setCookie : function (name, value, expire) {
			if(typeof(expire) === "undefined") {
				expire = +new Date()+1000*60 * 60 * 24 * 365;
			}
			var config = GravityRD.Core.getConfig();
			var cookie = name + "=" + escape(value) + (expire === false ? "" : ";expires=" + new Date(expire).toUTCString())+";path="+config['cookiePath']+"";
			var cookieDomainVal = this.resolveCookieDomain(config, document.location.host);
			if(cookieDomainVal !== null) {
				cookie += ";domain=" + cookieDomainVal;
			}	
			GravityRD.Log.debug("setCookie: '"+ cookie+"'");
			document.cookie = cookie;
		},

		getCookie : function (name) {
			var cookie = document.cookie;
			if (cookie.length > 0) {
				var start = cookie.indexOf(name + "=");
				if (start !== -1) {
					start = start + name.length + 1;
					var c_end = cookie.indexOf(";", start);
					if (c_end === -1) {
						c_end = cookie.length;
					}
					return unescape(cookie.substring(start, c_end));
				}
			}
			return null;
		},

		getGeneratedCookie : function (val) {
			var config = GravityRD.Core.getConfig()
			var name = config["cookieName"];
			var cId = this.getCookie(name);
			
			if (config.useJsGeneratedCookie) {
				// Cookie TOCTOU workaround
				var pattern = /[a-f0-9]{10,}-[a-f0-9]{16}/;
				if (cId !== null && cId.match(pattern) === null) {
					GravityRD.Core.Request.scriptTagRequest(GravityRD.Core.Request.getEndpoint(false, "JSServlet4"), "ec="+encodeURIComponent("INVALID CID")+"&ex="+encodeURIComponent(cId));
					cId = null;
				}
			}
			
			if (cId === null) {
				if (!GravityRD.isUndefined(val)) {
					cId = val;
				} else {
					var ts= +new Date().getTime();
					cId = ts.toString(16);
					cId += "-"+this.createRandomString();
				}
				GravityRD.Log.debug("Generating cookie ID [forced: " + (cId === val) +"]", cId);
				this.setCookie(name, cId);
			}
			return cId;
		},

		setGeneratedCookie : function (val) {
			var name = GravityRD.Core.getConfig()["cookieName"];
			var cId = this.getCookie(name);
			GravityRD.Log.debug("Generating cookie ID [forced: " + (cId === val) +"]", val);
			this.setCookie(name, val);
		},
		
		retarget: function(cId) {
			var config = GravityRD.Core.getConfig();
			if (config["retargetingTrack"] === true && config["retargeting"] === false) {
				if (GravityRD.Core.Cookie.getCookie("gr_rt") !== cId) {
					var ep = GravityRD.Core.Request.getEndpoint(true, "AdServlet");
					GravityRD.Core.Request.scriptTagRequest(ep, "action=setcookie&cid="+cId+"&customer="+config["partnerId"]);
					GravityRD.Core.Cookie.setCookie('gr_rt', cId, false);
				}
			}
		},

		lastViewedItem : null, 

		recIdMapping : new RecIdMapping(),
		recClickMapping : new RecClickMapping("gr_click"),
		eventHistory : new ClientObject("gr_event"),
		cart: new Cart("gr_cart"),
		persistentNameValues : new ClientObject("gr_persist"),
		sessionNameValues : new ClientSessionObject("gr_ps"),
 
		trackEvents:function (rd) {
			if (rd.eventType === 'VIEW') {
				this.lastViewedItem = rd.itemId;
			} else if (rd.eventType === 'ADD_TO_CART') {
				this.addToCartEvent(rd);
			} else if (rd.eventType === 'REMOVE_FROM_CART') {
				this.removeFromCartEvent(rd);
			} else if (rd.eventType === 'BUY') {
				this.buyEvent(rd);
			}


			var config = GravityRD.Core.getConfig();
			if(config["trackEvents"] === true) {
				var eventType = rd.eventType;
				var itemId = rd.itemId;
				var ta = (config["trackEventsFilter"]||{})[eventType];
				if(ta !== undefined) {
					var pushObj = null;
					var extraFields = false;
					var extra = {};
					var recId = null;
					if(ta.length > 0) {
					
						for(var i=0; i<ta.length; i++) {
							if(rd[ta[i]] !== undefined) {
								extra[ta[i]] = rd[ta[i]];
								extraFields = true;
							}
							if(ta[i] === 'recId') {
								recId = this.getRecId(itemId);
							}
						}
					}
					if(extraFields) {
						pushObj = [eventType, itemId, recId, extra];
					} else {
						if(recId !== null) {
							pushObj = [eventType, itemId, recId];
						} else {
							pushObj = [eventType, itemId];
						}
					}
					this.eventHistory.push(pushObj,config["trackEventsMaxSize"]);	
				}
			}
		}, 

		getLastViewedItem: function() {
			return this.lastViewedItem;
		},

		registerRecId: function (itemId, recId) {
			this.recIdMapping.registerRecId(itemId, recId);
		},

		getRecId: function(itemId) {
			return this.recIdMapping.getRecId(itemId);
		},

		registerRecClickId: function (itemId, recId) {
			GravityRD.Log.debug("Registering recId '"+recId+"' for item '"+itemId+"'");
			this.recClickMapping.setValue(itemId, recId);
		},

		getRecClickId: function(itemId) {
			return this.recClickMapping.getValue(itemId);
		},

		addToCartEvent : function(event) {
			if(GravityRD.Core.getConfig()["trackCart"] === true) {
				this.cart.addItem(event.itemId, event.quantity);
			}
		}, 

		removeFromCartEvent : function(event) {
			if(GravityRD.Core.getConfig()["trackCart"] === true) {
				this.cart.removeItem(event.itemId, event.quantity);
			}
		},

		buyEvent: function(event) {
			if(GravityRD.Core.getConfig()["trackCart"] === true) {
				this.cart.removeItems(event.itemId);
			}
		},

		getTracking: function() {
			return {
				cart: this.cart,
				eventHistory: this.eventHistory
			};
		},

		randHexWord : function () {
			return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
		},

		createRandomString : function () {
			return (this.randHexWord() + this.randHexWord() + this.randHexWord() + this.randHexWord());
		},
		
		addPersistentNameValue: function(name, value) {
			if(value == null) {
				this.persistentNameValues.removeValue(name);
			} else {
				this.persistentNameValues.setValue(name, value);
			}
		},

		addSessionNameValue: function(name, value) {
			if(value == null) {
				this.sessionNameValues.removeValue(name);
			} else {
				this.sessionNameValues.setValue(name, value);
			}
		},
		
		getPersistentNameValues: function() {
			return this.persistentNameValues.getAllValues();
		},
		
		getSessionNameValues: function() {
			return this.sessionNameValues.getAllValues();
		}

	});
	GravityRD.register("GravityRD.Core", "Cookie", new GravityRD_Core_Cookie());
})();

(function() {
	/** Common request parameters are shortened using this mapping. It comes from some legacy code. */
	var shortNameDictionary = [
		{ name: "BUY", value: "e1" },
		{ name: "VIEW", value: "e2" },
		{ name: "ADD_TO_CART", value: "e3" },
		{ name: "REMOVE_FROM_CART", value: "e4" },
		{ name: "ADD_TO_FAVORITES", value: "e5" },
		{ name: "REMOVE_FROM_FAVORITES", value: "e6" },
		{ name: "ADD_TO_WISHLIST", value: "e7" },
		{ name: "REMOVE_FROM_WISHLIST", value: "e8" },
		{ name: "RATING", value: "e9" },
		{ name: "REC_CLICK", value: "e10" },
		{ name: "ItemID", value: "nv1" },
		{ name: "Position", value: "nv2" },
		{ name: "UnitPrice", value: "nv3" },
		{ name: "Quantity", value: "nv4" },
		{ name: "OrderId", value: "nv5" },
		{ name: "Value", value: "nv6" },
		{ name: "MAIN_PAGE", value: "si1" },
		{ name: "CATEGORY_PAGE", value: "si2" },
		{ name: "LISTING_PAGE", value: "si3" },
		{ name: "ITEM_PAGE", value: "si4" },
		{ name: "CART_PAGE", value: "si5" }
	];

	/**
	 * GravityRD.Core.Request API class.
	 * 
	 * Responsible for:
	 *  - handle routing
	 *  - create request to the backend endpoint
	 */
	var GravityRD_Core_Request = GravityRD.Base.extend({
		__public__: ['createRequest', 'setupRouting', 'scriptTagRequest', 'getEndpoint', 'useResultCB', '_T', '_U'],

		routes: [],
		
		routeTargets: {},
		
		nr: 0,
		
		setupRouting: function(routes) {
			var that = this;
			GravityRD.each(routes, function(routeConfig) {
				var rc = GravityRD.apply(routeConfig, {
					predicate: function() {return true;},
					disableDefaultTarget: false,
					additionalTargets: []
				}, false);
				var at = [];
				for(var i=0; i < rc.additionalTargets.length; i++) {
					var cur = rc.additionalTargets[i];
					if (GravityRD.isString(cur)) {
						var ncur = {'partner': cur};
						cur = ncur;
					}
					if (cur.partner === null) {
						GravityRD.Log.error("Partner not set in additionalTargets (skipping configuration): ", routeConfig);
						continue;
					}
					that.routeTargets[cur.partner] = {eventRequests:[]};
					at.push(GravityRD.apply(cur, {
						process : function(event) {return event;}
					}, false));
					
				}
				rc.additionalTargets = at;
				that.routes.push(rc);
			});
			
			if (GravityRD.Core.debug("routing")) {
				GravityRD.Log.group("Routing info");
				for (var i = 0; i < this.routes.length; i++) {
					GravityRD.Log.debug("R" + i, this.routes[i]);
				}
				GravityRD.Log.debug("Route targets", this.routeTargets);
				GravityRD.Log.groupEnd();
				
			}
		},

		getShortNameValue : function (key) {
			var develop = GravityRD.Core.getConfig()["mode"] === 'DEVELOP';
			var ret = "";
			if (key !== null && key !== "") {
				var exist = false;
				var i = 0;
				while (!exist && i < shortNameDictionary.length) {
					exist = (key === shortNameDictionary[i].name);
					i++;
				}
				if (exist && !develop) {
					ret = "." + shortNameDictionary[i - 1].value;
				} else {
					ret = "*" + key;
				}
			}
			return ret;
		},

		_T: function(string) {
			if (string === null || string === undefined) {
				return null;
			}
			string = string.toString().replace(/-/g, "-0").replace(/,/g, "-1").replace(/;/g, "-2")
				.replace(/\[/g,"-3").replace(/\]/g,"-4").replace(/:/g,"-5").replace(/\|/g,"-6");

			return this._E(string);
		},

		_U: function(string) {
			if (string === null || string == undefined) {
				return null;
			}
			string = decodeURIComponent(string);

			string = string.toString().replace(/-6/g,"|").replace(/-5/g,":").replace(/-4/g,"]").replace(/-3/g,"[")
				.replace(/-2/g, ";").replace(/-1/g, ",").replace(/-0/g, "-");

			return string;
		},

		_E : function (string) {
			return encodeURIComponent(string);
		},
		
		_TA: function(arr) {
			var resultNamesEnc = [];
			for(var i=0; i < (arr||[]).length; i++) {
				resultNamesEnc.push(this._T(arr[i]));
			}
			return resultNamesEnc.join(";");
		},
		
		
		getEndpoint: function(serverNameOrRetargeting, servletName, customerName) {
			var config = GravityRD.Core.getConfig();
			var serverName = serverNameOrRetargeting;
			if (!GravityRD.isString(serverNameOrRetargeting)) {
				if (serverNameOrRetargeting) {
					var rs = config["retargetingServer"];
					var rsa = rs.split(".");
					if (rsa[0] == config.partnerId && !!customerName) {
						rsa[0] = customerName;
						serverName = rsa.join(".");
					} else {
						serverName = rs;
					}
				} else {
					serverName = config["targetServer"];
				}
			}
			var proto = document.location.protocol;
			if (proto === "file:") {proto = "http:";}
			if (!!config["forceProtocol"]) {
				proto = config["forceProtocol"] + ":";
			}
			return proto + "//" + serverName + "/grrec-" + (customerName || config.partnerId) + "-war/" + servletName;
		},
		
		useResultCB: function(result) {
		    var rn = result.requestNumber;
		    var rri = []
		    var eri = []
		    
		    if (result && result.recommendationWrappers) {
			for (i = 0; i < result.recommendationWrappers.length; i++) {
			    rri.push(result.recommendationWrappers[i].recommendationIndex);
			}
		    }
		    if (result && result.explanationWrappers) {
			for (var i = 0; i < result.explanationWrappers.length; i++) {
			    eri.push(result.explanationWrappers[i].explanationIndex);
			}
		    }
		    
		    var cb = window["GravityRD"]["callback"+rn +"__" + rri.join("_") + "__"+eri.join("_")];
		    if (cb) cb(result);
		    
		},
		
		scriptTagRequest: function(endpoint, params, rn, rri, eri) {
		    var that = this;

		    var script = document.createElement('script');
		    script.type = 'text/javascript';
		    script.id = 'x_'+(that.nr++);
		    
		    function isIE () {
			var myNav = navigator.userAgent.toLowerCase();
			return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
		    }

		    (function() {
			var lastValue;
			var done = 0;
			var cleanUp = function(i) {
				script["onreadystatechange"] = script["onload"] = script["onerror"] = null;
				(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).removeChild(script)
			}

			
			if (rri != null && eri != null) {
			    window["GravityRD"]["callback"+rn +"__" + rri.join("_") + "__"+eri.join("_")] = function(result) {
				lastValue = result;
			    };
			    // Success notifier
			    var notifySuccess = function(json) {
				    if ( !( done++ ) ) {
					    cleanUp();
					    //callIfDefined( successCallback , xOptions , [ json , STR_SUCCESS, xOptions ] );
					    //callIfDefined( completeCallback , xOptions , [ xOptions , STR_SUCCESS ] );
					    //console.log("SUCCESS");
				    }
			    }

			    // Error notifier
			    var notifyError = function(type) {
				    if (!( done++ )) {
					    cleanUp();
					    //callIfDefined( errorCallback , xOptions , [ xOptions , type ] );
					    //callIfDefined( completeCallback , xOptions , [ xOptions , type ] );
					    GravityRD.Core.errorCB(rn, rri, eri);
				    }
			    }
			    
			    
			    if (isIE() && isIE() < 10) {
				script.htmlFor = script.id;
				script.event = "onclick";
			    }

			    script["onload"] = script["onerror"] = function ( result ) {
				    // Test readyState if it exists
				    if (!script["readyState"] || !/i/.test( script["readyState"])) {
					    try {
						    script["onclick"] && script["onclick"]();
					    } catch( _ ) {}
					    result = lastValue;
					    lastValue = 0;
					    result ? notifySuccess(result[0]) : notifyError("error");
				    }
			    };
			}
			//gr.async = !0;
			// Attached event handlers
			
			script.src = endpoint + "?" + params;
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
		    })();
		},
		
		addNameValue: function(nameValues, n, v) {
 			var wrongAttributes = GravityRD.Core.getConfig()["wrongNameValues"]||[];
 			for (var i=0; i < wrongAttributes.length; i++) {
 				if(wrongAttributes[i] === n) {
 					GravityRD.Log.usageError("GR-022", n);
 				}
 			}
			if(Object.prototype.toString.apply(v) === '[object Array]') {
				if(v.length > 0) {
					var s = "";
					for(var i=0; i < v.length; i++) {
						s += (s==""?"":"|")+ this._T(v[i]);
					}
					nameValues.push(this._T(this.getShortNameValue(n)) + ":" + s);
				}
			} else {
				nameValues.push(this._T(this.getShortNameValue(n)) + ":" + this._T(v));
			}
		},

		serializeEvent: function(event) {
			var eventDec = [];
			eventDec.push(this._T(this.getShortNameValue(event.eventType)));
			eventDec.push(this._T(event.itemId));

			var nameValues = [];
			for(var k in event) {
				if (typeof(event[k]) === "function") continue;
				var kl = String(k).toLowerCase();
				if(kl.indexOf("__") === 0)
					continue;
				if("type" === kl || "eventtype" === kl || "itemid" === kl)
					continue;
				if(event[k] !== undefined) {
					this.addNameValue(nameValues, k, event[k]);
				}
			}
			if(nameValues.length !== 0) {
				eventDec.push("[" + nameValues.join(";") + "]");
			}		
			var eventSer = eventDec.join(",")		
			return eventSer;
		},	

		createGlobalData: function(addCookie, addUser, dontSetCookie) {

			if (GravityRD.isUndefined(addCookie)) 
				addCookie = true;
			if (GravityRD.isUndefined(addUser)) 
				addUser = true;
			if (GravityRD.isUndefined(dontSetCookie)) 
				dontSetCookie = false;

			var globalRecoData = [];
			var config = GravityRD.Core.getConfig();
			
			globalRecoData.push({name:'rn', value:GravityRD.Core.getRequestNumber()});
			
			if (config.userId !== null && addUser) {
				globalRecoData.push({name:'uid', value: this._E(config.userId)});
			}
			
			var cookieval = null;
			if (!config.retargeting && addCookie) {
				cookieval = config.cookieId;
				if (config.useJsGeneratedCookie) {
					cookieval = GravityRD.Core.Cookie.getGeneratedCookie();				
				}
                GravityRD.Core.Cookie.retarget(cookieval);
			}
			// else {
			//     cookieval = GravityRD.Core.Cookie.getCookie("gr_reco_"+config.partnerId);				
			// } 

			if (cookieval !== null) {
				globalRecoData.push({name:'cid', value: this._E(cookieval)});
			}
			
			globalRecoData.push({name:'v', value: GravityRD.Core.getConfig()["buildSHA"]});
			
			if (dontSetCookie) {
				globalRecoData.push({name:'dsc', value: 'true'});
			}
			
			return globalRecoData;
		},
		
		/**
		 * Prepares all event, recommendation, explanation requests to be sent to the backend endpoint.
		 */
		createRequest: function(config, params) {

			if(config.partnerId === null) {
				GravityRD.Log.usageError("GR-016");
				return;	
			}
			if (config.retargeting === false) {
				if(config.cookieId === null && config.useJsGeneratedCookie === false) {
					GravityRD.Log.usageError("GR-017");
				}
				if(config.cookieId !== null && config.useJsGeneratedCookie === true) {
					GravityRD.Log.usageError("GR-018");
				}
			}
			
			for (var i=0; i< params.recommendationRequests.length; i++) {
				params.recommendationRequests[i].__stag_time = +new Date()
			}
			
			params.__perf = GravityRD.Perf.create();

			var globalRecoData = this.createGlobalData();
			var eventRequests = [];
			var recommendationRequests = [];
			var explanationRequests = [];


			
			// EVENTS
			var routedebug = GravityRD.Core.debug("routing");
			routedebug && GravityRD.Log.group("Route matching");
				
			if (params.events.length > 0) {
				for (var i = 0; i < params.events.length; i++) {
					var event = GravityRD.apply({},params.events[i]);
					if (routedebug) {
						GravityRD.Log.group("Event " + event.eventType);
						GravityRD.Log.debug("Trying to match event", event);
					}
					var matched = false;
					var route = null;
					for (var j=0; j < this.routes.length && !matched; j++) {
						route = this.routes[j];
						matched = !!(new RegExp(route.eventType)).exec(event.eventType) && route.predicate(event);
						
						if (matched) {
							routedebug && GravityRD.Log.debug("[+] Route matched", route);
								
							if (!route.disableDefaultTarget)
								eventRequests.push(this.serializeEvent(event));
							
							for (var k=0; k < route.additionalTargets.length; k++) {
								var at = route.additionalTargets[k];
								var pev = at.process(event);
								if (pev !== null) 
									this.routeTargets[at.partner].eventRequests.push(this.serializeEvent(pev));
							}
							break;
						} else {
							routedebug && GravityRD.Log.debug("[-] Route not matched", route);
						}
					}
					
					if (!matched)
						eventRequests.push(this.serializeEvent(event));						

					routedebug && GravityRD.Log.groupEnd();
				}
			}
			
			routedebug && GravityRD.Log.groupEnd();
			
			// RECO REQUEST
			var remaining = [];

			if (params.recommendationRequests.length > 0) {
				for(var i=0; i<params.recommendationRequests.length;i++) {
					var rr = params.recommendationRequests[i];
					if(rr.groupId !== undefined) {
						if(rr.__completed !== true) {
							remaining.push(rr);
							continue;
						} else {
							if(rr.groupSeq === 1) {
								var ss = this.serializeRecRequest(rr);
								for(var last = rr; last.__next !== undefined; last = last.__next) {
                                    ss += this.serializeRecRequest(last.__next);
								}
			
								recommendationRequests.push(ss);		
							}
						}		
					} else {
						recommendationRequests.push(this.serializeRecRequest(rr));
					}
				}
			}
			
			// EXPLANATION REQUEST
			
			if (params.explanationRequests.length > 0) {
				for(var i=0; i<params.explanationRequests.length;i++) {
					var rr = params.explanationRequests[i];
					var rrDec = [];
					rrDec.push(rr.explanationIndex);
					rrDec.push(this._T(rr.scenarioId));
					rrDec.push(this._T(rr.numberLimit));
					rrDec.push(this._T(rr.recommendationId));
					rrDec.push( "[" + this._TA(rr.resultNames) + "]" );
					rrDec.push( "[" + this._TA(rr.recommendedItemIds) + "]" );
	
					var nameValues = [];
					for(var k in rr) {
						if (typeof(rr[k]) === "function") continue;
						var kl = String(k).toLowerCase();
						if(kl.indexOf("__") === 0)
							continue;
						if("explanationindex" === kl || "type" === kl || "scenarioid" === kl 
							|| "numberlimit" === kl || "templating" === kl || "resultnames" === kl
							|| "callback" === kl || "recommendeditemids" === kl || "recommendationid" === kl)
							continue;
						this.addNameValue(nameValues, k, rr[k]);
					}
					rrDec.push((nameValues.length > 0) ? "[" + nameValues.join(";") + "]" : "");
					
					explanationRequests.push(rrDec.join(","));
				}
			}

			params.__perf.phase('preprocess');

			GravityRD.Core.saveParams(remaining);

			var ep = GravityRD.Core.Request.getEndpoint(config.retargeting, "JSServlet4");
			this.createScriptTagRequests(ep, globalRecoData, eventRequests, recommendationRequests, explanationRequests, params.__perf);
			

			var config = GravityRD.Core.getConfig();
			
			for (var customer in this.routeTargets) {
				var useUser = (config.routingGlobal[customer]||{}).useUser || config.routingGlobal.useUser || false;
				var globalData = this.createGlobalData(false, useUser, true);

				var routeTarget = this.routeTargets[customer];
				if (routeTarget.eventRequests.length > 0) {
					var ep = GravityRD.Core.Request.getEndpoint(true, "JSServlet4", customer);
					this.createScriptTagRequests(ep, globalData, routeTarget.eventRequests, [], [], params.__perf);	
				}
			};
		},

		serializeRecRequest: function(rr) {
			var rrDec = [];
			rrDec.push(rr.recommendationIndex);
			rrDec.push(this._T(rr.scenarioId));
			if(rr.templating && rr.templating.serverSide){
                rrDec.push(rr.templating.templateId);
            }
			rrDec.push(this._T(rr.numberLimit || 0));
			var nameValues = [];
			for(var k in rr) {
				var kl = String(k).toLowerCase();
				if(kl.indexOf("__") === 0)
					continue;
				if (typeof(rr[k]) === "function") continue;
				if("recommendationindex" === kl || "type" === kl || "scenarioid" === kl 
					|| "numberlimit" === kl || "templating" === kl|| "resultnames" === kl
					|| "callback" === kl)
					continue;
				this.addNameValue(nameValues, k, rr[k]);
			}
			rrDec.push((nameValues.length > 0) ? "[" + nameValues.join(";") + "]" : "");
			rrDec.push( "[" + this._TA(rr.resultNames) + "]" );

            var serialized = rrDec.join(",");
			if(rr.templating && rr.templating.serverSide){
                serialized = "&rs=" + rrDec.join(",");
            }else{
                serialized = "&rd=" + rrDec.join(",");
            }

            return serialized
		},
		
		serializeCart: function() {
			var cart = GravityRD.Core.Cookie.getTracking()["cart"].get()||{};
			var cartS = "";
			var cartItemIds = [];
			var empty = true;
			for(var i in cart) {
				if(cart[i] !== null) {
					empty = false;
					cartItemIds.push(i);
				}
			}
			if(!empty) {
				var nameValues = [];
				this.addNameValue(nameValues, "CartItemId", cartItemIds);
				cartS = (nameValues.length > 0) ? "[" + nameValues.join(";") + "]" : "";
			}
			return cartS;
		}, 

		serializeEventHistory: function(maxsize) {

			var eventHistory = GravityRD.Core.Cookie.getTracking()["eventHistory"].get()||[];
			var eventHistoryS = "";
			for(var i=eventHistory.length-1; i>0; i--) {
				var eventDesc = eventHistory[i];
				var event = {type:'event', eventType:eventDesc[0], itemId:eventDesc[1]};
				if(eventDesc[2] !== undefined && eventDesc[2] !== null) {
					event.recId = eventDesc[2];
				}
				if(eventDesc[3] !== undefined) {
					GravityRD.apply(event, eventDesc[3]);
				}

				if(maxsize > 0 && (eventHistoryS.length + 1 + this.serializeEvent(event).length) > maxsize)
					break;

				eventHistoryS = this.serializeEvent(event) +  (eventHistoryS=="" ? "":"|") + eventHistoryS;
			}
			return eventHistoryS;
		},

		/**
		 * Split the request into multiple parts, so each subrequest is smaller than 2048 bytes if possible.
		 */
		createScriptTagRequests: function(endpoint, globalRecoData, eventRequests, recommendationRequests, explanationRequests, perf) {
			var queryLimit = 2048 - 5 /*indicating truncate*/ - 11 /*random identifier per request*/;

			var gparms = [];
			for(var i=0; i < globalRecoData.length; i++) {
				gparms.push(globalRecoData[i].name+"="+globalRecoData[i].value);	
			}
			var baseUrl = gparms.join("&");
			var maxrun = recommendationRequests.length + eventRequests.length + explanationRequests.length;

			while(recommendationRequests.length !== 0 || eventRequests.length !== 0 || explanationRequests.length !== 0) {
				if(maxrun-- < 0) {
					GravityRD.Log.error("Possible infinite loop");
					break;
				}

				var src = baseUrl;
				var hadEvent = false;
				var hadRecommendation = false;

				if(eventRequests.length > 0) {
					hadEvent = true;
					if((eventRequests[0].length+3) > (queryLimit - src.length)) {
						src += "&e=" + eventRequests[0];
						eventRequests = eventRequests.slice(1);
						src += "&tr=1";
						GravityRD.Log.error("Query limit is too small to push event request");
						this.loadScripts(endpoint, src, null, null, perf);
						continue;
					} else {
						while(eventRequests.length > 0 && (eventRequests[0].length+3) < (queryLimit - src.length)) {
							src += "&e=" + eventRequests[0];
							eventRequests = eventRequests.slice(1);
						}
					}
				}
				
				var rri = []
				var eri = []
				
				if(recommendationRequests.length > 0) {
					var cartS = this.serializeCart();
					var histS = this.serializeEventHistory(0);
					if((recommendationRequests[0].length+4+cartS.length+5+histS.length+4) >= (queryLimit - src.length)) {
						if(hadEvent) {
							GravityRD.Log.debug("Pushing events");
							this.loadScripts(endpoint, src, null, null, perf);
							continue;	
						}
						var rrs = recommendationRequests[0];
						src += rrs;
						rri.push(rrs.split(",")[0].split("=")[1])
						src += "&grd=" + cartS;
						var remaining = queryLimit - src.length-4;
						if(remaining  > 0) {
							GravityRD.Log.debug("Remaining: " + remaining);
							src += "&eh=" + this.serializeEventHistory(remaining);
						}	
						src += "&tr=2";
						recommendationRequests = recommendationRequests.slice(1);
						GravityRD.Log.error("Query limit is too small to push recommendation request");
						this.loadScripts(endpoint, src, rri, eri, perf);
						rri = []
						eri = []
						continue;
					} else {
						src += "&grd=" + cartS;
						src += "&eh="  + this.serializeEventHistory(0);

						while(recommendationRequests.length > 0 && (recommendationRequests[0].length+4) < (queryLimit - src.length)) {
							var rrs = recommendationRequests[0];
							src += rrs;
							rri.push(rrs.split(",")[0].split("=")[1])
							recommendationRequests = recommendationRequests.slice(1);
						}
					}
				}

				if(explanationRequests.length > 0) {
		
					if((explanationRequests[0].length+4) >= (queryLimit - src.length)) {
						if(hadEvent || hadRecommendation) {
							GravityRD.Log.debug("Pushing events");
							this.loadScripts(endpoint, src, rri, eri, perf);
							rri = []
							eri = []
							continue;	
						}

						var ers = explanationRequests[0];
						eri.push(ers.split(",")[0])
						src += "&xp=" + ers;
						var remaining = queryLimit - src.length-4;
						src += "&tr=3";
						explanationRequests = explanationRequests.slice(1);
						GravityRD.Log.error("Query limit is too small to push recommendation request");
						this.loadScripts(endpoint, src, rri, eri, perf);
						rri = []
						eri = []
						continue;
					} else {
						while(explanationRequests.length > 0 && (explanationRequests[0].length+4) < (queryLimit - src.length)) {
							var ers = explanationRequests[0];
							eri.push(ers.split(",")[0])
							src += "&xp=" + ers;
							explanationRequests = explanationRequests.slice(1);
						}
					}
				}
				this.loadScripts(endpoint, src, rri, eri, perf);
			}
		}, 

		/**
		 * Create the actual request using script-tag jsonp.
		 */
		loadScripts: function(endpoint, url, rri, eri, perf) {
			GravityRD.Log.timeStamp("GR:LOAD(" + (GravityRD.Core.getRequestNumber()-1)+")");
			perf.phase('loadscripts');

			GravityRD.Log.group("LoadScripts " + endpoint, true);
			url.replace(/([^&]+)&?/g, function(_, p) {
				GravityRD.Log.debug(p);	
			});
			GravityRD.Core.Request.scriptTagRequest(endpoint, url + "&r=" + GravityRD.Core.Cookie.randHexWord()+GravityRD.Core.Cookie.randHexWord(), GravityRD.Core.getRequestNumber()-1, rri, eri);
			GravityRD.Log.groupEnd();
			perf.phase('loadscriptsend');
		}
	});
	GravityRD.register("GravityRD.Core", "Request", new GravityRD_Core_Request());
})();


(function () {
	/** Predefined list of possible error messages. */
	var ErrorMessages = {
		"GR-001": "Attribute '#1#' should have '#2#' type",
		"GR-002": "Dom element with id '#1#' not found",
		"GR-003": "Attribute '#1#' shouldn't be null",
		"GR-004": "Parameter [#1#] of #2# should not be undefined",
		"GR-005": "Parameter [#1#] of #2# should not be null",
		"GR-006": "Parameter [#1#] of #2# should have '#3#' type",
		"GR-007": "Invalid push item specified: '#1#'. Parameters to push() should have object type",
		"GR-008": "Invalid type attribute in push item specified: '#1#'. Possible candidates: [#2#]",
		"GR-009": "Invalid eventType attribute in push item specified: '#1#'. Possible candidates: [#2#]",
		"GR-010": "Missing mandatory parameter: '#1#'. Push object: '#2#'",
		"GR-011": "Invalid attribute in push item specified: '#1#'. Possible candidates: [#2#]",
		"GR-012": "Either 'templating' or 'callback' attribute should be specified in recommendation push object",
		"GR-013": "Both template and templateElementId specified",
		"GR-014": "Neither template nor templateElementId specified",
		"GR-015": "Initializing of templates failed",
		"GR-016": "Please configure the 'partnerId'",
		"GR-017": "CookieId is null, and autogeneration of cookies is disabled",
		"GR-018": "CookieId is not null, and autogeneration of cookies is enabled",
		"GR-019": "You don't have enough privileges to access this function",
		"GR-020": "Array parameter [#1#] should have at least one element.",
		"GR-021": "The value [#1#] for attribute '#2#' is suspicious. If you are sure it is valid, please contact us, to disable this feature!",
		"GR-022": "Invalid push attribute [#1#] specified. Maybe you should try: {type:'set', #1#: [...]}",
		"GR-023": "No 'groupSeq' attribute defined for group [#1#]",
		"GR-024": "No 'groupSize' attribute defined for group [#1#]",
		"GR-025": "Invalid 'groupSequence' attribute specified for group [#1#]. Got: #2#, expected: [#3#]}",
		"GR-026": "Invalid 'groupSize' attribute specified for group [#1#], groupSize: [#2#]",
		"GR-027": "Invalid 'groupSize' attribute specified for group [#1#], groupSize: [#2#], expected: [#3#]",
		"GR-028": "Exception thrown by template rendering: '#1#'",
		"GR-029": "Loading the javascript framework from this domain is not allowed",
		"GR-030": "Server side and client side template rendering is not allowed on the same recommendation. (Use template or templateId, but not both)."
	};

	/** 
	 * This is a special logger which sends the output of the debug console from the client to the server.
	 * This class is not wired-in into the framework, it can be used in special debugging cases.
	 */
	var GravityRD_Log_Logback = GravityRD.Base.extend({

		__public__: ["debug", "error", "info", "usageError", "validationError", "flush", "message"],
		message : function (msg, obj) {
			var res = msg + " ";
			if (obj !== undefined)
				res += obj;
			res += "\n";
			return res;
		},
		debug: function (msg, obj) {
			this.log += GravityRD.message(msg, obj);
		},

		info: function (msg, obj) {
			this.log += GravityRD.message(msg, obj);
		},

		error: function (msg, obj) {
			this.log += GravityRD.message("ERROR: " + msg, obj);
		},

		usageError: function (msg, obj) {
			this.log += GravityRD.message("USAGEERROR: " + msg, obj);
		},

		validationError: function (msg, obj) {
			this.log += GravityRD.message("VALIDATIONERROR: " + msg, obj);
		},

		flush: function () {
			var ss = 1600;
			for (var p = 0; p + ss < this.log.length; p += ss) {
				var config = GravityRD.Core.getConfig();
				var ep = GravityRD.Core.Request.getEndpoint(config.retargeting, "JSServlet4");
				GravityRD.Core.Request.scriptTagRequest(ep, "ex=CUSTOM&ec=" + encodeURIComponent(this.log.slice(p, p + ss)));
			}
			this.log = "";
		}
	});

	/** 
	 * GravityRD.Log API class.
	 *
	 * Default console logger implementation. It supports an advanced logging and console interface, and if the underlying console doesn't support those (IE ofc), then emulates its behavior.
	 */
	var GravityRD_Log_Firebug = GravityRD.Base.extend({
		__public__: ["debug", "error", "info", "group", "groupEnd", "timeStamp", "time", "timeEnd", "usageError", "validationError"],
		prefixes: [],

		//assert, clear, count, debug, dir, dirxml, exception, profile, profileEnd, table, trace, warn, markTimeline
		//http://getfirebug.com/wiki/index.php/Console_API

		haveConsole: function () {
			return (typeof (console) !== "undefined");
		},

		haveGroup: function () {
			return this.haveConsole() &&
				GravityRD.isFunction(console.group) &&
				GravityRD.isFunction(console.groupCollapsed) &&
				GravityRD.isFunction(console.groupEnd) &&
				GravityRD.Core.getConfig()["mode"] === 'DEVELOP';
		},

		debug: function (msg, obj) {
			if (this.haveConsole()) {
				if (GravityRD.Core.getConfig()["mode"] === 'DEVELOP') {
					if (obj !== undefined) {
						console.log(this.prefix() + msg, obj);
					} else {
						console.log(this.prefix() + msg);
					}

				}
			}
		},

		group: function (groupName, collapsed) {
			if (this.haveGroup()) {
				if (collapsed === true) {
					console.groupCollapsed(groupName);
				} else {
					console.group(groupName);
				}
			} else {
				this.prefixes.push(groupName);
			}
		},

		prefix: function () {
			if (!this.haveGroup()) {
				return this.prefixes.join("::") + "::";
			} else {
				return "";
			}
		},

		groupEnd: function () {
			if (this.haveGroup()) {
				console.groupEnd();
			} else {
				this.prefixes.pop();
			}
		},

		info: function (msg, obj) {
			if (this.haveConsole()) {
				if (obj !== undefined) {
					console.info(this.prefix() + msg, obj);
				} else {
					console.info(this.prefix() + msg);
				}
			}
		},

		error: function (msg, obj) {
			if (this.haveConsole()) {
				if (obj !== undefined) {
					console.error(this.prefix() + msg, obj);
				} else {
					console.error(this.prefix() + msg);
				}
			}
		},

		timeStamp: function (str) {
			if (GravityRD.Core.debug("perf") && this.haveConsole() && GravityRD.isFunction(console.timeStamp)) {
				console.timeStamp(str);
			}
		},

		time: function (str) {
			if (GravityRD.Core.debug("perf") && this.haveConsole() && GravityRD.isFunction(console.time)) {
				console.time(str);
			}
		},

		timeEnd: function (str) {
			if (GravityRD.Core.debug("perf") && this.haveConsole() && GravityRD.isFunction(console.timeEnd)) {
				console.timeEnd(str);
			}
		},

		usageError: function (msg) {
			var error = msg;
			if (msg.indexOf("GR-") === 0 && ErrorMessages[msg] !== undefined) {
				error = msg + ":" + ErrorMessages[msg];
				for (var i = 1; i < arguments.length; i++) {
					error = error.replace(new RegExp("#" + i + "#", "img"), arguments[i]);
				}
			} else {
				this.error("Invalid error message specified");
			}

			if (this.haveConsole()) {
				console.error(error);
			}
			var config = GravityRD.Core.getConfig();

			if (config["logExceptions"] === true) {
				var ep = GravityRD.Core.Request.getEndpoint(config.retargeting, "JSServlet4");
				GravityRD.Core.Request.scriptTagRequest(ep, "ex=" + encodeURIComponent(error) + "&ec=" + encodeURIComponent(msg));
			}
			GravityRD.Core.onUsageError(error + ' @' + window.location.href);
			
			if (!(msg === "GR-021")) {
				throw new GravityRD.Exc(GravityRD.Exc.APIUSAGE, error);
			}
		},

		validationError: function (errors) {
			if (this.haveConsole()) {
				for (var i = 0; i < errors.length; i++) {
					GravityRD.Log.error("Error received from server, code=" + errors[i].code + ", message=" + errors[i].msg);
				}
			}

			throw new GravityRD.Exc(GravityRD.Exc.VALIDATION, errors[0].code + ":" + errors[0].msg);
		}

	});


	GravityRD.register("GravityRD", "Log", new GravityRD_Log_Firebug());
})();
(function () {
	var AbstractTypechecker = GravityRD.Base.extend({});

	/** Function argument typechecker class*/
	var ArgumentTypechecker = AbstractTypechecker.extend({

		constructor: function (name, args) {
			this.name = name;
			this.args = args;
		},

		validArg: function (pos) {
			return (this.args.length > pos);
		},

		notNull: function (pos) {
			if (!this.validArg(pos) || GravityRD.isNull(this.args[pos])) {
				GravityRD.Log.usageError("GR-005", (pos + 1), this.name);

			}
			return this;

		},

		notUndefined: function (pos) {
			if (!this.validArg(pos) || GravityRD.isUndefined(this.args[pos])) {
				GravityRD.Log.usageError("GR-004", (pos + 1), this.name);
			}
			return this;
		},

		isObject: function (pos) {
			if (!this.validArg(pos) || !GravityRD.isObject(this.args[pos])) {
				GravityRD.Log.usageError("GR-006", (pos + 1), this.name, 'object');
			}
			return this;
		}
	});

	/**
	 * Its a schema-like description for the available push requests. Events are handled by a special way.
	 */
	var GlobalTypeDescription = {
		'set': {
			strict: false,
			optional: {
				'partnerId': {
					type: 'string'
				},
				'userId': {
					type: 'string',
					nullString: true,
					allowNull: true
				},
				'cookieName': {
					type: 'string',
					nullString: true
				},
				'cookieId': {
					type: 'string',
					nullString: true
				},
				'useJsGeneratedCookie': {
					type: 'boolean'
				}
			}
		},
		'register-recid': {
			strict: true,
			mandatory: {
				'itemId': {
					type: 'string',
					array: true,
					nullString: true
				},
				'recId': {
					type: 'string'
				}
			}
		},
		'event': true,
		'domready': {
			mandatory: {
				'apply': {
					type: 'function'
				}
			}
		},
		'customconfig': {
			mandatory: {
				'config': {
					type: 'object'
				}
			}
		},
		'fakeexception': {},
		'recommendation': {
			mandatory: {
				'scenarioId': {
					type: 'string'
				}
			},
			optional: {
				'numberLimit': {
					type: 'integer'
				},
				'resultNames': {
					type: 'string',
					array: true
				},
				'callback': {
					type: 'function'
				},
				'groupId': {
					type: 'string'
				},
				'groupSeq': {
					type: 'integer'
				},
				'groupSize': {
					type: 'integer'
				},
				'templating': {
					type: 'object',
					descriptor: {
						mandatory: {
							'targetElementId': {
								type: 'dom'
							}
						},
						optional: {
							'templateElementId': {
								type: 'dom'
							},
							'template': {
								type: 'string'
							},
							'templateData': {
								type: 'object'
							},
							'targetElementId': {
								type: 'dom'
							},
							'modifiers': {
								type: 'object'
							}
						}
					}
				}
			}
		},
		'explanation': {
			mandatory: {
				'scenarioId': {
					type: 'string'
				},
				'numberLimit': {
					type: 'integer'
				},
				'recommendedItemIds': {
					type: 'string',
					array: true
				},
				'recommendationId': {
					type: 'string'
				}
			},
			optional: {
				'resultNames': {
					type: 'string',
					array: true
				},
				'callback': {
					type: 'function'
				},
				'templating': {
					type: 'object',
					descriptor: {
						mandatory: {
							'targetElementId': {
								type: 'dom'
							}
						},
						optional: {
							'templateElementId': {
								type: 'dom'
							},
							'template': {
								type: 'string'
							},
							'templateData': {
								type: 'object'
							},
							'targetElementId': {
								type: 'dom'
							},
							'modifiers': {
								type: 'object'
							}
						}
					}
				}
			}
		},
		search: {
			mandatory: {
				count: {
					type: 'integer'
				},
				inputSelector: {
					type: 'string'
				},
				name: {
					type: 'string'
				}
			},
			optional: {}
		}
	};

	var defEvDesc = {
		mandatory: {
			'eventType': {
				type: 'string'
			},
			'itemId': {
				type: 'string',
				nullString: true
			}
		}
	};
	var emptyEvDesc = {
		mandatory: {
			'eventType': {
				type: 'string'
			}
		}
	};

	/** Type validation description of different event-types. We only check if itemId exists if mandatory for the event. */
	var EventTypeDescription = {
		'FEEDBACK': defEvDesc,
		'BUY': defEvDesc,
		'MARK_BY_STAR': defEvDesc,
		'MARK_FOR_LATER_WATCHING': defEvDesc,
		'ADD_TO_FAVORITES': defEvDesc,
		'USER_RECOMMEND': defEvDesc,
		'RATING': defEvDesc,
		'LANCE': defEvDesc,
		'VIEW': defEvDesc,
		'ADD_TO_CART': defEvDesc,
		'REC_CLICK': defEvDesc,
		'HIDE': defEvDesc,
		'FREE_VIEW': defEvDesc,
		'PAID_VIEW': defEvDesc,
		'PRODUCT_SEARCH': emptyEvDesc,
		'LETTER_SEND': emptyEvDesc,
		'TEST_FILL': emptyEvDesc,
		'LOGIN': emptyEvDesc,
		'SHOW_ITEM': emptyEvDesc,
		'SMS_SEND': emptyEvDesc,
		'SUBSCRIBE': emptyEvDesc,
		'LETTER_READ': emptyEvDesc,
		'WATCH_LIVE': defEvDesc,
		'WATCH_RECORDED': defEvDesc,
		'WATCH_VOD_TRAILER': defEvDesc,
		'WATCH_VOD': defEvDesc,
		'RECORD': defEvDesc,
		'REMOVE_FROM_FAVORITES': defEvDesc,
		'SHOW_RECOMMENDATION': emptyEvDesc,
		'NEXT_RECOMMENDATION': emptyEvDesc,
		'ADD_TO_WISHLIST': defEvDesc,
		'REMOVE_FROM_WISHLIST': defEvDesc,
		'REMOVE_FROM_CART': defEvDesc,
		'CLICK_OUT': defEvDesc,
		'UNHIDE': defEvDesc,
		'SHARE': defEvDesc,
		'ASK_QUESTION': emptyEvDesc,
		'COMMENT': defEvDesc,
		'ADD_ITEM': defEvDesc,
		'ADD_FRIEND': emptyEvDesc,
		'DELETE_ITEM': defEvDesc,
		'SUBSCRIPTION_VIEW': defEvDesc,
		'FOLLOW_USER': emptyEvDesc,
		'NOT_INTERESTED': defEvDesc,
		'SMS_RECEIVE': emptyEvDesc,
		'REDEEM': defEvDesc,
		'BROWSE': emptyEvDesc,
		'PREMIUM_VIEW': defEvDesc,
		'CHAT': emptyEvDesc,
		'LIKE': defEvDesc,
		'DISLIKE': defEvDesc,
		'BUY_INIT': emptyEvDesc,
		'LEAVE_PAGE': emptyEvDesc,
		'COMPARE': emptyEvDesc,
		'LETTER_CLICK': emptyEvDesc,
		'SMS_START': emptyEvDesc,
		'GIFT_CLICK': emptyEvDesc,
		'GIFT_SEND': emptyEvDesc,
		'UNSUBSCRIBE': emptyEvDesc,
		'MODIFY_ITEM': defEvDesc,
		'QUANTITY_CHECK': defEvDesc,
		'CLICK': defEvDesc,
		'TRACK': emptyEvDesc,
		'RETURN': defEvDesc,
		'PHONE_CLICK': defEvDesc,
		'ADD_TO_PLAYLIST': defEvDesc,
		'PRINT_CLICK': defEvDesc,
		'LINK_MODULE_CLICK': defEvDesc,
		'GALLERY_CLICK': defEvDesc,
		'SEARCH': emptyEvDesc,
		'REMOVE_FROM_PLAYLIST': emptyEvDesc,
		'WATCHED': emptyEvDesc,
		'UNFOLLOW_USER': emptyEvDesc,
		'BID': defEvDesc,
		'IMPRESSION': defEvDesc,
		'DOWNLOAD': defEvDesc,
		'EMAIL_ALERT': emptyEvDesc,
		'ADD_TO_WATCH_LIST': defEvDesc,
		'USER_REGISTRATION': emptyEvDesc,
		'POST_ITEM': emptyEvDesc,
		'ITEM_ON_SCREEN': defEvDesc,
		'REC_ON_SCREEN': emptyEvDesc,
		'AGGREGATOR': emptyEvDesc,
		'ACTION_TRIGGERED': emptyEvDesc,
		'INTERNAL_A': emptyEvDesc,
		'INTERNAL_B': emptyEvDesc,
		'INTERNAL_C': emptyEvDesc,
		'INTERNAL_D': emptyEvDesc,
		'ERROR': emptyEvDesc,
		'APPLY':defEvDesc,
		'WATCH_LIVE_OTT' : defEvDesc,
		'WATCH' : defEvDesc,
		'BUY_CATCHUP'  : defEvDesc,
		'WATCH_CATCHUP' : defEvDesc,
		'WATCH_VOD_OTT' : defEvDesc,
		'SUBSCRIPTION_VIEW_OTT' : defEvDesc,
		'BUY_ANONYM'  : defEvDesc,
		'WATCH_VOD_ANONYM' : defEvDesc,
		'SUBSCRIPTION_VIEW_ANONYM' : defEvDesc,
		'WATCH_CATCHUP_ANONYM' : defEvDesc,
		'ADD_TO_BOOKMARK': defEvDesc
	};

	/** Check for string which indicate null possibly */
	var NullStringChecker = AbstractTypechecker.extend({

		constructor: function (name) {
			var config = GravityRD.Core.getConfig();
			this.name = name;
			this.nullStrings = config['nullStrings'];
		},
		checkNullString: function (val) {
			for (var i = 0; i < this.nullStrings.length; i++) {
				if (this.nullStrings[i] === val) {
					GravityRD.Log.usageError("GR-021", val, this.name);
				}
			}
		}
	});

	/**
	 * Checking the validity of a push-object, by applying the rules defined above.
	 */
	var PushObjectTypeChecker = AbstractTypechecker.extend({
		obj: null,
		constructor: function (obj) {
			this.obj = obj;
		},

		validate: function () {
			if (!GravityRD.isObject(this.obj)) {
				GravityRD.Log.usageError("GR-007", this.obj);
			}
			var type = this.obj["type"], k, candidates;
			if (GravityRD.isString(type) !== true || GlobalTypeDescription[type] === undefined) {
				candidates = "";
				for (k in GlobalTypeDescription) {
					if (!GlobalTypeDescription.hasOwnProperty(k)) {
						continue;
					}
					candidates += ((candidates === "") ? k : ("," + k));
				}
				GravityRD.Log.usageError("GR-008", type, candidates);
			}

			var descriptor;
			if ("event" == type) {
				var subType = this.obj["eventType"];

				if (GravityRD.isString(subType) !== true || EventTypeDescription[subType] === undefined) {
					candidates = "";
					for (k in EventTypeDescription) {
						if (!EventTypeDescription.hasOwnProperty(k)) {
							continue;
						}
						candidates += ((candidates === "") ? k : ("," + k));
					}
					GravityRD.Log.usageError("GR-008", subType, candidates);
				}
				descriptor = EventTypeDescription[subType];
			} else {
				descriptor = GlobalTypeDescription[type];
			}

			this.validateAttributes(this.obj, descriptor);

		},

		validateAttributes: function (obj, descriptor) {
			if (GravityRD.isUndefined(descriptor)) return;
			var mandatory = descriptor.mandatory || {};
			var optional = descriptor.optional || {};

			var candidates = "", k;
			for (k in mandatory) {
				if (!mandatory.hasOwnProperty(k)) {
					continue;
				}
				candidates += ((candidates === "") ? k : ("," + k));
			}
			for (k in optional) {
				if (!optional.hasOwnProperty(k)) {
					continue;
				}
				candidates += ((candidates === "") ? k : ("," + k));
			}

			for (k in mandatory) {
				if (!mandatory.hasOwnProperty(k)) {
					continue;
				}
				if (obj[k] === undefined) {
					GravityRD.Log.usageError("GR-010", k, obj);
				}
			}
			for (k in obj) {
				if (!obj.hasOwnProperty(k)) {
					continue;
				}
				if (k === 'type')
					continue;
				if (mandatory[k] !== undefined) {
					this.validateAttribute(k, obj, mandatory[k], true);
				} else if (optional[k] !== undefined) {
					this.validateAttribute(k, obj, optional[k], false);
				} else {
					if (descriptor.strict === true) {
						GravityRD.Log.usageError("GR-011", k, candidates)
					}
				}
			}
		},

		validateAttribute: function (attrname, attrs, descriptor) {
			var attrvalue = attrs[attrname];
			if (GravityRD.isNull(attrvalue)) {
				if (descriptor['allowNull'] === true) {
					return;
				} else {
					GravityRD.Log.usageError("GR-003", attrname);
				}
			}

			if (descriptor['array'] === true) {
				if (!GravityRD.isArray(attrvalue)) {
					var val = [];
					val.push(attrvalue);
					attrs[attrname] = val;
					attrvalue = val;
				}

				if (attrvalue.length == 0) {
					GravityRD.Log.usageError("GR-020", attrname);
				} else {
					attrvalue = attrvalue[0];
				}
			}

			if (descriptor['type'] !== undefined) {
				var type = descriptor['type'];
				if (type === 'string') {
					if (!GravityRD.isString(attrvalue)) {
						GravityRD.Log.usageError("GR-001", attrname, type);
					} else {
						if (descriptor['nullString'] === true) {
							new NullStringChecker(attrname).checkNullString(attrvalue);
						}
					}
				} else if (type === 'integer') {
					if (typeof (attrvalue) !== 'number') {
						GravityRD.Log.usageError("GR-001", attrname, type);
					}
				} else if (type === 'boolean') {
					if (typeof (attrvalue) !== 'boolean') {
						GravityRD.Log.usageError("GR-001", attrname, type);
					}
				} else if (type === 'dom') {
					var element = document.getElementById(attrvalue);
					if (element === null) {
						GravityRD.Log.usageError("GR-002", attrvalue);
					}
				} else if (type === 'object') {
					if (!GravityRD.isObject(attrvalue)) {
						GravityRD.Log.usageError("GR-001", attrname, type);
					}
					this.validateAttributes(attrvalue, descriptor.descriptor);
				} else if (type === 'function') {
					if (!GravityRD.isFunction(attrvalue)) {
						GravityRD.Log.usageError("GR-001", attrname, type);
					}
				} else {
					GravityRD.Log.error("Invalid type specified in the field descriptor: " + descriptor['type']);
				}
			}
		}
	});

	/**
	 * GravityRD.Typecheck API class. Its original purpose was to do some extra typechecking, because the .push({...}) style calls are too stringly typed.
	 * Its not enough powerfull, should be replaced with some more generic implementation.
	 */
	var GravityRD_Typecheck = GravityRD.Base.extend({
		__public__: ['argumentChecker', 'pushObjectChecker', 'nullStringChecker'],

		argumentChecker: function (name, args) {
			return new ArgumentTypechecker(name, args);
		},

		pushObjectChecker: function (o) {
			return new PushObjectTypeChecker(o);
		},

		nullStringChecker: function (name) {
			return new NullStringChecker(name);
		}

	});
	GravityRD.register("GravityRD", "Typecheck", new GravityRD_Typecheck());
})();
(function() {
	/**
	 * This class is responsible for group and debounce the calls which it receives through it's push interface.
	 * The defering happens when the interpreters callstack becomes empty (@see setTimeout(0)).
	 */
	var GravityRD_Worker_PushProcessor = GravityRD.extend(Object, {
		handle : null,
		equeue : [],
		queue : [],
		lastHash: -1,

		push : function (parm, sync) {
			GravityRD.Log.debug('Push request:', parm);
			
			if (parm.type === 'event') {
				var hash = GravityRD.hash(parm);
				if (hash === this.lastHash) {
					GravityRD.Log.debug("Suppressed event because it has the same hash as the previous event", parm);
					return;
				}
				this.lastHash = hash;
			}
			
			if (GravityRD.Core.debug("eventbirth") && parm.type === 'event') {
				parm._I = (+new Date())+"_"+parseInt(Math.random()*9999,10);
				parm._S = (sync !== false) ? '1':'0'; 
			}
			

			if (parm.type === 'event') {
				this.equeue.push(parm);
				GravityRD.Core.Cookie.queueEvents(this.equeue, 'push', true);
			} else {
				this.queue.push(parm);
			}
			
			this.schedule();			
		},

		process : function() {
			this.handle = null;
			GravityRD.Log.group("Process queue");
			for (var i=0; i < this.queue.length; i++) {
				GravityRD.Log.debug('' + i, this.queue[i]);
			}
			GravityRD.Log.groupEnd();
			var fromCookie = GravityRD.Core.Cookie.dequeueEvents('push') || [];
			GravityRD.Core.process((this.queue||[]).concat(fromCookie));
			this.equeue = [];
			this.queue = [];
		},
		
		schedule: function() {
			this.clearPending();
			this.handle = window.setTimeout(GravityRD.bind(this.process, this), 0);
		},
		
		clearPending: function() {
			if(this.handle !== null) {
				window.clearTimeout(this.handle);		
			}			
		}
	});
	
	var pushProcessor = new GravityRD_Worker_PushProcessor();
	
	/**
	 * GravityRD.Worker API class. It's main responsibility to initialize its framework:
	 *   - process calls which were queued before the framework was initialized
	 *   - install the push processor
	 *   - initialize  custom configuration
	 *   - initialize HUD (if present)
	 *   - initialize debug hashmarks options
	 *   - handle special parameters by retargeting __rid, __iid, __cid
	 *   - initalize page and session cookies
	 */
	var GravityRD_Worker = GravityRD.Base.extend({
		__public__: ['start'],
		keyHooked: false,
		
		installPushProcessor : function() {
			_gravity = pushProcessor;
			window.onunload = function() {

				if(GravityRD.Core.getConfig()["trackPageLeaveEvent"] === true) {
					var last = GravityRD.Core.Cookie.getLastViewedItem();
					if(last !== null) {
						_gravity.push({type:'event', eventType: 'LEAVE_PAGE', itemId: last});
					}
				}
			}
			pushProcessor.schedule();
			GravityRD.Log.debug("Push processor installed");
		}, 

		processInitialArray : function() {
			for(var i=0; i < _gravity.length; i++) {
				var item = _gravity[i]
				pushProcessor.push(item, false);
			}
			GravityRD.Log.debug("Initial array processed");
		},
		
		handleGravityHashCustom: function() {
			var hash = document.location.href.match("#gravity-?([^-]*)");
			if (hash !== null) {
				if(""==hash[1] || "on"==hash[1]) {
					GravityRD.Log.debug("Turning on customconfig in demo mode");
					GravityRD.Core.Cookie.setCookie("gr_custom", "true");
				} else if ("session" === hash[1]) {
					GravityRD.Core.Cookie.setCookie("gr_custom", "true", false);
				} else {
					GravityRD.Core.Cookie.setCookie("gr_custom", "false", 0);
				}
			}
		},

		hookCustomConfigInitKey: function() {
			if (!this.keyHooked) {
				GravityRD.Log.debug("Hooking key");
				var demoKey = GravityRD.Core.getConfig()["customConfigDemoKey"];
				if (demoKey !== null) {
					var keyCode = 119;
					var match = demoKey.match("F([1-9][0-2]?)");
					if (match !== null) {
						keyCode = 111+1*match[1];
					}
					that = this;
					var callback = function(e) {
						if (e.keyCode === keyCode) {
								GravityRD.CustomConfig.init(true);
						}
					};

					if (document.addEventListener) {
						document.addEventListener("keydown", callback, false);
					} else {
						document.attachEvent("on" + "keydown", callback);
					}
				}
			}
			this.keyHooked = true;
		},
		
		handleCustomConfigMode: function() {
			if (GravityRD.CustomConfig !== undefined) {
				var customConfig = GravityRD.Core.getConfig()["customConfig"];
				if (customConfig === 'enabled') {
					GravityRD.CustomConfig.enable();
				} else if (customConfig === 'demo') {
					this.handleGravityHashCustom();
					var val = GravityRD.Core.Cookie.getCookie("gr_custom");
					if (val !== null) {
						GravityRD.CustomConfig.enable();
					}
					this.hookCustomConfigInitKey();
				}
			}
		},
		
		handleGravityHashDebug: function() {
			var hash = document.location.href.match("#gravity-debug-([^-]*)-([^-]*)");
			var debugJson = GravityRD.Core.Cookie.getDebugCookie() || {};
			
			if (hash !== null) {
				var facet = hash[1].split(",");
				GravityRD.each(facet, function(f) {
					if ("on"==hash[2]) {
						debugJson[f] = true;
					} else if("off"==hash[2])  {
						debugJson[f] = false;
					}
					GravityRD.Log.debug("DEBUG_"+f, debugJson[f]);
				});
				GravityRD.Core.Cookie.setDebugCookie(debugJson);
			}
			GravityRD.Core.debugMask(debugJson);
		},
		
		start: function() {
			
			this.handleCustomConfigMode();
			this.handleGravityHashDebug();
			
			GravityRD.Log.debug("Gravity object", window.gravity);
			
			_gravity = window._gravity || [];

			if (GravityRD.Core.debug("log")) {
				GravityRD.Core.set({mode:'DEVELOP'});
			}

			GravityRD.Log.timeStamp("GR:start()");

			if (!GravityRD.isArray(_gravity)) {
				GravityRD.Log.debug("Framework already initialized");
				return false;
			}

			var pnv = GravityRD.Core.Cookie.getPersistentNameValues();
			GravityRD.each(GravityRD.keys(pnv), function(k) {
				GravityRD.Log.debug("Loading persistent namevalue: " + k, pnv[k]);
				GravityRD.Core.addGlobalNameValue(k, pnv[k],true, false);
			});

			var snv = GravityRD.Core.Cookie.getSessionNameValues();
			GravityRD.each(GravityRD.keys(snv), function(k) {
				GravityRD.Log.debug("Loading session namevalue: " + k, snv[k]);
				GravityRD.Core.addGlobalNameValue(k, snv[k],true, false);
			});

			//apply all the client setting before we do anything
			for(var i=0; i < _gravity.length; i++) {
				var item = _gravity[i]
				if (item.type === 'set') {
					GravityRD.Core.set(item);
				}
			}

			var config = GravityRD.Core.getConfig();
			
			if (config["disabled"] === true) {
				GravityRD.Log.usageError("GR-029");
				return;
			}

			if (GravityRD.CustomConfig !== undefined) {
				GravityRD.CustomConfig.init();
			}

			if (GravityRD.HUD !== undefined && GravityRD.Core.debug("hud")) {
				GravityRD.HUD.start();
			}

			if (!!window._gravity_init_jq) {
				if(!!window.jQuery) {
					window._gravity_init_jq(window.jQuery);
				} else {
					GravityRD.Log.debug("Typeahead support disabled because no jQuery found.");
				}
			}
			GravityRD.Log.debug("Customconfig initialized");

			
			GravityRD.Core.Cookie.setCookie("gr_recmap", "", 0);
			GravityRD.Core.Cookie.setCookie("gr_recmapkeys", "", 0);

			GravityRD.Core.Cookie.initSessionCookie();
			GravityRD.Core.Cookie.initPageCookie();
			
			var qp = {};
			document.location.href.replace(/.*\?/, "").replace(/([^=]+)=([^#&]*)&?/g, function (_, a, b) {
				try {
					qp[decodeURIComponent(a)] = decodeURIComponent(b);
				} catch(e) {
					GravityRD.Log.debug("Invalid parameter: ", [a,b]);
				}
			});
			
			if (!!qp["__iid"] && !!qp["__rid"]) {
				GravityRD.Core.Cookie.registerRecId(qp["__iid"], qp["__rid"]);
				GravityRD.Log.debug("Registering '" + qp["__rid"] + "' for item '" + qp["__iid"]+ "', reason: landing page.");
			}
			
			if (!!qp["__cid"] && config["retargeting"] === true && config["useJsGeneratedCookie"] === true) {
				GravityRD.Core.Cookie.getGeneratedCookie(qp["__cid"]);
			}
			
			
			this.processInitialArray();
			this.installPushProcessor();
			GravityRD.Log.debug("Worker started");

			return true;
		}
	});
	GravityRD.register("GravityRD", "Worker", new GravityRD_Worker());
})();


(function() {
	/** PerfObject is the 'unit' of collecting performance statistics. If we want to measure some timing related info for a given object, we assing a perfobject to it.
	 *  Each PerfObject can have a startTime, and endTime, and several phases of its lifecycle. 
	 */
	var PerfObject  = GravityRD.extend(Object, {
		startTime: null,
		endTime: null,
		lastTime: null,
		phaseNames: [],		
		phases: {},
		name: null,
		
		constructor: function(name) {
			this.name = name;
			this.lastTime = this.startTime = +new Date();	
			this.phaseNames = [];
			this.phases = {};
			this.endTime = null;	
		},
		
		phase: function(name) {
			this.phaseNames.push(name);
			var currTime = +new Date();
			this.phases[name] = (currTime - this.lastTime);
			this.lastTime = currTime;		
		},

		end: function(name) {
			if (name != undefined)  {
				this.phase(name);			
			}
			this.endTime = +new Date();
		},
	
		getTime: function() {
			if (this.endTime != null) {
				return this.endTime - this.startTime;			
			} else {
				return -1;			
			}
		},

		getPhases: function() {
			return this.phases;		
		},

		get: function() {
			return {
				diff: this.getTime(),
				phaseNames: this.phaseNames,
				phases: this.getPhases()
			};		
		}
	});

	/** GravityRD.Perf API class. Can be used to create simple performance statistics in the framework, for internal use. */
	var GravityRD_Perf = GravityRD.Base.extend({
		__public__: ['create', 'stat', 'dump'],
		perfObjectNames: [],		
		perfObjects: {},

		create: function(name) {
			var perfObj = new PerfObject();
			if (name !== undefined)
				this.attach(name, perfObj);
			return perfObj;
		},
		
		attach: function(name, perfobj) {
			perfobj.name = name;
			this.perfObjectNames.push(name);
			this.perfObjects[name] = perfobj;
		},

		get: function(name) {
			return this.perfObjects[name];
		},
		
		stat: function() {
			return this.perfObjects;		
		},
		
		padR: function(s, d) {
			while (s.length < (d||60)) {s += " ";}
			return s;
		},

		padL: function(s, d) {
			while (s.length < (d||60)) {s = " "+s;}
			return s;
		},

		dump: function(phases) {
			var ret = "";
			GravityRD.each(this.perfObjectNames, function(s) {
				ret += this.padR(s) + this.padL(""+this.perfObjects[s].getTime(),8)+ " ms\n";
				if (phases === true) {
					var perf = this.perfObjects[s].get();
					GravityRD.each(perf.phaseNames, function(name) {
						ret += this.padR("      - "+ name) + this.padL(""+perf.phases[name],8) +" ms\n";				
					}, this);
				}				
			}, this);
			return ret;
		}
	});

	GravityRD.register("GravityRD", "Perf", new GravityRD_Perf());	
})();

(function(){
	/*! extracted from https://github.com/sindresorhus/multiline */
	var reCommentContents = /\/\*!?(?:\@preserve)?[ \t]*(?:\r\n|\n)([\s\S]*?)(?:\r\n|\n)\s*\*\//;
	var stripIndent = function (str) {
		var match = str.match(/^[ \t]*(?=[^\s])/gm);
		if (!match) {
			return str;
		}
		var indent = Math.min.apply(Math, match.map(function (el) { return el.length }));
		var re = new RegExp('^[ \\t]{' + indent + '}', 'gm');

		return indent > 0 ? str.replace(re, '') : str;
	};
	multiline = function (fn) {
		if (typeof fn !== 'function') {
			throw new TypeError('Expected a function.');
		}
		var match = reCommentContents.exec(fn.toString());
		if (!match) {
			throw new TypeError('Multiline comment missing.');
		}
		return match[1];
	};
	multiline.stripIndent = function (fn) {
		return stripIndent(multiline(fn));
	};
})();
/*!
  * domready (c) Dustin Diaz 2012 - License MIT
  */
!function (name, definition) {
  if (typeof module != 'undefined') module.exports = definition()
  else if (typeof define == 'function' && typeof define.amd == 'object') define(definition)
  else this[name] = definition()
}('domready', function (ready) {

  var fns = [], fn, f = false
    , doc = document
    , testEl = doc.documentElement
    , hack = testEl.doScroll
    , domContentLoaded = 'DOMContentLoaded'
    , addEventListener = 'addEventListener'
    , onreadystatechange = 'onreadystatechange'
    , readyState = 'readyState'
    , loadedRgx = hack ? /^loaded|^c/ : /^loaded|c/
    , loaded = loadedRgx.test(doc[readyState])

  function flush(f) {
    loaded = 1
    while (f = fns.shift()) f()
  }

  doc[addEventListener] && doc[addEventListener](domContentLoaded, fn = function () {
    doc.removeEventListener(domContentLoaded, fn, f)
    flush()
  }, f)


  hack && doc.attachEvent(onreadystatechange, fn = function () {
    if (/^c/.test(doc[readyState])) {
      doc.detachEvent(onreadystatechange, fn)
      flush()
    }
  })

  return (ready = hack ?
    function (fn) {
      self != top ?
        loaded ? fn() : fns.push(fn) :
        function () {
          try {
            testEl.doScroll('left')
          } catch (e) {
            return setTimeout(function() { ready(fn) }, 50)
          }
          fn()
        }()
    } :
    function (fn) {
      loaded ? fn() : fns.push(fn)
    })
})
/*! JSON v3.3.2 | http://bestiejs.github.io/json3 | Copyright 2012-2014, Kit Cambridge | http://kit.mit-license.org */
;(function () {
  // Detect the `define` function exposed by asynchronous module loaders. The
  // strict `define` check is necessary for compatibility with `r.js`.
  var isLoader = typeof define === "function" && define.amd;

  // A set of types used to distinguish objects from primitives.
  var objectTypes = {
    "function": true,
    "object": true
  };

  // Detect the `exports` object exposed by CommonJS implementations.
  var freeExports = objectTypes[typeof exports] && exports && !exports.nodeType && exports;

  // Use the `global` object exposed by Node (including Browserify via
  // `insert-module-globals`), Narwhal, and Ringo as the default context,
  // and the `window` object in browsers. Rhino exports a `global` function
  // instead.
  var root = objectTypes[typeof window] && window || this,
      freeGlobal = freeExports && objectTypes[typeof module] && module && !module.nodeType && typeof global == "object" && global;

  if (freeGlobal && (freeGlobal["global"] === freeGlobal || freeGlobal["window"] === freeGlobal || freeGlobal["self"] === freeGlobal)) {
    root = freeGlobal;
  }

  // Public: Initializes JSON 3 using the given `context` object, attaching the
  // `stringify` and `parse` functions to the specified `exports` object.
  function runInContext(context, exports) {
    context || (context = root["Object"]());
    exports || (exports = root["Object"]());

    // Native constructor aliases.
    var Number = context["Number"] || root["Number"],
        String = context["String"] || root["String"],
        Object = context["Object"] || root["Object"],
        Date = context["Date"] || root["Date"],
        SyntaxError = context["SyntaxError"] || root["SyntaxError"],
        TypeError = context["TypeError"] || root["TypeError"],
        Math = context["Math"] || root["Math"],
        nativeJSON = context["JSON"] || root["JSON"];

    // Delegate to the native `stringify` and `parse` implementations.
    if (typeof nativeJSON == "object" && nativeJSON) {
      exports.stringify = nativeJSON.stringify;
      exports.parse = nativeJSON.parse;
    }

    // Convenience aliases.
    var objectProto = Object.prototype,
        getClass = objectProto.toString,
        isProperty, forEach, undef;

    // Test the `Date#getUTC*` methods. Based on work by @Yaffle.
    var isExtended = new Date(-3509827334573292);
    try {
      // The `getUTCFullYear`, `Month`, and `Date` methods return nonsensical
      // results for certain dates in Opera >= 10.53.
      isExtended = isExtended.getUTCFullYear() == -109252 && isExtended.getUTCMonth() === 0 && isExtended.getUTCDate() === 1 &&
        // Safari < 2.0.2 stores the internal millisecond time value correctly,
        // but clips the values returned by the date methods to the range of
        // signed 32-bit integers ([-2 ** 31, 2 ** 31 - 1]).
        isExtended.getUTCHours() == 10 && isExtended.getUTCMinutes() == 37 && isExtended.getUTCSeconds() == 6 && isExtended.getUTCMilliseconds() == 708;
    } catch (exception) {}

    // Internal: Determines whether the native `JSON.stringify` and `parse`
    // implementations are spec-compliant. Based on work by Ken Snyder.
    function has(name) {
      if (has[name] !== undef) {
        // Return cached feature test result.
        return has[name];
      }
      var isSupported;
      if (name == "bug-string-char-index") {
        // IE <= 7 doesn't support accessing string characters using square
        // bracket notation. IE 8 only supports this for primitives.
        isSupported = "a"[0] != "a";
      } else if (name == "json") {
        // Indicates whether both `JSON.stringify` and `JSON.parse` are
        // supported.
        isSupported = has("json-stringify") && has("json-parse");
      } else {
        var value, serialized = '{"a":[1,true,false,null,"\\u0000\\b\\n\\f\\r\\t"]}';
        // Test `JSON.stringify`.
        if (name == "json-stringify") {
          var stringify = exports.stringify, stringifySupported = typeof stringify == "function" && isExtended;
          if (stringifySupported) {
            // A test function object with a custom `toJSON` method.
            (value = function () {
              return 1;
            }).toJSON = value;
            try {
              stringifySupported =
                // Firefox 3.1b1 and b2 serialize string, number, and boolean
                // primitives as object literals.
                stringify(0) === "0" &&
                // FF 3.1b1, b2, and JSON 2 serialize wrapped primitives as object
                // literals.
                stringify(new Number()) === "0" &&
                stringify(new String()) == '""' &&
                // FF 3.1b1, 2 throw an error if the value is `null`, `undefined`, or
                // does not define a canonical JSON representation (this applies to
                // objects with `toJSON` properties as well, *unless* they are nested
                // within an object or array).
                stringify(getClass) === undef &&
                // IE 8 serializes `undefined` as `"undefined"`. Safari <= 5.1.7 and
                // FF 3.1b3 pass this test.
                stringify(undef) === undef &&
                // Safari <= 5.1.7 and FF 3.1b3 throw `Error`s and `TypeError`s,
                // respectively, if the value is omitted entirely.
                stringify() === undef &&
                // FF 3.1b1, 2 throw an error if the given value is not a number,
                // string, array, object, Boolean, or `null` literal. This applies to
                // objects with custom `toJSON` methods as well, unless they are nested
                // inside object or array literals. YUI 3.0.0b1 ignores custom `toJSON`
                // methods entirely.
                stringify(value) === "1" &&
                stringify([value]) == "[1]" &&
                // Prototype <= 1.6.1 serializes `[undefined]` as `"[]"` instead of
                // `"[null]"`.
                stringify([undef]) == "[null]" &&
                // YUI 3.0.0b1 fails to serialize `null` literals.
                stringify(null) == "null" &&
                // FF 3.1b1, 2 halts serialization if an array contains a function:
                // `[1, true, getClass, 1]` serializes as "[1,true,],". FF 3.1b3
                // elides non-JSON values from objects and arrays, unless they
                // define custom `toJSON` methods.
                stringify([undef, getClass, null]) == "[null,null,null]" &&
                // Simple serialization test. FF 3.1b1 uses Unicode escape sequences
                // where character escape codes are expected (e.g., `\b` => `\u0008`).
                stringify({ "a": [value, true, false, null, "\x00\b\n\f\r\t"] }) == serialized &&
                // FF 3.1b1 and b2 ignore the `filter` and `width` arguments.
                stringify(null, value) === "1" &&
                stringify([1, 2], null, 1) == "[\n 1,\n 2\n]" &&
                // JSON 2, Prototype <= 1.7, and older WebKit builds incorrectly
                // serialize extended years.
                stringify(new Date(-8.64e15)) == '"-271821-04-20T00:00:00.000Z"' &&
                // The milliseconds are optional in ES 5, but required in 5.1.
                stringify(new Date(8.64e15)) == '"+275760-09-13T00:00:00.000Z"' &&
                // Firefox <= 11.0 incorrectly serializes years prior to 0 as negative
                // four-digit years instead of six-digit years. Credits: @Yaffle.
                stringify(new Date(-621987552e5)) == '"-000001-01-01T00:00:00.000Z"' &&
                // Safari <= 5.1.5 and Opera >= 10.53 incorrectly serialize millisecond
                // values less than 1000. Credits: @Yaffle.
                stringify(new Date(-1)) == '"1969-12-31T23:59:59.999Z"';
            } catch (exception) {
              stringifySupported = false;
            }
          }
          isSupported = stringifySupported;
        }
        // Test `JSON.parse`.
        if (name == "json-parse") {
          var parse = exports.parse;
          if (typeof parse == "function") {
            try {
              // FF 3.1b1, b2 will throw an exception if a bare literal is provided.
              // Conforming implementations should also coerce the initial argument to
              // a string prior to parsing.
              if (parse("0") === 0 && !parse(false)) {
                // Simple parsing test.
                value = parse(serialized);
                var parseSupported = value["a"].length == 5 && value["a"][0] === 1;
                if (parseSupported) {
                  try {
                    // Safari <= 5.1.2 and FF 3.1b1 allow unescaped tabs in strings.
                    parseSupported = !parse('"\t"');
                  } catch (exception) {}
                  if (parseSupported) {
                    try {
                      // FF 4.0 and 4.0.1 allow leading `+` signs and leading
                      // decimal points. FF 4.0, 4.0.1, and IE 9-10 also allow
                      // certain octal literals.
                      parseSupported = parse("01") !== 1;
                    } catch (exception) {}
                  }
                  if (parseSupported) {
                    try {
                      // FF 4.0, 4.0.1, and Rhino 1.7R3-R4 allow trailing decimal
                      // points. These environments, along with FF 3.1b1 and 2,
                      // also allow trailing commas in JSON objects and arrays.
                      parseSupported = parse("1.") !== 1;
                    } catch (exception) {}
                  }
                }
              }
            } catch (exception) {
              parseSupported = false;
            }
          }
          isSupported = parseSupported;
        }
      }
      return has[name] = !!isSupported;
    }

    if (!has("json")) {
      // Common `[[Class]]` name aliases.
      var functionClass = "[object Function]",
          dateClass = "[object Date]",
          numberClass = "[object Number]",
          stringClass = "[object String]",
          arrayClass = "[object Array]",
          booleanClass = "[object Boolean]";

      // Detect incomplete support for accessing string characters by index.
      var charIndexBuggy = has("bug-string-char-index");

      // Define additional utility methods if the `Date` methods are buggy.
      if (!isExtended) {
        var floor = Math.floor;
        // A mapping between the months of the year and the number of days between
        // January 1st and the first of the respective month.
        var Months = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        // Internal: Calculates the number of days between the Unix epoch and the
        // first day of the given month.
        var getDay = function (year, month) {
          return Months[month] + 365 * (year - 1970) + floor((year - 1969 + (month = +(month > 1))) / 4) - floor((year - 1901 + month) / 100) + floor((year - 1601 + month) / 400);
        };
      }

      // Internal: Determines if a property is a direct property of the given
      // object. Delegates to the native `Object#hasOwnProperty` method.
      if (!(isProperty = objectProto.hasOwnProperty)) {
        isProperty = function (property) {
          var members = {}, constructor;
          if ((members.__proto__ = null, members.__proto__ = {
            // The *proto* property cannot be set multiple times in recent
            // versions of Firefox and SeaMonkey.
            "toString": 1
          }, members).toString != getClass) {
            // Safari <= 2.0.3 doesn't implement `Object#hasOwnProperty`, but
            // supports the mutable *proto* property.
            isProperty = function (property) {
              // Capture and break the object's prototype chain (see section 8.6.2
              // of the ES 5.1 spec). The parenthesized expression prevents an
              // unsafe transformation by the Closure Compiler.
              var original = this.__proto__, result = property in (this.__proto__ = null, this);
              // Restore the original prototype chain.
              this.__proto__ = original;
              return result;
            };
          } else {
            // Capture a reference to the top-level `Object` constructor.
            constructor = members.constructor;
            // Use the `constructor` property to simulate `Object#hasOwnProperty` in
            // other environments.
            isProperty = function (property) {
              var parent = (this.constructor || constructor).prototype;
              return property in this && !(property in parent && this[property] === parent[property]);
            };
          }
          members = null;
          return isProperty.call(this, property);
        };
      }

      // Internal: Normalizes the `for...in` iteration algorithm across
      // environments. Each enumerated key is yielded to a `callback` function.
      forEach = function (object, callback) {
        var size = 0, Properties, members, property;

        // Tests for bugs in the current environment's `for...in` algorithm. The
        // `valueOf` property inherits the non-enumerable flag from
        // `Object.prototype` in older versions of IE, Netscape, and Mozilla.
        (Properties = function () {
          this.valueOf = 0;
        }).prototype.valueOf = 0;

        // Iterate over a new instance of the `Properties` class.
        members = new Properties();
        for (property in members) {
          // Ignore all properties inherited from `Object.prototype`.
          if (isProperty.call(members, property)) {
            size++;
          }
        }
        Properties = members = null;

        // Normalize the iteration algorithm.
        if (!size) {
          // A list of non-enumerable properties inherited from `Object.prototype`.
          members = ["valueOf", "toString", "toLocaleString", "propertyIsEnumerable", "isPrototypeOf", "hasOwnProperty", "constructor"];
          // IE <= 8, Mozilla 1.0, and Netscape 6.2 ignore shadowed non-enumerable
          // properties.
          forEach = function (object, callback) {
            var isFunction = getClass.call(object) == functionClass, property, length;
            var hasProperty = !isFunction && typeof object.constructor != "function" && objectTypes[typeof object.hasOwnProperty] && object.hasOwnProperty || isProperty;
            for (property in object) {
              // Gecko <= 1.0 enumerates the `prototype` property of functions under
              // certain conditions; IE does not.
              if (!(isFunction && property == "prototype") && hasProperty.call(object, property)) {
                callback(property);
              }
            }
            // Manually invoke the callback for each non-enumerable property.
            for (length = members.length; property = members[--length]; hasProperty.call(object, property) && callback(property));
          };
        } else if (size == 2) {
          // Safari <= 2.0.4 enumerates shadowed properties twice.
          forEach = function (object, callback) {
            // Create a set of iterated properties.
            var members = {}, isFunction = getClass.call(object) == functionClass, property;
            for (property in object) {
              // Store each property name to prevent double enumeration. The
              // `prototype` property of functions is not enumerated due to cross-
              // environment inconsistencies.
              if (!(isFunction && property == "prototype") && !isProperty.call(members, property) && (members[property] = 1) && isProperty.call(object, property)) {
                callback(property);
              }
            }
          };
        } else {
          // No bugs detected; use the standard `for...in` algorithm.
          forEach = function (object, callback) {
            var isFunction = getClass.call(object) == functionClass, property, isConstructor;
            for (property in object) {
              if (!(isFunction && property == "prototype") && isProperty.call(object, property) && !(isConstructor = property === "constructor")) {
                callback(property);
              }
            }
            // Manually invoke the callback for the `constructor` property due to
            // cross-environment inconsistencies.
            if (isConstructor || isProperty.call(object, (property = "constructor"))) {
              callback(property);
            }
          };
        }
        return forEach(object, callback);
      };

      // Public: Serializes a JavaScript `value` as a JSON string. The optional
      // `filter` argument may specify either a function that alters how object and
      // array members are serialized, or an array of strings and numbers that
      // indicates which properties should be serialized. The optional `width`
      // argument may be either a string or number that specifies the indentation
      // level of the output.
      if (!has("json-stringify")) {
        // Internal: A map of control characters and their escaped equivalents.
        var Escapes = {
          92: "\\\\",
          34: '\\"',
          8: "\\b",
          12: "\\f",
          10: "\\n",
          13: "\\r",
          9: "\\t"
        };

        // Internal: Converts `value` into a zero-padded string such that its
        // length is at least equal to `width`. The `width` must be <= 6.
        var leadingZeroes = "000000";
        var toPaddedString = function (width, value) {
          // The `|| 0` expression is necessary to work around a bug in
          // Opera <= 7.54u2 where `0 == -0`, but `String(-0) !== "0"`.
          return (leadingZeroes + (value || 0)).slice(-width);
        };

        // Internal: Double-quotes a string `value`, replacing all ASCII control
        // characters (characters with code unit values between 0 and 31) with
        // their escaped equivalents. This is an implementation of the
        // `Quote(value)` operation defined in ES 5.1 section 15.12.3.
        var unicodePrefix = "\\u00";
        var quote = function (value) {
          var result = '"', index = 0, length = value.length, useCharIndex = !charIndexBuggy || length > 10;
          var symbols = useCharIndex && (charIndexBuggy ? value.split("") : value);
          for (; index < length; index++) {
            var charCode = value.charCodeAt(index);
            // If the character is a control character, append its Unicode or
            // shorthand escape sequence; otherwise, append the character as-is.
            switch (charCode) {
              case 8: case 9: case 10: case 12: case 13: case 34: case 92:
                result += Escapes[charCode];
                break;
              default:
                if (charCode < 32) {
                  result += unicodePrefix + toPaddedString(2, charCode.toString(16));
                  break;
                }
                result += useCharIndex ? symbols[index] : value.charAt(index);
            }
          }
          return result + '"';
        };

        // Internal: Recursively serializes an object. Implements the
        // `Str(key, holder)`, `JO(value)`, and `JA(value)` operations.
        var serialize = function (property, object, callback, properties, whitespace, indentation, stack) {
          var value, className, year, month, date, time, hours, minutes, seconds, milliseconds, results, element, index, length, prefix, result;
          try {
            // Necessary for host object support.
            value = object[property];
          } catch (exception) {}
          if (typeof value == "object" && value) {
            className = getClass.call(value);
            if (className == dateClass && !isProperty.call(value, "toJSON")) {
              if (value > -1 / 0 && value < 1 / 0) {
                // Dates are serialized according to the `Date#toJSON` method
                // specified in ES 5.1 section 15.9.5.44. See section 15.9.1.15
                // for the ISO 8601 date time string format.
                if (getDay) {
                  // Manually compute the year, month, date, hours, minutes,
                  // seconds, and milliseconds if the `getUTC*` methods are
                  // buggy. Adapted from @Yaffle's `date-shim` project.
                  date = floor(value / 864e5);
                  for (year = floor(date / 365.2425) + 1970 - 1; getDay(year + 1, 0) <= date; year++);
                  for (month = floor((date - getDay(year, 0)) / 30.42); getDay(year, month + 1) <= date; month++);
                  date = 1 + date - getDay(year, month);
                  // The `time` value specifies the time within the day (see ES
                  // 5.1 section 15.9.1.2). The formula `(A % B + B) % B` is used
                  // to compute `A modulo B`, as the `%` operator does not
                  // correspond to the `modulo` operation for negative numbers.
                  time = (value % 864e5 + 864e5) % 864e5;
                  // The hours, minutes, seconds, and milliseconds are obtained by
                  // decomposing the time within the day. See section 15.9.1.10.
                  hours = floor(time / 36e5) % 24;
                  minutes = floor(time / 6e4) % 60;
                  seconds = floor(time / 1e3) % 60;
                  milliseconds = time % 1e3;
                } else {
                  year = value.getUTCFullYear();
                  month = value.getUTCMonth();
                  date = value.getUTCDate();
                  hours = value.getUTCHours();
                  minutes = value.getUTCMinutes();
                  seconds = value.getUTCSeconds();
                  milliseconds = value.getUTCMilliseconds();
                }
                // Serialize extended years correctly.
                value = (year <= 0 || year >= 1e4 ? (year < 0 ? "-" : "+") + toPaddedString(6, year < 0 ? -year : year) : toPaddedString(4, year)) +
                  "-" + toPaddedString(2, month + 1) + "-" + toPaddedString(2, date) +
                  // Months, dates, hours, minutes, and seconds should have two
                  // digits; milliseconds should have three.
                  "T" + toPaddedString(2, hours) + ":" + toPaddedString(2, minutes) + ":" + toPaddedString(2, seconds) +
                  // Milliseconds are optional in ES 5.0, but required in 5.1.
                  "." + toPaddedString(3, milliseconds) + "Z";
              } else {
                value = null;
              }
            } else if (typeof value.toJSON == "function" && ((className != numberClass && className != stringClass && className != arrayClass) || isProperty.call(value, "toJSON"))) {
              // Prototype <= 1.6.1 adds non-standard `toJSON` methods to the
              // `Number`, `String`, `Date`, and `Array` prototypes. JSON 3
              // ignores all `toJSON` methods on these objects unless they are
              // defined directly on an instance.
              value = value.toJSON(property);
            }
          }
          if (callback) {
            // If a replacement function was provided, call it to obtain the value
            // for serialization.
            value = callback.call(object, property, value);
          }
          if (value === null) {
            return "null";
          }
          className = getClass.call(value);
          if (className == booleanClass) {
            // Booleans are represented literally.
            return "" + value;
          } else if (className == numberClass) {
            // JSON numbers must be finite. `Infinity` and `NaN` are serialized as
            // `"null"`.
            return value > -1 / 0 && value < 1 / 0 ? "" + value : "null";
          } else if (className == stringClass) {
            // Strings are double-quoted and escaped.
            return quote("" + value);
          }
          // Recursively serialize objects and arrays.
          if (typeof value == "object") {
            // Check for cyclic structures. This is a linear search; performance
            // is inversely proportional to the number of unique nested objects.
            for (length = stack.length; length--;) {
              if (stack[length] === value) {
                // Cyclic structures cannot be serialized by `JSON.stringify`.
                throw TypeError();
              }
            }
            // Add the object to the stack of traversed objects.
            stack.push(value);
            results = [];
            // Save the current indentation level and indent one additional level.
            prefix = indentation;
            indentation += whitespace;
            if (className == arrayClass) {
              // Recursively serialize array elements.
              for (index = 0, length = value.length; index < length; index++) {
                element = serialize(index, value, callback, properties, whitespace, indentation, stack);
                results.push(element === undef ? "null" : element);
              }
              result = results.length ? (whitespace ? "[\n" + indentation + results.join(",\n" + indentation) + "\n" + prefix + "]" : ("[" + results.join(",") + "]")) : "[]";
            } else {
              // Recursively serialize object members. Members are selected from
              // either a user-specified list of property names, or the object
              // itself.
              forEach(properties || value, function (property) {
                var element = serialize(property, value, callback, properties, whitespace, indentation, stack);
                if (element !== undef) {
                  // According to ES 5.1 section 15.12.3: "If `gap` {whitespace}
                  // is not the empty string, let `member` {quote(property) + ":"}
                  // be the concatenation of `member` and the `space` character."
                  // The "`space` character" refers to the literal space
                  // character, not the `space` {width} argument provided to
                  // `JSON.stringify`.
                  results.push(quote(property) + ":" + (whitespace ? " " : "") + element);
                }
              });
              result = results.length ? (whitespace ? "{\n" + indentation + results.join(",\n" + indentation) + "\n" + prefix + "}" : ("{" + results.join(",") + "}")) : "{}";
            }
            // Remove the object from the traversed object stack.
            stack.pop();
            return result;
          }
        };

        // Public: `JSON.stringify`. See ES 5.1 section 15.12.3.
        exports.stringify = function (source, filter, width) {
          var whitespace, callback, properties, className;
          if (objectTypes[typeof filter] && filter) {
            if ((className = getClass.call(filter)) == functionClass) {
              callback = filter;
            } else if (className == arrayClass) {
              // Convert the property names array into a makeshift set.
              properties = {};
              for (var index = 0, length = filter.length, value; index < length; value = filter[index++], ((className = getClass.call(value)), className == stringClass || className == numberClass) && (properties[value] = 1));
            }
          }
          if (width) {
            if ((className = getClass.call(width)) == numberClass) {
              // Convert the `width` to an integer and create a string containing
              // `width` number of space characters.
              if ((width -= width % 1) > 0) {
                for (whitespace = "", width > 10 && (width = 10); whitespace.length < width; whitespace += " ");
              }
            } else if (className == stringClass) {
              whitespace = width.length <= 10 ? width : width.slice(0, 10);
            }
          }
          // Opera <= 7.54u2 discards the values associated with empty string keys
          // (`""`) only if they are used directly within an object member list
          // (e.g., `!("" in { "": 1})`).
          return serialize("", (value = {}, value[""] = source, value), callback, properties, whitespace, "", []);
        };
      }

      // Public: Parses a JSON source string.
      if (!has("json-parse")) {
        var fromCharCode = String.fromCharCode;

        // Internal: A map of escaped control characters and their unescaped
        // equivalents.
        var Unescapes = {
          92: "\\",
          34: '"',
          47: "/",
          98: "\b",
          116: "\t",
          110: "\n",
          102: "\f",
          114: "\r"
        };

        // Internal: Stores the parser state.
        var Index, Source;

        // Internal: Resets the parser state and throws a `SyntaxError`.
        var abort = function () {
          Index = Source = null;
          throw SyntaxError();
        };

        // Internal: Returns the next token, or `"$"` if the parser has reached
        // the end of the source string. A token may be a string, number, `null`
        // literal, or Boolean literal.
        var lex = function () {
          var source = Source, length = source.length, value, begin, position, isSigned, charCode;
          while (Index < length) {
            charCode = source.charCodeAt(Index);
            switch (charCode) {
              case 9: case 10: case 13: case 32:
                // Skip whitespace tokens, including tabs, carriage returns, line
                // feeds, and space characters.
                Index++;
                break;
              case 123: case 125: case 91: case 93: case 58: case 44:
                // Parse a punctuator token (`{`, `}`, `[`, `]`, `:`, or `,`) at
                // the current position.
                value = charIndexBuggy ? source.charAt(Index) : source[Index];
                Index++;
                return value;
              case 34:
                // `"` delimits a JSON string; advance to the next character and
                // begin parsing the string. String tokens are prefixed with the
                // sentinel `@` character to distinguish them from punctuators and
                // end-of-string tokens.
                for (value = "@", Index++; Index < length;) {
                  charCode = source.charCodeAt(Index);
                  if (charCode < 32) {
                    // Unescaped ASCII control characters (those with a code unit
                    // less than the space character) are not permitted.
                    abort();
                  } else if (charCode == 92) {
                    // A reverse solidus (`\`) marks the beginning of an escaped
                    // control character (including `"`, `\`, and `/`) or Unicode
                    // escape sequence.
                    charCode = source.charCodeAt(++Index);
                    switch (charCode) {
                      case 92: case 34: case 47: case 98: case 116: case 110: case 102: case 114:
                        // Revive escaped control characters.
                        value += Unescapes[charCode];
                        Index++;
                        break;
                      case 117:
                        // `\u` marks the beginning of a Unicode escape sequence.
                        // Advance to the first character and validate the
                        // four-digit code point.
                        begin = ++Index;
                        for (position = Index + 4; Index < position; Index++) {
                          charCode = source.charCodeAt(Index);
                          // A valid sequence comprises four hexdigits (case-
                          // insensitive) that form a single hexadecimal value.
                          if (!(charCode >= 48 && charCode <= 57 || charCode >= 97 && charCode <= 102 || charCode >= 65 && charCode <= 70)) {
                            // Invalid Unicode escape sequence.
                            abort();
                          }
                        }
                        // Revive the escaped character.
                        value += fromCharCode("0x" + source.slice(begin, Index));
                        break;
                      default:
                        // Invalid escape sequence.
                        abort();
                    }
                  } else {
                    if (charCode == 34) {
                      // An unescaped double-quote character marks the end of the
                      // string.
                      break;
                    }
                    charCode = source.charCodeAt(Index);
                    begin = Index;
                    // Optimize for the common case where a string is valid.
                    while (charCode >= 32 && charCode != 92 && charCode != 34) {
                      charCode = source.charCodeAt(++Index);
                    }
                    // Append the string as-is.
                    value += source.slice(begin, Index);
                  }
                }
                if (source.charCodeAt(Index) == 34) {
                  // Advance to the next character and return the revived string.
                  Index++;
                  return value;
                }
                // Unterminated string.
                abort();
              default:
                // Parse numbers and literals.
                begin = Index;
                // Advance past the negative sign, if one is specified.
                if (charCode == 45) {
                  isSigned = true;
                  charCode = source.charCodeAt(++Index);
                }
                // Parse an integer or floating-point value.
                if (charCode >= 48 && charCode <= 57) {
                  // Leading zeroes are interpreted as octal literals.
                  if (charCode == 48 && ((charCode = source.charCodeAt(Index + 1)), charCode >= 48 && charCode <= 57)) {
                    // Illegal octal literal.
                    abort();
                  }
                  isSigned = false;
                  // Parse the integer component.
                  for (; Index < length && ((charCode = source.charCodeAt(Index)), charCode >= 48 && charCode <= 57); Index++);
                  // Floats cannot contain a leading decimal point; however, this
                  // case is already accounted for by the parser.
                  if (source.charCodeAt(Index) == 46) {
                    position = ++Index;
                    // Parse the decimal component.
                    for (; position < length && ((charCode = source.charCodeAt(position)), charCode >= 48 && charCode <= 57); position++);
                    if (position == Index) {
                      // Illegal trailing decimal.
                      abort();
                    }
                    Index = position;
                  }
                  // Parse exponents. The `e` denoting the exponent is
                  // case-insensitive.
                  charCode = source.charCodeAt(Index);
                  if (charCode == 101 || charCode == 69) {
                    charCode = source.charCodeAt(++Index);
                    // Skip past the sign following the exponent, if one is
                    // specified.
                    if (charCode == 43 || charCode == 45) {
                      Index++;
                    }
                    // Parse the exponential component.
                    for (position = Index; position < length && ((charCode = source.charCodeAt(position)), charCode >= 48 && charCode <= 57); position++);
                    if (position == Index) {
                      // Illegal empty exponent.
                      abort();
                    }
                    Index = position;
                  }
                  // Coerce the parsed value to a JavaScript number.
                  return +source.slice(begin, Index);
                }
                // A negative sign may only precede numbers.
                if (isSigned) {
                  abort();
                }
                // `true`, `false`, and `null` literals.
                if (source.slice(Index, Index + 4) == "true") {
                  Index += 4;
                  return true;
                } else if (source.slice(Index, Index + 5) == "false") {
                  Index += 5;
                  return false;
                } else if (source.slice(Index, Index + 4) == "null") {
                  Index += 4;
                  return null;
                }
                // Unrecognized token.
                abort();
            }
          }
          // Return the sentinel `$` character if the parser has reached the end
          // of the source string.
          return "$";
        };

        // Internal: Parses a JSON `value` token.
        var get = function (value) {
          var results, hasMembers;
          if (value == "$") {
            // Unexpected end of input.
            abort();
          }
          if (typeof value == "string") {
            if ((charIndexBuggy ? value.charAt(0) : value[0]) == "@") {
              // Remove the sentinel `@` character.
              return value.slice(1);
            }
            // Parse object and array literals.
            if (value == "[") {
              // Parses a JSON array, returning a new JavaScript array.
              results = [];
              for (;; hasMembers || (hasMembers = true)) {
                value = lex();
                // A closing square bracket marks the end of the array literal.
                if (value == "]") {
                  break;
                }
                // If the array literal contains elements, the current token
                // should be a comma separating the previous element from the
                // next.
                if (hasMembers) {
                  if (value == ",") {
                    value = lex();
                    if (value == "]") {
                      // Unexpected trailing `,` in array literal.
                      abort();
                    }
                  } else {
                    // A `,` must separate each array element.
                    abort();
                  }
                }
                // Elisions and leading commas are not permitted.
                if (value == ",") {
                  abort();
                }
                results.push(get(value));
              }
              return results;
            } else if (value == "{") {
              // Parses a JSON object, returning a new JavaScript object.
              results = {};
              for (;; hasMembers || (hasMembers = true)) {
                value = lex();
                // A closing curly brace marks the end of the object literal.
                if (value == "}") {
                  break;
                }
                // If the object literal contains members, the current token
                // should be a comma separator.
                if (hasMembers) {
                  if (value == ",") {
                    value = lex();
                    if (value == "}") {
                      // Unexpected trailing `,` in object literal.
                      abort();
                    }
                  } else {
                    // A `,` must separate each object member.
                    abort();
                  }
                }
                // Leading commas are not permitted, object property names must be
                // double-quoted strings, and a `:` must separate each property
                // name and value.
                if (value == "," || typeof value != "string" || (charIndexBuggy ? value.charAt(0) : value[0]) != "@" || lex() != ":") {
                  abort();
                }
                results[value.slice(1)] = get(lex());
              }
              return results;
            }
            // Unexpected token encountered.
            abort();
          }
          return value;
        };

        // Internal: Updates a traversed object member.
        var update = function (source, property, callback) {
          var element = walk(source, property, callback);
          if (element === undef) {
            delete source[property];
          } else {
            source[property] = element;
          }
        };

        // Internal: Recursively traverses a parsed JSON object, invoking the
        // `callback` function for each value. This is an implementation of the
        // `Walk(holder, name)` operation defined in ES 5.1 section 15.12.2.
        var walk = function (source, property, callback) {
          var value = source[property], length;
          if (typeof value == "object" && value) {
            // `forEach` can't be used to traverse an array in Opera <= 8.54
            // because its `Object#hasOwnProperty` implementation returns `false`
            // for array indices (e.g., `![1, 2, 3].hasOwnProperty("0")`).
            if (getClass.call(value) == arrayClass) {
              for (length = value.length; length--;) {
                update(value, length, callback);
              }
            } else {
              forEach(value, function (property) {
                update(value, property, callback);
              });
            }
          }
          return callback.call(source, property, value);
        };

        // Public: `JSON.parse`. See ES 5.1 section 15.12.2.
        exports.parse = function (source, callback) {
          var result, value;
          Index = 0;
          Source = "" + source;
          result = get(lex());
          // If a JSON string contains multiple tokens, it is invalid.
          if (lex() != "$") {
            abort();
          }
          // Reset the parser state.
          Index = Source = null;
          return callback && getClass.call(callback) == functionClass ? walk((value = {}, value[""] = result, value), "", callback) : result;
        };
      }
    }

    exports["runInContext"] = runInContext;
    return exports;
  }

  if (freeExports && !isLoader) {
    // Export for CommonJS environments.
    runInContext(root, freeExports);
  } else {
    // Export for web browsers and JavaScript engines.
    var nativeJSON = root.JSON,
        previousJSON = root["JSON3"],
        isRestored = false;

    var JSON3 = runInContext(root, (root["JSON3"] = {
      // Public: Restores the original value of the global `JSON` object and
      // returns a reference to the `JSON3` object.
      "noConflict": function () {
        if (!isRestored) {
          isRestored = true;
          root.JSON = nativeJSON;
          root["JSON3"] = previousJSON;
          nativeJSON = previousJSON = null;
        }
        return JSON3;
      }
    }));

    root.JSON = {
      "parse": JSON3.parse,
      "stringify": JSON3.stringify
    };
  }

  // Export for asynchronous module loaders.
  if (isLoader) {
    define(function () {
      return JSON3;
    });
  }
}).call(this);
grJSON3 = JSON3.noConflict();


/**
 * TrimPath Template. Release 1.1.2.
 * Copyright (C) 2004 - 2007 TrimPath.
 * 
 * TrimPath Template is licensed under the GNU General Public License
 * and the Apache License, Version 2.0, as follows:
 *
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed WITHOUT ANY WARRANTY; without even the 
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (typeof(TrimPath) == 'undefined')
    TrimPath = {};

// TODO: Debugging mode vs stop-on-error mode - runtime flag.
// TODO: Handle || (or) characters and backslashes.
// TODO: Add more modifiers.

(function() {               // Using a closure to keep global namespace clean.
    if (TrimPath.evalEx == null)
        TrimPath.evalEx = function(src) { return eval(src); };

    var UNDEFINED;
    if (Array.prototype.pop == null)  // IE 5.x fix from Igor Poteryaev.
        Array.prototype.pop = function() {
            if (this.length === 0) {return UNDEFINED;}
            return this[--this.length];
        };
    if (Array.prototype.push == null) // IE 5.x fix from Igor Poteryaev.
        Array.prototype.push = function() {
            for (var i = 0; i < arguments.length; ++i) {this[this.length] = arguments[i];}
            return this.length;
        };

    TrimPath.parseTemplate = function(tmplContent, optTmplName, optEtc) {
        if (optEtc == null)
            optEtc = TrimPath.parseTemplate_etc;
        var funcSrc = parse(tmplContent, optTmplName, optEtc);
        var func = TrimPath.evalEx(funcSrc, optTmplName, 1);
        if (func != null)
            return new optEtc.Template(optTmplName, tmplContent, funcSrc, func, optEtc);
        return null;
    }
    
    var exceptionDetails = function(e) {
        return (e.toString()) + ";\n " +
               (e.message) + ";\n " + 
               (e.name) + ";\n " + 
               (e.stack       || 'no stack trace') + ";\n " +
               (e.description || 'no further description') + ";\n " +
               (e.fileName    || 'no file name') + ";\n " +
               (e.lineNumber  || 'no line number');
    }

    try {
        String.prototype.process = function(context, optFlags) {
            var template = TrimPath.parseTemplate(this, null);
            if (template != null)
                return template.process(context, optFlags);
            return this;
        }
    } catch (e) { // Swallow exception, such as when String.prototype is sealed.
    }
    
    TrimPath.parseTemplate_etc = {};            // Exposed for extensibility.
    TrimPath.parseTemplate_etc.statementTag = "forelse|for|if|elseif|else|var|macro";
    TrimPath.parseTemplate_etc.statementDef = { // Lookup table for statement tags.
        "if"     : { delta:  1, prefix: "if (", suffix: ") {", paramMin: 1 },
        "else"   : { delta:  0, prefix: "} else {" },
        "elseif" : { delta:  0, prefix: "} else if (", suffix: ") {", paramDefault: "true" },
        "/if"    : { delta: -1, prefix: "}" },
        "for"    : { delta:  1, paramMin: 3, 
                     prefixFunc : function(stmtParts, state, tmplName, etc) {
                        if (stmtParts[2] != "in")
                            throw new etc.ParseError(tmplName, state.line, "bad for loop statement: " + stmtParts.join(' '));
                        var iterVar = stmtParts[1];
                        var listVar = "__LIST__" + iterVar;
                        return [ "var ", listVar, " = ", stmtParts[3], ";",
                             // Fix from Ross Shaull for hash looping, make sure that we have an array of loop lengths to treat like a stack.
                             "var __LENGTH_STACK__;",
                             "if (typeof(__LENGTH_STACK__) == 'undefined' || !__LENGTH_STACK__.length) __LENGTH_STACK__ = new Array();", 
                             "__LENGTH_STACK__[__LENGTH_STACK__.length] = 0;", // Push a new for-loop onto the stack of loop lengths.
                             "if ((", listVar, ") != null) { ",
                             "var ", iterVar, "_ct = 0;",       // iterVar_ct variable, added by B. Bittman     
                             "for (var ", iterVar, "_index in ", listVar, ") { ",
                             iterVar, "_ct++;",
                             "if (typeof(", listVar, "[", iterVar, "_index]) == 'function') {continue;}", // IE 5.x fix from Igor Poteryaev.
                             "__LENGTH_STACK__[__LENGTH_STACK__.length - 1]++;",
                             "var ", iterVar, " = ", listVar, "[", iterVar, "_index];" ].join("");
                     } },
        "forelse" : { delta:  0, prefix: "} } if (__LENGTH_STACK__[__LENGTH_STACK__.length - 1] == 0) { if (", suffix: ") {", paramDefault: "true" },
        "/for"    : { delta: -1, prefix: "} }; delete __LENGTH_STACK__[__LENGTH_STACK__.length - 1];" }, // Remove the just-finished for-loop from the stack of loop lengths.
        "var"     : { delta:  0, prefix: "var ", suffix: ";" },
        "macro"   : { delta:  1, 
                      prefixFunc : function(stmtParts, state, tmplName, etc) {
                          var macroName = stmtParts[1].split('(')[0];
                          return [ "var ", macroName, " = function", 
                                   stmtParts.slice(1).join(' ').substring(macroName.length),
                                   "{ var _OUT_arr = []; var _OUT = { write: function(m) { if (m) _OUT_arr.push(m); } }; " ].join('');
                     } }, 
        "/macro"  : { delta: -1, prefix: " return _OUT_arr.join(''); };" }
    }
    TrimPath.parseTemplate_etc.modifierDef = {
        "eat"        : function(v)    { return ""; },
        "escape"     : function(s)    { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); },
        "capitalize" : function(s)    { return String(s).toUpperCase(); },
        "default"    : function(s, d) { return s != null ? s : d; }
    }
    TrimPath.parseTemplate_etc.modifierDef.h = TrimPath.parseTemplate_etc.modifierDef.escape;

    TrimPath.parseTemplate_etc.Template = function(tmplName, tmplContent, funcSrc, func, etc) {
        this.process = function(context, flags) {
            if (context == null)
                context = {};
            if (context._MODIFIERS == null)
                context._MODIFIERS = {};
            if (context.defined == null)
                context.defined = function(str) { return (context[str] != undefined); };
            for (var k in etc.modifierDef) {
                if (context._MODIFIERS[k] == null)
                    context._MODIFIERS[k] = etc.modifierDef[k];
            }
            if (flags == null)
                flags = {};
            var resultArr = [];
            var resultOut = { write: function(m) { resultArr.push(m); } };
            try {
                func(resultOut, context, flags);
            } catch (e) {
                if (flags.throwExceptions == true)
                    throw e;
                var result = new String(resultArr.join("") + 
                    "[ERROR: template: <pre>" + exceptionDetails(e) + "</pre>]");
                result["exception"] = e;
                return result;
            }
            return resultArr.join("");
        }
        this.name       = tmplName;
        this.source     = tmplContent; 
        this.sourceFunc = funcSrc;
        this.toString   = function() { return "TrimPath.Template [" + tmplName + "]"; }
    }
    TrimPath.parseTemplate_etc.ParseError = function(name, line, message) {
        this.name    = name;
        this.line    = line;
        this.message = message;
    }
    TrimPath.parseTemplate_etc.ParseError.prototype.toString = function() { 
        return ("TrimPath template ParseError in " + this.name + ": line " + this.line + ", " + this.message);
    }
    
    var parse = function(body, tmplName, etc) {
        body = cleanWhiteSpace(body);
        var funcText = [ "var TrimPath_Template_TEMP = function(_OUT, _CONTEXT, _FLAGS) { with (_CONTEXT) {" ];
        var state    = { stack: [], line: 1 };                              // TODO: Fix line number counting.
        var endStmtPrev = -1;
        while (endStmtPrev + 1 < body.length) {
            var begStmt = endStmtPrev;
            // Scan until we find some statement markup.
            begStmt = body.indexOf("{", begStmt + 1);
            while (begStmt >= 0) {
                var endStmt = body.indexOf('}', begStmt + 1);
                var stmt = body.substring(begStmt, endStmt);
                var blockrx = stmt.match(/^\{(cdata|minify|eval)/); // From B. Bittman, minify/eval/cdata implementation.
                if (blockrx) {
                    var blockType = blockrx[1]; 
                    var blockMarkerBeg = begStmt + blockType.length + 1;
                    var blockMarkerEnd = body.indexOf('}', blockMarkerBeg);
                    if (blockMarkerEnd >= 0) {
                        var blockMarker;
                        if( blockMarkerEnd - blockMarkerBeg <= 0 ) {
                            blockMarker = "{/" + blockType + "}";
                        } else {
                            blockMarker = body.substring(blockMarkerBeg + 1, blockMarkerEnd);
                        }                        
                        
                        var blockEnd = body.indexOf(blockMarker, blockMarkerEnd + 1);
                        if (blockEnd >= 0) {                            
                            emitSectionText(body.substring(endStmtPrev + 1, begStmt), funcText);
                            
                            var blockText = body.substring(blockMarkerEnd + 1, blockEnd);
                            if (blockType == 'cdata') {
                                emitText(blockText, funcText);
                            } else if (blockType == 'minify') {
                                emitText(scrubWhiteSpace(blockText), funcText);
                            } else if (blockType == 'eval') {
                                if (blockText != null && blockText.length > 0) // From B. Bittman, eval should not execute until process().
                                    funcText.push('_OUT.write( (function() { ' + blockText + ' })() );');
                            }
                            begStmt = endStmtPrev = blockEnd + blockMarker.length - 1;
                        }
                    }                        
                } else if (body.charAt(begStmt - 1) != '$' &&               // Not an expression or backslashed,
                           body.charAt(begStmt - 1) != '\\') {              // so check if it is a statement tag.
                    var offset = (body.charAt(begStmt + 1) == '/' ? 2 : 1); // Close tags offset of 2 skips '/'.
                                                                            // 10 is larger than maximum statement tag length.
                    if (body.substring(begStmt + offset, begStmt + 10 + offset).search(TrimPath.parseTemplate_etc.statementTag) == 0) 
                        break;                                              // Found a match.
                }
                begStmt = body.indexOf("{", begStmt + 1);
            }
            if (begStmt < 0)                              // In "a{for}c", begStmt will be 1.
                break;
            var endStmt = body.indexOf("}", begStmt + 1); // In "a{for}c", endStmt will be 5.
            if (endStmt < 0)
                break;
            emitSectionText(body.substring(endStmtPrev + 1, begStmt), funcText);
            emitStatement(body.substring(begStmt, endStmt + 1), state, funcText, tmplName, etc);
            endStmtPrev = endStmt;
        }
        emitSectionText(body.substring(endStmtPrev + 1), funcText);
        if (state.stack.length != 0)
            throw new etc.ParseError(tmplName, state.line, "unclosed, unmatched statement(s): " + state.stack.join(","));
        funcText.push("}}; TrimPath_Template_TEMP");
        return funcText.join("");
    }
    
    var emitStatement = function(stmtStr, state, funcText, tmplName, etc) {
        var parts = stmtStr.slice(1, -1).split(' ');
        var stmt = etc.statementDef[parts[0]]; // Here, parts[0] == for/if/else/...
        if (stmt == null) {                    // Not a real statement.
            emitSectionText(stmtStr, funcText);
            return;
        }
        if (stmt.delta < 0) {
            if (state.stack.length <= 0)
                throw new etc.ParseError(tmplName, state.line, "close tag does not match any previous statement: " + stmtStr);
            state.stack.pop();
        } 
        if (stmt.delta > 0)
            state.stack.push(stmtStr);

        if (stmt.paramMin != null &&
            stmt.paramMin >= parts.length)
            throw new etc.ParseError(tmplName, state.line, "statement needs more parameters: " + stmtStr);
        if (stmt.prefixFunc != null)
            funcText.push(stmt.prefixFunc(parts, state, tmplName, etc));
        else 
            funcText.push(stmt.prefix);
        if (stmt.suffix != null) {
            if (parts.length <= 1) {
                if (stmt.paramDefault != null)
                    funcText.push(stmt.paramDefault);
            } else {
                for (var i = 1; i < parts.length; i++) {
                    if (i > 1)
                        funcText.push(' ');
                    funcText.push(parts[i]);
                }
            }
            funcText.push(stmt.suffix);
        }
    }

    var emitSectionText = function(text, funcText) {
        if (text.length <= 0)
            return;
        var nlPrefix = 0;               // Index to first non-newline in prefix.
        var nlSuffix = text.length - 1; // Index to first non-space/tab in suffix.
        while (nlPrefix < text.length && (text.charAt(nlPrefix) == '\n'))
            nlPrefix++;
        while (nlSuffix >= 0 && (text.charAt(nlSuffix) == ' ' || text.charAt(nlSuffix) == '\t'))
            nlSuffix--;
        if (nlSuffix < nlPrefix)
            nlSuffix = nlPrefix;
        if (nlPrefix > 0) {
            funcText.push('if (_FLAGS.keepWhitespace == true) _OUT.write("');
            var s = text.substring(0, nlPrefix).replace('\n', '\\n'); // A macro IE fix from BJessen.
            if (s.charAt(s.length - 1) == '\n')
            	s = s.substring(0, s.length - 1);
            funcText.push(s);
            funcText.push('");');
        }
        var lines = text.substring(nlPrefix, nlSuffix + 1).split('\n');
        for (var i = 0; i < lines.length; i++) {
            emitSectionTextLine(lines[i], funcText);
            if (i < lines.length - 1)
                funcText.push('_OUT.write("\\n");\n');
        }
        if (nlSuffix + 1 < text.length) {
            funcText.push('if (_FLAGS.keepWhitespace == true) _OUT.write("');
            var s = text.substring(nlSuffix + 1).replace('\n', '\\n');
            if (s.charAt(s.length - 1) == '\n')
            	s = s.substring(0, s.length - 1);
            funcText.push(s);
            funcText.push('");');
        }
    }
    
    var emitSectionTextLine = function(line, funcText) {
        var endMarkPrev = '}';
        var endExprPrev = -1;
        while (endExprPrev + endMarkPrev.length < line.length) {
            var begMark = "${", endMark = "}";
            var begExpr = line.indexOf(begMark, endExprPrev + endMarkPrev.length); // In "a${b}c", begExpr == 1
            if (begExpr < 0)
                break;
            if (line.charAt(begExpr + 2) == '%') {
                begMark = "${%";
                endMark = "%}";
            }
            var endExpr = line.indexOf(endMark, begExpr + begMark.length);         // In "a${b}c", endExpr == 4;
            if (endExpr < 0)
                break;
            emitText(line.substring(endExprPrev + endMarkPrev.length, begExpr), funcText);                
            // Example: exprs == 'firstName|default:"John Doe"|capitalize'.split('|')
            var exprArr = line.substring(begExpr + begMark.length, endExpr).replace(/\|\|/g, "#@@#").split('|');
            for (var k in exprArr) {
                if (exprArr[k].replace) // IE 5.x fix from Igor Poteryaev.
                    exprArr[k] = exprArr[k].replace(/#@@#/g, '||');
            }
            funcText.push('_OUT.write(');
            emitExpression(exprArr, exprArr.length - 1, funcText); 
            funcText.push(');');
            endExprPrev = endExpr;
            endMarkPrev = endMark;
        }
        emitText(line.substring(endExprPrev + endMarkPrev.length), funcText); 
    }
    
    var emitText = function(text, funcText) {
        if (text == null ||
            text.length <= 0)
            return;
        text = text.replace(/\\/g, '\\\\');
        text = text.replace(/\n/g, '\\n');
        text = text.replace(/"/g,  '\\"');
        funcText.push('_OUT.write("');
        funcText.push(text);
        funcText.push('");');
    }
    
    var emitExpression = function(exprArr, index, funcText) {
        // Ex: foo|a:x|b:y1,y2|c:z1,z2 is emitted as c(b(a(foo,x),y1,y2),z1,z2)
        var expr = exprArr[index]; // Ex: exprArr == [firstName,capitalize,default:"John Doe"]
        if (index <= 0) {          // Ex: expr    == 'default:"John Doe"'
            funcText.push(expr);
            return;
        }
        var parts = expr.split(':');
        funcText.push('_MODIFIERS["');
        funcText.push(parts[0]); // The parts[0] is a modifier function name, like capitalize.
        funcText.push('"](');
        emitExpression(exprArr, index - 1, funcText);
        if (parts.length > 1) {
            funcText.push(',');
            funcText.push(parts[1]);
        }
        funcText.push(')');
    }

    var cleanWhiteSpace = function(result) {
        result = result.replace(/\t/g,   "    ");
        result = result.replace(/\r\n/g, "\n");
        result = result.replace(/\r/g,   "\n");
        result = result.replace(/^(\s*\S*(\s+\S+)*)\s*$/, '$1'); // Right trim by Igor Poteryaev.
        return result;
    }

    var scrubWhiteSpace = function(result) {
        result = result.replace(/^\s+/g,   "");
        result = result.replace(/\s+$/g,   "");
        result = result.replace(/\s+/g,   " ");
        result = result.replace(/^(\s*\S*(\s+\S+)*)\s*$/, '$1'); // Right trim by Igor Poteryaev.
        return result;
    }

    // The DOM helper functions depend on DOM/DHTML, so they only work in a browser.
    // However, these are not considered core to the engine.
    //
    TrimPath.parseDOMTemplate = function(elementId, optDocument, optEtc) {
        if (optDocument == null)
            optDocument = document;
        var element = optDocument.getElementById(elementId);
        var content = element.value;     // Like textarea.value.
        if (content == null)
            content = element.innerHTML; // Like textarea.innerHTML.
        content = content.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
        return TrimPath.parseTemplate(content, elementId, optEtc);
    }

    TrimPath.processDOMTemplate = function(elementId, context, optFlags, optDocument, optEtc) {
        return TrimPath.parseDOMTemplate(elementId, optDocument, optEtc).process(context, optFlags);
    }
}) ();
GravityRD.init({partnerId: 'rosszlanyok'}, 'e137382');



domready(function() {
GravityRD.start();
});

})();

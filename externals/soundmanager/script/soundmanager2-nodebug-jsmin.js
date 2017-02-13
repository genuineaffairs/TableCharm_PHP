/** @license
 *
 * SoundManager 2: JavaScript Sound for the Web
 * ----------------------------------------------
 * http://schillmania.com/projects/soundmanager2/
 *
 * Copyright (c) 2007, Scott Schiller. All rights reserved.
 * Code provided under the BSD License:
 * http://schillmania.com/projects/soundmanager2/license.txt
 *
 * V2.97a.20111030
 */
(function($){function N(N,Z){function j(c){return function(a){return!this._t||!this._t._a?null:c.call(this,a)}}this.flashVersion=8;this.debugFlash=this.debugMode=false;this.useConsole=true;this.waitForWindowLoad=this.consoleOnly=false;this.bgColor="#ffffff";this.useHighPerformance=false;this.flashPollingInterval=null;this.flashLoadTimeout=1E3;this.wmode=null;this.allowScriptAccess="always";this.useFlashBlock=false;this.useHTML5Audio=true;this.html5Test=/^(probably|maybe)$/i;this.preferFlash=true;
this.noSWFCache=false;this.audioFormats={mp3:{type:['audio/mpeg; codecs="mp3"',"audio/mpeg","audio/mp3","audio/MPA","audio/mpa-robust"],required:true},mp4:{related:["aac","m4a"],type:['audio/mp4; codecs="mp4a.40.2"',"audio/aac","audio/x-m4a","audio/MP4A-LATM","audio/mpeg4-generic"],required:false},ogg:{type:["audio/ogg; codecs=vorbis"],required:false},wav:{type:['audio/wav; codecs="1"',"audio/wav","audio/wave","audio/x-wav"],required:false}};this.defaultOptions={autoLoad:false,stream:true,autoPlay:false,
loops:1,onid3:null,onload:null,whileloading:null,onplay:null,onpause:null,onresume:null,whileplaying:null,onstop:null,onfailure:null,onfinish:null,multiShot:true,multiShotEvents:false,position:null,pan:0,type:null,usePolicyFile:false,volume:100};this.flash9Options={isMovieStar:null,usePeakData:false,useWaveformData:false,useEQData:false,onbufferchange:null,ondataerror:null};this.movieStarOptions={bufferTime:3,serverURL:null,onconnect:null,duration:null};this.movieID="sm2-container";this.id=Z||"sm2movie";
this.swfCSS={swfBox:"sm2-object-box",swfDefault:"movieContainer",swfError:"swf_error",swfTimedout:"swf_timedout",swfLoaded:"swf_loaded",swfUnblocked:"swf_unblocked",sm2Debug:"sm2_debug",highPerf:"high_performance",flashDebug:"flash_debug"};this.debugID="soundmanager-debug";this.debugURLParam=/([#?&])debug=1/i;this.versionNumber="V2.97a.20111030";this.movieURL=this.version=null;this.url=N||null;this.altURL=null;this.enabled=this.swfLoaded=false;this.oMC=this.o=null;this.sounds={};this.soundIDs=[];
this.didFlashBlock=this.specialWmodeCase=this.muted=false;this.filePattern=null;this.filePatterns={flash8:/\.mp3(\?.*)?$/i,flash9:/\.mp3(\?.*)?$/i};this.features={buffering:false,peakData:false,waveformData:false,eqData:false,movieStar:false};this.sandbox={};var F;try{F=typeof Audio!=="undefined"&&typeof(new Audio).canPlayType!=="undefined"}catch(Oa){F=false}this.hasHTML5=F;this.html5={usingFlash:null};this.flash={};this.ignoreFlash=this.html5Only=false;var ra,c=this,O,o=navigator.userAgent,i=$,aa=
i.location.href.toString(),h=document,ba,P,g,s=[],G=false,H=false,m=false,t=false,sa=false,I,n,ca,z,A,Q,ta,da,x,R,B,ea,fa,S,C,ua,ga,va,T,wa,J=null,ha=null,y,ia,D,U,V,ja,l,W=false,ka=false,xa,ya,q=null,za,X,K,u,la,ma,Aa,k,Ia=Array.prototype.slice,L=false,p,Y,Ba,r,Ca,na=o.match(/(ipad|iphone|ipod)/i),Ja=o.match(/firefox/i),Ka=o.match(/droid/i),v=o.match(/msie/i),La=o.match(/webkit/i),M=o.match(/safari/i)&&!o.match(/chrome/i),Ma=o.match(/opera/i);F=o.match(/(mobile|pre\/|xoom)/i)||na;var oa=!aa.match(/usehtml5audio/i)&&
!aa.match(/sm2\-ignorebadua/i)&&M&&o.match(/OS X 10_6_([3-7])/i),pa=typeof h.hasFocus!=="undefined"?h.hasFocus():null,E=M&&typeof h.hasFocus==="undefined",Da=!E,Ea=/(mp3|mp4|mpa)/i,qa=h.location?h.location.protocol.match(/http/i):null,Fa=!qa?"http://":"",Ga=/^\s*audio\/(?:x-)?(?:mpeg4|aac|flv|mov|mp4||m4v|m4a|mp4v|3gp|3g2)\s*(?:$|;)/i,Ha="mpeg4,aac,flv,mov,mp4,m4v,f4v,m4a,mp4v,3gp,3g2".split(","),Na=RegExp("\\.("+Ha.join("|")+")(\\?.*)?$","i");this.mimePattern=/^\s*audio\/(?:x-)?(?:mp(?:eg|3))\s*(?:$|;)/i;
this.useAltURL=!qa;this._global_a=null;if(F&&(c.useHTML5Audio=true,c.preferFlash=false,na))L=c.ignoreFlash=true;this.supported=this.ok=function(){return q?m&&!t:c.useHTML5Audio&&c.hasHTML5};this.getMovie=function(c){return O(c)||h[c]||i[c]};this.createSound=function(b){function a(){e=U(e);c.sounds[d.id]=new ra(d);c.soundIDs.push(d.id);return c.sounds[d.id]}var e=null,f=null,d=null;if(!m||!c.ok())return ja("soundManager.createSound(): "+y(!m?"notReady":"notOK")),false;arguments.length===2&&(b={id:arguments[0],
url:arguments[1]});d=e=n(b);if(l(d.id,true))return c.sounds[d.id];if(X(d))f=a(),f._setup_html5(d);else{if(g>8){if(d.isMovieStar===null)d.isMovieStar=d.serverURL||(d.type?d.type.match(Ga):false)||d.url.match(Na);if(d.isMovieStar&&d.usePeakData)d.usePeakData=false}d=V(d,"soundManager.createSound(): ");f=a();if(g===8)c.o._createSound(d.id,d.loops||1,d.usePolicyFile);else if(c.o._createSound(d.id,d.url,d.usePeakData,d.useWaveformData,d.useEQData,d.isMovieStar,d.isMovieStar?d.bufferTime:false,d.loops||
1,d.serverURL,d.duration||null,d.autoPlay,true,d.autoLoad,d.usePolicyFile),!d.serverURL)f.connected=true,d.onconnect&&d.onconnect.apply(f);!d.serverURL&&(d.autoLoad||d.autoPlay)&&f.load(d)}!d.serverURL&&d.autoPlay&&f.play();return f};this.destroySound=function(b,a){if(!l(b))return false;var e=c.sounds[b],f;e._iO={};e.stop();e.unload();for(f=0;f<c.soundIDs.length;f++)if(c.soundIDs[f]===b){c.soundIDs.splice(f,1);break}a||e.destruct(true);delete c.sounds[b];return true};this.load=function(b,a){return!l(b)?
false:c.sounds[b].load(a)};this.unload=function(b){return!l(b)?false:c.sounds[b].unload()};this.onposition=function(b,a,e,f){return!l(b)?false:c.sounds[b].onposition(a,e,f)};this.start=this.play=function(b,a){if(!m||!c.ok())return ja("soundManager.play(): "+y(!m?"notReady":"notOK")),false;return!l(b)?(a instanceof Object||(a={url:a}),a&&a.url?(a.id=b,c.createSound(a).play()):false):c.sounds[b].play(a)};this.setPosition=function(b,a){return!l(b)?false:c.sounds[b].setPosition(a)};this.stop=function(b){return!l(b)?
false:c.sounds[b].stop()};this.stopAll=function(){for(var b in c.sounds)c.sounds.hasOwnProperty(b)&&c.sounds[b].stop()};this.pause=function(b){return!l(b)?false:c.sounds[b].pause()};this.pauseAll=function(){var b;for(b=c.soundIDs.length;b--;)c.sounds[c.soundIDs[b]].pause()};this.resume=function(b){return!l(b)?false:c.sounds[b].resume()};this.resumeAll=function(){var b;for(b=c.soundIDs.length;b--;)c.sounds[c.soundIDs[b]].resume()};this.togglePause=function(b){return!l(b)?false:c.sounds[b].togglePause()};
this.setPan=function(b,a){return!l(b)?false:c.sounds[b].setPan(a)};this.setVolume=function(b,a){return!l(b)?false:c.sounds[b].setVolume(a)};this.mute=function(b){var a=0;typeof b!=="string"&&(b=null);if(b)return!l(b)?false:c.sounds[b].mute();else{for(a=c.soundIDs.length;a--;)c.sounds[c.soundIDs[a]].mute();c.muted=true}return true};this.muteAll=function(){c.mute()};this.unmute=function(b){typeof b!=="string"&&(b=null);if(b)return!l(b)?false:c.sounds[b].unmute();else{for(b=c.soundIDs.length;b--;)c.sounds[c.soundIDs[b]].unmute();
c.muted=false}return true};this.unmuteAll=function(){c.unmute()};this.toggleMute=function(b){return!l(b)?false:c.sounds[b].toggleMute()};this.getMemoryUse=function(){var b=0;c.o&&g!==8&&(b=parseInt(c.o._getMemoryUse(),10));return b};this.disable=function(b){var a;typeof b==="undefined"&&(b=false);if(t)return false;t=true;for(a=c.soundIDs.length;a--;)va(c.sounds[c.soundIDs[a]]);I(b);k.remove(i,"load",A);return true};this.canPlayMIME=function(b){var a;c.hasHTML5&&(a=K({type:b}));return!q||a?a:b?!!(g>
8&&b.match(Ga)||b.match(c.mimePattern)):null};this.canPlayURL=function(b){var a;c.hasHTML5&&(a=K({url:b}));return!q||a?a:b?!!b.match(c.filePattern):null};this.canPlayLink=function(b){return typeof b.type!=="undefined"&&b.type&&c.canPlayMIME(b.type)?true:c.canPlayURL(b.href)};this.getSoundById=function(b){if(!b)throw Error("soundManager.getSoundById(): sID is null/undefined");return c.sounds[b]};this.onready=function(c,a){if(c&&c instanceof Function)return a||(a=i),ca("onready",c,a),z(),true;else throw y("needFunction",
"onready");};this.ontimeout=function(c,a){if(c&&c instanceof Function)return a||(a=i),ca("ontimeout",c,a),z({type:"ontimeout"}),true;else throw y("needFunction","ontimeout");};this._wD=this._writeDebug=function(){return true};this._debug=function(){};this.reboot=function(){var b,a;for(b=c.soundIDs.length;b--;)c.sounds[c.soundIDs[b]].destruct();try{if(v)ha=c.o.innerHTML;J=c.o.parentNode.removeChild(c.o)}catch(e){}ha=J=q=null;c.enabled=ea=m=W=ka=G=H=t=c.swfLoaded=false;c.soundIDs=c.sounds=[];c.o=null;
for(b in s)if(s.hasOwnProperty(b))for(a=s[b].length;a--;)s[b][a].fired=false;i.setTimeout(c.beginDelayedInit,20)};this.getMoviePercent=function(){return c.o&&typeof c.o.PercentLoaded!=="undefined"?c.o.PercentLoaded():null};this.beginDelayedInit=function(){sa=true;B();setTimeout(function(){if(ka)return false;S();R();return ka=true},20);Q()};this.destruct=function(){c.disable(true)};ra=function(b){var a=this,e,f,d;this.sID=b.id;this.url=b.url;this._iO=this.instanceOptions=this.options=n(b);this.pan=
this.options.pan;this.volume=this.options.volume;this._lastURL=null;this.isHTML5=false;this._a=null;this.id3={};this._debug=function(){};this.load=function(b){var d=null;if(typeof b!=="undefined")a._iO=n(b,a.options),a.instanceOptions=a._iO;else if(b=a.options,a._iO=b,a.instanceOptions=a._iO,a._lastURL&&a._lastURL!==a.url)a._iO.url=a.url,a.url=null;if(!a._iO.url)a._iO.url=a.url;if(a._iO.url===a.url&&a.readyState!==0&&a.readyState!==2)return a;a._lastURL=a.url;a.loaded=false;a.readyState=1;a.playState=
0;if(X(a._iO)){if(d=a._setup_html5(a._iO),!d._called_load)a._html5_canplay=false,d.load(),d._called_load=true,a._iO.autoPlay&&a.play()}else try{a.isHTML5=false,a._iO=V(U(a._iO)),g===8?c.o._load(a.sID,a._iO.url,a._iO.stream,a._iO.autoPlay,a._iO.whileloading?1:0,a._iO.loops||1,a._iO.usePolicyFile):c.o._load(a.sID,a._iO.url,!!a._iO.stream,!!a._iO.autoPlay,a._iO.loops||1,!!a._iO.autoLoad,a._iO.usePolicyFile)}catch(e){C({type:"SMSOUND_LOAD_JS_EXCEPTION",fatal:true})}return a};this.unload=function(){a.readyState!==
0&&(a.isHTML5?(f(),a._a&&(a._a.pause(),la(a._a))):g===8?c.o._unload(a.sID,"about:blank"):c.o._unload(a.sID),e());return a};this.destruct=function(b){if(a.isHTML5){if(f(),a._a)a._a.pause(),la(a._a),L||a._remove_html5_events(),a._a._t=null,a._a=null}else a._iO.onfailure=null,c.o._destroySound(a.sID);b||c.destroySound(a.sID,true)};this.start=this.play=function(b,w){var e,w=w===void 0?true:w;b||(b={});a._iO=n(b,a._iO);a._iO=n(a._iO,a.options);a.instanceOptions=a._iO;if(a._iO.serverURL&&!a.connected)return a.getAutoPlay()||
a.setAutoPlay(true),a;X(a._iO)&&(a._setup_html5(a._iO),d());if(a.playState===1&&!a.paused&&(e=a._iO.multiShot,!e))return a;if(!a.loaded)if(a.readyState===0){if(!a.isHTML5)a._iO.autoPlay=true;a.load(a._iO)}else if(a.readyState===2)return a;if(!a.isHTML5&&g===9&&a.position>0&&a.position===a.duration)a._iO.position=0;if(a.paused&&a.position&&a.position>0)a.resume();else{a.playState=1;a.paused=false;(!a.instanceCount||a._iO.multiShotEvents||!a.isHTML5&&g>8&&!a.getAutoPlay())&&a.instanceCount++;a.position=
typeof a._iO.position!=="undefined"&&!isNaN(a._iO.position)?a._iO.position:0;if(!a.isHTML5)a._iO=V(U(a._iO));if(a._iO.onplay&&w)a._iO.onplay.apply(a),a._onplay_called=true;a.setVolume(a._iO.volume,true);a.setPan(a._iO.pan,true);a.isHTML5?(d(),e=a._setup_html5(),a.setPosition(a._iO.position),e.play()):c.o._start(a.sID,a._iO.loops||1,g===9?a._iO.position:a._iO.position/1E3)}return a};this.stop=function(b){if(a.playState===1){a._onbufferchange(0);a.resetOnPosition(0);a.paused=false;if(!a.isHTML5)a.playState=
0;a._iO.onstop&&a._iO.onstop.apply(a);if(a.isHTML5){if(a._a)a.setPosition(0),a._a.pause(),a.playState=0,a._onTimer(),f()}else c.o._stop(a.sID,b),a._iO.serverURL&&a.unload();a.instanceCount=0;a._iO={}}return a};this.setAutoPlay=function(b){a._iO.autoPlay=b;a.isHTML5||(c.o._setAutoPlay(a.sID,b),b&&!a.instanceCount&&a.readyState===1&&a.instanceCount++)};this.getAutoPlay=function(){return a._iO.autoPlay};this.setPosition=function(b){b===void 0&&(b=0);var d=a.isHTML5?Math.max(b,0):Math.min(a.duration||
a._iO.duration,Math.max(b,0));a.position=d;b=a.position/1E3;a.resetOnPosition(a.position);a._iO.position=d;if(a.isHTML5){if(a._a&&a._html5_canplay&&a._a.currentTime!==b)try{a._a.currentTime=b,(a.playState===0||a.paused)&&a._a.pause()}catch(e){}}else b=g===9?a.position:b,a.readyState&&a.readyState!==2&&c.o._setPosition(a.sID,b,a.paused||!a.playState);a.isHTML5&&a.paused&&a._onTimer(true);return a};this.pause=function(b){if(a.paused||a.playState===0&&a.readyState!==1)return a;a.paused=true;a.isHTML5?
(a._setup_html5().pause(),f()):(b||b===void 0)&&c.o._pause(a.sID);a._iO.onpause&&a._iO.onpause.apply(a);return a};this.resume=function(){if(!a.paused)return a;a.paused=false;a.playState=1;a.isHTML5?(a._setup_html5().play(),d()):(a._iO.isMovieStar&&a.setPosition(a.position),c.o._pause(a.sID));!a._onplay_called&&a._iO.onplay?(a._iO.onplay.apply(a),a._onplay_called=true):a._iO.onresume&&a._iO.onresume.apply(a);return a};this.togglePause=function(){if(a.playState===0)return a.play({position:g===9&&!a.isHTML5?
a.position:a.position/1E3}),a;a.paused?a.resume():a.pause();return a};this.setPan=function(b,d){typeof b==="undefined"&&(b=0);typeof d==="undefined"&&(d=false);a.isHTML5||c.o._setPan(a.sID,b);a._iO.pan=b;if(!d)a.pan=b,a.options.pan=b;return a};this.setVolume=function(b,d){typeof b==="undefined"&&(b=100);typeof d==="undefined"&&(d=false);if(a.isHTML5){if(a._a)a._a.volume=Math.max(0,Math.min(1,b/100))}else c.o._setVolume(a.sID,c.muted&&!a.muted||a.muted?0:b);a._iO.volume=b;if(!d)a.volume=b,a.options.volume=
b;return a};this.mute=function(){a.muted=true;if(a.isHTML5){if(a._a)a._a.muted=true}else c.o._setVolume(a.sID,0);return a};this.unmute=function(){a.muted=false;var b=typeof a._iO.volume!=="undefined";if(a.isHTML5){if(a._a)a._a.muted=false}else c.o._setVolume(a.sID,b?a._iO.volume:a.options.volume);return a};this.toggleMute=function(){return a.muted?a.unmute():a.mute()};this.onposition=function(b,c,d){a._onPositionItems.push({position:b,method:c,scope:typeof d!=="undefined"?d:a,fired:false});return a};
this.processOnPosition=function(){var b,d;b=a._onPositionItems.length;if(!b||!a.playState||a._onPositionFired>=b)return false;for(;b--;)if(d=a._onPositionItems[b],!d.fired&&a.position>=d.position)d.fired=true,c._onPositionFired++,d.method.apply(d.scope,[d.position]);return true};this.resetOnPosition=function(b){var d,e;d=a._onPositionItems.length;if(!d)return false;for(;d--;)if(e=a._onPositionItems[d],e.fired&&b<=e.position)e.fired=false,c._onPositionFired--;return true};d=function(){a.isHTML5&&xa(a)};
f=function(){a.isHTML5&&ya(a)};e=function(){a._onPositionItems=[];a._onPositionFired=0;a._hasTimer=null;a._onplay_called=false;a._a=null;a._html5_canplay=false;a.bytesLoaded=null;a.bytesTotal=null;a.position=null;a.duration=a._iO&&a._iO.duration?a._iO.duration:null;a.durationEstimate=null;a.failures=0;a.loaded=false;a.playState=0;a.paused=false;a.readyState=0;a.muted=false;a.isBuffering=false;a.instanceOptions={};a.instanceCount=0;a.peakData={left:0,right:0};a.waveformData={left:[],right:[]};a.eqData=
[];a.eqData.left=[];a.eqData.right=[]};e();this._onTimer=function(b){var c={};if(a._hasTimer||b)return a._a&&(b||(a.playState>0||a.readyState===1)&&!a.paused)?(a.duration=a._get_html5_duration(),a.durationEstimate=a.duration,b=a._a.currentTime?a._a.currentTime*1E3:0,a._whileplaying(b,c,c,c,c),true):false};this._get_html5_duration=function(){var b=a._a?a._a.duration*1E3:a._iO?a._iO.duration:void 0;return b&&!isNaN(b)&&b!==Infinity?b:a._iO?a._iO.duration:null};this._setup_html5=function(b){var b=n(a._iO,
b),d=L?c._global_a:a._a;decodeURI(b.url);var f=d&&d._t?d._t.instanceOptions:null;if(d){if(d._t&&f.url===b.url&&(!a._lastURL||a._lastURL===f.url))return d;L&&d._t&&d._t.playState&&b.url!==f.url&&d._t.stop();e();d.src=b.url;a.url=b.url;a._lastURL=b.url;d._called_load=false}else{d=new Audio(b.url);d._called_load=false;if(Ka)d._called_load=true;if(L)c._global_a=d}a.isHTML5=true;a._a=d;d._t=a;a._add_html5_events();d.loop=b.loops>1?"loop":"";b.autoLoad||b.autoPlay?(d.autobuffer="auto",d.preload="auto",
a.load(),d._called_load=true):(d.autobuffer=false,d.preload="none");d.loop=b.loops>1?"loop":"";return d};this._add_html5_events=function(){if(a._a._added_events)return false;var b;a._a._added_events=true;for(b in r)r.hasOwnProperty(b)&&a._a&&a._a.addEventListener(b,r[b],false);return true};this._remove_html5_events=function(){var b;a._a._added_events=false;for(b in r)r.hasOwnProperty(b)&&a._a&&a._a.removeEventListener(b,r[b],false)};this._onload=function(b){b=!!b;a.loaded=b;a.readyState=b?3:2;a._onbufferchange(0);
a._iO.onload&&a._iO.onload.apply(a,[b]);return true};this._onbufferchange=function(b){if(a.playState===0)return false;if(b&&a.isBuffering||!b&&!a.isBuffering)return false;a.isBuffering=b===1;a._iO.onbufferchange&&a._iO.onbufferchange.apply(a);return true};this._onsuspend=function(){a._iO.onsuspend&&a._iO.onsuspend.apply(a);return true};this._onfailure=function(b,c,d){a.failures++;if(a._iO.onfailure&&a.failures===1)a._iO.onfailure(a,b,c,d)};this._onfinish=function(){var b=a._iO.onfinish;a._onbufferchange(0);
a.resetOnPosition(0);if(a.instanceCount){a.instanceCount--;if(!a.instanceCount)a.playState=0,a.paused=false,a.instanceCount=0,a.instanceOptions={},a._iO={},f();(!a.instanceCount||a._iO.multiShotEvents)&&b&&b.apply(a)}};this._whileloading=function(b,c,d,e){a.bytesLoaded=b;a.bytesTotal=c;a.duration=Math.floor(d);a.bufferLength=e;if(a._iO.isMovieStar)a.durationEstimate=a.duration;else if(a.durationEstimate=a._iO.duration?a.duration>a._iO.duration?a.duration:a._iO.duration:parseInt(a.bytesTotal/a.bytesLoaded*
a.duration,10),a.durationEstimate===void 0)a.durationEstimate=a.duration;a.readyState!==3&&a._iO.whileloading&&a._iO.whileloading.apply(a)};this._whileplaying=function(b,c,d,e,f){if(isNaN(b)||b===null)return false;a.position=b;a.processOnPosition();if(!a.isHTML5&&g>8){if(a._iO.usePeakData&&typeof c!=="undefined"&&c)a.peakData={left:c.leftPeak,right:c.rightPeak};if(a._iO.useWaveformData&&typeof d!=="undefined"&&d)a.waveformData={left:d.split(","),right:e.split(",")};if(a._iO.useEQData&&typeof f!==
"undefined"&&f&&f.leftEQ&&(b=f.leftEQ.split(","),a.eqData=b,a.eqData.left=b,typeof f.rightEQ!=="undefined"&&f.rightEQ))a.eqData.right=f.rightEQ.split(",")}a.playState===1&&(!a.isHTML5&&g===8&&!a.position&&a.isBuffering&&a._onbufferchange(0),a._iO.whileplaying&&a._iO.whileplaying.apply(a));return true};this._onid3=function(b,c){var d=[],e,f;for(e=0,f=b.length;e<f;e++)d[b[e]]=c[e];a.id3=n(a.id3,d);a._iO.onid3&&a._iO.onid3.apply(a)};this._onconnect=function(b){b=b===1;if(a.connected=b)a.failures=0,l(a.sID)&&
(a.getAutoPlay()?a.play(void 0,a.getAutoPlay()):a._iO.autoLoad&&a.load()),a._iO.onconnect&&a._iO.onconnect.apply(a,[b])};this._ondataerror=function(){a.playState>0&&a._iO.ondataerror&&a._iO.ondataerror.apply(a)}};fa=function(){return h.body||h._docElement||h.getElementsByTagName("div")[0]};O=function(b){return h.getElementById(b)};n=function(b,a){var e={},f,d;for(f in b)b.hasOwnProperty(f)&&(e[f]=b[f]);f=typeof a==="undefined"?c.defaultOptions:a;for(d in f)f.hasOwnProperty(d)&&typeof e[d]==="undefined"&&
(e[d]=f[d]);return e};k=function(){function b(a){var a=Ia.call(a),b=a.length;c?(a[1]="on"+a[1],b>3&&a.pop()):b===3&&a.push(false);return a}function a(a,b){var w=a.shift(),h=[f[b]];if(c)w[h](a[0],a[1]);else w[h].apply(w,a)}var c=i.attachEvent,f={add:c?"attachEvent":"addEventListener",remove:c?"detachEvent":"removeEventListener"};return{add:function(){a(b(arguments),"add")},remove:function(){a(b(arguments),"remove")}}}();r={abort:j(function(){}),canplay:j(function(){if(this._t._html5_canplay)return true;
this._t._html5_canplay=true;this._t._onbufferchange(0);var b=!isNaN(this._t.position)?this._t.position/1E3:null;if(this._t.position&&this.currentTime!==b)try{this.currentTime=b}catch(a){}}),load:j(function(){this._t.loaded||(this._t._onbufferchange(0),this._t._whileloading(this._t.bytesTotal,this._t.bytesTotal,this._t._get_html5_duration()),this._t._onload(true))}),emptied:j(function(){}),ended:j(function(){this._t._onfinish()}),error:j(function(){this._t._onload(false)}),loadeddata:j(function(){var b=
this._t,a=b.bytesTotal||1;if(!b._loaded&&!M)b.duration=b._get_html5_duration(),b._whileloading(a,a,b._get_html5_duration()),b._onload(true)}),loadedmetadata:j(function(){}),loadstart:j(function(){this._t._onbufferchange(1)}),play:j(function(){this._t._onbufferchange(0)}),playing:j(function(){this._t._onbufferchange(0)}),progress:j(function(b){if(this._t.loaded)return false;var a,c=0,f=b.target.buffered;a=b.loaded||0;var d=b.total||1;if(f&&f.length){for(a=f.length;a--;)c=f.end(a)-f.start(a);a=c/b.target.duration}isNaN(a)||
(this._t._onbufferchange(0),this._t._whileloading(a,d,this._t._get_html5_duration()),a&&d&&a===d&&r.load.call(this,b))}),ratechange:j(function(){}),suspend:j(function(b){r.progress.call(this,b);this._t._onsuspend()}),stalled:j(function(){}),timeupdate:j(function(){this._t._onTimer()}),waiting:j(function(){this._t._onbufferchange(1)})};X=function(b){return!b.serverURL&&(b.type?K({type:b.type}):K({url:b.url})||c.html5Only)};la=function(b){if(b)b.src=Ja?"":"about:blank"};K=function(b){function a(a){return c.preferFlash&&
p&&!c.ignoreFlash&&typeof c.flash[a]!=="undefined"&&c.flash[a]}if(!c.useHTML5Audio||!c.hasHTML5)return false;var e=b.url||null,b=b.type||null,f=c.audioFormats,d;if(b&&c.html5[b]!=="undefined")return c.html5[b]&&!a(b);if(!u){u=[];for(d in f)f.hasOwnProperty(d)&&(u.push(d),f[d].related&&(u=u.concat(f[d].related)));u=RegExp("\\.("+u.join("|")+")(\\?.*)?$","i")}d=e?e.toLowerCase().match(u):null;if(!d||!d.length)if(b)e=b.indexOf(";"),d=(e!==-1?b.substr(0,e):b).substr(6);else return false;else d=d[1];return d&&
typeof c.html5[d]!=="undefined"?c.html5[d]&&!a(d):(b="audio/"+d,e=c.html5.canPlayType({type:b}),(c.html5[d]=e)&&c.html5[b]&&!a(b))};Aa=function(){function b(b){var d,e,f=false;if(!a||typeof a.canPlayType!=="function")return false;if(b instanceof Array){for(d=0,e=b.length;d<e&&!f;d++)if(c.html5[b[d]]||a.canPlayType(b[d]).match(c.html5Test))f=true,c.html5[b[d]]=true,c.flash[b[d]]=!(!c.preferFlash||!p||!b[d].match(Ea));return f}else return b=a&&typeof a.canPlayType==="function"?a.canPlayType(b):false,
!(!b||!b.match(c.html5Test))}if(!c.useHTML5Audio||typeof Audio==="undefined")return false;var a=typeof Audio!=="undefined"?Ma?new Audio(null):new Audio:null,e,f={},d,h;d=c.audioFormats;for(e in d)if(d.hasOwnProperty(e)&&(f[e]=b(d[e].type),f["audio/"+e]=f[e],c.flash[e]=c.preferFlash&&!c.ignoreFlash&&e.match(Ea)?true:false,d[e]&&d[e].related))for(h=d[e].related.length;h--;)f["audio/"+d[e].related[h]]=f[e],c.html5[d[e].related[h]]=f[e],c.flash[d[e].related[h]]=f[e];f.canPlayType=a?b:null;c.html5=n(c.html5,
f);return true};y=function(){};U=function(b){if(g===8&&b.loops>1&&b.stream)b.stream=false;return b};V=function(b){if(b&&!b.usePolicyFile&&(b.onid3||b.usePeakData||b.useWaveformData||b.useEQData))b.usePolicyFile=true;return b};ja=function(){};ba=function(){return false};va=function(b){for(var a in b)b.hasOwnProperty(a)&&typeof b[a]==="function"&&(b[a]=ba)};T=function(b){typeof b==="undefined"&&(b=false);(t||b)&&c.disable(b)};wa=function(b){var a=null;if(b)if(b.match(/\.swf(\?.*)?$/i)){if(a=b.substr(b.toLowerCase().lastIndexOf(".swf?")+
4))return b}else b.lastIndexOf("/")!==b.length-1&&(b+="/");b=(b&&b.lastIndexOf("/")!==-1?b.substr(0,b.lastIndexOf("/")+1):"./")+c.movieURL;c.noSWFCache&&(b+="?ts="+(new Date).getTime());return b};da=function(){g=parseInt(c.flashVersion,10);if(g!==8&&g!==9)c.flashVersion=g=8;var b=c.debugMode||c.debugFlash?"_debug.swf":".swf";if(c.useHTML5Audio&&!c.html5Only&&c.audioFormats.mp4.required&&g<9)c.flashVersion=g=9;c.version=c.versionNumber+(c.html5Only?" (HTML5-only mode)":g===9?" (AS3/Flash 9)":" (AS2/Flash 8)");
g>8?(c.defaultOptions=n(c.defaultOptions,c.flash9Options),c.features.buffering=true,c.defaultOptions=n(c.defaultOptions,c.movieStarOptions),c.filePatterns.flash9=RegExp("\\.(mp3|"+Ha.join("|")+")(\\?.*)?$","i"),c.features.movieStar=true):c.features.movieStar=false;c.filePattern=c.filePatterns[g!==8?"flash9":"flash8"];c.movieURL=(g===8?"soundmanager2.swf":"soundmanager2_flash9.swf").replace(".swf",b);c.features.peakData=c.features.waveformData=c.features.eqData=g>8};ua=function(b,a){if(!c.o)return false;
c.o._setPolling(b,a)};ga=function(){if(c.debugURLParam.test(aa))c.debugMode=true};l=this.getSoundById;D=function(){var b=[];c.debugMode&&b.push(c.swfCSS.sm2Debug);c.debugFlash&&b.push(c.swfCSS.flashDebug);c.useHighPerformance&&b.push(c.swfCSS.highPerf);return b.join(" ")};ia=function(){y("fbHandler");var b=c.getMoviePercent(),a=c.swfCSS,e={type:"FLASHBLOCK"};if(c.html5Only)return false;if(c.ok()){if(c.oMC)c.oMC.className=[D(),a.swfDefault,a.swfLoaded+(c.didFlashBlock?" "+a.swfUnblocked:"")].join(" ")}else{if(q)c.oMC.className=
D()+" "+a.swfDefault+" "+(b===null?a.swfTimedout:a.swfError);c.didFlashBlock=true;z({type:"ontimeout",ignoreInit:true,error:e});C(e)}};ca=function(b,a,c){typeof s[b]==="undefined"&&(s[b]=[]);s[b].push({method:a,scope:c||null,fired:false})};z=function(b){b||(b={type:"onready"});if(!m&&b&&!b.ignoreInit)return false;if(b.type==="ontimeout"&&c.ok())return false;var a={success:b&&b.ignoreInit?c.ok():!t},e=b&&b.type?s[b.type]||[]:[],f=[],d,a=[a],h=q&&c.useFlashBlock&&!c.ok();if(b.error)a[0].error=b.error;
for(b=0,d=e.length;b<d;b++)e[b].fired!==true&&f.push(e[b]);if(f.length)for(b=0,d=f.length;b<d;b++)if(f[b].scope?f[b].method.apply(f[b].scope,a):f[b].method.apply(this,a),!h)f[b].fired=true;return true};A=function(){i.setTimeout(function(){c.useFlashBlock&&ia();z();c.onload instanceof Function&&c.onload.apply(i);c.waitForWindowLoad&&k.add(i,"load",A)},1)};Y=function(){if(p!==void 0)return p;var b=false,a=navigator,c=a.plugins,f,d=i.ActiveXObject;if(c&&c.length)(a=a.mimeTypes)&&a["application/x-shockwave-flash"]&&
a["application/x-shockwave-flash"].enabledPlugin&&a["application/x-shockwave-flash"].enabledPlugin.description&&(b=true);else if(typeof d!=="undefined"){try{f=new d("ShockwaveFlash.ShockwaveFlash")}catch(h){}b=!!f}return p=b};za=function(){var b,a;if(na&&o.match(/os (1|2|3_0|3_1)/i)){c.hasHTML5=false;c.html5Only=true;if(c.oMC)c.oMC.style.display="none";return false}if(c.useHTML5Audio){if(!c.html5||!c.html5.canPlayType)return c.hasHTML5=false,true;else c.hasHTML5=true;if(oa&&Y())return true}else return true;
for(a in c.audioFormats)if(c.audioFormats.hasOwnProperty(a)&&(c.audioFormats[a].required&&!c.html5.canPlayType(c.audioFormats[a].type)||c.flash[a]||c.flash[c.audioFormats[a].type]))b=true;c.ignoreFlash&&(b=false);c.html5Only=c.hasHTML5&&c.useHTML5Audio&&!b;return!c.html5Only};xa=function(b){if(!b._hasTimer)b._hasTimer=true};ya=function(b){if(b._hasTimer)b._hasTimer=false};C=function(b){b=typeof b!=="undefined"?b:{};c.onerror instanceof Function&&c.onerror.apply(i,[{type:typeof b.type!=="undefined"?
b.type:null}]);typeof b.fatal!=="undefined"&&b.fatal&&c.disable()};Ba=function(){if(!oa||!Y())return false;var b=c.audioFormats,a,e;for(e in b)if(b.hasOwnProperty(e)&&(e==="mp3"||e==="mp4"))if(c.html5[e]=false,b[e]&&b[e].related)for(a=b[e].related.length;a--;)c.html5[b[e].related[a]]=false};this._setSandboxType=function(){};this._externalInterfaceOK=function(){if(c.swfLoaded)return false;(new Date).getTime();c.swfLoaded=true;E=false;oa&&Ba();v?setTimeout(P,100):P()};S=function(b,a){function e(a,b){return'<param name="'+
a+'" value="'+b+'" />'}if(G&&H)return false;if(c.html5Only)return da(),c.oMC=O(c.movieID),P(),H=G=true,false;var f=a||c.url,d=c.altURL||f,g;g=fa();var i,l,j=D(),k,m=null,m=(m=h.getElementsByTagName("html")[0])&&m.dir&&m.dir.match(/rtl/i),b=typeof b==="undefined"?c.id:b;da();c.url=wa(qa?f:d);a=c.url;c.wmode=!c.wmode&&c.useHighPerformance?"transparent":c.wmode;if(c.wmode!==null&&(o.match(/msie 8/i)||!v&&!c.useHighPerformance)&&navigator.platform.match(/win32|win64/i))c.specialWmodeCase=true,c.wmode=
null;g={name:b,id:b,src:a,width:"auto",height:"auto",quality:"high",allowScriptAccess:c.allowScriptAccess,bgcolor:c.bgColor,pluginspage:Fa+"www.macromedia.com/go/getflashplayer",title:"JS/Flash audio component (SoundManager 2)",type:"application/x-shockwave-flash",wmode:c.wmode,hasPriority:"true"};if(c.debugFlash)g.FlashVars="debug=1";c.wmode||delete g.wmode;if(v)f=h.createElement("div"),l=['<object id="'+b+'" data="'+a+'" type="'+g.type+'" title="'+g.title+'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="'+
Fa+'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="'+g.width+'" height="'+g.height+'">',e("movie",a),e("AllowScriptAccess",c.allowScriptAccess),e("quality",g.quality),c.wmode?e("wmode",c.wmode):"",e("bgcolor",c.bgColor),e("hasPriority","true"),c.debugFlash?e("FlashVars",g.FlashVars):"","</object>"].join("");else for(i in f=h.createElement("embed"),g)g.hasOwnProperty(i)&&f.setAttribute(i,g[i]);ga();j=D();if(g=fa())if(c.oMC=O(c.movieID)||h.createElement("div"),
c.oMC.id){k=c.oMC.className;c.oMC.className=(k?k+" ":c.swfCSS.swfDefault)+(j?" "+j:"");c.oMC.appendChild(f);if(v)i=c.oMC.appendChild(h.createElement("div")),i.className=c.swfCSS.swfBox,i.innerHTML=l;H=true}else{c.oMC.id=c.movieID;c.oMC.className=c.swfCSS.swfDefault+" "+j;i=j=null;if(!c.useFlashBlock)if(c.useHighPerformance)j={position:"fixed",width:"8px",height:"8px",bottom:"0px",left:"0px",overflow:"hidden"};else if(j={position:"absolute",width:"6px",height:"6px",top:"-9999px",left:"-9999px"},m)j.left=
Math.abs(parseInt(j.left,10))+"px";if(La)c.oMC.style.zIndex=1E4;if(!c.debugFlash)for(k in j)j.hasOwnProperty(k)&&(c.oMC.style[k]=j[k]);try{v||c.oMC.appendChild(f);g.appendChild(c.oMC);if(v)i=c.oMC.appendChild(h.createElement("div")),i.className=c.swfCSS.swfBox,i.innerHTML=l;H=true}catch(n){throw Error(y("domError")+" \n"+n.toString());}}return G=true};R=function(){if(c.html5Only)return S(),false;if(c.o)return false;c.o=c.getMovie(c.id);if(!c.o)J?(v?c.oMC.innerHTML=ha:c.oMC.appendChild(J),J=null,G=
true):S(c.id,c.url),c.o=c.getMovie(c.id);c.oninitmovie instanceof Function&&setTimeout(c.oninitmovie,1);return true};Q=function(){setTimeout(ta,1E3)};ta=function(){if(W)return false;W=true;k.remove(i,"load",Q);if(E&&!pa)return false;var b;m||(b=c.getMoviePercent());setTimeout(function(){b=c.getMoviePercent();!m&&Da&&(b===null?c.useFlashBlock||c.flashLoadTimeout===0?c.useFlashBlock&&ia():T(true):c.flashLoadTimeout!==0&&T(true))},c.flashLoadTimeout)};x=function(){function b(){k.remove(i,"focus",x);
k.remove(i,"load",x)}if(pa||!E)return b(),true;pa=Da=true;M&&E&&k.remove(i,"mousemove",x);W=false;b();return true};Ca=function(){var b,a=[];if(c.useHTML5Audio&&c.hasHTML5)for(b in c.audioFormats)c.audioFormats.hasOwnProperty(b)&&a.push(b+": "+c.html5[b]+(!c.html5[b]&&p&&c.flash[b]?" (using flash)":c.preferFlash&&c.flash[b]&&p?" (preferring flash)":!c.html5[b]?" ("+(c.audioFormats[b].required?"required, ":"")+"and no flash support)":""))};I=function(b){if(m)return false;if(c.html5Only)return m=true,
A(),true;var a;if(!c.useFlashBlock||!c.flashLoadTimeout||c.getMoviePercent())m=true,t&&(a={type:!p&&q?"NO_FLASH":"INIT_TIMEOUT"});if(t||b){if(c.useFlashBlock&&c.oMC)c.oMC.className=D()+" "+(c.getMoviePercent()===null?c.swfCSS.swfTimedout:c.swfCSS.swfError);z({type:"ontimeout",error:a});C(a);return false}if(c.waitForWindowLoad&&!sa)return k.add(i,"load",A),false;else A();return true};P=function(){if(m)return false;if(c.html5Only){if(!m)k.remove(i,"load",c.beginDelayedInit),c.enabled=true,I();return true}R();
try{c.o._externalInterfaceTest(false),ua(true,c.flashPollingInterval||(c.useHighPerformance?10:50)),c.debugMode||c.o._disableDebug(),c.enabled=true,c.html5Only||k.add(i,"unload",ba)}catch(b){return C({type:"JS_TO_FLASH_EXCEPTION",fatal:true}),T(true),I(),false}I();k.remove(i,"load",c.beginDelayedInit);return true};B=function(){if(ea)return false;ea=true;ga();if(!p&&c.hasHTML5)c.useHTML5Audio=true,c.preferFlash=false;Aa();c.html5.usingFlash=za();q=c.html5.usingFlash;Ca();if(!p&&q)c.flashLoadTimeout=
1;h.removeEventListener&&h.removeEventListener("DOMContentLoaded",B,false);R();return true};ma=function(){h.readyState==="complete"&&(B(),h.detachEvent("onreadystatechange",ma));return true};Y();k.add(i,"focus",x);k.add(i,"load",x);k.add(i,"load",Q);M&&E&&k.add(i,"mousemove",x);h.addEventListener?h.addEventListener("DOMContentLoaded",B,false):h.attachEvent?h.attachEvent("onreadystatechange",ma):C({type:"NO_DOM2_EVENTS",fatal:true});h.readyState==="complete"&&setTimeout(B,100)}var Z=null;
if(typeof SM2_DEFER==="undefined"||!SM2_DEFER) {
//Z=new N;
  // Modified by webligo - default initialization options
  var inDev = ( $type(en4.core.environment) && en4.core.environment == 'development' );
  var inXdom = ( $type(en4.core.staticBaseUrl) && en4.core.staticBaseUrl.indexOf('http') >= 0 );
  var url;
  if( $type(en4.core.staticBaseUrl) ) {
    url = en4.core.staticBaseUrl;
  } else {
    try {
      url = document.getElementsByTagName('base')[0].href;
    } catch( e ) {
      url = '';
    }
  }
  url += 'externals/soundmanager/swf/';
  if( inXdom ) {
    url += 'xdomain/';
  }
  Z = soundManager = new N(url);
  soundManager.url = url;

  soundManager.consoleOnly = true;
  soundManager.debugMode = inDev;
  soundManager.debugFlash = inDev;
  soundManager.flashVersion = 9;
  soundManager.useConsole = inDev;
  soundManager.useMovieStar = true;
  soundManager.useHTML5Audio = true;
  soundManager.noSWFCache = inDev;
  soundManager.allowPolling = true;
  soundManager.usePolicyFile = inXdom;
  soundManager.defaultOptions.usePolicyFile = inXdom;
  soundManager.preferFlash = false;
  soundManager.useHTML5Audio = true;
  // end - modified by webligo - default initialization options
}
$.SoundManager=N;$.soundManager=Z
})(window);
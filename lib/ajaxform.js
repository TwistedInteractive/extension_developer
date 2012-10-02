/**
 * @license GPL licenses.
 * @author Jason Green [guileen AT gmail.com]
 * Migrate from jquery Form Plugin : http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html.
 *
 *
 */

(function(window) {

/**
 * Base type fix
 */
String.prototype['trim'] = function() {
    return this.replace(/^\s+|\s+$/g, '');
}

String.prototype['trimLeft'] = function() {
    return this.replace(/^\s+/, '');
}

String.prototype['trimRight'] = function() {
    return this.replace(/\s+$/, '');
}

var rclass = /[\n\t]/g,
    rspace = /\s+/,
    rreturn = /\r/g,
    rspecialurl = /href|src|style/,
    rtype = /(button|input)/i,
    rfocusable = /(button|input|object|select|textarea)/i,
    rclickable = /^(a|area)$/i,
    rradiocheck = /radio|checkbox/;

var addClass = function(elem, value) {
    var classNames = (value || '').split(rspace);
    if (elem.nodeType === 1) {
        if (!elem.className) {
            elem.className = value;

        } else {
            var className = ' ' + elem.className + ' ';
            var setClass = elem.className;
            for (var c = 0, cl = classNames.length; c < cl; c++) {
                if (className.indexOf(' ' + classNames[c] + ' ') < 0) {
                    setClass += ' ' + classNames[c];
                }
            }
            elem.className = setClass.trim();
        }
    }
}

var removeClass = function(elem, value) {

    var classNames = (value || '').split(rspace);

    if (elem.nodeType === 1 && elem.className) {
        if (value) {
            var className = (' ' + elem.className + ' ').replace(rclass, ' ');
            for (var c = 0, cl = classNames.length; c < cl; c++) {
                className = className.replace(' ' + classNames[c] + ' ', ' ');
            }
            elem.className = className .trim();

        } else {
            elem.className = '';
        }
    }

}

var toggleClass = function(elem, value) {
    if (hasClass(elem, value)) {
        removeClass(elem, value);
    }else {
        addClass(elem, value);
    }
}

var hasClass = function(elem, selector) {
    var className = ' ' + selector + ' ';
    return (' ' + elem.className + ' ').replace(rclass, ' ')
        .indexOf(className) > -1;
}

/*
window.JSON = window.JSON || {};

JSON.parse = JSON.parse || function(data) {
    if (typeof data !== 'string' || !data) {
        return null;
    }

    // Make sure leading/trailing whitespace is removed (IE can't handle it)
    data = data.trim();

    // Make sure the incoming data is actual JSON
    // Logic borrowed from http://json.org/json2.js
    if (/^[\],:{}\s]*$/.test(
        data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').replace(
        /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

        // Try to use the native JSON parser first
        return (new Function('return ' + data))();

    } else {
        throw 'Invalid JSON: ' + data;
    }
};
*/

/**
 * call uri?jsonp=callback_name
 */
var JSONP = function(uri, params) {
    callback_called = false;

    var agent = navigator.userAgent.toLowerCase();

    uri += uri.indexOf('?') >= 0 ? '&' : '?' + buildQueryString(params);

    var script_channel = document.getElementById(uri);// reuse script element
    if (script_channel) {
        script_channel.setAttribute('src', uri);
        return;
    }
    script_channel = document.createElement('script');
    script_channel.id = uri;
    script_channel.src = uri;
    script_channel.type = 'text/javascript';
    script_channel.className = 'temp_script';

    var body = document.getElementsByTagName('body')[0];
    body.appendChild(script_channel);
};

/**
 * Returns the value of the field element.
 */
var fieldValue = function(el) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();

    if (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1) {
            return null;
    }

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) {
            return null;
        }
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index + 1 : ops.length);
        for (var i = (one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) { // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] &&
                            !(op.attributes['value'].specified)) ?
                            op.text : op.value;
                }
                if (one) {
                    return v;
                }
                a.push(v);
            }
        }
        return a;
    }
    return el.value;
};


var _appendNameValue = function(arr, name, value) {
    if (arr && arr.constructor == Array) {
        arr.push({name: name, value: value});
    }else {
        old = arr[name];
        if (old) {
            if (old.constructor != Array)
                arr[name] = [old];
            arr[name].push(value);
        }else {
            arr[name] = value;
        }
    }
}


/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example
 * of an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' },
 * { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to
 * the ajaxSubmit() and ajaxForm() methods.
 */
var formToArray = function(form, arr) {
    var a = arr || [];

    var els = form.elements;
    if (!els) {
        return a;
    }

    var i, j, n, v, el, max, jmax;
    for (i = 0, max = els.length; i < max; i++) {
        el = els[i];
        n = el.name;
        if (!n) {
            continue;
        }

        v = fieldValue(el);

        if (v && v.constructor == Array) {
            for (j = 0, jmax = v.length; j < jmax; j++) {
                _appendNameValue(a, n, v[j]);
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            _appendNameValue(a, n, v);
        }
    }

    return a;
}

/**
 * {"formname": {"name1":value1, "name2":["item1","item2","item3"] } }
 */
var formToObject = function(form, name) {
    var obj = formToArray(form, {});
    if (name) {
        var o = {};
        o[name] = obj;
        return o;
    }
    return obj;
}

// Serialize an array of form elements or a set of
// key/values into a query string
var buildQueryString = function(a) {

    var s = [];
    var isArray = a.constructor == Array;

    if (isArray) {
        for (var i = 0; i < a.length; i++) {
            var v = a[i];
            var k = v.name;
            v = v.value;
            s[s.length] = encodeURIComponent(k) + '=' + encodeURIComponent(v);
        }
    }else {
        for (var k in a) {
            var v = a[k];
            if (v && v.constructor == Array) {
                for (var i in v) {
                    s[s.length] = encodeURIComponent(k) +
                        '=' + encodeURIComponent(v[i]);
                }
            }else {
                s[s.length] = encodeURIComponent(k) +
                    '=' + encodeURIComponent(v);
            }
        }
    }

    // Return the resulting serialization
    return s.join('&').replace(' ', '+');
}

/**
 * serialize the form to query string
 */
var formSerialize = function(form) {
    return buildQueryString(formToArray(form));
}

var ajax = function(url, oncomplet, method, params, data, headers) {
    var method = method ? method.toUpperCase() : 'GET';
    var q = params ? buildQueryString(params) : null;
    var data = data || null;
    if (q) {
        if (method == 'GET' || data) {
            url += url.indexOf('?') >= 0 ? '&' : '?' + q;
        } else {
            data = q;
        }
    }

    var xmlHttp = window.XMLHttpRequest ? new XMLHttpRequest() :
                        new ActiveXObject('Microsoft.XMLHTTP');
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4)
            oncomplet(xmlHttp);
    };
    xmlHttp.open(method, url, true);
    for (var k in headers) {
        v = headers[k];
        if (typeof v == 'string')
            xmlHttp.setRequestHeader(k, v);
    }
    if (data && headers == null) {
        xmlHttp.setRequestHeader('Content-Type',
            'application/x-www-form-urlencoded');
    }
    xmlHttp.send(data);
}

/**
 * oncomplet is a function takes 1 argument instance of XMLHttpRequest
 */
var ajaxSubmit = function(form, oncomplet, headers) {
    ajax(form.action, oncomplet, form.method, formToArray(form), headers || {'Content-Type': 'application/x-www-form-urlencoded'});
}

var ajaxForm = function(form, oncomplet) {
    form.onsubmit = function(e) {
        ajaxSubmit(form, oncomplet);
        return false;
    }
}

var rest_content_type = 'application/json';

var rest = function(url, oncomplet, method, params, data, headers, content_type) {
    headers = headers || {};
    content_type = content_type || rest_content_type;
    headers['Accept'] = headers['Accept'] || content_type;
    headers['Content-Type'] = headers['Content-Type'] || content_type;
    ajax(url, oncomplet, method, params, data, headers);
}

var restSubmit = function(form, oncomplet, headers, content_type) {
    var postObject = formToObject(form, form.name);
    rest(form.action,
        function(xmlHttp) {
            oncomplet(xmlHttp, postObject);
        },
        form.method, null,
        JSON.stringify(postObject),
        headers, content_type);
}

var restForm = function(form, oncomplet, headers, content_type) {
    form.onsubmit = function(e) {
        restSubmit(form, oncomplet, headers, content_type);
        return false;
    }
}
window['formToArray'] = formToArray;
window['formToObject'] = formToObject;
window['formSerialize'] = formSerialize;
window['ajax'] = ajax;
window['ajaxSubmit'] = ajaxSubmit;
window['ajaxForm'] = ajaxForm;
window['rest'] = rest;
window['restSubmit'] = restSubmit;
window['restForm'] = restForm;

})(window);

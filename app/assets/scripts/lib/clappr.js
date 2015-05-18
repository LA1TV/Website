// my fix to get clappr working without requirejs conflict
(function() {

var require = undefined;
var define = undefined;
// end of fix



require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({"base_object":[function(require,module,exports){
"use strict";

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var uniqueId = require("./utils").uniqueId;
var Events = require("./events");

var BaseObject = (function (_Events) {
  function BaseObject() {
    var options = arguments[0] === undefined ? {} : arguments[0];

    _classCallCheck(this, BaseObject);

    this.uniqueId = uniqueId("o");
  }

  _inherits(BaseObject, _Events);

  return BaseObject;
})(Events);

module.exports = BaseObject;

},{"./events":"events","./utils":49}],"browser":[function(require,module,exports){
"use strict";

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Browser = function Browser() {
  _classCallCheck(this, Browser);
};

var hasLocalstorage = function hasLocalstorage() {
  try {
    localStorage.setItem("clappr", "clappr");
    localStorage.removeItem("clappr");
    return true;
  } catch (e) {
    return false;
  }
};

var hasFlash = function hasFlash() {
  try {
    var fo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
    return !!fo;
  } catch (e) {
    return !!(navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"] !== undefined && navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin);
  }
};

Browser.isSafari = !!navigator.userAgent.match(/safari/i) && navigator.userAgent.indexOf("Chrome") === -1;
Browser.isChrome = !!navigator.userAgent.match(/chrome/i);
Browser.isFirefox = !!navigator.userAgent.match(/firefox/i);
Browser.isLegacyIE = !!window.ActiveXObject;
Browser.isIE = Browser.isLegacyIE || !!navigator.userAgent.match(/trident.*rv:1\d/i);
Browser.isIE11 = !!navigator.userAgent.match(/trident.*rv:11/i);
Browser.isMobile = !!/Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone|IEMobile|Opera Mini/i.test(navigator.userAgent);
Browser.isiOs = !!/iPad|iPhone|iPod/i.test(navigator.userAgent);
Browser.isWin8App = !!/MSAppHost/i.test(navigator.userAgent);
Browser.isWiiU = !!/WiiU/i.test(navigator.userAgent);
Browser.isPS4 = !!/PlayStation 4/i.test(navigator.userAgent);
Browser.hasLocalstorage = hasLocalstorage();
Browser.hasFlash = hasFlash();

module.exports = Browser;

},{}],1:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseAssign = require('lodash._baseassign'),
    createAssigner = require('lodash._createassigner');

/**
 * Assigns own enumerable properties of source object(s) to the destination
 * object. Subsequent sources overwrite property assignments of previous sources.
 * If `customizer` is provided it is invoked to produce the assigned values.
 * The `customizer` is bound to `thisArg` and invoked with five arguments;
 * (objectValue, sourceValue, key, object, source).
 *
 * @static
 * @memberOf _
 * @alias extend
 * @category Object
 * @param {Object} object The destination object.
 * @param {...Object} [sources] The source objects.
 * @param {Function} [customizer] The function to customize assigning values.
 * @param {*} [thisArg] The `this` binding of `customizer`.
 * @returns {Object} Returns `object`.
 * @example
 *
 * _.assign({ 'user': 'barney' }, { 'age': 40 }, { 'user': 'fred' });
 * // => { 'user': 'fred', 'age': 40 }
 *
 * // using a customizer callback
 * var defaults = _.partialRight(_.assign, function(value, other) {
 *   return typeof value == 'undefined' ? other : value;
 * });
 *
 * defaults({ 'user': 'barney' }, { 'age': 36 }, { 'user': 'fred' });
 * // => { 'user': 'barney', 'age': 36 }
 */
var assign = createAssigner(baseAssign);

module.exports = assign;

},{"lodash._baseassign":2,"lodash._createassigner":8}],2:[function(require,module,exports){
/**
 * lodash 3.1.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseCopy = require('lodash._basecopy'),
    isNative = require('lodash.isnative'),
    keys = require('lodash.keys');

/** Native method references. */
var getOwnPropertySymbols = isNative(getOwnPropertySymbols = Object.getOwnPropertySymbols) && getOwnPropertySymbols;

/**
 * The base implementation of `_.assign` without support for argument juggling,
 * multiple sources, and `customizer` functions.
 *
 * @private
 * @param {Object} object The destination object.
 * @param {Object} source The source object.
 * @returns {Object} Returns `object`.
 */
var baseAssign = function(object, source) {
  return source == null
    ? object
    : baseCopy(source, getSymbols(source), baseCopy(source, keys(source), object));
};

/**
 * Creates an array of the own symbols of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of symbols.
 */
var getSymbols = !getOwnPropertySymbols ? constant([]) : function(object) {
  return getOwnPropertySymbols(toObject(object));
};

/**
 * Converts `value` to an object if it is not one.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {Object} Returns the object.
 */
function toObject(value) {
  return isObject(value) ? value : Object(value);
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

/**
 * Creates a function that returns `value`.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value The value to return from the new function.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var object = { 'user': 'fred' };
 * var getter = _.constant(object);
 *
 * getter() === object;
 * // => true
 */
function constant(value) {
  return function() {
    return value;
  };
}

module.exports = baseAssign;

},{"lodash._basecopy":3,"lodash.isnative":4,"lodash.keys":5}],3:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Copies properties of `source` to `object`.
 *
 * @private
 * @param {Object} source The object to copy properties from.
 * @param {Array} props The property names to copy.
 * @param {Object} [object={}] The object to copy properties to.
 * @returns {Object} Returns `object`.
 */
function baseCopy(source, props, object) {
  object || (object = {});

  var index = -1,
      length = props.length;

  while (++index < length) {
    var key = props[index];
    object[key] = source[key];
  }
  return object;
}

module.exports = baseCopy;

},{}],4:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var funcTag = '[object Function]';

/**
 * Used to match `RegExp` [special characters](http://www.regular-expressions.info/characters.html#special).
 * In addition to special characters the forward slash is escaped to allow for
 * easier `eval` use and `Function` compilation.
 */
var reRegExpChars = /[.*+?^${}()|[\]\/\\]/g,
    reHasRegExpChars = RegExp(reRegExpChars.source);

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * Converts `value` to a string if it is not one. An empty string is returned
 * for `null` or `undefined` values.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  if (typeof value == 'string') {
    return value;
  }
  return value == null ? '' : (value + '');
}

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  escapeRegExp(objToString)
  .replace(/toString|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (objToString.call(value) == funcTag) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

/**
 * Escapes the `RegExp` special characters "\", "/", "^", "$", ".", "|", "?",
 * "*", "+", "(", ")", "[", "]", "{" and "}" in `string`.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to escape.
 * @returns {string} Returns the escaped string.
 * @example
 *
 * _.escapeRegExp('[lodash](https://lodash.com/)');
 * // => '\[lodash\]\(https:\/\/lodash\.com\/\)'
 */
function escapeRegExp(string) {
  string = baseToString(string);
  return (string && reHasRegExpChars.test(string))
    ? string.replace(reRegExpChars, '\\$&')
    : string;
}

module.exports = isNative;

},{}],5:[function(require,module,exports){
/**
 * lodash 3.0.7 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var isArguments = require('lodash.isarguments'),
    isArray = require('lodash.isarray'),
    isNative = require('lodash.isnative');

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/** Native method references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/* Native method references for those with the same name as other `lodash` methods. */
var nativeKeys = isNative(nativeKeys = Object.keys) && nativeKeys;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * An object environment feature flags.
 *
 * @static
 * @memberOf _
 * @type Object
 */
var support = {};

(function(x) {
  var Ctor = function() { this.x = x; },
      args = arguments,
      object = { '0': x, 'length': x },
      props = [];

  Ctor.prototype = { 'valueOf': x, 'y': x };
  for (var key in new Ctor) { props.push(key); }

  /**
   * Detect if `arguments` object indexes are non-enumerable.
   *
   * In Firefox < 4, IE < 9, PhantomJS, and Safari < 5.1 `arguments` object
   * indexes are non-enumerable. Chrome < 25 and Node.js < 0.11.0 treat
   * `arguments` object indexes as non-enumerable and fail `hasOwnProperty`
   * checks for indexes that exceed the number of function parameters and
   * whose associated argument values are `0`.
   *
   * @memberOf _.support
   * @type boolean
   */
  try {
    support.nonEnumArgs = !propertyIsEnumerable.call(args, 1);
  } catch(e) {
    support.nonEnumArgs = true;
  }
}(1, 0));

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = +value;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * A fallback implementation of `Object.keys` which creates an array of the
 * own enumerable property names of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */
function shimKeys(object) {
  var props = keysIn(object),
      propsLength = props.length,
      length = propsLength && object.length;

  var allowIndexes = length && isLength(length) &&
    (isArray(object) || (support.nonEnumArgs && isArguments(object)));

  var index = -1,
      result = [];

  while (++index < propsLength) {
    var key = props[index];
    if ((allowIndexes && isIndex(key, length)) || hasOwnProperty.call(object, key)) {
      result.push(key);
    }
  }
  return result;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

/**
 * Creates an array of the own enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects. See the
 * [ES spec](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.keys)
 * for more details.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keys(new Foo);
 * // => ['a', 'b'] (iteration order is not guaranteed)
 *
 * _.keys('hi');
 * // => ['0', '1']
 */
var keys = !nativeKeys ? shimKeys : function(object) {
  var Ctor = object != null && object.constructor;
  if ((typeof Ctor == 'function' && Ctor.prototype === object) ||
      (typeof object != 'function' && isArrayLike(object))) {
    return shimKeys(object);
  }
  return isObject(object) ? nativeKeys(object) : [];
};

/**
 * Creates an array of the own and inherited enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keysIn(new Foo);
 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
 */
function keysIn(object) {
  if (object == null) {
    return [];
  }
  if (!isObject(object)) {
    object = Object(object);
  }
  var length = object.length;
  length = (length && isLength(length) &&
    (isArray(object) || (support.nonEnumArgs && isArguments(object))) && length) || 0;

  var Ctor = object.constructor,
      index = -1,
      isProto = typeof Ctor == 'function' && Ctor.prototype === object,
      result = Array(length),
      skipIndexes = length > 0;

  while (++index < length) {
    result[index] = (index + '');
  }
  for (var key in object) {
    if (!(skipIndexes && isIndex(key, length)) &&
        !(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = keys;

},{"lodash.isarguments":6,"lodash.isarray":7,"lodash.isnative":4}],6:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var argsTag = '[object Arguments]';

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as an `arguments` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */
function isArguments(value) {
  return isObjectLike(value) && isArrayLike(value) && objToString.call(value) == argsTag;
}

module.exports = isArguments;

},{}],7:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var arrayTag = '[object Array]',
    funcTag = '[object Function]';

/**
 * Used to match `RegExp` [special characters](http://www.regular-expressions.info/characters.html#special).
 * In addition to special characters the forward slash is escaped to allow for
 * easier `eval` use and `Function` compilation.
 */
var reRegExpChars = /[.*+?^${}()|[\]\/\\]/g,
    reHasRegExpChars = RegExp(reRegExpChars.source);

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * Converts `value` to a string if it is not one. An empty string is returned
 * for `null` or `undefined` values.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  if (typeof value == 'string') {
    return value;
  }
  return value == null ? '' : (value + '');
}

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  escapeRegExp(objToString)
  .replace(/toString|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/* Native method references for those with the same name as other `lodash` methods. */
var nativeIsArray = isNative(nativeIsArray = Array.isArray) && nativeIsArray;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(function() { return arguments; }());
 * // => false
 */
var isArray = nativeIsArray || function(value) {
  return isObjectLike(value) && isLength(value.length) && objToString.call(value) == arrayTag;
};

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (objToString.call(value) == funcTag) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

/**
 * Escapes the `RegExp` special characters "\", "/", "^", "$", ".", "|", "?",
 * "*", "+", "(", ")", "[", "]", "{" and "}" in `string`.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to escape.
 * @returns {string} Returns the escaped string.
 * @example
 *
 * _.escapeRegExp('[lodash](https://lodash.com/)');
 * // => '\[lodash\]\(https:\/\/lodash\.com\/\)'
 */
function escapeRegExp(string) {
  string = baseToString(string);
  return (string && reHasRegExpChars.test(string))
    ? string.replace(reRegExpChars, '\\$&')
    : string;
}

module.exports = isArray;

},{}],8:[function(require,module,exports){
/**
 * lodash 3.1.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var bindCallback = require('lodash._bindcallback'),
    isIterateeCall = require('lodash._isiterateecall'),
    restParam = require('lodash.restparam');

/**
 * Creates a function that assigns properties of source object(s) to a given
 * destination object.
 *
 * **Note:** This function is used to create `_.assign`, `_.defaults`, and `_.merge`.
 *
 * @private
 * @param {Function} assigner The function to assign values.
 * @returns {Function} Returns the new assigner function.
 */
function createAssigner(assigner) {
  return restParam(function(object, sources) {
    var index = -1,
        length = object == null ? 0 : sources.length,
        customizer = length > 2 && sources[length - 2],
        guard = length > 2 && sources[2],
        thisArg = length > 1 && sources[length - 1];

    if (typeof customizer == 'function') {
      customizer = bindCallback(customizer, thisArg, 5);
      length -= 2;
    } else {
      customizer = typeof thisArg == 'function' ? thisArg : null;
      length -= (customizer ? 1 : 0);
    }
    if (guard && isIterateeCall(sources[0], sources[1], guard)) {
      customizer = length < 3 ? null : customizer;
      length = 1;
    }
    while (++index < length) {
      var source = sources[index];
      if (source) {
        assigner(object, source, customizer);
      }
    }
    return object;
  });
}

module.exports = createAssigner;

},{"lodash._bindcallback":9,"lodash._isiterateecall":10,"lodash.restparam":11}],9:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * A specialized version of `baseCallback` which only supports `this` binding
 * and specifying the number of arguments to provide to `func`.
 *
 * @private
 * @param {Function} func The function to bind.
 * @param {*} thisArg The `this` binding of `func`.
 * @param {number} [argCount] The number of arguments to provide to `func`.
 * @returns {Function} Returns the callback.
 */
function bindCallback(func, thisArg, argCount) {
  if (typeof func != 'function') {
    return identity;
  }
  if (thisArg === undefined) {
    return func;
  }
  switch (argCount) {
    case 1: return function(value) {
      return func.call(thisArg, value);
    };
    case 3: return function(value, index, collection) {
      return func.call(thisArg, value, index, collection);
    };
    case 4: return function(accumulator, value, index, collection) {
      return func.call(thisArg, accumulator, value, index, collection);
    };
    case 5: return function(value, other, key, object, source) {
      return func.call(thisArg, value, other, key, object, source);
    };
  }
  return function() {
    return func.apply(thisArg, arguments);
  };
}

/**
 * This method returns the first argument provided to it.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'user': 'fred' };
 *
 * _.identity(object) === object;
 * // => true
 */
function identity(value) {
  return value;
}

module.exports = bindCallback;

},{}],10:[function(require,module,exports){
/**
 * lodash 3.0.7 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = +value;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if the provided arguments are from an iteratee call.
 *
 * @private
 * @param {*} value The potential iteratee value argument.
 * @param {*} index The potential iteratee index or key argument.
 * @param {*} object The potential iteratee object argument.
 * @returns {boolean} Returns `true` if the arguments are from an iteratee call, else `false`.
 */
function isIterateeCall(value, index, object) {
  if (!isObject(object)) {
    return false;
  }
  var type = typeof index;
  if (type == 'number'
      ? (isArrayLike(object) && isIndex(index, object.length))
      : (type == 'string' && index in object)) {
    var other = object[index];
    return value === value ? (value === other) : (other !== other);
  }
  return false;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

module.exports = isIterateeCall;

},{}],11:[function(require,module,exports){
/**
 * lodash 3.6.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used as the `TypeError` message for "Functions" methods. */
var FUNC_ERROR_TEXT = 'Expected a function';

/* Native method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max;

/**
 * Creates a function that invokes `func` with the `this` binding of the
 * created function and arguments from `start` and beyond provided as an array.
 *
 * **Note:** This method is based on the [rest parameter](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/rest_parameters).
 *
 * @static
 * @memberOf _
 * @category Function
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var say = _.restParam(function(what, names) {
 *   return what + ' ' + _.initial(names).join(', ') +
 *     (_.size(names) > 1 ? ', & ' : '') + _.last(names);
 * });
 *
 * say('hello', 'fred', 'barney', 'pebbles');
 * // => 'hello fred, barney, & pebbles'
 */
function restParam(func, start) {
  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  start = nativeMax(start === undefined ? (func.length - 1) : (+start || 0), 0);
  return function() {
    var args = arguments,
        index = -1,
        length = nativeMax(args.length - start, 0),
        rest = Array(length);

    while (++index < length) {
      rest[index] = args[start + index];
    }
    switch (start) {
      case 0: return func.call(this, rest);
      case 1: return func.call(this, args[0], rest);
      case 2: return func.call(this, args[0], args[1], rest);
    }
    var otherArgs = Array(start + 1);
    index = -1;
    while (++index < start) {
      otherArgs[index] = args[index];
    }
    otherArgs[start] = rest;
    return func.apply(this, otherArgs);
  };
}

module.exports = restParam;

},{}],12:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseCallback = require('lodash._basecallback'),
    baseEach = require('lodash._baseeach'),
    baseFind = require('lodash._basefind'),
    findIndex = require('lodash.findindex'),
    isArray = require('lodash.isarray');

/**
 * Iterates over elements of `collection`, returning the first element
 * `predicate` returns truthy for. The predicate is bound to `thisArg` and
 * invoked with three arguments; (value, index|key, collection).
 *
 * If a property name is provided for `predicate` the created "_.property"
 * style callback returns the property value of the given element.
 *
 * If an object is provided for `predicate` the created "_.matches" style
 * callback returns `true` for elements that have the properties of the given
 * object, else `false`.
 *
 * @static
 * @memberOf _
 * @alias detect
 * @category Collection
 * @param {Array|Object|string} collection The collection to search.
 * @param {Function|Object|string} [predicate=_.identity] The function invoked
 *  per iteration. If a property name or object is provided it is used to
 *  create a "_.property" or "_.matches" style callback respectively.
 * @param {*} [thisArg] The `this` binding of `predicate`.
 * @returns {*} Returns the matched element, else `undefined`.
 * @example
 *
 * var users = [
 *   { 'user': 'barney',  'age': 36, 'active': false },
 *   { 'user': 'fred',    'age': 40, 'active': true },
 *   { 'user': 'pebbles', 'age': 1,  'active': false }
 * ];
 *
 * _.result(_.find(users, function(chr) { return chr.age < 40; }), 'user');
 * // => 'barney'
 *
 * // using the "_.matches" callback shorthand
 * _.result(_.find(users, { 'age': 1 }), 'user');
 * // => 'pebbles'
 *
 * // using the "_.property" callback shorthand
 * _.result(_.find(users, 'active'), 'user');
 * // => 'fred'
 */
function find(collection, predicate, thisArg) {
  if (isArray(collection)) {
    var index = findIndex(collection, predicate, thisArg);
    return index > -1 ? collection[index] : undefined;
  }
  predicate = baseCallback(predicate, thisArg, 3);
  return baseFind(collection, predicate, baseEach);
}

module.exports = find;

},{"lodash._basecallback":13,"lodash._baseeach":20,"lodash._basefind":24,"lodash.findindex":25,"lodash.isarray":27}],13:[function(require,module,exports){
/**
 * lodash 3.2.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseIsEqual = require('lodash._baseisequal'),
    bindCallback = require('lodash._bindcallback'),
    isArray = require('lodash.isarray'),
    keys = require('lodash.keys');

/** Used to match property names within property paths. */
var reIsDeepProp = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\n\\]|\\.)*?\1)\]/,
    reIsPlainProp = /^\w*$/,
    rePropName = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\n\\]|\\.)*?)\2)\]/g;

/** Used to match backslashes in property paths. */
var reEscapeChar = /\\(\\)?/g;

/**
 * Converts `value` to a string if it is not one. An empty string is returned
 * for `null` or `undefined` values.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  if (typeof value == 'string') {
    return value;
  }
  return value == null ? '' : (value + '');
}

/**
 * The base implementation of `_.callback` which supports specifying the
 * number of arguments to provide to `func`.
 *
 * @private
 * @param {*} [func=_.identity] The value to convert to a callback.
 * @param {*} [thisArg] The `this` binding of `func`.
 * @param {number} [argCount] The number of arguments to provide to `func`.
 * @returns {Function} Returns the callback.
 */
function baseCallback(func, thisArg, argCount) {
  var type = typeof func;
  if (type == 'function') {
    return thisArg === undefined
      ? func
      : bindCallback(func, thisArg, argCount);
  }
  if (func == null) {
    return identity;
  }
  if (type == 'object') {
    return baseMatches(func);
  }
  return thisArg === undefined
    ? property(func)
    : baseMatchesProperty(func, thisArg);
}

/**
 * The base implementation of `get` without support for string paths
 * and default values.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {Array} path The path of the property to get.
 * @param {string} [pathKey] The key representation of path.
 * @returns {*} Returns the resolved value.
 */
function baseGet(object, path, pathKey) {
  if (object == null) {
    return;
  }
  if (pathKey !== undefined && pathKey in toObject(object)) {
    path = [pathKey];
  }
  var index = -1,
      length = path.length;

  while (object != null && ++index < length) {
    object = object[path[index]];
  }
  return (index && index == length) ? object : undefined;
}

/**
 * The base implementation of `_.isMatch` without support for callback
 * shorthands and `this` binding.
 *
 * @private
 * @param {Object} object The object to inspect.
 * @param {Array} props The source property names to match.
 * @param {Array} values The source values to match.
 * @param {Array} strictCompareFlags Strict comparison flags for source values.
 * @param {Function} [customizer] The function to customize comparing objects.
 * @returns {boolean} Returns `true` if `object` is a match, else `false`.
 */
function baseIsMatch(object, props, values, strictCompareFlags, customizer) {
  var index = -1,
      length = props.length,
      noCustomizer = !customizer;

  while (++index < length) {
    if ((noCustomizer && strictCompareFlags[index])
          ? values[index] !== object[props[index]]
          : !(props[index] in object)
        ) {
      return false;
    }
  }
  index = -1;
  while (++index < length) {
    var key = props[index],
        objValue = object[key],
        srcValue = values[index];

    if (noCustomizer && strictCompareFlags[index]) {
      var result = objValue !== undefined || (key in object);
    } else {
      result = customizer ? customizer(objValue, srcValue, key) : undefined;
      if (result === undefined) {
        result = baseIsEqual(srcValue, objValue, customizer, true);
      }
    }
    if (!result) {
      return false;
    }
  }
  return true;
}

/**
 * The base implementation of `_.matches` which does not clone `source`.
 *
 * @private
 * @param {Object} source The object of property values to match.
 * @returns {Function} Returns the new function.
 */
function baseMatches(source) {
  var props = keys(source),
      length = props.length;

  if (!length) {
    return constant(true);
  }
  if (length == 1) {
    var key = props[0],
        value = source[key];

    if (isStrictComparable(value)) {
      return function(object) {
        if (object == null) {
          return false;
        }
        return object[key] === value && (value !== undefined || (key in toObject(object)));
      };
    }
  }
  var values = Array(length),
      strictCompareFlags = Array(length);

  while (length--) {
    value = source[props[length]];
    values[length] = value;
    strictCompareFlags[length] = isStrictComparable(value);
  }
  return function(object) {
    return object != null && baseIsMatch(toObject(object), props, values, strictCompareFlags);
  };
}

/**
 * The base implementation of `_.matchesProperty` which does not which does
 * not clone `value`.
 *
 * @private
 * @param {string} path The path of the property to get.
 * @param {*} value The value to compare.
 * @returns {Function} Returns the new function.
 */
function baseMatchesProperty(path, value) {
  var isArr = isArray(path),
      isCommon = isKey(path) && isStrictComparable(value),
      pathKey = (path + '');

  path = toPath(path);
  return function(object) {
    if (object == null) {
      return false;
    }
    var key = pathKey;
    object = toObject(object);
    if ((isArr || !isCommon) && !(key in object)) {
      object = path.length == 1 ? object : baseGet(object, baseSlice(path, 0, -1));
      if (object == null) {
        return false;
      }
      key = last(path);
      object = toObject(object);
    }
    return object[key] === value
      ? (value !== undefined || (key in object))
      : baseIsEqual(value, object[key], null, true);
  };
}

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * A specialized version of `baseProperty` which supports deep paths.
 *
 * @private
 * @param {Array|string} path The path of the property to get.
 * @returns {Function} Returns the new function.
 */
function basePropertyDeep(path) {
  var pathKey = (path + '');
  path = toPath(path);
  return function(object) {
    return baseGet(object, path, pathKey);
  };
}

/**
 * The base implementation of `_.slice` without an iteratee call guard.
 *
 * @private
 * @param {Array} array The array to slice.
 * @param {number} [start=0] The start position.
 * @param {number} [end=array.length] The end position.
 * @returns {Array} Returns the slice of `array`.
 */
function baseSlice(array, start, end) {
  var index = -1,
      length = array.length;

  start = start == null ? 0 : (+start || 0);
  if (start < 0) {
    start = -start > length ? 0 : (length + start);
  }
  end = (end === undefined || end > length) ? length : (+end || 0);
  if (end < 0) {
    end += length;
  }
  length = start > end ? 0 : ((end - start) >>> 0);
  start >>>= 0;

  var result = Array(length);
  while (++index < length) {
    result[index] = array[index + start];
  }
  return result;
}

/**
 * Checks if `value` is a property name and not a property path.
 *
 * @private
 * @param {*} value The value to check.
 * @param {Object} [object] The object to query keys on.
 * @returns {boolean} Returns `true` if `value` is a property name, else `false`.
 */
function isKey(value, object) {
  var type = typeof value;
  if ((type == 'string' && reIsPlainProp.test(value)) || type == 'number') {
    return true;
  }
  if (isArray(value)) {
    return false;
  }
  var result = !reIsDeepProp.test(value);
  return result || (object != null && value in toObject(object));
}

/**
 * Checks if `value` is suitable for strict equality comparisons, i.e. `===`.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` if suitable for strict
 *  equality comparisons, else `false`.
 */
function isStrictComparable(value) {
  return value === value && !isObject(value);
}

/**
 * Converts `value` to an object if it is not one.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {Object} Returns the object.
 */
function toObject(value) {
  return isObject(value) ? value : Object(value);
}

/**
 * Converts `value` to property path array if it is not one.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {Array} Returns the property path array.
 */
function toPath(value) {
  if (isArray(value)) {
    return value;
  }
  var result = [];
  baseToString(value).replace(rePropName, function(match, number, quote, string) {
    result.push(quote ? string.replace(reEscapeChar, '$1') : (number || match));
  });
  return result;
}

/**
 * Gets the last element of `array`.
 *
 * @static
 * @memberOf _
 * @category Array
 * @param {Array} array The array to query.
 * @returns {*} Returns the last element of `array`.
 * @example
 *
 * _.last([1, 2, 3]);
 * // => 3
 */
function last(array) {
  var length = array ? array.length : 0;
  return length ? array[length - 1] : undefined;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

/**
 * Creates a function that returns `value`.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value The value to return from the new function.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var object = { 'user': 'fred' };
 * var getter = _.constant(object);
 *
 * getter() === object;
 * // => true
 */
function constant(value) {
  return function() {
    return value;
  };
}

/**
 * This method returns the first argument provided to it.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'user': 'fred' };
 *
 * _.identity(object) === object;
 * // => true
 */
function identity(value) {
  return value;
}

/**
 * Creates a function which returns the property value at `path` on a
 * given object.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {Array|string} path The path of the property to get.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var objects = [
 *   { 'a': { 'b': { 'c': 2 } } },
 *   { 'a': { 'b': { 'c': 1 } } }
 * ];
 *
 * _.map(objects, _.property('a.b.c'));
 * // => [2, 1]
 *
 * _.pluck(_.sortBy(objects, _.property(['a', 'b', 'c'])), 'a.b.c');
 * // => [1, 2]
 */
function property(path) {
  return isKey(path) ? baseProperty(path) : basePropertyDeep(path);
}

module.exports = baseCallback;

},{"lodash._baseisequal":14,"lodash._bindcallback":16,"lodash.isarray":27,"lodash.keys":17}],14:[function(require,module,exports){
/**
 * lodash 3.0.5 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var isArray = require('lodash.isarray'),
    isTypedArray = require('lodash.istypedarray'),
    keys = require('lodash.keys');

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    arrayTag = '[object Array]',
    boolTag = '[object Boolean]',
    dateTag = '[object Date]',
    errorTag = '[object Error]',
    numberTag = '[object Number]',
    objectTag = '[object Object]',
    regexpTag = '[object RegExp]',
    stringTag = '[object String]';

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/**
 * The base implementation of `_.isEqual` without support for `this` binding
 * `customizer` functions.
 *
 * @private
 * @param {*} value The value to compare.
 * @param {*} other The other value to compare.
 * @param {Function} [customizer] The function to customize comparing values.
 * @param {boolean} [isLoose] Specify performing partial comparisons.
 * @param {Array} [stackA] Tracks traversed `value` objects.
 * @param {Array} [stackB] Tracks traversed `other` objects.
 * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
 */
function baseIsEqual(value, other, customizer, isLoose, stackA, stackB) {
  // Exit early for identical values.
  if (value === other) {
    return true;
  }
  var valType = typeof value,
      othType = typeof other;

  // Exit early for unlike primitive values.
  if ((valType != 'function' && valType != 'object' && othType != 'function' && othType != 'object') ||
      value == null || other == null) {
    // Return `false` unless both values are `NaN`.
    return value !== value && other !== other;
  }
  return baseIsEqualDeep(value, other, baseIsEqual, customizer, isLoose, stackA, stackB);
}

/**
 * A specialized version of `baseIsEqual` for arrays and objects which performs
 * deep comparisons and tracks traversed objects enabling objects with circular
 * references to be compared.
 *
 * @private
 * @param {Object} object The object to compare.
 * @param {Object} other The other object to compare.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Function} [customizer] The function to customize comparing objects.
 * @param {boolean} [isLoose] Specify performing partial comparisons.
 * @param {Array} [stackA=[]] Tracks traversed `value` objects.
 * @param {Array} [stackB=[]] Tracks traversed `other` objects.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function baseIsEqualDeep(object, other, equalFunc, customizer, isLoose, stackA, stackB) {
  var objIsArr = isArray(object),
      othIsArr = isArray(other),
      objTag = arrayTag,
      othTag = arrayTag;

  if (!objIsArr) {
    objTag = objToString.call(object);
    if (objTag == argsTag) {
      objTag = objectTag;
    } else if (objTag != objectTag) {
      objIsArr = isTypedArray(object);
    }
  }
  if (!othIsArr) {
    othTag = objToString.call(other);
    if (othTag == argsTag) {
      othTag = objectTag;
    } else if (othTag != objectTag) {
      othIsArr = isTypedArray(other);
    }
  }
  var objIsObj = objTag == objectTag,
      othIsObj = othTag == objectTag,
      isSameTag = objTag == othTag;

  if (isSameTag && !(objIsArr || objIsObj)) {
    return equalByTag(object, other, objTag);
  }
  if (!isLoose) {
    var valWrapped = objIsObj && hasOwnProperty.call(object, '__wrapped__'),
        othWrapped = othIsObj && hasOwnProperty.call(other, '__wrapped__');

    if (valWrapped || othWrapped) {
      return equalFunc(valWrapped ? object.value() : object, othWrapped ? other.value() : other, customizer, isLoose, stackA, stackB);
    }
  }
  if (!isSameTag) {
    return false;
  }
  // Assume cyclic values are equal.
  // For more information on detecting circular references see https://es5.github.io/#JO.
  stackA || (stackA = []);
  stackB || (stackB = []);

  var length = stackA.length;
  while (length--) {
    if (stackA[length] == object) {
      return stackB[length] == other;
    }
  }
  // Add `object` and `other` to the stack of traversed objects.
  stackA.push(object);
  stackB.push(other);

  var result = (objIsArr ? equalArrays : equalObjects)(object, other, equalFunc, customizer, isLoose, stackA, stackB);

  stackA.pop();
  stackB.pop();

  return result;
}

/**
 * A specialized version of `baseIsEqualDeep` for arrays with support for
 * partial deep comparisons.
 *
 * @private
 * @param {Array} array The array to compare.
 * @param {Array} other The other array to compare.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Function} [customizer] The function to customize comparing arrays.
 * @param {boolean} [isLoose] Specify performing partial comparisons.
 * @param {Array} [stackA] Tracks traversed `value` objects.
 * @param {Array} [stackB] Tracks traversed `other` objects.
 * @returns {boolean} Returns `true` if the arrays are equivalent, else `false`.
 */
function equalArrays(array, other, equalFunc, customizer, isLoose, stackA, stackB) {
  var index = -1,
      arrLength = array.length,
      othLength = other.length,
      result = true;

  if (arrLength != othLength && !(isLoose && othLength > arrLength)) {
    return false;
  }
  // Deep compare the contents, ignoring non-numeric properties.
  while (result && ++index < arrLength) {
    var arrValue = array[index],
        othValue = other[index];

    result = undefined;
    if (customizer) {
      result = isLoose
        ? customizer(othValue, arrValue, index)
        : customizer(arrValue, othValue, index);
    }
    if (result === undefined) {
      // Recursively compare arrays (susceptible to call stack limits).
      if (isLoose) {
        var othIndex = othLength;
        while (othIndex--) {
          othValue = other[othIndex];
          result = (arrValue && arrValue === othValue) || equalFunc(arrValue, othValue, customizer, isLoose, stackA, stackB);
          if (result) {
            break;
          }
        }
      } else {
        result = (arrValue && arrValue === othValue) || equalFunc(arrValue, othValue, customizer, isLoose, stackA, stackB);
      }
    }
  }
  return !!result;
}

/**
 * A specialized version of `baseIsEqualDeep` for comparing objects of
 * the same `toStringTag`.
 *
 * **Note:** This function only supports comparing values with tags of
 * `Boolean`, `Date`, `Error`, `Number`, `RegExp`, or `String`.
 *
 * @private
 * @param {Object} value The object to compare.
 * @param {Object} other The other object to compare.
 * @param {string} tag The `toStringTag` of the objects to compare.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function equalByTag(object, other, tag) {
  switch (tag) {
    case boolTag:
    case dateTag:
      // Coerce dates and booleans to numbers, dates to milliseconds and booleans
      // to `1` or `0` treating invalid dates coerced to `NaN` as not equal.
      return +object == +other;

    case errorTag:
      return object.name == other.name && object.message == other.message;

    case numberTag:
      // Treat `NaN` vs. `NaN` as equal.
      return (object != +object)
        ? other != +other
        : object == +other;

    case regexpTag:
    case stringTag:
      // Coerce regexes to strings and treat strings primitives and string
      // objects as equal. See https://es5.github.io/#x15.10.6.4 for more details.
      return object == (other + '');
  }
  return false;
}

/**
 * A specialized version of `baseIsEqualDeep` for objects with support for
 * partial deep comparisons.
 *
 * @private
 * @param {Object} object The object to compare.
 * @param {Object} other The other object to compare.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Function} [customizer] The function to customize comparing values.
 * @param {boolean} [isLoose] Specify performing partial comparisons.
 * @param {Array} [stackA] Tracks traversed `value` objects.
 * @param {Array} [stackB] Tracks traversed `other` objects.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function equalObjects(object, other, equalFunc, customizer, isLoose, stackA, stackB) {
  var objProps = keys(object),
      objLength = objProps.length,
      othProps = keys(other),
      othLength = othProps.length;

  if (objLength != othLength && !isLoose) {
    return false;
  }
  var skipCtor = isLoose,
      index = -1;

  while (++index < objLength) {
    var key = objProps[index],
        result = isLoose ? key in other : hasOwnProperty.call(other, key);

    if (result) {
      var objValue = object[key],
          othValue = other[key];

      result = undefined;
      if (customizer) {
        result = isLoose
          ? customizer(othValue, objValue, key)
          : customizer(objValue, othValue, key);
      }
      if (result === undefined) {
        // Recursively compare objects (susceptible to call stack limits).
        result = (objValue && objValue === othValue) || equalFunc(objValue, othValue, customizer, isLoose, stackA, stackB);
      }
    }
    if (!result) {
      return false;
    }
    skipCtor || (skipCtor = key == 'constructor');
  }
  if (!skipCtor) {
    var objCtor = object.constructor,
        othCtor = other.constructor;

    // Non `Object` object instances with different constructors are not equal.
    if (objCtor != othCtor &&
        ('constructor' in object && 'constructor' in other) &&
        !(typeof objCtor == 'function' && objCtor instanceof objCtor &&
          typeof othCtor == 'function' && othCtor instanceof othCtor)) {
      return false;
    }
  }
  return true;
}

module.exports = baseIsEqual;

},{"lodash.isarray":27,"lodash.istypedarray":15,"lodash.keys":17}],15:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    arrayTag = '[object Array]',
    boolTag = '[object Boolean]',
    dateTag = '[object Date]',
    errorTag = '[object Error]',
    funcTag = '[object Function]',
    mapTag = '[object Map]',
    numberTag = '[object Number]',
    objectTag = '[object Object]',
    regexpTag = '[object RegExp]',
    setTag = '[object Set]',
    stringTag = '[object String]',
    weakMapTag = '[object WeakMap]';

var arrayBufferTag = '[object ArrayBuffer]',
    float32Tag = '[object Float32Array]',
    float64Tag = '[object Float64Array]',
    int8Tag = '[object Int8Array]',
    int16Tag = '[object Int16Array]',
    int32Tag = '[object Int32Array]',
    uint8Tag = '[object Uint8Array]',
    uint8ClampedTag = '[object Uint8ClampedArray]',
    uint16Tag = '[object Uint16Array]',
    uint32Tag = '[object Uint32Array]';

/** Used to identify `toStringTag` values of typed arrays. */
var typedArrayTags = {};
typedArrayTags[float32Tag] = typedArrayTags[float64Tag] =
typedArrayTags[int8Tag] = typedArrayTags[int16Tag] =
typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] =
typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] =
typedArrayTags[uint32Tag] = true;
typedArrayTags[argsTag] = typedArrayTags[arrayTag] =
typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] =
typedArrayTags[dateTag] = typedArrayTags[errorTag] =
typedArrayTags[funcTag] = typedArrayTags[mapTag] =
typedArrayTags[numberTag] = typedArrayTags[objectTag] =
typedArrayTags[regexpTag] = typedArrayTags[setTag] =
typedArrayTags[stringTag] = typedArrayTags[weakMapTag] = false;

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as a typed array.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isTypedArray(new Uint8Array);
 * // => true
 *
 * _.isTypedArray([]);
 * // => false
 */
function isTypedArray(value) {
  return isObjectLike(value) && isLength(value.length) && !!typedArrayTags[objToString.call(value)];
}

module.exports = isTypedArray;

},{}],16:[function(require,module,exports){
arguments[4][9][0].apply(exports,arguments)
},{"dup":9}],17:[function(require,module,exports){
arguments[4][5][0].apply(exports,arguments)
},{"dup":5,"lodash.isarguments":18,"lodash.isarray":27,"lodash.isnative":19}],18:[function(require,module,exports){
arguments[4][6][0].apply(exports,arguments)
},{"dup":6}],19:[function(require,module,exports){
arguments[4][4][0].apply(exports,arguments)
},{"dup":4}],20:[function(require,module,exports){
/**
 * lodash 3.0.3 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var keys = require('lodash.keys');

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = Math.pow(2, 53) - 1;

/**
 * The base implementation of `_.forEach` without support for callback
 * shorthands and `this` binding.
 *
 * @private
 * @param {Array|Object|string} collection The collection to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array|Object|string} Returns `collection`.
 */
var baseEach = createBaseEach(baseForOwn);

/**
 * The base implementation of `baseForIn` and `baseForOwn` which iterates
 * over `object` properties returned by `keysFunc` invoking `iteratee` for
 * each property. Iteratee functions may exit iteration early by explicitly
 * returning `false`.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @returns {Object} Returns `object`.
 */
var baseFor = createBaseFor();

/**
 * The base implementation of `_.forOwn` without support for callback
 * shorthands and `this` binding.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Object} Returns `object`.
 */
function baseForOwn(object, iteratee) {
  return baseFor(object, iteratee, keys);
}

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Creates a `baseEach` or `baseEachRight` function.
 *
 * @private
 * @param {Function} eachFunc The function to iterate over a collection.
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseEach(eachFunc, fromRight) {
  return function(collection, iteratee) {
    var length = collection ? getLength(collection) : 0;
    if (!isLength(length)) {
      return eachFunc(collection, iteratee);
    }
    var index = fromRight ? length : -1,
        iterable = toObject(collection);

    while ((fromRight ? index-- : ++index < length)) {
      if (iteratee(iterable[index], index, iterable) === false) {
        break;
      }
    }
    return collection;
  };
}

/**
 * Creates a base function for `_.forIn` or `_.forInRight`.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseFor(fromRight) {
  return function(object, iteratee, keysFunc) {
    var iterable = toObject(object),
        props = keysFunc(object),
        length = props.length,
        index = fromRight ? length : -1;

    while ((fromRight ? index-- : ++index < length)) {
      var key = props[index];
      if (iteratee(iterable[key], key, iterable) === false) {
        break;
      }
    }
    return object;
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * in Safari on iOS 8.1 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Converts `value` to an object if it is not one.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {Object} Returns the object.
 */
function toObject(value) {
  return isObject(value) ? value : Object(value);
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

module.exports = baseEach;

},{"lodash.keys":21}],21:[function(require,module,exports){
arguments[4][5][0].apply(exports,arguments)
},{"dup":5,"lodash.isarguments":22,"lodash.isarray":27,"lodash.isnative":23}],22:[function(require,module,exports){
arguments[4][6][0].apply(exports,arguments)
},{"dup":6}],23:[function(require,module,exports){
arguments[4][4][0].apply(exports,arguments)
},{"dup":4}],24:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * The base implementation of `_.find`, `_.findLast`, `_.findKey`, and `_.findLastKey`,
 * without support for callback shorthands and `this` binding, which iterates
 * over `collection` using the provided `eachFunc`.
 *
 * @private
 * @param {Array|Object|string} collection The collection to search.
 * @param {Function} predicate The function invoked per iteration.
 * @param {Function} eachFunc The function to iterate over `collection`.
 * @param {boolean} [retKey] Specify returning the key of the found element
 *  instead of the element itself.
 * @returns {*} Returns the found element or its key, else `undefined`.
 */
function baseFind(collection, predicate, eachFunc, retKey) {
  var result;
  eachFunc(collection, function(value, key, collection) {
    if (predicate(value, key, collection)) {
      result = retKey ? key : value;
      return false;
    }
  });
  return result;
}

module.exports = baseFind;

},{}],25:[function(require,module,exports){
/**
 * lodash 3.2.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseCallback = require('lodash._basecallback'),
    baseFindIndex = require('lodash._basefindindex'),
    isArray = require('lodash.isarray');

/**
 * Creates a `_.findIndex` or `_.findLastIndex` function.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new find function.
 */
function createFindIndex(fromRight) {
  return function(array, predicate, thisArg) {
    if (!(array && array.length)) {
      return -1;
    }
    predicate = baseCallback(predicate, thisArg, 3);
    return baseFindIndex(array, predicate, fromRight);
  };
}

/**
 * This method is like `_.find` except that it returns the index of the first
 * element `predicate` returns truthy for instead of the element itself.
 *
 * If a property name is provided for `predicate` the created `_.property`
 * style callback returns the property value of the given element.
 *
 * If a value is also provided for `thisArg` the created `_.matchesProperty`
 * style callback returns `true` for elements that have a matching property
 * value, else `false`.
 *
 * If an object is provided for `predicate` the created `_.matches` style
 * callback returns `true` for elements that have the properties of the given
 * object, else `false`.
 *
 * @static
 * @memberOf _
 * @category Array
 * @param {Array} array The array to search.
 * @param {Function|Object|string} [predicate=_.identity] The function invoked
 *  per iteration.
 * @param {*} [thisArg] The `this` binding of `predicate`.
 * @returns {number} Returns the index of the found element, else `-1`.
 * @example
 *
 * var users = [
 *   { 'user': 'barney',  'active': false },
 *   { 'user': 'fred',    'active': false },
 *   { 'user': 'pebbles', 'active': true }
 * ];
 *
 * _.findIndex(users, function(chr) {
 *   return chr.user == 'barney';
 * });
 * // => 0
 *
 * // using the `_.matches` callback shorthand
 * _.findIndex(users, { 'user': 'fred', 'active': false });
 * // => 1
 *
 * // using the `_.matchesProperty` callback shorthand
 * _.findIndex(users, 'active', false);
 * // => 0
 *
 * // using the `_.property` callback shorthand
 * _.findIndex(users, 'active');
 * // => 2
 */
var findIndex = createFindIndex();

module.exports = findIndex;

},{"lodash._basecallback":13,"lodash._basefindindex":26,"lodash.isarray":27}],26:[function(require,module,exports){
/**
 * lodash 3.6.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * The base implementation of `_.findIndex` and `_.findLastIndex` without
 * support for callback shorthands and `this` binding.
 *
 * @private
 * @param {Array} array The array to search.
 * @param {Function} predicate The function invoked per iteration.
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function baseFindIndex(array, predicate, fromRight) {
  var length = array.length,
      index = fromRight ? length : -1;

  while ((fromRight ? index-- : ++index < length)) {
    if (predicate(array[index], index, array)) {
      return index;
    }
  }
  return -1;
}

module.exports = baseFindIndex;

},{}],27:[function(require,module,exports){
arguments[4][7][0].apply(exports,arguments)
},{"dup":7}],28:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var before = require('lodash.before');

/**
 * Creates a function that is restricted to invoking `func` once. Repeat calls
 * to the function return the value of the first call. The `func` is invoked
 * with the `this` binding of the created function.
 *
 * @static
 * @memberOf _
 * @type Function
 * @category Function
 * @param {Function} func The function to restrict.
 * @returns {Function} Returns the new restricted function.
 * @example
 *
 * var initialize = _.once(createApplication);
 * initialize();
 * initialize();
 * // `initialize` invokes `createApplication` once
 */
function once(func) {
  return before(func, 2);
}

module.exports = once;

},{"lodash.before":29}],29:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used as the `TypeError` message for "Functions" methods. */
var FUNC_ERROR_TEXT = 'Expected a function';

/**
 * Creates a function that invokes `func`, with the `this` binding and arguments
 * of the created function, while it is called less than `n` times. Subsequent
 * calls to the created function return the result of the last `func` invocation.
 *
 * @static
 * @memberOf _
 * @category Function
 * @param {number} n The number of calls at which `func` is no longer invoked.
 * @param {Function} func The function to restrict.
 * @returns {Function} Returns the new restricted function.
 * @example
 *
 * jQuery('#add').on('click', _.before(5, addContactToList));
 * // => allows adding up to 4 contacts to the list
 */
function before(n, func) {
  var result;
  if (typeof func != 'function') {
    if (typeof n == 'function') {
      var temp = n;
      n = func;
      func = temp;
    } else {
      throw new TypeError(FUNC_ERROR_TEXT);
    }
  }
  return function() {
    if (--n > 0) {
      result = func.apply(this, arguments);
    }
    if (n <= 1) {
      func = null;
    }
    return result;
  };
}

module.exports = before;

},{}],30:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var isFunction = require('lodash.isfunction');

/**
 * Resolves the value of property `key` on `object`. If the value of `key` is
 * a function it is invoked with the `this` binding of `object` and its result
 * is returned, else the property value is returned. If the property value is
 * `undefined` the `defaultValue` is used in its place.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @param {string} key The key of the property to resolve.
 * @param {*} [defaultValue] The value returned if the property value
 *  resolves to `undefined`.
 * @returns {*} Returns the resolved value.
 * @example
 *
 * var object = { 'user': 'fred', 'age': _.constant(40) };
 *
 * _.result(object, 'user');
 * // => 'fred'
 *
 * _.result(object, 'age');
 * // => 40
 *
 * _.result(object, 'status', 'busy');
 * // => 'busy'
 *
 * _.result(object, 'status', _.constant('busy'));
 * // => 'busy'
 */
function result(object, key, defaultValue) {
  var value = object == null ? undefined : object[key];
  if (typeof value == 'undefined') {
    value = defaultValue;
  }
  return isFunction(value) ? value.call(object) : value;
}

module.exports = result;

},{"lodash.isfunction":31}],31:[function(require,module,exports){
(function (global){
/**
 * lodash 3.0.3 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var funcTag = '[object Function]';

/**
 * Used to match `RegExp` [special characters](http://www.regular-expressions.info/characters.html#special).
 * In addition to special characters the forward slash is escaped to allow for
 * easier `eval` use and `Function` compilation.
 */
var reRegExpChars = /[.*+?^${}()|[\]\/\\]/g,
    reHasRegExpChars = RegExp(reRegExpChars.source);

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * The base implementation of `_.isFunction` without support for environments
 * with incorrect `typeof` results.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 */
function baseIsFunction(value) {
  // Avoid a Chakra JIT bug in compatibility modes of IE 11.
  // See https://github.com/jashkenas/underscore/issues/1621 for more details.
  return typeof value == 'function' || false;
}

/**
 * Converts `value` to a string if it is not one. An empty string is returned
 * for `null` or `undefined` values.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  if (typeof value == 'string') {
    return value;
  }
  return value == null ? '' : (value + '');
}

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/**
 * Used to resolve the [`toStringTag`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  escapeRegExp(objToString)
  .replace(/toString|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/** Native method references. */
var Uint8Array = isNative(Uint8Array = global.Uint8Array) && Uint8Array;

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
var isFunction = !(baseIsFunction(/x/) || (Uint8Array && !baseIsFunction(Uint8Array))) ? baseIsFunction : function(value) {
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in older versions of Chrome and Safari which return 'function' for regexes
  // and Safari 8 equivalents which return 'object' for typed array constructors.
  return objToString.call(value) == funcTag;
};

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (objToString.call(value) == funcTag) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

/**
 * Escapes the `RegExp` special characters "\", "/", "^", "$", ".", "|", "?",
 * "*", "+", "(", ")", "[", "]", "{" and "}" in `string`.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to escape.
 * @returns {string} Returns the escaped string.
 * @example
 *
 * _.escapeRegExp('[lodash](https://lodash.com/)');
 * // => '\[lodash\]\(https:\/\/lodash\.com\/\)'
 */
function escapeRegExp(string) {
  string = baseToString(string);
  return (string && reHasRegExpChars.test(string))
    ? string.replace(reRegExpChars, '\\$&')
    : string;
}

module.exports = isFunction;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],32:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseCallback = require('lodash._basecallback'),
    baseUniq = require('lodash._baseuniq'),
    isIterateeCall = require('lodash._isiterateecall');

/**
 * An implementation of `_.uniq` optimized for sorted arrays without support
 * for callback shorthands and `this` binding.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {Function} [iteratee] The function invoked per iteration.
 * @returns {Array} Returns the new duplicate-value-free array.
 */
function sortedUniq(array, iteratee) {
  var seen,
      index = -1,
      length = array.length,
      resIndex = -1,
      result = [];

  while (++index < length) {
    var value = array[index],
        computed = iteratee ? iteratee(value, index, array) : value;

    if (!index || seen !== computed) {
      seen = computed;
      result[++resIndex] = value;
    }
  }
  return result;
}

/**
 * Creates a duplicate-value-free version of an array using `SameValueZero`
 * for equality comparisons. Providing `true` for `isSorted` performs a faster
 * search algorithm for sorted arrays. If an iteratee function is provided it
 * is invoked for each value in the array to generate the criterion by which
 * uniqueness is computed. The `iteratee` is bound to `thisArg` and invoked
 * with three arguments; (value, index, array).
 *
 * If a property name is provided for `predicate` the created "_.property"
 * style callback returns the property value of the given element.
 *
 * If an object is provided for `predicate` the created "_.matches" style
 * callback returns `true` for elements that have the properties of the given
 * object, else `false`.
 *
 * **Note:** `SameValueZero` comparisons are like strict equality comparisons,
 * e.g. `===`, except that `NaN` matches `NaN`. See the
 * [ES spec](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-samevaluezero)
 * for more details.
 *
 * @static
 * @memberOf _
 * @alias unique
 * @category Array
 * @param {Array} array The array to inspect.
 * @param {boolean} [isSorted] Specify the array is sorted.
 * @param {Function|Object|string} [iteratee] The function invoked per iteration.
 *  If a property name or object is provided it is used to create a "_.property"
 *  or "_.matches" style callback respectively.
 * @param {*} [thisArg] The `this` binding of `iteratee`.
 * @returns {Array} Returns the new duplicate-value-free array.
 * @example
 *
 * _.uniq([1, 2, 1]);
 * // => [1, 2]
 *
 * // using `isSorted`
 * _.uniq([1, 1, 2], true);
 * // => [1, 2]
 *
 * // using an iteratee function
 * _.uniq([1, 2.5, 1.5, 2], function(n) { return this.floor(n); }, Math);
 * // => [1, 2.5]
 *
 * // using the "_.property" callback shorthand
 * _.uniq([{ 'x': 1 }, { 'x': 2 }, { 'x': 1 }], 'x');
 * // => [{ 'x': 1 }, { 'x': 2 }]
 */
function uniq(array, isSorted, iteratee, thisArg) {
  var length = array ? array.length : 0;
  if (!length) {
    return [];
  }
  // Juggle arguments.
  if (typeof isSorted != 'boolean' && isSorted != null) {
    thisArg = iteratee;
    iteratee = isIterateeCall(array, isSorted, thisArg) ? null : isSorted;
    isSorted = false;
  }
  iteratee = iteratee == null ? iteratee : baseCallback(iteratee, thisArg, 3);
  return (isSorted)
    ? sortedUniq(array, iteratee)
    : baseUniq(array, iteratee);
}

module.exports = uniq;

},{"lodash._basecallback":33,"lodash._baseuniq":41,"lodash._isiterateecall":46}],33:[function(require,module,exports){
arguments[4][13][0].apply(exports,arguments)
},{"dup":13,"lodash._baseisequal":34,"lodash._bindcallback":36,"lodash.isarray":37,"lodash.keys":38}],34:[function(require,module,exports){
arguments[4][14][0].apply(exports,arguments)
},{"dup":14,"lodash.isarray":37,"lodash.istypedarray":35,"lodash.keys":38}],35:[function(require,module,exports){
arguments[4][15][0].apply(exports,arguments)
},{"dup":15}],36:[function(require,module,exports){
arguments[4][9][0].apply(exports,arguments)
},{"dup":9}],37:[function(require,module,exports){
arguments[4][7][0].apply(exports,arguments)
},{"dup":7}],38:[function(require,module,exports){
arguments[4][5][0].apply(exports,arguments)
},{"dup":5,"lodash.isarguments":39,"lodash.isarray":37,"lodash.isnative":40}],39:[function(require,module,exports){
arguments[4][6][0].apply(exports,arguments)
},{"dup":6}],40:[function(require,module,exports){
arguments[4][4][0].apply(exports,arguments)
},{"dup":4}],41:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseIndexOf = require('lodash._baseindexof'),
    cacheIndexOf = require('lodash._cacheindexof'),
    createCache = require('lodash._createcache');

/**
 * The base implementation of `_.uniq` without support for callback shorthands
 * and `this` binding.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {Function} [iteratee] The function invoked per iteration.
 * @returns {Array} Returns the new duplicate-value-free array.
 */
function baseUniq(array, iteratee) {
  var index = -1,
      indexOf = baseIndexOf,
      length = array.length,
      isCommon = true,
      isLarge = isCommon && length >= 200,
      seen = isLarge ? createCache() : null,
      result = [];

  if (seen) {
    indexOf = cacheIndexOf;
    isCommon = false;
  } else {
    isLarge = false;
    seen = iteratee ? [] : result;
  }
  outer:
  while (++index < length) {
    var value = array[index],
        computed = iteratee ? iteratee(value, index, array) : value;

    if (isCommon && value === value) {
      var seenIndex = seen.length;
      while (seenIndex--) {
        if (seen[seenIndex] === computed) {
          continue outer;
        }
      }
      if (iteratee) {
        seen.push(computed);
      }
      result.push(value);
    }
    else if (indexOf(seen, computed, 0) < 0) {
      if (iteratee || isLarge) {
        seen.push(computed);
      }
      result.push(value);
    }
  }
  return result;
}

module.exports = baseUniq;

},{"lodash._baseindexof":42,"lodash._cacheindexof":43,"lodash._createcache":44}],42:[function(require,module,exports){
/**
 * lodash 3.1.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * The base implementation of `_.indexOf` without support for binary searches.
 *
 * @private
 * @param {Array} array The array to search.
 * @param {*} value The value to search for.
 * @param {number} fromIndex The index to search from.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function baseIndexOf(array, value, fromIndex) {
  if (value !== value) {
    return indexOfNaN(array, fromIndex);
  }
  var index = fromIndex - 1,
      length = array.length;

  while (++index < length) {
    if (array[index] === value) {
      return index;
    }
  }
  return -1;
}

/**
 * Gets the index at which the first occurrence of `NaN` is found in `array`.
 * If `fromRight` is provided elements of `array` are iterated from right to left.
 *
 * @private
 * @param {Array} array The array to search.
 * @param {number} fromIndex The index to search from.
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {number} Returns the index of the matched `NaN`, else `-1`.
 */
function indexOfNaN(array, fromIndex, fromRight) {
  var length = array.length,
      index = fromIndex + (fromRight ? 0 : -1);

  while ((fromRight ? index-- : ++index < length)) {
    var other = array[index];
    if (other !== other) {
      return index;
    }
  }
  return -1;
}

module.exports = baseIndexOf;

},{}],43:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Checks if `value` is in `cache` mimicking the return signature of
 * `_.indexOf` by returning `0` if the value is found, else `-1`.
 *
 * @private
 * @param {Object} cache The cache to search.
 * @param {*} value The value to search for.
 * @returns {number} Returns `0` if `value` is found, else `-1`.
 */
function cacheIndexOf(cache, value) {
  var data = cache.data,
      result = (typeof value == 'string' || isObject(value)) ? data.set.has(value) : data.hash[value];

  return result ? 0 : -1;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

module.exports = cacheIndexOf;

},{}],44:[function(require,module,exports){
(function (global){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.2 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var isNative = require('lodash.isnative');

/** Native method references. */
var Set = isNative(Set = global.Set) && Set;

/* Native method references for those with the same name as other `lodash` methods. */
var nativeCreate = isNative(nativeCreate = Object.create) && nativeCreate;

/**
 *
 * Creates a cache object to store unique values.
 *
 * @private
 * @param {Array} [values] The values to cache.
 */
function SetCache(values) {
  var length = values ? values.length : 0;

  this.data = { 'hash': nativeCreate(null), 'set': new Set };
  while (length--) {
    this.push(values[length]);
  }
}

/**
 * Adds `value` to the cache.
 *
 * @private
 * @name push
 * @memberOf SetCache
 * @param {*} value The value to cache.
 */
function cachePush(value) {
  var data = this.data;
  if (typeof value == 'string' || isObject(value)) {
    data.set.add(value);
  } else {
    data.hash[value] = true;
  }
}

/**
 * Creates a `Set` cache object to optimize linear searches of large arrays.
 *
 * @private
 * @param {Array} [values] The values to cache.
 * @returns {null|Object} Returns the new cache object if `Set` is supported, else `null`.
 */
var createCache = !(nativeCreate && Set) ? constant(null) : function(values) {
  return new SetCache(values);
};

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return type == 'function' || (!!value && type == 'object');
}

/**
 * Creates a function that returns `value`.
 *
 * @static
 * @memberOf _
 * @category Utility
 * @param {*} value The value to return from the new function.
 * @returns {Function} Returns the new function.
 * @example
 *
 * var object = { 'user': 'fred' };
 * var getter = _.constant(object);
 *
 * getter() === object;
 * // => true
 */
function constant(value) {
  return function() {
    return value;
  };
}

// Add functions to the `Set` cache.
SetCache.prototype.push = cachePush;

module.exports = createCache;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"lodash.isnative":45}],45:[function(require,module,exports){
arguments[4][4][0].apply(exports,arguments)
},{"dup":4}],46:[function(require,module,exports){
arguments[4][10][0].apply(exports,arguments)
},{"dup":10}],47:[function(require,module,exports){
//This file is generated by bin/hook.js
"use strict";

var template = require("./template");
module.exports = { media_control: template("<div class=\"media-control-background\" data-background></div><div class=\"media-control-layer\" data-controls><% var renderBar=function(name) { %><div class=\"bar-container\" data-<%= name %>><div class=\"bar-background\" data-<%= name %>><div class=\"bar-fill-1\" data-<%= name %>></div><div class=\"bar-fill-2\" data-<%= name %>></div><div class=\"bar-hover\" data-<%= name %>></div></div><div class=\"bar-scrubber\" data-<%= name %>><div class=\"bar-scrubber-icon\" data-<%= name %>></div></div></div><% }; %><% var renderSegmentedBar=function(name, segments) { segments=segments || 10; %><div class=\"bar-container\" data-<%= name %>><% for (var i = 0; i < segments; i++) { %><div class=\"segmented-bar-element\" data-<%= name %>></div><% } %></div><% }; %><% var renderDrawer=function(name, renderContent) { %><div class=\"drawer-container\" data-<%= name %>><div class=\"drawer-icon-container\" data-<%= name %>><div class=\"drawer-icon media-control-icon\" data-<%= name %>></div><span class=\"drawer-text\" data-<%= name %>></span></div><% renderContent(name); %></div><% }; %><% var renderIndicator=function(name) { %><div class=\"media-control-indicator\" data-<%= name %>></div><% }; %><% var renderButton=function(name) { %><button class=\"media-control-button media-control-icon\" data-<%= name %>></button><% }; %><% var templates={ bar: renderBar, segmentedBar: renderSegmentedBar, }; var render=function(settingsList) { settingsList.forEach(function(setting) { if(setting === \"seekbar\") { renderBar(setting); } else if (setting === \"volume\") { renderDrawer(setting, settings.volumeBarTemplate ? templates[settings.volumeBarTemplate] : function(name) { return renderSegmentedBar(name); }); } else if (setting === \"duration\" || setting=== \"position\") { renderIndicator(setting); } else { renderButton(setting); } }); }; %><% if (settings.default && settings.default.length) { %><div class=\"media-control-center-panel\" data-media-control><% render(settings.default); %></div><% } %><% if (settings.left && settings.left.length) { %><div class=\"media-control-left-panel\" data-media-control><% render(settings.left); %></div><% } %><% if (settings.right && settings.right.length) { %><div class=\"media-control-right-panel\" data-media-control><% render(settings.right); %></div><% } %></div>"), seek_time: template("<span data-seek-time></span>"), flash: template("<param name=\"movie\" value=\"<%= baseUrl %>/assets/Player.swf\"><param name=\"quality\" value=\"autohigh\"><param name=\"swliveconnect\" value=\"true\"><param name=\"allowScriptAccess\" value=\"always\"><param name=\"bgcolor\" value=\"#001122\"><param name=\"allowFullScreen\" value=\"false\"><param name=\"wmode\" value=\"transparent\"><param name=\"tabindex\" value=\"1\"><param name=FlashVars value=\"playbackId=<%= playbackId %>\" /><embed type=\"application/x-shockwave-flash\" disabled tabindex=\"-1\" enablecontextmenu=\"false\" allowScriptAccess=\"always\" quality=\"autohight\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" wmode=\"transparent\" swliveconnect=\"true\" type=\"application/x-shockwave-flash\" allowfullscreen=\"false\" bgcolor=\"#000000\" FlashVars=\"playbackId=<%= playbackId %>\" src=\"<%= baseUrl %>/assets/Player.swf\"></embed>"), hls: template("<param name=\"movie\" value=\"<%= baseUrl %>/assets/HLSPlayer.swf?inline=1\"><param name=\"quality\" value=\"autohigh\"><param name=\"swliveconnect\" value=\"true\"><param name=\"allowScriptAccess\" value=\"always\"><param name=\"bgcolor\" value=\"#001122\"><param name=\"allowFullScreen\" value=\"false\"><param name=\"wmode\" value=\"transparent\"><param name=\"tabindex\" value=\"1\"><param name=FlashVars value=\"playbackId=<%= playbackId %>&callback=<%= callbackName %>\" /><embed type=\"application/x-shockwave-flash\" tabindex=\"1\" enablecontextmenu=\"false\" allowScriptAccess=\"always\" quality=\"autohigh\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" wmode=\"transparent\" swliveconnect=\"true\" type=\"application/x-shockwave-flash\" allowfullscreen=\"false\" bgcolor=\"#000000\" FlashVars=\"playbackId=<%= playbackId %>&callback=<%= callbackName %>\" src=\"<%= baseUrl %>/assets/HLSPlayer.swf\" width=\"100%\" height=\"100%\"></embed>"), html5_video: template("<source src=\"<%=src%>\" type=\"<%=type%>\">"), no_op: template("<canvas data-no-op-canvas></canvas><p data-no-op-msg>Your browser does not support the playback of this video. Try to use a different browser.<p>"), dvr_controls: template("<div class=\"live-info\">LIVE</div><button class=\"live-button\">BACK TO LIVE</button>"), poster: template("<div class=\"play-wrapper\" data-poster><span class=\"poster-icon play\" data-poster/></div>"), spinner_three_bounce: template("<div data-bounce1></div><div data-bounce2></div><div data-bounce3></div>"), watermark: template("<div data-watermark data-watermark-<%=position %>><img src=\"<%= imageUrl %>\"></div>"), CSS: { container: ".container[data-container]{position:absolute;background-color:#000;height:100%;width:100%}.container[data-container].pointer-enabled{cursor:pointer}", core: "[data-player]{-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;-o-user-select:none;user-select:none;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;-webkit-transform:translate3d(0,0,0);-moz-transform:translate3d(0,0,0);-ms-transform:translate3d(0,0,0);-o-transform:translate3d(0,0,0);transform:translate3d(0,0,0);position:relative;margin:0;padding:0;border:0;font-style:normal;font-weight:400;text-align:center;overflow:hidden;font-size:100%;font-family:\"lucida grande\",tahoma,verdana,arial,sans-serif;text-shadow:0 0 0;box-sizing:border-box}[data-player] a,[data-player] abbr,[data-player] acronym,[data-player] address,[data-player] applet,[data-player] article,[data-player] aside,[data-player] audio,[data-player] b,[data-player] big,[data-player] blockquote,[data-player] canvas,[data-player] caption,[data-player] center,[data-player] cite,[data-player] code,[data-player] dd,[data-player] del,[data-player] details,[data-player] dfn,[data-player] div,[data-player] dl,[data-player] dt,[data-player] em,[data-player] embed,[data-player] fieldset,[data-player] figcaption,[data-player] figure,[data-player] footer,[data-player] form,[data-player] h1,[data-player] h2,[data-player] h3,[data-player] h4,[data-player] h5,[data-player] h6,[data-player] header,[data-player] hgroup,[data-player] i,[data-player] iframe,[data-player] img,[data-player] ins,[data-player] kbd,[data-player] label,[data-player] legend,[data-player] li,[data-player] mark,[data-player] menu,[data-player] nav,[data-player] object,[data-player] ol,[data-player] output,[data-player] p,[data-player] pre,[data-player] q,[data-player] ruby,[data-player] s,[data-player] samp,[data-player] section,[data-player] small,[data-player] span,[data-player] strike,[data-player] strong,[data-player] sub,[data-player] summary,[data-player] sup,[data-player] table,[data-player] tbody,[data-player] td,[data-player] tfoot,[data-player] th,[data-player] thead,[data-player] time,[data-player] tr,[data-player] tt,[data-player] u,[data-player] ul,[data-player] var,[data-player] video{margin:0;padding:0;border:0;font:inherit;font-size:100%;vertical-align:baseline}[data-player] table{border-collapse:collapse;border-spacing:0}[data-player] caption,[data-player] td,[data-player] th{text-align:left;font-weight:400;vertical-align:middle}[data-player] blockquote,[data-player] q{quotes:none}[data-player] blockquote:after,[data-player] blockquote:before,[data-player] q:after,[data-player] q:before{content:\"\";content:none}[data-player] a img{border:none}[data-player]:focus{outline:0}[data-player] *{max-width:initial;box-sizing:inherit;float:initial}[data-player].fullscreen{width:100%!important;height:100%!important}[data-player].nocursor{cursor:none}.clappr-style{display:none!important}@media screen{[data-player]{opacity:.99}}", media_control: "@font-face{font-family:Player;src:url(\"<%= baseUrl %>/assets/Player-Regular.eot\");src:url(\"<%= baseUrl %>/assets/Player-Regular.eot?#iefix\") format(\"embedded-opentype\"),url(\"<%= baseUrl %>/assets/Player-Regular.ttf\") format(\"truetype\"),url(\"<%= baseUrl %>/assets/Player-Regular.svg#player\") format(\"svg\")}.media-control-notransition{-webkit-transition:none!important;-webkit-transition-delay:0s;-moz-transition:none!important;-o-transition:none!important;transition:none!important}.media-control[data-media-control]{position:absolute;width:100%;height:100%;z-index:9999;pointer-events:none}.media-control[data-media-control].dragging{pointer-events:auto;cursor:-webkit-grabbing!important;cursor:grabbing!important}.media-control[data-media-control].dragging *{cursor:-webkit-grabbing!important;cursor:grabbing!important}.media-control[data-media-control] .media-control-background[data-background]{position:absolute;height:40%;width:100%;bottom:0;background-image:-owg(linear-gradient(rgba(0,0,0,0),rgba(0,0,0,.9)));background-image:-webkit(linear-gradient(rgba(0,0,0,0),rgba(0,0,0,.9)));background-image:-moz(linear-gradient(rgba(0,0,0,0),rgba(0,0,0,.9)));background-image:-o(linear-gradient(rgba(0,0,0,0),rgba(0,0,0,.9)));background-image:linear-gradient(rgba(0,0,0,0),rgba(0,0,0,.9));-webkit-transition:opacity .6s;-webkit-transition-delay:ease-out;-moz-transition:opacity .6s ease-out;-o-transition:opacity .6s ease-out;transition:opacity .6s ease-out}.media-control[data-media-control] .media-control-icon{font-family:Player;font-weight:400;font-style:normal;font-size:26px;line-height:32px;letter-spacing:0;speak:none;color:#fff;opacity:.5;vertical-align:middle;text-align:left;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;-webkit-transition:all .1s;-webkit-transition-delay:ease;-moz-transition:all .1s ease;-o-transition:all .1s ease;transition:all .1s ease}.media-control[data-media-control] .media-control-icon:hover{color:#fff;opacity:.75;text-shadow:rgba(255,255,255,.8) 0 0 5px}.media-control[data-media-control].media-control-hide .media-control-background[data-background]{opacity:0}.media-control[data-media-control].media-control-hide .media-control-layer[data-controls]{bottom:-50px}.media-control[data-media-control].media-control-hide .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-scrubber[data-seekbar]{opacity:0}.media-control[data-media-control] .media-control-layer[data-controls]{position:absolute;bottom:7px;width:100%;height:32px;vertical-align:middle;pointer-events:auto;-webkit-transition:bottom .4s;-webkit-transition-delay:ease-out;-moz-transition:bottom .4s ease-out;-o-transition:bottom .4s ease-out;transition:bottom .4s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-left-panel[data-media-control]{position:absolute;top:0;left:4px;height:100%}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-center-panel[data-media-control]{height:100%;text-align:center;line-height:32px}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-right-panel[data-media-control]{position:absolute;top:0;right:4px;height:100%}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button{background-color:transparent;border:0;margin:0 6px;padding:0;cursor:pointer;display:inline-block}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button:focus{outline:0}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-play]{float:left;height:100%;font-size:20px}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-play]:before{content:\"\\e001\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-pause]{float:left;height:100%;font-size:20px}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-pause]:before{content:\"\\e002\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-stop]{float:left;height:100%;font-size:20px}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-stop]:before{content:\"\\e003\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-fullscreen]{float:right;background-color:transparent;border:0;height:100%}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-fullscreen]:before{content:\"\\e006\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-fullscreen].shrink:before{content:\"\\e007\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-hd-indicator]{cursor:default;float:right;background-color:transparent;border:0;height:100%;opacity:0}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-hd-indicator]:before{content:\"\\e008\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-hd-indicator].enabled{opacity:1}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-hd-indicator].enabled:hover{opacity:1;text-shadow:none}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playpause]{float:left;height:100%;font-size:20px}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playpause]:before{content:\"\\e001\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playpause].playing:before{content:\"\\e002\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playpause].paused:before{content:\"\\e001\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playstop]{float:left;height:100%;font-size:20px}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playstop]:before{content:\"\\e001\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playstop].playing:before{content:\"\\e003\"}.media-control[data-media-control] .media-control-layer[data-controls] button.media-control-button[data-playstop].stopped:before{content:\"\\e001\"}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-indicator[data-duration],.media-control[data-media-control] .media-control-layer[data-controls] .media-control-indicator[data-position]{display:inline-block;font-size:10px;color:#fff;cursor:default;line-height:32px;position:relative}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-indicator[data-position]{margin-left:6px}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-indicator[data-duration]{color:rgba(255,255,255,.5);margin-right:6px}.media-control[data-media-control] .media-control-layer[data-controls] .media-control-indicator[data-duration]:before{content:\"|\";margin:0 3px}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar]{position:absolute;top:-20px;left:0;display:inline-block;vertical-align:middle;width:100%;height:25px;cursor:pointer}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar]{width:100%;height:1px;position:relative;top:12px;background-color:#666}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar] .bar-fill-1[data-seekbar]{position:absolute;top:0;left:0;width:0;height:100%;background-color:#c2c2c2;-webkit-transition:all .1s;-webkit-transition-delay:ease-out;-moz-transition:all .1s ease-out;-o-transition:all .1s ease-out;transition:all .1s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar] .bar-fill-2[data-seekbar]{position:absolute;top:0;left:0;width:0;height:100%;background-color:#005aff;-webkit-transition:all .1s;-webkit-transition-delay:ease-out;-moz-transition:all .1s ease-out;-o-transition:all .1s ease-out;transition:all .1s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar] .bar-hover[data-seekbar]{opacity:0;position:absolute;top:-3px;width:5px;height:7px;background-color:rgba(255,255,255,.5);-webkit-transition:opacity .1s;-webkit-transition-delay:ease;-moz-transition:opacity .1s ease;-o-transition:opacity .1s ease;transition:opacity .1s ease}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar]:hover .bar-background[data-seekbar] .bar-hover[data-seekbar]{opacity:1}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar].seek-disabled{cursor:default}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar].seek-disabled:hover .bar-background[data-seekbar] .bar-hover[data-seekbar]{opacity:0}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-scrubber[data-seekbar]{position:absolute;top:2px;left:0;width:20px;height:20px;opacity:1;-webkit-transition:all .1s;-webkit-transition-delay:ease-out;-moz-transition:all .1s ease-out;-o-transition:all .1s ease-out;transition:all .1s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-scrubber[data-seekbar] .bar-scrubber-icon[data-seekbar]{position:absolute;left:6px;top:6px;width:8px;height:8px;border-radius:10px;box-shadow:0 0 0 6px rgba(255,255,255,.2);background-color:#fff}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume]{float:right;display:inline-block;height:32px;cursor:pointer;margin:0 6px;box-sizing:border-box}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume]{float:left;bottom:0}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume]{background-color:transparent;border:0;box-sizing:content-box;width:16px;height:32px;margin-right:6px;opacity:1}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume]:hover{opacity:1}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume]:before{content:\"\\e004\"}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume].muted{opacity:.5}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume].muted:hover{opacity:.7}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .drawer-icon-container[data-volume] .drawer-icon[data-volume].muted:before{content:\"\\e005\"}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume]{float:left;position:relative;top:6px;width:42px;height:18px;padding:3px 0;overflow:hidden;-webkit-transition:width .2s;-webkit-transition-delay:ease-out;-moz-transition:width .2s ease-out;-o-transition:width .2s ease-out;transition:width .2s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume] .segmented-bar-element[data-volume]{float:left;width:4px;padding-left:2px;height:12px;opacity:.5;-webkit-box-shadow:inset 2px 0 0 #fff;-moz-box-shadow:inset 2px 0 0 #fff;-ms-box-shadow:inset 2px 0 0 #fff;-o-box-shadow:inset 2px 0 0 #fff;box-shadow:inset 2px 0 0 #fff;-webkit-transition:-webkit-transform .2s;-webkit-transition-delay:ease-out;-moz-transition:-moz-transform .2s ease-out;-o-transition:-o-transform .2s ease-out;transition:transform .2s ease-out}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume] .segmented-bar-element[data-volume].fill{-webkit-box-shadow:inset 2px 0 0 #fff;-moz-box-shadow:inset 2px 0 0 #fff;-ms-box-shadow:inset 2px 0 0 #fff;-o-box-shadow:inset 2px 0 0 #fff;box-shadow:inset 2px 0 0 #fff;opacity:1}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume] .segmented-bar-element[data-volume]:nth-of-type(1){padding-left:0}.media-control[data-media-control] .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume] .segmented-bar-element[data-volume]:hover{-webkit-transform:scaleY(1.5);-moz-transform:scaleY(1.5);-ms-transform:scaleY(1.5);-o-transform:scaleY(1.5);transform:scaleY(1.5)}.media-control[data-media-control].w320 .media-control-layer[data-controls] .drawer-container[data-volume] .bar-container[data-volume].volume-bar-hide{height:12px;top:9px;padding:0;width:0}", seek_time: ".seek-time[data-seek-time]{position:absolute;width:auto;height:20px;line-height:20px;bottom:55px;background-color:rgba(2,2,2,.5);z-index:9999;-webkit-transition:opacity .1s;-webkit-transition-delay:ease;-moz-transition:opacity .1s ease;-o-transition:opacity .1s ease;transition:opacity .1s ease}.seek-time[data-seek-time].hidden[data-seek-time]{opacity:0}.seek-time[data-seek-time] span[data-seek-time]{position:relative;color:#fff;font-size:10px;padding-left:7px;padding-right:7px}", flash: "[data-flash]{position:absolute;height:100%;width:100%;background-color:#000;display:block;pointer-events:none}", hls: "[data-hls]{position:absolute;display:block;pointer-events:none;top:0}", html_img: "[data-html-img]{max-width:100%;max-height:100%}", html5_video: "[data-html5-video]{position:absolute;height:100%;width:100%;display:block}", no_op: "[data-no-op]{z-index:1000;position:absolute;height:100%;width:100%;text-align:center}[data-no-op] p[data-no-op-msg]{position:absolute;font-size:25px;top:40%;color:#fff}[data-no-op] canvas[data-no-op-canvas]{background-color:#777;height:100%;width:100%}", dvr_controls: "@font-face{font-family:Roboto;font-style:normal;font-weight:400;src:local(\"Roboto\"),local(\"Roboto-Regular\"),url(\"<%= baseUrl %>/assets/Roboto.ttf\") format(\"truetype\")}.dvr-controls[data-dvr-controls]{display:inline-block;float:left;color:#fff;line-height:32px;font-size:10px;font-weight:700;margin-left:6px}.dvr-controls[data-dvr-controls] .live-info{cursor:default;font-family:Roboto,\"Open Sans\",Arial,sans-serif}.dvr-controls[data-dvr-controls] .live-info:before{content:\"\";display:inline-block;position:relative;width:7px;height:7px;border-radius:3.5px;margin-right:3.5px;background-color:#ff0101}.dvr-controls[data-dvr-controls] .live-info.disabled{opacity:.3}.dvr-controls[data-dvr-controls] .live-info.disabled:before{background-color:#fff}.dvr-controls[data-dvr-controls] .live-button{cursor:pointer;outline:0;display:none;border:0;color:#fff;background-color:transparent;height:32px;padding:0;opacity:.7;font-family:Roboto,\"Open Sans\",Arial,sans-serif;-webkit-transition:all .1s;-webkit-transition-delay:ease;-moz-transition:all .1s ease;-o-transition:all .1s ease;transition:all .1s ease}.dvr-controls[data-dvr-controls] .live-button:before{content:\"\";display:inline-block;position:relative;width:7px;height:7px;border-radius:3.5px;margin-right:3.5px;background-color:#fff}.dvr-controls[data-dvr-controls] .live-button:hover{opacity:1;text-shadow:rgba(255,255,255,.75) 0 0 5px}.dvr .dvr-controls[data-dvr-controls] .live-info{display:none}.dvr .dvr-controls[data-dvr-controls] .live-button{display:block}.dvr.media-control.live[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar] .bar-fill-2[data-seekbar]{background-color:#005aff}.media-control.live[data-media-control] .media-control-layer[data-controls] .bar-container[data-seekbar] .bar-background[data-seekbar] .bar-fill-2[data-seekbar]{background-color:#ff0101}.seek-time[data-seek-time] span[data-duration]{position:relative;color:rgba(255,255,255,.5);font-size:10px;padding-right:7px}.seek-time[data-seek-time] span[data-duration]:before{content:\"|\";margin-right:7px}", poster: "@font-face{font-family:Player;src:url(\"<%= baseUrl %>/assets/Player-Regular.eot\");src:url(\"<%= baseUrl %>/assets/Player-Regular.eot?#iefix\") format(\"embedded-opentype\"),url(\"<%= baseUrl %>/assets/Player-Regular.ttf\") format(\"truetype\"),url(\"<%= baseUrl %>/assets/Player-Regular.svg#player\") format(\"svg\")}.player-poster[data-poster]{cursor:pointer;position:absolute;height:100%;width:100%;z-index:998;top:0}.player-poster[data-poster] .poster-background[data-poster]{width:100%;height:100%;background-size:cover;background-repeat:no-repeat;background-position:50% 50%}.player-poster[data-poster] .play-wrapper[data-poster]{position:absolute;width:100%;height:25%;line-height:100%;font-size:25%;top:50%;text-align:center}.player-poster[data-poster] .play-wrapper[data-poster] .poster-icon[data-poster]{font-family:Player;font-weight:400;font-style:normal;line-height:1;letter-spacing:0;speak:none;color:#fff;opacity:.75;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;-webkit-transition:opacity text-shadow;-webkit-transition-delay:.1s;-moz-transition:opacity text-shadow .1s;-o-transition:opacity text-shadow .1s;transition:opacity text-shadow .1s ease}.player-poster[data-poster] .play-wrapper[data-poster] .poster-icon[data-poster].play[data-poster]:before{content:\"\\e001\"}.player-poster[data-poster] .play-wrapper[data-poster] .poster-icon[data-poster]:hover{opacity:1;text-shadow:rgba(255,255,255,.8) 0 0 15px}", spinner_three_bounce: ".spinner-three-bounce[data-spinner]{position:absolute;margin:0 auto;width:70px;text-align:center;z-index:999;top:47%;left:0;right:0}.spinner-three-bounce[data-spinner]>div{width:18px;height:18px;background-color:#FFF;border-radius:100%;display:inline-block;-webkit-animation:bouncedelay 1.4s infinite ease-in-out;-moz-animation:bouncedelay 1.4s infinite ease-in-out;-ms-animation:bouncedelay 1.4s infinite ease-in-out;-o-animation:bouncedelay 1.4s infinite ease-in-out;animation:bouncedelay 1.4s infinite ease-in-out;-webkit-animation-fill-mode:both;-moz-animation-fill-mode:both;-ms-animation-fill-mode:both;-o-animation-fill-mode:both;animation-fill-mode:both}.spinner-three-bounce[data-spinner] [data-bounce1],.spinner-three-bounce[data-spinner] [data-bounce2]{-webkit-animation-delay:-.32s;-moz-animation-delay:-.32s;-ms-animation-delay:-.32s;-o-animation-delay:-.32s;animation-delay:-.32s}@-moz-keyframes bouncedelay{0%,100%,80%{-moz-transform:scale(0);transform:scale(0)}40%{-moz-transform:scale(1);transform:scale(1)}}@-webkit-keyframes bouncedelay{0%,100%,80%{-webkit-transform:scale(0);transform:scale(0)}40%{-webkit-transform:scale(1);transform:scale(1)}}@-o-keyframes bouncedelay{0%,100%,80%{-o-transform:scale(0);transform:scale(0)}40%{-o-transform:scale(1);transform:scale(1)}}@-ms-keyframes bouncedelay{0%,100%,80%{-ms-transform:scale(0);transform:scale(0)}40%{-ms-transform:scale(1);transform:scale(1)}}@keyframes bouncedelay{0%,100%,80%{transform:scale(0)}40%{transform:scale(1)}}", watermark: "[data-watermark]{position:absolute;margin:100px auto 0;width:70px;text-align:center;z-index:10}[data-watermark-bottom-left]{bottom:10px;left:10px}[data-watermark-bottom-right]{bottom:10px;right:42px}[data-watermark-top-left]{top:-95px;left:10px}[data-watermark-top-right]{top:-95px;right:37px}" } };

},{"./template":"template"}],48:[function(require,module,exports){
// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

"use strict";

var $ = require("clappr-zepto");
var template = require("./template");
var JST = require("./jst");

var Styler = {
  getStyleFor: function getStyleFor(name) {
    var options = arguments[1] === undefined ? { baseUrl: "" } : arguments[1];

    return $("<style class=\"clappr-style\"></style>").html(template(JST.CSS[name])(options));
  }
};

module.exports = Styler;

},{"./jst":47,"./template":"template","clappr-zepto":"zepto"}],49:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var assign = require("lodash.assign");
var Browser = require("../components/browser");

var extend = function extend(parent, properties) {
  var constructor = function constructor() {
    parent.prototype.constructor.apply(this, arguments);
    if (properties.constructor) {
      properties.constructor.apply(this, arguments);
    }
  };
  constructor.prototype = Object.create(parent.prototype);
  assign(constructor.prototype, properties);
  return constructor;
};

var formatTime = function formatTime(time) {
  if (!isFinite(time)) {
    return "--:--";
  }
  time = time * 1000;
  time = parseInt(time / 1000);
  var seconds = time % 60;
  time = parseInt(time / 60);
  var minutes = time % 60;
  time = parseInt(time / 60);
  var hours = time % 24;
  var out = "";
  if (hours && hours > 0) out += ("0" + hours).slice(-2) + ":";
  out += ("0" + minutes).slice(-2) + ":";
  out += ("0" + seconds).slice(-2);
  return out.trim();
};

var Fullscreen = {
  isFullscreen: function isFullscreen() {
    return document.webkitFullscreenElement || document.webkitIsFullScreen || document.mozFullScreen || !!document.msFullscreenElement;
  },
  requestFullscreen: function requestFullscreen(el) {
    if (el.requestFullscreen) {
      el.requestFullscreen();
    } else if (el.webkitRequestFullscreen) {
      el.webkitRequestFullscreen();
    } else if (el.mozRequestFullScreen) {
      el.mozRequestFullScreen();
    } else if (el.msRequestFullscreen) {
      el.msRequestFullscreen();
    } else if (el.querySelector && el.querySelector("video") && el.querySelector("video").webkitEnterFullScreen) {
      el.querySelector("video").webkitEnterFullScreen();
    }
  },
  cancelFullscreen: function cancelFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  }
};

var Config = (function () {
  function Config() {
    _classCallCheck(this, Config);
  }

  _createClass(Config, null, {
    _defaultConfig: {
      value: function _defaultConfig() {
        return {
          volume: {
            value: 100,
            parse: parseInt
          }
        };
      }
    },
    _defaultValueFor: {
      value: function _defaultValueFor(key) {
        try {
          return this._defaultConfig()[key].parse(this._defaultConfig()[key].value);
        } catch (e) {
          return undefined;
        }
      }
    },
    _create_keyspace: {
      value: function _create_keyspace(key) {
        return "clappr." + document.domain + "." + key;
      }
    },
    restore: {
      value: function restore(key) {
        if (Browser.hasLocalstorage && localStorage[this._create_keyspace(key)]) {
          return this._defaultConfig()[key].parse(localStorage[this._create_keyspace(key)]);
        }
        return this._defaultValueFor(key);
      }
    },
    persist: {
      value: function persist(key, value) {
        if (Browser.hasLocalstorage) {
          try {
            localStorage[this._create_keyspace(key)] = value;
            return true;
          } catch (e) {
            return false;
          }
        }
      }
    }
  });

  return Config;
})();

var seekStringToSeconds = function seekStringToSeconds(url) {
  var elements = (url.match(/t=([0-9]*h)?([0-9]*m)?([0-9]*s)?/) || []).splice(1);
  return !!elements.length ? elements.map(function (el) {
    if (el) {
      var value = parseInt(el.slice(0, 2)) || 0;
      switch (el[el.length - 1]) {
        case "h":
          value = value * 3600;break;
        case "m":
          value = value * 60;break;
      }
      return value;
    }
    return 0;
  }).reduce(function (a, b) {
    return a + b;
  }) : 0;
};

var idsCounter = {};

var uniqueId = function uniqueId(prefix) {
  idsCounter[prefix] || (idsCounter[prefix] = 0);
  var id = ++idsCounter[prefix];
  return prefix + id;
};

var isNumber = function isNumber(value) {
  return value - parseFloat(value) + 1 >= 0;
};

var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || function (fn) {
  return window.setTimeout(fn, 1000 / 60);
};

var cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame || window.webkitCancelAnimationFrame || window.clearTimeout;

module.exports = {
  extend: extend,
  formatTime: formatTime,
  Fullscreen: Fullscreen,
  Config: Config,
  seekStringToSeconds: seekStringToSeconds,
  uniqueId: uniqueId,
  isNumber: isNumber,
  requestAnimationFrame: requestAnimationFrame,
  cancelAnimationFrame: cancelAnimationFrame
};

},{"../components/browser":"browser","lodash.assign":1}],50:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * Container is responsible for the video rendering and state
 */

var UIObject = require("../../base/ui_object");
var Styler = require("../../base/styler");
var Events = require("../../base/events");
var find = require("lodash.find");

var Container = (function (_UIObject) {
  function Container(options) {
    _classCallCheck(this, Container);

    _get(Object.getPrototypeOf(Container.prototype), "constructor", this).call(this, options);
    this.currentTime = 0;
    this.playback = options.playback;
    this.settings = this.playback.settings;
    this.isReady = false;
    this.mediaControlDisabled = false;
    this.plugins = [this.playback];
    this.bindEvents();
  }

  _inherits(Container, _UIObject);

  _createClass(Container, {
    name: {
      get: function () {
        return "Container";
      }
    },
    attributes: {
      get: function () {
        return { "class": "container", "data-container": "" };
      }
    },
    events: {
      get: function () {
        return {
          click: "clicked",
          dblclick: "dblClicked",
          doubleTap: "dblClicked",
          mouseenter: "mouseEnter",
          mouseleave: "mouseLeave"
        };
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.playback, Events.PLAYBACK_PROGRESS, this.progress);
        this.listenTo(this.playback, Events.PLAYBACK_TIMEUPDATE, this.timeUpdated);
        this.listenTo(this.playback, Events.PLAYBACK_READY, this.ready);
        this.listenTo(this.playback, Events.PLAYBACK_BUFFERING, this.buffering);
        this.listenTo(this.playback, Events.PLAYBACK_BUFFERFULL, this.bufferfull);
        this.listenTo(this.playback, Events.PLAYBACK_SETTINGSUPDATE, this.settingsUpdate);
        this.listenTo(this.playback, Events.PLAYBACK_LOADEDMETADATA, this.loadedMetadata);
        this.listenTo(this.playback, Events.PLAYBACK_HIGHDEFINITIONUPDATE, this.highDefinitionUpdate);
        this.listenTo(this.playback, Events.PLAYBACK_BITRATE, this.updateBitrate);
        this.listenTo(this.playback, Events.PLAYBACK_PLAYBACKSTATE, this.playbackStateChanged);
        this.listenTo(this.playback, Events.PLAYBACK_DVR, this.playbackDvrStateChanged);
        this.listenTo(this.playback, Events.PLAYBACK_MEDIACONTROL_DISABLE, this.disableMediaControl);
        this.listenTo(this.playback, Events.PLAYBACK_MEDIACONTROL_ENABLE, this.enableMediaControl);
        this.listenTo(this.playback, Events.PLAYBACK_ENDED, this.ended);
        this.listenTo(this.playback, Events.PLAYBACK_PLAY, this.playing);
        this.listenTo(this.playback, Events.PLAYBACK_PAUSE, this.paused);
        this.listenTo(this.playback, Events.PLAYBACK_ERROR, this.error);
      }
    },
    playbackStateChanged: {
      value: function playbackStateChanged() {
        this.trigger(Events.CONTAINER_PLAYBACKSTATE);
      }
    },
    playbackDvrStateChanged: {
      value: function playbackDvrStateChanged(dvrInUse) {
        this.settings = this.playback.settings;
        this.dvrInUse = dvrInUse;
        this.trigger(Events.CONTAINER_PLAYBACKDVRSTATECHANGED, dvrInUse);
      }
    },
    updateBitrate: {
      value: function updateBitrate(newBitrate) {
        this.trigger(Events.CONTAINER_BITRATE, newBitrate);
      }
    },
    statsReport: {
      value: function statsReport(metrics) {
        this.trigger(Events.CONTAINER_STATS_REPORT, metrics);
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return this.playback.getPlaybackType();
      }
    },
    isDvrEnabled: {
      value: function isDvrEnabled() {
        return !!this.playback.dvrEnabled;
      }
    },
    isDvrInUse: {
      value: function isDvrInUse() {
        return !!this.dvrInUse;
      }
    },
    destroy: {
      value: function destroy() {
        this.trigger(Events.CONTAINER_DESTROYED, this, this.name);
        this.playback.destroy();
        this.plugins.forEach(function (plugin) {
          return plugin.destroy();
        });
        this.$el.remove();
      }
    },
    setStyle: {
      value: function setStyle(style) {
        this.$el.css(style);
      }
    },
    animate: {
      value: function animate(style, duration) {
        return this.$el.animate(style, duration).promise();
      }
    },
    ready: {
      value: function ready() {
        this.isReady = true;
        this.trigger(Events.CONTAINER_READY, this.name);
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return this.playback.isPlaying();
      }
    },
    getCurrentTime: {
      value: function getCurrentTime() {
        return this.currentTime;
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.playback.getDuration();
      }
    },
    error: {
      value: function error(errorObj) {
        this.trigger(Events.CONTAINER_ERROR, { error: errorObj, container: this }, this.name);
      }
    },
    loadedMetadata: {
      value: function loadedMetadata(duration) {
        this.trigger(Events.CONTAINER_LOADEDMETADATA, duration);
      }
    },
    timeUpdated: {
      value: function timeUpdated(position, duration) {
        this.currentTime = position;
        this.trigger(Events.CONTAINER_TIMEUPDATE, position, duration, this.name);
      }
    },
    progress: {
      value: function progress(startPosition, endPosition, duration) {
        this.trigger(Events.CONTAINER_PROGRESS, startPosition, endPosition, duration, this.name);
      }
    },
    playing: {
      value: function playing() {
        this.trigger(Events.CONTAINER_PLAY, this.name);
      }
    },
    paused: {
      value: function paused() {
        this.trigger(Events.CONTAINER_PAUSE, this.name);
      }
    },
    play: {
      value: function play() {
        this.playback.play();
      }
    },
    stop: {
      value: function stop() {
        this.trigger(Events.CONTAINER_STOP, this.name);
        this.playback.stop();
        this.currentTime = 0;
      }
    },
    pause: {
      value: function pause() {
        this.playback.pause();
      }
    },
    ended: {
      value: function ended() {
        this.trigger(Events.CONTAINER_ENDED, this, this.name);
        this.currentTime = 0;
      }
    },
    clicked: {
      value: function clicked() {
        this.trigger(Events.CONTAINER_CLICK, this, this.name);
      }
    },
    dblClicked: {
      value: function dblClicked() {
        this.trigger(Events.CONTAINER_DBLCLICK, this, this.name);
      }
    },
    setCurrentTime: {
      value: function setCurrentTime(time) {
        this.trigger(Events.CONTAINER_SEEK, time, this.name);
        this.playback.seek(time);
      }
    },
    setVolume: {
      value: function setVolume(value) {
        this.trigger(Events.CONTAINER_VOLUME, value, this.name);
        this.playback.volume(value);
      }
    },
    fullscreen: {
      value: function fullscreen() {
        this.trigger(Events.CONTAINER_FULLSCREEN, this.name);
      }
    },
    buffering: {
      value: function buffering() {
        this.trigger(Events.CONTAINER_STATE_BUFFERING, this.name);
      }
    },
    bufferfull: {
      value: function bufferfull() {
        this.trigger(Events.CONTAINER_STATE_BUFFERFULL, this.name);
      }
    },
    addPlugin: {
      value: function addPlugin(plugin) {
        this.plugins.push(plugin);
      }
    },
    hasPlugin: {
      value: function hasPlugin(name) {
        return !!this.getPlugin(name);
      }
    },
    getPlugin: {
      value: function getPlugin(name) {
        return find(this.plugins, function (plugin) {
          return plugin.name === name;
        });
      }
    },
    mouseEnter: {
      value: function mouseEnter() {
        this.trigger(Events.CONTAINER_MOUSE_ENTER);
      }
    },
    mouseLeave: {
      value: function mouseLeave() {
        this.trigger(Events.CONTAINER_MOUSE_LEAVE);
      }
    },
    settingsUpdate: {
      value: function settingsUpdate() {
        this.settings = this.playback.settings;
        this.trigger(Events.CONTAINER_SETTINGSUPDATE);
      }
    },
    highDefinitionUpdate: {
      value: function highDefinitionUpdate() {
        this.trigger(Events.CONTAINER_HIGHDEFINITIONUPDATE);
      }
    },
    isHighDefinitionInUse: {
      value: function isHighDefinitionInUse() {
        return this.playback.isHighDefinitionInUse();
      }
    },
    disableMediaControl: {
      value: function disableMediaControl() {
        this.mediaControlDisabled = true;
        this.trigger(Events.CONTAINER_MEDIACONTROL_DISABLE);
      }
    },
    enableMediaControl: {
      value: function enableMediaControl() {
        this.mediaControlDisabled = false;
        this.trigger(Events.CONTAINER_MEDIACONTROL_ENABLE);
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor("container");
        this.$el.append(style);
        this.$el.append(this.playback.render().el);
        return this;
      }
    }
  });

  return Container;
})(UIObject);

module.exports = Container;

},{"../../base/events":"events","../../base/styler":48,"../../base/ui_object":"ui_object","lodash.find":12}],51:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * The ContainerFactory is responsible for manage playback bootstrap and create containers.
 */

var assign = require("lodash.assign");
var BaseObject = require("../../base/base_object");
var Container = require("../container");
var $ = require("clappr-zepto");
var Events = require("../../base/events");
var find = require("lodash.find");

var ContainerFactory = (function (_BaseObject) {
  function ContainerFactory(options, loader) {
    _classCallCheck(this, ContainerFactory);

    _get(Object.getPrototypeOf(ContainerFactory.prototype), "constructor", this).call(this, options);
    this.options = options;
    this.loader = loader;
  }

  _inherits(ContainerFactory, _BaseObject);

  _createClass(ContainerFactory, {
    createContainers: {
      value: function createContainers() {
        var _this = this;

        return $.Deferred(function (promise) {
          promise.resolve(_this.options.sources.map(function (source) {
            return _this.createContainer(source);
          }));
        });
      }
    },
    findPlaybackPlugin: {
      value: function findPlaybackPlugin(source) {
        var _this = this;

        return find(this.loader.playbackPlugins, function (p) {
          return p.canPlay(source.toString(), _this.options.mimeType);
        });
      }
    },
    createContainer: {
      value: function createContainer(source, options) {
        if (!!source.match(/^\/\//)) source = window.location.protocol + source;
        options = assign({}, options, this.options, { src: source, autoPlay: !!this.options.autoPlay });
        var playbackPlugin = this.findPlaybackPlugin(source);
        var playback = new playbackPlugin(options);
        var container = new Container({ playback: playback });
        var defer = $.Deferred();
        defer.promise(container);
        this.addContainerPlugins(container, source);
        this.listenToOnce(container, Events.CONTAINER_READY, function () {
          return defer.resolve(container);
        });
        return container;
      }
    },
    addContainerPlugins: {
      value: function addContainerPlugins(container, source) {
        var _this = this;

        this.loader.containerPlugins.forEach(function (Plugin) {
          var options = assign(_this.options, { container: container, src: source });
          container.addPlugin(new Plugin(options));
        });
      }
    }
  });

  return ContainerFactory;
})(BaseObject);

module.exports = ContainerFactory;

},{"../../base/base_object":"base_object","../../base/events":"events","../container":"container","clappr-zepto":"zepto","lodash.assign":1,"lodash.find":12}],52:[function(require,module,exports){
"use strict";

module.exports = require("./container_factory");

},{"./container_factory":51}],53:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * The Core is responsible to manage Containers, the mediator, MediaControl
 * and the player state.
 */

var assign = require("lodash.assign");
var $ = require("clappr-zepto");

var UIObject = require("../../base/ui_object");
var ContainerFactory = require("../container_factory");
var Fullscreen = require("../../base/utils").Fullscreen;
var Styler = require("../../base/styler");
var MediaControl = require("../media_control");
var PlayerInfo = require("../player_info");
var Mediator = require("../mediator");
var Events = require("../../base/events");
var Browser = require("../browser");

var find = require("lodash.find");
var isNumber = require("../../base/utils").isNumber;
var requestAnimationFrame = require("../../base/utils").requestAnimationFrame;
var cancelAnimationFrame = require("../../base/utils").cancelAnimationFrame;

var Core = (function (_UIObject) {
  function Core(options) {
    var _this = this;

    _classCallCheck(this, Core);

    _get(Object.getPrototypeOf(Core.prototype), "constructor", this).call(this, options);
    PlayerInfo.options = options;
    this.options = options;
    this.plugins = [];
    this.containers = [];
    this.createContainers(options);
    //FIXME fullscreen api sucks
    $(document).bind("fullscreenchange", function () {
      return _this.exit();
    });
    $(document).bind("MSFullscreenChange", function () {
      return _this.exit();
    });
    $(document).bind("mozfullscreenchange", function () {
      return _this.exit();
    });
  }

  _inherits(Core, _UIObject);

  _createClass(Core, {
    events: {
      get: function () {
        return {
          webkitfullscreenchange: "exit",
          mousemove: "showMediaControl",
          mouseleave: "hideMediaControl"
        };
      }
    },
    attributes: {
      get: function () {
        return {
          "data-player": "",
          tabindex: 9999 };
      }
    },
    createContainers: {
      value: function createContainers(options) {
        var _this = this;

        this.defer = $.Deferred();
        this.defer.promise(this);
        this.containerFactory = new ContainerFactory(options, options.loader);
        this.containerFactory.createContainers().then(function (containers) {
          return _this.setupContainers(containers);
        }).then(function (containers) {
          return _this.resolveOnContainersReady(containers);
        });
      }
    },
    updateSize: {
      value: function updateSize() {
        if (Fullscreen.isFullscreen()) {
          this.setFullscreen();
        } else {
          this.setPlayerSize();
        }
        Mediator.trigger(Events.PLAYER_RESIZE);
      }
    },
    setFullscreen: {
      value: function setFullscreen() {
        if (!Browser.isiOs) {
          this.$el.addClass("fullscreen");
          this.$el.removeAttr("style");
          PlayerInfo.previousSize = PlayerInfo.currentSize;
          PlayerInfo.currentSize = { width: $(window).width(), height: $(window).height() };
        }
      }
    },
    setPlayerSize: {
      value: function setPlayerSize() {
        this.$el.removeClass("fullscreen");
        PlayerInfo.currentSize = PlayerInfo.previousSize;
        PlayerInfo.previousSize = { width: $(window).width(), height: $(window).height() };
        this.resize(PlayerInfo.currentSize);
      }
    },
    resize: {
      value: function resize(options) {
        if (!isNumber(options.height) && !isNumber(options.width)) {
          this.el.style.height = "" + options.height;
          this.el.style.width = "" + options.width;
        } else {
          this.el.style.height = "" + options.height + "px";
          this.el.style.width = "" + options.width + "px";
        }
        PlayerInfo.previousSize = PlayerInfo.currentSize;
        PlayerInfo.currentSize = options;
        Mediator.trigger(Events.PLAYER_RESIZE);
      }
    },
    enableResizeObserver: {
      value: function enableResizeObserver() {
        var _this = this;

        var checkSizeCallback = function () {
          if (_this.reqAnimFrame) cancelAnimationFrame(_this.reqAnimFrame);
          if (_this.previousSize.width != _this.$el.width() || _this.previousSize.height != _this.$el.height()) {
            Mediator.trigger(Events.PLAYER_RESIZE);
            _this.previousSize = { width: _this.$el.width(), height: _this.$el.height() };
          }
          _this.reqAnimFrame = requestAnimationFrame(checkSizeCallback);
        };

        this.reqAnimFrame = requestAnimationFrame(checkSizeCallback);
      }
    },
    disableResizeObserver: {
      value: function disableResizeObserver() {
        if (this.reqAnimFrame) cancelAnimationFrame(this.reqAnimFrame);
      }
    },
    resolveOnContainersReady: {
      value: function resolveOnContainersReady(containers) {
        var _this = this;

        $.when.apply($, containers).done(function () {
          return _this.defer.resolve(_this);
        });
      }
    },
    addPlugin: {
      value: function addPlugin(plugin) {
        this.plugins.push(plugin);
      }
    },
    hasPlugin: {
      value: function hasPlugin(name) {
        return !!this.getPlugin(name);
      }
    },
    getPlugin: {
      value: function getPlugin(name) {
        return find(this.plugins, function (plugin) {
          return plugin.name === name;
        });
      }
    },
    load: {
      value: function load(sources, mimeType) {
        var _this = this;

        this.options.mimeType = mimeType;
        sources = sources && sources.constructor === Array ? sources : [sources.toString()];
        this.containers.forEach(function (container) {
          return container.destroy();
        });
        this.containerFactory.options = assign(this.options, { sources: sources });
        this.containerFactory.createContainers().then(function (containers) {
          _this.setupContainers(containers);
        });
      }
    },
    destroy: {
      value: function destroy() {
        this.disableResizeObserver();
        this.containers.forEach(function (container) {
          return container.destroy();
        });
        this.plugins.forEach(function (plugin) {
          return plugin.destroy();
        });
        this.$el.remove();
        this.mediaControl.destroy();
        $(document).unbind("fullscreenchange");
        $(document).unbind("MSFullscreenChange");
        $(document).unbind("mozfullscreenchange");
      }
    },
    exit: {
      value: function exit() {
        this.updateSize();
        this.mediaControl.show();
      }
    },
    setMediaControlContainer: {
      value: function setMediaControlContainer(container) {
        this.mediaControl.setContainer(container);
        this.mediaControl.render();
      }
    },
    disableMediaControl: {
      value: function disableMediaControl() {
        this.mediaControl.disable();
        this.$el.removeClass("nocursor");
      }
    },
    enableMediaControl: {
      value: function enableMediaControl() {
        this.mediaControl.enable();
      }
    },
    removeContainer: {
      value: function removeContainer(container) {
        this.stopListening(container);
        this.containers = this.containers.filter(function (c) {
          return c !== container;
        });
      }
    },
    appendContainer: {
      value: function appendContainer(container) {
        this.listenTo(container, Events.CONTAINER_DESTROYED, this.removeContainer);
        this.el.appendChild(container.render().el);
        this.containers.push(container);
      }
    },
    setupContainers: {
      value: function setupContainers(containers) {
        containers.map(this.appendContainer.bind(this));
        this.setupMediaControl(this.getCurrentContainer());
        this.render();
        this.$el.appendTo(this.options.parentElement);
        return containers;
      }
    },
    createContainer: {
      value: function createContainer(source, options) {
        var container = this.containerFactory.createContainer(source, options);
        this.appendContainer(container);
        return container;
      }
    },
    setupMediaControl: {
      value: function setupMediaControl(container) {
        if (this.mediaControl) {
          this.mediaControl.setContainer(container);
        } else {
          this.mediaControl = this.createMediaControl(assign({ container: container, focusElement: this.el }, this.options));
          this.listenTo(this.mediaControl, Events.MEDIACONTROL_FULLSCREEN, this.toggleFullscreen);
          this.listenTo(this.mediaControl, Events.MEDIACONTROL_SHOW, this.onMediaControlShow.bind(this, true));
          this.listenTo(this.mediaControl, Events.MEDIACONTROL_HIDE, this.onMediaControlShow.bind(this, false));
        }
      }
    },
    createMediaControl: {
      value: function createMediaControl(options) {
        if (options.mediacontrol && options.mediacontrol.external) {
          return new options.mediacontrol.external(options);
        } else {
          return new MediaControl(options);
        }
      }
    },
    getCurrentContainer: {
      value: function getCurrentContainer() {
        return this.containers[0];
      }
    },
    toggleFullscreen: {
      value: function toggleFullscreen() {
        if (!Fullscreen.isFullscreen()) {
          Fullscreen.requestFullscreen(this.el);
          if (!Browser.isiOs) {
            this.$el.addClass("fullscreen");
          }
        } else {
          Fullscreen.cancelFullscreen();
          if (!Browser.isiOs) {
            this.$el.removeClass("fullscreen nocursor");
          }
        }
        this.mediaControl.show();
      }
    },
    showMediaControl: {
      value: function showMediaControl(event) {
        this.mediaControl.show(event);
      }
    },
    hideMediaControl: {
      value: function hideMediaControl(event) {
        this.mediaControl.hide(event);
      }
    },
    onMediaControlShow: {
      value: function onMediaControlShow(showing) {
        if (showing) this.$el.removeClass("nocursor");else if (Fullscreen.isFullscreen()) this.$el.addClass("nocursor");
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor("core");
        //FIXME
        //this.$el.empty()
        this.$el.append(style);
        this.$el.append(this.mediaControl.render().el);

        this.options.width = this.options.width || this.$el.width();
        this.options.height = this.options.height || this.$el.height();
        var size = { width: this.options.width, height: this.options.height };
        PlayerInfo.previousSize = PlayerInfo.currentSize = size;
        this.updateSize();

        this.previousSize = { width: this.$el.width(), height: this.$el.height() };

        this.enableResizeObserver();

        return this;
      }
    }
  });

  return Core;
})(UIObject);

module.exports = Core;

},{"../../base/events":"events","../../base/styler":48,"../../base/ui_object":"ui_object","../../base/utils":49,"../browser":"browser","../container_factory":52,"../media_control":"media_control","../mediator":"mediator","../player_info":"player_info","clappr-zepto":"zepto","lodash.assign":1,"lodash.find":12}],54:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * The Core Factory is responsible for instantiate the core and it's plugins.
 */

var BaseObject = require("../../base/base_object");
var Core = require("../core");

var CoreFactory = (function (_BaseObject) {
  function CoreFactory(player, loader) {
    _classCallCheck(this, CoreFactory);

    this.player = player;
    this.options = player.options;
    this.loader = loader;
    this.options.loader = this.loader;
  }

  _inherits(CoreFactory, _BaseObject);

  _createClass(CoreFactory, {
    create: {
      value: function create() {
        this.core = new Core(this.options);
        this.core.then(this.addCorePlugins.bind(this));
        return this.core;
      }
    },
    addCorePlugins: {
      value: function addCorePlugins() {
        var _this = this;

        this.loader.corePlugins.forEach(function (Plugin) {
          var plugin = new Plugin(_this.core);
          _this.core.addPlugin(plugin);
          _this.setupExternalInterface(plugin);
        });
        return this.core;
      }
    },
    setupExternalInterface: {
      value: function setupExternalInterface(plugin) {
        var externalFunctions = plugin.getExternalInterface();
        for (var key in externalFunctions) {
          this.player[key] = externalFunctions[key].bind(plugin);
        }
      }
    }
  });

  return CoreFactory;
})(BaseObject);

module.exports = CoreFactory;

},{"../../base/base_object":"base_object","../core":"core"}],55:[function(require,module,exports){
"use strict";

module.exports = require("./core_factory");

},{"./core_factory":54}],56:[function(require,module,exports){
"use strict";

module.exports = require("./loader");

},{"./loader":57}],57:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var BaseObject = require("../../base/base_object");
var PlayerInfo = require("../player_info");
var uniq = require("lodash.uniq");

/* Playback Plugins */
var HTML5VideoPlayback = require("../../playbacks/html5_video");
var FlashVideoPlayback = require("../../playbacks/flash");
var HTML5AudioPlayback = require("../../playbacks/html5_audio");
var HLSVideoPlayback = require("../../playbacks/hls");
var HTMLImgPlayback = require("../../playbacks/html_img");
var NoOp = require("../../playbacks/no_op");

/* Container Plugins */
var SpinnerThreeBouncePlugin = require("../../plugins/spinner_three_bounce");
var StatsPlugin = require("../../plugins/stats");
var WaterMarkPlugin = require("../../plugins/watermark");
var PosterPlugin = require("../../plugins/poster");
var GoogleAnalyticsPlugin = require("../../plugins/google_analytics");
var ClickToPausePlugin = require("../../plugins/click_to_pause");

/* Core Plugins */
var DVRControls = require("../../plugins/dvr_controls");
var Favicon = require("../../plugins/favicon");

var Loader = (function (_BaseObject) {
  function Loader(externalPlugins) {
    _classCallCheck(this, Loader);

    _get(Object.getPrototypeOf(Loader.prototype), "constructor", this).call(this);
    this.playbackPlugins = [HTML5AudioPlayback, HTML5VideoPlayback, FlashVideoPlayback, HLSVideoPlayback, HTMLImgPlayback, NoOp];
    this.containerPlugins = [SpinnerThreeBouncePlugin, WaterMarkPlugin, PosterPlugin, StatsPlugin, GoogleAnalyticsPlugin, ClickToPausePlugin];
    this.corePlugins = [DVRControls, Favicon];
    if (externalPlugins) {
      this.addExternalPlugins(externalPlugins);
    }
  }

  _inherits(Loader, _BaseObject);

  _createClass(Loader, {
    addExternalPlugins: {
      value: function addExternalPlugins(plugins) {
        var pluginName = function pluginName(plugin) {
          return plugin.prototype.name;
        };
        if (plugins.playback) {
          this.playbackPlugins = uniq(plugins.playback.concat(this.playbackPlugins), pluginName);
        }
        if (plugins.container) {
          this.containerPlugins = uniq(plugins.container.concat(this.containerPlugins), pluginName);
        }
        if (plugins.core) {
          this.corePlugins = uniq(plugins.core.concat(this.corePlugins), pluginName);
        }
        PlayerInfo.playbackPlugins = this.playbackPlugins;
      }
    },
    getPlugin: {
      value: function getPlugin(name) {
        var allPlugins = this.containerPlugins.concat(this.playbackPlugins).concat(this.corePlugins);
        return allPlugins.find(function (plugin) {
          return plugin.prototype.name === name;
        });
      }
    }
  });

  return Loader;
})(BaseObject);

module.exports = Loader;

},{"../../base/base_object":"base_object","../../playbacks/flash":"flash","../../playbacks/hls":"hls","../../playbacks/html5_audio":"html5_audio","../../playbacks/html5_video":"html5_video","../../playbacks/html_img":"html_img","../../playbacks/no_op":68,"../../plugins/click_to_pause":71,"../../plugins/dvr_controls":73,"../../plugins/favicon":75,"../../plugins/google_analytics":77,"../../plugins/poster":"poster","../../plugins/spinner_three_bounce":81,"../../plugins/stats":83,"../../plugins/watermark":85,"../player_info":"player_info","lodash.uniq":32}],58:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * The MediaControl is responsible for displaying the Player controls.
 */

var $ = require("clappr-zepto");
var JST = require("../../base/jst");
var Styler = require("../../base/styler");
var UIObject = require("../../base/ui_object");
var Utils = require("../../base/utils");
var Browser = require("../browser");
var SeekTime = require("../seek_time");
var Mediator = require("../mediator");
var PlayerInfo = require("../player_info");
var Events = require("../../base/events");
var Kibo = require("../../base/kibo");

var MediaControl = (function (_UIObject) {
  function MediaControl(options) {
    var _this = this;

    _classCallCheck(this, MediaControl);

    _get(Object.getPrototypeOf(MediaControl.prototype), "constructor", this).call(this, options);
    this.seekTime = new SeekTime(this);
    this.options = options;
    this.mute = this.options.mute;
    this.persistConfig = this.options.persistConfig;
    this.container = options.container;
    var initialVolume = this.persistConfig ? Utils.Config.restore("volume") : 100;
    this.setVolume(this.mute ? 0 : initialVolume);
    this.keepVisible = false;
    this.volumeBarClickDown = false;
    this.addEventListeners();
    this.settings = {
      left: ["play", "stop", "pause"],
      right: ["volume"],
      "default": ["position", "seekbar", "duration"]
    };
    this.settings = Object.keys(this.container.settings).length === 0 ? this.settings : this.container.settings;
    this.disabled = false;
    if (this.container.mediaControlDisabled || this.options.chromeless) {
      this.disable();
    }
    this.stopDragHandler = function (event) {
      return _this.stopDrag(event);
    };
    this.updateDragHandler = function (event) {
      return _this.updateDrag(event);
    };
    $(document).bind("mouseup", this.stopDragHandler);
    $(document).bind("mousemove", this.updateDragHandler);
    Mediator.on(Events.PLAYER_RESIZE, function () {
      return _this.playerResize();
    });
  }

  _inherits(MediaControl, _UIObject);

  _createClass(MediaControl, {
    name: {
      get: function () {
        return "MediaControl";
      }
    },
    attributes: {
      get: function () {
        return {
          "class": "media-control",
          "data-media-control": ""
        };
      }
    },
    events: {
      get: function () {
        return {
          "click [data-play]": "play",
          "click [data-pause]": "pause",
          "click [data-playpause]": "togglePlayPause",
          "click [data-stop]": "stop",
          "click [data-playstop]": "togglePlayStop",
          "click [data-fullscreen]": "toggleFullscreen",
          "click .bar-container[data-seekbar]": "seek",
          "click .bar-container[data-volume]": "volume",
          "click .drawer-icon[data-volume]": "toggleMute",
          "mouseenter .drawer-container[data-volume]": "showVolumeBar",
          "mouseleave .drawer-container[data-volume]": "hideVolumeBar",
          "mousedown .segmented-bar-element[data-volume]": "mousedownOnVolumeBar",
          "mouseleave .media-control-layer": "mouseleaveOnVolumeBar",
          "mousemove .segmented-bar-element[data-volume]": "mousemoveOnVolumeBar",
          "mouseup .segmented-bar-element[data-volume]": "mouseupOnVolumeBar",
          "mousedown .bar-scrubber[data-volume]": "startVolumeDrag",
          "mousedown .bar-scrubber[data-seekbar]": "startSeekDrag",
          "mousemove .bar-container[data-seekbar]": "mousemoveOnSeekBar",
          "mouseleave .bar-container[data-seekbar]": "mouseleaveOnSeekBar",
          "mouseenter .media-control-layer[data-controls]": "setKeepVisible",
          "mouseleave .media-control-layer[data-controls]": "resetKeepVisible"
        };
      }
    },
    template: {
      get: function () {
        return JST.media_control;
      }
    },
    addEventListeners: {
      value: function addEventListeners() {
        this.listenTo(this.container, Events.CONTAINER_PLAY, this.changeTogglePlay);
        this.listenTo(this.container, Events.CONTAINER_PAUSE, this.changeTogglePlay);
        this.listenTo(this.container, Events.CONTAINER_DBLCLICK, this.toggleFullscreen);
        this.listenTo(this.container, Events.CONTAINER_TIMEUPDATE, this.updateSeekBar);
        this.listenTo(this.container, Events.CONTAINER_PROGRESS, this.updateProgressBar);
        this.listenTo(this.container, Events.CONTAINER_SETTINGSUPDATE, this.settingsUpdate);
        this.listenTo(this.container, Events.CONTAINER_PLAYBACKDVRSTATECHANGED, this.settingsUpdate);
        this.listenTo(this.container, Events.CONTAINER_HIGHDEFINITIONUPDATE, this.highDefinitionUpdate);
        this.listenTo(this.container, Events.CONTAINER_MEDIACONTROL_DISABLE, this.disable);
        this.listenTo(this.container, Events.CONTAINER_MEDIACONTROL_ENABLE, this.enable);
        this.listenTo(this.container, Events.CONTAINER_ENDED, this.ended);
      }
    },
    disable: {
      value: function disable() {
        this.disabled = true;
        this.hide();
        this.$el.hide();
      }
    },
    enable: {
      value: function enable() {
        if (this.options.chromeless) {
          return;
        }this.disabled = false;
        this.show();
      }
    },
    play: {
      value: function play() {
        this.container.play();
      }
    },
    pause: {
      value: function pause() {
        this.container.pause();
      }
    },
    stop: {
      value: function stop() {
        this.container.stop();
      }
    },
    changeTogglePlay: {
      value: function changeTogglePlay() {
        if (this.container.isPlaying()) {
          this.$playPauseToggle.removeClass("paused").addClass("playing");
          this.$playStopToggle.removeClass("stopped").addClass("playing");
          this.trigger(Events.MEDIACONTROL_PLAYING);
        } else {
          this.$playPauseToggle.removeClass("playing").addClass("paused");
          this.$playStopToggle.removeClass("playing").addClass("stopped");
          this.trigger(Events.MEDIACONTROL_NOTPLAYING);
        }
      }
    },
    mousemoveOnSeekBar: {
      value: function mousemoveOnSeekBar(event) {
        if (this.container.settings.seekEnabled) {
          var offsetX = event.pageX - this.$seekBarContainer.offset().left - this.$seekBarHover.width() / 2;
          this.$seekBarHover.css({ left: offsetX });
        }
        this.trigger(Events.MEDIACONTROL_MOUSEMOVE_SEEKBAR, event);
      }
    },
    mouseleaveOnSeekBar: {
      value: function mouseleaveOnSeekBar(event) {
        this.trigger(Events.MEDIACONTROL_MOUSELEAVE_SEEKBAR, event);
      }
    },
    mousemoveOnVolumeBar: {
      value: function mousemoveOnVolumeBar(event) {
        if (this.volumeBarClickDown) {
          this.volume(event);
        }
      }
    },
    mousedownOnVolumeBar: {
      value: function mousedownOnVolumeBar() {
        var cursorStyleProperty = "url(http://www.google.com/intl/en_ALL/mapfiles/closedhand.cur), move";
        this.$volumeBarContainer.css("cursor", cursorStyleProperty);
        this.$volumeBarContainer.css("cursor", "-webkit-grabbing");
        this.$volumeBarContainer.css("cursor", "-moz-grabbing");
        this.volumeBarClickDown = true;
      }
    },
    mouseupOnVolumeBar: {
      value: function mouseupOnVolumeBar() {
        this.$volumeBarContainer.css("cursor", "pointer");
        this.volumeBarClickDown = false;
      }
    },
    mouseleaveOnVolumeBar: {
      value: function mouseleaveOnVolumeBar(event) {
        var volOffset = this.$volumeBarContainer.offset();

        var outsideByLeft = event.pageX < this.$seekBarContainer.offset().left;
        var outsideByRight = event.pageX > volOffset.left + volOffset.width;
        var outsideHorizontally = outsideByLeft || outsideByRight;

        var outsideByTop = event.pageY < volOffset.top;
        var outsideByBottom = event.pageY > volOffset.top + volOffset.height;

        var outsideVertically = outsideByTop || outsideByBottom;

        if (outsideHorizontally || outsideVertically) {
          this.mouseupOnVolumeBar();
        }
      }
    },
    playerResize: {
      value: function playerResize() {
        if (Utils.Fullscreen.isFullscreen()) {
          this.$fullscreenToggle.addClass("shrink");
        } else {
          this.$fullscreenToggle.removeClass("shrink");
        }
        this.$el.removeClass("w320");
        if (PlayerInfo.currentSize.width <= 320 || this.options.hideVolumeBar) {
          this.$el.addClass("w320");
        }
      }
    },
    togglePlayPause: {
      value: function togglePlayPause() {
        if (this.container.isPlaying()) {
          this.container.pause();
        } else {
          this.container.play();
        }
        return false;
      }
    },
    togglePlayStop: {
      value: function togglePlayStop() {
        if (this.container.isPlaying()) {
          this.container.stop();
        } else {
          this.container.play();
        }
      }
    },
    startSeekDrag: {
      value: function startSeekDrag(event) {
        if (!this.container.settings.seekEnabled) {
          return;
        }this.draggingSeekBar = true;
        this.$el.addClass("dragging");
        this.$seekBarLoaded.addClass("media-control-notransition");
        this.$seekBarPosition.addClass("media-control-notransition");
        this.$seekBarScrubber.addClass("media-control-notransition");
        if (event) {
          event.preventDefault();
        }
      }
    },
    startVolumeDrag: {
      value: function startVolumeDrag(event) {
        this.draggingVolumeBar = true;
        this.$el.addClass("dragging");
        if (event) {
          event.preventDefault();
        }
      }
    },
    stopDrag: {
      value: function stopDrag(event) {
        if (this.draggingSeekBar) {
          this.seek(event);
        }
        this.$el.removeClass("dragging");
        this.$seekBarLoaded.removeClass("media-control-notransition");
        this.$seekBarPosition.removeClass("media-control-notransition");
        this.$seekBarScrubber.removeClass("media-control-notransition dragging");
        this.draggingSeekBar = false;
        this.draggingVolumeBar = false;
      }
    },
    updateDrag: {
      value: function updateDrag(event) {
        if (this.draggingSeekBar) {
          event.preventDefault();
          var offsetX = event.pageX - this.$seekBarContainer.offset().left;
          var pos = offsetX / this.$seekBarContainer.width() * 100;
          pos = Math.min(100, Math.max(pos, 0));
          this.setSeekPercentage(pos);
        } else if (this.draggingVolumeBar) {
          event.preventDefault();
          this.volume(event);
        }
      }
    },
    volume: {
      value: function volume(event) {
        var offsetY = event.pageX - this.$volumeBarContainer.offset().left;
        var volumeFromUI = offsetY / this.$volumeBarContainer.width() * 100;
        this.setVolume(volumeFromUI);
      }
    },
    toggleMute: {
      value: function toggleMute() {
        if (this.mute) {
          if (this.currentVolume <= 0) {
            this.currentVolume = 100;
          }
          this.setVolume(this.currentVolume);
        } else {
          this.setVolume(0);
        }
      }
    },
    setVolume: {
      value: function setVolume(value) {
        this.currentVolume = Math.min(100, Math.max(value, 0));
        this.container.setVolume(this.currentVolume);
        this.setVolumeLevel(this.currentVolume);
        this.mute = this.currentVolume === 0;
        this.persistConfig && Utils.Config.persist("volume", this.currentVolume);
      }
    },
    toggleFullscreen: {
      value: function toggleFullscreen() {
        this.trigger(Events.MEDIACONTROL_FULLSCREEN, this.name);
        this.container.fullscreen();
        this.resetKeepVisible();
      }
    },
    setContainer: {
      value: function setContainer(container) {
        this.stopListening(this.container);
        this.container = container;
        this.changeTogglePlay();
        this.addEventListeners();
        this.settingsUpdate();
        this.container.trigger(Events.CONTAINER_PLAYBACKDVRSTATECHANGED, this.container.isDvrInUse());
        this.setVolume(this.currentVolume);
        if (this.container.mediaControlDisabled) {
          this.disable();
        }
        this.trigger(Events.MEDIACONTROL_CONTAINERCHANGED);
      }
    },
    showVolumeBar: {
      value: function showVolumeBar() {
        if (this.hideVolumeId) {
          clearTimeout(this.hideVolumeId);
        }
        this.$volumeBarContainer.removeClass("volume-bar-hide");
      }
    },
    hideVolumeBar: {
      value: function hideVolumeBar() {
        var _this = this;

        var timeout = 400;
        if (!this.$volumeBarContainer) {
          return;
        }if (this.draggingVolumeBar) {
          this.hideVolumeId = setTimeout(function () {
            return _this.hideVolumeBar();
          }, timeout);
        } else {
          if (this.hideVolumeId) {
            clearTimeout(this.hideVolumeId);
          }
          this.hideVolumeId = setTimeout(function () {
            return _this.$volumeBarContainer.addClass("volume-bar-hide");
          }, timeout);
        }
      }
    },
    ended: {
      value: function ended() {
        this.changeTogglePlay();
      }
    },
    updateProgressBar: {
      value: function updateProgressBar(startPosition, endPosition, duration) {
        var loadedStart = startPosition / duration * 100;
        var loadedEnd = endPosition / duration * 100;
        this.$seekBarLoaded.css({ left: loadedStart + "%", width: loadedEnd - loadedStart + "%" });
      }
    },
    updateSeekBar: {
      value: function updateSeekBar(position, duration) {
        if (this.draggingSeekBar) {
          return;
        }if (position < 0) position = duration;
        this.$seekBarPosition.removeClass("media-control-notransition");
        this.$seekBarScrubber.removeClass("media-control-notransition");
        var seekbarValue = 100 / duration * position;
        this.setSeekPercentage(seekbarValue);
        this.$("[data-position]").html(Utils.formatTime(position));
        this.$("[data-duration]").html(Utils.formatTime(duration));
      }
    },
    seek: {
      value: function seek(event) {
        if (!this.container.settings.seekEnabled) {
          return;
        }var offsetX = event.pageX - this.$seekBarContainer.offset().left;
        var pos = offsetX / this.$seekBarContainer.width() * 100;
        pos = Math.min(100, Math.max(pos, 0));
        this.container.setCurrentTime(pos);
        this.setSeekPercentage(pos);
        return false;
      }
    },
    setKeepVisible: {
      value: function setKeepVisible() {
        this.keepVisible = true;
      }
    },
    resetKeepVisible: {
      value: function resetKeepVisible() {
        this.keepVisible = false;
      }
    },
    isVisible: {
      value: function isVisible() {
        return !this.$el.hasClass("media-control-hide");
      }
    },
    show: {
      value: function show(event) {
        var _this = this;

        if (this.disabled) {
          return;
        }var timeout = 2000;
        if (!event || event.clientX !== this.lastMouseX && event.clientY !== this.lastMouseY || navigator.userAgent.match(/firefox/i)) {
          clearTimeout(this.hideId);
          this.$el.show();
          this.trigger(Events.MEDIACONTROL_SHOW, this.name);
          this.$el.removeClass("media-control-hide");
          this.hideId = setTimeout(function () {
            return _this.hide();
          }, timeout);
          if (event) {
            this.lastMouseX = event.clientX;
            this.lastMouseY = event.clientY;
          }
        }
      }
    },
    hide: {
      value: function hide() {
        var _this = this;

        var timeout = 2000;
        clearTimeout(this.hideId);
        if (!this.isVisible() || this.options.hideMediaControl === false) {
          return;
        }if (this.keepVisible || this.draggingSeekBar || this.draggingVolumeBar) {
          this.hideId = setTimeout(function () {
            return _this.hide();
          }, timeout);
        } else {
          this.trigger(Events.MEDIACONTROL_HIDE, this.name);
          this.$el.addClass("media-control-hide");
          this.hideVolumeBar();
        }
      }
    },
    settingsUpdate: {
      value: function settingsUpdate() {
        if (this.container.getPlaybackType() !== null && Object.keys(this.container.settings).length !== 0) {
          this.settings = this.container.settings;
          this.render();
        } else {
          this.disable();
        }
      }
    },
    highDefinitionUpdate: {
      value: function highDefinitionUpdate() {
        if (this.container.isHighDefinitionInUse()) {
          this.$el.find("button[data-hd-indicator]").addClass("enabled");
        } else {
          this.$el.find("button[data-hd-indicator]").removeClass("enabled");
        }
      }
    },
    createCachedElements: {
      value: function createCachedElements() {
        this.$playPauseToggle = this.$el.find("button.media-control-button[data-playpause]");
        this.$playStopToggle = this.$el.find("button.media-control-button[data-playstop]");
        this.$fullscreenToggle = this.$el.find("button.media-control-button[data-fullscreen]");
        this.$seekBarContainer = this.$el.find(".bar-container[data-seekbar]");
        this.$seekBarLoaded = this.$el.find(".bar-fill-1[data-seekbar]");
        this.$seekBarPosition = this.$el.find(".bar-fill-2[data-seekbar]");
        this.$seekBarScrubber = this.$el.find(".bar-scrubber[data-seekbar]");
        this.$seekBarHover = this.$el.find(".bar-hover[data-seekbar]");
        this.$volumeContainer = this.$el.find(".drawer-container[data-volume]");
        this.$volumeBarContainer = this.$el.find(".bar-container[data-volume]");
        this.$volumeIcon = this.$el.find(".drawer-icon[data-volume]");
      }
    },
    setVolumeLevel: {
      value: function setVolumeLevel(value) {
        var _this = this;

        if (!this.container.isReady || !this.$volumeBarContainer) {
          this.listenToOnce(this.container, Events.CONTAINER_READY, function () {
            return _this.setVolumeLevel(value);
          });
        } else {
          this.$volumeBarContainer.find(".segmented-bar-element").removeClass("fill");
          var item = Math.ceil(value / 10);
          this.$volumeBarContainer.find(".segmented-bar-element").slice(0, item).addClass("fill");
          if (value > 0) {
            this.$volumeIcon.removeClass("muted");
          } else {
            this.$volumeIcon.addClass("muted");
          }
        }
      }
    },
    setSeekPercentage: {
      value: function setSeekPercentage(value) {
        value = Math.min(value, 100);
        var pos = this.$seekBarContainer.width() * value / 100 - this.$seekBarScrubber.width() / 2;
        this.currentSeekPercentage = value;
        this.$seekBarPosition.css({ width: value + "%" });
        this.$seekBarScrubber.css({ left: pos });
      }
    },
    seekRelative: {
      value: function seekRelative(delta) {
        if (!this.container.settings.seekEnabled) {
          return;
        }var currentTime = this.container.getCurrentTime();
        var duration = this.container.getDuration();
        var position = Math.min(Math.max(currentTime + delta, 0), duration);
        position = Math.min(position * 100 / duration, 100);
        this.container.setCurrentTime(position);
      }
    },
    bindKeyEvents: {
      value: function bindKeyEvents() {
        var _this = this;

        if (this.kibo) {
          this.unbindKeyEvents();
        }
        this.kibo = new Kibo(this.options.focusElement);
        this.kibo.down(["space"], function () {
          return _this.togglePlayPause();
        });
        this.kibo.down(["left"], function () {
          return _this.seekRelative(-15);
        });
        this.kibo.down(["right"], function () {
          return _this.seekRelative(15);
        });
        var keys = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
        keys.forEach(function (i) {
          _this.kibo.down(i.toString(), function () {
            return _this.container.settings.seekEnabled && _this.container.setCurrentTime(i * 10);
          });
        });
      }
    },
    unbindKeyEvents: {
      value: function unbindKeyEvents() {
        this.kibo.off("space");
        this.kibo.off([1, 2, 3, 4, 5, 6, 7, 8, 9, 0]);
      }
    },
    parseColors: {
      value: function parseColors() {
        if (this.options.mediacontrol) {
          var buttonsColor = this.options.mediacontrol.buttons;
          var seekbarColor = this.options.mediacontrol.seekbar;
          this.$el.find(".bar-fill-2[data-seekbar]").css("background-color", seekbarColor);
          this.$el.find("[data-media-control] > .media-control-icon, .drawer-icon").css("color", buttonsColor);
          this.$el.find(".segmented-bar-element[data-volume]").css("boxShadow", "inset 2px 0 0 " + buttonsColor);
        }
      }
    },
    destroy: {
      value: function destroy() {
        $(document).unbind("mouseup", this.stopDragHandler);
        $(document).unbind("mousemove", this.updateDragHandler);
        this.unbindKeyEvents();
      }
    },
    render: {
      value: function render() {
        var _this = this;

        var timeout = 1000;
        var style = Styler.getStyleFor("media_control", { baseUrl: this.options.baseUrl });
        this.$el.html(this.template({ settings: this.settings }));
        this.$el.append(style);
        this.createCachedElements();
        this.$playPauseToggle.addClass("paused");
        this.$playStopToggle.addClass("stopped");

        this.changeTogglePlay();
        this.hideId = setTimeout(function () {
          return _this.hide();
        }, timeout);
        if (this.disabled) {
          this.hide();
        }

        if (Browser.isSafari && Browser.isMobile) {
          this.$volumeContainer.css("display", "none");
        }

        this.$seekBarPosition.addClass("media-control-notransition");
        this.$seekBarScrubber.addClass("media-control-notransition");

        if (!this.currentSeekPercentage) {
          this.currentSeekPercentage = 0;
        }
        this.setSeekPercentage(this.currentSeekPercentage);

        this.$el.ready(function () {
          if (!_this.container.settings.seekEnabled) {
            _this.$seekBarContainer.addClass("seek-disabled");
          }

          _this.setVolume(_this.currentVolume);
          _this.bindKeyEvents();
          _this.hideVolumeBar();
        });

        this.parseColors();
        this.seekTime.render();
        this.highDefinitionUpdate();

        this.trigger(Events.MEDIACONTROL_RENDERED);
        return this;
      }
    }
  });

  return MediaControl;
})(UIObject);

module.exports = MediaControl;

},{"../../base/events":"events","../../base/jst":47,"../../base/kibo":"kibo","../../base/styler":48,"../../base/ui_object":"ui_object","../../base/utils":49,"../browser":"browser","../mediator":"mediator","../player_info":"player_info","../seek_time":60,"clappr-zepto":"zepto"}],59:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var BaseObject = require("../base/base_object");
var CoreFactory = require("./core_factory");
var Loader = require("./loader");
var assign = require("lodash.assign");
var find = require("lodash.find");
var Events = require("events");
var uniqueId = require("../base/utils").uniqueId;
var PlayerInfo = require("./player_info");

var Player = (function (_BaseObject) {
  function Player(options) {
    _classCallCheck(this, Player);

    _get(Object.getPrototypeOf(Player.prototype), "constructor", this).call(this, options);
    window.p = this;
    var defaultOptions = { playerId: uniqueId(""), persistConfig: true, width: 640, height: 360, baseUrl: "http://cdn.clappr.io/latest" };
    this.options = assign(defaultOptions, options);
    this.options.sources = this.normalizeSources(options);
    this.loader = new Loader(this.options.plugins || {});
    this.coreFactory = new CoreFactory(this, this.loader);
    PlayerInfo.currentSize = { width: options.width, height: options.height };
    if (this.options.parentId) {
      this.setParentId(this.options.parentId);
    }
  }

  _inherits(Player, _BaseObject);

  _createClass(Player, {
    setParentId: {
      value: function setParentId(parentId) {
        var el = document.querySelector(parentId);
        if (el) {
          this.attachTo(el);
        }
      }
    },
    attachTo: {
      value: function attachTo(element) {
        this.options.parentElement = element;
        this.core = this.coreFactory.create();
        this.addEventListeners();
      }
    },
    addEventListeners: {
      value: function addEventListeners() {
        this.listenTo(this.core.mediaControl, Events.MEDIACONTROL_CONTAINERCHANGED, this.containerChanged);
        var container = this.core.mediaControl.container;
        if (!!container) {
          this.listenTo(container, Events.CONTAINER_PLAY, this.onPlay);
          this.listenTo(container, Events.CONTAINER_PAUSE, this.onPause);
          this.listenTo(container, Events.CONTAINER_STOP, this.onStop);
          this.listenTo(container, Events.CONTAINER_ENDED, this.onEnded);
          this.listenTo(container, Events.CONTAINER_SEEK, this.onSeek);
          this.listenTo(container, Events.CONTAINER_ERROR, this.onError);
          this.listenTo(container, Events.CONTAINER_TIMEUPDATE, this.onTimeUpdate);
        }
      }
    },
    containerChanged: {
      value: function containerChanged() {
        this.stopListening();
        this.addEventListeners();
      }
    },
    onPlay: {
      value: function onPlay() {
        this.trigger(Events.PLAYER_PLAY);
      }
    },
    onPause: {
      value: function onPause() {
        this.trigger(Events.PLAYER_PAUSE);
      }
    },
    onStop: {
      value: function onStop() {
        this.trigger(Events.PLAYER_STOP, this.getCurrentTime());
      }
    },
    onEnded: {
      value: function onEnded() {
        this.trigger(Events.PLAYER_ENDED);
      }
    },
    onSeek: {
      value: function onSeek(percent) {
        this.trigger(Events.PLAYER_SEEK, percent);
      }
    },
    onTimeUpdate: {
      value: function onTimeUpdate(position, duration) {
        this.trigger(Events.PLAYER_TIMEUPDATE, position, duration);
      }
    },
    onError: {
      value: function onError(error) {
        this.trigger(Events.PLAYER_ERROR, error);
      }
    },
    is: {
      value: function is(value, type) {
        return value.constructor === type;
      }
    },
    normalizeSources: {
      value: function normalizeSources(options) {
        var sources = options.sources || (options.source !== undefined ? [options.source.toString()] : []);
        return sources.length === 0 ? ["no.op"] : sources;
      }
    },
    resize: {
      value: function resize(size) {
        this.core.resize(size);
      }
    },
    load: {
      value: function load(sources, mimeType) {
        this.core.load(sources, mimeType);
      }
    },
    destroy: {
      value: function destroy() {
        this.core.destroy();
      }
    },
    play: {
      value: function play() {
        this.core.mediaControl.container.play();
      }
    },
    pause: {
      value: function pause() {
        this.core.mediaControl.container.pause();
      }
    },
    stop: {
      value: function stop() {
        this.core.mediaControl.container.stop();
      }
    },
    seek: {
      value: function seek(time) {
        this.core.mediaControl.container.setCurrentTime(time);
      }
    },
    setVolume: {
      value: function setVolume(volume) {
        this.core.mediaControl.container.setVolume(volume);
      }
    },
    mute: {
      value: function mute() {
        this.core.mediaControl.container.setVolume(0);
      }
    },
    unmute: {
      value: function unmute() {
        this.core.mediaControl.container.setVolume(100);
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return this.core.mediaControl.container.isPlaying();
      }
    },
    getPlugin: {
      value: function getPlugin(name) {
        var plugins = this.core.plugins.concat(this.core.mediaControl.container.plugins);
        return find(plugins, function (plugin) {
          return plugin.name === name;
        });
      }
    },
    getCurrentTime: {
      value: function getCurrentTime() {
        return this.core.mediaControl.container.getCurrentTime();
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.core.mediaControl.container.getDuration();
      }
    }
  });

  return Player;
})(BaseObject);

module.exports = Player;

},{"../base/base_object":"base_object","../base/utils":49,"./core_factory":55,"./loader":56,"./player_info":"player_info","events":"events","lodash.assign":1,"lodash.find":12}],60:[function(require,module,exports){
"use strict";

module.exports = require("./seek_time");

},{"./seek_time":61}],61:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var UIObject = require("../../base/ui_object");
var Styler = require("../../base/styler");
var JST = require("../../base/jst");
var formatTime = require("../../base/utils").formatTime;
var Events = require("../../base/events");

var SeekTime = (function (_UIObject) {
  function SeekTime(mediaControl) {
    _classCallCheck(this, SeekTime);

    _get(Object.getPrototypeOf(SeekTime.prototype), "constructor", this).call(this);
    this.mediaControl = mediaControl;
    this.addEventListeners();
  }

  _inherits(SeekTime, _UIObject);

  _createClass(SeekTime, {
    name: {
      get: function () {
        return "seek_time";
      }
    },
    template: {
      get: function () {
        return JST.seek_time;
      }
    },
    attributes: {
      get: function () {
        return {
          "class": "seek-time hidden",
          "data-seek-time": ""
        };
      }
    },
    addEventListeners: {
      value: function addEventListeners() {
        this.listenTo(this.mediaControl, Events.MEDIACONTROL_MOUSEMOVE_SEEKBAR, this.showTime);
        this.listenTo(this.mediaControl, Events.MEDIACONTROL_MOUSELEAVE_SEEKBAR, this.hideTime);
      }
    },
    showTime: {
      value: function showTime(event) {
        var offset = event.pageX - this.mediaControl.$seekBarContainer.offset().left;
        var timePosition = Math.min(100, Math.max(offset / this.mediaControl.$seekBarContainer.width() * 100, 0));
        var pointerPosition = event.pageX - this.mediaControl.$el.offset().left;
        pointerPosition = Math.min(Math.max(0, pointerPosition), this.mediaControl.$el.width() - this.$el.width());
        var currentTime = timePosition * this.mediaControl.container.getDuration() / 100;
        var options = {
          timestamp: currentTime,
          formattedTime: formatTime(currentTime),
          pointerPosition: pointerPosition
        };

        this.update(options);
      }
    },
    hideTime: {
      value: function hideTime() {
        this.$el.addClass("hidden");
        this.$el.css("left", "-100%");
      }
    },
    update: {
      value: function update(options) {
        if (this.mediaControl.container.settings.seekEnabled) {
          this.$el.find("[data-seek-time]").text(options.formattedTime);
          this.$el.css("left", options.pointerPosition - this.$el.width() / 2);
          this.$el.removeClass("hidden");
        }
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name);
        this.$el.html(this.template());
        this.$el.append(style);
        this.mediaControl.$el.append(this.el);
      }
    }
  });

  return SeekTime;
})(UIObject);

module.exports = SeekTime;

},{"../../base/events":"events","../../base/jst":47,"../../base/styler":48,"../../base/ui_object":"ui_object","../../base/utils":49}],62:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Playback = require("../../base/playback");
var Styler = require("../../base/styler");
var JST = require("../../base/jst");
var Mediator = require("../../components/mediator");
var template = require("../../base/template");
var $ = require("clappr-zepto");
var Browser = require("../../components/browser");
var seekStringToSeconds = require("../../base/utils").seekStringToSeconds;
var Events = require("../../base/events");

var objectIE = "<object type=\"application/x-shockwave-flash\" id=\"<%= cid %>\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" data-flash-vod=\"\"><param name=\"movie\" value=\"<%= baseUrl %>/assets/Player.swf\"> <param name=\"quality\" value=\"autohigh\"> <param name=\"swliveconnect\" value=\"true\"> <param name=\"allowScriptAccess\" value=\"always\"> <param name=\"bgcolor\" value=\"#001122\"> <param name=\"allowFullScreen\" value=\"false\"> <param name=\"wmode\" value=\"gpu\"> <param name=\"tabindex\" value=\"1\"> <param name=FlashVars value=\"playbackId=<%= playbackId %>\" /> </object>";

var Flash = (function (_Playback) {
  function Flash(options) {
    _classCallCheck(this, Flash);

    _get(Object.getPrototypeOf(Flash.prototype), "constructor", this).call(this, options);
    this.src = options.src;
    this.baseUrl = options.baseUrl;
    this.autoPlay = options.autoPlay;
    this.settings = { "default": ["seekbar"] };
    this.settings.left = ["playpause", "position", "duration"];
    this.settings.right = ["fullscreen", "volume"];
    this.settings.seekEnabled = true;
    this.isReady = false;
    this.addListeners();
  }

  _inherits(Flash, _Playback);

  _createClass(Flash, {
    name: {
      get: function () {
        return "flash";
      }
    },
    tagName: {
      get: function () {
        return "object";
      }
    },
    template: {
      get: function () {
        return JST.flash;
      }
    },
    bootstrap: {
      value: function bootstrap() {
        this.el.width = "100%";
        this.el.height = "100%";
        this.isReady = true;
        if (this.currentState === "PLAYING") {
          this.firstPlay();
        } else {
          this.currentState = "IDLE";
          this.autoPlay && this.play();
        }
        $("<div style=\"position: absolute; top: 0; left: 0; width: 100%; height: 100%\" />").insertAfter(this.$el);
        this.trigger(Events.PLAYBACK_READY, this.name);
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return "vod";
      }
    },
    setupFirefox: {
      value: function setupFirefox() {
        var $el = this.$("embed");
        $el.attr("data-flash", "");
        this.setElement($el[0]);
      }
    },
    isHighDefinitionInUse: {
      value: function isHighDefinitionInUse() {
        return false;
      }
    },
    updateTime: {
      value: function updateTime() {
        this.trigger(Events.PLAYBACK_TIMEUPDATE, this.el.getPosition(), this.el.getDuration(), this.name);
      }
    },
    addListeners: {
      value: function addListeners() {
        Mediator.on(this.uniqueId + ":progress", this.progress, this);
        Mediator.on(this.uniqueId + ":timeupdate", this.updateTime, this);
        Mediator.on(this.uniqueId + ":statechanged", this.checkState, this);
        Mediator.on(this.uniqueId + ":flashready", this.bootstrap, this);
      }
    },
    stopListening: {
      value: function stopListening() {
        _get(Object.getPrototypeOf(Flash.prototype), "stopListening", this).call(this);
        Mediator.off(this.uniqueId + ":progress");
        Mediator.off(this.uniqueId + ":timeupdate");
        Mediator.off(this.uniqueId + ":statechanged");
        Mediator.off(this.uniqueId + ":flashready");
      }
    },
    checkState: {
      value: function checkState() {
        if (this.currentState === "PAUSED") {
          return;
        } else if (this.currentState !== "PLAYING_BUFFERING" && this.el.getState() === "PLAYING_BUFFERING") {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
          this.currentState = "PLAYING_BUFFERING";
        } else if (this.el.getState() === "PLAYING") {
          this.trigger(Events.PLAYBACK_BUFFERFULL, this.name);
          this.currentState = "PLAYING";
        } else if (this.el.getState() === "IDLE") {
          this.currentState = "IDLE";
        } else if (this.el.getState() === "ENDED") {
          this.trigger(Events.PLAYBACK_ENDED, this.name);
          this.trigger(Events.PLAYBACK_TIMEUPDATE, 0, this.el.getDuration(), this.name);
          this.currentState = "ENDED";
        }
      }
    },
    progress: {
      value: function progress() {
        if (this.currentState !== "IDLE" && this.currentState !== "ENDED") {
          this.trigger(Events.PLAYBACK_PROGRESS, 0, this.el.getBytesLoaded(), this.el.getBytesTotal(), this.name);
        }
      }
    },
    firstPlay: {
      value: function firstPlay() {
        var _this = this;

        if (this.el.playerPlay) {
          this.el.playerPlay(this.src);
          this.listenToOnce(this, Events.PLAYBACK_BUFFERFULL, function () {
            return _this.checkInitialSeek();
          });
          this.currentState = "PLAYING";
        } else {
          this.listenToOnce(this, Events.PLAYBACK_READY, this.firstPlay);
        }
      }
    },
    checkInitialSeek: {
      value: function checkInitialSeek() {
        var seekTime = seekStringToSeconds(window.location.href);
        if (seekTime !== 0) {
          this.seekSeconds(seekTime);
        }
      }
    },
    play: {
      value: function play() {
        if (this.el.getState() === "PAUSED" || this.el.getState() === "PLAYING_BUFFERING") {
          this.currentState = "PLAYING";
          this.el.playerResume();
        } else if (this.el.getState() !== "PLAYING") {
          this.firstPlay();
        }
        this.trigger(Events.PLAYBACK_PLAY, this.name);
      }
    },
    volume: {
      value: function volume(value) {
        var _this = this;

        if (this.isReady) {
          this.el.playerVolume(value);
        } else {
          this.listenToOnce(this, Events.PLAYBACK_BUFFERFULL, function () {
            return _this.volume(value);
          });
        }
      }
    },
    pause: {
      value: function pause() {
        this.currentState = "PAUSED";
        this.el.playerPause();
        this.trigger(Events.PLAYBACK_PAUSE, this.name);
      }
    },
    stop: {
      value: function stop() {
        this.el.playerStop();
        this.trigger(Events.PLAYBACK_TIMEUPDATE, 0, this.name);
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return !!(this.isReady && this.currentState.indexOf("PLAYING") > -1);
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.el.getDuration();
      }
    },
    seek: {
      value: function seek(seekBarValue) {
        var seekTo = this.el.getDuration() * (seekBarValue / 100);
        this.seekSeconds(seekTo);
      }
    },
    seekSeconds: {
      value: function seekSeconds(seekTo) {
        this.el.playerSeek(seekTo);
        this.trigger(Events.PLAYBACK_TIMEUPDATE, seekTo, this.el.getDuration(), this.name);
        if (this.currentState === "PAUSED") {
          this.el.playerPause();
        }
      }
    },
    destroy: {
      value: function destroy() {
        clearInterval(this.bootstrapId);
        _get(Object.getPrototypeOf(Flash.prototype), "stopListening", this).call(this);
        this.$el.remove();
      }
    },
    setupIE: {
      value: function setupIE() {
        this.setElement($(template(objectIE)({ cid: this.cid, baseUrl: this.baseUrl, playbackId: this.uniqueId })));
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name);
        this.$el.html(this.template({ cid: this.cid, baseUrl: this.baseUrl, playbackId: this.uniqueId }));
        if (Browser.isFirefox) {
          this.setupFirefox();
        } else if (Browser.isLegacyIE) {
          this.setupIE();
        }
        this.$el.append(style);
        return this;
      }
    }
  });

  return Flash;
})(Playback);

Flash.canPlay = function (resource) {
  if (!Browser.hasFlash) {
    return false;
  } else if (!Browser.isMobile && Browser.isFirefox || Browser.isLegacyIE) {
    return resource && resource.constructor === String && !!resource.match(/(.*)\.(mp4|mov|f4v|3gpp|3gp)/);
  } else {
    return resource && resource.constructor === String && !!resource.match(/(.*)\.(mov|f4v|3gpp|3gp)/);
  }
};

module.exports = Flash;

},{"../../base/events":"events","../../base/jst":47,"../../base/playback":"playback","../../base/styler":48,"../../base/template":"template","../../base/utils":49,"../../components/browser":"browser","../../components/mediator":"mediator","clappr-zepto":"zepto"}],63:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var Mediator = require("../../components/mediator");

var HLSEvents = (function () {
  function HLSEvents(instanceId) {
    _classCallCheck(this, HLSEvents);

    this.instanceId = instanceId;
  }

  _createClass(HLSEvents, {
    ready: {
      value: function ready() {
        Mediator.trigger("" + this.instanceId + ":flashready");
      }
    },
    videoSize: {
      value: function videoSize(width, height) {
        Mediator.trigger("" + this.instanceId + ":videosizechanged", width, height);
      }
    },
    complete: {
      value: function complete() {
        Mediator.trigger("" + this.instanceId + ":complete");
      }
    },
    error: {
      value: function error(code, url, message) {
        Mediator.trigger("" + this.instanceId + ":error", code, url, message);
      }
    },
    manifest: {
      value: function manifest(duration, loadmetrics) {
        Mediator.trigger("" + this.instanceId + ":manifestloaded", duration, loadmetrics);
      }
    },
    audioLevelLoaded: {
      value: function audioLevelLoaded(loadmetrics) {
        Mediator.trigger("" + this.instanceId + ":audiolevelloaded", loadmetrics);
      }
    },
    levelLoaded: {
      value: function levelLoaded(loadmetrics) {
        Mediator.trigger("" + this.instanceId + ":levelloaded", loadmetrics);
      }
    },
    fragmentLoaded: {
      value: function fragmentLoaded(loadmetrics) {
        Mediator.trigger("" + this.instanceId + ":fragmentloaded", loadmetrics);
      }
    },
    fragmentPlaying: {
      value: function fragmentPlaying(playmetrics) {
        Mediator.trigger("" + this.instanceId + ":fragmentplaying", playmetrics);
      }
    },
    position: {
      value: function position(timemetrics) {
        Mediator.trigger("" + this.instanceId + ":timeupdate", timemetrics);
      }
    },
    state: {
      value: function state(newState) {
        Mediator.trigger("" + this.instanceId + ":playbackstate", newState);
      }
    },
    seekState: {
      value: function seekState(newState) {
        Mediator.trigger("" + this.instanceId + ":seekstate", newState);
      }
    },
    "switch": {
      value: function _switch(newLevel) {
        Mediator.trigger("" + this.instanceId + ":levelchanged", newLevel);
      }
    },
    audioTracksListChange: {
      value: function audioTracksListChange(trackList) {
        Mediator.trigger("" + this.instanceId + ":audiotracklistchanged", trackList);
      }
    },
    audioTrackChange: {
      value: function audioTrackChange(trackId) {
        Mediator.trigger("" + this.instanceId + ":audiotrackchanged", trackId);
      }
    }
  });

  return HLSEvents;
})();

module.exports = HLSEvents;

},{"../../components/mediator":"mediator"}],64:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Playback = require("../../base/playback");
var JST = require("../../base/jst");
var assign = require("lodash.assign");
var template = require("../../base/template");

var Mediator = require("../../components/mediator");
var Browser = require("../../components/browser");
var Events = require("../../base/events");
var Styler = require("../../base/styler");
var $ = require("clappr-zepto");

var HLSEvents = require("./flashls_events");

var objectIE = "<object type=\"application/x-shockwave-flash\" id=\"<%= cid %>\" class=\"hls-playback\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" data-hls=\"\" width=\"100%\" height=\"100%\"><param name=\"movie\" value=\"<%= baseUrl %>/assets/HLSPlayer.swf\"> <param name=\"quality\" value=\"autohigh\"> <param name=\"swliveconnect\" value=\"true\"> <param name=\"allowScriptAccess\" value=\"always\"> <param name=\"bgcolor\" value=\"#001122\"> <param name=\"allowFullScreen\" value=\"false\"> <param name=\"wmode\" value=\"transparent\"> <param name=\"tabindex\" value=\"1\"> <param name=FlashVars value=\"playbackId=<%= playbackId %>\" /> </object>";

var HLS = (function (_Playback) {
  function HLS(options) {
    _classCallCheck(this, HLS);

    _get(Object.getPrototypeOf(HLS.prototype), "constructor", this).call(this, options);
    this.src = options.src;
    this.baseUrl = options.baseUrl;
    this.flushLiveURLCache = options.flushLiveURLCache === undefined ? true : options.flushLiveURLCache;
    this.capLevelToStage = options.capLevelToStage === undefined ? false : options.capLevelToStage;
    this.useHardwareVideoDecoder = options.useHardwareVideoDecoder === undefined ? !Browser.isChrome : options.useHardwareVideoDecoder;
    this.maxBufferLength = options.maxBufferLength === undefined ? 120 : options.maxBufferLength;
    this.hlsMinimumDvrSize = options.hlsMinimumDvrSize == undefined ? 60 : options.hlsMinimumDvrSize;
    this.highDefinition = false;
    this.autoPlay = options.autoPlay;
    this.defaultSettings = {
      left: ["playstop"],
      "default": ["seekbar"],
      right: ["fullscreen", "volume", "hd-indicator"],
      seekEnabled: false
    };
    this.settings = assign({}, this.defaultSettings);
    this.playbackType = "live";
    this.addListeners();
  }

  _inherits(HLS, _Playback);

  _createClass(HLS, {
    name: {
      get: function () {
        return "hls";
      }
    },
    tagName: {
      get: function () {
        return "object";
      }
    },
    template: {
      get: function () {
        return JST.hls;
      }
    },
    attributes: {
      get: function () {
        return {
          "class": "hls-playback",
          "data-hls": "",
          type: "application/x-shockwave-flash",
          width: "100%",
          height: "100%"
        };
      }
    },
    addListeners: {
      value: function addListeners() {
        var _this = this;

        Mediator.on(this.cid + ":flashready", function () {
          return _this.bootstrap();
        });
        Mediator.on(this.cid + ":timeupdate", function (timeMetrics) {
          return _this.updateTime(timeMetrics);
        });
        Mediator.on(this.cid + ":playbackstate", function (state) {
          return _this.setPlaybackState(state);
        });
        Mediator.on(this.cid + ":levelchanged", function (level) {
          return _this.updateHighDefinition(level);
        });
        Mediator.on(this.cid + ":playbackerror", function () {
          return _this.flashPlaybackError();
        });
      }
    },
    stopListening: {
      value: function stopListening() {
        _get(Object.getPrototypeOf(HLS.prototype), "stopListening", this).call(this);
        Mediator.off(this.cid + ":flashready");
        Mediator.off(this.cid + ":timeupdate");
        Mediator.off(this.cid + ":playbackstate");
        Mediator.off(this.cid + ":levelchanged");
        Mediator.off(this.cid + ":playbackerror");
      }
    },
    bootstrap: {
      value: function bootstrap() {
        this.el.width = "100%";
        this.el.height = "100%";
        this.isReady = true;
        this.srcLoaded = false;
        this.currentState = "IDLE";
        this.setFlashSettings();
        this.updatePlaybackType();
        this.autoPlay && this.play();
        this.trigger(Events.PLAYBACK_READY, this.name);
      }
    },
    setFlashSettings: {
      value: function setFlashSettings() {
        this.el.playerSetflushLiveURLCache(this.flushLiveURLCache);
        this.el.playerCapLeveltoStage(this.capLevelToStage);
        this.el.playerSetmaxBufferLength(this.maxBufferLength);
        this.el.playerSetUseHardwareVideoDecoder(this.useHardwareVideoDecoder);
      }
    },
    updateHighDefinition: {
      value: function updateHighDefinition(level) {
        var currentLevel = this.getLevels()[level];
        this.highDefinition = currentLevel.height >= 720 || currentLevel.bitrate / 1000 >= 2000;
        this.trigger(Events.PLAYBACK_HIGHDEFINITIONUPDATE);
        this.trigger(Events.PLAYBACK_BITRATE, { bitrate: this.getCurrentBitrate() });
      }
    },
    updateTime: {
      value: function updateTime(timeMetrics) {
        if (this.currentState === "IDLE") {
          return;
        }var duration = this.normalizeDuration(timeMetrics.duration);
        var position = Math.min(Math.max(timeMetrics.position, 0), duration);
        var previousDVRStatus = this.dvrEnabled;
        var livePlayback = this.playbackType === "live";
        this.dvrEnabled = livePlayback && duration > this.hlsMinimumDvrSize;

        if (duration === 100 || livePlayback === undefined) {
          return;
        }

        if (this.dvrEnabled !== previousDVRStatus) {
          this.updateSettings();
          this.trigger(Events.PLAYBACK_SETTINGSUPDATE, this.name);
        }

        if (livePlayback && (!this.dvrEnabled || !this.dvrInUse)) {
          position = duration;
        }

        this.trigger(Events.PLAYBACK_TIMEUPDATE, position, duration, this.name);
      }
    },
    play: {
      value: function play() {
        if (this.currentState === "PAUSED") {
          this.el.playerResume();
        } else if (!this.srcLoaded && this.currentState !== "PLAYING") {
          this.firstPlay();
        } else {
          this.el.playerPlay();
        }
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return this.playbackType ? this.playbackType : null;
      }
    },
    getCurrentBitrate: {
      value: function getCurrentBitrate() {
        var currentLevel = this.getLevels()[this.el.getLevel()];
        return currentLevel.bitrate;
      }
    },
    isHighDefinitionInUse: {
      value: function isHighDefinitionInUse() {
        return this.highDefinition;
      }
    },
    getLevels: {
      value: function getLevels() {
        if (!this.levels || this.levels.length === 0) {
          this.levels = this.el.getLevels();
        }
        return this.levels;
      }
    },
    setPlaybackState: {
      value: function setPlaybackState(state) {
        if (["PLAYING_BUFFERING", "PAUSED_BUFFERING"].indexOf(state) >= 0) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
          this.updateCurrentState(state);
        } else if (["PLAYING", "PAUSED"].indexOf(state) >= 0) {
          if (["PLAYING_BUFFERING", "PAUSED_BUFFERING", "PAUSED", "IDLE"].indexOf(this.currentState) >= 0) {
            this.trigger(Events.PLAYBACK_BUFFERFULL, this.name);
          }
          this.updateCurrentState(state);
        } else if (state === "IDLE") {
          this.trigger(Events.PLAYBACK_ENDED, this.name);
          this.trigger(Events.PLAYBACK_TIMEUPDATE, 0, this.el.getDuration(), this.name);
          this.updateCurrentState(state);
        }
      }
    },
    updateCurrentState: {
      value: function updateCurrentState(state) {
        this.currentState = state;
        this.updatePlaybackType();
        if (state === "PLAYING") {
          this.trigger(Events.PLAYBACK_PLAY, this.name);
        } else {
          this.trigger(Events.PLAYBACK_PAUSE, this.name);
        }
      }
    },
    updatePlaybackType: {
      value: function updatePlaybackType() {
        this.playbackType = this.el.getType();
        if (this.playbackType) {
          this.playbackType = this.playbackType.toLowerCase();
          if (this.playbackType === "vod") {
            this.startReportingProgress();
          } else {
            this.stopReportingProgress();
          }
        }
        this.trigger(Events.PLAYBACK_PLAYBACKSTATE);
      }
    },
    startReportingProgress: {
      value: function startReportingProgress() {
        var _this = this;

        if (!this.reportingProgress) {
          this.reportingProgress = true;
          Mediator.on(this.cid + ":fragmentloaded", function () {
            return _this.onFragmentLoaded();
          });
        }
      }
    },
    stopReportingProgress: {
      value: function stopReportingProgress() {
        Mediator.off(this.cid + ":fragmentloaded", this.onFragmentLoaded, this);
      }
    },
    onFragmentLoaded: {
      value: function onFragmentLoaded() {
        var buffered = this.el.getPosition() + this.el.getbufferLength();
        this.trigger(Events.PLAYBACK_PROGRESS, this.el.getPosition(), buffered, this.el.getDuration(), this.name);
      }
    },
    firstPlay: {
      value: function firstPlay() {
        var _this = this;

        this.setFlashSettings(); //ensure flushLiveURLCache will work (#327)
        this.el.playerLoad(this.src);
        Mediator.once(this.cid + ":manifestloaded", function () {
          return _this.el.playerPlay();
        });
        this.srcLoaded = true;
      }
    },
    volume: {
      value: function volume(value) {
        var _this = this;

        if (this.isReady) {
          this.el.playerVolume(value);
        } else {
          this.listenToOnce(this, Events.PLAYBACK_BUFFERFULL, function () {
            return _this.volume(value);
          });
        }
      }
    },
    pause: {
      value: function pause() {
        if (this.playbackType !== "live" || this.dvrEnabled) {
          this.el.playerPause();
          if (this.playbackType === "live" && this.dvrEnabled) {
            this.updateDvr(true);
          }
        }
      }
    },
    stop: {
      value: function stop() {
        this.el.playerStop();
        this.trigger(Events.PLAYBACK_TIMEUPDATE, 0, this.name);
      }
    },
    isPlaying: {
      value: function isPlaying() {
        if (this.currentState) {
          return !!this.currentState.match(/playing/i);
        }
        return false;
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.normalizeDuration(this.el.getDuration());
      }
    },
    normalizeDuration: {
      value: function normalizeDuration(duration) {
        if (this.playbackType === "live") {
          // estimate 10 seconds of buffer time for live streams for seek positions
          duration = duration - 10;
        }
        return duration;
      }
    },
    seek: {
      value: function seek(time) {
        var duration = this.el.getDuration();
        if (time > 0) {
          time = duration * time / 100;
        }

        if (this.playbackType === "live") {
          // seek operations to a time within 5 seconds from live stream will position playhead back to live
          var dvrInUse = time >= 0 && duration - time > 5;
          if (!dvrInUse) {
            time = -1;
          }
          this.updateDvr(dvrInUse);
        }
        this.el.playerSeek(time);
        this.trigger(Events.PLAYBACK_TIMEUPDATE, time, duration, this.name);
        this.trigger(Events.PLAYBACK_HIGHDEFINITIONUPDATE);
      }
    },
    updateDvr: {
      value: function updateDvr(dvrInUse) {
        var previousDvrInUse = !!this.dvrInUse;
        this.dvrInUse = dvrInUse;
        if (this.dvrInUse !== previousDvrInUse) {
          this.updateSettings();
          this.trigger(Events.PLAYBACK_DVR, this.dvrInUse);
          this.trigger(Events.PLAYBACK_STATS_ADD, { dvr: this.dvrInUse });
        }
      }
    },
    flashPlaybackError: {
      value: function flashPlaybackError() {
        this.trigger(Events.PLAYBACK_STOP);
      }
    },
    timeUpdate: {
      value: function timeUpdate(time, duration) {
        this.trigger(Events.PLAYBACK_TIMEUPDATE, time, duration, this.name);
      }
    },
    destroy: {
      value: function destroy() {
        this.stopListening();
        this.$el.remove();
      }
    },
    setupFirefox: {
      value: function setupFirefox() {
        var $el = this.$("embed");
        $el.attr("data-hls", "");
        this.setElement($el);
      }
    },
    setupIE: {
      value: function setupIE() {
        this.setElement($(template(objectIE)({ cid: this.cid, baseUrl: this.baseUrl, playbackId: this.uniqueId })));
      }
    },
    updateSettings: {
      value: function updateSettings() {
        this.settings = assign({}, this.defaultSettings);
        if (this.playbackType === "vod" || this.dvrInUse) {
          this.settings.left = ["playpause", "position", "duration"];
          this.settings.seekEnabled = true;
        } else if (this.dvrEnabled) {
          this.settings.left = ["playpause"];
          this.settings.seekEnabled = true;
        } else {
          this.settings.seekEnabled = false;
        }
      }
    },
    setElement: {
      value: function setElement(element) {
        this.$el = element;
        this.el = element[0];
      }
    },
    createCallbacks: {
      value: function createCallbacks() {
        var _this = this;

        if (!window.Clappr.flashlsCallbacks) {
          window.Clappr.flashlsCallbacks = {};
        }
        this.flashlsEvents = new HLSEvents(this.cid);
        window.Clappr.flashlsCallbacks[this.cid] = function (eventName, args) {
          _this.flashlsEvents[eventName].apply(_this.flashlsEvents, args);
        };
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name);
        if (Browser.isLegacyIE) {
          this.setupIE();
        } else {
          var callbackName = this.createCallbacks();
          this.$el.html(this.template({ cid: this.cid, baseUrl: this.baseUrl, playbackId: this.uniqueId, callbackName: "window.Clappr.flashlsCallbacks." + this.cid }));
          if (Browser.isFirefox) {
            this.setupFirefox();
          } else if (Browser.isIE) {
            this.$("embed").remove();
          }
        }
        this.el.id = this.cid;
        this.$el.append(style);
        return this;
      }
    }
  });

  return HLS;
})(Playback);

HLS.canPlay = function (resource, mimeType) {
  return Browser.hasFlash && (!!resource.match(/^http(.*).m3u8?/) || mimeType === "application/x-mpegURL");
};

module.exports = HLS;

},{"../../base/events":"events","../../base/jst":47,"../../base/playback":"playback","../../base/styler":48,"../../base/template":"template","../../components/browser":"browser","../../components/mediator":"mediator","./flashls_events":63,"clappr-zepto":"zepto","lodash.assign":1}],65:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Playback = require("../../base/playback");
var Events = require("../../base/events");
var find = require("lodash.find");

var HTML5Audio = (function (_Playback) {
  function HTML5Audio(params) {
    _classCallCheck(this, HTML5Audio);

    _get(Object.getPrototypeOf(HTML5Audio.prototype), "constructor", this).call(this, params);
    this.options = params;
    this.settings = {
      left: ["playpause", "position", "duration"],
      right: ["fullscreen", "volume"],
      "default": ["seekbar"],
      seekEnabled: true
    };
    this.render();
    params.autoPlay && this.play();
  }

  _inherits(HTML5Audio, _Playback);

  _createClass(HTML5Audio, {
    name: {
      get: function () {
        return "html5_audio";
      }
    },
    tagName: {
      get: function () {
        return "audio";
      }
    },
    events: {
      get: function () {
        return {
          loadedmetadata: "loadedMetadata",
          stalled: "stalled",
          waiting: "waiting",
          timeupdate: "timeUpdated",
          ended: "ended",
          canplaythrough: "bufferFull",
          playing: "playing",
          pause: "paused"
        };
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.container, Events.CONTAINER_PLAY, this.play);
        this.listenTo(this.container, Events.CONTAINER_PAUSE, this.pause);
        this.listenTo(this.container, Events.CONTAINER_SEEK, this.seek);
        this.listenTo(this.container, Events.CONTAINER_VOLUME, this.volume);
        this.listenTo(this.container, Events.CONTAINER_STOP, this.stop);
      }
    },
    loadedMetadata: {
      value: function loadedMetadata(e) {
        this.durationChange();
        this.trigger(Events.PLAYBACK_LOADEDMETADATA, e.target.duration);
      }
    },
    durationChange: {
      value: function durationChange() {
        // we can't figure out if hls resource is VoD or not until it is being loaded or duration has changed.
        // that's why we check it again and update media control accordingly.
        if (this.getPlaybackType() === "aod") {
          this.settings.left = ["playpause", "position", "duration"];
        } else {
          this.settings.left = ["playstop"];
        }
        this.settings.seekEnabled = isFinite(this.getDuration());
        this.trigger(Events.PLAYBACK_SETTINGSUPDATE);
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return [0, undefined, Infinity].indexOf(this.el.duration) >= 0 ? "live" : "aod";
      }
    },
    stalled: {
      value: function stalled() {
        if (this.getPlaybackType() === "vod" && this.el.readyState < this.el.HAVE_FUTURE_DATA) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
        }
      }
    },
    waiting: {
      value: function waiting() {
        if (this.el.readyState < this.el.HAVE_FUTURE_DATA) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
        }
      }
    },
    play: {
      value: function play() {
        if (this.el.src !== this.options.src) {
          this.el.src = this.options.src;
        }
        this.el.play();
        this.trigger(Events.PLAYBACK_PLAY);
      }
    },
    pause: {
      value: function pause() {
        this.el.pause();
      }
    },
    stop: {
      value: function stop() {
        this.pause();
        this.el.currentTime = 0;
        this.el.src = "";
      }
    },
    volume: {
      value: function volume(value) {
        this.el.volume = value / 100;
      }
    },
    mute: {
      value: function mute() {
        this.el.volume = 0;
      }
    },
    unmute: {
      value: function unmute() {
        this.el.volume = 1;
      }
    },
    isMuted: {
      value: function isMuted() {
        return !!this.el.volume;
      }
    },
    ended: {
      value: function ended() {
        this.trigger(Events.CONTAINER_TIMEUPDATE, 0);
      }
    },
    seek: {
      value: function seek(seekBarValue) {
        var time = this.el.duration * (seekBarValue / 100);
        this.el.currentTime = time;
      }
    },
    getCurrentTime: {
      value: function getCurrentTime() {
        return this.el.currentTime;
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.el.duration;
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return !this.el.paused && !this.el.ended;
      }
    },
    playing: {
      value: function playing() {
        this.trigger(Events.PLAYBACK_PLAY);
      }
    },
    paused: {
      value: function paused() {
        this.trigger(Events.PLAYBACK_PAUSE);
      }
    },
    timeUpdated: {
      value: function timeUpdated() {
        if (this.getPlaybackType() === "live") {
          this.trigger(Events.PLAYBACK_TIMEUPDATE, 1, 1, this.name);
        } else {
          this.trigger(Events.PLAYBACK_TIMEUPDATE, this.el.currentTime, this.el.duration, this.name);
        }
      }
    },
    bufferFull: {
      value: function bufferFull() {
        this.trigger(Events.PLAYBACK_TIMEUPDATE, this.el.currentTime, this.el.duration, this.name);
        this.trigger(Events.PLAYBACK_BUFFERFULL);
      }
    },
    render: {
      value: function render() {
        this.trigger(Events.PLAYBACK_READY, this.name);
        return this;
      }
    }
  });

  return HTML5Audio;
})(Playback);

HTML5Audio.canPlay = function (resource, mimeType) {
  var mimetypes = {
    wav: ["audio/wav"],
    mp3: ["audio/mp3", "audio/mpeg;codecs=\"mp3\""],
    aac: ["audio/mp4;codecs=\"mp4a.40.5\""],
    oga: ["audio/ogg"]
  };
  var resourceParts = resource.split("?")[0].match(/.*\.(.*)$/) || [];
  if (resourceParts.length > 1 && mimetypes[resourceParts[1]] !== undefined) {
    var a = document.createElement("audio");
    return !!find(mimetypes[resourceParts[1]], function (ext) {
      return !!a.canPlayType(ext).replace(/no/, "");
    });
  } else if (mimeType) {
    var a = document.createElement("audio");
    return !!a.canPlayType(mimeType).replace(/no/, "");
  }
  return false;
};

module.exports = HTML5Audio;

},{"../../base/events":"events","../../base/playback":"playback","lodash.find":12}],66:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Playback = require("../../base/playback");
var JST = require("../../base/jst");
var Styler = require("../../base/styler");
var Browser = require("../../components/browser");
var seekStringToSeconds = require("../../base/utils").seekStringToSeconds;
var Events = require("../../base/events");
var find = require("lodash.find");

var HTML5Video = (function (_Playback) {
  function HTML5Video(options) {
    _classCallCheck(this, HTML5Video);

    _get(Object.getPrototypeOf(HTML5Video.prototype), "constructor", this).call(this, options);
    this.options = options;
    this.src = options.src;
    this.el.src = options.src;
    this.el.loop = options.loop;
    this.firstBuffer = true;
    this.settings = { "default": ["seekbar"] };
    if (Browser.isSafari) {
      this.setupSafari();
    } else {
      this.el.preload = options.preload ? options.preload : "metadata";
      this.settings.seekEnabled = true;
    }
    this.settings.left = ["playpause", "position", "duration"];
    this.settings.right = ["fullscreen", "volume"];
  }

  _inherits(HTML5Video, _Playback);

  _createClass(HTML5Video, {
    name: {
      get: function () {
        return "html5_video";
      }
    },
    tagName: {
      get: function () {
        return "video";
      }
    },
    template: {
      get: function () {
        return JST.html5_video;
      }
    },
    attributes: {
      get: function () {
        return {
          "data-html5-video": ""
        };
      }
    },
    events: {
      get: function () {
        return {
          timeupdate: "timeUpdated",
          progress: "progress",
          ended: "ended",
          stalled: "stalled",
          waiting: "waiting",
          canplaythrough: "bufferFull",
          loadedmetadata: "loadedMetadata",
          canplay: "ready",
          durationchange: "durationChange",
          error: "error",
          playing: "playing",
          pause: "paused"
        };
      }
    },
    setupSafari: {
      value: function setupSafari() {
        this.el.preload = "auto";
      }
    },
    loadedMetadata: {
      value: function loadedMetadata(e) {
        this.durationChange();
        this.trigger(Events.PLAYBACK_LOADEDMETADATA, e.target.duration);
        this.checkInitialSeek();
      }
    },
    durationChange: {
      value: function durationChange() {
        // we can't figure out if hls resource is VoD or not until it is being loaded or duration has changed.
        // that's why we check it again and update media control accordingly.
        if (this.getPlaybackType() === "vod") {
          this.settings.left = ["playpause", "position", "duration"];
        } else {
          this.settings.left = ["playstop"];
        }
        this.settings.seekEnabled = isFinite(this.getDuration());
        this.trigger(Events.PLAYBACK_SETTINGSUPDATE);
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return [0, undefined, Infinity].indexOf(this.el.duration) >= 0 ? "live" : "vod";
      }
    },
    isHighDefinitionInUse: {
      value: function isHighDefinitionInUse() {
        return false;
      }
    },
    play: {
      value: function play() {
        this.el.play();
      }
    },
    pause: {
      value: function pause() {
        this.el.pause();
      }
    },
    stop: {
      value: function stop() {
        this.pause();
        if (this.el.readyState !== 0) {
          this.el.currentTime = 0;
        }
      }
    },
    volume: {
      value: function volume(value) {
        this.el.volume = value / 100;
      }
    },
    mute: {
      value: function mute() {
        this.el.volume = 0;
      }
    },
    unmute: {
      value: function unmute() {
        this.el.volume = 1;
      }
    },
    isMuted: {
      value: function isMuted() {
        return !!this.el.volume;
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return !this.el.paused && !this.el.ended;
      }
    },
    playing: {
      value: function playing() {
        this.trigger(Events.PLAYBACK_PLAY);
      }
    },
    paused: {
      value: function paused() {
        this.trigger(Events.PLAYBACK_PAUSE);
      }
    },
    ended: {
      value: function ended() {
        this.trigger(Events.PLAYBACK_BUFFERFULL, this.name);
        this.trigger(Events.PLAYBACK_ENDED, this.name);
        this.trigger(Events.PLAYBACK_TIMEUPDATE, 0, this.el.duration, this.name);
      }
    },
    stalled: {
      value: function stalled() {
        if (this.getPlaybackType() === "vod" && this.el.readyState < this.el.HAVE_FUTURE_DATA) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
        }
      }
    },
    waiting: {
      value: function waiting() {
        if (this.el.readyState < this.el.HAVE_FUTURE_DATA) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
        }
      }
    },
    bufferFull: {
      value: function bufferFull() {
        if (this.options.poster && this.firstBuffer) {
          this.firstBuffer = false;
          if (!this.isPlaying()) {
            this.el.poster = this.options.poster;
          }
        } else {
          this.el.poster = "";
        }
        this.trigger(Events.PLAYBACK_BUFFERFULL, this.name);
      }
    },
    error: {
      value: function error(event) {
        this.trigger(Events.PLAYBACK_ERROR, this.el.error, this.name);
      }
    },
    destroy: {
      value: function destroy() {
        this.stop();
        this.el.src = "";
        this.$el.remove();
      }
    },
    seek: {
      value: function seek(seekBarValue) {
        var time = this.el.duration * (seekBarValue / 100);
        this.seekSeconds(time);
      }
    },
    seekSeconds: {
      value: function seekSeconds(time) {
        this.el.currentTime = time;
      }
    },
    checkInitialSeek: {
      value: function checkInitialSeek() {
        var seekTime = seekStringToSeconds(window.location.href);
        this.seekSeconds(seekTime);
      }
    },
    getCurrentTime: {
      value: function getCurrentTime() {
        return this.el.currentTime;
      }
    },
    getDuration: {
      value: function getDuration() {
        return this.el.duration;
      }
    },
    timeUpdated: {
      value: function timeUpdated() {
        if (this.getPlaybackType() === "live") {
          this.trigger(Events.PLAYBACK_TIMEUPDATE, 1, 1, this.name);
        } else {
          this.trigger(Events.PLAYBACK_TIMEUPDATE, this.el.currentTime, this.el.duration, this.name);
        }
      }
    },
    progress: {
      value: function progress() {
        if (!this.el.buffered.length) {
          return;
        }var bufferedPos = 0;
        for (var i = 0; i < this.el.buffered.length; i++) {
          if (this.el.currentTime >= this.el.buffered.start(i) && this.el.currentTime <= this.el.buffered.end(i)) {
            bufferedPos = i;
            break;
          }
        }
        this.checkBufferState(this.el.buffered.end(bufferedPos));
        this.trigger(Events.PLAYBACK_PROGRESS, this.el.buffered.start(bufferedPos), this.el.buffered.end(bufferedPos), this.el.duration, this.name);
      }
    },
    checkBufferState: {
      value: function checkBufferState(bufferedPos) {
        var playbackPos = this.el.currentTime + 1; // 1 second of threshold
        if (this.isPlaying() && playbackPos >= bufferedPos) {
          this.trigger(Events.PLAYBACK_BUFFERING, this.name);
          this.buffering = true;
        } else if (this.buffering) {
          this.trigger(Events.PLAYBACK_BUFFERFULL, this.name);
          this.buffering = false;
        }
      }
    },
    typeFor: {
      value: function typeFor(src) {
        return src.indexOf(".m3u8") > 0 ? "application/vnd.apple.mpegurl" : "video/mp4";
      }
    },
    ready: {
      value: function ready() {
        this.trigger(Events.PLAYBACK_READY, this.name);
      }
    },
    render: {
      value: function render() {
        var _this = this;

        var style = Styler.getStyleFor(this.name);
        this.$el.html(this.template({ src: this.src, type: this.typeFor(this.src) }));
        this.$el.append(style);
        setTimeout(function () {
          return _this.options.autoPlay && _this.play();
        }, 0);
        if (this.el.readyState === this.el.HAVE_ENOUGH_DATA) {
          this.ready();
        }
        return this;
      }
    }
  });

  return HTML5Video;
})(Playback);

HTML5Video.canPlay = function (resource, mimeType) {
  var mimetypes = {
    mp4: ["avc1.42E01E", "avc1.58A01E", "avc1.4D401E", "avc1.64001E", "mp4v.20.8", "mp4v.20.240", "mp4a.40.2"].map(function (codec) {
      return "video/mp4; codecs=\"" + codec + ", mp4a.40.2\"";
    }),
    ogg: ["video/ogg; codecs=\"theora, vorbis\"", "video/ogg; codecs=\"dirac\"", "video/ogg; codecs=\"theora, speex\""],
    "3gpp": ["video/3gpp; codecs=\"mp4v.20.8, samr\""],
    webm: ["video/webm; codecs=\"vp8, vorbis\""],
    mkv: ["video/x-matroska; codecs=\"theora, vorbis\""],
    m3u8: ["application/x-mpegURL"]
  };
  mimetypes.ogv = mimetypes.ogg;
  mimetypes["3gp"] = mimetypes["3gpp"];

  var resourceParts = resource.split("?")[0].match(/.*\.(.*)$/) || [];
  if (resourceParts.length > 1 && mimetypes[resourceParts[1]] !== undefined) {
    var v = document.createElement("video");
    return !!find(mimetypes[resourceParts[1]], function (ext) {
      return !!v.canPlayType(ext).replace(/no/, "");
    });
  } else if (mimeType) {
    var v = document.createElement("video");
    return !!v.canPlayType(mimeType).replace(/no/, "");
  }
  return false;
};

module.exports = HTML5Video;

},{"../../base/events":"events","../../base/jst":47,"../../base/playback":"playback","../../base/styler":48,"../../base/utils":49,"../../components/browser":"browser","lodash.find":12}],67:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Playback = require("../../base/playback");
var Styler = require("../../base/styler");

var HTMLImg = (function (_Playback) {
  function HTMLImg(params) {
    _classCallCheck(this, HTMLImg);

    _get(Object.getPrototypeOf(HTMLImg.prototype), "constructor", this).call(this, params);
    this.el.src = params.src;
  }

  _inherits(HTMLImg, _Playback);

  _createClass(HTMLImg, {
    name: {
      get: function () {
        return "html_img";
      }
    },
    tagName: {
      get: function () {
        return "img";
      }
    },
    attributes: {
      get: function () {
        return {
          "data-html-img": ""
        };
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return null;
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name);
        this.$el.append(style);
        return this;
      }
    }
  });

  return HTMLImg;
})(Playback);

HTMLImg.canPlay = function (resource) {
  return !!resource.match(/(.*).(png|jpg|jpeg|gif|bmp)/);
};

module.exports = HTMLImg;

},{"../../base/playback":"playback","../../base/styler":48}],68:[function(require,module,exports){
"use strict";

module.exports = require("./no_op");

},{"./no_op":69}],69:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var Playback = require("../../base/playback");
var JST = require("../../base/jst");
var Styler = require("../../base/styler");
var Events = require("../../base/events");
var requestAnimationFrame = require("../../base/utils").requestAnimationFrame;
var cancelAnimationFrame = require("../../base/utils").cancelAnimationFrame;

var NoOp = (function (_Playback) {
  function NoOp(options) {
    _classCallCheck(this, NoOp);

    _get(Object.getPrototypeOf(NoOp.prototype), "constructor", this).call(this, options);
  }

  _inherits(NoOp, _Playback);

  _createClass(NoOp, {
    name: {
      get: function () {
        return "no_op";
      }
    },
    template: {
      get: function () {
        return JST.no_op;
      }
    },
    attributes: {
      get: function () {
        return { "data-no-op": "" };
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name);
        this.$el.html(this.template());
        this.$el.append(style);
        this.animate();
        this.trigger(Events.PLAYBACK_READY, this.name);
        return this;
      }
    },
    noise: {
      value: function noise() {
        var idata = this.context.createImageData(this.context.canvas.width, this.context.canvas.height);
        var buffer32 = new Uint32Array(idata.data.buffer);
        var len = buffer32.length;
        var run = 0;
        var color = 0;
        var m = Math.random() * 6 + 4;

        for (var i = 0; i < len;) {
          if (run < 0) {
            run = m * Math.random();
            var p = Math.pow(Math.random(), 0.4);
            color = 255 * p << 24;
          }
          run -= 1;
          buffer32[i++] = color;
        }
        this.context.putImageData(idata, 0, 0);
      }
    },
    loop: {
      value: function loop() {
        var _this = this;

        this.noise();
        requestAnimationFrame(function () {
          return _this.loop();
        });
      }
    },
    animate: {
      value: function animate() {
        this.canvas = this.$el.find("canvas[data-no-op-canvas]")[0];
        this.context = this.canvas.getContext("2d");
        this.loop();
      }
    }
  });

  return NoOp;
})(Playback);

NoOp.canPlay = function (source) {
  return true;
};

module.exports = NoOp;

},{"../../base/events":"events","../../base/jst":47,"../../base/playback":"playback","../../base/styler":48,"../../base/utils":49}],70:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

//Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var ContainerPlugin = require("../../base/container_plugin");
var Events = require("../../base/events");
var Browser = require("../../components/browser");

var ClickToPausePlugin = (function (_ContainerPlugin) {
  function ClickToPausePlugin(options) {
    _classCallCheck(this, ClickToPausePlugin);

    if (!options.chromeless && !Browser.isMobile) {
      _get(Object.getPrototypeOf(ClickToPausePlugin.prototype), "constructor", this).call(this, options);
    }
  }

  _inherits(ClickToPausePlugin, _ContainerPlugin);

  _createClass(ClickToPausePlugin, {
    name: {
      get: function () {
        return "click_to_pause";
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.container, Events.CONTAINER_CLICK, this.click);
        this.listenTo(this.container, Events.CONTAINER_SETTINGSUPDATE, this.settingsUpdate);
      }
    },
    click: {
      value: function click() {
        if (this.container.getPlaybackType() !== "live" || this.container.isDvrEnabled()) {
          if (this.container.isPlaying()) {
            this.container.pause();
          } else {
            this.container.play();
          }
        }
      }
    },
    settingsUpdate: {
      value: function settingsUpdate() {
        this.container.$el.removeClass("pointer-enabled");
        if (this.container.getPlaybackType() !== "live" || this.container.isDvrEnabled()) {
          this.container.$el.addClass("pointer-enabled");
        }
      }
    }
  });

  return ClickToPausePlugin;
})(ContainerPlugin);

module.exports = ClickToPausePlugin;

},{"../../base/container_plugin":"container_plugin","../../base/events":"events","../../components/browser":"browser"}],71:[function(require,module,exports){
"use strict";

module.exports = require("./click_to_pause");

},{"./click_to_pause":70}],72:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var UICorePlugin = require("../../base/ui_core_plugin");
var JST = require("../../base/jst");
var Styler = require("../../base/styler");
var Events = require("../../base/events");
var $ = require("clappr-zepto");

var DVRControls = (function (_UICorePlugin) {
  function DVRControls(core) {
    _classCallCheck(this, DVRControls);

    _get(Object.getPrototypeOf(DVRControls.prototype), "constructor", this).call(this, core);
    this.core = core;
    this.settingsUpdate();
  }

  _inherits(DVRControls, _UICorePlugin);

  _createClass(DVRControls, {
    template: {
      get: function () {
        return JST.dvr_controls;
      }
    },
    name: {
      get: function () {
        return "dvr_controls";
      }
    },
    events: {
      get: function () {
        return {
          "click .live-button": "click"
        };
      }
    },
    attributes: {
      get: function () {
        return {
          "class": "dvr-controls",
          "data-dvr-controls": "" };
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.core.mediaControl, Events.MEDIACONTROL_CONTAINERCHANGED, this.settingsUpdate);
        this.listenTo(this.core.mediaControl, Events.MEDIACONTROL_RENDERED, this.settingsUpdate);
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_PLAYBACKDVRSTATECHANGED, this.dvrChanged);
      }
    },
    dvrChanged: {
      value: function dvrChanged(dvrEnabled) {
        this.settingsUpdate();
        this.core.mediaControl.$el.addClass("live");
        if (dvrEnabled) {
          this.core.mediaControl.$el.addClass("dvr");
          this.core.mediaControl.$el.find(".media-control-indicator[data-position], .media-control-indicator[data-duration]").hide();
        } else {
          this.core.mediaControl.$el.removeClass("dvr");
        }
      }
    },
    click: {
      value: function click() {
        if (!this.core.mediaControl.container.isPlaying()) {
          this.core.mediaControl.container.play();
        }
        if (this.core.mediaControl.$el.hasClass("dvr")) {
          this.core.mediaControl.container.setCurrentTime(-1);
        }
      }
    },
    settingsUpdate: {
      value: function settingsUpdate() {
        var _this = this;

        this.stopListening();
        if (this.shouldRender()) {
          this.render();
          this.$el.click(function () {
            return _this.click();
          });
        }
        this.bindEvents();
      }
    },
    shouldRender: {
      value: function shouldRender() {
        var useDvrControls = this.core.options.useDvrControls === undefined || !!this.core.options.useDvrControls;
        return useDvrControls && this.core.mediaControl.container.getPlaybackType() === "live";
      }
    },
    render: {
      value: function render() {
        var style = Styler.getStyleFor(this.name, { baseUrl: this.core.options.baseUrl });
        this.$el.html(this.template());
        this.$el.append(style);
        if (this.shouldRender()) {
          this.core.mediaControl.$el.addClass("live");
          this.core.mediaControl.$(".media-control-left-panel[data-media-control]").append(this.$el);
          if (this.$duration) {
            this.$duration.remove();
          }
          this.$duration = $("<span data-duration></span>");
          this.core.mediaControl.seekTime.$el.append(this.$duration);
        }
        return this;
      }
    }
  });

  return DVRControls;
})(UICorePlugin);

module.exports = DVRControls;

},{"../../base/events":"events","../../base/jst":47,"../../base/styler":48,"../../base/ui_core_plugin":"ui_core_plugin","clappr-zepto":"zepto"}],73:[function(require,module,exports){
"use strict";

module.exports = require("./dvr_controls");

},{"./dvr_controls":72}],74:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var CorePlugin = require("../../base/core_plugin");
var Events = require("../../base/events");

var Favicon = (function (_CorePlugin) {
  function Favicon(core) {
    _classCallCheck(this, Favicon);

    _get(Object.getPrototypeOf(Favicon.prototype), "constructor", this).call(this, core);
    this.oldIcon = $("link[rel=\"shortcut icon\"]");
    this.listenTo(this.core.mediaControl, Events.MEDIACONTROL_CONTAINERCHANGED, this.containerChanged);
    if (this.core.mediaControl.container) {
      this.containerChanged();
    }
  }

  _inherits(Favicon, _CorePlugin);

  _createClass(Favicon, {
    name: {
      get: function () {
        return "favicon";
      }
    },
    containerChanged: {
      value: function containerChanged() {
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_PLAY, this.setPlayIcon);
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_PAUSE, this.setPauseIcon);
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_STOP, this.resetIcon);
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_ENDED, this.resetIcon);
        this.listenTo(this.core.mediaControl.container, Events.CONTAINER_ERROR, this.resetIcon);
      }
    },
    createIcon: {
      value: function createIcon(charCode) {
        var canvas = $("<canvas/>");
        canvas[0].width = 32;
        canvas[0].height = 32;
        var ctx = canvas[0].getContext("2d");
        ctx.fillStyle = "#000";
        ctx.font = "25px Player";
        ctx.fillText(String.fromCharCode(charCode), 5, 26);
        var icon = $("<link rel=\"shortcut icon\" type=\"image/png\"/>");
        icon.attr("href", canvas[0].toDataURL("image/png"));
        return icon;
      }
    },
    setPlayIcon: {
      value: function setPlayIcon() {
        console.log("setPlayIcon");
        if (!this.playIcon) {
          this.playIcon = this.createIcon(57345);
        }
        this.changeIcon(this.playIcon);
      }
    },
    setPauseIcon: {
      value: function setPauseIcon() {
        if (!this.pauseIcon) {
          this.pauseIcon = this.createIcon(57346);
        }
        this.changeIcon(this.pauseIcon);
      }
    },
    resetIcon: {
      value: function resetIcon() {
        if (this.currentIcon) {
          this.currentIcon.remove();
        }
        $("head").append(this.oldIcon);
      }
    },
    changeIcon: {
      value: function changeIcon(icon) {
        if (icon) {
          this.oldIcon.remove();
          if (this.currentIcon) {
            this.currentIcon.remove();
          }
          this.currentIcon = icon;
          $("head").append(icon);
        }
      }
    }
  });

  return Favicon;
})(CorePlugin);

module.exports = Favicon;

},{"../../base/core_plugin":"core_plugin","../../base/events":"events"}],75:[function(require,module,exports){
"use strict";

module.exports = require("./favicon.js");

},{"./favicon.js":74}],76:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var ContainerPlugin = require("../../base/container_plugin");
var Events = require("../../base/events");

var GoogleAnalytics = (function (_ContainerPlugin) {
  function GoogleAnalytics(options) {
    _classCallCheck(this, GoogleAnalytics);

    _get(Object.getPrototypeOf(GoogleAnalytics.prototype), "constructor", this).call(this, options);
    if (options.gaAccount) {
      this.account = options.gaAccount;
      this.trackerName = options.gaTrackerName ? options.gaTrackerName + "." : "Clappr.";
      this.domainName = options.gaDomainName;
      this.currentHDState = undefined;
      this.embedScript();
    }
  }

  _inherits(GoogleAnalytics, _ContainerPlugin);

  _createClass(GoogleAnalytics, {
    name: {
      get: function () {
        return "google_analytics";
      }
    },
    embedScript: {
      value: function embedScript() {
        var _this = this;

        if (!window._gat) {
          var script = document.createElement("script");
          script.setAttribute("type", "text/javascript");
          script.setAttribute("async", "async");
          script.setAttribute("src", "http://www.google-analytics.com/ga.js");
          script.onload = function () {
            return _this.addEventListeners();
          };
          document.body.appendChild(script);
        } else {
          this.addEventListeners();
        }
      }
    },
    addEventListeners: {
      value: function addEventListeners() {
        var _this = this;

        if (this.container) {
          this.listenTo(this.container, Events.CONTAINER_PLAY, this.onPlay);
          this.listenTo(this.container, Events.CONTAINER_STOP, this.onStop);
          this.listenTo(this.container, Events.CONTAINER_PAUSE, this.onPause);
          this.listenTo(this.container, Events.CONTAINER_ENDED, this.onEnded);
          this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERING, this.onBuffering);
          this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERFULL, this.onBufferFull);
          this.listenTo(this.container, Events.CONTAINER_ENDED, this.onEnded);
          this.listenTo(this.container, Events.CONTAINER_ERROR, this.onError);
          this.listenTo(this.container, Events.CONTAINER_PLAYBACKSTATE, this.onPlaybackChanged);
          this.listenTo(this.container, Events.CONTAINER_VOLUME, function (event) {
            return _this.onVolumeChanged(event);
          });
          this.listenTo(this.container, Events.CONTAINER_SEEK, function (event) {
            return _this.onSeek(event);
          });
          this.listenTo(this.container, Events.CONTAINER_FULL_SCREEN, this.onFullscreen);
          this.listenTo(this.container, Events.CONTAINER_HIGHDEFINITIONUPDATE, this.onHD);
          this.listenTo(this.container, Events.CONTAINER_PLAYBACKDVRSTATECHANGED, this.onDVR);
        }
        _gaq.push([this.trackerName + "_setAccount", this.account]);
        if (!!this.domainName) _gaq.push([this.trackerName + "_setDomainName", this.domainName]);
      }
    },
    onPlay: {
      value: function onPlay() {
        this.push(["Video", "Play", this.container.playback.src]);
      }
    },
    onStop: {
      value: function onStop() {
        this.push(["Video", "Stop", this.container.playback.src]);
      }
    },
    onEnded: {
      value: function onEnded() {
        this.push(["Video", "Ended", this.container.playback.src]);
      }
    },
    onBuffering: {
      value: function onBuffering() {
        this.push(["Video", "Buffering", this.container.playback.src]);
      }
    },
    onBufferFull: {
      value: function onBufferFull() {
        this.push(["Video", "Bufferfull", this.container.playback.src]);
      }
    },
    onError: {
      value: function onError() {
        this.push(["Video", "Error", this.container.playback.src]);
      }
    },
    onHD: {
      value: function onHD() {
        var status = this.container.isHighDefinitionInUse() ? "ON" : "OFF";
        if (status !== this.currentHDState) {
          this.currentHDState = status;
          this.push(["Video", "HD - " + status, this.container.playback.src]);
        }
      }
    },
    onPlaybackChanged: {
      value: function onPlaybackChanged() {
        var type = this.container.getPlaybackType();
        if (type !== null) {
          this.push(["Video", "Playback Type - " + type, this.container.playback.src]);
        }
      }
    },
    onDVR: {
      value: function onDVR(dvrInUse) {
        var status = dvrInUse ? "ON" : "OFF";
        this.push(["Interaction", "DVR - " + status, this.container.playback.src]);
      }
    },
    onPause: {
      value: function onPause() {
        this.push(["Video", "Pause", this.container.playback.src]);
      }
    },
    onSeek: {
      value: function onSeek() {
        this.push(["Video", "Seek", this.container.playback.src]);
      }
    },
    onVolumeChanged: {
      value: function onVolumeChanged() {
        this.push(["Interaction", "Volume", this.container.playback.src]);
      }
    },
    onFullscreen: {
      value: function onFullscreen() {
        this.push(["Interaction", "Fullscreen", this.container.playback.src]);
      }
    },
    push: {
      value: function push(array) {
        var res = [this.trackerName + "_trackEvent"].concat(array);
        _gaq.push(res);
      }
    }
  });

  return GoogleAnalytics;
})(ContainerPlugin);

module.exports = GoogleAnalytics;

},{"../../base/container_plugin":"container_plugin","../../base/events":"events"}],77:[function(require,module,exports){
"use strict";

module.exports = require("./google_analytics");

},{"./google_analytics":76}],78:[function(require,module,exports){
"use strict";

module.exports = require("./log");

},{"./log":79}],79:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var Kibo = require("../../base/kibo");

var BOLD = "font-weight: bold; font-size: 13px;";
var INFO = "color: #006600;" + BOLD;
var DEBUG = "color: #0000ff;" + BOLD;
var WARN = "color: #ff8000;" + BOLD;
var ERROR = "color: #ff0000;" + BOLD;
var DEFAULT = "";

var Log = (function () {
  function Log() {
    var _this = this;

    _classCallCheck(this, Log);

    this.kibo = new Kibo();
    this.kibo.down(["ctrl shift d"], function () {
      return _this.onOff();
    });
    this.BLACKLIST = ["timeupdate", "playback:timeupdate", "playback:progress", "container:hover", "container:timeupdate", "container:progress"];
  }

  _createClass(Log, {
    info: {
      value: function info(klass) {
        this.log(klass, "info", Array.prototype.slice.call(arguments, 1));
      }
    },
    warn: {
      value: function warn(klass) {
        this.log(klass, "warn", Array.prototype.slice.call(arguments, 1));
      }
    },
    debug: {
      value: function debug(klass) {
        this.log(klass, "debug", Array.prototype.slice.call(arguments, 1));
      }
    },
    onOff: {
      value: function onOff() {
        window.DEBUG = !window.DEBUG;
        if (window.DEBUG) {
          console.log("log enabled");
        } else {
          console.log("log disabled");
        }
      }
    },
    log: {
      value: function log(klass, level, message) {
        if (!window.DEBUG || this.BLACKLIST.indexOf(message[0]) >= 0) {
          return;
        }if (!message) {
          message = klass;
          klass = null;
        }
        var color;
        if (level === "warn") {
          color = WARN;
        } else if (level === "info") {
          color = INFO;
        } else if (level === "debug") {
          color = DEBUG;
        } else if (level === "error") {
          color = ERROR;
        }
        var klassDescription = "";
        if (klass) {
          klassDescription = "[" + klass + "]";
        }
        console.log.apply(console, ["%c[" + level + "]" + klassDescription, color].concat(message));
      }
    }
  });

  return Log;
})();

Log.getInstance = function () {
  if (this._instance === undefined) {
    this._instance = new this();
  }
  return this._instance;
};

module.exports = Log;

},{"../../base/kibo":"kibo"}],80:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

//Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var UIContainerPlugin = require("../../base/ui_container_plugin");
var Styler = require("../../base/styler");
var JST = require("../../base/jst");
var Events = require("../../base/events");

var Mediator = require("../../components/mediator");
var PlayerInfo = require("../../components/player_info");

var $ = require("clappr-zepto");

var PosterPlugin = (function (_UIContainerPlugin) {
  function PosterPlugin(options) {
    _classCallCheck(this, PosterPlugin);

    _get(Object.getPrototypeOf(PosterPlugin.prototype), "constructor", this).call(this, options);
    this.options = options;
    this.container.disableMediaControl();
    this.render();
    this.bufferFull = false;
  }

  _inherits(PosterPlugin, _UIContainerPlugin);

  _createClass(PosterPlugin, {
    name: {
      get: function () {
        return "poster";
      }
    },
    template: {
      get: function () {
        return JST.poster;
      }
    },
    attributes: {
      get: function () {
        return {
          "class": "player-poster",
          "data-poster": ""
        };
      }
    },
    events: {
      get: function () {
        return {
          click: "clicked"
        };
      }
    },
    load: {
      value: function load(source) {
        this.options.poster = source;
        this.render();
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERING, this.onBuffering);
        this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERFULL, this.onBufferfull);
        this.listenTo(this.container, Events.CONTAINER_STOP, this.onStop);
        this.listenTo(this.container, Events.CONTAINER_PLAY, this.onPlay);
        this.listenTo(this.container, Events.CONTAINER_ENDED, this.onStop);
        Mediator.on(Events.PLAYER_RESIZE, this.updateSize, this);
      }
    },
    stopListening: {
      value: function stopListening() {
        _get(Object.getPrototypeOf(PosterPlugin.prototype), "stopListening", this).call(this);
        Mediator.off(Events.PLAYER_RESIZE, this.updateSize, this);
      }
    },
    onBuffering: {
      value: function onBuffering() {
        this.bufferFull = false;
        this.hidePlayButton();
      }
    },
    onPlay: {
      value: function onPlay() {
        if (this.bufferFull) {
          this.$el.hide();
          this.container.enableMediaControl();
        }
      }
    },
    onBufferfull: {
      value: function onBufferfull() {
        this.bufferFull = true;
        if (this.container.playback.name === "html5_video" && !this.container.isPlaying()) {
          return;
        }this.$el.hide();
        this.container.enableMediaControl();
      }
    },
    onStop: {
      value: function onStop() {
        this.$el.show();
        this.container.disableMediaControl();
        this.showPlayButton();
      }
    },
    showPlayButton: {
      value: function showPlayButton() {
        this.$playButton.show();
        this.updateSize();
      }
    },
    hidePlayButton: {
      value: function hidePlayButton() {
        this.$playButton.hide();
      }
    },
    clicked: {
      value: function clicked() {
        if (!this.options.chromeless) {
          this.container.play();
          this.hidePlayButton();
        }
        return false;
      }
    },
    updateSize: {
      value: function updateSize() {
        if (this.container.playback.name === "html_img") {
          return;
        }var height = this.$el.height();
        this.$el.css({ fontSize: height });
        if (this.$playWrapper.is(":visible")) {
          this.$playWrapper.css({ marginTop: -(this.$playWrapper.height() / 2) });
        }
      }
    },
    render: {
      value: function render() {
        var _this = this;

        if (this.container.playback.name === "html_img") {
          return;
        }var style = Styler.getStyleFor(this.name, { baseUrl: this.options.baseUrl })[0];
        this.$el.html(this.template());
        this.$el.append(style);
        if (this.options.poster) {
          var imgEl = $("<div data-poster class=\"poster-background\"></div>");
          imgEl.css({ "background-image": "url(" + this.options.poster + ")" });
          this.$el.prepend(imgEl);
        }
        this.container.$el.append(this.el);
        this.$playButton = this.$el.find(".poster-icon");
        this.$playWrapper = this.$el.find(".play-wrapper");
        setTimeout(function () {
          return _this.updateSize();
        }, 0);
        if (this.options.chromeless) {
          this.hidePlayButton();
          this.$el.css({ cursor: "initial" });
        }
        return this;
      }
    }
  });

  return PosterPlugin;
})(UIContainerPlugin);

module.exports = PosterPlugin;

},{"../../base/events":"events","../../base/jst":47,"../../base/styler":48,"../../base/ui_container_plugin":"ui_container_plugin","../../components/mediator":"mediator","../../components/player_info":"player_info","clappr-zepto":"zepto"}],81:[function(require,module,exports){
"use strict";

module.exports = require("./spinner_three_bounce");

},{"./spinner_three_bounce":82}],82:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var UIContainerPlugin = require("../../base/ui_container_plugin");
var Styler = require("../../base/styler");
var JST = require("../../base/jst");
var Events = require("../../base/events");

var SpinnerThreeBouncePlugin = (function (_UIContainerPlugin) {
  function SpinnerThreeBouncePlugin(options) {
    _classCallCheck(this, SpinnerThreeBouncePlugin);

    _get(Object.getPrototypeOf(SpinnerThreeBouncePlugin.prototype), "constructor", this).call(this, options);
    this.template = JST.spinner_three_bounce;
    this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERING, this.onBuffering);
    this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERFULL, this.onBufferFull);
    this.listenTo(this.container, Events.CONTAINER_STOP, this.onStop);
    this.render();
  }

  _inherits(SpinnerThreeBouncePlugin, _UIContainerPlugin);

  _createClass(SpinnerThreeBouncePlugin, {
    name: {
      get: function () {
        return "spinner";
      }
    },
    attributes: {
      get: function () {
        return {
          "data-spinner": "",
          "class": "spinner-three-bounce"
        };
      }
    },
    onBuffering: {
      value: function onBuffering() {
        var _this = this;

        this.showTimeout = setTimeout(function () {
          return _this.$el.show();
        }, 300);
      }
    },
    onBufferFull: {
      value: function onBufferFull() {
        clearTimeout(this.showTimeout);
        this.$el.hide();
      }
    },
    onStop: {
      value: function onStop() {
        this.$el.hide();
      }
    },
    render: {
      value: function render() {
        this.$el.html(this.template());
        var style = Styler.getStyleFor("spinner_three_bounce");
        this.container.$el.append(style);
        this.container.$el.append(this.$el);
        this.$el.hide();
        return this;
      }
    }
  });

  return SpinnerThreeBouncePlugin;
})(UIContainerPlugin);

module.exports = SpinnerThreeBouncePlugin;

},{"../../base/events":"events","../../base/jst":47,"../../base/styler":48,"../../base/ui_container_plugin":"ui_container_plugin"}],83:[function(require,module,exports){
"use strict";

module.exports = require("./stats");

},{"./stats":84}],84:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var ContainerPlugin = require("../../base/container_plugin");
var $ = require("clappr-zepto");
var Events = require("../../base/events");

var StatsPlugin = (function (_ContainerPlugin) {
  function StatsPlugin(options) {
    _classCallCheck(this, StatsPlugin);

    _get(Object.getPrototypeOf(StatsPlugin.prototype), "constructor", this).call(this, options);
    this.setInitialAttrs();
    this.reportInterval = options.reportInterval || 5000;
    this.state = "IDLE";
  }

  _inherits(StatsPlugin, _ContainerPlugin);

  _createClass(StatsPlugin, {
    name: {
      get: function () {
        return "stats";
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.container.playback, Events.PLAYBACK_PLAY, this.onPlay);
        this.listenTo(this.container, Events.CONTAINER_STOP, this.onStop);
        this.listenTo(this.container, Events.CONTAINER_DESTROYED, this.onStop);
        this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERING, this.onBuffering);
        this.listenTo(this.container, Events.CONTAINER_STATE_BUFFERFULL, this.onBufferFull);
        this.listenTo(this.container, Events.CONTAINER_STATS_ADD, this.onStatsAdd);
        this.listenTo(this.container, Events.CONTAINER_BITRATE, this.onStatsAdd);
        this.listenTo(this.container.playback, Events.PLAYBACK_STATS_ADD, this.onStatsAdd);
      }
    },
    setInitialAttrs: {
      value: function setInitialAttrs() {
        this.firstPlay = true;
        this.startupTime = 0;
        this.rebufferingTime = 0;
        this.watchingTime = 0;
        this.rebuffers = 0;
        this.externalMetrics = {};
      }
    },
    onPlay: {
      value: function onPlay() {
        this.state = "PLAYING";
        this.watchingTimeInit = Date.now();
        if (!this.intervalId) {
          this.intervalId = setInterval(this.report.bind(this), this.reportInterval);
        }
      }
    },
    onStop: {
      value: function onStop() {
        clearInterval(this.intervalId);
        this.intervalId = undefined;
        this.state = "STOPPED";
      }
    },
    onBuffering: {
      value: function onBuffering() {
        if (this.firstPlay) {
          this.startupTimeInit = Date.now();
        } else {
          this.rebufferingTimeInit = Date.now();
        }
        this.state = "BUFFERING";
        this.rebuffers++;
      }
    },
    onBufferFull: {
      value: function onBufferFull() {
        if (this.firstPlay && !!this.startupTimeInit) {
          this.firstPlay = false;
          this.startupTime = Date.now() - this.startupTimeInit;
          this.watchingTimeInit = Date.now();
        } else if (!!this.rebufferingTimeInit) {
          this.rebufferingTime += this.getRebufferingTime();
        }
        this.rebufferingTimeInit = undefined;
        this.state = "PLAYING";
      }
    },
    getRebufferingTime: {
      value: function getRebufferingTime() {
        return Date.now() - this.rebufferingTimeInit;
      }
    },
    getWatchingTime: {
      value: function getWatchingTime() {
        var totalTime = Date.now() - this.watchingTimeInit;
        return totalTime - this.rebufferingTime;
      }
    },
    isRebuffering: {
      value: function isRebuffering() {
        return !!this.rebufferingTimeInit;
      }
    },
    onStatsAdd: {
      value: function onStatsAdd(metric) {
        $.extend(this.externalMetrics, metric);
      }
    },
    getStats: {
      value: function getStats() {
        var metrics = {
          startupTime: this.startupTime,
          rebuffers: this.rebuffers,
          rebufferingTime: this.isRebuffering() ? this.rebufferingTime + this.getRebufferingTime() : this.rebufferingTime,
          watchingTime: this.isRebuffering() ? this.getWatchingTime() - this.getRebufferingTime() : this.getWatchingTime()
        };
        $.extend(metrics, this.externalMetrics);
        return metrics;
      }
    },
    report: {
      value: function report() {
        this.container.statsReport(this.getStats());
      }
    }
  });

  return StatsPlugin;
})(ContainerPlugin);

module.exports = StatsPlugin;

},{"../../base/container_plugin":"container_plugin","../../base/events":"events","clappr-zepto":"zepto"}],85:[function(require,module,exports){
"use strict";

module.exports = require("./watermark");

},{"./watermark":86}],86:[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var UIContainerPlugin = require("../../base/ui_container_plugin");
var Styler = require("../../base/styler");
var JST = require("../../base/jst");
var Events = require("../../base/events");

var WaterMarkPlugin = (function (_UIContainerPlugin) {
  function WaterMarkPlugin(options) {
    _classCallCheck(this, WaterMarkPlugin);

    _get(Object.getPrototypeOf(WaterMarkPlugin.prototype), "constructor", this).call(this, options);
    this.template = JST[this.name];
    this.position = options.position || "bottom-right";
    if (options.watermark) {
      this.imageUrl = options.watermark;
      this.render();
    } else {
      this.$el.remove();
    }
  }

  _inherits(WaterMarkPlugin, _UIContainerPlugin);

  _createClass(WaterMarkPlugin, {
    name: {
      get: function () {
        return "watermark";
      }
    },
    bindEvents: {
      value: function bindEvents() {
        this.listenTo(this.container, Events.CONTAINER_PLAY, this.onPlay);
        this.listenTo(this.container, Events.CONTAINER_STOP, this.onStop);
      }
    },
    onPlay: {
      value: function onPlay() {
        if (!this.hidden) this.$el.show();
      }
    },
    onStop: {
      value: function onStop() {
        this.$el.hide();
      }
    },
    render: {
      value: function render() {
        this.$el.hide();
        var templateOptions = { position: this.position, imageUrl: this.imageUrl };
        this.$el.html(this.template(templateOptions));
        var style = Styler.getStyleFor(this.name);
        this.container.$el.append(style);
        this.container.$el.append(this.$el);
        return this;
      }
    }
  });

  return WaterMarkPlugin;
})(UIContainerPlugin);

module.exports = WaterMarkPlugin;

},{"../../base/events":"events","../../base/jst":47,"../../base/styler":48,"../../base/ui_container_plugin":"ui_container_plugin"}],"clappr":[function(require,module,exports){
// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

"use strict";

var Player = require("./components/player");
var Mediator = require("./components/mediator");
var Events = require("./base/events");
var ContainerPlugin = require("./base/container_plugin");
var UIContainerPlugin = require("./base/ui_container_plugin");
var CorePlugin = require("./base/core_plugin");
var UICorePlugin = require("./base/ui_core_plugin");

window.DEBUG = false;

window.Clappr = {
  Player: Player,
  Mediator: Mediator,
  Events: Events,
  ContainerPlugin: ContainerPlugin,
  UIContainerPlugin: UIContainerPlugin,
  CorePlugin: CorePlugin,
  UICorePlugin: UICorePlugin
};
window.Clappr.version = "0.1.2";

module.exports = window.Clappr;

},{"./base/container_plugin":"container_plugin","./base/core_plugin":"core_plugin","./base/events":"events","./base/ui_container_plugin":"ui_container_plugin","./base/ui_core_plugin":"ui_core_plugin","./components/mediator":"mediator","./components/player":59}],"container_plugin":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var BaseObject = require("./base_object");
var extend = require("./utils").extend;

var ContainerPlugin = (function (_BaseObject) {
  function ContainerPlugin(options) {
    _classCallCheck(this, ContainerPlugin);

    _get(Object.getPrototypeOf(ContainerPlugin.prototype), "constructor", this).call(this, options);
    this.container = options.container;
    this.enabled = true;
    this.bindEvents();
  }

  _inherits(ContainerPlugin, _BaseObject);

  _createClass(ContainerPlugin, {
    enable: {
      value: function enable() {
        if (!this.enabled) {
          this.bindEvents();
          this.enabled = true;
        }
      }
    },
    disable: {
      value: function disable() {
        if (this.enabled) {
          this.stopListening();
          this.enabled = false;
        }
      }
    },
    bindEvents: {
      value: function bindEvents() {}
    },
    destroy: {
      value: function destroy() {
        this.stopListening();
      }
    }
  });

  return ContainerPlugin;
})(BaseObject);

ContainerPlugin.extend = function (properties) {
  return extend(ContainerPlugin, properties);
};

module.exports = ContainerPlugin;

},{"./base_object":"base_object","./utils":49}],"container":[function(require,module,exports){
"use strict";

module.exports = require("./container");

},{"./container":50}],"core_plugin":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var BaseObject = require("./base_object");
var extend = require("./utils").extend;

var CorePlugin = (function (_BaseObject) {
  function CorePlugin(core) {
    _classCallCheck(this, CorePlugin);

    _get(Object.getPrototypeOf(CorePlugin.prototype), "constructor", this).call(this, core);
    this.core = core;
  }

  _inherits(CorePlugin, _BaseObject);

  _createClass(CorePlugin, {
    getExternalInterface: {
      value: function getExternalInterface() {
        return {};
      }
    },
    destroy: {
      value: function destroy() {}
    }
  });

  return CorePlugin;
})(BaseObject);

CorePlugin.extend = function (properties) {
  return extend(CorePlugin, properties);
};

module.exports = CorePlugin;

},{"./base_object":"base_object","./utils":49}],"core":[function(require,module,exports){
"use strict";

module.exports = require("./core");

},{"./core":53}],"events":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var execOnce = require("lodash.once");
var uniqueId = require("./utils").uniqueId;
var Log = require("../plugins/log").getInstance();

var slice = Array.prototype.slice;

var Events = (function () {
  function Events() {
    _classCallCheck(this, Events);
  }

  _createClass(Events, {
    on: {
      value: function on(name, callback, context) {
        if (!eventsApi(this, "on", name, [callback, context]) || !callback) {
          return this;
        }this._events || (this._events = {});
        var events = this._events[name] || (this._events[name] = []);
        events.push({ callback: callback, context: context, ctx: context || this });
        return this;
      }
    },
    once: {
      value: (function (_once) {
        var _onceWrapper = function once(_x, _x2, _x3) {
          return _once.apply(this, arguments);
        };

        _onceWrapper.toString = function () {
          return _once.toString();
        };

        return _onceWrapper;
      })(function (name, callback, context) {
        if (!eventsApi(this, "once", name, [callback, context]) || !callback) return this;
        var self = this;
        var once = execOnce(function () {
          self.off(name, once);
          callback.apply(this, arguments);
        });
        once._callback = callback;
        return this.on(name, once, context);
      })
    },
    off: {
      value: function off(name, callback, context) {
        var retain, ev, events, names, i, l, j, k;
        if (!this._events || !eventsApi(this, "off", name, [callback, context])) {
          return this;
        }if (!name && !callback && !context) {
          this._events = void 0;
          return this;
        }
        names = name ? [name] : Object.keys(this._events);
        for (i = 0, l = names.length; i < l; i++) {
          name = names[i];
          events = this._events[name];
          if (events) {
            this._events[name] = retain = [];
            if (callback || context) {
              for (j = 0, k = events.length; j < k; j++) {
                ev = events[j];
                if (callback && callback !== ev.callback && callback !== ev.callback._callback || context && context !== ev.context) {
                  retain.push(ev);
                }
              }
            }
            if (!retain.length) delete this._events[name];
          }
        }
        return this;
      }
    },
    trigger: {
      value: function trigger(name) {
        var klass = this.constructor.name;
        if (this.hasOwnProperty(name)) {
          klass = this.name;
        }
        Log.debug.apply(Log, [klass].concat(Array.prototype.slice.call(arguments)));
        if (!this._events) {
          return this;
        }var args = slice.call(arguments, 1);
        if (!eventsApi(this, "trigger", name, args)) {
          return this;
        }var events = this._events[name];
        var allEvents = this._events.all;
        if (events) triggerEvents(events, args);
        if (allEvents) triggerEvents(allEvents, arguments);
        return this;
      }
    },
    stopListening: {
      value: function stopListening(obj, name, callback) {
        var listeningTo = this._listeningTo;
        if (!listeningTo) {
          return this;
        }var remove = !name && !callback;
        if (!callback && typeof name === "object") callback = this;
        if (obj) (listeningTo = {})[obj._listenId] = obj;
        for (var id in listeningTo) {
          obj = listeningTo[id];
          obj.off(name, callback, this);
          if (remove || Object.keys(obj._events).length === 0) delete this._listeningTo[id];
        }
        return this;
      }
    }
  });

  return Events;
})();

var eventSplitter = /\s+/;

var eventsApi = function eventsApi(obj, action, name, rest) {
  if (!name) {
    return true;
  } // Handle event maps.
  if (typeof name === "object") {
    for (var key in name) {
      obj[action].apply(obj, [key, name[key]].concat(rest));
    }
    return false;
  }

  // Handle space separated event names.
  if (eventSplitter.test(name)) {
    var names = name.split(eventSplitter);
    for (var i = 0, l = names.length; i < l; i++) {
      obj[action].apply(obj, [names[i]].concat(rest));
    }
    return false;
  }

  return true;
};

var triggerEvents = function triggerEvents(events, args) {
  var ev,
      i = -1,
      l = events.length,
      a1 = args[0],
      a2 = args[1],
      a3 = args[2];
  switch (args.length) {
    case 0:
      while (++i < l) (ev = events[i]).callback.call(ev.ctx);return;
    case 1:
      while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1);return;
    case 2:
      while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2);return;
    case 3:
      while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2, a3);return;
    default:
      while (++i < l) (ev = events[i]).callback.apply(ev.ctx, args);return;
  }
};

var listenMethods = { listenTo: "on", listenToOnce: "once" };

Object.keys(listenMethods).forEach(function (method) {
  Events.prototype[method] = function (obj, name, callback) {
    var listeningTo = this._listeningTo || (this._listeningTo = {});
    var id = obj._listenId || (obj._listenId = uniqueId("l"));
    listeningTo[id] = obj;
    if (!callback && typeof name === "object") callback = this;
    obj[listenMethods[method]](name, callback, this);
    return this;
  };
});

// PLAYER EVENTS
Events.PLAYER_RESIZE = "resize";
Events.PLAYER_PLAY = "play";
Events.PLAYER_PAUSE = "pause";
Events.PLAYER_STOP = "stop";
Events.PLAYER_ENDED = "ended";
Events.PLAYER_SEEK = "seek";
Events.PLAYER_ERROR = "error";
Events.PLAYER_TIMEUPDATE = "timeupdate";

// Playback Events
Events.PLAYBACK_PROGRESS = "playback:progress";
Events.PLAYBACK_TIMEUPDATE = "playback:timeupdate";
Events.PLAYBACK_READY = "playback:ready";
Events.PLAYBACK_BUFFERING = "playback:buffering";
Events.PLAYBACK_BUFFERFULL = "playback:bufferfull";
Events.PLAYBACK_SETTINGSUPDATE = "playback:settingsupdate";
Events.PLAYBACK_LOADEDMETADATA = "playback:loadedmetadata";
Events.PLAYBACK_HIGHDEFINITIONUPDATE = "playback:highdefinitionupdate";
Events.PLAYBACK_BITRATE = "playback:bitrate";
Events.PLAYBACK_PLAYBACKSTATE = "playback:playbackstate";
Events.PLAYBACK_DVR = "playback:dvr";
Events.PLAYBACK_MEDIACONTROL_DISABLE = "playback:mediacontrol:disable";
Events.PLAYBACK_MEDIACONTROL_ENABLE = "playback:mediacontrol:enable";
Events.PLAYBACK_ENDED = "playback:ended";
Events.PLAYBACK_PLAY = "playback:play";
Events.PLAYBACK_PAUSE = "playback:pause";
Events.PLAYBACK_ERROR = "playback:error";
Events.PLAYBACK_STATS_ADD = "playback:stats:add";

// Container Events
Events.CONTAINER_PLAYBACKSTATE = "container:playbackstate";
Events.CONTAINER_PLAYBACKDVRSTATECHANGED = "container:dvr";
Events.CONTAINER_BITRATE = "container:bitrate";
Events.CONTAINER_STATS_REPORT = "container:stats:report";
Events.CONTAINER_DESTROYED = "container:destroyed";
Events.CONTAINER_READY = "container:ready";
Events.CONTAINER_ERROR = "container:error";
Events.CONTAINER_LOADEDMETADATA = "container:loadedmetadata";
Events.CONTAINER_TIMEUPDATE = "container:timeupdate";
Events.CONTAINER_PROGRESS = "container:progress";
Events.CONTAINER_PLAY = "container:play";
Events.CONTAINER_STOP = "container:stop";
Events.CONTAINER_PAUSE = "container:pause";
Events.CONTAINER_ENDED = "container:ended";
Events.CONTAINER_CLICK = "container:click";
Events.CONTAINER_DBLCLICK = "container:dblclick";
Events.CONTAINER_MOUSE_ENTER = "container:mouseenter";
Events.CONTAINER_MOUSE_LEAVE = "container:mouseleave";
Events.CONTAINER_SEEK = "container:seek";
Events.CONTAINER_VOLUME = "container:volume";
Events.CONTAINER_FULLSCREEN = "container:fullscreen";
Events.CONTAINER_STATE_BUFFERING = "container:state:buffering";
Events.CONTAINER_STATE_BUFFERFULL = "container:state:bufferfull";
Events.CONTAINER_SETTINGSUPDATE = "container:settingsupdate";
Events.CONTAINER_HIGHDEFINITIONUPDATE = "container:highdefinitionupdate";
Events.CONTAINER_MEDIACONTROL_DISABLE = "container:mediacontrol:disable";
Events.CONTAINER_MEDIACONTROL_ENABLE = "container:mediacontrol:enable";
Events.CONTAINER_STATS_ADD = "container:stats:add";

// MediaControl Events
Events.MEDIACONTROL_RENDERED = "mediacontrol:rendered";
Events.MEDIACONTROL_FULLSCREEN = "mediacontrol:fullscreen";
Events.MEDIACONTROL_SHOW = "mediacontrol:show";
Events.MEDIACONTROL_HIDE = "mediacontrol:hide";
Events.MEDIACONTROL_MOUSEMOVE_SEEKBAR = "mediacontrol:mousemove:seekbar";
Events.MEDIACONTROL_MOUSELEAVE_SEEKBAR = "mediacontrol:mouseleave:seekbar";
Events.MEDIACONTROL_PLAYING = "mediacontrol:playing";
Events.MEDIACONTROL_NOTPLAYING = "mediacontrol:notplaying";
Events.MEDIACONTROL_CONTAINERCHANGED = "mediacontrol:containerchanged";

module.exports = Events;

},{"../plugins/log":78,"./utils":49,"lodash.once":28}],"flash":[function(require,module,exports){
"use strict";

module.exports = require("./flash");

},{"./flash":62}],"hls":[function(require,module,exports){
"use strict";

module.exports = require("./hls");

},{"./hls":64}],"html5_audio":[function(require,module,exports){
"use strict";

module.exports = require("./html5_audio");

},{"./html5_audio":65}],"html5_video":[function(require,module,exports){
"use strict";

module.exports = require("./html5_video");

},{"./html5_video":66}],"html_img":[function(require,module,exports){
"use strict";

module.exports = require("./html_img");

},{"./html_img":67}],"kibo":[function(require,module,exports){
"use strict";

var Kibo = function Kibo(element) {
  this.element = element || window.document;
  this.initialize();
};

Kibo.KEY_NAMES_BY_CODE = {
  8: "backspace", 9: "tab", 13: "enter",
  16: "shift", 17: "ctrl", 18: "alt",
  20: "caps_lock",
  27: "esc",
  32: "space",
  37: "left", 38: "up", 39: "right", 40: "down",
  48: "0", 49: "1", 50: "2", 51: "3", 52: "4", 53: "5", 54: "6", 55: "7", 56: "8", 57: "9",
  65: "a", 66: "b", 67: "c", 68: "d", 69: "e", 70: "f", 71: "g", 72: "h", 73: "i", 74: "j", 75: "k", 76: "l", 77: "m", 78: "n", 79: "o", 80: "p", 81: "q", 82: "r", 83: "s", 84: "t", 85: "u", 86: "v", 87: "w", 88: "x", 89: "y", 90: "z",
  112: "f1", 113: "f2", 114: "f3", 115: "f4", 116: "f5", 117: "f6", 118: "f7", 119: "f8", 120: "f9", 121: "f10", 122: "f11", 123: "f12"
};

Kibo.KEY_CODES_BY_NAME = {};
(function () {
  for (var key in Kibo.KEY_NAMES_BY_CODE) if (Object.prototype.hasOwnProperty.call(Kibo.KEY_NAMES_BY_CODE, key)) Kibo.KEY_CODES_BY_NAME[Kibo.KEY_NAMES_BY_CODE[key]] = +key;
})();

Kibo.MODIFIERS = ["shift", "ctrl", "alt"];

Kibo.registerEvent = (function () {
  if (document.addEventListener) {
    return function (element, eventName, func) {
      element.addEventListener(eventName, func, false);
    };
  } else if (document.attachEvent) {
    return function (element, eventName, func) {
      element.attachEvent("on" + eventName, func);
    };
  }
})();

Kibo.unregisterEvent = (function () {
  if (document.removeEventListener) {
    return function (element, eventName, func) {
      element.removeEventListener(eventName, func, false);
    };
  } else if (document.detachEvent) {
    return function (element, eventName, func) {
      element.detachEvent("on" + eventName, func);
    };
  }
})();

Kibo.stringContains = function (string, substring) {
  return string.indexOf(substring) !== -1;
};

Kibo.neatString = function (string) {
  return string.replace(/^\s+|\s+$/g, "").replace(/\s+/g, " ");
};

Kibo.capitalize = function (string) {
  return string.toLowerCase().replace(/^./, function (match) {
    return match.toUpperCase();
  });
};

Kibo.isString = function (what) {
  return Kibo.stringContains(Object.prototype.toString.call(what), "String");
};

Kibo.arrayIncludes = (function () {
  if (Array.prototype.indexOf) {
    return function (haystack, needle) {
      return haystack.indexOf(needle) !== -1;
    };
  } else {
    return function (haystack, needle) {
      for (var i = 0; i < haystack.length; i++) if (haystack[i] === needle) return true;
      return false;
    };
  }
})();

Kibo.extractModifiers = function (keyCombination) {
  var modifiers, i;
  modifiers = [];
  for (i = 0; i < Kibo.MODIFIERS.length; i++) if (Kibo.stringContains(keyCombination, Kibo.MODIFIERS[i])) modifiers.push(Kibo.MODIFIERS[i]);
  return modifiers;
};

Kibo.extractKey = function (keyCombination) {
  var keys, i;
  keys = Kibo.neatString(keyCombination).split(" ");
  for (i = 0; i < keys.length; i++) if (!Kibo.arrayIncludes(Kibo.MODIFIERS, keys[i])) return keys[i];
};

Kibo.modifiersAndKey = function (keyCombination) {
  var result, key;

  if (Kibo.stringContains(keyCombination, "any")) {
    return Kibo.neatString(keyCombination).split(" ").slice(0, 2).join(" ");
  }

  result = Kibo.extractModifiers(keyCombination);

  key = Kibo.extractKey(keyCombination);
  if (key && !Kibo.arrayIncludes(Kibo.MODIFIERS, key)) result.push(key);

  return result.join(" ");
};

Kibo.keyName = function (keyCode) {
  return Kibo.KEY_NAMES_BY_CODE[keyCode + ""];
};

Kibo.keyCode = function (keyName) {
  return +Kibo.KEY_CODES_BY_NAME[keyName];
};

Kibo.prototype.initialize = function () {
  var i,
      that = this;

  this.lastKeyCode = -1;
  this.lastModifiers = {};
  for (i = 0; i < Kibo.MODIFIERS.length; i++) this.lastModifiers[Kibo.MODIFIERS[i]] = false;

  this.keysDown = { any: [] };
  this.keysUp = { any: [] };
  this.downHandler = this.handler("down");
  this.upHandler = this.handler("up");

  Kibo.registerEvent(this.element, "keydown", this.downHandler);
  Kibo.registerEvent(this.element, "keyup", this.upHandler);
  Kibo.registerEvent(window, "unload", function unloader() {
    Kibo.unregisterEvent(that.element, "keydown", that.downHandler);
    Kibo.unregisterEvent(that.element, "keyup", that.upHandler);
    Kibo.unregisterEvent(window, "unload", unloader);
  });
};

Kibo.prototype.handler = function (upOrDown) {
  var that = this;
  return function (e) {
    var i, registeredKeys, lastModifiersAndKey;

    e = e || window.event;

    that.lastKeyCode = e.keyCode;
    for (i = 0; i < Kibo.MODIFIERS.length; i++) that.lastModifiers[Kibo.MODIFIERS[i]] = e[Kibo.MODIFIERS[i] + "Key"];
    if (Kibo.arrayIncludes(Kibo.MODIFIERS, Kibo.keyName(that.lastKeyCode))) that.lastModifiers[Kibo.keyName(that.lastKeyCode)] = true;

    registeredKeys = that["keys" + Kibo.capitalize(upOrDown)];

    for (i = 0; i < registeredKeys.any.length; i++) if (registeredKeys.any[i](e) === false && e.preventDefault) e.preventDefault();

    lastModifiersAndKey = that.lastModifiersAndKey();
    if (registeredKeys[lastModifiersAndKey]) for (i = 0; i < registeredKeys[lastModifiersAndKey].length; i++) if (registeredKeys[lastModifiersAndKey][i](e) === false && e.preventDefault) e.preventDefault();
  };
};

Kibo.prototype.registerKeys = function (upOrDown, newKeys, func) {
  var i,
      keys,
      registeredKeys = this["keys" + Kibo.capitalize(upOrDown)];

  if (Kibo.isString(newKeys)) newKeys = [newKeys];

  for (i = 0; i < newKeys.length; i++) {
    keys = newKeys[i];
    keys = Kibo.modifiersAndKey(keys + "");

    if (registeredKeys[keys]) registeredKeys[keys].push(func);else registeredKeys[keys] = [func];
  }

  return this;
};

Kibo.prototype.unregisterKeys = function (upOrDown, newKeys, func) {
  var i,
      j,
      keys,
      registeredKeys = this["keys" + Kibo.capitalize(upOrDown)];

  if (Kibo.isString(newKeys)) newKeys = [newKeys];

  for (i = 0; i < newKeys.length; i++) {
    keys = newKeys[i];
    keys = Kibo.modifiersAndKey(keys + "");

    if (func === null) delete registeredKeys[keys];else {
      if (registeredKeys[keys]) {
        for (j = 0; j < registeredKeys[keys].length; j++) {
          if (String(registeredKeys[keys][j]) === String(func)) {
            registeredKeys[keys].splice(j, 1);
            break;
          }
        }
      }
    }
  }

  return this;
};

Kibo.prototype.off = function (keys) {
  return this.unregisterKeys("down", keys, null);
};

Kibo.prototype.delegate = function (upOrDown, keys, func) {
  return func !== null || func !== undefined ? this.registerKeys(upOrDown, keys, func) : this.unregisterKeys(upOrDown, keys, func);
};

Kibo.prototype.down = function (keys, func) {
  return this.delegate("down", keys, func);
};

Kibo.prototype.up = function (keys, func) {
  return this.delegate("up", keys, func);
};

Kibo.prototype.lastKey = function (modifier) {
  if (!modifier) return Kibo.keyName(this.lastKeyCode);

  return this.lastModifiers[modifier];
};

Kibo.prototype.lastModifiersAndKey = function () {
  var result, i;

  result = [];
  for (i = 0; i < Kibo.MODIFIERS.length; i++) if (this.lastKey(Kibo.MODIFIERS[i])) result.push(Kibo.MODIFIERS[i]);

  if (!Kibo.arrayIncludes(result, this.lastKey())) result.push(this.lastKey());

  return result.join(" ");
};

module.exports = Kibo;

},{}],"media_control":[function(require,module,exports){
"use strict";

module.exports = require("./media_control");

},{"./media_control":58}],"mediator":[function(require,module,exports){
"use strict";

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

/**
 * The mediator is a singleton for handling global events.
 */

var Events = require("../base/events");

var events = new Events();

var Mediator = function Mediator() {
  _classCallCheck(this, Mediator);
};

Mediator.on = function (name, callback, context) {
  events.on(name, callback, context);
  return;
};

Mediator.once = function (name, callback, context) {
  events.once(name, callback, context);
  return;
};

Mediator.off = function (name, callback, context) {
  events.off(name, callback, context);
  return;
};

Mediator.trigger = function (name, opts) {
  events.trigger(name, opts);
  return;
};

Mediator.stopListening = function (obj, name, callback) {
  events.stopListening(obj, name, callback);
  return;
};

module.exports = Mediator;

},{"../base/events":"events"}],"playback":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var UIObject = require("./ui_object");

var Playback = (function (_UIObject) {
  function Playback(options) {
    _classCallCheck(this, Playback);

    _get(Object.getPrototypeOf(Playback.prototype), "constructor", this).call(this, options);
    this.settings = {};
  }

  _inherits(Playback, _UIObject);

  _createClass(Playback, {
    play: {
      value: function play() {}
    },
    pause: {
      value: function pause() {}
    },
    stop: {
      value: function stop() {}
    },
    seek: {
      value: function seek(time) {}
    },
    getDuration: {
      value: function getDuration() {
        return 0;
      }
    },
    isPlaying: {
      value: function isPlaying() {
        return false;
      }
    },
    getPlaybackType: {
      value: function getPlaybackType() {
        return "no_op";
      }
    },
    isHighDefinitionInUse: {
      value: function isHighDefinitionInUse() {
        return false;
      }
    },
    volume: {
      value: function volume(value) {}
    },
    destroy: {
      value: function destroy() {
        this.$el.remove();
      }
    }
  });

  return Playback;
})(UIObject);

Playback.canPlay = function (source) {
  return false;
};

module.exports = Playback;

},{"./ui_object":"ui_object"}],"player_info":[function(require,module,exports){
// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

"use strict";

var PlayerInfo = {
  options: {},
  playbackPlugins: [],
  currentSize: { width: 0, height: 0 }
};

module.exports = PlayerInfo;

},{}],"poster":[function(require,module,exports){
"use strict";

module.exports = require("./poster");

},{"./poster":80}],"template":[function(require,module,exports){
// Simple JavaScript Templating
// Paul Miller (http://paulmillr.com)
// http://underscorejs.org
// (c) 2009-2013 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
"use strict";

(function (globals) {
  // By default, Underscore uses ERB-style template delimiters, change the
  // following template settings to use alternative delimiters.
  var settings = {
    evaluate: /<%([\s\S]+?)%>/g,
    interpolate: /<%=([\s\S]+?)%>/g,
    escape: /<%-([\s\S]+?)%>/g
  };

  // When customizing `templateSettings`, if you don't want to define an
  // interpolation, evaluation or escaping regex, we need one that is
  // guaranteed not to match.
  var noMatch = /(.)^/;

  // Certain characters need to be escaped so that they can be put into a
  // string literal.
  var escapes = {
    "'": "'",
    "\\": "\\",
    "\r": "r",
    "\n": "n",
    "\t": "t",
    "\u2028": "u2028",
    "\u2029": "u2029"
  };

  var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g;

  // List of HTML entities for escaping.
  var htmlEntities = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    "\"": "&quot;",
    "'": "&#x27;"
  };

  var entityRe = new RegExp("[&<>\"']", "g");

  var escapeExpr = function escapeExpr(string) {
    if (string == null) {
      return "";
    }return ("" + string).replace(entityRe, function (match) {
      return htmlEntities[match];
    });
  };

  var counter = 0;

  // JavaScript micro-templating, similar to John Resig's implementation.
  // Underscore templating handles arbitrary delimiters, preserves whitespace,
  // and correctly escapes quotes within interpolated code.
  var tmpl = function tmpl(text, data) {
    var render;

    // Combine delimiters into one regular expression via alternation.
    var matcher = new RegExp([(settings.escape || noMatch).source, (settings.interpolate || noMatch).source, (settings.evaluate || noMatch).source].join("|") + "|$", "g");

    // Compile the template source, escaping string literals appropriately.
    var index = 0;
    var source = "__p+='";
    text.replace(matcher, function (match, escape, interpolate, evaluate, offset) {
      source += text.slice(index, offset).replace(escaper, function (match) {
        return "\\" + escapes[match];
      });

      if (escape) {
        source += "'+\n((__t=(" + escape + "))==null?'':escapeExpr(__t))+\n'";
      }
      if (interpolate) {
        source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
      }
      if (evaluate) {
        source += "';\n" + evaluate + "\n__p+='";
      }
      index = offset + match.length;
      return match;
    });
    source += "';\n";

    // If a variable is not specified, place data values in local scope.
    if (!settings.variable) source = "with(obj||{}){\n" + source + "}\n";

    source = "var __t,__p='',__j=Array.prototype.join," + "print=function(){__p+=__j.call(arguments,'');};\n" + source + "return __p;\n//# sourceURL=/microtemplates/source[" + counter++ + "]";

    try {
      render = new Function(settings.variable || "obj", "escapeExpr", source);
    } catch (e) {
      e.source = source;
      throw e;
    }

    if (data) {
      return render(data, escapeExpr);
    }var template = function template(data) {
      return render.call(this, data, escapeExpr);
    };

    // Provide the compiled function source as a convenience for precompilation.
    template.source = "function(" + (settings.variable || "obj") + "){\n" + source + "}";

    return template;
  };
  tmpl.settings = settings;

  if (typeof define !== "undefined" && define.amd) {
    define([], function () {
      return tmpl;
    }); // RequireJS
  } else if (typeof module !== "undefined" && module.exports) {
    module.exports = tmpl; // CommonJS
  } else {
    globals.microtemplate = tmpl; // <script>
  }
})(undefined);

},{}],"ui_container_plugin":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var UIObject = require("./ui_object");
var extend = require("./utils").extend;

var UIContainerPlugin = (function (_UIObject) {
  function UIContainerPlugin(options) {
    _classCallCheck(this, UIContainerPlugin);

    _get(Object.getPrototypeOf(UIContainerPlugin.prototype), "constructor", this).call(this, options);
    this.container = options.container;
    this.enabled = true;
    this.bindEvents();
  }

  _inherits(UIContainerPlugin, _UIObject);

  _createClass(UIContainerPlugin, {
    enable: {
      value: function enable() {
        if (!this.enabled) {
          this.bindEvents();
          this.$el.show();
          this.enabled = true;
        }
      }
    },
    disable: {
      value: function disable() {
        this.stopListening();
        this.$el.hide();
        this.enabled = false;
      }
    },
    bindEvents: {
      value: function bindEvents() {}
    },
    destroy: {
      value: function destroy() {
        this.remove();
      }
    }
  });

  return UIContainerPlugin;
})(UIObject);

UIContainerPlugin.extend = function (properties) {
  return extend(UIContainerPlugin, properties);
};

module.exports = UIContainerPlugin;

},{"./ui_object":"ui_object","./utils":49}],"ui_core_plugin":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

var UIObject = require("./ui_object");
var extend = require("./utils").extend;

var UICorePlugin = (function (_UIObject) {
  function UICorePlugin(core) {
    _classCallCheck(this, UICorePlugin);

    _get(Object.getPrototypeOf(UICorePlugin.prototype), "constructor", this).call(this, core);
    this.core = core;
    this.enabled = true;
    this.bindEvents();
    this.render();
  }

  _inherits(UICorePlugin, _UIObject);

  _createClass(UICorePlugin, {
    bindEvents: {
      value: function bindEvents() {}
    },
    getExternalInterface: {
      value: function getExternalInterface() {
        return {};
      }
    },
    enable: {
      value: function enable() {
        if (!this.enabled) {
          this.bindEvents();
          this.$el.show();
          this.enabled = true;
        }
      }
    },
    disable: {
      value: function disable() {
        this.stopListening();
        this.$el.hide();
        this.enabled = false;
      }
    },
    destroy: {
      value: function destroy() {
        this.remove();
      }
    },
    render: {
      value: function render() {
        this.$el.html(this.template());
        this.$el.append(this.styler.getStyleFor(this.name));
        this.core.$el.append(this.el);
        return this;
      }
    }
  });

  return UICorePlugin;
})(UIObject);

UICorePlugin.extend = function (properties) {
  return extend(UICorePlugin, properties);
};

module.exports = UICorePlugin;

},{"./ui_object":"ui_object","./utils":49}],"ui_object":[function(require,module,exports){
"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var key in props) { var prop = props[key]; prop.configurable = true; if (prop.value) prop.writable = true; } Object.defineProperties(target, props); } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(object, property, receiver) { var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { return get(parent, property, receiver); } } else if ("value" in desc && desc.writable) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } };

var _inherits = function (subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

// Copyright 2014 Globo.com Player authors. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

var $ = require("clappr-zepto");
var uniqueId = require("./utils").uniqueId;
var result = require("lodash.result");
var assign = require("lodash.assign");
var BaseObject = require("./base_object");

var delegateEventSplitter = /^(\S+)\s*(.*)$/;

var UIObject = (function (_BaseObject) {
  function UIObject(options) {
    _classCallCheck(this, UIObject);

    _get(Object.getPrototypeOf(UIObject.prototype), "constructor", this).call(this, options);
    this.cid = uniqueId("c");
    this._ensureElement();
    this.delegateEvents();
  }

  _inherits(UIObject, _BaseObject);

  _createClass(UIObject, {
    tagName: {
      get: function () {
        return "div";
      }
    },
    $: {
      value: function $(selector) {
        return this.$el.find(selector);
      }
    },
    render: {
      value: function render() {
        return this;
      }
    },
    remove: {
      value: function remove() {
        this.$el.remove();
        this.stopListening();
        return this;
      }
    },
    setElement: {
      value: function setElement(element, delegate) {
        if (this.$el) this.undelegateEvents();
        this.$el = element instanceof $ ? element : $(element);
        this.el = this.$el[0];
        if (delegate !== false) this.delegateEvents();
        return this;
      }
    },
    delegateEvents: {
      value: function delegateEvents(events) {
        if (!(events || (events = result(this, "events")))) {
          return this;
        }this.undelegateEvents();
        for (var key in events) {
          var method = events[key];
          if (method && method.constructor !== Function) method = this[events[key]];
          if (!method) continue;

          var match = key.match(delegateEventSplitter);
          var eventName = match[1],
              selector = match[2];
          //method = _.bind(method, this)
          eventName += ".delegateEvents" + this.cid;
          if (selector === "") {
            this.$el.on(eventName, method.bind(this));
          } else {
            this.$el.on(eventName, selector, method.bind(this));
          }
        }
        return this;
      }
    },
    undelegateEvents: {
      value: function undelegateEvents() {
        this.$el.off(".delegateEvents" + this.cid);
        return this;
      }
    },
    _ensureElement: {
      value: function _ensureElement() {
        if (!this.el) {
          var attrs = assign({}, result(this, "attributes"));
          if (this.id) attrs.id = result(this, "id");
          if (this.className) attrs["class"] = result(this, "className");
          var $el = $("<" + result(this, "tagName") + ">").attr(attrs);
          this.setElement($el, false);
        } else {
          this.setElement(result(this, "el"), false);
        }
      }
    }
  });

  return UIObject;
})(BaseObject);

module.exports = UIObject;

},{"./base_object":"base_object","./utils":49,"clappr-zepto":"zepto","lodash.assign":1,"lodash.result":30}],"zepto":[function(require,module,exports){
/* Zepto v1.1.4-80-ga9184b2 - zepto event ajax callbacks deferred touch selector ie - zeptojs.com/license */
var Zepto=function(){function D(t){return null==t?String(t):j[S.call(t)]||"object"}function L(t){return"function"==D(t)}function k(t){return null!=t&&t==t.window}function Z(t){return null!=t&&t.nodeType==t.DOCUMENT_NODE}function $(t){return"object"==D(t)}function F(t){return $(t)&&!k(t)&&Object.getPrototypeOf(t)==Object.prototype}function R(t){return"number"==typeof t.length}function q(t){return s.call(t,function(t){return null!=t})}function W(t){return t.length>0?n.fn.concat.apply([],t):t}function z(t){return t.replace(/::/g,"/").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/_/g,"-").toLowerCase()}function H(t){return t in c?c[t]:c[t]=new RegExp("(^|\\s)"+t+"(\\s|$)")}function _(t,e){return"number"!=typeof e||l[z(t)]?e:e+"px"}function I(t){var e,n;return f[t]||(e=u.createElement(t),u.body.appendChild(e),n=getComputedStyle(e,"").getPropertyValue("display"),e.parentNode.removeChild(e),"none"==n&&(n="block"),f[t]=n),f[t]}function U(t){return"children"in t?a.call(t.children):n.map(t.childNodes,function(t){return 1==t.nodeType?t:void 0})}function X(t,e){var n,i=t?t.length:0;for(n=0;i>n;n++)this[n]=t[n];this.length=i,this.selector=e||""}function B(n,i,r){for(e in i)r&&(F(i[e])||A(i[e]))?(F(i[e])&&!F(n[e])&&(n[e]={}),A(i[e])&&!A(n[e])&&(n[e]=[]),B(n[e],i[e],r)):i[e]!==t&&(n[e]=i[e])}function V(t,e){return null==e?n(t):n(t).filter(e)}function Y(t,e,n,i){return L(e)?e.call(t,n,i):e}function J(t,e,n){null==n?t.removeAttribute(e):t.setAttribute(e,n)}function G(e,n){var i=e.className||"",r=i&&i.baseVal!==t;return n===t?r?i.baseVal:i:void(r?i.baseVal=n:e.className=n)}function K(t){try{return t?"true"==t||("false"==t?!1:"null"==t?null:+t+""==t?+t:/^[\[\{]/.test(t)?n.parseJSON(t):t):t}catch(e){return t}}function Q(t,e){e(t);for(var n=0,i=t.childNodes.length;i>n;n++)Q(t.childNodes[n],e)}var t,e,n,i,N,P,r=[],o=r.concat,s=r.filter,a=r.slice,u=window.document,f={},c={},l={"column-count":1,columns:1,"font-weight":1,"line-height":1,opacity:1,"z-index":1,zoom:1},h=/^\s*<(\w+|!)[^>]*>/,p=/^<(\w+)\s*\/?>(?:<\/\1>|)$/,d=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,m=/^(?:body|html)$/i,g=/([A-Z])/g,v=["val","css","html","text","data","width","height","offset"],y=["after","prepend","before","append"],w=u.createElement("table"),x=u.createElement("tr"),b={tr:u.createElement("tbody"),tbody:w,thead:w,tfoot:w,td:x,th:x,"*":u.createElement("div")},E=/complete|loaded|interactive/,T=/^[\w-]*$/,j={},S=j.toString,C={},O=u.createElement("div"),M={tabindex:"tabIndex",readonly:"readOnly","for":"htmlFor","class":"className",maxlength:"maxLength",cellspacing:"cellSpacing",cellpadding:"cellPadding",rowspan:"rowSpan",colspan:"colSpan",usemap:"useMap",frameborder:"frameBorder",contenteditable:"contentEditable"},A=Array.isArray||function(t){return t instanceof Array};return C.matches=function(t,e){if(!e||!t||1!==t.nodeType)return!1;var n=t.webkitMatchesSelector||t.mozMatchesSelector||t.oMatchesSelector||t.matchesSelector;if(n)return n.call(t,e);var i,r=t.parentNode,o=!r;return o&&(r=O).appendChild(t),i=~C.qsa(r,e).indexOf(t),o&&O.removeChild(t),i},N=function(t){return t.replace(/-+(.)?/g,function(t,e){return e?e.toUpperCase():""})},P=function(t){return s.call(t,function(e,n){return t.indexOf(e)==n})},C.fragment=function(e,i,r){var o,s,f;return p.test(e)&&(o=n(u.createElement(RegExp.$1))),o||(e.replace&&(e=e.replace(d,"<$1></$2>")),i===t&&(i=h.test(e)&&RegExp.$1),i in b||(i="*"),f=b[i],f.innerHTML=""+e,o=n.each(a.call(f.childNodes),function(){f.removeChild(this)})),F(r)&&(s=n(o),n.each(r,function(t,e){v.indexOf(t)>-1?s[t](e):s.attr(t,e)})),o},C.Z=function(t,e){return new X(t,e)},C.isZ=function(t){return t instanceof C.Z},C.init=function(e,i){var r;if(!e)return C.Z();if("string"==typeof e)if(e=e.trim(),"<"==e[0]&&h.test(e))r=C.fragment(e,RegExp.$1,i),e=null;else{if(i!==t)return n(i).find(e);r=C.qsa(u,e)}else{if(L(e))return n(u).ready(e);if(C.isZ(e))return e;if(A(e))r=q(e);else if($(e))r=[e],e=null;else if(h.test(e))r=C.fragment(e.trim(),RegExp.$1,i),e=null;else{if(i!==t)return n(i).find(e);r=C.qsa(u,e)}}return C.Z(r,e)},n=function(t,e){return C.init(t,e)},n.extend=function(t){var e,n=a.call(arguments,1);return"boolean"==typeof t&&(e=t,t=n.shift()),n.forEach(function(n){B(t,n,e)}),t},C.qsa=function(t,e){var n,i="#"==e[0],r=!i&&"."==e[0],o=i||r?e.slice(1):e,s=T.test(o);return t.getElementById&&s&&i?(n=t.getElementById(o))?[n]:[]:1!==t.nodeType&&9!==t.nodeType&&11!==t.nodeType?[]:a.call(s&&!i&&t.getElementsByClassName?r?t.getElementsByClassName(o):t.getElementsByTagName(e):t.querySelectorAll(e))},n.contains=u.documentElement.contains?function(t,e){return t!==e&&t.contains(e)}:function(t,e){for(;e&&(e=e.parentNode);)if(e===t)return!0;return!1},n.type=D,n.isFunction=L,n.isWindow=k,n.isArray=A,n.isPlainObject=F,n.isEmptyObject=function(t){var e;for(e in t)return!1;return!0},n.inArray=function(t,e,n){return r.indexOf.call(e,t,n)},n.camelCase=N,n.trim=function(t){return null==t?"":String.prototype.trim.call(t)},n.uuid=0,n.support={},n.expr={},n.noop=function(){},n.map=function(t,e){var n,r,o,i=[];if(R(t))for(r=0;r<t.length;r++)n=e(t[r],r),null!=n&&i.push(n);else for(o in t)n=e(t[o],o),null!=n&&i.push(n);return W(i)},n.each=function(t,e){var n,i;if(R(t)){for(n=0;n<t.length;n++)if(e.call(t[n],n,t[n])===!1)return t}else for(i in t)if(e.call(t[i],i,t[i])===!1)return t;return t},n.grep=function(t,e){return s.call(t,e)},window.JSON&&(n.parseJSON=JSON.parse),n.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(t,e){j["[object "+e+"]"]=e.toLowerCase()}),n.fn={constructor:C.Z,length:0,forEach:r.forEach,reduce:r.reduce,push:r.push,sort:r.sort,splice:r.splice,indexOf:r.indexOf,concat:function(){var t,e,n=[];for(t=0;t<arguments.length;t++)e=arguments[t],n[t]=C.isZ(e)?e.toArray():e;return o.apply(C.isZ(this)?this.toArray():this,n)},map:function(t){return n(n.map(this,function(e,n){return t.call(e,n,e)}))},slice:function(){return n(a.apply(this,arguments))},ready:function(t){return E.test(u.readyState)&&u.body?t(n):u.addEventListener("DOMContentLoaded",function(){t(n)},!1),this},get:function(e){return e===t?a.call(this):this[e>=0?e:e+this.length]},toArray:function(){return this.get()},size:function(){return this.length},remove:function(){return this.each(function(){null!=this.parentNode&&this.parentNode.removeChild(this)})},each:function(t){return r.every.call(this,function(e,n){return t.call(e,n,e)!==!1}),this},filter:function(t){return L(t)?this.not(this.not(t)):n(s.call(this,function(e){return C.matches(e,t)}))},add:function(t,e){return n(P(this.concat(n(t,e))))},is:function(t){return this.length>0&&C.matches(this[0],t)},not:function(e){var i=[];if(L(e)&&e.call!==t)this.each(function(t){e.call(this,t)||i.push(this)});else{var r="string"==typeof e?this.filter(e):R(e)&&L(e.item)?a.call(e):n(e);this.forEach(function(t){r.indexOf(t)<0&&i.push(t)})}return n(i)},has:function(t){return this.filter(function(){return $(t)?n.contains(this,t):n(this).find(t).size()})},eq:function(t){return-1===t?this.slice(t):this.slice(t,+t+1)},first:function(){var t=this[0];return t&&!$(t)?t:n(t)},last:function(){var t=this[this.length-1];return t&&!$(t)?t:n(t)},find:function(t){var e,i=this;return e=t?"object"==typeof t?n(t).filter(function(){var t=this;return r.some.call(i,function(e){return n.contains(e,t)})}):1==this.length?n(C.qsa(this[0],t)):this.map(function(){return C.qsa(this,t)}):n()},closest:function(t,e){var i=this[0],r=!1;for("object"==typeof t&&(r=n(t));i&&!(r?r.indexOf(i)>=0:C.matches(i,t));)i=i!==e&&!Z(i)&&i.parentNode;return n(i)},parents:function(t){for(var e=[],i=this;i.length>0;)i=n.map(i,function(t){return(t=t.parentNode)&&!Z(t)&&e.indexOf(t)<0?(e.push(t),t):void 0});return V(e,t)},parent:function(t){return V(P(this.pluck("parentNode")),t)},children:function(t){return V(this.map(function(){return U(this)}),t)},contents:function(){return this.map(function(){return this.contentDocument||a.call(this.childNodes)})},siblings:function(t){return V(this.map(function(t,e){return s.call(U(e.parentNode),function(t){return t!==e})}),t)},empty:function(){return this.each(function(){this.innerHTML=""})},pluck:function(t){return n.map(this,function(e){return e[t]})},show:function(){return this.each(function(){"none"==this.style.display&&(this.style.display=""),"none"==getComputedStyle(this,"").getPropertyValue("display")&&(this.style.display=I(this.nodeName))})},replaceWith:function(t){return this.before(t).remove()},wrap:function(t){var e=L(t);if(this[0]&&!e)var i=n(t).get(0),r=i.parentNode||this.length>1;return this.each(function(o){n(this).wrapAll(e?t.call(this,o):r?i.cloneNode(!0):i)})},wrapAll:function(t){if(this[0]){n(this[0]).before(t=n(t));for(var e;(e=t.children()).length;)t=e.first();n(t).append(this)}return this},wrapInner:function(t){var e=L(t);return this.each(function(i){var r=n(this),o=r.contents(),s=e?t.call(this,i):t;o.length?o.wrapAll(s):r.append(s)})},unwrap:function(){return this.parent().each(function(){n(this).replaceWith(n(this).children())}),this},clone:function(){return this.map(function(){return this.cloneNode(!0)})},hide:function(){return this.css("display","none")},toggle:function(e){return this.each(function(){var i=n(this);(e===t?"none"==i.css("display"):e)?i.show():i.hide()})},prev:function(t){return n(this.pluck("previousElementSibling")).filter(t||"*")},next:function(t){return n(this.pluck("nextElementSibling")).filter(t||"*")},html:function(t){return 0 in arguments?this.each(function(e){var i=this.innerHTML;n(this).empty().append(Y(this,t,e,i))}):0 in this?this[0].innerHTML:null},text:function(t){return 0 in arguments?this.each(function(e){var n=Y(this,t,e,this.textContent);this.textContent=null==n?"":""+n}):0 in this?this[0].textContent:null},attr:function(n,i){var r;return"string"!=typeof n||1 in arguments?this.each(function(t){if(1===this.nodeType)if($(n))for(e in n)J(this,e,n[e]);else J(this,n,Y(this,i,t,this.getAttribute(n)))}):this.length&&1===this[0].nodeType?!(r=this[0].getAttribute(n))&&n in this[0]?this[0][n]:r:t},removeAttr:function(t){return this.each(function(){1===this.nodeType&&t.split(" ").forEach(function(t){J(this,t)},this)})},prop:function(t,e){return t=M[t]||t,1 in arguments?this.each(function(n){this[t]=Y(this,e,n,this[t])}):this[0]&&this[0][t]},data:function(e,n){var i="data-"+e.replace(g,"-$1").toLowerCase(),r=1 in arguments?this.attr(i,n):this.attr(i);return null!==r?K(r):t},val:function(t){return 0 in arguments?this.each(function(e){this.value=Y(this,t,e,this.value)}):this[0]&&(this[0].multiple?n(this[0]).find("option").filter(function(){return this.selected}).pluck("value"):this[0].value)},offset:function(t){if(t)return this.each(function(e){var i=n(this),r=Y(this,t,e,i.offset()),o=i.offsetParent().offset(),s={top:r.top-o.top,left:r.left-o.left};"static"==i.css("position")&&(s.position="relative"),i.css(s)});if(!this.length)return null;if(!n.contains(u.documentElement,this[0]))return{top:0,left:0};var e=this[0].getBoundingClientRect();return{left:e.left+window.pageXOffset,top:e.top+window.pageYOffset,width:Math.round(e.width),height:Math.round(e.height)}},css:function(t,i){if(arguments.length<2){var r,o=this[0];if(!o)return;if(r=getComputedStyle(o,""),"string"==typeof t)return o.style[N(t)]||r.getPropertyValue(t);if(A(t)){var s={};return n.each(t,function(t,e){s[e]=o.style[N(e)]||r.getPropertyValue(e)}),s}}var a="";if("string"==D(t))i||0===i?a=z(t)+":"+_(t,i):this.each(function(){this.style.removeProperty(z(t))});else for(e in t)t[e]||0===t[e]?a+=z(e)+":"+_(e,t[e])+";":this.each(function(){this.style.removeProperty(z(e))});return this.each(function(){this.style.cssText+=";"+a})},index:function(t){return t?this.indexOf(n(t)[0]):this.parent().children().indexOf(this[0])},hasClass:function(t){return t?r.some.call(this,function(t){return this.test(G(t))},H(t)):!1},addClass:function(t){return t?this.each(function(e){if("className"in this){i=[];var r=G(this),o=Y(this,t,e,r);o.split(/\s+/g).forEach(function(t){n(this).hasClass(t)||i.push(t)},this),i.length&&G(this,r+(r?" ":"")+i.join(" "))}}):this},removeClass:function(e){return this.each(function(n){if("className"in this){if(e===t)return G(this,"");i=G(this),Y(this,e,n,i).split(/\s+/g).forEach(function(t){i=i.replace(H(t)," ")}),G(this,i.trim())}})},toggleClass:function(e,i){return e?this.each(function(r){var o=n(this),s=Y(this,e,r,G(this));s.split(/\s+/g).forEach(function(e){(i===t?!o.hasClass(e):i)?o.addClass(e):o.removeClass(e)})}):this},scrollTop:function(e){if(this.length){var n="scrollTop"in this[0];return e===t?n?this[0].scrollTop:this[0].pageYOffset:this.each(n?function(){this.scrollTop=e}:function(){this.scrollTo(this.scrollX,e)})}},scrollLeft:function(e){if(this.length){var n="scrollLeft"in this[0];return e===t?n?this[0].scrollLeft:this[0].pageXOffset:this.each(n?function(){this.scrollLeft=e}:function(){this.scrollTo(e,this.scrollY)})}},position:function(){if(this.length){var t=this[0],e=this.offsetParent(),i=this.offset(),r=m.test(e[0].nodeName)?{top:0,left:0}:e.offset();return i.top-=parseFloat(n(t).css("margin-top"))||0,i.left-=parseFloat(n(t).css("margin-left"))||0,r.top+=parseFloat(n(e[0]).css("border-top-width"))||0,r.left+=parseFloat(n(e[0]).css("border-left-width"))||0,{top:i.top-r.top,left:i.left-r.left}}},offsetParent:function(){return this.map(function(){for(var t=this.offsetParent||u.body;t&&!m.test(t.nodeName)&&"static"==n(t).css("position");)t=t.offsetParent;return t})}},n.fn.detach=n.fn.remove,["width","height"].forEach(function(e){var i=e.replace(/./,function(t){return t[0].toUpperCase()});n.fn[e]=function(r){var o,s=this[0];return r===t?k(s)?s["inner"+i]:Z(s)?s.documentElement["scroll"+i]:(o=this.offset())&&o[e]:this.each(function(t){s=n(this),s.css(e,Y(this,r,t,s[e]()))})}}),y.forEach(function(t,e){var i=e%2;n.fn[t]=function(){var t,o,r=n.map(arguments,function(e){return t=D(e),"object"==t||"array"==t||null==e?e:C.fragment(e)}),s=this.length>1;return r.length<1?this:this.each(function(t,a){o=i?a:a.parentNode,a=0==e?a.nextSibling:1==e?a.firstChild:2==e?a:null;var f=n.contains(u.documentElement,o);r.forEach(function(t){if(s)t=t.cloneNode(!0);else if(!o)return n(t).remove();o.insertBefore(t,a),f&&Q(t,function(t){null==t.nodeName||"SCRIPT"!==t.nodeName.toUpperCase()||t.type&&"text/javascript"!==t.type||t.src||window.eval.call(window,t.innerHTML)})})})},n.fn[i?t+"To":"insert"+(e?"Before":"After")]=function(e){return n(e)[t](this),this}}),C.Z.prototype=X.prototype=n.fn,C.uniq=P,C.deserializeValue=K,n.zepto=C,n}();window.Zepto=Zepto,void 0===window.$&&(window.$=Zepto),function(t){function l(t){return t._zid||(t._zid=e++)}function h(t,e,n,i){if(e=p(e),e.ns)var r=d(e.ns);return(s[l(t)]||[]).filter(function(t){return!(!t||e.e&&t.e!=e.e||e.ns&&!r.test(t.ns)||n&&l(t.fn)!==l(n)||i&&t.sel!=i)})}function p(t){var e=(""+t).split(".");return{e:e[0],ns:e.slice(1).sort().join(" ")}}function d(t){return new RegExp("(?:^| )"+t.replace(" "," .* ?")+"(?: |$)")}function m(t,e){return t.del&&!u&&t.e in f||!!e}function g(t){return c[t]||u&&f[t]||t}function v(e,i,r,o,a,u,f){var h=l(e),d=s[h]||(s[h]=[]);i.split(/\s/).forEach(function(i){if("ready"==i)return t(document).ready(r);var s=p(i);s.fn=r,s.sel=a,s.e in c&&(r=function(e){var n=e.relatedTarget;return!n||n!==this&&!t.contains(this,n)?s.fn.apply(this,arguments):void 0}),s.del=u;var l=u||r;s.proxy=function(t){if(t=T(t),!t.isImmediatePropagationStopped()){t.data=o;var i=l.apply(e,t._args==n?[t]:[t].concat(t._args));return i===!1&&(t.preventDefault(),t.stopPropagation()),i}},s.i=d.length,d.push(s),"addEventListener"in e&&e.addEventListener(g(s.e),s.proxy,m(s,f))})}function y(t,e,n,i,r){var o=l(t);(e||"").split(/\s/).forEach(function(e){h(t,e,n,i).forEach(function(e){delete s[o][e.i],"removeEventListener"in t&&t.removeEventListener(g(e.e),e.proxy,m(e,r))})})}function T(e,i){return(i||!e.isDefaultPrevented)&&(i||(i=e),t.each(E,function(t,n){var r=i[t];e[t]=function(){return this[n]=w,r&&r.apply(i,arguments)},e[n]=x}),(i.defaultPrevented!==n?i.defaultPrevented:"returnValue"in i?i.returnValue===!1:i.getPreventDefault&&i.getPreventDefault())&&(e.isDefaultPrevented=w)),e}function j(t){var e,i={originalEvent:t};for(e in t)b.test(e)||t[e]===n||(i[e]=t[e]);return T(i,t)}var n,e=1,i=Array.prototype.slice,r=t.isFunction,o=function(t){return"string"==typeof t},s={},a={},u="onfocusin"in window,f={focus:"focusin",blur:"focusout"},c={mouseenter:"mouseover",mouseleave:"mouseout"};a.click=a.mousedown=a.mouseup=a.mousemove="MouseEvents",t.event={add:v,remove:y},t.proxy=function(e,n){var s=2 in arguments&&i.call(arguments,2);if(r(e)){var a=function(){return e.apply(n,s?s.concat(i.call(arguments)):arguments)};return a._zid=l(e),a}if(o(n))return s?(s.unshift(e[n],e),t.proxy.apply(null,s)):t.proxy(e[n],e);throw new TypeError("expected function")},t.fn.bind=function(t,e,n){return this.on(t,e,n)},t.fn.unbind=function(t,e){return this.off(t,e)},t.fn.one=function(t,e,n,i){return this.on(t,e,n,i,1)};var w=function(){return!0},x=function(){return!1},b=/^([A-Z]|returnValue$|layer[XY]$)/,E={preventDefault:"isDefaultPrevented",stopImmediatePropagation:"isImmediatePropagationStopped",stopPropagation:"isPropagationStopped"};t.fn.delegate=function(t,e,n){return this.on(e,t,n)},t.fn.undelegate=function(t,e,n){return this.off(e,t,n)},t.fn.live=function(e,n){return t(document.body).delegate(this.selector,e,n),this},t.fn.die=function(e,n){return t(document.body).undelegate(this.selector,e,n),this},t.fn.on=function(e,s,a,u,f){var c,l,h=this;return e&&!o(e)?(t.each(e,function(t,e){h.on(t,s,a,e,f)}),h):(o(s)||r(u)||u===!1||(u=a,a=s,s=n),(u===n||a===!1)&&(u=a,a=n),u===!1&&(u=x),h.each(function(n,r){f&&(c=function(t){return y(r,t.type,u),u.apply(this,arguments)}),s&&(l=function(e){var n,o=t(e.target).closest(s,r).get(0);return o&&o!==r?(n=t.extend(j(e),{currentTarget:o,liveFired:r}),(c||u).apply(o,[n].concat(i.call(arguments,1)))):void 0}),v(r,e,u,a,s,l||c)}))},t.fn.off=function(e,i,s){var a=this;return e&&!o(e)?(t.each(e,function(t,e){a.off(t,i,e)}),a):(o(i)||r(s)||s===!1||(s=i,i=n),s===!1&&(s=x),a.each(function(){y(this,e,s,i)}))},t.fn.trigger=function(e,n){return e=o(e)||t.isPlainObject(e)?t.Event(e):T(e),e._args=n,this.each(function(){e.type in f&&"function"==typeof this[e.type]?this[e.type]():"dispatchEvent"in this?this.dispatchEvent(e):t(this).triggerHandler(e,n)})},t.fn.triggerHandler=function(e,n){var i,r;return this.each(function(s,a){i=j(o(e)?t.Event(e):e),i._args=n,i.target=a,t.each(h(a,e.type||e),function(t,e){return r=e.proxy(i),i.isImmediatePropagationStopped()?!1:void 0})}),r},"focusin focusout focus blur load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select keydown keypress keyup error".split(" ").forEach(function(e){t.fn[e]=function(t){return 0 in arguments?this.bind(e,t):this.trigger(e)}}),t.Event=function(t,e){o(t)||(e=t,t=e.type);var n=document.createEvent(a[t]||"Events"),i=!0;if(e)for(var r in e)"bubbles"==r?i=!!e[r]:n[r]=e[r];return n.initEvent(t,i,!0),T(n)}}(Zepto),function(t){function h(e,n,i){var r=t.Event(n);return t(e).trigger(r,i),!r.isDefaultPrevented()}function p(t,e,i,r){return t.global?h(e||n,i,r):void 0}function d(e){e.global&&0===t.active++&&p(e,null,"ajaxStart")}function m(e){e.global&&!--t.active&&p(e,null,"ajaxStop")}function g(t,e){var n=e.context;return e.beforeSend.call(n,t,e)===!1||p(e,n,"ajaxBeforeSend",[t,e])===!1?!1:void p(e,n,"ajaxSend",[t,e])}function v(t,e,n,i){var r=n.context,o="success";n.success.call(r,t,o,e),i&&i.resolveWith(r,[t,o,e]),p(n,r,"ajaxSuccess",[e,n,t]),w(o,e,n)}function y(t,e,n,i,r){var o=i.context;i.error.call(o,n,e,t),r&&r.rejectWith(o,[n,e,t]),p(i,o,"ajaxError",[n,i,t||e]),w(e,n,i)}function w(t,e,n){var i=n.context;n.complete.call(i,e,t),p(n,i,"ajaxComplete",[e,n]),m(n)}function x(){}function b(t){return t&&(t=t.split(";",2)[0]),t&&(t==f?"html":t==u?"json":s.test(t)?"script":a.test(t)&&"xml")||"text"}function E(t,e){return""==e?t:(t+"&"+e).replace(/[&?]{1,2}/,"?")}function T(e){e.processData&&e.data&&"string"!=t.type(e.data)&&(e.data=t.param(e.data,e.traditional)),!e.data||e.type&&"GET"!=e.type.toUpperCase()||(e.url=E(e.url,e.data),e.data=void 0)}function j(e,n,i,r){return t.isFunction(n)&&(r=i,i=n,n=void 0),t.isFunction(i)||(r=i,i=void 0),{url:e,data:n,success:i,dataType:r}}function C(e,n,i,r){var o,s=t.isArray(n),a=t.isPlainObject(n);t.each(n,function(n,u){o=t.type(u),r&&(n=i?r:r+"["+(a||"object"==o||"array"==o?n:"")+"]"),!r&&s?e.add(u.name,u.value):"array"==o||!i&&"object"==o?C(e,u,i,n):e.add(n,u)})}var i,r,e=0,n=window.document,o=/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,s=/^(?:text|application)\/javascript/i,a=/^(?:text|application)\/xml/i,u="application/json",f="text/html",c=/^\s*$/,l=n.createElement("a");l.href=window.location.href,t.active=0,t.ajaxJSONP=function(i,r){if(!("type"in i))return t.ajax(i);var f,h,o=i.jsonpCallback,s=(t.isFunction(o)?o():o)||"jsonp"+ ++e,a=n.createElement("script"),u=window[s],c=function(e){t(a).triggerHandler("error",e||"abort")},l={abort:c};return r&&r.promise(l),t(a).on("load error",function(e,n){clearTimeout(h),t(a).off().remove(),"error"!=e.type&&f?v(f[0],l,i,r):y(null,n||"error",l,i,r),window[s]=u,f&&t.isFunction(u)&&u(f[0]),u=f=void 0}),g(l,i)===!1?(c("abort"),l):(window[s]=function(){f=arguments},a.src=i.url.replace(/\?(.+)=\?/,"?$1="+s),n.head.appendChild(a),i.timeout>0&&(h=setTimeout(function(){c("timeout")},i.timeout)),l)},t.ajaxSettings={type:"GET",beforeSend:x,success:x,error:x,complete:x,context:null,global:!0,xhr:function(){return new window.XMLHttpRequest},accepts:{script:"text/javascript, application/javascript, application/x-javascript",json:u,xml:"application/xml, text/xml",html:f,text:"text/plain"},crossDomain:!1,timeout:0,processData:!0,cache:!0},t.ajax=function(e){var a,u,o=t.extend({},e||{}),s=t.Deferred&&t.Deferred();for(i in t.ajaxSettings)void 0===o[i]&&(o[i]=t.ajaxSettings[i]);d(o),o.crossDomain||(a=n.createElement("a"),a.href=o.url,a.href=a.href,o.crossDomain=l.protocol+"//"+l.host!=a.protocol+"//"+a.host),o.url||(o.url=window.location.toString()),(u=o.url.indexOf("#"))>-1&&(o.url=o.url.slice(0,u)),T(o);var f=o.dataType,h=/\?.+=\?/.test(o.url);if(h&&(f="jsonp"),o.cache!==!1&&(e&&e.cache===!0||"script"!=f&&"jsonp"!=f)||(o.url=E(o.url,"_="+Date.now())),"jsonp"==f)return h||(o.url=E(o.url,o.jsonp?o.jsonp+"=?":o.jsonp===!1?"":"callback=?")),t.ajaxJSONP(o,s);var N,p=o.accepts[f],m={},w=function(t,e){m[t.toLowerCase()]=[t,e]},j=/^([\w-]+:)\/\//.test(o.url)?RegExp.$1:window.location.protocol,S=o.xhr(),C=S.setRequestHeader;if(s&&s.promise(S),o.crossDomain||w("X-Requested-With","XMLHttpRequest"),w("Accept",p||"*/*"),(p=o.mimeType||p)&&(p.indexOf(",")>-1&&(p=p.split(",",2)[0]),S.overrideMimeType&&S.overrideMimeType(p)),(o.contentType||o.contentType!==!1&&o.data&&"GET"!=o.type.toUpperCase())&&w("Content-Type",o.contentType||"application/x-www-form-urlencoded"),o.headers)for(r in o.headers)w(r,o.headers[r]);if(S.setRequestHeader=w,S.onreadystatechange=function(){if(4==S.readyState){S.onreadystatechange=x,clearTimeout(N);var e,n=!1;if(S.status>=200&&S.status<300||304==S.status||0==S.status&&"file:"==j){f=f||b(o.mimeType||S.getResponseHeader("content-type")),e=S.responseText;try{"script"==f?(1,eval)(e):"xml"==f?e=S.responseXML:"json"==f&&(e=c.test(e)?null:t.parseJSON(e))}catch(i){n=i}n?y(n,"parsererror",S,o,s):v(e,S,o,s)}else y(S.statusText||null,S.status?"error":"abort",S,o,s)}},g(S,o)===!1)return S.abort(),y(null,"abort",S,o,s),S;if(o.xhrFields)for(r in o.xhrFields)S[r]=o.xhrFields[r];var P="async"in o?o.async:!0;S.open(o.type,o.url,P,o.username,o.password);for(r in m)C.apply(S,m[r]);return o.timeout>0&&(N=setTimeout(function(){S.onreadystatechange=x,S.abort(),y(null,"timeout",S,o,s)},o.timeout)),S.send(o.data?o.data:null),S},t.get=function(){return t.ajax(j.apply(null,arguments))},t.post=function(){var e=j.apply(null,arguments);return e.type="POST",t.ajax(e)},t.getJSON=function(){var e=j.apply(null,arguments);return e.dataType="json",t.ajax(e)},t.fn.load=function(e,n,i){if(!this.length)return this;var a,r=this,s=e.split(/\s/),u=j(e,n,i),f=u.success;return s.length>1&&(u.url=s[0],a=s[1]),u.success=function(e){r.html(a?t("<div>").html(e.replace(o,"")).find(a):e),f&&f.apply(r,arguments)},t.ajax(u),this};var S=encodeURIComponent;t.param=function(e,n){var i=[];return i.add=function(e,n){t.isFunction(n)&&(n=n()),null==n&&(n=""),this.push(S(e)+"="+S(n))},C(i,e,n),i.join("&").replace(/%20/g,"+")}}(Zepto),function(t){t.Callbacks=function(e){e=t.extend({},e);var n,i,r,o,s,a,u=[],f=!e.once&&[],c=function(t){for(n=e.memory&&t,i=!0,a=o||0,o=0,s=u.length,r=!0;u&&s>a;++a)if(u[a].apply(t[0],t[1])===!1&&e.stopOnFalse){n=!1;break}r=!1,u&&(f?f.length&&c(f.shift()):n?u.length=0:l.disable())},l={add:function(){if(u){var i=u.length,a=function(n){t.each(n,function(t,n){"function"==typeof n?e.unique&&l.has(n)||u.push(n):n&&n.length&&"string"!=typeof n&&a(n)})};a(arguments),r?s=u.length:n&&(o=i,c(n))}return this},remove:function(){return u&&t.each(arguments,function(e,n){for(var i;(i=t.inArray(n,u,i))>-1;)u.splice(i,1),r&&(s>=i&&--s,a>=i&&--a)}),this},has:function(e){return!(!u||!(e?t.inArray(e,u)>-1:u.length))},empty:function(){return s=u.length=0,this},disable:function(){return u=f=n=void 0,this},disabled:function(){return!u},lock:function(){return f=void 0,n||l.disable(),this},locked:function(){return!f},fireWith:function(t,e){return!u||i&&!f||(e=e||[],e=[t,e.slice?e.slice():e],r?f.push(e):c(e)),this},fire:function(){return l.fireWith(this,arguments)},fired:function(){return!!i}};return l}}(Zepto),function(t){function n(e){var i=[["resolve","done",t.Callbacks({once:1,memory:1}),"resolved"],["reject","fail",t.Callbacks({once:1,memory:1}),"rejected"],["notify","progress",t.Callbacks({memory:1})]],r="pending",o={state:function(){return r},always:function(){return s.done(arguments).fail(arguments),this},then:function(){var e=arguments;return n(function(n){t.each(i,function(i,r){var a=t.isFunction(e[i])&&e[i];s[r[1]](function(){var e=a&&a.apply(this,arguments);if(e&&t.isFunction(e.promise))e.promise().done(n.resolve).fail(n.reject).progress(n.notify);else{var i=this===o?n.promise():this,s=a?[e]:arguments;n[r[0]+"With"](i,s)}})}),e=null}).promise()},promise:function(e){return null!=e?t.extend(e,o):o}},s={};return t.each(i,function(t,e){var n=e[2],a=e[3];o[e[1]]=n.add,a&&n.add(function(){r=a},i[1^t][2].disable,i[2][2].lock),s[e[0]]=function(){return s[e[0]+"With"](this===s?o:this,arguments),this},s[e[0]+"With"]=n.fireWith}),o.promise(s),e&&e.call(s,s),s}var e=Array.prototype.slice;t.when=function(i){var f,c,l,r=e.call(arguments),o=r.length,s=0,a=1!==o||i&&t.isFunction(i.promise)?o:0,u=1===a?i:n(),h=function(t,n,i){return function(r){n[t]=this,i[t]=arguments.length>1?e.call(arguments):r,i===f?u.notifyWith(n,i):--a||u.resolveWith(n,i)}};if(o>1)for(f=new Array(o),c=new Array(o),l=new Array(o);o>s;++s)r[s]&&t.isFunction(r[s].promise)?r[s].promise().done(h(s,l,r)).fail(u.reject).progress(h(s,c,f)):--a;return a||u.resolveWith(l,r),u.promise()},t.Deferred=n}(Zepto),function(t){function u(t,e,n,i){return Math.abs(t-e)>=Math.abs(n-i)?t-e>0?"Left":"Right":n-i>0?"Up":"Down"}function f(){o=null,e.last&&(e.el.trigger("longTap"),e={})}function c(){o&&clearTimeout(o),o=null}function l(){n&&clearTimeout(n),i&&clearTimeout(i),r&&clearTimeout(r),o&&clearTimeout(o),n=i=r=o=null,e={}}function h(t){return("touch"==t.pointerType||t.pointerType==t.MSPOINTER_TYPE_TOUCH)&&t.isPrimary}function p(t,e){return t.type=="pointer"+e||t.type.toLowerCase()=="mspointer"+e}var n,i,r,o,a,e={},s=750;t(document).ready(function(){var d,m,y,w,g=0,v=0;"MSGesture"in window&&(a=new MSGesture,a.target=document.body),t(document).bind("MSGestureEnd",function(t){var n=t.velocityX>1?"Right":t.velocityX<-1?"Left":t.velocityY>1?"Down":t.velocityY<-1?"Up":null;n&&(e.el.trigger("swipe"),e.el.trigger("swipe"+n))}).on("touchstart MSPointerDown pointerdown",function(i){(!(w=p(i,"down"))||h(i))&&(y=w?i:i.touches[0],i.touches&&1===i.touches.length&&e.x2&&(e.x2=void 0,e.y2=void 0),d=Date.now(),m=d-(e.last||d),e.el=t("tagName"in y.target?y.target:y.target.parentNode),n&&clearTimeout(n),e.x1=y.pageX,e.y1=y.pageY,m>0&&250>=m&&(e.isDoubleTap=!0),e.last=d,o=setTimeout(f,s),a&&w&&a.addPointer(i.pointerId))}).on("touchmove MSPointerMove pointermove",function(t){(!(w=p(t,"move"))||h(t))&&(y=w?t:t.touches[0],c(),e.x2=y.pageX,e.y2=y.pageY,g+=Math.abs(e.x1-e.x2),v+=Math.abs(e.y1-e.y2))}).on("touchend MSPointerUp pointerup",function(o){(!(w=p(o,"up"))||h(o))&&(c(),e.x2&&Math.abs(e.x1-e.x2)>30||e.y2&&Math.abs(e.y1-e.y2)>30?r=setTimeout(function(){e.el.trigger("swipe"),e.el.trigger("swipe"+u(e.x1,e.x2,e.y1,e.y2)),e={}},0):"last"in e&&(30>g&&30>v?i=setTimeout(function(){var i=t.Event("tap");i.cancelTouch=l,e.el.trigger(i),e.isDoubleTap?(e.el&&e.el.trigger("doubleTap"),e={}):n=setTimeout(function(){n=null,e.el&&e.el.trigger("singleTap"),e={}},250)},0):e={}),g=v=0)}).on("touchcancel MSPointerCancel pointercancel",l),t(window).on("scroll",l)}),["swipe","swipeLeft","swipeRight","swipeUp","swipeDown","doubleTap","tap","singleTap","longTap"].forEach(function(e){t.fn[e]=function(t){return this.on(e,t)}})}(Zepto),function(t){function r(e){return e=t(e),!(!e.width()&&!e.height())&&"none"!==e.css("display")}function f(t,e){t=t.replace(/=#\]/g,'="#"]');var n,i,r=s.exec(t);if(r&&r[2]in o&&(n=o[r[2]],i=r[3],t=r[1],i)){var a=Number(i);i=isNaN(a)?i.replace(/^["']|["']$/g,""):a}return e(t,n,i)}var e=t.zepto,n=e.qsa,i=e.matches,o=t.expr[":"]={visible:function(){return r(this)?this:void 0},hidden:function(){return r(this)?void 0:this},selected:function(){return this.selected?this:void 0},checked:function(){return this.checked?this:void 0},parent:function(){return this.parentNode},first:function(t){return 0===t?this:void 0},last:function(t,e){return t===e.length-1?this:void 0},eq:function(t,e,n){return t===n?this:void 0},contains:function(e,n,i){return t(this).text().indexOf(i)>-1?this:void 0},has:function(t,n,i){return e.qsa(this,i).length?this:void 0}},s=new RegExp("(.*):(\\w+)(?:\\(([^)]+)\\))?$\\s*"),a=/^\s*>/,u="Zepto"+ +new Date;e.qsa=function(i,r){return f(r,function(o,s,f){try{var c;!o&&s?o="*":a.test(o)&&(c=t(i).addClass(u),o="."+u+" "+o);var l=n(i,o)}catch(h){throw console.error("error performing selector: %o",r),h}finally{c&&c.removeClass(u)}return s?e.uniq(t.map(l,function(t,e){return s.call(t,e,l,f)})):l})},e.matches=function(t,e){return f(e,function(e,n,r){return!(e&&!i(t,e)||n&&n.call(t,null,r)!==t)})}}(Zepto),function(){try{getComputedStyle(void 0)}catch(t){var e=getComputedStyle;window.getComputedStyle=function(t){try{return e(t)}catch(n){return null}}}}();
module.exports = Zepto;

},{}]},{},["clappr"])

})();
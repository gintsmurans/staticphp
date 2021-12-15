/* eslint no-restricted-properties: [1] */

// Avoid `console` errors in browsers that lack a console.
(function () {
    let method;
    const noop = function () {};
    const methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn',
    ];
    let { length } = methods;
    const console = (window.console ? window.console : {});

    while (length) {
        length -= 1;
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Browser addons
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector
        || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
        let el = this;

        do {
            if (Element.prototype.matches.call(el, s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

(function () {
    if (typeof window.CustomEvent === 'function') return; // If not IE

    function CustomEvent(event, _params) {
        const params = _params || { bubbles: false, cancelable: false, detail: undefined };
        const evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);

        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;
    window.CustomEvent = CustomEvent;

    if (typeof window.Event !== 'function') {
        window.Event = CustomEvent;
        window.Event.prototype = Event;
    }
}());

(function () {
    if (typeof Number === 'undefined') {
        window.Number = {};
    }
    if (typeof Number.isNaN === 'function') {
        return;
    }

    window.Number.isNaN = window.isNaN;
}());

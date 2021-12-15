import 'core-js/es/object/assign';
import 'customPolyfill';
import { Utils } from 'utils';

// Assign stuff to global context
window.Utils = Utils;

// Require few other libraries
require('bootstrap');

// -- BASE --
// import init from './base/js/default.js';
// init($);

// Export all
export {
    Utils,
};

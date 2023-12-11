import 'core-js/es/object/assign';
import 'base/customPolyfill';

import { Utils } from 'base/utils';

// Assign stuff to global context
window.Utils = Utils;

// Require few other libraries
require('bootstrap');

// -- BASE --
// import init from './base/js/default.js';
// init($);

// Export all
export { Utils };

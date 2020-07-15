import $ from 'jquery';
import { Utils } from 'utils';


// Assign stuff to global context
window.$ = $;
window.jQuery = $;

// Require few other libraries
require('popper.js');
require('bootstrap');

// -- BASE --
// import init from './base/js/default.js';
// init($);

// Export all
export {
    $,
    Utils,
};

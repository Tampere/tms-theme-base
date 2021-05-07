/**
 * Theme JS building
 */
import '@babel/polyfill';
import Theme from './theme';
import './fonts';

// Export the theme controller for global usage.
window.Theme = Theme;

import '../styles/main.scss';

import '../fonts';
import '../icons';
import '../images';
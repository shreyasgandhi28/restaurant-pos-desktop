import axios from 'axios';
import { Ziggy } from './ziggy';
import _ from 'lodash';

// Set up Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Make lodash available globally
window._ = _;

// Set up Ziggy routes
window.Ziggy = Ziggy;

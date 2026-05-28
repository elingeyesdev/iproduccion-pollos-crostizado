import axios from 'axios';
import 'bootstrap/dist/js/bootstrap.bundle';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

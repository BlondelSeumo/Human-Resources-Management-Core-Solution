/**
 * This bootstrap file is used for both frontend and core
 */

import _ from 'lodash'
import axios from 'axios'
import Swal from 'sweetalert2';
import $ from 'jquery';
import 'popper.js'; // Required for BS4
import 'bootstrap';

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = $;
window.Swal = Swal;
window._ = _; // Lodash

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    const code = error.response.status;
    if (404 === code || 401 === code) {
        if (error.response.data?.status == false || error.response.data?.status == 'false'){
            return Promise.reject(error);
        } else {
            swal(error.response, code === 401);
        }
    } else {
        return Promise.reject(error);
    }
});

function swal(response, showConfirm = true) {
    Swal.fire({
        title: "Error!!",
        text: response.data.message,
        showCancelButton: true,
        showConfirmButton: showConfirm,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Login",
        imageWidth: 100,
        imageHeight: 70,
        imageAlt: 'Error',
    }).then(function(response){
        if (!response.dismiss) {
            window.location = window.localStorage.getItem('base_url')+'/admin/users/login';
        }
    });
}


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
if (document.querySelector('meta[name="csrf-token"]')) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

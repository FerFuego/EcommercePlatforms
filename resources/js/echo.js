import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('[Echo] Connected to Pusher successfully');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('[Echo] Error connecting to Pusher:', err);
});

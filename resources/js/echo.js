import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const host = import.meta.env.VITE_REVERB_HOST === 'localhost' && window.location.hostname !== 'localhost'
    ? window.location.hostname
    : import.meta.env.VITE_REVERB_HOST;

const scheme = window.location.protocol === 'https:' ? 'https' : (import.meta.env.VITE_REVERB_SCHEME ?? 'https');
const port = import.meta.env.VITE_REVERB_PORT ?? (scheme === 'https' ? 443 : 80);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
});

import Echo from 'laravel-echo';
import Pusher from 'pusher-js'; // أو: import * as Pusher from '@pusher/pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb', // أو 'pusher' حسب إعداداتك
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
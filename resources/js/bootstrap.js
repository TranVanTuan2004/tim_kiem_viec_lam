import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'vvb8wlg9szbi3okrm68e',
    wsHost: 'localhost',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

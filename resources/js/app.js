import './../../vendor/power-components/livewire-powergrid/dist/powergrid'
import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js";
// Hacemos que el calendario sea global para que PowerGrid lo encuentre
window.flatpickr = flatpickr;
flatpickr.localize(Spanish);


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

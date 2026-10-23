import './bootstrap';
import Alpine from 'alpinejs';
import bookingForm from './components/booking-form';
import chatRoom from './components/chat-room';

window.Alpine = Alpine;

window.rupiah = (value, dash = true) =>
    'Rp ' + Math.round(value || 0).toLocaleString('id-ID') + (dash ? ',-' : '');

Alpine.data('bookingForm', bookingForm);
Alpine.data('chatRoom', chatRoom);

Alpine.start();

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import ujs from '@rails/ujs';
ujs.start();

// Initialization for ES Users
import {
    Input,
    initTE,
} from "tw-elements";

initTE({ Input });

import { writable } from 'svelte/store'

export const currentPoste = writable({
    id: null,
    name: '',
    color: '#347deb',
    desc: '',
});
export const posteOpened = writable(false);
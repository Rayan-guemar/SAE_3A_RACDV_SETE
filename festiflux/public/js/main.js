import { Planning } from './planning/Planning.js';

console.log('Hello from main.js');
let planning = new Planning(document.getElementById('planning'), Date.parse('2019-07-01'), Date.parse('2019-07-07'));

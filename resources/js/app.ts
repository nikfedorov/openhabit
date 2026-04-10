import { createApp } from 'vue';
import '../css/app.css';
import App from './App.vue';
import router from './router';

createApp(App).use(router).mount('#app');

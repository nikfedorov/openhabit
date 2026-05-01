import * as Sentry from '@sentry/vue';
import { createApp } from 'vue';
import '../css/app.css';
import App from './App.vue';
import router from './router';

const app = createApp(App);

const sentryDsn = import.meta.env.VITE_SENTRY_DSN;

if (sentryDsn) {
    Sentry.init({
        app,
        dsn: sentryDsn,
        integrations: [Sentry.browserTracingIntegration({ router })],
        tracesSampleRate: 1.0,
    });
}

app.use(router).mount('#app');

/**
 * We'll load a simple HTTP library which allows us to easily issue requests
 * to our Laravel back-end using the modern fetch API.
 * This automatically handles sending the CSRF token as a header.
 */

// Create a fetch wrapper that mimics axios behavior
const httpClient = {
    defaults: {
        headers: {
            common: {}
        }
    },
    
    get: async (url, config = {}) => {
        return await httpClient.request({ ...config, method: 'GET', url });
    },
    
    post: async (url, data, config = {}) => {
        return await httpClient.request({ ...config, method: 'POST', url, data });
    },
    
    put: async (url, data, config = {}) => {
        return await httpClient.request({ ...config, method: 'PUT', url, data });
    },
    
    delete: async (url, config = {}) => {
        return await httpClient.request({ ...config, method: 'DELETE', url });
    },
    
    request: async (config) => {
        const {
            method = 'GET',
            url,
            data,
            headers = {},
            ...otherConfig
        } = config;
        
        const fetchConfig = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...httpClient.defaults.headers.common,
                ...headers
            },
            ...otherConfig
        };
        
        if (data) {
            fetchConfig.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, fetchConfig);
            const responseData = await response.json();
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return {
                data: responseData,
                status: response.status,
                statusText: response.statusText,
                headers: response.headers
            };
        } catch (error) {
            throw error;
        }
    }
};

window.axios = httpClient;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

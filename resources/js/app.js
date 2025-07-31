import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// Import Tailwind CSS
import '../css/tailwind.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

app.mount('#app')

// Global utilities
window.Kaszflow = {
    // Utility functions
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('pl-PL', {
            style: 'currency',
            currency: 'PLN'
        }).format(amount)
    },
    
    // Analytics tracking
    trackEvent: (event, data = {}) => {
        if (window.gtag) {
            window.gtag('event', event, data)
        }
        // Send to our analytics
        fetch('/api/analytics/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ event, data })
        }).catch(console.error)
    }
} 
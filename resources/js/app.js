import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#1976D2',
          secondary: '#424242',
          accent: '#82B1FF',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FB8C00',
        }
      }
    }
  }
})

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'SIA - Sistema ITSM'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, app, props, plugin }) {
    const vueApp = createApp({ render: () => h(app, props) })
      .use(plugin)
      .use(vuetify)

    // Adicionar função route globalmente usando Ziggy
    vueApp.config.globalProperties.route = function(name, params = {}) {
      if (window.route) {
        return window.route(name, params)
      }

      // Fallback manual para as rotas principais
      const routes = {
        'incidentes.index': '/incidentes',
        'incidentes.create': '/incidentes/create',
        'incidentes.show': (id) => `/incidentes/${id}`,
        'incidentes.edit': (id) => `/incidentes/${id}/edit`,
        'problemas.index': '/problemas',
        'problemas.create': '/problemas/create',
        'artigos-kb.index': '/artigos-kb',
        'artigos-kb.create': '/artigos-kb/create'
      }

      const route = routes[name]
      if (typeof route === 'function') {
        return route(params)
      }
      return route || '#'
    }

    return vueApp.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})

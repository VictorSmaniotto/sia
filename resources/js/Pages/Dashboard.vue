<template>
  <v-app>    <v-app-bar app color="primary" dark>
      <v-app-bar-title>{{ tenant?.nome || 'SIA - Sistema ITSM' }}</v-app-bar-title>

      <v-spacer></v-spacer>

      <v-btn icon @click="logout" title="Sair">
        <v-icon>mdi-logout</v-icon>
      </v-btn>

      <v-btn icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" to="/dashboard"></v-list-item>
        <v-list-item prepend-icon="mdi-alert-circle" title="Incidentes" :to="route('incidentes.index')"></v-list-item>
        <v-list-item prepend-icon="mdi-bug" title="Problemas" :to="route('problemas.index')"></v-list-item>
        <v-list-item prepend-icon="mdi-book-open-variant" title="Base de Conhecimento" :to="route('artigos-kb.index')"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid>
        <v-row>
          <v-col cols="12">
            <h1 class="text-h3 mb-6">Bem-vindo ao Sistema ITSM</h1>

            <v-row>
              <v-col v-for="stat in statCards" :key="stat.title" cols="12" sm="6" md="3">
                <v-card>
                  <v-card-text>
                    <div class="d-flex align-center">
                      <v-icon :color="stat.color" size="40" class="mr-4">{{ stat.icon }}</v-icon>
                      <div>
                        <div class="text-h6">{{ stat.value }}</div>
                        <div class="text-caption text-medium-emphasis">{{ stat.title }}</div>
                      </div>
                    </div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <v-row class="mt-6">
              <v-col cols="12" md="6">
                <v-card>
                  <v-card-title>Ações Rápidas</v-card-title>
                  <v-card-text>
                    <v-list>
                      <v-list-item prepend-icon="mdi-plus" title="Novo Incidente" :to="route('incidentes.create')"></v-list-item>
                      <v-list-item prepend-icon="mdi-plus" title="Novo Problema" :to="route('problemas.create')"></v-list-item>
                      <v-list-item prepend-icon="mdi-plus" title="Novo Artigo KB" :to="route('artigos-kb.create')"></v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="6">
                <v-card>
                  <v-card-title>Informações do Sistema</v-card-title>
                  <v-card-text>
                    <v-list>
                      <v-list-item>
                        <v-list-item-title>Tenant: {{ tenant?.nome || 'N/A' }}</v-list-item-title>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-title>Domínio: {{ tenant?.dominio || 'N/A' }}</v-list-item-title>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-title>Status: Ativo</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'Dashboard',
  props: {
    tenant: Object,
    stats: Object
  },
  data() {
    return {
      drawer: false
    }
  },
  computed: {
    statCards() {
      return [
        {
          title: 'Incidentes Abertos',
          value: this.stats?.incidentes_abertos || 0,
          icon: 'mdi-alert-circle',
          color: 'error'
        },
        {
          title: 'Problemas Novos',
          value: this.stats?.problemas_novos || 0,
          icon: 'mdi-bug',
          color: 'warning'
        },
        {
          title: 'Artigos KB',
          value: this.stats?.artigos_kb || 0,
          icon: 'mdi-book-open-variant',
          color: 'info'
        },
        {
          title: 'Usuários Ativos',
          value: this.stats?.usuarios_ativos || 0,
          icon: 'mdi-account-multiple',
          color: 'success'
        }
      ]
    }
  },
  methods: {
    route(name, params) {
      return this.$root.route ? this.$root.route(name, params) : '#'
    },
    logout() {
      this.$inertia.post('/logout')
    }
  }
}
</script>

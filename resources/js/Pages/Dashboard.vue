<template>
  <v-app>
    <!-- Barra de navegação superior -->
    <v-app-bar color="primary" density="compact" dark>
      <v-app-bar-title>
        <v-icon left>mdi-tools</v-icon>
        SIA - Sistema ITSM
      </v-app-bar-title>

      <v-spacer></v-spacer>

      <v-chip color="white" variant="tonal" class="mr-4">
        {{ tenant.nome }}
      </v-chip>

      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn icon v-bind="props">
            <v-avatar size="32">
              <v-icon>mdi-account-circle</v-icon>
            </v-avatar>
          </v-btn>
        </template>

        <v-list>
          <v-list-item>
            <v-list-item-title>{{ usuario.nome }}</v-list-item-title>
            <v-list-item-subtitle>{{ usuario.role }}</v-list-item-subtitle>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click="logout">
            <v-list-item-title>
              <v-icon left>mdi-logout</v-icon>
              Sair
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Menu lateral -->
    <v-navigation-drawer permanent>
      <v-list>
        <v-list-item>
          <v-list-item-title class="text-h6">
            Dashboard
          </v-list-item-title>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-view-dashboard" title="Visão Geral" value="dashboard"></v-list-item>
        <v-list-item prepend-icon="mdi-alert-circle" title="Incidentes" value="incidentes"></v-list-item>
        <v-list-item prepend-icon="mdi-magnify" title="Problemas" value="problemas"></v-list-item>
        <v-list-item prepend-icon="mdi-book-open" title="Base de Conhecimento" value="kb"></v-list-item>

        <v-divider class="my-2"></v-divider>

        <v-list-item v-if="usuario.role === 'admin'" prepend-icon="mdi-cog" title="Configurações" value="config"></v-list-item>
        <v-list-item v-if="usuario.role === 'gestor' || usuario.role === 'admin'" prepend-icon="mdi-chart-line" title="Relatórios" value="relatorios"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Conteúdo principal -->
    <v-main>
      <v-container>
        <v-row>
          <v-col cols="12">
            <h1 class="text-h4 mb-4">
              Bem-vindo, {{ usuario.nome }}!
            </h1>
          </v-col>
        </v-row>

        <!-- Cards de estatísticas -->
        <v-row>
          <v-col cols="12" md="3">
            <v-card color="error" variant="tonal">
              <v-card-text class="d-flex align-center">
                <div>
                  <h2 class="text-h3">{{ stats.incidentes_abertos }}</h2>
                  <p class="text-body-1">Incidentes Abertos</p>
                </div>
                <v-spacer></v-spacer>
                <v-icon size="48" color="error">mdi-alert-circle</v-icon>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-card color="warning" variant="tonal">
              <v-card-text class="d-flex align-center">
                <div>
                  <h2 class="text-h3">{{ stats.incidentes_em_andamento }}</h2>
                  <p class="text-body-1">Em Andamento</p>
                </div>
                <v-spacer></v-spacer>
                <v-icon size="48" color="warning">mdi-clock-alert</v-icon>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-card color="info" variant="tonal">
              <v-card-text class="d-flex align-center">
                <div>
                  <h2 class="text-h3">{{ stats.problemas_novos }}</h2>
                  <p class="text-body-1">Problemas Novos</p>
                </div>
                <v-spacer></v-spacer>
                <v-icon size="48" color="info">mdi-magnify</v-icon>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-card color="success" variant="tonal">
              <v-card-text class="d-flex align-center">
                <div>
                  <h2 class="text-h3">{{ stats.artigos_kb }}</h2>
                  <p class="text-body-1">Artigos KB</p>
                </div>
                <v-spacer></v-spacer>
                <v-icon size="48" color="success">mdi-book-open</v-icon>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <v-row class="mt-4">
          <v-col cols="12" md="6">
            <v-card>
              <v-card-title>
                <v-icon left>mdi-chart-bar</v-icon>
                Atividades Recentes
              </v-card-title>
              <v-card-text>
                <p class="text-body-2">Funcionalidade em desenvolvimento...</p>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card>
              <v-card-title>
                <v-icon left>mdi-clock</v-icon>
                Próximos Vencimentos SLA
              </v-card-title>
              <v-card-text>
                <p class="text-body-2">Funcionalidade em desenvolvimento...</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  usuario: Object,
  stats: Object,
  tenant: Object
})

const logout = () => {
  router.post('/logout')
}
</script>

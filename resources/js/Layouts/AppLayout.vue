<template>
  <v-app>
    <!-- Barra de navegação superior -->
    <v-app-bar
      app
      color="primary"
      dark
      elevation="4"
      height="64"
    >
      <template v-slot:prepend>
        <v-btn
          icon="mdi-menu"
          @click="drawer = !drawer"
        ></v-btn>
      </template>

      <v-toolbar-title class="d-flex align-center">
        <v-icon size="32" class="mr-2">mdi-shield-account</v-icon>
        <span class="text-h6 font-weight-medium">SIA - Sistema ITSM</span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <!-- Botões de ação do usuário -->
      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            icon
            class="mr-2"
          >
            <v-avatar size="32" color="secondary">
              <span class="text-caption font-weight-bold">
                {{ getUserInitials() }}
              </span>
            </v-avatar>
          </v-btn>
        </template>

        <v-list>
          <v-list-item>
            <v-list-item-title>{{ usuario?.nome }}</v-list-item-title>
            <v-list-item-subtitle>{{ usuario?.email }}</v-list-item-subtitle>
          </v-list-item>

          <v-divider></v-divider>          <v-list-item @click="$inertia.visit('/perfil')">
            <template v-slot:prepend>
              <v-icon>mdi-account-circle</v-icon>
            </template>
            <v-list-item-title>Meu Perfil</v-list-item-title>
          </v-list-item>

          <v-list-item @click="$inertia.visit('/perfil/configuracoes')">
            <template v-slot:prepend>
              <v-icon>mdi-cog</v-icon>
            </template>
            <v-list-item-title>Configurações</v-list-item-title>
          </v-list-item>

          <v-divider></v-divider>

          <v-list-item @click="logout">
            <template v-slot:prepend>
              <v-icon>mdi-logout</v-icon>
            </template>
            <v-list-item-title>Sair</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Menu lateral -->
    <v-navigation-drawer
      v-model="drawer"
      app
      width="280"
      color="grey-lighten-5"
    >
      <!-- Logo/Header do drawer -->
      <v-list-item class="pa-4">
        <div class="text-center w-100">
          <v-icon size="48" color="primary" class="mb-2">mdi-shield-account</v-icon>
          <div class="text-body-1 font-weight-medium">Sistema ITSM</div>
          <div class="text-caption text-grey">{{ tenant?.nome }}</div>
        </div>
      </v-list-item>

      <v-divider></v-divider>

      <!-- Itens do menu -->
      <v-list nav>
        <v-list-item
          :to="'/dashboard'"
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
          :active="$page.component === 'Dashboard'"
        ></v-list-item>

        <v-list-group>
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-ticket-account"
              title="Incidentes"
            ></v-list-item>
          </template>

          <v-list-item
            :to="'/incidentes'"
            prepend-icon="mdi-format-list-bulleted"
            title="Listar"
            :active="$page.component === 'Incidentes/Index'"
          ></v-list-item>

          <v-list-item
            :to="'/incidentes/create'"
            prepend-icon="mdi-plus"
            title="Novo"
            :active="$page.component === 'Incidentes/Create'"
          ></v-list-item>
        </v-list-group>

        <v-list-group>
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-wrench"
              title="Problemas"
            ></v-list-item>
          </template>

          <v-list-item
            :to="'/problemas'"
            prepend-icon="mdi-format-list-bulleted"
            title="Listar"
            :active="$page.component === 'Problemas/Index'"
          ></v-list-item>

          <v-list-item
            :to="'/problemas/create'"
            prepend-icon="mdi-plus"
            title="Novo"
            :active="$page.component === 'Problemas/Create'"
          ></v-list-item>
        </v-list-group>

        <v-list-group>
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-book-open-page-variant"
              title="Base de Conhecimento"
            ></v-list-item>
          </template>

          <v-list-item
            :to="'/base-conhecimento'"
            prepend-icon="mdi-format-list-bulleted"
            title="Listar"
            :active="$page.component === 'BaseConhecimento/Index'"
          ></v-list-item>

          <v-list-item
            :to="'/base-conhecimento/create'"
            prepend-icon="mdi-plus"
            title="Novo Artigo"
            :active="$page.component === 'BaseConhecimento/Create'"
          ></v-list-item>
        </v-list-group>

        <v-divider class="my-2"></v-divider>

        <!-- Seção Administração (só para admins) -->
        <v-list-group v-if="isAdmin">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-cog"
              title="Administração"
            ></v-list-item>
          </template>

          <v-list-item
            :to="'/usuarios'"
            prepend-icon="mdi-account-group"
            title="Usuários"
            :active="$page.component.startsWith('Usuarios')"
          ></v-list-item>

          <v-list-item
            :to="'/relatorios'"
            prepend-icon="mdi-chart-line"
            title="Relatórios"
            :active="$page.component.startsWith('Relatorios')"
          ></v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <!-- Conteúdo principal -->
    <v-main>
      <slot />
    </v-main>

    <!-- Componente de mensagens flash -->
    <FlashMessages />

    <!-- Dialog Perfil do usuário -->
    <v-dialog v-model="showProfile" max-width="500">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon left>mdi-account-circle</v-icon>
          Meu Perfil
        </v-card-title>
        <v-card-text>
          <div class="text-center mb-4">
            <v-avatar size="80" color="primary">
              <span class="text-h4 font-weight-bold text-white">
                {{ getUserInitials() }}
              </span>
            </v-avatar>
          </div>

          <v-row>
            <v-col cols="12">
              <v-text-field
                :model-value="usuario?.nome"
                label="Nome"
                readonly
                variant="outlined"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                :model-value="usuario?.email"
                label="E-mail"
                readonly
                variant="outlined"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                :model-value="getRoleText(usuario?.role)"
                label="Perfil"
                readonly
                variant="outlined"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="showProfile = false">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog Configurações -->
    <v-dialog v-model="showSettings" max-width="500">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon left>mdi-cog</v-icon>
          Configurações
        </v-card-title>
        <v-card-text>
          <p class="text-body-2 mb-4">
            Funcionalidade em desenvolvimento. Em breve você poderá:
          </p>
          <ul class="text-body-2">
            <li>Alterar senha</li>
            <li>Configurar notificações</li>
            <li>Personalizar interface</li>
            <li>Configurar fuso horário</li>
          </ul>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="showSettings = false">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/inertia-vue3'
import FlashMessages from '@/Components/FlashMessages.vue'

const drawer = ref(true)
const showProfile = ref(false)
const showSettings = ref(false)

const page = usePage()

const usuario = computed(() => page.props.value.auth?.user || page.props.value.usuario)
const tenant = computed(() => page.props.value.tenant)

const isAdmin = computed(() => usuario.value?.role === 'admin')

const getUserInitials = () => {
  if (!usuario.value?.nome) return 'U'

  const names = usuario.value.nome.split(' ')
  if (names.length === 1) {
    return names[0].charAt(0).toUpperCase()
  }

  return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase()
}

const getRoleText = (role) => {
  const roles = {
    'admin': 'Administrador',
    'tecnico': 'Técnico',
    'gestor': 'Gestor'
  }
  return roles[role] || role
}

const logout = () => {
  router.post('/logout')
}
</script>

<style scoped>
.v-navigation-drawer {
  border-right: 1px solid rgba(0,0,0,0.12);
}

.v-list-item--active {
  color: rgb(var(--v-theme-primary)) !important;
}
</style>

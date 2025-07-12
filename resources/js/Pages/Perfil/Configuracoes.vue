<template>
  <AppLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center mb-6">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit('/perfil')"
              class="mr-3"
            />
            <v-icon size="32" color="primary" class="mr-3">mdi-cog</v-icon>
            <h1 class="text-h4 font-weight-medium">Configurações</h1>
          </div>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" lg="8">
          <!-- Configurações de Notificação -->
          <v-card class="mb-6">
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-bell-outline</v-icon>
              Notificações
            </v-card-title>
            <v-card-text>
              <div class="text-body-2 text-grey mb-4">
                Configure como e quando você deseja receber notificações do sistema.
              </div>

              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-body-1 font-weight-medium mb-2">Notificações por E-mail</div>

                    <v-switch
                      v-model="notifications.email.incidentes"
                      label="Novos incidentes atribuídos"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>

                    <v-switch
                      v-model="notifications.email.comentarios"
                      label="Novos comentários"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>

                    <v-switch
                      v-model="notifications.email.status"
                      label="Mudanças de status"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>
                  </div>
                </v-col>

                <v-col cols="12" md="6">
                  <div class="mb-4">
                    <div class="text-body-1 font-weight-medium mb-2">Notificações do Sistema</div>

                    <v-switch
                      v-model="notifications.system.desktop"
                      label="Notificações desktop"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>

                    <v-switch
                      v-model="notifications.system.sound"
                      label="Sons de notificação"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>

                    <v-switch
                      v-model="notifications.system.resumo"
                      label="Resumo diário"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Configurações de Interface -->
          <v-card class="mb-6">
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-palette</v-icon>
              Interface e Aparência
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="preferences.theme"
                    label="Tema"
                    :items="themeOptions"
                    variant="outlined"
                    prepend-icon="mdi-brightness-6"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="preferences.language"
                    label="Idioma"
                    :items="languageOptions"
                    variant="outlined"
                    prepend-icon="mdi-translate"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="preferences.timezone"
                    label="Fuso Horário"
                    :items="timezoneOptions"
                    variant="outlined"
                    prepend-icon="mdi-clock-outline"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="preferences.itemsPerPage"
                    label="Itens por página"
                    :items="itemsPerPageOptions"
                    variant="outlined"
                    prepend-icon="mdi-format-list-numbered"
                  ></v-select>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Configurações de Segurança -->
          <v-card class="mb-6">
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-shield-check</v-icon>
              Segurança
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <div class="mb-4">
                    <div class="text-body-1 font-weight-medium mb-2">Configurações de Sessão</div>

                    <v-switch
                      v-model="security.autoLogout"
                      label="Logout automático por inatividade"
                      color="primary"
                      hide-details
                      class="mb-2"
                    ></v-switch>

                    <v-select
                      v-if="security.autoLogout"
                      v-model="security.logoutTime"
                      label="Tempo de inatividade (minutos)"
                      :items="logoutTimeOptions"
                      variant="outlined"
                      class="mt-3"
                    ></v-select>
                  </div>
                </v-col>
              </v-row>

              <v-divider class="my-4"></v-divider>

              <div class="d-flex justify-space-between align-center">
                <div>
                  <div class="text-body-1 font-weight-medium">Alterar Senha</div>
                  <div class="text-caption text-grey">Última alteração há 30 dias</div>
                </div>
                <v-btn
                  color="warning"
                  variant="outlined"
                  @click="showPasswordDialog = true"
                >
                  <v-icon left>mdi-lock-reset</v-icon>
                  Alterar Senha
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Botões de Ação -->
          <div class="d-flex justify-space-between">
            <v-btn
              variant="outlined"
              @click="resetToDefaults"
            >
              <v-icon left>mdi-restore</v-icon>
              Restaurar Padrões
            </v-btn>

            <v-btn
              color="primary"
              @click="saveSettings"
              :loading="saving"
              variant="elevated"
            >
              <v-icon left>mdi-content-save</v-icon>
              Salvar Configurações
            </v-btn>
          </div>
        </v-col>

        <!-- Sidebar com informações -->
        <v-col cols="12" lg="4">
          <v-card>
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-information</v-icon>
              Informações
            </v-card-title>
            <v-card-text>
              <div class="mb-4">
                <div class="text-body-2 font-weight-medium mb-1">Versão do Sistema</div>
                <div class="text-caption text-grey">SIA ITSM v2.1.0</div>
              </div>

              <div class="mb-4">
                <div class="text-body-2 font-weight-medium mb-1">Última Atualização</div>
                <div class="text-caption text-grey">12 de julho de 2025</div>
              </div>

              <v-divider class="my-4"></v-divider>

              <div class="text-center">
                <v-btn
                  color="info"
                  variant="outlined"
                  size="small"
                  href="#"
                  target="_blank"
                  class="mb-2"
                  block
                >
                  <v-icon left>mdi-help-circle</v-icon>
                  Central de Ajuda
                </v-btn>

                <v-btn
                  color="secondary"
                  variant="outlined"
                  size="small"
                  href="#"
                  target="_blank"
                  block
                >
                  <v-icon left>mdi-file-document</v-icon>
                  Documentação
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Card com dicas -->
          <v-card class="mt-4" variant="outlined">
            <v-card-text>
              <div class="d-flex align-center mb-2">
                <v-icon color="info" size="small" class="mr-2">mdi-lightbulb</v-icon>
                <span class="text-body-2 font-weight-medium">Dica:</span>
              </div>
              <p class="text-caption text-grey">
                Ative as notificações por e-mail para ficar sempre atualizado sobre os incidentes atribuídos a você.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Dialog para alteração de senha -->
    <v-dialog v-model="showPasswordDialog" max-width="500">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon left>mdi-lock-reset</v-icon>
          Alterar Senha
        </v-card-title>

        <v-card-text>
          <v-form @submit.prevent="updatePassword">
            <v-text-field
              v-model="passwordForm.current_password"
              label="Senha Atual"
              type="password"
              required
              :error-messages="passwordErrors.current_password"
              variant="outlined"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="passwordForm.password"
              label="Nova Senha"
              type="password"
              required
              :error-messages="passwordErrors.password"
              variant="outlined"
              class="mb-3"
              hint="Mínimo 6 caracteres"
            ></v-text-field>

            <v-text-field
              v-model="passwordForm.password_confirmation"
              label="Confirmar Nova Senha"
              type="password"
              required
              :error-messages="passwordErrors.password_confirmation"
              variant="outlined"
            ></v-text-field>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="showPasswordDialog = false">Cancelar</v-btn>
          <v-btn
            color="primary"
            @click="updatePassword"
            :loading="passwordLoading"
          >
            Alterar Senha
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  usuario: Object
})

const saving = ref(false)
const showPasswordDialog = ref(false)
const passwordLoading = ref(false)
const passwordErrors = ref({})

// Configurações de notificação
const notifications = reactive({
  email: {
    incidentes: true,
    comentarios: true,
    status: true
  },
  system: {
    desktop: false,
    sound: true,
    resumo: true
  }
})

// Preferências da interface
const preferences = reactive({
  theme: 'system',
  language: 'pt-BR',
  timezone: 'America/Sao_Paulo',
  itemsPerPage: 25
})

// Configurações de segurança
const security = reactive({
  autoLogout: true,
  logoutTime: 30
})

// Formulário de senha
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Opções para selects
const themeOptions = [
  { title: 'Sistema', value: 'system' },
  { title: 'Claro', value: 'light' },
  { title: 'Escuro', value: 'dark' }
]

const languageOptions = [
  { title: 'Português (Brasil)', value: 'pt-BR' },
  { title: 'English', value: 'en' },
  { title: 'Español', value: 'es' }
]

const timezoneOptions = [
  { title: 'São Paulo (UTC-3)', value: 'America/Sao_Paulo' },
  { title: 'Manaus (UTC-4)', value: 'America/Manaus' },
  { title: 'Rio Branco (UTC-5)', value: 'America/Rio_Branco' }
]

const itemsPerPageOptions = [
  { title: '10 itens', value: 10 },
  { title: '25 itens', value: 25 },
  { title: '50 itens', value: 50 },
  { title: '100 itens', value: 100 }
]

const logoutTimeOptions = [
  { title: '15 minutos', value: 15 },
  { title: '30 minutos', value: 30 },
  { title: '60 minutos', value: 60 },
  { title: '120 minutos', value: 120 }
]

const saveSettings = () => {
  saving.value = true

  // Simular salvamento das configurações
  setTimeout(() => {
    saving.value = false
    // Aqui você faria a requisição real para salvar as configurações
    console.log('Configurações salvas:', { notifications, preferences, security })
  }, 1000)
}

const resetToDefaults = () => {
  // Restaurar configurações padrão
  notifications.email.incidentes = true
  notifications.email.comentarios = true
  notifications.email.status = true

  notifications.system.desktop = false
  notifications.system.sound = true
  notifications.system.resumo = true

  preferences.theme = 'system'
  preferences.language = 'pt-BR'
  preferences.timezone = 'America/Sao_Paulo'
  preferences.itemsPerPage = 25

  security.autoLogout = true
  security.logoutTime = 30
}

const updatePassword = () => {
  passwordLoading.value = true
  passwordErrors.value = {}

  passwordForm.post('/perfil/password', {
    onSuccess: () => {
      showPasswordDialog.value = false
      passwordForm.reset()
    },
    onError: (errors) => {
      passwordErrors.value = errors
    },
    onFinish: () => {
      passwordLoading.value = false
    }
  })
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>

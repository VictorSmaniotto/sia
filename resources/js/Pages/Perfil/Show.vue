<template>
  <AppLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center mb-6">
            <v-icon size="32" color="primary" class="mr-3">mdi-account-circle</v-icon>
            <h1 class="text-h4 font-weight-medium">Meu Perfil</h1>
          </div>
        </v-col>
      </v-row>

      <v-row>
        <!-- Card Principal do Perfil -->
        <v-col cols="12" lg="4">
          <v-card class="h-100">
            <v-card-text class="text-center pa-6">
              <v-avatar size="120" color="primary" class="mb-4">
                <span class="text-h2 font-weight-bold text-white">
                  {{ getUserInitials() }}
                </span>
              </v-avatar>

              <h2 class="text-h5 mb-2">{{ usuario.nome }}</h2>
              <p class="text-body-1 text-grey mb-2">{{ usuario.email }}</p>

              <v-chip
                :color="getRoleColor(usuario.role)"
                variant="flat"
                size="large"
                class="mb-4"
              >
                <v-icon left size="small">{{ getRoleIcon(usuario.role) }}</v-icon>
                {{ getRoleText(usuario.role) }}
              </v-chip>

              <v-divider class="my-4"></v-divider>

              <div class="text-left">
                <div class="d-flex align-center mb-2">
                  <v-icon color="grey" size="small" class="mr-2">mdi-calendar</v-icon>
                  <span class="text-body-2">
                    <strong>Membro desde:</strong> {{ formatDate(usuario.criado_em) }}
                  </span>
                </div>

                <div class="d-flex align-center mb-2">
                  <v-icon color="grey" size="small" class="mr-2">mdi-clock-outline</v-icon>
                  <span class="text-body-2">
                    <strong>Último acesso:</strong> {{ formatDate(usuario.atualizado_em) }}
                  </span>
                </div>

                <div class="d-flex align-center">
                  <v-icon
                    :color="usuario.ativo ? 'success' : 'error'"
                    size="small"
                    class="mr-2"
                  >
                    {{ usuario.ativo ? 'mdi-check-circle' : 'mdi-close-circle' }}
                  </v-icon>
                  <span class="text-body-2">
                    <strong>Status:</strong>
                    <span :class="usuario.ativo ? 'text-success' : 'text-error'">
                      {{ usuario.ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                  </span>
                </div>
              </div>

              <v-btn
                color="primary"
                variant="elevated"
                class="mt-4"
                @click="$inertia.visit('/perfil/edit')"
                block
              >
                <v-icon left>mdi-pencil</v-icon>
                Editar Perfil
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Estatísticas e Ações -->
        <v-col cols="12" lg="8">
          <v-row>
            <!-- Estatísticas de Atividade -->
            <v-col cols="12">
              <v-card>
                <v-card-title class="d-flex align-center">
                  <v-icon left>mdi-chart-line</v-icon>
                  Estatísticas de Atividade
                </v-card-title>
                <v-card-text>
                  <v-row>
                    <v-col cols="6" md="3">
                      <div class="text-center">
                        <div class="text-h4 text-primary font-weight-bold">12</div>
                        <div class="text-caption text-grey">Incidentes Criados</div>
                      </div>
                    </v-col>
                    <v-col cols="6" md="3">
                      <div class="text-center">
                        <div class="text-h4 text-success font-weight-bold">8</div>
                        <div class="text-caption text-grey">Incidentes Resolvidos</div>
                      </div>
                    </v-col>
                    <v-col cols="6" md="3">
                      <div class="text-center">
                        <div class="text-h4 text-warning font-weight-bold">3</div>
                        <div class="text-caption text-grey">Problemas Investigados</div>
                      </div>
                    </v-col>
                    <v-col cols="6" md="3">
                      <div class="text-center">
                        <div class="text-h4 text-info font-weight-bold">45</div>
                        <div class="text-caption text-grey">Comentários</div>
                      </div>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>

            <!-- Ações Rápidas -->
            <v-col cols="12">
              <v-card>
                <v-card-title class="d-flex align-center">
                  <v-icon left>mdi-lightning-bolt</v-icon>
                  Ações Rápidas
                </v-card-title>
                <v-card-text>
                  <v-row>
                    <v-col cols="12" sm="6" md="4">
                      <v-btn
                        color="primary"
                        variant="outlined"
                        @click="$inertia.visit('/perfil/edit')"
                        block
                        size="large"
                      >
                        <v-icon left>mdi-account-edit</v-icon>
                        Editar Perfil
                      </v-btn>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                      <v-btn
                        color="warning"
                        variant="outlined"
                        @click="showPasswordDialog = true"
                        block
                        size="large"
                      >
                        <v-icon left>mdi-lock-reset</v-icon>
                        Alterar Senha
                      </v-btn>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                      <v-btn
                        color="info"
                        variant="outlined"
                        @click="$inertia.visit('/perfil/configuracoes')"
                        block
                        size="large"
                      >
                        <v-icon left>mdi-cog</v-icon>
                        Configurações
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>

            <!-- Atividade Recente -->
            <v-col cols="12">
              <v-card>
                <v-card-title class="d-flex align-center">
                  <v-icon left>mdi-history</v-icon>
                  Atividade Recente
                </v-card-title>
                <v-card-text>
                  <v-timeline align="start" density="compact">
                    <v-timeline-item
                      dot-color="success"
                      size="small"
                    >
                      <template v-slot:opposite>
                        <span class="text-caption text-grey">Hoje, 14:30</span>
                      </template>
                      <div>
                        <div class="text-body-2 font-weight-medium">Comentário adicionado</div>
                        <div class="text-caption text-grey">Incidente #1023 - Problema de conectividade</div>
                      </div>
                    </v-timeline-item>

                    <v-timeline-item
                      dot-color="primary"
                      size="small"
                    >
                      <template v-slot:opposite>
                        <span class="text-caption text-grey">Ontem, 16:45</span>
                      </template>
                      <div>
                        <div class="text-body-2 font-weight-medium">Incidente resolvido</div>
                        <div class="text-caption text-grey">Incidente #1019 - Lentidão no sistema</div>
                      </div>
                    </v-timeline-item>

                    <v-timeline-item
                      dot-color="warning"
                      size="small"
                    >
                      <template v-slot:opposite>
                        <span class="text-caption text-grey">2 dias atrás</span>
                      </template>
                      <div>
                        <div class="text-body-2 font-weight-medium">Problema criado</div>
                        <div class="text-caption text-grey">Problema #45 - Falha recorrente no backup</div>
                      </div>
                    </v-timeline-item>
                  </v-timeline>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
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
import { ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  usuario: Object
})

const showPasswordDialog = ref(false)
const passwordLoading = ref(false)
const passwordErrors = ref({})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const getUserInitials = () => {
  if (!props.usuario?.nome) return 'U'

  const names = props.usuario.nome.split(' ')
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

const getRoleColor = (role) => {
  const colors = {
    'admin': 'red',
    'tecnico': 'blue',
    'gestor': 'purple'
  }
  return colors[role] || 'grey'
}

const getRoleIcon = (role) => {
  const icons = {
    'admin': 'mdi-shield-crown',
    'tecnico': 'mdi-wrench',
    'gestor': 'mdi-account-tie'
  }
  return icons[role] || 'mdi-account'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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

.v-timeline {
  padding: 0;
}
</style>

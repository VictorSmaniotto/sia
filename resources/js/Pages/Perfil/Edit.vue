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
            <v-icon size="32" color="primary" class="mr-3">mdi-account-edit</v-icon>
            <h1 class="text-h4 font-weight-medium">Editar Perfil</h1>
          </div>
        </v-col>
      </v-row>

      <v-row justify="center">
        <v-col cols="12" md="8" lg="6">
          <v-card class="pa-4">
            <v-card-title class="text-center pb-4">
              <v-avatar size="80" color="primary" class="mb-3">
                <span class="text-h4 font-weight-bold text-white">
                  {{ getUserInitials() }}
                </span>
              </v-avatar>
              <div class="text-h6">Informações Pessoais</div>
            </v-card-title>

            <v-card-text>
              <v-form @submit.prevent="updateProfile">
                <v-text-field
                  v-model="form.nome"
                  label="Nome Completo"
                  name="nome"
                  prepend-icon="mdi-account"
                  type="text"
                  required
                  :error-messages="errors.nome"
                  variant="outlined"
                  class="mb-4"
                ></v-text-field>

                <v-text-field
                  v-model="form.email"
                  label="E-mail"
                  name="email"
                  prepend-icon="mdi-email"
                  type="email"
                  required
                  :error-messages="errors.email"
                  variant="outlined"
                  class="mb-4"
                ></v-text-field>

                <v-text-field
                  :model-value="getRoleText(usuario.role)"
                  label="Perfil de Acesso"
                  prepend-icon="mdi-shield-account"
                  readonly
                  variant="outlined"
                  class="mb-4"
                  hint="O perfil de acesso é definido pelo administrador do sistema"
                ></v-text-field>

                <v-text-field
                  :model-value="formatDate(usuario.criado_em)"
                  label="Membro desde"
                  prepend-icon="mdi-calendar"
                  readonly
                  variant="outlined"
                  class="mb-4"
                ></v-text-field>

                <v-alert
                  type="info"
                  variant="tonal"
                  class="mb-4"
                >
                  <div class="text-body-2">
                    <strong>Dica:</strong> Para alterar sua senha, use o botão "Alterar Senha" na página do perfil.
                  </div>
                </v-alert>

                <div class="d-flex justify-space-between">
                  <v-btn
                    variant="outlined"
                    @click="$inertia.visit('/perfil')"
                  >
                    Cancelar
                  </v-btn>

                  <v-btn
                    color="primary"
                    type="submit"
                    :loading="loading"
                    variant="elevated"
                  >
                    <v-icon left>mdi-content-save</v-icon>
                    Salvar Alterações
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>

          <!-- Card com dicas de segurança -->
          <v-card class="mt-6" variant="outlined">
            <v-card-title class="d-flex align-center">
              <v-icon left color="info">mdi-shield-check</v-icon>
              Dicas de Segurança
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="d-flex align-start mb-3">
                    <v-icon color="success" class="mr-3 mt-1">mdi-check-circle</v-icon>
                    <div>
                      <div class="text-body-2 font-weight-medium">E-mail único</div>
                      <div class="text-caption text-grey">Use um e-mail único para sua conta</div>
                    </div>
                  </div>

                  <div class="d-flex align-start mb-3">
                    <v-icon color="success" class="mr-3 mt-1">mdi-check-circle</v-icon>
                    <div>
                      <div class="text-body-2 font-weight-medium">Informações atualizadas</div>
                      <div class="text-caption text-grey">Mantenha seus dados sempre atualizados</div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" md="6">
                  <div class="d-flex align-start mb-3">
                    <v-icon color="warning" class="mr-3 mt-1">mdi-alert</v-icon>
                    <div>
                      <div class="text-body-2 font-weight-medium">Senha segura</div>
                      <div class="text-caption text-grey">Altere sua senha regularmente</div>
                    </div>
                  </div>

                  <div class="d-flex align-start mb-3">
                    <v-icon color="info" class="mr-3 mt-1">mdi-information</v-icon>
                    <div>
                      <div class="text-body-2 font-weight-medium">Logout seguro</div>
                      <div class="text-caption text-grey">Sempre faça logout em computadores compartilhados</div>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  usuario: Object,
  errors: {
    type: Object,
    default: () => ({})
  }
})

const loading = ref(false)

const form = useForm({
  nome: props.usuario.nome,
  email: props.usuario.email
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const updateProfile = () => {
  loading.value = true
  form.put('/perfil', {
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>

<template>
  <v-app>
    <v-main>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>
                  <v-icon left>mdi-key-variant</v-icon>
                  Recuperar Senha
                </v-toolbar-title>
              </v-toolbar>

              <v-card-text class="pa-6">
                <div class="text-center mb-4">
                  <v-icon size="64" color="primary" class="mb-3">
                    mdi-email-lock
                  </v-icon>
                  <p class="text-h6 mb-2">Esqueceu sua senha?</p>
                  <p class="text-body-2 text-grey">
                    Digite seu e-mail e enviaremos um link para redefinir sua senha
                  </p>
                </div>

                <!-- Mensagem de sucesso -->
                <v-alert
                  v-if="$page.props.flash?.status"
                  type="success"
                  variant="tonal"
                  class="mb-4"
                >
                  {{ $page.props.flash.status }}
                </v-alert>

                <form @submit.prevent="submitForgotPassword">
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
                    hint="Digite o e-mail cadastrado em sua conta"
                  ></v-text-field>
                </form>
              </v-card-text>

              <v-card-actions class="pa-6 pt-0">
                <v-btn
                  color="primary"
                  @click="submitForgotPassword"
                  :loading="loading"
                  block
                  size="large"
                  variant="elevated"
                >
                  Enviar Link de Recuperação
                </v-btn>
              </v-card-actions>

              <v-divider></v-divider>

              <v-card-actions class="justify-center pa-4">
                <span class="text-body-2">Lembrou da senha?</span>
                <v-btn
                  color="primary"
                  variant="text"
                  @click="$inertia.visit('/login')"
                  class="ml-2"
                >
                  Voltar ao Login
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Card com informações adicionais -->
            <v-card class="mt-4" variant="outlined">
              <v-card-text class="text-center pa-4">
                <v-icon color="info" size="small" class="mb-2">mdi-information</v-icon>
                <p class="text-body-2 mb-2">
                  <strong>Dica de Segurança:</strong>
                </p>
                <p class="text-caption text-grey">
                  O link de recuperação expira em 24 horas por motivos de segurança.
                  Se não receber o e-mail, verifique sua pasta de spam.
                </p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const loading = ref(false)

const form = useForm({
  email: ''
})

const submitForgotPassword = () => {
  loading.value = true
  form.post('/forgot-password', {
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

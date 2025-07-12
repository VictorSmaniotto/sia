<template>
  <v-app>
    <v-main>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>
                  <v-icon left>mdi-shield-account</v-icon>
                  Login - SIA Sistema ITSM
                </v-toolbar-title>
              </v-toolbar>

              <v-card-text>
                <div class="text-center mb-4">
                  <p class="text-h6 mb-2">Bem-vindo de volta!</p>
                  <p class="text-body-2 text-grey">
                    Entre com suas credenciais para acessar o sistema
                  </p>
                </div>

                <!-- Mensagem de sucesso se houver -->
                <v-alert
                  v-if="$page.props.flash?.success"
                  type="success"
                  variant="tonal"
                  class="mb-4"
                >
                  {{ $page.props.flash.success }}
                </v-alert>

                <form @submit.prevent="submitLogin">
                  <v-text-field
                    v-model="form.email"
                    label="E-mail"
                    name="email"
                    prepend-icon="mdi-account"
                    type="email"
                    required
                    :error-messages="errors.email"
                    variant="outlined"
                    class="mb-3"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.password"
                    label="Senha"
                    name="password"
                    prepend-icon="mdi-lock"
                    :type="showPassword ? 'text' : 'password'"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                    required
                    :error-messages="errors.password"
                    variant="outlined"
                    class="mb-3"
                  ></v-text-field>
                </form>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  color="primary"
                  @click="submitLogin"
                  :loading="loading"
                  block
                  size="large"
                  variant="elevated"
                >
                  Entrar
                </v-btn>
              </v-card-actions>

              <v-divider></v-divider>

              <v-card-actions class="justify-center pa-4">
                <div class="d-flex flex-column align-center w-100">
                  <div class="mb-2">
                    <span class="text-body-2">Esqueceu a senha?</span>
                    <v-btn
                      color="primary"
                      variant="text"
                      size="small"
                      @click="$inertia.visit('/forgot-password')"
                      class="ml-1"
                    >
                      Recuperar
                    </v-btn>
                  </div>
                  <div>
                    <span class="text-body-2">Não tem conta?</span>
                    <v-btn
                      color="primary"
                      variant="text"
                      size="small"
                      @click="$inertia.visit('/register')"
                      class="ml-1"
                    >
                      Criar conta
                    </v-btn>
                  </div>
                </div>
              </v-card-actions>
            </v-card>

            <v-card class="mt-4" variant="outlined">
              <v-card-text class="text-center">
                <p class="text-body-2 mb-2">Usuários de teste disponíveis:</p>
                <v-chip color="primary" variant="tonal" size="small" class="ma-1">
                  admin@teste.com
                </v-chip>
                <v-chip color="success" variant="tonal" size="small" class="ma-1">
                  tecnico@teste.com
                </v-chip>
                <v-chip color="warning" variant="tonal" size="small" class="ma-1">
                  usuario@teste.com
                </v-chip>
                <p class="text-caption mt-2">Senha: 123456</p>
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
const showPassword = ref(false)

const form = useForm({
  email: '',
  password: ''
})

const submitLogin = () => {
  loading.value = true
  form.post('/login', {
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

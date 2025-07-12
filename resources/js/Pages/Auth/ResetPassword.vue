<template>
  <v-app>
    <v-main>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>
                  <v-icon left>mdi-lock-reset</v-icon>
                  Redefinir Senha
                </v-toolbar-title>
              </v-toolbar>

              <v-card-text class="pa-6">
                <div class="text-center mb-4">
                  <v-icon size="64" color="success" class="mb-3">
                    mdi-shield-check
                  </v-icon>
                  <p class="text-h6 mb-2">Defina sua nova senha</p>
                  <p class="text-body-2 text-grey">
                    Crie uma senha segura para proteger sua conta
                  </p>
                </div>

                <form @submit.prevent="submitResetPassword">
                  <v-text-field
                    v-model="form.email"
                    label="E-mail"
                    name="email"
                    prepend-icon="mdi-email"
                    type="email"
                    required
                    readonly
                    :error-messages="errors.email"
                    variant="outlined"
                    class="mb-3"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.password"
                    label="Nova Senha"
                    name="password"
                    prepend-icon="mdi-lock"
                    :type="showPassword ? 'text' : 'password'"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                    required
                    :error-messages="errors.password"
                    variant="outlined"
                    class="mb-3"
                    hint="Mínimo 6 caracteres"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirmar Nova Senha"
                    name="password_confirmation"
                    prepend-icon="mdi-lock-check"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    :append-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPasswordConfirm = !showPasswordConfirm"
                    required
                    :error-messages="errors.password_confirmation"
                    variant="outlined"
                    class="mb-4"
                  ></v-text-field>

                  <!-- Indicador de força da senha -->
                  <div v-if="form.password" class="mb-4">
                    <div class="text-caption mb-2">Força da senha:</div>
                    <v-progress-linear
                      :model-value="passwordStrength.score * 25"
                      :color="passwordStrength.color"
                      height="6"
                      rounded
                    ></v-progress-linear>
                    <div class="text-caption mt-1" :class="passwordStrength.color + '--text'">
                      {{ passwordStrength.text }}
                    </div>
                  </div>
                </form>
              </v-card-text>

              <v-card-actions class="pa-6 pt-0">
                <v-btn
                  color="primary"
                  @click="submitResetPassword"
                  :loading="loading"
                  :disabled="passwordStrength.score < 2"
                  block
                  size="large"
                  variant="elevated"
                >
                  Redefinir Senha
                </v-btn>
              </v-card-actions>

              <v-divider></v-divider>

              <v-card-actions class="justify-center pa-4">
                <v-btn
                  color="primary"
                  variant="text"
                  @click="$inertia.visit('/login')"
                >
                  Voltar ao Login
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Dicas de segurança -->
            <v-card class="mt-4" variant="outlined">
              <v-card-text class="pa-4">
                <div class="d-flex align-center mb-2">
                  <v-icon color="info" size="small" class="mr-2">mdi-shield-account</v-icon>
                  <span class="text-body-2 font-weight-medium">Dicas para uma senha segura:</span>
                </div>
                <ul class="text-caption text-grey ml-4">
                  <li>Use pelo menos 8 caracteres</li>
                  <li>Combine letras maiúsculas e minúsculas</li>
                  <li>Inclua números e símbolos</li>
                  <li>Evite informações pessoais</li>
                </ul>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({
  token: String,
  email: String,
  errors: {
    type: Object,
    default: () => ({})
  }
})

const loading = ref(false)
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

// Calculador de força da senha
const passwordStrength = computed(() => {
  const password = form.password
  if (!password) return { score: 0, text: '', color: 'grey' }

  let score = 0

  // Critérios de avaliação
  if (password.length >= 6) score++
  if (password.length >= 10) score++
  if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++
  if (/\d/.test(password)) score++
  if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++

  const levels = [
    { text: 'Muito fraca', color: 'red' },
    { text: 'Fraca', color: 'orange' },
    { text: 'Razoável', color: 'yellow' },
    { text: 'Boa', color: 'light-green' },
    { text: 'Excelente', color: 'green' }
  ]

  return {
    score,
    ...levels[Math.min(score, 4)]
  }
})

const submitResetPassword = () => {
  loading.value = true
  form.post('/reset-password', {
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

ul {
  list-style-type: disc;
}
</style>

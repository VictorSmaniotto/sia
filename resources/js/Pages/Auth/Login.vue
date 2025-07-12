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
                <form @submit.prevent="submitLogin">
                  <v-text-field
                    v-model="form.email"
                    label="E-mail"
                    name="email"
                    prepend-icon="mdi-account"
                    type="email"
                    required
                    :error-messages="errors.email"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.password"
                    label="Senha"
                    name="password"
                    prepend-icon="mdi-lock"
                    type="password"
                    required
                    :error-messages="errors.password"
                  ></v-text-field>
                </form>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="primary"
                  @click="submitLogin"
                  :loading="loading"
                  block
                  size="large"
                >
                  Entrar
                </v-btn>
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

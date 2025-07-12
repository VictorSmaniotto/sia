<template>
  <v-app>
    <v-main>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="5">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>
                  <v-icon left>mdi-account-plus</v-icon>
                  Criar Conta - SIA Sistema ITSM
                </v-toolbar-title>
              </v-toolbar>

              <v-card-text class="pa-6">
                <div class="text-center mb-4">
                  <p class="text-h6 mb-2">Bem-vindo!</p>
                  <p class="text-body-2 text-grey">
                    Crie sua conta para acessar o sistema ITSM
                  </p>
                </div>

                <form @submit.prevent="submitRegister">
                  <v-text-field
                    v-model="form.nome"
                    label="Nome Completo"
                    name="nome"
                    prepend-icon="mdi-account"
                    type="text"
                    required
                    :error-messages="errors.nome"
                    variant="outlined"
                    class="mb-3"
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
                    class="mb-3"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.organization"
                    label="Organização"
                    name="organization"
                    prepend-icon="mdi-domain"
                    type="text"
                    required
                    :error-messages="errors.organization"
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
                    hint="Mínimo 6 caracteres"
                  ></v-text-field>

                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirmar Senha"
                    name="password_confirmation"
                    prepend-icon="mdi-lock-check"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    :append-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPasswordConfirm = !showPasswordConfirm"
                    required
                    :error-messages="errors.password_confirmation"
                    variant="outlined"
                    class="mb-3"
                  ></v-text-field>

                  <v-checkbox
                    v-model="acceptTerms"
                    :error-messages="!acceptTerms && submitted ? ['Você deve aceitar os termos'] : []"
                    class="mb-3"
                  >
                    <template v-slot:label>
                      <div class="text-body-2">
                        Eu concordo com os
                        <a href="#" @click.prevent="showTerms = true" class="text-primary">
                          termos de uso
                        </a>
                        e
                        <a href="#" @click.prevent="showPrivacy = true" class="text-primary">
                          política de privacidade
                        </a>
                      </div>
                    </template>
                  </v-checkbox>
                </form>
              </v-card-text>

              <v-card-actions class="pa-6 pt-0">
                <v-btn
                  color="primary"
                  @click="submitRegister"
                  :loading="loading"
                  :disabled="!acceptTerms"
                  block
                  size="large"
                  variant="elevated"
                >
                  Criar Conta
                </v-btn>
              </v-card-actions>

              <v-divider></v-divider>

              <v-card-actions class="justify-center pa-4">
                <span class="text-body-2">Já tem uma conta?</span>
                <v-btn
                  color="primary"
                  variant="text"
                  @click="$inertia.visit('/login')"
                  class="ml-2"
                >
                  Fazer Login
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>

    <!-- Dialog Termos de Uso -->
    <v-dialog v-model="showTerms" max-width="600">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon left>mdi-file-document</v-icon>
          Termos de Uso
        </v-card-title>
        <v-card-text>
          <div class="text-body-2">
            <h3>1. Aceitação dos Termos</h3>
            <p>Ao utilizar este sistema, você concorda com estes termos.</p>

            <h3>2. Uso do Sistema</h3>
            <p>O sistema destina-se ao gerenciamento de serviços de TI conforme as melhores práticas ITIL.</p>

            <h3>3. Responsabilidades do Usuário</h3>
            <p>Você é responsável por manter a confidencialidade de suas credenciais de acesso.</p>

            <h3>4. Privacidade de Dados</h3>
            <p>Seus dados são protegidos conforme nossa política de privacidade.</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="showTerms = false">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog Política de Privacidade -->
    <v-dialog v-model="showPrivacy" max-width="600">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon left>mdi-shield-account</v-icon>
          Política de Privacidade
        </v-card-title>
        <v-card-text>
          <div class="text-body-2">
            <h3>Coleta de Dados</h3>
            <p>Coletamos apenas os dados necessários para o funcionamento do sistema ITSM.</p>

            <h3>Uso dos Dados</h3>
            <p>Seus dados são utilizados exclusivamente para prover os serviços do sistema.</p>

            <h3>Compartilhamento</h3>
            <p>Não compartilhamos seus dados com terceiros sem seu consentimento.</p>

            <h3>Segurança</h3>
            <p>Implementamos medidas de segurança para proteger suas informações.</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="showPrivacy = false">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
const showPasswordConfirm = ref(false)
const acceptTerms = ref(false)
const submitted = ref(false)
const showTerms = ref(false)
const showPrivacy = ref(false)

const form = useForm({
  nome: '',
  email: '',
  organization: '',
  password: '',
  password_confirmation: ''
})

const submitRegister = () => {
  submitted.value = true

  if (!acceptTerms.value) {
    return
  }

  loading.value = true
  form.post('/register', {
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

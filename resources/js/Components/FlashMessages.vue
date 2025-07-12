<template>
  <div class="flash-messages">
    <!-- Mensagem de sucesso -->
    <v-snackbar
      v-model="showSuccess"
      color="success"
      timeout="4000"
      location="top right"
      variant="elevated"
    >
      <v-icon left>mdi-check-circle</v-icon>
      {{ successMessage }}
      <template v-slot:actions>
        <v-btn
          icon="mdi-close"
          size="small"
          @click="showSuccess = false"
        ></v-btn>
      </template>
    </v-snackbar>

    <!-- Mensagem de erro -->
    <v-snackbar
      v-model="showError"
      color="error"
      timeout="5000"
      location="top right"
      variant="elevated"
    >
      <v-icon left>mdi-alert-circle</v-icon>
      {{ errorMessage }}
      <template v-slot:actions>
        <v-btn
          icon="mdi-close"
          size="small"
          @click="showError = false"
        ></v-btn>
      </template>
    </v-snackbar>

    <!-- Mensagem de info -->
    <v-snackbar
      v-model="showInfo"
      color="info"
      timeout="4000"
      location="top right"
      variant="elevated"
    >
      <v-icon left>mdi-information</v-icon>
      {{ infoMessage }}
      <template v-slot:actions>
        <v-btn
          icon="mdi-close"
          size="small"
          @click="showInfo = false"
        ></v-btn>
      </template>
    </v-snackbar>

    <!-- Mensagem de warning -->
    <v-snackbar
      v-model="showWarning"
      color="warning"
      timeout="4000"
      location="top right"
      variant="elevated"
    >
      <v-icon left>mdi-alert</v-icon>
      {{ warningMessage }}
      <template v-slot:actions>
        <v-btn
          icon="mdi-close"
          size="small"
          @click="showWarning = false"
        ></v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const showSuccess = ref(false)
const showError = ref(false)
const showInfo = ref(false)
const showWarning = ref(false)

const successMessage = ref('')
const errorMessage = ref('')
const infoMessage = ref('')
const warningMessage = ref('')

const page = usePage()

// Função para mostrar mensagens do flash
const checkFlashMessages = () => {
  const flash = page.props.value.flash || {}

  if (flash.success) {
    successMessage.value = flash.success
    showSuccess.value = true
  }

  if (flash.error) {
    errorMessage.value = flash.error
    showError.value = true
  }

  if (flash.info) {
    infoMessage.value = flash.info
    showInfo.value = true
  }

  if (flash.warning) {
    warningMessage.value = flash.warning
    showWarning.value = true
  }

  if (flash.status) {
    infoMessage.value = flash.status
    showInfo.value = true
  }
}

// Vigiar mudanças nas props da página
watch(() => page.props.value.flash, checkFlashMessages, { deep: true })

// Verificar na montagem
onMounted(checkFlashMessages)
</script>

<style scoped>
.flash-messages {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 9999;
}
</style>

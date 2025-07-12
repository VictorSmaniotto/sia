<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit(route('incidentes.index'))"
            />
            <h2 class="ml-4">Novo Incidente</h2>
          </v-card-title>

          <v-card-text>
            <v-form ref="form" @submit.prevent="submeter">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="formulario.titulo"
                    label="Título *"
                    :error-messages="errors.titulo"
                    required
                  />
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    v-model="formulario.descricao"
                    label="Descrição"
                    :error-messages="errors.descricao"
                    rows="4"
                  />
                </v-col>

                <v-col cols="12" md="4">
                  <v-select
                    v-model="formulario.prioridade"
                    :items="prioridadeOptions"
                    label="Prioridade *"
                    :error-messages="errors.prioridade"
                    required
                  />
                </v-col>

                <v-col cols="12" md="4">
                  <v-select
                    v-model="formulario.impacto"
                    :items="impactoOptions"
                    label="Impacto"
                    :error-messages="errors.impacto"
                  />
                </v-col>

                <v-col cols="12" md="4">
                  <v-select
                    v-model="formulario.urgencia"
                    :items="urgenciaOptions"
                    label="Urgência"
                    :error-messages="errors.urgencia"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-autocomplete
                    v-model="formulario.solicitante_id"
                    :items="usuarios"
                    item-title="nome"
                    item-value="id"
                    label="Solicitante"
                    :error-messages="errors.solicitante_id"
                    clearable
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <v-list-item-title>{{ item.raw.nome }}</v-list-item-title>
                        <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                      </v-list-item>
                    </template>
                  </v-autocomplete>
                </v-col>

                <v-col cols="12" md="6">
                  <v-autocomplete
                    v-model="formulario.responsavel_id"
                    :items="usuarios"
                    item-title="nome"
                    item-value="id"
                    label="Responsável"
                    :error-messages="errors.responsavel_id"
                    clearable
                  >
                    <template v-slot:item="{ props, item }">
                      <v-list-item v-bind="props">
                        <v-list-item-title>{{ item.raw.nome }}</v-list-item-title>
                        <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                      </v-list-item>
                    </template>
                  </v-autocomplete>
                </v-col>

                <v-col cols="12" md="6">
                  <v-autocomplete
                    v-model="formulario.problema_id"
                    :items="problemas"
                    item-title="titulo"
                    item-value="id"
                    label="Problema Relacionado"
                    :error-messages="errors.problema_id"
                    clearable
                  />
                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <v-row>
                <v-col cols="12" class="d-flex justify-end gap-2">
                  <v-btn
                    variant="outlined"
                    @click="$inertia.visit(route('incidentes.index'))"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    type="submit"
                    color="primary"
                    :loading="loading"
                  >
                    Criar Incidente
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/inertia-vue3'

const props = defineProps({
  usuarios: Array,
  problemas: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
})

const loading = ref(false)
const form = ref(null)

const formulario = ref({
  titulo: '',
  descricao: '',
  prioridade: null,
  impacto: null,
  urgencia: null,
  solicitante_id: null,
  responsavel_id: null,
  problema_id: null
})

const prioridadeOptions = [
  { title: 'Alta', value: 'Alta' },
  { title: 'Média', value: 'Média' },
  { title: 'Baixa', value: 'Baixa' }
]

const impactoOptions = [
  { title: 'Alto', value: 'Alto' },
  { title: 'Médio', value: 'Médio' },
  { title: 'Baixo', value: 'Baixo' }
]

const urgenciaOptions = [
  { title: 'Alta', value: 'Alta' },
  { title: 'Média', value: 'Média' },
  { title: 'Baixa', value: 'Baixa' }
]

const submeter = async () => {
  const { valid } = await form.value.validate()

  if (!valid) return

  loading.value = true

  router.post(route('incidentes.store'), formulario.value, {
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

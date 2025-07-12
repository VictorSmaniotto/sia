<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit(route('incidentes.show', incidente.id))"
            />
            <h2 class="ml-4">Editar Incidente #{{ incidente.id }}</h2>
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
                    v-model="formulario.status"
                    :items="statusOptions"
                    label="Status *"
                    :error-messages="errors.status"
                    required
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

                <v-col cols="12" md="4">
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

                <v-col cols="12" md="4">
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

                <v-col cols="12" v-if="['Resolvido', 'Fechado'].includes(formulario.status)">
                  <v-textarea
                    v-model="formulario.resolucao"
                    label="Resolução"
                    :error-messages="errors.resolucao"
                    rows="4"
                    hint="Descreva como o incidente foi resolvido"
                  />
                </v-col>
              </v-row>

              <v-divider class="my-4" />

              <v-row>
                <v-col cols="12" class="d-flex justify-end gap-2">
                  <v-btn
                    variant="outlined"
                    @click="$inertia.visit(route('incidentes.show', incidente.id))"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    type="submit"
                    color="primary"
                    :loading="loading"
                  >
                    Salvar Alterações
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
import { ref, watch } from 'vue'
import { router } from '@inertiajs/inertia-vue3'

const props = defineProps({
  incidente: Object,
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
  titulo: props.incidente.titulo,
  descricao: props.incidente.descricao,
  status: props.incidente.status,
  prioridade: props.incidente.prioridade,
  impacto: props.incidente.impacto,
  urgencia: props.incidente.urgencia,
  solicitante_id: props.incidente.solicitante_id,
  responsavel_id: props.incidente.responsavel_id,
  problema_id: props.incidente.problema_id,
  resolucao: props.incidente.resolucao
})

const statusOptions = [
  { title: 'Aberto', value: 'Aberto' },
  { title: 'Em Andamento', value: 'Em Andamento' },
  { title: 'Aguardando', value: 'Aguardando' },
  { title: 'Resolvido', value: 'Resolvido' },
  { title: 'Fechado', value: 'Fechado' }
]

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

// Limpar resolução se status não for Resolvido ou Fechado
watch(() => formulario.value.status, (novoStatus) => {
  if (!['Resolvido', 'Fechado'].includes(novoStatus)) {
    formulario.value.resolucao = ''
  }
})

const submeter = async () => {
  const { valid } = await form.value.validate()

  if (!valid) return

  loading.value = true

  router.put(route('incidentes.update', props.incidente.id), formulario.value, {
    onFinish: () => {
      loading.value = false
    }
  })
}
</script>

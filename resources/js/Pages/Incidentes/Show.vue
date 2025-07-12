<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <div class="d-flex align-center">
              <v-btn
                icon="mdi-arrow-left"
                variant="text"
                @click="$inertia.visit(route('incidentes.index'))"
              />
              <h2 class="ml-4">Incidente #{{ incidente.id }}</h2>
            </div>
            <div>
              <v-btn
                color="primary"
                prepend-icon="mdi-pencil"
                @click="$inertia.visit(route('incidentes.edit', incidente.id))"
              >
                Editar
              </v-btn>
            </div>
          </v-card-title>

          <v-card-text>
            <!-- Informações principais -->
            <v-row>
              <v-col cols="12">
                <h3>{{ incidente.titulo }}</h3>
                <p class="text-body-1 mt-2">{{ incidente.descricao || 'Nenhuma descrição fornecida' }}</p>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <!-- Status e prioridades -->
            <v-row>
              <v-col cols="12" md="3">
                <v-card variant="outlined">
                  <v-card-text class="text-center">
                    <div class="text-caption">Status</div>
                    <v-chip
                      :color="getStatusColor(incidente.status)"
                      size="large"
                      variant="flat"
                      class="mt-2"
                    >
                      {{ incidente.status }}
                    </v-chip>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="3">
                <v-card variant="outlined">
                  <v-card-text class="text-center">
                    <div class="text-caption">Prioridade</div>
                    <v-chip
                      :color="getPrioridadeColor(incidente.prioridade)"
                      size="large"
                      variant="flat"
                      class="mt-2"
                    >
                      {{ incidente.prioridade }}
                    </v-chip>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="3" v-if="incidente.impacto">
                <v-card variant="outlined">
                  <v-card-text class="text-center">
                    <div class="text-caption">Impacto</div>
                    <div class="text-h6 mt-2">{{ incidente.impacto }}</div>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="3" v-if="incidente.urgencia">
                <v-card variant="outlined">
                  <v-card-text class="text-center">
                    <div class="text-caption">Urgência</div>
                    <div class="text-h6 mt-2">{{ incidente.urgencia }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <!-- Pessoas envolvidas -->
            <v-row>
              <v-col cols="12" md="6">
                <v-card variant="outlined">
                  <v-card-text>
                    <div class="text-caption">Solicitante</div>
                    <div class="text-body-1 mt-1">
                      {{ incidente.solicitante?.nome || 'Não informado' }}
                    </div>
                    <div class="text-caption text-grey">
                      {{ incidente.solicitante?.email || '' }}
                    </div>
                  </v-card-text>
                </v-card>
              </v-col>

              <v-col cols="12" md="6">
                <v-card variant="outlined">
                  <v-card-text>
                    <div class="text-caption">Responsável</div>
                    <div class="text-body-1 mt-1">
                      {{ incidente.responsavel?.nome || 'Não atribuído' }}
                    </div>
                    <div class="text-caption text-grey">
                      {{ incidente.responsavel?.email || '' }}
                    </div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <!-- Problema relacionado -->
            <v-row v-if="incidente.problema">
              <v-col cols="12">
                <v-card variant="outlined">
                  <v-card-text>
                    <div class="text-caption">Problema Relacionado</div>
                    <div class="text-body-1 mt-1">{{ incidente.problema.titulo }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <!-- Resolução -->
            <v-row v-if="incidente.resolucao">
              <v-col cols="12">
                <v-card variant="outlined">
                  <v-card-text>
                    <div class="text-caption">Resolução</div>
                    <div class="text-body-1 mt-1">{{ incidente.resolucao }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <!-- Datas -->
            <v-row>
              <v-col cols="12" md="4">
                <div class="text-caption">Criado em</div>
                <div class="text-body-2">{{ formatDate(incidente.criado_em) }}</div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="text-caption">Atualizado em</div>
                <div class="text-body-2">{{ formatDate(incidente.atualizado_em) }}</div>
              </v-col>
              <v-col cols="12" md="4" v-if="incidente.resolvido_em">
                <div class="text-caption">Resolvido em</div>
                <div class="text-body-2">{{ formatDate(incidente.resolvido_em) }}</div>
              </v-col>
            </v-row>

            <!-- Comentários -->
            <v-divider class="my-4" />

            <div>
              <h4 class="mb-4">Comentários</h4>

              <!-- Form para novo comentário -->
              <v-card variant="outlined" class="mb-4">
                <v-card-text>
                  <v-textarea
                    v-model="novoComentario"
                    label="Adicionar comentário"
                    rows="3"
                    :disabled="adicionandoComentario"
                  />
                  <div class="d-flex justify-end mt-2">
                    <v-btn
                      color="primary"
                      :loading="adicionandoComentario"
                      @click="adicionarComentario"
                      :disabled="!novoComentario.trim()"
                    >
                      Adicionar
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>

              <!-- Lista de comentários -->
              <div v-if="incidente.comentarios?.length">
                <v-card
                  v-for="comentario in incidente.comentarios"
                  :key="comentario.id"
                  variant="outlined"
                  class="mb-2"
                >
                  <v-card-text>
                    <div class="d-flex justify-space-between align-start">
                      <div>
                        <div class="text-body-1">{{ comentario.conteudo }}</div>
                        <div class="text-caption text-grey mt-1">
                          Por {{ comentario.usuario?.nome }} em {{ formatDate(comentario.criado_em) }}
                        </div>
                      </div>
                    </div>
                  </v-card-text>
                </v-card>
              </div>
              <div v-else class="text-center text-grey py-4">
                Nenhum comentário ainda
              </div>
            </div>
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
  incidente: Object,
  usuarios: Array,
  problemas: Array
})

const novoComentario = ref('')
const adicionandoComentario = ref(false)

const getStatusColor = (status) => {
  const cores = {
    'Aberto': 'red',
    'Em Andamento': 'orange',
    'Aguardando': 'yellow',
    'Resolvido': 'green',
    'Fechado': 'blue-grey'
  }
  return cores[status] || 'grey'
}

const getPrioridadeColor = (prioridade) => {
  const cores = {
    'Alta': 'red',
    'Média': 'orange',
    'Baixa': 'green'
  }
  return cores[prioridade] || 'grey'
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('pt-BR')
}

const adicionarComentario = () => {
  if (!novoComentario.value.trim()) return

  adicionandoComentario.value = true

  router.post(route('comentarios.store'), {
    incidente_id: props.incidente.id,
    conteudo: novoComentario.value
  }, {
    onSuccess: () => {
      novoComentario.value = ''
    },
    onFinish: () => {
      adicionandoComentario.value = false
    }
  })
}
</script>

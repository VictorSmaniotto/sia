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

            <ComentariosSection
              :comentarios="incidente.comentarios || []"
              :entity-type="'incidente'"
              :entity-id="incidente.id"
            />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/inertia-vue3'
import ComentariosSection from '@/Components/ComentariosSection.vue'

const props = defineProps({
  incidente: Object,
  usuarios: Array,
  problemas: Array
})

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
</script>

<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <div class="d-flex align-center">
              <v-btn
                icon="mdi-arrow-left"
                variant="text"
                @click="$inertia.visit(route('problemas.index'))"
                class="mr-3"
              />
              <div>
                <h2>Problema #{{ problema.id }}</h2>
                <div class="text-subtitle-1 text-medium-emphasis">{{ problema.titulo }}</div>
              </div>
            </div>
            <div>
              <v-btn
                color="primary"
                prepend-icon="mdi-pencil"
                @click="$inertia.visit(route('problemas.edit', problema.id))"
                class="mr-2"
              >
                Editar
              </v-btn>
              <v-btn
                color="error"
                prepend-icon="mdi-delete"
                variant="outlined"
                @click="confirmarExclusao"
              >
                Excluir
              </v-btn>
            </div>
          </v-card-title>

          <v-tabs v-model="tabAtiva">
            <v-tab value="detalhes">Detalhes</v-tab>
            <v-tab value="incidentes">Incidentes Relacionados</v-tab>
            <v-tab value="comentarios">Comentários</v-tab>
            <v-tab value="historico">Histórico</v-tab>
          </v-tabs>

          <v-card-text>
            <v-tabs-window v-model="tabAtiva">
              <!-- Aba Detalhes -->
              <v-tabs-window-item value="detalhes">
                <v-row>
                  <v-col cols="12" md="8">
                    <!-- Informações Básicas -->
                    <v-card variant="outlined" class="mb-4">
                      <v-card-title>Informações Básicas</v-card-title>
                      <v-card-text>
                        <v-row>
                          <v-col cols="12" md="6">
                            <div class="mb-3">
                              <strong>Status:</strong>
                              <v-chip
                                :color="getStatusColor(problema.status)"
                                size="small"
                                variant="tonal"
                                class="ml-2"
                              >
                                {{ problema.status }}
                              </v-chip>
                            </div>
                          </v-col>
                          <v-col cols="12" md="6">
                            <div class="mb-3">
                              <strong>Prioridade:</strong>
                              <v-chip
                                :color="getPrioridadeColor(problema.prioridade)"
                                size="small"
                                variant="tonal"
                                class="ml-2"
                              >
                                {{ problema.prioridade }}
                              </v-chip>
                            </div>
                          </v-col>
                          <v-col cols="12" md="6">
                            <div class="mb-3">
                              <strong>Responsável:</strong>
                              {{ problema.responsavel?.nome || 'Não atribuído' }}
                            </div>
                          </v-col>
                          <v-col cols="12" md="6">
                            <div class="mb-3">
                              <strong>Criado em:</strong>
                              {{ formatDateTime(problema.criado_em) }}
                            </div>
                          </v-col>
                        </v-row>

                        <v-divider class="my-4" />

                        <div class="mb-3">
                          <strong>Descrição:</strong>
                          <p class="mt-2">{{ problema.descricao || 'Nenhuma descrição fornecida' }}</p>
                        </div>
                      </v-card-text>
                    </v-card>

                    <!-- Análise Técnica -->
                    <v-card variant="outlined">
                      <v-card-title>Análise Técnica</v-card-title>
                      <v-card-text>
                        <div class="mb-4" v-if="problema.causa_raiz">
                          <strong>Causa Raiz:</strong>
                          <p class="mt-2">{{ problema.causa_raiz }}</p>
                        </div>

                        <div class="mb-4" v-if="problema.solucao_contorno">
                          <strong>Solução de Contorno (Workaround):</strong>
                          <p class="mt-2">{{ problema.solucao_contorno }}</p>
                        </div>

                        <div class="mb-4" v-if="problema.solucao_definitiva">
                          <strong>Solução Definitiva:</strong>
                          <p class="mt-2">{{ problema.solucao_definitiva }}</p>
                        </div>

                        <div v-if="!problema.causa_raiz && !problema.solucao_contorno && !problema.solucao_definitiva">
                          <p class="text-medium-emphasis">Nenhuma análise técnica registrada ainda.</p>
                        </div>
                      </v-card-text>
                    </v-card>
                  </v-col>

                  <v-col cols="12" md="4">
                    <!-- Timeline de Status -->
                    <v-card variant="outlined">
                      <v-card-title>Progresso</v-card-title>
                      <v-card-text>
                        <v-timeline density="compact">
                          <v-timeline-item
                            v-for="(item, index) in timelineItems"
                            :key="index"
                            :dot-color="item.color"
                            size="small"
                          >
                            <div>
                              <strong>{{ item.status }}</strong>
                              <div class="text-caption text-medium-emphasis">
                                {{ item.data }}
                              </div>
                            </div>
                          </v-timeline-item>
                        </v-timeline>
                      </v-card-text>
                    </v-card>
                  </v-col>
                </v-row>
              </v-tabs-window-item>

              <!-- Aba Incidentes Relacionados -->
              <v-tabs-window-item value="incidentes">
                <v-card variant="outlined">
                  <v-card-title>Incidentes Relacionados</v-card-title>
                  <v-card-text>
                    <div v-if="incidentesRelacionados.length > 0">
                      <v-data-table
                        :headers="incidentesHeaders"
                        :items="incidentesRelacionados"
                        item-key="id"
                        class="elevation-1"
                      >
                        <template #item.status="{ item }">
                          <v-chip size="small" variant="tonal">
                            {{ item.status }}
                          </v-chip>
                        </template>

                        <template #item.actions="{ item }">
                          <v-btn
                            icon="mdi-eye"
                            size="small"
                            variant="text"
                            @click="$inertia.visit(route('incidentes.show', item.id))"
                            title="Visualizar Incidente"
                          />
                        </template>
                      </v-data-table>
                    </div>
                    <div v-else>
                      <p class="text-medium-emphasis">Nenhum incidente relacionado a este problema.</p>
                    </div>
                  </v-card-text>
                </v-card>
              </v-tabs-window-item>

              <!-- Aba Comentários -->
              <v-tabs-window-item value="comentarios">
                <ComentariosSection
                  :comentarios="comentarios"
                  tipo-entidade="problema"
                  :entidade-id="problema.id"
                  @comentario-adicionado="recarregarPagina"
                />
              </v-tabs-window-item>

              <!-- Aba Histórico -->
              <v-tabs-window-item value="historico">
                <v-card variant="outlined">
                  <v-card-title>Histórico de Alterações</v-card-title>
                  <v-card-text>
                    <p class="text-medium-emphasis">Funcionalidade de histórico será implementada em breve.</p>
                  </v-card-text>
                </v-card>
              </v-tabs-window-item>
            </v-tabs-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog de Confirmação de Exclusão -->
    <v-dialog v-model="dialogExclusao" max-width="500px">
      <v-card>
        <v-card-title>Confirmar Exclusão</v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir este problema? Esta ação não pode ser desfeita.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="dialogExclusao = false">Cancelar</v-btn>
          <v-btn color="error" @click="excluirProblema">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import ComentariosSection from '../../Components/ComentariosSection.vue'

export default {
  name: 'ProblemasShow',
  components: {
    ComentariosSection
  },
  props: {
    problema: Object,
    incidentesRelacionados: {
      type: Array,
      default: () => []
    },
    comentarios: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      tabAtiva: 'detalhes',
      dialogExclusao: false,
      incidentesHeaders: [
        { title: 'ID', key: 'id', width: '80px' },
        { title: 'Título', key: 'titulo' },
        { title: 'Status', key: 'status', width: '120px' },
        { title: 'Criado em', key: 'criado_em', width: '120px' },
        { title: 'Ações', key: 'actions', sortable: false, width: '100px' }
      ]
    }
  },
  computed: {
    timelineItems() {
      const items = [
        {
          status: 'Criado',
          data: this.formatDateTime(this.problema.criado_em),
          color: 'blue'
        }
      ]

      if (this.problema.status !== 'Novo') {
        items.push({
          status: this.problema.status,
          data: this.formatDateTime(this.problema.atualizado_em),
          color: this.getStatusColor(this.problema.status)
        })
      }

      return items
    }
  },
  methods: {
    route(name, params) {
      return this.$root.route ? this.$root.route(name, params) : '#'
    },
    formatDateTime(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleString('pt-BR')
    },
    getStatusColor(status) {
      const cores = {
        'Novo': 'blue',
        'Investigando': 'orange',
        'Erro Conhecido': 'purple',
        'Resolvido': 'green',
        'Fechado': 'grey'
      }
      return cores[status] || 'grey'
    },
    getPrioridadeColor(prioridade) {
      const cores = {
        'Alta': 'red',
        'Média': 'orange',
        'Baixa': 'green'
      }
      return cores[prioridade] || 'grey'
    },
    confirmarExclusao() {
      this.dialogExclusao = true
    },
    excluirProblema() {
      this.$inertia.delete(this.route('problemas.destroy', this.problema.id), {
        onSuccess: () => {
          this.$inertia.visit(this.route('problemas.index'))
        }
      })
    },
    recarregarPagina() {
      this.$inertia.reload()
    }
  }
}
</script>

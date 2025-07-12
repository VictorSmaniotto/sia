<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <h2>Gerenciamento de Problemas</h2>
            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              @click="$inertia.visit(route('problemas.create'))"
            >
              Novo Problema
            </v-btn>
          </v-card-title>

          <!-- Filtros -->
          <v-card-text>
            <v-row>
              <v-col cols="12" md="3">
                <v-text-field
                  v-model="filtros.busca"
                  label="Buscar"
                  prepend-inner-icon="mdi-magnify"
                  clearable
                  @input="aplicarFiltros"
                />
              </v-col>
              <v-col cols="12" md="2">
                <v-select
                  v-model="filtros.status"
                  :items="statusOptions"
                  label="Status"
                  clearable
                  @update:model-value="aplicarFiltros"
                />
              </v-col>
              <v-col cols="12" md="2">
                <v-select
                  v-model="filtros.prioridade"
                  :items="prioridadeOptions"
                  label="Prioridade"
                  clearable
                  @update:model-value="aplicarFiltros"
                />
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="filtros.responsavel"
                  :items="usuarioOptions"
                  item-title="nome"
                  item-value="id"
                  label="Responsável"
                  clearable
                  @update:model-value="aplicarFiltros"
                />
              </v-col>
              <v-col cols="12" md="2">
                <v-btn
                  color="secondary"
                  variant="outlined"
                  @click="limparFiltros"
                >
                  Limpar Filtros
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>

          <!-- Tabela de Problemas -->
          <v-data-table
            :headers="headers"
            :items="problemasFiltrados"
            :loading="loading"
            item-key="id"
            class="elevation-1"
            :items-per-page="25"
            :search="filtros.busca"
          >
            <!-- Status com cores -->
            <template #item.status="{ item }">
              <v-chip
                :color="getStatusColor(item.status)"
                size="small"
                variant="tonal"
              >
                {{ item.status }}
              </v-chip>
            </template>

            <!-- Prioridade com cores -->
            <template #item.prioridade="{ item }">
              <v-chip
                :color="getPrioridadeColor(item.prioridade)"
                size="small"
                variant="tonal"
              >
                {{ item.prioridade }}
              </v-chip>
            </template>

            <!-- Data formatada -->
            <template #item.criado_em="{ item }">
              {{ formatDate(item.criado_em) }}
            </template>

            <!-- Ações -->
            <template #item.actions="{ item }">
              <v-btn
                icon="mdi-eye"
                size="small"
                variant="text"
                @click="$inertia.visit(route('problemas.show', item.id))"
                title="Visualizar"
              />
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                @click="$inertia.visit(route('problemas.edit', item.id))"
                title="Editar"
              />
              <v-btn
                icon="mdi-delete"
                size="small"
                variant="text"
                color="error"
                @click="confirmarExclusao(item)"
                title="Excluir"
              />
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog de Confirmação de Exclusão -->
    <v-dialog v-model="dialogExclusao" max-width="500px">
      <v-card>
        <v-card-title>Confirmar Exclusão</v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir o problema "{{ problemaParaExcluir?.titulo }}"?
          Esta ação não pode ser desfeita.
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
export default {
  name: 'ProblemasIndex',
  props: {
    problemas: Array,
    usuarios: Array
  },
  data() {
    return {
      loading: false,
      dialogExclusao: false,
      problemaParaExcluir: null,
      filtros: {
        busca: '',
        status: null,
        prioridade: null,
        responsavel: null
      },
      headers: [
        { title: 'ID', key: 'id', width: '80px' },
        { title: 'Título', key: 'titulo' },
        { title: 'Status', key: 'status', width: '120px' },
        { title: 'Prioridade', key: 'prioridade', width: '120px' },
        { title: 'Responsável', key: 'responsavel.nome', width: '150px' },
        { title: 'Criado em', key: 'criado_em', width: '120px' },
        { title: 'Ações', key: 'actions', sortable: false, width: '150px' }
      ],
      statusOptions: [
        'Novo',
        'Investigando',
        'Erro Conhecido',
        'Resolvido',
        'Fechado'
      ],
      prioridadeOptions: [
        'Alta',
        'Média',
        'Baixa'
      ]
    }
  },
  computed: {
    usuarioOptions() {
      return this.usuarios || []
    },
    problemasFiltrados() {
      let problemas = this.problemas || []

      if (this.filtros.status) {
        problemas = problemas.filter(p => p.status === this.filtros.status)
      }

      if (this.filtros.prioridade) {
        problemas = problemas.filter(p => p.prioridade === this.filtros.prioridade)
      }

      if (this.filtros.responsavel) {
        problemas = problemas.filter(p => p.responsavel_id === this.filtros.responsavel)
      }

      return problemas
    }
  },
  methods: {
    route(name, params) {
      return this.$root.route ? this.$root.route(name, params) : '#'
    },
    aplicarFiltros() {
      // Filtros são aplicados automaticamente via computed
    },
    limparFiltros() {
      this.filtros = {
        busca: '',
        status: null,
        prioridade: null,
        responsavel: null
      }
    },
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('pt-BR')
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
    confirmarExclusao(problema) {
      this.problemaParaExcluir = problema
      this.dialogExclusao = true
    },
    excluirProblema() {
      if (this.problemaParaExcluir) {
        this.$inertia.delete(this.route('problemas.destroy', this.problemaParaExcluir.id), {
          onSuccess: () => {
            this.dialogExclusao = false
            this.problemaParaExcluir = null
          }
        })
      }
    }
  }
}
</script>

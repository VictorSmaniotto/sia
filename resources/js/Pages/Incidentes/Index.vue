<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <h2>Gerenciamento de Incidentes</h2>
            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              @click="$inertia.visit(route('incidentes.create'))"
            >
              Novo Incidente
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

          <!-- Tabela -->
          <v-data-table
            :headers="headers"
            :items="incidentes.data"
            :loading="loading"
            class="elevation-1"
            item-value="id"
          >
            <template v-slot:item.status="{ item }">
              <v-chip
                :color="getStatusColor(item.status)"
                size="small"
                variant="flat"
              >
                {{ item.status }}
              </v-chip>
            </template>

            <template v-slot:item.prioridade="{ item }">
              <v-chip
                :color="getPrioridadeColor(item.prioridade)"
                size="small"
                variant="flat"
              >
                {{ item.prioridade }}
              </v-chip>
            </template>

            <template v-slot:item.solicitante="{ item }">
              {{ item.solicitante?.nome || '-' }}
            </template>

            <template v-slot:item.responsavel="{ item }">
              {{ item.responsavel?.nome || 'Não atribuído' }}
            </template>

            <template v-slot:item.criado_em="{ item }">
              {{ formatDate(item.criado_em) }}
            </template>

            <template v-slot:item.actions="{ item }">
              <v-btn
                icon="mdi-eye"
                size="small"
                variant="text"
                @click="$inertia.visit(route('incidentes.show', item.id))"
              />
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                @click="$inertia.visit(route('incidentes.edit', item.id))"
              />
              <v-btn
                icon="mdi-delete"
                size="small"
                variant="text"
                color="error"
                @click="confirmarExclusao(item)"
              />
            </template>
          </v-data-table>

          <!-- Paginação -->
          <v-card-actions v-if="incidentes.last_page > 1">
            <v-pagination
              v-model="paginaAtual"
              :length="incidentes.last_page"
              @update:model-value="mudarPagina"
            />
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog de confirmação -->
    <v-dialog v-model="dialogExclusao" max-width="500">
      <v-card>
        <v-card-title>Confirmar Exclusão</v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir o incidente "{{ incidenteParaExcluir?.titulo }}"?
          Esta ação não pode ser desfeita.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="dialogExclusao = false">
            Cancelar
          </v-btn>
          <v-btn color="error" @click="excluirIncidente">
            Excluir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/inertia-vue3'

const props = defineProps({
  incidentes: Object,
  usuarios: Array,
  filtros: Object
})

const loading = ref(false)
const dialogExclusao = ref(false)
const incidenteParaExcluir = ref(null)

const filtros = ref({
  busca: props.filtros.busca || '',
  status: props.filtros.status || null,
  prioridade: props.filtros.prioridade || null,
  responsavel: props.filtros.responsavel || null
})

const paginaAtual = ref(props.incidentes.current_page)

const headers = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Título', key: 'titulo' },
  { title: 'Status', key: 'status', width: '120px' },
  { title: 'Prioridade', key: 'prioridade', width: '120px' },
  { title: 'Solicitante', key: 'solicitante', width: '150px' },
  { title: 'Responsável', key: 'responsavel', width: '150px' },
  { title: 'Criado em', key: 'criado_em', width: '120px' },
  { title: 'Ações', key: 'actions', sortable: false, width: '120px' }
]

const statusOptions = [
  'Aberto', 'Em Andamento', 'Aguardando', 'Resolvido', 'Fechado'
]

const prioridadeOptions = [
  'Alta', 'Média', 'Baixa'
]

const usuarioOptions = computed(() => props.usuarios)

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

const aplicarFiltros = () => {
  loading.value = true
  router.get(route('incidentes.index'), filtros.value, {
    preserveState: true,
    onFinish: () => {
      loading.value = false
    }
  })
}

const limparFiltros = () => {
  filtros.value = {
    busca: '',
    status: null,
    prioridade: null,
    responsavel: null
  }
  aplicarFiltros()
}

const mudarPagina = (pagina) => {
  loading.value = true
  router.get(route('incidentes.index'), {
    ...filtros.value,
    page: pagina
  }, {
    preserveState: true,
    onFinish: () => {
      loading.value = false
    }
  })
}

const confirmarExclusao = (incidente) => {
  incidenteParaExcluir.value = incidente
  dialogExclusao.value = true
}

const excluirIncidente = () => {
  router.delete(route('incidentes.destroy', incidenteParaExcluir.value.id), {
    onSuccess: () => {
      dialogExclusao.value = false
      incidenteParaExcluir.value = null
    }
  })
}
</script>

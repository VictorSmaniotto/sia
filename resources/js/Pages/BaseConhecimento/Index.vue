<template>
  <AppLayout>
    <v-container fluid>
      <!-- Cabeçalho -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center justify-space-between mb-6">
            <div class="d-flex align-center">
              <v-icon size="32" color="primary" class="mr-3">mdi-book-open-page-variant</v-icon>
              <div>
                <h1 class="text-h4 font-weight-medium">Base de Conhecimento</h1>
                <p class="text-body-2 text-grey ma-0">
                  Artigos, tutoriais e documentação técnica
                </p>
              </div>
            </div>
            <v-btn
              color="primary"
              size="large"
              @click="$inertia.visit('/base-conhecimento/create')"
              variant="elevated"
            >
              <v-icon left>mdi-plus</v-icon>
              Novo Artigo
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Filtros e Busca -->
      <v-row>
        <v-col cols="12">
          <v-card class="mb-6">
            <v-card-text>
              <v-row align="center">
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="filtros.busca"
                    label="Buscar artigos..."
                    prepend-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    @input="aplicarFiltros"
                    clearable
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="3">
                  <v-select
                    v-model="filtros.categoria"
                    label="Categoria"
                    :items="categorias"
                    variant="outlined"
                    density="compact"
                    @update:model-value="aplicarFiltros"
                    clearable
                  ></v-select>
                </v-col>

                <v-col cols="12" md="3">
                  <v-select
                    v-model="filtros.status"
                    label="Status"
                    :items="statusOptions"
                    variant="outlined"
                    density="compact"
                    @update:model-value="aplicarFiltros"
                    clearable
                  ></v-select>
                </v-col>

                <v-col cols="12" md="2">
                  <v-btn
                    @click="limparFiltros"
                    variant="outlined"
                    block
                  >
                    <v-icon left>mdi-filter-off</v-icon>
                    Limpar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Estatísticas Rápidas -->
      <v-row class="mb-4">
        <v-col cols="12" sm="6" md="3">
          <v-card color="primary" dark>
            <v-card-text class="text-center">
              <v-icon size="40" class="mb-2">mdi-book-multiple</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.total }}</div>
              <div class="text-body-2">Total de Artigos</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card color="success" dark>
            <v-card-text class="text-center">
              <v-icon size="40" class="mb-2">mdi-check-circle</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.publicados }}</div>
              <div class="text-body-2">Publicados</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card color="warning" dark>
            <v-card-text class="text-center">
              <v-icon size="40" class="mb-2">mdi-clock-outline</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.rascunhos }}</div>
              <div class="text-body-2">Rascunhos</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card color="info" dark>
            <v-card-text class="text-center">
              <v-icon size="40" class="mb-2">mdi-eye</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.visualizacoes }}</div>
              <div class="text-body-2">Visualizações</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Lista de Artigos -->
      <v-row>
        <v-col cols="12">
          <div v-if="artigos.data.length === 0" class="text-center py-8">
            <v-icon size="64" color="grey" class="mb-4">mdi-book-open-page-variant</v-icon>
            <h3 class="text-h6 text-grey mb-2">Nenhum artigo encontrado</h3>
            <p class="text-body-2 text-grey">
              {{ filtros.busca || filtros.categoria || filtros.status
                 ? 'Tente ajustar os filtros para encontrar artigos.'
                 : 'Comece criando seu primeiro artigo da base de conhecimento.' }}
            </p>
            <v-btn
              v-if="!filtros.busca && !filtros.categoria && !filtros.status"
              color="primary"
              @click="$inertia.visit('/base-conhecimento/create')"
              class="mt-4"
            >
              <v-icon left>mdi-plus</v-icon>
              Criar Primeiro Artigo
            </v-btn>
          </div>

          <v-row v-else>
            <v-col
              v-for="artigo in artigos.data"
              :key="artigo.id"
              cols="12"
              md="6"
              lg="4"
            >
              <v-card
                hover
                class="h-100 d-flex flex-column"
                @click="$inertia.visit(`/base-conhecimento/${artigo.id}`)"
              >
                <v-card-text class="flex-grow-1">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <v-chip
                      :color="getCategoriaColor(artigo.categoria)"
                      size="small"
                      variant="flat"
                    >
                      {{ artigo.categoria }}
                    </v-chip>
                    <v-chip
                      :color="getStatusColor(artigo.status)"
                      size="small"
                      variant="outlined"
                    >
                      {{ artigo.status }}
                    </v-chip>
                  </div>

                  <h3 class="text-h6 mb-2 text-truncate-2">{{ artigo.titulo }}</h3>

                  <p class="text-body-2 text-grey text-truncate-3 mb-3">
                    {{ artigo.resumo || artigo.conteudo }}
                  </p>

                  <div class="d-flex align-center text-caption text-grey mb-2">
                    <v-icon size="small" class="mr-1">mdi-account</v-icon>
                    {{ artigo.autor?.nome }}
                    <v-spacer></v-spacer>
                    <v-icon size="small" class="mr-1">mdi-calendar</v-icon>
                    {{ formatDate(artigo.criado_em) }}
                  </div>

                  <div class="d-flex align-center text-caption text-grey">
                    <v-icon size="small" class="mr-1">mdi-eye</v-icon>
                    {{ artigo.visualizacoes || 0 }} visualizações
                    <v-spacer></v-spacer>
                    <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
                    {{ artigo.tempo_leitura || 5 }} min
                  </div>
                </v-card-text>

                <v-card-actions>
                  <v-btn
                    variant="text"
                    @click.stop="$inertia.visit(`/base-conhecimento/${artigo.id}`)"
                  >
                    <v-icon left>mdi-eye</v-icon>
                    Ver Artigo
                  </v-btn>

                  <v-spacer></v-spacer>

                  <v-btn
                    icon="mdi-pencil"
                    variant="text"
                    size="small"
                    @click.stop="$inertia.visit(`/base-conhecimento/${artigo.id}/edit`)"
                  ></v-btn>

                  <v-btn
                    icon="mdi-share-variant"
                    variant="text"
                    size="small"
                    @click.stop="compartilhar(artigo)"
                  ></v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>

          <!-- Paginação -->
          <div v-if="artigos.data.length > 0" class="d-flex justify-center mt-6">
            <v-pagination
              :length="artigos.last_page"
              :model-value="artigos.current_page"
              @update:model-value="mudarPagina"
            ></v-pagination>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  artigos: Object,
  filtros: Object,
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      publicados: 0,
      rascunhos: 0,
      visualizacoes: 0
    })
  }
})

const filtros = ref({
  busca: props.filtros?.busca || '',
  categoria: props.filtros?.categoria || '',
  status: props.filtros?.status || ''
})

const categorias = [
  { title: 'Tutoriais', value: 'tutoriais' },
  { title: 'Procedimentos', value: 'procedimentos' },
  { title: 'FAQ', value: 'faq' },
  { title: 'Troubleshooting', value: 'troubleshooting' },
  { title: 'Documentação', value: 'documentacao' },
  { title: 'Políticas', value: 'politicas' }
]

const statusOptions = [
  { title: 'Publicado', value: 'publicado' },
  { title: 'Rascunho', value: 'rascunho' },
  { title: 'Revisão', value: 'revisao' },
  { title: 'Arquivado', value: 'arquivado' }
]

const getCategoriaColor = (categoria) => {
  const cores = {
    'tutoriais': 'blue',
    'procedimentos': 'green',
    'faq': 'orange',
    'troubleshooting': 'red',
    'documentacao': 'purple',
    'politicas': 'brown'
  }
  return cores[categoria] || 'grey'
}

const getStatusColor = (status) => {
  const cores = {
    'publicado': 'success',
    'rascunho': 'warning',
    'revisao': 'info',
    'arquivado': 'grey'
  }
  return cores[status] || 'grey'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const aplicarFiltros = () => {
  router.get('/base-conhecimento', filtros.value, {
    preserveState: true,
    replace: true
  })
}

const limparFiltros = () => {
  filtros.value = {
    busca: '',
    categoria: '',
    status: ''
  }
  aplicarFiltros()
}

const mudarPagina = (pagina) => {
  router.get('/base-conhecimento', {
    ...filtros.value,
    page: pagina
  }, {
    preserveState: true
  })
}

const compartilhar = (artigo) => {
  if (navigator.share) {
    navigator.share({
      title: artigo.titulo,
      text: artigo.resumo,
      url: window.location.origin + `/base-conhecimento/${artigo.id}`
    })
  } else {
    // Fallback para copiar URL
    navigator.clipboard.writeText(window.location.origin + `/base-conhecimento/${artigo.id}`)
  }
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  overflow: hidden;
}

.text-truncate-3 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  overflow: hidden;
}
</style>

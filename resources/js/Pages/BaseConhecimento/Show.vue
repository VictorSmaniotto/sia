<template>
  <AppLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12" lg="8">
          <!-- Cabeçalho do Artigo -->
          <div class="d-flex align-center mb-4">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit('/base-conhecimento')"
              class="mr-3"
            />
            <div class="flex-grow-1">
              <div class="d-flex align-center mb-2">
                <v-chip
                  :color="getCategoriaColor(artigo.categoria)"
                  size="small"
                  variant="flat"
                  class="mr-2"
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
              <h1 class="text-h4 font-weight-medium mb-2">{{ artigo.titulo }}</h1>
            </div>

            <v-menu>
              <template v-slot:activator="{ props }">
                <v-btn
                  icon="mdi-dots-vertical"
                  variant="text"
                  v-bind="props"
                />
              </template>
              <v-list>
                <v-list-item @click="$inertia.visit(`/base-conhecimento/${artigo.id}/edit`)">
                  <template v-slot:prepend>
                    <v-icon>mdi-pencil</v-icon>
                  </template>
                  <v-list-item-title>Editar</v-list-item-title>
                </v-list-item>
                <v-list-item @click="compartilhar">
                  <template v-slot:prepend>
                    <v-icon>mdi-share-variant</v-icon>
                  </template>
                  <v-list-item-title>Compartilhar</v-list-item-title>
                </v-list-item>
                <v-list-item @click="imprimir">
                  <template v-slot:prepend>
                    <v-icon>mdi-printer</v-icon>
                  </template>
                  <v-list-item-title>Imprimir</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>

          <!-- Metadados -->
          <v-card variant="outlined" class="mb-6">
            <v-card-text>
              <v-row>
                <v-col cols="12" sm="6" md="3">
                  <div class="d-flex align-center">
                    <v-icon color="grey" size="small" class="mr-2">mdi-account</v-icon>
                    <div>
                      <div class="text-caption text-grey">Autor</div>
                      <div class="text-body-2">{{ artigo.autor?.nome }}</div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                  <div class="d-flex align-center">
                    <v-icon color="grey" size="small" class="mr-2">mdi-calendar</v-icon>
                    <div>
                      <div class="text-caption text-grey">Criado em</div>
                      <div class="text-body-2">{{ formatDate(artigo.criado_em) }}</div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                  <div class="d-flex align-center">
                    <v-icon color="grey" size="small" class="mr-2">mdi-eye</v-icon>
                    <div>
                      <div class="text-caption text-grey">Visualizações</div>
                      <div class="text-body-2">{{ artigo.visualizacoes || 0 }}</div>
                    </div>
                  </div>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                  <div class="d-flex align-center">
                    <v-icon color="grey" size="small" class="mr-2">mdi-clock-outline</v-icon>
                    <div>
                      <div class="text-caption text-grey">Tempo de leitura</div>
                      <div class="text-body-2">{{ artigo.tempo_leitura || 5 }} min</div>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <!-- Resumo (se existir) -->
          <v-card v-if="artigo.resumo" variant="outlined" class="mb-6">
            <v-card-text>
              <div class="d-flex align-center mb-3">
                <v-icon color="info" class="mr-2">mdi-information</v-icon>
                <span class="text-h6">Resumo</span>
              </div>
              <p class="text-body-1">{{ artigo.resumo }}</p>
            </v-card-text>
          </v-card>

          <!-- Conteúdo Principal -->
          <v-card class="mb-6">
            <v-card-text class="pa-6">
              <div class="article-content" v-html="formatContent(artigo.conteudo)"></div>
            </v-card-text>
          </v-card>

          <!-- Palavras-chave -->
          <div v-if="artigo.palavras_chave" class="mb-6">
            <h3 class="text-h6 mb-3">Palavras-chave</h3>
            <div class="d-flex flex-wrap gap-2">
              <v-chip
                v-for="palavra in palavrasChave"
                :key="palavra"
                size="small"
                variant="outlined"
                color="primary"
              >
                {{ palavra }}
              </v-chip>
            </div>
          </div>

          <!-- Avaliação do Artigo -->
          <v-card class="mb-6">
            <v-card-title>Este artigo foi útil?</v-card-title>
            <v-card-text>
              <div class="d-flex align-center mb-4">
                <v-rating
                  v-model="avaliacaoUsuario"
                  color="amber"
                  half-increments
                  @update:model-value="avaliarArtigo"
                ></v-rating>
                <span class="ml-3 text-body-2">
                  {{ avaliacaoUsuario ? `${avaliacaoUsuario}/5` : 'Avalie este artigo' }}
                </span>
              </div>

              <div class="text-body-2 text-grey">
                Avaliação média: {{ artigo.avaliacao_media || 0 }}/5
                ({{ artigo.total_avaliacoes || 0 }} avaliações)
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Sidebar -->
        <v-col cols="12" lg="4">
          <!-- Índice do Artigo -->
          <v-card class="mb-4 sticky-sidebar">
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-format-list-bulleted</v-icon>
              Índice
            </v-card-title>
            <v-card-text>
              <div v-if="indiceArtigo.length > 0">
                <div
                  v-for="(item, index) in indiceArtigo"
                  :key="index"
                  class="mb-2"
                >
                  <a
                    :href="`#${item.id}`"
                    class="text-decoration-none text-primary"
                    :class="`ml-${(item.level - 1) * 3}`"
                    @click.prevent="scrollTo(item.id)"
                  >
                    {{ item.text }}
                  </a>
                </div>
              </div>
              <div v-else class="text-body-2 text-grey">
                Nenhum cabeçalho encontrado no artigo.
              </div>
            </v-card-text>
          </v-card>

          <!-- Artigos Relacionados -->
          <v-card class="mb-4">
            <v-card-title class="d-flex align-center">
              <v-icon left>mdi-file-document-multiple</v-icon>
              Artigos Relacionados
            </v-card-title>
            <v-card-text>
              <div v-if="artigosRelacionados.length > 0">
                <div
                  v-for="relacionado in artigosRelacionados"
                  :key="relacionado.id"
                  class="mb-3"
                >
                  <a
                    @click="$inertia.visit(`/base-conhecimento/${relacionado.id}`)"
                    class="text-decoration-none cursor-pointer"
                  >
                    <div class="text-body-2 font-weight-medium text-primary">
                      {{ relacionado.titulo }}
                    </div>
                    <div class="text-caption text-grey">
                      {{ relacionado.categoria }} • {{ formatDate(relacionado.criado_em) }}
                    </div>
                  </a>
                </div>
              </div>
              <div v-else class="text-body-2 text-grey">
                Nenhum artigo relacionado encontrado.
              </div>
            </v-card-text>
          </v-card>

          <!-- Ações Rápidas -->
          <v-card>
            <v-card-title>Ações</v-card-title>
            <v-card-text>
              <v-btn
                color="primary"
                variant="outlined"
                @click="$inertia.visit(`/base-conhecimento/${artigo.id}/edit`)"
                block
                class="mb-2"
              >
                <v-icon left>mdi-pencil</v-icon>
                Editar Artigo
              </v-btn>

              <v-btn
                color="info"
                variant="outlined"
                @click="compartilhar"
                block
                class="mb-2"
              >
                <v-icon left>mdi-share-variant</v-icon>
                Compartilhar
              </v-btn>

              <v-btn
                color="secondary"
                variant="outlined"
                @click="imprimir"
                block
              >
                <v-icon left>mdi-printer</v-icon>
                Imprimir
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  artigo: Object,
  artigosRelacionados: {
    type: Array,
    default: () => []
  }
})

const avaliacaoUsuario = ref(0)
const indiceArtigo = ref([])

const palavrasChave = computed(() => {
  if (!props.artigo.palavras_chave) return []
  return props.artigo.palavras_chave.split(',').map(p => p.trim())
})

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
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatContent = (content) => {
  if (!content) return ''

  // Converter quebras de linha em parágrafos HTML
  let formatted = content.replace(/\n\n/g, '</p><p>')
  formatted = '<p>' + formatted + '</p>'

  // Converter títulos marcados com # em headers HTML
  formatted = formatted.replace(/^### (.*$)/gim, '<h3 id="$1">$1</h3>')
  formatted = formatted.replace(/^## (.*$)/gim, '<h2 id="$1">$1</h2>')
  formatted = formatted.replace(/^# (.*$)/gim, '<h1 id="$1">$1</h1>')

  return formatted
}

const gerarIndice = () => {
  const content = props.artigo.conteudo
  if (!content) return

  const headers = content.match(/^(#{1,3})\s+(.+)$/gm)
  if (!headers) return

  indiceArtigo.value = headers.map(header => {
    const level = (header.match(/#/g) || []).length
    const text = header.replace(/^#+\s+/, '')
    const id = text.toLowerCase().replace(/\s+/g, '-')

    return { level, text, id }
  })
}

const scrollTo = (id) => {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const avaliarArtigo = (nota) => {
  // Aqui você enviaria a avaliação para o backend
  console.log('Avaliação:', nota)
}

const compartilhar = () => {
  if (navigator.share) {
    navigator.share({
      title: props.artigo.titulo,
      text: props.artigo.resumo,
      url: window.location.href
    })
  } else {
    navigator.clipboard.writeText(window.location.href)
  }
}

const imprimir = () => {
  window.print()
}

onMounted(() => {
  gerarIndice()

  // Incrementar visualizações (poderia ser feito no backend)
  router.post(`/base-conhecimento/${props.artigo.id}/view`, {}, {
    preserveScroll: true,
    preserveState: true
  })
})
</script>

<style scoped>
.sticky-sidebar {
  position: sticky;
  top: 20px;
}

.article-content {
  line-height: 1.8;
}

.article-content h1,
.article-content h2,
.article-content h3 {
  margin: 1.5em 0 0.5em 0;
  font-weight: 600;
}

.article-content p {
  margin-bottom: 1em;
}

.article-content ul,
.article-content ol {
  margin: 1em 0;
  padding-left: 2em;
}

.article-content code {
  background-color: #f5f5f5;
  padding: 0.2em 0.4em;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
}

@media print {
  .sticky-sidebar {
    display: none !important;
  }

  .v-btn {
    display: none !important;
  }
}
</style>

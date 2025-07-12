<template>
  <AppLayout title="Editar Artigo">
    <v-container>
      <!-- Header -->
      <div class="d-flex align-center justify-space-between mb-6">
        <div>
          <h1 class="text-h4 font-weight-bold mb-2">Editar Artigo</h1>
          <p class="text-body-1 text-medium-emphasis">
            {{ artigo.titulo }}
          </p>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            variant="outlined"
            prepend-icon="mdi-eye"
            @click="$inertia.visit(route('base-conhecimento.show', artigo.id))"
          >
            Visualizar
          </v-btn>
          <v-btn
            variant="outlined"
            prepend-icon="mdi-arrow-left"
            @click="$inertia.visit(route('base-conhecimento.index'))"
          >
            Voltar
          </v-btn>
        </div>
      </div>

      <!-- Formulário -->
      <v-card>
        <v-card-text>
          <v-form @submit.prevent="submit">
            <v-row>
              <!-- Título -->
              <v-col cols="12">
                <v-text-field
                  v-model="form.titulo"
                  label="Título do Artigo"
                  variant="outlined"
                  :error-messages="form.errors.titulo"
                  required
                  autofocus
                />
              </v-col>

              <!-- Resumo -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.resumo"
                  label="Resumo"
                  variant="outlined"
                  :error-messages="form.errors.resumo"
                  rows="3"
                  hint="Breve descrição do artigo (máximo 255 caracteres)"
                  counter="255"
                />
              </v-col>

              <!-- Categoria e Status -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.categoria"
                  :items="categorias"
                  label="Categoria"
                  variant="outlined"
                  :error-messages="form.errors.categoria"
                  required
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="statusOptions"
                  item-title="title"
                  item-value="value"
                  label="Status"
                  variant="outlined"
                  :error-messages="form.errors.status"
                  required
                />
              </v-col>

              <!-- Palavras-chave -->
              <v-col cols="12">
                <v-textarea
                  v-model="form.palavras_chave"
                  label="Palavras-chave"
                  variant="outlined"
                  :error-messages="form.errors.palavras_chave"
                  rows="2"
                  hint="Separe as palavras-chave por vírgula"
                  placeholder="ex: problema, solução, tutorial, rede"
                />
              </v-col>

              <!-- Conteúdo -->
              <v-col cols="12">
                <v-label class="mb-2">Conteúdo do Artigo</v-label>
                <div class="mb-2">
                  <!-- Toolbar do editor -->
                  <v-toolbar density="compact" color="grey-lighten-4">
                    <v-btn
                      icon="mdi-format-bold"
                      size="small"
                      variant="text"
                      @click="formatText('**')"
                      title="Negrito"
                    />
                    <v-btn
                      icon="mdi-format-italic"
                      size="small"
                      variant="text"
                      @click="formatText('*')"
                      title="Itálico"
                    />
                    <v-divider vertical />
                    <v-btn
                      icon="mdi-format-header-1"
                      size="small"
                      variant="text"
                      @click="formatHeader(1)"
                      title="Título 1"
                    />
                    <v-btn
                      icon="mdi-format-header-2"
                      size="small"
                      variant="text"
                      @click="formatHeader(2)"
                      title="Título 2"
                    />
                    <v-btn
                      icon="mdi-format-header-3"
                      size="small"
                      variant="text"
                      @click="formatHeader(3)"
                      title="Título 3"
                    />
                    <v-divider vertical />
                    <v-btn
                      icon="mdi-format-list-bulleted"
                      size="small"
                      variant="text"
                      @click="formatList('-')"
                      title="Lista com marcadores"
                    />
                    <v-btn
                      icon="mdi-format-list-numbered"
                      size="small"
                      variant="text"
                      @click="formatList('1.')"
                      title="Lista numerada"
                    />
                    <v-divider vertical />
                    <v-btn
                      icon="mdi-code-tags"
                      size="small"
                      variant="text"
                      @click="formatCode()"
                      title="Código"
                    />
                    <v-btn
                      icon="mdi-link"
                      size="small"
                      variant="text"
                      @click="formatLink()"
                      title="Link"
                    />
                    <v-spacer />
                    <v-btn
                      icon="mdi-eye"
                      size="small"
                      variant="text"
                      @click="showPreview = !showPreview"
                      :color="showPreview ? 'primary' : ''"
                      title="Visualizar"
                    />
                  </v-toolbar>
                </div>

                <v-row no-gutters>
                  <v-col :cols="showPreview ? 6 : 12">
                    <v-textarea
                      ref="contentEditor"
                      v-model="form.conteudo"
                      variant="outlined"
                      :error-messages="form.errors.conteudo"
                      rows="20"
                      required
                      hint="Use Markdown para formatação"
                      no-resize
                    />
                  </v-col>
                  <v-col v-if="showPreview" cols="6">
                    <v-card
                      class="ml-2"
                      variant="outlined"
                      style="height: 100%;"
                    >
                      <v-card-title class="text-subtitle-2">
                        Visualização
                      </v-card-title>
                      <v-card-text
                        class="pa-4"
                        style="height: calc(100% - 48px); overflow-y: auto;"
                        v-html="markdownPreview"
                      />
                    </v-card>
                  </v-col>
                </v-row>
              </v-col>

              <!-- Tempo de Leitura Estimado -->
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="form.tempo_leitura"
                  label="Tempo de Leitura (minutos)"
                  variant="outlined"
                  type="number"
                  :error-messages="form.errors.tempo_leitura"
                  hint="Tempo estimado para ler o artigo"
                  min="1"
                />
              </v-col>

              <!-- Estatísticas (somente leitura) -->
              <v-col cols="12" md="6">
                <v-card variant="outlined" class="pa-3">
                  <v-card-subtitle class="pa-0 mb-2">Estatísticas</v-card-subtitle>
                  <div class="text-body-2">
                    <div class="d-flex justify-space-between mb-1">
                      <span>Visualizações:</span>
                      <span class="font-weight-medium">{{ artigo.visualizacoes }}</span>
                    </div>
                    <div class="d-flex justify-space-between mb-1">
                      <span>Criado em:</span>
                      <span class="font-weight-medium">{{ formatDate(artigo.created_at) }}</span>
                    </div>
                    <div class="d-flex justify-space-between">
                      <span>Atualizado em:</span>
                      <span class="font-weight-medium">{{ formatDate(artigo.updated_at) }}</span>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>

            <!-- Ações -->
            <v-card-actions class="px-0 pt-6">
              <v-btn
                color="primary"
                variant="elevated"
                prepend-icon="mdi-content-save"
                type="submit"
                :loading="form.processing"
                size="large"
              >
                Salvar Alterações
              </v-btn>

              <v-btn
                variant="outlined"
                prepend-icon="mdi-eye"
                @click="saveAsDraft"
                :loading="form.processing"
                size="large"
                class="ml-3"
                v-if="form.status !== 'rascunho'"
              >
                Salvar como Rascunho
              </v-btn>

              <v-spacer />

              <!-- Ações de Perigo -->
              <v-menu offset-y>
                <template v-slot:activator="{ props }">
                  <v-btn
                    variant="outlined"
                    color="error"
                    prepend-icon="mdi-dots-vertical"
                    v-bind="props"
                  >
                    Ações
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item @click="deleteArticle">
                    <v-list-item-title>
                      <v-icon left>mdi-delete</v-icon>
                      Excluir Artigo
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>

              <v-btn
                variant="text"
                @click="$inertia.visit(route('base-conhecimento.index'))"
              >
                Cancelar
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>

      <!-- Dialog de confirmação para exclusão -->
      <v-dialog v-model="deleteDialog" max-width="500">
        <v-card>
          <v-card-title class="text-h5">
            Confirmar Exclusão
          </v-card-title>
          <v-card-text>
            Tem certeza que deseja excluir este artigo? Esta ação não pode ser desfeita.
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="deleteDialog = false">
              Cancelar
            </v-btn>
            <v-btn
              color="error"
              variant="elevated"
              @click="confirmDelete"
              :loading="deleting"
            >
              Excluir
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { marked } from 'marked'

// Props
const props = defineProps({
  artigo: Object,
  categorias: Array,
  statusOptions: Array,
})

// Refs
const showPreview = ref(false)
const contentEditor = ref(null)
const deleteDialog = ref(false)
const deleting = ref(false)

// Form
const form = useForm({
  titulo: props.artigo.titulo,
  resumo: props.artigo.resumo || '',
  categoria: props.artigo.categoria,
  status: props.artigo.status,
  palavras_chave: props.artigo.palavras_chave || '',
  conteudo: props.artigo.conteudo,
  tempo_leitura: props.artigo.tempo_leitura,
})

// Computed
const markdownPreview = computed(() => {
  if (!form.conteudo) return '<p class="text-medium-emphasis">Digite o conteúdo para ver a visualização...</p>'

  try {
    return marked(form.conteudo, {
      breaks: true,
      gfm: true,
    })
  } catch (error) {
    return '<p class="text-error">Erro ao processar Markdown</p>'
  }
})

// Methods
const submit = () => {
  form.put(route('base-conhecimento.update', props.artigo.id), {
    onSuccess: () => {
      // Redirecionamento será feito pelo controller
    }
  })
}

const saveAsDraft = () => {
  form.status = 'rascunho'
  submit()
}

const deleteArticle = () => {
  deleteDialog.value = true
}

const confirmDelete = () => {
  deleting.value = true
  router.delete(route('base-conhecimento.destroy', props.artigo.id), {
    onFinish: () => {
      deleting.value = false
      deleteDialog.value = false
    }
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatText = (marker) => {
  const textarea = contentEditor.value.$el.querySelector('textarea')
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.conteudo.substring(start, end)

  if (selectedText) {
    const newText = `${marker}${selectedText}${marker}`
    form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(end)

    nextTick(() => {
      textarea.focus()
      textarea.setSelectionRange(start + marker.length, end + marker.length)
    })
  } else {
    const placeholder = marker === '**' ? 'texto em negrito' : 'texto em itálico'
    const newText = `${marker}${placeholder}${marker}`
    form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(end)

    nextTick(() => {
      textarea.focus()
      textarea.setSelectionRange(start + marker.length, start + marker.length + placeholder.length)
    })
  }
}

const formatHeader = (level) => {
  const textarea = contentEditor.value.$el.querySelector('textarea')
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.conteudo.substring(start, end) || `Título ${level}`

  const marker = '#'.repeat(level)
  const newText = `${marker} ${selectedText}`

  // Inserir no início da linha
  const beforeText = form.conteudo.substring(0, start)
  const afterText = form.conteudo.substring(end)
  const lastNewline = beforeText.lastIndexOf('\n')
  const lineStart = lastNewline + 1

  form.conteudo = form.conteudo.substring(0, lineStart) + newText + afterText.substring(end - lineStart)

  nextTick(() => {
    textarea.focus()
  })
}

const formatList = (marker) => {
  const textarea = contentEditor.value.$el.querySelector('textarea')
  const start = textarea.selectionStart
  const selectedText = form.conteudo.substring(start, start) || 'Item da lista'

  const newText = `${marker} ${selectedText}\n`
  form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(start)

  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(start + marker.length + 1, start + marker.length + 1 + selectedText.length)
  })
}

const formatCode = () => {
  const textarea = contentEditor.value.$el.querySelector('textarea')
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.conteudo.substring(start, end)

  if (selectedText.includes('\n')) {
    // Bloco de código
    const newText = `\`\`\`\n${selectedText || 'código aqui'}\n\`\`\``
    form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(end)
  } else {
    // Código inline
    const placeholder = selectedText || 'código'
    const newText = `\`${placeholder}\``
    form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(end)
  }

  nextTick(() => {
    textarea.focus()
  })
}

const formatLink = () => {
  const textarea = contentEditor.value.$el.querySelector('textarea')
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.conteudo.substring(start, end) || 'texto do link'

  const newText = `[${selectedText}](URL)`
  form.conteudo = form.conteudo.substring(0, start) + newText + form.conteudo.substring(end)

  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(start + selectedText.length + 3, start + selectedText.length + 6)
  })
}
</script>

<style scoped>
:deep(.v-card-text) {
  padding: 24px;
}

:deep(.v-toolbar) {
  border: 1px solid rgba(0,0,0,0.12);
  border-bottom: none;
  border-radius: 4px 4px 0 0;
}

:deep(.v-textarea .v-field) {
  border-radius: 0 0 4px 4px;
}

:deep(.markdown-preview) {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
  line-height: 1.6;
}

:deep(.markdown-preview h1) { font-size: 2em; margin: 0.67em 0; }
:deep(.markdown-preview h2) { font-size: 1.5em; margin: 0.75em 0; }
:deep(.markdown-preview h3) { font-size: 1.17em; margin: 0.83em 0; }
:deep(.markdown-preview p) { margin: 1em 0; }
:deep(.markdown-preview ul, .markdown-preview ol) { margin: 1em 0; padding-left: 2em; }
:deep(.markdown-preview li) { margin: 0.5em 0; }
:deep(.markdown-preview code) {
  background: #f5f5f5;
  padding: 2px 4px;
  border-radius: 3px;
  font-family: 'Courier New', monospace;
}
:deep(.markdown-preview pre) {
  background: #f5f5f5;
  padding: 1em;
  border-radius: 4px;
  overflow-x: auto;
  margin: 1em 0;
}
:deep(.markdown-preview blockquote) {
  border-left: 4px solid #ddd;
  margin: 1em 0;
  padding-left: 1em;
  color: #666;
}
</style>

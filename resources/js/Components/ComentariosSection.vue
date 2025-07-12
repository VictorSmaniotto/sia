<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-comment-multiple</v-icon>
      Comentários ({{ comentarios.length }})
    </v-card-title>

    <v-card-text>
      <!-- Lista de Comentários -->
      <div v-if="comentarios.length > 0" class="mb-4">
        <v-timeline density="compact">
          <v-timeline-item
            v-for="comentario in comentarios"
            :key="comentario.id"
            dot-color="primary"
            size="small"
          >
            <template #prepend>
              <v-avatar size="32" color="primary">
                {{ getInitials(comentario.autor?.nome || 'AN') }}
              </v-avatar>
            </template>

            <v-card variant="outlined" class="mb-2">
              <v-card-text>
                <div class="d-flex justify-space-between align-center mb-2">
                  <strong>{{ comentario.autor?.nome || 'Usuário Anônimo' }}</strong>
                  <span class="text-caption text-medium-emphasis">
                    {{ formatDateTime(comentario.criado_em) }}
                  </span>
                </div>
                <p class="mb-0">{{ comentario.conteudo }}</p>
              </v-card-text>
            </v-card>
          </v-timeline-item>
        </v-timeline>
      </div>

      <div v-else class="text-center text-medium-emphasis mb-4">
        <v-icon size="48" color="grey-lighten-2">mdi-comment-outline</v-icon>
        <p>Nenhum comentário ainda. Seja o primeiro a comentar!</p>
      </div>

      <!-- Formulário de Novo Comentário -->
      <v-divider class="mb-4" />

      <v-form @submit.prevent="adicionarComentario">
        <v-textarea
          v-model="novoComentario"
          label="Adicionar comentário"
          placeholder="Digite seu comentário aqui..."
          rows="3"
          :error-messages="errors.conteudo"
          prepend-inner-icon="mdi-comment-plus"
          required
        />

        <div class="d-flex justify-end mt-3">
          <v-btn
            type="submit"
            color="primary"
            :loading="loading"
            :disabled="!novoComentario.trim()"
            prepend-icon="mdi-send"
          >
            Enviar Comentário
          </v-btn>
        </div>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: 'ComentariosSection',
  props: {
    comentarios: {
      type: Array,
      default: () => []
    },
    tipoEntidade: {
      type: String,
      required: true,
      validator: value => ['incidente', 'problema'].includes(value)
    },
    entidadeId: {
      type: [String, Number],
      required: true
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['comentario-adicionado'],
  data() {
    return {
      novoComentario: '',
      loading: false
    }
  },
  methods: {
    formatDateTime(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleString('pt-BR')
    },
    getInitials(nome) {
      return nome
        .split(' ')
        .map(word => word.charAt(0))
        .slice(0, 2)
        .join('')
        .toUpperCase()
    },
    adicionarComentario() {
      if (!this.novoComentario.trim()) return

      this.loading = true

      const dados = {
        conteudo: this.novoComentario.trim()
      }

      // Adicionar campo específico para o tipo de entidade
      if (this.tipoEntidade === 'incidente') {
        dados.incidente_id = this.entidadeId
      } else if (this.tipoEntidade === 'problema') {
        dados.problema_id = this.entidadeId
      }

      this.$inertia.post('/comentarios', dados, {
        preserveScroll: true,
        onSuccess: () => {
          this.novoComentario = ''
          this.$emit('comentario-adicionado')
        },
        onError: (errors) => {
          console.log('Erro ao adicionar comentário:', errors)
        },
        onFinish: () => {
          this.loading = false
        }
      })
    }
  }
}
</script>

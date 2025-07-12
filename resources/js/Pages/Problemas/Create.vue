<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit(route('problemas.index'))"
              class="mr-3"
            />
            <h2>Novo Problema</h2>
          </v-card-title>

          <v-form @submit.prevent="salvarProblema">
            <v-card-text>
              <v-row>
                <!-- Título -->
                <v-col cols="12">
                  <v-text-field
                    v-model="form.titulo"
                    label="Título *"
                    required
                    :error-messages="errors.titulo"
                    prepend-inner-icon="mdi-format-title"
                  />
                </v-col>

                <!-- Descrição -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.descricao"
                    label="Descrição *"
                    required
                    :error-messages="errors.descricao"
                    prepend-inner-icon="mdi-text"
                    rows="4"
                    hint="Descreva detalhadamente o problema identificado"
                  />
                </v-col>

                <!-- Status e Prioridade -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.status"
                    :items="statusOptions"
                    label="Status *"
                    required
                    :error-messages="errors.status"
                    prepend-inner-icon="mdi-flag"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.prioridade"
                    :items="prioridadeOptions"
                    label="Prioridade *"
                    required
                    :error-messages="errors.prioridade"
                    prepend-inner-icon="mdi-priority-high"
                  />
                </v-col>

                <!-- Responsável -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.responsavel_id"
                    :items="usuarios"
                    item-title="nome"
                    item-value="id"
                    label="Responsável"
                    :error-messages="errors.responsavel_id"
                    prepend-inner-icon="mdi-account"
                    clearable
                  />
                </v-col>

                <!-- Incidentes Relacionados -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.incidentes_relacionados"
                    :items="incidentes"
                    item-title="titulo"
                    item-value="id"
                    label="Incidentes Relacionados"
                    multiple
                    chips
                    prepend-inner-icon="mdi-link"
                    hint="Selecione incidentes que podem estar relacionados a este problema"
                  />
                </v-col>

                <!-- Causa Raiz -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.causa_raiz"
                    label="Causa Raiz"
                    :error-messages="errors.causa_raiz"
                    prepend-inner-icon="mdi-source-branch"
                    rows="3"
                    hint="Descreva a causa raiz identificada (se conhecida)"
                  />
                </v-col>

                <!-- Solução de Contorno -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.solucao_contorno"
                    label="Solução de Contorno (Workaround)"
                    :error-messages="errors.solucao_contorno"
                    prepend-inner-icon="mdi-wrench"
                    rows="3"
                    hint="Descreva uma solução temporária que pode minimizar o impacto"
                  />
                </v-col>

                <!-- Solução Definitiva -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.solucao_definitiva"
                    label="Solução Definitiva"
                    :error-messages="errors.solucao_definitiva"
                    prepend-inner-icon="mdi-check-circle"
                    rows="3"
                    hint="Descreva a solução definitiva (se já implementada)"
                  />
                </v-col>
              </v-row>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                variant="outlined"
                @click="$inertia.visit(route('problemas.index'))"
              >
                Cancelar
              </v-btn>
              <v-btn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="mdi-content-save"
              >
                Salvar Problema
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'ProblemasCreate',
  props: {
    usuarios: Array,
    incidentes: Array,
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      loading: false,
      form: {
        titulo: '',
        descricao: '',
        status: 'Novo',
        prioridade: 'Média',
        responsavel_id: null,
        causa_raiz: '',
        solucao_contorno: '',
        solucao_definitiva: '',
        incidentes_relacionados: []
      },
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
  methods: {
    route(name, params) {
      return this.$root.route ? this.$root.route(name, params) : '#'
    },
    salvarProblema() {
      this.loading = true

      this.$inertia.post(this.route('problemas.store'), this.form, {
        onFinish: () => {
          this.loading = false
        },
        onSuccess: () => {
          // Redirecionamento será feito pelo controller
        },
        onError: (errors) => {
          console.log('Erros de validação:', errors)
        }
      })
    }
  }
}
</script>

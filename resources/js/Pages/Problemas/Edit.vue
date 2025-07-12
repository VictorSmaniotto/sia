<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="$inertia.visit(route('problemas.show', problema.id))"
              class="mr-3"
            />
            <h2>Editar Problema #{{ problema.id }}</h2>
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

                <!-- Workflow Section -->
                <v-col cols="12">
                  <v-divider class="my-4" />
                  <h3 class="text-h6 mb-4">Análise e Resolução</h3>
                </v-col>

                <!-- Causa Raiz -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.causa_raiz"
                    label="Causa Raiz"
                    :error-messages="errors.causa_raiz"
                    prepend-inner-icon="mdi-source-branch"
                    rows="3"
                    hint="Descreva a causa raiz identificada (obrigatório para status 'Erro Conhecido' ou 'Resolvido')"
                    :rules="causaRaizRules"
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
                    hint="Descreva a solução definitiva (obrigatório para status 'Resolvido')"
                    :rules="solucaoDefinitivaRules"
                  />
                </v-col>

                <!-- Observações sobre Workflow -->
                <v-col cols="12" v-if="mostrarAlertaWorkflow">
                  <v-alert
                    :type="alertaWorkflow.tipo"
                    variant="tonal"
                    :icon="alertaWorkflow.icon"
                  >
                    {{ alertaWorkflow.mensagem }}
                  </v-alert>
                </v-col>
              </v-row>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                variant="outlined"
                @click="$inertia.visit(route('problemas.show', problema.id))"
              >
                Cancelar
              </v-btn>
              <v-btn
                type="submit"
                color="primary"
                :loading="loading"
                prepend-icon="mdi-content-save"
              >
                Salvar Alterações
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
  name: 'ProblemasEdit',
  props: {
    problema: Object,
    usuarios: Array,
    incidentes: Array,
    incidentesRelacionados: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      loading: false,
      form: {
        titulo: this.problema.titulo || '',
        descricao: this.problema.descricao || '',
        status: this.problema.status || 'Novo',
        prioridade: this.problema.prioridade || 'Média',
        responsavel_id: this.problema.responsavel_id || null,
        causa_raiz: this.problema.causa_raiz || '',
        solucao_contorno: this.problema.solucao_contorno || '',
        solucao_definitiva: this.problema.solucao_definitiva || '',
        incidentes_relacionados: this.incidentesRelacionados.map(i => i.id) || []
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
  computed: {
    causaRaizRules() {
      if (['Erro Conhecido', 'Resolvido'].includes(this.form.status)) {
        return [v => !!v || 'Causa raiz é obrigatória para este status']
      }
      return []
    },
    solucaoDefinitivaRules() {
      if (this.form.status === 'Resolvido') {
        return [v => !!v || 'Solução definitiva é obrigatória para status Resolvido']
      }
      return []
    },
    mostrarAlertaWorkflow() {
      return !!this.alertaWorkflow
    },
    alertaWorkflow() {
      switch (this.form.status) {
        case 'Erro Conhecido':
          if (!this.form.causa_raiz) {
            return {
              tipo: 'warning',
              icon: 'mdi-alert',
              mensagem: 'Para marcar como "Erro Conhecido", é necessário informar a causa raiz identificada.'
            }
          }
          break
        case 'Resolvido':
          if (!this.form.causa_raiz || !this.form.solucao_definitiva) {
            return {
              tipo: 'error',
              icon: 'mdi-alert-circle',
              mensagem: 'Para marcar como "Resolvido", é obrigatório informar tanto a causa raiz quanto a solução definitiva.'
            }
          }
          return {
            tipo: 'success',
            icon: 'mdi-check-circle',
            mensagem: 'Problema pronto para ser resolvido! Verifique se todos os incidentes relacionados foram fechados.'
          }
        case 'Fechado':
          return {
            tipo: 'info',
            icon: 'mdi-information',
            mensagem: 'Ao fechar o problema, ele será marcado como concluído definitivamente.'
          }
        default:
          return null
      }
    }
  },
  methods: {
    route(name, params) {
      return this.$root.route ? this.$root.route(name, params) : '#'
    },
    salvarProblema() {
      // Validação de workflow antes de submeter
      if (this.form.status === 'Erro Conhecido' && !this.form.causa_raiz) {
        return
      }
      if (this.form.status === 'Resolvido' && (!this.form.causa_raiz || !this.form.solucao_definitiva)) {
        return
      }

      this.loading = true

      this.$inertia.put(this.route('problemas.update', this.problema.id), this.form, {
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

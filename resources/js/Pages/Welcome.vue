<template>
  <v-app>
    <v-main>
      <v-container>
        <v-row justify="center">
          <v-col cols="12" md="8">
            <v-card class="mx-auto pa-8" elevation="4">
              <v-card-title class="text-h4 text-center mb-4">
                <v-icon left color="primary" size="large">mdi-tools</v-icon>
                SIA - Sistema de Gestão ITSM
              </v-card-title>

              <v-card-text class="text-center">
                <v-chip color="success" variant="tonal" class="mb-4">
                  Sistema em Desenvolvimento
                </v-chip>

                <p class="text-h6 mb-4">
                  Bem-vindo ao Sistema de Gestão de Incidentes, Problemas e Base de Conhecimento
                </p>

                <v-divider class="my-4"></v-divider>

                <v-row>
                  <v-col cols="12" md="4">
                    <v-card variant="outlined" class="pa-4">
                      <v-icon color="primary" size="48" class="mb-2">mdi-alert-circle</v-icon>
                      <h3>Gestão de Incidentes</h3>
                      <p class="text-body-2">Registre e acompanhe incidentes de TI</p>
                    </v-card>
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-card variant="outlined" class="pa-4">
                      <v-icon color="warning" size="48" class="mb-2">mdi-magnify</v-icon>
                      <h3>Gestão de Problemas</h3>
                      <p class="text-body-2">Identifique e resolva causas raiz</p>
                    </v-card>
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-card variant="outlined" class="pa-4">
                      <v-icon color="success" size="48" class="mb-2">mdi-book-open</v-icon>
                      <h3>Base de Conhecimento</h3>
                      <p class="text-body-2">Compartilhe soluções e documentação</p>
                    </v-card>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { Head } from '@inertiajs/inertia-vue3'
</script>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {reactive, watch} from 'vue';
import {LineChart} from 'vue-chart-3';
import {Chart, registerables} from "chart.js";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import {Icon} from "@iconify/vue";

Chart.register(...registerables);

const options = reactive({
    scales: {
        y: {
            title: {
                display: true,
                text: 'Monto'
            }
        },
        x: {
            title: {
                display: true,
                text: 'Horas'
            },
        }
    }
})

const props = defineProps({
    ventas: Object,
    meseros: Array,
    querys: Object
})

const data = reactive({
    fecha: props.querys.hasOwnProperty('fecha') ? new Date(props.querys.fecha) : '',
    mesero: props.querys.hasOwnProperty('mesero') ? props.querys.mesero : '',
    mesa: props.querys.hasOwnProperty('mesa') ? props.querys.mesa : '',
    monto: props.querys.hasOwnProperty('monto') ? props.querys.monto : '',
})

const loading = reactive({
    active: false
})

const params = reactive({})

function addParam(param) {
    if (!params[param]) {
        params[param] = data[param]
    }
}

function verifyParams() {
    ['fecha', 'mesero', 'mesa', 'monto'].forEach(item => {
        if (data[item]) {
            addParam(item)
        }
    })
}

watch(
    () => data.fecha,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            addParam('fecha')
        }
    }
)
watch(
    () => data.mesero,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            addParam('mesero')
        }
    }
)
watch(
    () => data.mesa,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            addParam('mesa')
        }
    }
)
watch(
    () => data.monto,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            addParam('monto')
        }
    }
)

function send() {
    loading.active = true
    verifyParams()
    router.get(route('panel.dashboard', params))
}
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <v-container fluid>
            <v-card title="Bienvenido" :subtitle="$page.props.auth.user.name">
                <v-card-text>
                    <span class="text-caption">Aplica los filtros necesarios y da click sobre el botón buscar</span>
                    <v-row>
                        <v-col>
                            <p class="text-subtitle-2">Fecha</p>
                            <VueDatePicker
                                v-model="data.fecha"
                                :enable-time-picker="false"
                                format="d-MM-y"
                            ></VueDatePicker>
                        </v-col>
                        <v-col>
                            <v-select
                                class="mt-5"
                                label="Mesero"
                                v-model="data.mesero"
                                :items="meseros"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                density="compact"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col>
                            <v-select
                                class="mt-5"
                                label="Mesa"
                                v-model="data.mesa"
                                variant="outlined"
                                density="compact"
                                :items="[]"
                            ></v-select>
                        </v-col>
                        <v-col>
                            <v-text-field
                                class="mt-5"
                                label="Monto"
                                v-model="data.monto"
                                variant="outlined"
                                density="compact"
                                type="number"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="2">
                            <v-btn
                                class="mt-5"
                                color="primary"
                                @click="send"
                                block
                            >
                                <Icon icon="mdi:search" width="24"/>
                                buscar
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-progress-linear v-if="loading.active" :indeterminate="loading.active"
                                       color="primary"></v-progress-linear>
                    <LineChart :chartData="ventas" :options="options"/>
                </v-card-text>
                <v-card-actions>
                    <v-row justify="end">
                        <v-col cols="3">
                            <v-btn
                                :href="route('panel.pdf',params)"
                                target="_blank"
                                color="primary"
                                block
                                variant="flat"
                            >
                                <Icon icon="mdi:file" width="24"/>
                                PDF
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-actions>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

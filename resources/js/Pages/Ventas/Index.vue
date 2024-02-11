<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Icon} from "@iconify/vue";
import {computed, reactive, watch} from "vue";
import {da} from "vuetify/locale";

const props = defineProps({
    sucursales: Object,
    meseros: Array,
    perPage: Number
})

const data = reactive({
    step: 1,
    form: useForm({
        id: '',
        sucursal: '',
        mesero: props.meseros.length === 1 ? props.meseros[0].id : '',
        mesa: '',
        platillo: '',
        platillo_count: 1,
        platillos: []
    })
});

const snakbar = reactive({
    text: '',
    open: false,
    icon: '',
    color: '',
})

function saveVenta() {
    if (data.form.id) {
        data.form.put(route('panel.ventas.update', data.form.id), {
            onSuccess: page => {
                if (!page.props.status.error) {
                    data.form.reset()
                    closeDialog()
                    showSnak(true, page.props.status.success)
                } else {
                    showSnak(false, page.props.status.error)
                }
            }
        })
    } else {
        data.form.post(route('panel.ventas.store'), {
            onSuccess: page => {
                if (!page.props.status.error) {
                    data.form.reset()
                    closeDialog()
                    showSnak(true, page.props.status.success)
                } else {
                    showSnak(false, page.props.status.error)
                }
            }
        })
    }
}

function addPlatillo(item) {
    let platillo = {
        name: item.name,
        price: item.price,
        total: data.form.platillo_count,
        amout: item.price * data.form.platillo_count
    }
    data.form.platillos.push(platillo)
    //TODO: Reset del form
}

function showSnak(status, text) {
    if (status) {
        snakbar.open = status
        snakbar.text = text
        snakbar.icon = 'mdi:check'
        snakbar.color = 'green'
    } else {
        snakbar.open = true
        snakbar.text = text
        snakbar.icon = 'mdi:error'
        snakbar.color = 'red'
    }
}

const permissions = computed(() => usePage().props.auth.permissions);

const tables = computed(() => {
    let tables = []
    let index = props.sucursales.data.findIndex(item => item.id === data.form.sucursal)
    if (index >= 0) {
        tables = props.sucursales.data[index].tablesOp
    }
    return tables
});

const platillos = computed(() => {
    let platillos = []
    let index = props.sucursales.data.findIndex(item => item.id === data.form.sucursal)
    if (index >= 0) {
        platillos = props.sucursales.data[index].platillos
    }
    return platillos
});

</script>

<template>
    <Head title="Venta"></Head>
    <AuthenticatedLayout>
        <v-card flat>
            <template v-slot:title>
                <v-row>
                    <v-col md="2">
                        <h1>Venta</h1>
                    </v-col>
                    <v-spacer></v-spacer>
                </v-row>
            </template>

            <v-stepper v-model="data.step">
                <v-stepper-header>
                    <v-stepper-item
                        complete
                        editable
                        title="Datos Generales"
                        :value="1"
                    >
                        <template v-slot:icon>
                            <Icon icon="mdi:local-restaurant" width="22"/>
                        </template>
                    </v-stepper-item>

                    <v-divider></v-divider>

                    <v-stepper-item
                        complete
                        editable
                        :value="2"
                    >
                        <template v-slot:icon>
                            <Icon icon="game-icons:meal" width="21"/>
                        </template>
                        <template v-slot:title>
                            <v-badge v-if="data.form.platillos.length>0" color="red" floating
                                     :content="data.form.platillos.length">
                                Platillos
                            </v-badge>
                            <p v-else>
                                Platillos
                            </p>
                        </template>
                    </v-stepper-item>

                    <v-divider></v-divider>

                    <v-stepper-item
                        editable
                        title="Final"
                        :value="3"
                    >
                        <template v-slot:icon>
                            <Icon icon="mdi:account-cash" width="22"/>
                        </template>
                    </v-stepper-item>
                </v-stepper-header>
                <v-stepper-window>
                    <v-stepper-window-item
                        v-for="n in 3"
                        :key="`${n}-content`"
                        :value="n"
                    >
                        <v-card v-if="n===1">
                            <v-col>
                                <v-select
                                    label="Sucursal"
                                    v-model="data.form.sucursal"
                                    :error="!!data.form.errors.sucursal"
                                    :error-messages="data.form.errors.sucursal"
                                    :items="sucursales.data"
                                    item-title="name"
                                    item-value="id"
                                    variant="outlined"
                                    density="compact"
                                ></v-select>
                            </v-col>
                            <v-col>
                                <v-select
                                    label="Mesero"
                                    v-model="data.form.mesero"
                                    :error="!!data.form.errors.mesero"
                                    :error-messages="data.form.errors.mesero"
                                    :items="meseros"
                                    item-title="name"
                                    item-value="id"
                                    variant="outlined"
                                    density="compact"
                                ></v-select>
                            </v-col>
                            <v-col>
                                <v-select
                                    label="Mesa"
                                    v-model="data.form.name"
                                    :error="!!data.form.errors.name"
                                    :error-messages="data.form.errors.name"
                                    variant="outlined"
                                    density="compact"
                                    :items="tables"
                                ></v-select>
                            </v-col>
                        </v-card>
                        <v-card v-if="n===2">
                            <v-row class="mt-1">
                                <v-col md="8">
                                    <v-select
                                        label="Platillo"
                                        v-model="data.form.platillo"
                                        :items="platillos"
                                        item-title="name"
                                        item-value="id"
                                        variant="outlined"
                                        density="compact"
                                    ></v-select>
                                </v-col>
                                <v-col>
                                    <v-text-field
                                        label="Cantidad de Platillos"
                                        v-model="data.form.platillo_count"
                                        variant="outlined"
                                        density="compact"
                                        type="number"
                                    ></v-text-field>
                                </v-col>
                                <v-col>
                                    <v-btn
                                        color="orange"
                                        block
                                    >
                                        <Icon icon="mdi:plus" width="24"/>
                                        Agregar
                                    </v-btn>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-data-table
                                    :items="data.form.platillos"
                                ></v-data-table>
                            </v-row>
                        </v-card>
                        <v-card v-if="n===3">

                        </v-card>
                    </v-stepper-window-item>
                </v-stepper-window>
                <v-stepper-actions>
                    <template v-slot:prev>
                        <v-btn @click="data.step=data.step-1">Anterior</v-btn>
                    </template>
                    <template v-slot:next>
                        <v-btn @click="data.step=data.step+1">Siguiente</v-btn>
                    </template>
                </v-stepper-actions>
            </v-stepper>
            <v-snackbar
                v-model="snakbar.open"
                :timeout="2000"
                :color="snakbar.color"
            >
                <Icon style="color:white" :icon="snakbar.icon" width="24"></Icon>
                {{ snakbar.text }}

                <template v-slot:actions>
                    <v-btn
                        color="white"
                        @click="snakbar.open = false"
                    >
                        Cerrar
                    </v-btn>
                </template>
            </v-snackbar>
        </v-card>
    </AuthenticatedLayout>
</template>

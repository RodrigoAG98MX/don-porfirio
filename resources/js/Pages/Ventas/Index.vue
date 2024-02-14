<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Icon} from "@iconify/vue";
import {computed, reactive, watch} from "vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

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
        number_table: '',
        platillo: '',
        platillo_count: 1,
        platillos: [],
        date: new Date(),
        time: '',
        propina: 0,
        total: 0,
        amount: 0
    }),
    platillos: [],
    tables: [],
    headers: [
        {
            title: 'Nombre',
            value: 'name',
        }, {
            title: 'Precio',
            value: 'price'
        }, {
            title: 'Total',
            value: 'total',
        }, {
            title: 'Monto',
            value: 'amount'
        }
    ],
    loading: false
});

const snakbar = reactive({
    text: '',
    open: false,
    icon: '',
    color: '',
})

//TODO: Almaenar venta en el back_end
function saveVenta() {
    /*if (data.form.id) {
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
    } else {*/
    data.form.post(route('panel.ventas.store'), {
        onSuccess: page => {
            if (!page.props.status.error) {
                data.step = 1
                data.form.reset()
                showSnak(true, page.props.status.success)
            } else {
                showSnak(false, page.props.status.error)
            }
        }
    })
    //}
}

function addPlatillo() {
    if (data.form.sucursal && data.form.platillo) {
        let index_su = props.sucursales.data.findIndex(item => item.id === data.form.sucursal)
        let index = props.sucursales.data[index_su].platillos.findIndex(item => item.id === data.form.platillo)
        let item = props.sucursales.data[index_su].platillos[index]
        let platillo = {
            id: item.id,
            name: item.name,
            price: item.price,
            total: data.form.platillo_count,
            amount: item.price * data.form.platillo_count
        }
        data.form.total = data.form.total + platillo.amount
        data.form.platillos.push(platillo)
        data.form.platillo = ''
        data.form.platillo_count = 1
    }
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

function setTables() {
    data.loading = true
    let index = props.sucursales.data.findIndex(item => item.id === data.form.sucursal)
    if (index >= 0) {
        data.tables = props.sucursales.data[index].tablesOp
    }
    data.loading = false
}

function setPlatillos() {
    let index = props.sucursales.data.findIndex(item => item.id === data.form.sucursal)
    if (index >= 0) {
        data.platillos = props.sucursales.data[index].platillos
    }
}

watch(
    () => data.form.sucursal,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            setTables()
            setPlatillos()
        }
    }
)
watch(
    () => data.form.propina,
    (newValue, oldValue) => {
        data.form.amount = data.form.total + parseInt(newValue)
    }
)
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
                                    v-model="data.form.number_table"
                                    :error="!!data.form.errors.number_table"
                                    :error-messages="data.form.errors.number_table"
                                    variant="outlined"
                                    density="compact"
                                    :items="data.tables"
                                    :loading="data.loading"
                                ></v-select>
                            </v-col>
                        </v-card>
                        <v-card v-if="n===2">
                            <v-row class="mt-1">
                                <v-col md="8">
                                    <v-select
                                        label="Platillo"
                                        v-model="data.form.platillo"
                                        :items="data.platillos"
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
                                        @click="addPlatillo()"
                                    >
                                        <Icon icon="mdi:plus" width="24"/>
                                        Agregar
                                    </v-btn>
                                </v-col>
                            </v-row>
                            <v-data-table
                                :headers="data.headers"
                                :items="data.form.platillos"
                            ></v-data-table>
                        </v-card>
                        <v-card v-if="n===3">
                            <v-sheet height="450">
                                <v-row justify="center">
                                    <v-col md="4">
                                        <p class="text-subtitle-2">Fecha</p>
                                        <VueDatePicker
                                            v-model="data.form.date"
                                            :enable-time-picker="false"
                                            locale="es-MX"
                                            teleport-center format="d-MM-y"
                                        ></VueDatePicker>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-col md="4">
                                        <p class="text-subtitle-2">Hora</p>
                                        <VueDatePicker
                                            v-model="data.form.time"
                                            time-picker
                                            locale="es-MX"
                                            teleport-center
                                            :min-time="{ hours: 9, minutes: 0 }"
                                            :max-time="{ hours: 18, minutes: 1 }"
                                        ></VueDatePicker>
                                        <span v-if="data.form.errors.time"
                                              class="text-red">{{ data.form.errors.time }}</span>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-col cols="4">
                                        <v-text-field
                                            label="Propina"
                                            v-model="data.form.propina"
                                            variant="outlined"
                                            density="compact"
                                            type="number"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-col cols="1">
                                        <p class="text-center text-subtitle-2">Subtotal ${{ data.form.total }}</p>
                                    </v-col>
                                </v-row>
                                <v-row justify="center">
                                    <v-col cols="1">
                                        <p class="text-center text-h6">Total ${{ data.form.amount }}</p>
                                    </v-col>
                                </v-row>
                            </v-sheet>
                        </v-card>
                    </v-stepper-window-item>
                </v-stepper-window>
                <v-stepper-actions :disabled="false">
                    <template v-slot:prev>
                        <v-btn variant="flat" color="orange" :disabled="data.step===1" @click="data.step=data.step-1">
                            Anterior
                        </v-btn>
                    </template>
                    <template v-slot:next>
                        <v-btn variant="flat" color="orange" v-if="data.step<3" @click="data.step=data.step+1">
                            Siguiente
                        </v-btn>
                        <v-btn variant="flat" color="orange" v-if="data.step===3" @click="saveVenta"
                               :loading="data.form.processing">
                            Guardar
                        </v-btn>
                    </template>
                </v-stepper-actions>
            </v-stepper>
            <v-snackbar
                v-model="snakbar.open"
                :timeout="2000"
                :color="snakbar.color"
            >
                <Icon style="color:white" :icon="snakbar.icon" width="24"></Icon>
                <p class="text-white">{{ snakbar.text }}</p>
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

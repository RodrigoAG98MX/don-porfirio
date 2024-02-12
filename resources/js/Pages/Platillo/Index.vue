<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Icon} from "@iconify/vue";
import {computed, reactive, watch} from "vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    modelData: Array,
    sucursales: Array,
    platillos: Object,
    perPage: Number
})

const data = reactive({
    search: '',
    itemsPerPage: props.perPage,
    page: props.platillos.meta.curent_page,
    dialog: false,
    form: useForm({
        id: '',
        name: '',
        description: '',
        preparation_time: '',
        cost: 0,
        price: 0,
        sucursal: [],
    })
});

const snakbar = reactive({
    text: '',
    open: false,
    icon: '',
    color: '',
})

function openDialog(item = null) {
    if (item !== null) {
        data.form.id = item.id
        data.form.name = item.name
        data.form.description = item.description
        data.form.preparation_time = item.preparation_time
        data.form.cost = item.cost
        data.form.price = item.price
        data.form.sucursal = item.sucursal
    }
    data.dialog = true
}

function closeDialog() {
    data.dialog = false
}

function savePlatillo() {
    if (data.form.id) {
        data.form.put(route('panel.platillos.update', data.form.id), {
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
        data.form.post(route('panel.platillos.store'), {
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

function deletePlatillo(user_id) {
    router.delete(route('panel.platillos.destroy', user_id), {
        onSuccess: page => {
            if (!page.props.status.error) {
                showSnak(true, page.props.status.success)
            } else {
                showSnak(false, page.props.status.error)
            }
        }
    })
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

function getIcon(icon) {
    let icons = {
        '&laquo; Previous': 'mdi:menu-left',
        'Next &raquo;': 'mdi:menu-right',
        'default': icon
    }
    return icons[icon] ?? icons['default']
}

function isNumber(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

function getFullUrl(link = route('panel.platillos.index')) {
    let params = {}
    let queryString = window.location.search;
    let urlParams = new URLSearchParams(queryString);
    const url = new URL(link);
    const urlParams2 = new URLSearchParams(url.search);
    if (urlParams.has('perPage')) {
        params.perPage = urlParams.get('perPage')
    }
    if (urlParams2.has('page')) {
        params.page = urlParams2.get('page')
    }
    if (data.search) {
        params.search = data.search
    }
    router.get(route('panel.platillos.index', params))
}

watch(
    () => data.itemsPerPage,
    (newValue, oldValue) => {
        let queryString = window.location.search;
        let urlParams = new URLSearchParams(queryString);
        //TODO: Generar un objeto e ir comprobando con un has si tiene la propiedad para irla agregando al objeto
        if (newValue !== oldValue) {
            router.get(route('panel.platillos.index', {
                'perPage': newValue
            }))
        }
    }
)
const type = computed(() => data.form.id ? 'Editar' : 'Agregar');

const permissions = computed(() => usePage().props.auth.permissions);
</script>

<template>
    <Head title="Platillos"></Head>
    <AuthenticatedLayout>
        <v-card flat>
            <template v-slot:title>
                <v-row>
                    <v-col md="2">
                        <h1>Platillos</h1>
                    </v-col>
                    <v-spacer></v-spacer>
                    <v-col md="2">
                        <v-btn v-if="permissions.includes('create-platillos')" @click="openDialog" block variant="plain"
                               density="compact">
                            <Icon icon="mdi:plus" width="24"/>
                            Agregar
                        </v-btn>
                    </v-col>
                </v-row>
            </template>
            <template v-slot:text>
                <v-row>
                    <v-col>
                        <v-text-field
                            variant="outlined"
                            v-model="data.search"
                            label="Buscar"
                            density="compact"
                            @keyup.enter="getFullUrl()"
                        >
                            <template v-slot:prepend>
                                <Icon icon="mdi:search" width="24"/>
                            </template>
                        </v-text-field>
                    </v-col>
                    <!--<v-col md="1">
                        <v-menu>
                            <template v-slot:activator="{ props }">
                                <v-btn block v-bind="props">
                                    <Icon icon="mdi:filter" width="24"/>
                                    Filtros
                                </v-btn>
                            </template>

                            <v-list>
                                <v-list-item
                                    v-for="(item, i) in [0,1]"
                                    :key="i"
                                >
                                    <v-list-item-title>{{ item.title }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-col>-->
                </v-row>
            </template>

            <v-data-table
                :headers="modelData"
                :items="platillos.data"
                density="compact"
            >
                <template v-slot:item.actions="{item}">
                    <v-btn v-if="permissions.includes('update-platillos')" icon @click="openDialog(item)"
                           variant="plain"
                           density="compact">
                        <Icon icon="mdi:pencil" width="24"/>
                    </v-btn>
                    <v-btn v-if="permissions.includes('delete-platillos')" icon @click="deletePlatillo(item.id)"
                           variant="plain" density="compact">
                        <Icon icon="mdi:delete" width="24"/>
                    </v-btn>
                </template>
                <template v-slot:bottom>
                    <v-row justify="center">
                        <v-col md="1">
                            <v-text-field
                                :model-value="data.itemsPerPage"
                                class="pa-2"
                                hide-details
                                label="Items per page"
                                min="-1"
                                max="15"
                                type="number"
                                @update:model-value="data.itemsPerPage = parseInt($event, 10)"
                                variant="outlined"
                                density="compact"
                            ></v-text-field>
                        </v-col>
                        <v-col>
                            <v-btn
                                v-for="(item,key) in props.platillos.meta.links"
                                :disabled="!item.url"
                                :key="key"
                                @click="getFullUrl(item.url)"
                            >
                                <Icon v-if="!isNumber(item.label)" :icon="getIcon(item.label)"
                                      width="24"></Icon>
                                <span v-else>{{ item.label }}</span>
                            </v-btn>
                        </v-col>
                    </v-row>
                </template>
            </v-data-table>
            <v-dialog
                v-model="data.dialog"
                width="1024"
            >
                <v-card>
                    <v-toolbar
                        color="grey-darken-3"
                    >
                        <v-toolbar-title>{{ type }} Platillo</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Nombre"
                                    v-model="data.form.name"
                                    :error="!!data.form.errors.name"
                                    :error-messages="data.form.errors.name"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col md="2">
                                <v-text-field
                                    label="Costo"
                                    v-model="data.form.cost"
                                    :error="!!data.form.errors.cost"
                                    :error-messages="data.form.errors.cost"
                                    prefix="$"
                                    variant="outlined"
                                    density="compact"
                                    type="number"
                                ></v-text-field>
                            </v-col>
                            <v-col md="2">
                                <v-text-field
                                    label="Precio"
                                    v-model="data.form.price"
                                    :error="!!data.form.errors.price"
                                    :error-messages="data.form.errors.price"
                                    prefix="$"
                                    variant="outlined"
                                    density="compact"
                                    type="number"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-textarea
                                    label="Descripción"
                                    v-model="data.form.description"
                                    :error="!!data.form.errors.description"
                                    :error-messages="data.form.errors.description"
                                    variant="outlined"
                                    rows="3"
                                    row-height="15"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <p class="text-subtitle-2">Tiempo de preparación</p>
                                <VueDatePicker v-model="data.form.preparation_time" time-picker
                                               teleport-center></VueDatePicker>
                            </v-col>
                            <v-col>
                                <v-select
                                    class="mt-5"
                                    label="Sucursales"
                                    v-model="data.form.sucursal"
                                    :error="!!data.form.errors.sucursal"
                                    :error-messages="data.form.errors.sucursal"
                                    :items="sucursales"
                                    item-title="name"
                                    item-value="id"
                                    variant="outlined"
                                    density="compact"
                                    multiple
                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            @click="closeDialog"
                            color="red"
                            :disabled="data.form.processing"
                            variant="flat"
                        >
                            Cerrar
                        </v-btn>
                        <v-btn
                            @click.prevent="savePlatillo()"
                            color="green"
                            :loading="data.form.processing"
                            variant="flat"
                        >
                            Guardar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
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

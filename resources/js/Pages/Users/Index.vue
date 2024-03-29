<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Icon} from "@iconify/vue";
import {computed, reactive, watch} from "vue";

const props = defineProps({
    modelData: Array,
    users: Object,
    perPage: Number
})

const data = reactive({
    search: '',
    itemsPerPage: props.perPage,
    page: props.users.meta.curent_page,
    dialog: false,
    form: useForm({
        id: '',
        name: '',
        first_name: '',
        email: '',
        telephone: '',
        nss: '',
        rfc: '',
        salary: 0,
        street: '',
        number: '',
        suburb: '',
        cp: '',
        references: '',
        state: '',
        country: ''
    }),
    loading: false
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
        data.form.first_name = item.first_name
        data.form.email = item.email
        data.form.telephone = item.telephone
        data.form.nss = item.nss
        data.form.rfc = item.rfc
        data.form.salary = item.salary
        data.form.street = item.address ? item.address.street : ''
        data.form.number = item.address ? item.address.number : ''
        data.form.suburb = item.address ? item.address.suburb : ''
        data.form.cp = item.address ? item.address.cp : ''
        data.form.references = item.address ? item.address.references : ''
        data.form.state = item.address ? item.address.state : ''
        data.form.country = item.address ? item.address.country : ''
    }
    data.dialog = true
}

function closeDialog() {
    data.dialog = false
}

function saveUser() {
    if (data.form.id) {
        data.form.put(route('panel.users.update', data.form.id), {
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
        data.form.post(route('panel.users.store'), {
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

function deleteUser(user_id) {
    router.delete(route('panel.users.destroy', user_id), {
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

function getFullUrl(link = route('panel.users.index')) {
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
    data.loading = true
    router.get(route('panel.users.index', params))
}

watch(
    () => data.itemsPerPage,
    (newValue, oldValue) => {
        let queryString = window.location.search;
        let urlParams = new URLSearchParams(queryString);
        //TODO: Generar un objeto e ir comprobando con un has si tiene la propiedad para irla agregando al objeto
        if (newValue !== oldValue) {
            data.loading = true
            router.get(route('panel.users.index', {
                'perPage': newValue
            }))
        }
    }
)
const type = computed(() => data.form.id ? 'Editar' : 'Agregar');

const permissions = computed(() => usePage().props.auth.permissions);
</script>

<template>
    <Head title="Usuarios"></Head>
    <AuthenticatedLayout>
        <v-card flat>
            <template v-slot:title>
                <v-row>
                    <v-col md="2">
                        <h1>Usuarios</h1>
                    </v-col>
                    <v-spacer></v-spacer>
                    <v-col md="2">
                        <v-btn v-if="permissions.includes('create-users')" @click="openDialog" block variant="plain"
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
                            placeholder="escribe y presiona enter"
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
                :items="users.data"
                density="compact"
                :loading="data.loading"
                loading-text="Cargando..."
                no-data-text="No hay resultados"
            >
                <template v-slot:item.actions="{item}">
                    <v-btn v-if="permissions.includes('update-users')" icon @click="openDialog(item)" variant="plain"
                           density="compact">
                        <Icon icon="mdi:pencil" width="24"/>
                    </v-btn>
                    <v-btn v-if="permissions.includes('delete-users')" icon @click="deleteUser(item.id)" variant="plain"
                           density="compact">
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
                                v-for="(item,key) in props.users.meta.links"
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
                        <v-toolbar-title>{{ type }} Usuario</v-toolbar-title>
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
                            <v-col>
                                <v-text-field
                                    label="Apellidos"
                                    v-model="data.form.first_name"
                                    :error="!!data.form.errors.first_name"
                                    :error-messages="data.form.errors.first_name"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Correo electrónico"
                                    v-model="data.form.email"
                                    :error="!!data.form.errors.email"
                                    :error-messages="data.form.errors.email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Teléfono"
                                    v-model="data.form.telephone"
                                    :error="!!data.form.errors.telephone"
                                    :error-messages="data.form.errors.telephone"
                                    variant="outlined"
                                    density="compact"
                                    counter
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="NSS"
                                    v-model="data.form.nss"
                                    :error="!!data.form.errors.nss"
                                    :error-messages="data.form.errors.nss"
                                    variant="outlined"
                                    density="compact"
                                    counter
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="RFC"
                                    v-model="data.form.rfc"
                                    :error="!!data.form.errors.rfc"
                                    :error-messages="data.form.errors.rfc"
                                    variant="outlined"
                                    density="compact"
                                    counter
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Salario (Diario)"
                                    v-model="data.form.salary"
                                    :error="!!data.form.errors.salary"
                                    :error-messages="data.form.errors.salary"
                                    variant="outlined"
                                    density="compact"
                                    type="number"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Calle"
                                    v-model="data.form.street"
                                    :error="!!data.form.errors.street"
                                    :error-messages="data.form.errors.street"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col md="2">
                                <v-text-field
                                    label="Número"
                                    v-model="data.form.number"
                                    :error="!!data.form.errors.number"
                                    :error-messages="data.form.errors.number"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Colonia"
                                    v-model="data.form.suburb"
                                    :error="!!data.form.errors.suburb"
                                    :error-messages="data.form.errors.suburb"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Código postal"
                                    v-model="data.form.cp"
                                    :error="!!data.form.errors.cp"
                                    :error-messages="data.form.errors.cp"
                                    variant="outlined"
                                    density="compact"
                                    counter
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Referencias"
                                    v-model="data.form.references"
                                    :error="!!data.form.errors.references"
                                    :error-messages="data.form.errors.references"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Estado"
                                    v-model="data.form.state"
                                    :error="!!data.form.errors.state"
                                    :error-messages="data.form.errors.state"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="País"
                                    v-model="data.form.country"
                                    :error="!!data.form.errors.country"
                                    :error-messages="data.form.errors.country"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
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
                            @click.prevent="saveUser()"
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

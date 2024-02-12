<script setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Icon} from "@iconify/vue";
import {computed, reactive, watch} from "vue";
import {da} from "vuetify/locale";

const props = defineProps({
    modelData: Array,
    roles: Object,
    users: Array,
    permissions: Array,
    perPage: Number
})

const data = reactive({
    search: '',
    itemsPerPage: props.perPage,
    page: props.roles.meta.curent_page,
    dialog: false,
    form: useForm({
        id: '',
        name: '',
        users: [],
        permissions: [],
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
        data.form.permissions = item.permissions
        data.form.users = item.users
    }
    data.dialog = true
}

function closeDialog() {
    data.dialog = false
}

function saveRole() {
    if (data.form.id) {
        data.form.put(route('panel.roles.update', data.form.id), {
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
        data.form.post(route('panel.roles.store'), {
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

function deleteRole(user_id) {
    router.delete(route('panel.roles.destroy', user_id), {
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

function getFullUrl(link = route('panel.roles.index')) {
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
    router.get(route('panel.roles.index', params))
}

watch(
    () => data.itemsPerPage,
    (newValue, oldValue) => {
        let queryString = window.location.search;
        let urlParams = new URLSearchParams(queryString);
        //TODO: Generar un objeto e ir comprobando con un has si tiene la propiedad para irla agregando al objeto
        if (newValue !== oldValue) {
            router.get(route('panel.roles.index', {
                'perPage': newValue
            }))
        }
    }
)
const type = computed(() => data.form.id ? 'Editar' : 'Agregar');

const permissions = computed(() => usePage().props.auth.permissions);
</script>

<template>
    <Head title="Roles"></Head>
    <AuthenticatedLayout>
        <v-card flat>
            <template v-slot:title>
                <v-row>
                    <v-col md="2">
                        <h1>Roles</h1>
                    </v-col>
                    <v-spacer></v-spacer>
                    <v-col md="2">
                        <v-btn v-if="permissions.includes('create-roles')" @click="openDialog" block variant="plain"
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
                :items="roles.data"
                density="compact"
            >
                <template v-slot:item.actions="{item}">
                    <v-btn v-if="permissions.includes('update-roles')" icon @click="openDialog(item)" variant="plain"
                           density="compact">
                        <Icon icon="mdi:pencil" width="24"/>
                    </v-btn>
                    <v-btn v-if="permissions.includes('delete-roles')" icon @click="deleteRole(item.id)"
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
                                v-for="(item,key) in props.roles.meta.links"
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
                        <v-toolbar-title>{{ type }} Role</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-col>
                            <v-text-field
                                label="Nombre"
                                v-model="data.form.name"
                                :error="!!data.form.errors.name"
                                :error-messages="data.form.errors.name"
                                variant="outlined"
                                density="compact"
                                :disabled="['Administrador','Mesero'].includes(data.form.name)"
                            ></v-text-field>
                        </v-col>
                        <v-col>
                            <v-select
                                label="Usuarios"
                                v-model="data.form.users"
                                :error="!!data.form.errors.users"
                                :error-messages="data.form.errors.users"
                                clearable
                                chips
                                :items="users"
                                item-title="name"
                                item-value="id"
                                multiple
                                variant="outlined"
                                density="compact"
                            ></v-select>
                        </v-col>
                        <v-col>
                            <v-select
                                label="Permisos"
                                v-model="data.form.permissions"
                                :error="!!data.form.errors.permissions"
                                :error-messages="data.form.errors.permissions"
                                clearable
                                chips
                                :items="permissions"
                                item-title="name"
                                item-value="id"
                                multiple
                                variant="outlined"
                                density="compact"
                            ></v-select>
                        </v-col>
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
                            @click.prevent="saveRole()"
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

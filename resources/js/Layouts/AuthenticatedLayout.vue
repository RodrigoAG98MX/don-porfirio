<script setup>
import {Icon} from "@iconify/vue";
import {router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

function logout() {
    router.post(route('logout'))
}

const permissions = computed(() => usePage().props.auth.permissions);

</script>

<template>
    <v-app>
        <v-app-bar scroll-behavior="elevate" color="grey-darken-3">
            <template v-slot:prepend>
                <v-menu
                    open-on-click
                >
                    <template v-slot:activator="{ props }">
                        <v-btn
                            v-bind="props"
                        >
                            <Icon icon="material-symbols:menu" width="24"/>
                        </v-btn>
                    </template>

                    <v-list>
                        <v-list-item :href="route('panel.dashboard')">
                            <template v-slot:prepend>
                                <Icon icon="mdi:home" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Panel</v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="permissions.includes('browse-roles')" :href="route('panel.roles.index')">
                            <template v-slot:prepend>
                                <Icon icon="carbon:user-role" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Roles</v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="permissions.includes('browse-users')" :href="route('panel.users.index')">
                            <template v-slot:prepend>
                                <Icon icon="mdi:users" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Usuarios</v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="permissions.includes('browse-sucursales')"
                                     :href="route('panel.sucursales.index')">
                            <template v-slot:prepend>
                                <Icon icon="mdi:shop" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Sucursales</v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="permissions.includes('browse-platillos')"
                                     :href="route('panel.platillos.index')">
                            <template v-slot:prepend>
                                <Icon icon="game-icons:meal" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Platillos</v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="permissions.includes('browse-ventas')" :href="route('panel.ventas.index')">
                            <template v-slot:prepend>
                                <Icon icon="material-symbols:point-of-sale" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Venta</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click.prevent="logout">
                            <template v-slot:prepend>
                                <Icon icon="material-symbols:logout" width="24" class="mr-2"/>
                            </template>
                            <v-list-item-title>Cerrar sesión</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
            <v-app-bar-title>Don Porfirio - Restaurante</v-app-bar-title>
        </v-app-bar>
        <!-- Page Content -->
        <v-container fluid>
            <v-main>
                <slot/>
            </v-main>
        </v-container>
    </v-app>
</template>

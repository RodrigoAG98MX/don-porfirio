<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <v-sheet max-width="500" class="mx-auto">
            <v-card title="Bienvenido" :subtitle="$page.props.auth.user.name">
                <template slot="text">

                </template>
            </v-card>
        </v-sheet>
    </AuthenticatedLayout>
</template>

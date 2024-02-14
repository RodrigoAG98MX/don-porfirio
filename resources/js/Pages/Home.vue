<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <v-container fluid>
            <v-card title="Bienvenido" :subtitle="$page.props.auth.user.name">
                <v-card-text>

                </v-card-text>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

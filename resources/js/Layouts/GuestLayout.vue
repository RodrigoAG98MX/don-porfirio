<script setup>
import {Icon} from "@iconify/vue";
</script>

<template>
    <v-app>
        <v-app-bar scroll-behavior="elevate" color="grey-darken-3">
            <v-app-bar-title>Don Porfirio - Restaurante</v-app-bar-title>
        </v-app-bar>
        <v-main>
            <slot/>
        </v-main>
    </v-app>
</template>

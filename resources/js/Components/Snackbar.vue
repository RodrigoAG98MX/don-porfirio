<script setup>
import {Icon} from "@iconify/vue";
import {ref} from "vue";

const props = defineProps({
    text: String,
    open: Boolean,
    icon: String,
    color: String,
})

const snakbar = ref({
    text: props.text,
    open: props.open,
    icon: props.icon,
    color: props.color,
})

</script>

<template>
    <div>
        <v-snackbar
            :v-model="true"
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
    </div>
</template>

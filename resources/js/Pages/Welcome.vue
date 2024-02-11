<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {Icon} from "@iconify/vue";

const form = useForm({
    email: '',
    password: '',
})

function send() {
    form.post(route('login'), {
        onFinish: () => {
            form.reset()
        }
    })
}
</script>

<template>
    <Head title="Welcome"/>
    <GuestLayout>
        <v-container fluid>
            <v-sheet width="300" class="mx-auto">
                <v-row justify="center" class="my-4">
                    <Icon icon="material-symbols:login" width="28"/>
                </v-row>
                <v-row>
                    <v-text-field
                        v-model="form.email"
                        label="Email"
                        type="email"
                        :error="!!form.errors.email"
                        :error-messages="form.errors.email"
                        variant="outlined"
                    ></v-text-field>
                </v-row>
                <v-row>
                    <v-text-field
                        v-model="form.password"
                        label="constraseña"
                        type="password"
                        :error="!!form.errors.password"
                        :error-messages="form.errors.password"
                        variant="outlined"
                    ></v-text-field>
                </v-row>
                <v-row>
                    <v-btn
                        :loading="form.processing"
                        block
                        color="primary"
                        @click="send()"
                    >
                        iniciar sesión
                    </v-btn>
                </v-row>
            </v-sheet>
        </v-container>
    </GuestLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateProductForm from '@/Pages/Products/Partials/CreateProductForm.vue';
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Producto
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateProductForm />
            </div>
        </div>
    </AppLayout>
</template>

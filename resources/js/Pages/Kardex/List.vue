<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from '@inertiajs/vue3';
    import { faTrashAlt, faPencilAlt, faPrint, faWarehouse } from "@fortawesome/free-solid-svg-icons";
    import Pagination from '@/Components/Pagination.vue';
    import Keypad from '@/Components/Keypad.vue';

    const props = defineProps({
        establisment: {
            type: Object,
            default: () => ({}),
        },
        kardexes: {
            type: Object,
            default: () => ({}),
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
    });

    const form = useForm({
        search: props.filters.search,
    });


</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="col-span-6 p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center justify-between pb-4 bg-white dark:bg-gray-900">
                        <form @submit.prevent="form.get(route('users.index'))">
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                                </div>
                                <input v-model="form.search" type="text" id="table-search-users" class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar venta por fecha">
                            </div>
                        </form>
                        <div class="text-right">
                            <Keypad>
                                <template #botones>
                                    <a :href="route('users.create')" class="inline-block px-6 py-2.5 bg-blue-900 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Nuevo</a>
                                </template>
                            </Keypad>
                        </div>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-2">
                        <table class="w-full text-sm text-left text-blue-100 dark:text-blue-100">
                            <thead class="text-xs text-white uppercase bg-blue-600 border-b border-blue-400 dark:text-white">
                                <tr>
                                    <th scope="col" class="px-6 py-2">
                                        Acciones
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        Fecha
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        Cliente
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <Pagination :data="kardexes" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>
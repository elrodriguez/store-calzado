<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import SearchProducts from '@/Pages/Sales/Partials/SearchProducts.vue';
    import { faTrashAlt } from "@fortawesome/free-solid-svg-icons";

    import { useForm } from '@inertiajs/vue3';

    const form = useForm({
        products: [],
    });

    let xproducts = [] ;

    const getDataTable = async (data) =>{
        form.products.push(data);
        console.log(xproducts);
    }
</script>

<template>
    <AppLayout title="Punto de Ventas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Punto de Venta
            </h2>
        </template>

        <div>
            <div class="mx-auto py-2 sm:px-2 lg:px-4">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 sm:col-span-12 md:col-span-6 bg-teal-400 p-4">
                        <SearchProducts @eventdata="getDataTable" />
                        <div class="mt-4" style="height: 270px;overflow-y: auto;">
                            <table class="min-w-full">
                                <thead class="border-b bg-gray-800">
                                    <tr>
                                        <th class="text-sm font-medium text-white px-6 py-2"></th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-2">
                                            Código
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-2">
                                            Descripción
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-2">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-2">
                                            Precio
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-2">
                                            Importe
                                        </th>
                                    </tr>
                                </thead >
                                <tbody>
                                    <tr v-for="(product, key) in form.products" class="border-b bg-gray-800 boder-gray-900">
                                        <td class="text-center text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            <button type="button" class="inline-block rounded-full bg-blue-600 text-white leading-normal uppercase shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out w-9 h-9">
                                                <font-awesome-icon :icon="faTrashAlt" />
                                            </button>
                                        </td>
                                        <td class="text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.interne  }}
                                        </td>
                                        <td class="text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.description  }}
                                        </td>
                                        <td class="text-right text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.quantity  }}
                                        </td>
                                        <td class="text-right text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.price  }}
                                        </td>
                                        <td class="text-right text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.total  }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-12 md:col-span-3 h-4 bg-teal-400p-4">
                        
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

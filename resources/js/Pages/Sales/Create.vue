<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import SearchProducts from '@/Pages/Sales/Partials/SearchProducts.vue';
    import SearchClients from './Partials/SearchClients.vue';
    import { faTrashAlt } from "@fortawesome/free-solid-svg-icons";
    import { useForm } from '@inertiajs/vue3';

    const props = defineProps({
        payments: {
            type: Object,
            default: () => ({}),
        },
        client: {
            type: Object,
            default: () => ({}),
        }
    });

    const form = useForm({
        products: [],
        total: 0,
        payments: [{
            type:1,
            reference: null,
            amount:0
        }],
        client:{
            id: props.client.id,
            full_name: props.client.full_name
        }
    });

    const getDataTable = async (data) => {
        form.total = parseFloat(data.total) + parseFloat(form.total);
        form.products.push(data);
        form.payments[0].amount = form.total;
    }

    const removeProduct = (key) => {
        form.products.splice(key,1);
    }

    const addPayment = () => {
        let ar = {
            type:1,
            reference: null,
            amount:0
        };
        form.payments.push(ar);
    };
    const removePayment = (index) => {
        if(index>0){
            form.payments.splice(index,1);
        }
    };
    const saveSale = async () => {

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
                        <div class="mt-4 mb-4" style="height: 255px;">
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
                                <tbody style="max-height: 250px;overflow-y: auto;overflow-x: hidden;">
                                   <template v-if="form.products.length > 0">
                                    <tr v-for="(product, key) in form.products" class="border-b bg-gray-800 boder-gray-900">
                                        <td class="text-center text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            <div class="relative">
                                                <button @click="removeProduct(key)" type="button" class="inline-block rounded-full bg-blue-600 text-white leading-normal uppercase shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out w-9 h-9">
                                                    <font-awesome-icon :icon="faTrashAlt" />
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.interne  }}
                                        </td>
                                        <td class="text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                            {{ product.description  }} / {{ product.size  }}
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
                                   </template>
                                   <template v-else>
                                        <tr class="border-b bg-gray-800 boder-gray-900">
                                           <td colspan="6" class="text-center text-sm text-white font-medium px-6 py-2 whitespace-nowrap">
                                                <div class="flex p-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                                                    <svg aria-hidden="true" class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                    <span class="sr-only">Info</span>
                                                    <div>
                                                        <span class="font-medium">Basio!</span> Agregar productos
                                                    </div>
                                                </div>
                                           </td> 
                                        </tr>
                                   </template>
                                </tbody>
                                <tfoot>
                                    <tr class="border-b bg-gray-800 boder-gray-900">
                                        <td colspan="5" class="text-right text-sm text-white font-medium px-6 py-2 whitespace-nowrap"> Total a Cobrar</td>
                                        <td class="text-right text-sm text-white font-medium px-6 py-2 whitespace-nowrap">S/. {{ form.total }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-12 md:col-span-6 bg-teal-400 p-4">
                        <SearchClients :clientDefault="client" />
                        <div>
                            <h4 class="italic font-bold mb-4">Medio de Pago</h4>
                            <div class="grid grid-cols-10 gap-4">
                                <template v-for="(row, index) in form.payments" v-bind:key="index">
                                    <div class="col-span-3 mb-2">
                                        <select v-model="row.type" id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <template v-for="(payment) in payments">
                                                <option :value="payment.id">{{ payment.description }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-span-3 mb-2">
                                        <input v-model="row.reference" type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Referencia" required>
                                    </div>
                                    <div class="col-span-3 mb-2">
                                        <input v-model="row.amount" type="text" id="first_name" class="text-right bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Monto" required>
                                    </div>
                                    <div class="col-span-1">
                                        <button @click="removePayment(index)" type="button" class="inline-block rounded-full bg-blue-600 text-white leading-normal uppercase shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out w-9 h-9">
                                            <font-awesome-icon :icon="faTrashAlt" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button @click="addPayment()" type="button" class="inline-block px-6 py-2 bg-transparent text-blue-600 font-medium text-xs leading-tight uppercase rounded transition duration-150 ease-in-out">Agregar (+)</button>
                        </div>
                        <div class="grid grid-cols-6 gap-4">
                            <div class="col-end-7 col-span-2">
                                <button @click="saveSale()" type="button" class="w-full inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out">
                                    Cobrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

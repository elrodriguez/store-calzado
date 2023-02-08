<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from '@inertiajs/vue3';
    import { faTrashAlt, faPencilAlt, faPrint, faWarehouse } from "@fortawesome/free-solid-svg-icons";
    import Pagination from '@/Components/Pagination.vue';
    import DialogModal from '@/Components/DialogModal.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import { ref } from 'vue';

    const props = defineProps({
        products: {
            type: Object,
            default: () => ({}),
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
    })
    const form = useForm({
        search: props.filters.search,
    });

    const formDetails = useForm({
        usine: '',
        interne: '',
        description: '',
        image: '',
        purchase_prices: '',
        sale_prices:{
            high:'',
            medium: '',
            under:''
        },
        sizes: [{
            size:'',
            quantity: ''
        }],
        stock_min:'',
        stock:'',
        quantity_total:''

    });

    const formDelete = useForm({});
    const openModalDetilsProduct = ref(false);
        
    const showDetailProduct = (product) => {
        formDetails.interne = product.interne;
        formDetails.description = product.description;
        formDetails.purchase_prices = product.purchase_prices;
        formDetails.sale_prices = JSON.parse(product.sale_prices);
        formDetails.sizes = JSON.parse(product.sizes);
        formDetails.stock_min = product.stock_min;
        formDetails.stock = product.stock;   
        formDetails.quantity_total=0;         
        formDetails.sizes.forEach(size => {
            formDetails.quantity_total+= parseFloat(size.quantity); //*1 para parsear a numero
        });


        openModalDetilsProduct.value = true;
    }    

    function destroy(id) {
        if (confirm("¿Estás seguro de que quieres eliminar?")) {
            formDelete.delete(route('products.destroy', id));
        }
    }
    const closeModalDetailsProduct = () => {
      openModalDetilsProduct.value = false;
    }
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Productos
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="col-span-6 p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <form @submit.prevent="form.get(route('products.index'))">
                        <div class="grid grid-cols-3 gap-4 py-2">
                            <div>
                                <input type="search" v-model="form.search"
                                class="
                                        form-control
                                        block
                                        w-full
                                        px-3
                                        py-1.5
                                        text-base
                                        font-normal
                                        text-gray-700
                                        bg-white bg-clip-padding
                                        border border-solid border-gray-300
                                        rounded
                                        transition
                                        ease-in-out
                                        m-0
                                        focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none
                                    "
                                placeholder="Escriba nombre">
                            </div>
                            <div class="">
                                <button type='submit' class='inline-block px-6 py-2.5 bg-gray-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-900 hover:shadow-lg focus:bg-gray-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-900 active:shadow-lg transition duration-150 ease-in-out'>
                                    Buscar
                                </button>
                            </div>
                            <div v-can="'productos_nuevo'" class="text-right">
                                <a :href="route('products.create')" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Nuevo</a>
                            </div>
                        </div>
                    </form>

                    <table class="border mb-4" style="width: 100%;">
                        <thead class="border-b">
                            <tr>
                                <th scope="col" class="w-2.5 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                #
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-8 py-6 border-r">
                                    Acción
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Imagen
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Código
                                </th>
                                <th scope="col" class="text-left text-sm font-medium text-gray-900 px-4 py-2">
                                    Descripción
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in products.data" :key="product.id" class="border-b">
                                <td class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                                    {{ index + 1 }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    <div class="flex space-x-2 justify-center">
                                        <div>
                                            <a v-permission="'productos_editar'" :href="route('products.edit',product.id)" class="mr-1 inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out">
                                                <font-awesome-icon :icon="faPencilAlt" />
                                            </a>
                                            <button type="button" class="mr-1 inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out">
                                                <font-awesome-icon :icon="faPrint" />
                                            </button>
                                            <button type="button" class="mr-1 inline-block px-6 py-2.5 bg-blue-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                              @click="showDetailProduct(product)"
                                            >
                                              <font-awesome-icon :icon="faWarehouse" />
                                            </button>
                                            <button type="button" class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                              @click="destroy(product.id)"
                                              >
                                              <font-awesome-icon :icon="faTrashAlt" />
                                          </button>

                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    <img :src="product.image">
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ product.interne }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-2 py-2 whitespace-nowrap">
                                    {{ product.description }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <Pagination :data="products" />
                </div>

            </div>
        </div>

        <DialogModal :show="openModalDetilsProduct" @close="closeModalDetailsProduct">
            <template #title>
              {{ formDetails.interne }} - {{ formDetails.description }}
            </template>

            <template #content>
              <table class="max-w-full">
                    <thead class="bg-white border-b">
                      <tr class="text-xs text-white bg-blue-700 border-b dark:text-white">
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Tallas
                        </th>
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Cantidad
                        </th>
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Precio V. Normal
                        </th>
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Precio V. Medio
                        </th>
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Precio V. Minimo
                        </th>
                        <th class="text-lg font-medium  px-6 py-4 text-left italic hover:not-italic">
                          Precio de Compra
                        </th>
                      </tr>
                    </thead>
                    <tbody>

                      <tr v-for="(size, index) in formDetails.sizes" :key="formDetails.id"  class="border-b">
                        <td class="text-right px-6 py-4 text-sm font-medium text-white bg-blue-500">
                          {{ size.size }}</td>
                        <td class="text-right text-sm text-white font-light px-6 py-4 bg-blue-700">
                          {{ size.quantity }}
                        </td>
                        <td class="text-right text-sm text-white font-light px-6 py-4 bg-blue-500">
                          S/. {{ formDetails.sale_prices.high }}
                        </td>
                        <td class="text-right text-sm text-white font-light px-6 py-4 bg-blue-700">
                          S/. {{ formDetails.sale_prices.medium }}
                        </td>
                        <td class="text-right text-sm text-white font-light px-6 py-4 bg-blue-500">
                          S/. {{ formDetails.sale_prices.under }}
                        </td>
                        <td class="text-right text-sm text-white font-light px-6 py-4 bg-blue-700">
                          S/. {{ formDetails.purchase_prices }}
                        </td>
                      </tr>


                      <tr class=" border-b">
                        <td class="text-right px-6 py-4 text-sm font-medium bg-blue-500 text-white">
                            Totales
                        </td>
                        <td class="text-right text-sm text-white font-light bg-blue-700 px-6 py-4 ">
                          {{ formDetails.quantity_total }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-blue-500 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.high }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-blue-700 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.medium }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-blue-500 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.under }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-blue-700 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.purchase_prices }}
                        </td>
                      </tr>     
                      
                      <tr class="border-b">
                        <td colspan="2" class="text-right px-6 py-4 text-sm bg-green-800 font-medium text-white">
                          Ganancias Esperadas
                        </td>
                        <td class="text-right text-sm text-white font-light bg-green-600 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.high-(formDetails.quantity_total*formDetails.purchase_prices) }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-green-800 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.medium-(formDetails.quantity_total*formDetails.purchase_prices) }}
                        </td>
                        <td class="text-right text-sm text-white font-light bg-green-600 px-6 py-4">
                          S/. {{ formDetails.quantity_total*formDetails.sale_prices.under-(formDetails.quantity_total*formDetails.purchase_prices) }}
                        </td>
                        <td class="text-sm text-white font-light bg-green-800 px-6 py-4">
                          <!-- no usar esta clase whitespace-nowrap -->
                        </td>
                      </tr>  
                    </tbody>
                  </table>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModalDetailsProduct">
                    Cancel
                </SecondaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

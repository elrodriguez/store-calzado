<script setup>
    import DialogModal from '@/Components/DialogModal.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import { ref } from 'vue';
    import { useForm } from '@inertiajs/vue3';

    const displayModal = ref(false);

    const form = useForm({
        search: null,
        products: [],
        product: {},
        data:{
            id:'',
            interne: '',
            stock:'',
            description:'',
            price:'',
            size:'',
            quantity: 1
        }
    });
    const searchProducts = async () => {
        axios.post(route('search_product'), form ).then((res) => {
            form.products = res;
        });
    };
    const closeModalSelectProduct  = () => {
        displayModal.value = false;
    }
    const openModalSelectProduct = async (product) => {
        displayModal.value = true;
        form.products = [];
        form.product = product;
        form.data.id = product.id;
        form.data.interne = product.interne;
        form.data.stock = product.stock;
        form.data.description = product.description;
        form.data.price = JSON.parse(product.sale_prices).high;
        form.data.total = 0;
        form.data.quantity = 1;
        form.search = null;
    }

    const emit = defineEmits(['eventdata']);

    const addProduct = () => {
        if(form.data.size){
            let total = parseFloat(form.data.quantity)*parseFloat(form.data.price)
            form.data.total = total;
            let data = {
                id: form.data.id,
                interne: form.data.interne,
                description: form.data.description,
                price: form.data.price,
                total: form.data.total,
                quantity: form.data.quantity,
                size: form.data.size,
            }
            emit('eventdata',data)
            displayModal.value = false;
        }else{
            alert('Seleccionar Talla')
        }
    }
</script>


<template>
    <div style="position: relative;">
        <form @submit.prevent="searchProducts()"> 
            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input v-model="form.search" autocomplete="off" type="search" id="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar Producto" required>
                <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buscar</button>
            </div>
        </form>
        <div  id="result" style="position: absolute;width: 100%;">
            <div class="mt-1" style="height: 300px;overflow-y: auto;">
                <table class="min-w-full" >
                    <tbody>
                        <tr @click="openModalSelectProduct(product)" v-for="(product, index) in form.products.data" class="border-b bg-gray-100 boder-gray-900" style="cursor: pointer;">
                            <td class="text-sm font-medium px-6 py-4 whitespace-nowrap">
                                {{ product.interne }} - {{ product.description }}
                            </td>
                        </tr>
                    </tbody> 
                </table>
            </div>
        </div>
    </div>
    <DialogModal 
        :show="displayModal" 
        @close="closeModalSelectProduct"
    >
        <template #title>
            Detalles del Producto
        </template>
        <template #content>
            <div class="grid grid-cols-3">
                <div class="col-span-1">
                    <div class="flex flex-wrap justify-center p-4">
                        <img
                        :src="form.product.image"
                        class="p-1 bg-white border rounded max-w-sm"
                        :alt="form.product.description"
                        style="width: 100%;"
                        />
                    </div>
                </div>
                <div class="col-span-2">
                    <h4>{{ form.product.interne  }} - {{ form.product.description  }}</h4>
                    <p class="my-4">Stock Actual : {{ form.data.stock  }}</p>
                    <div class="mb-4">
                        <label for="stock" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Precios Disponibles
                        </label>
                        <div class="flex">
                            <div>
                                <div class="form-check">
                                    <input v-model="form.data.price" :value="JSON.parse(form.product.sale_prices).high" class="form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                    <label class="form-check-label inline-block text-gray-800" for="flexRadioDefault1">
                                        Precio Normal {{ JSON.parse(form.product.sale_prices).high  }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input v-model="form.data.price" :value="JSON.parse(form.product.sale_prices).medium" class="form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                    <label class="form-check-label inline-block text-gray-800" for="flexRadioDefault2">
                                        Precio Medio {{ JSON.parse(form.product.sale_prices).medium  }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input v-model="form.data.price" :value="JSON.parse(form.product.sale_prices).under" class="form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="flexRadioDefault" id="flexRadioDefault3">
                                    <label class="form-check-label inline-block text-gray-800" for="flexRadioDefault3">
                                        Precio Minimo {{ JSON.parse(form.product.sale_prices).under  }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="size" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Tallas Disponibles
                        </label>
                        <div class="flex">
                            <div>
                                <template v-for="(item, key) in JSON.parse(form.product.sizes)">
                                    <div class="form-check">
                                        <input :disabled="item.quantity == 0 ? '' : disabled" v-model="form.data.size" :value="item.size" class="form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="sizes" :id="'size'+ key">
                                        <label :class="item.quantity == 0 ? 'text-gray-500': 'text-gray-800'" class="form-check-label inline-block" :for="'size'+ key ">
                                            Talla: {{ item.size }} / Cantidad: {{ item.quantity }}
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Cantidad a vender
                        </label>
                        <input v-model="form.data.quantity" type="number" id="quantity" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            
            <DangerButton
                class="mr-3"
                @click="addProduct()"
            >
                Agregar
            </DangerButton>
            <SecondaryButton @click="closeModalSelectProduct">
                Cancel
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
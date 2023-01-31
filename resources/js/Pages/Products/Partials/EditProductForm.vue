<script setup>
    import { useForm } from '@inertiajs/vue3';
    import FormSection from '@/Components/FormSection.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { library } from "@fortawesome/fontawesome-svg-core";
    import { faTrashAlt } from "@fortawesome/free-solid-svg-icons";
    import { router } from '@inertiajs/vue3'
    
    const props = defineProps({
        product: {
            type: Object,
            default: () => ({}),
        }
    });

    const form = useForm({
        usine: props.product.usine,
        interne: props.product.interne,
        description: props.product.description,
        image: '',
        purchase_prices: props.product.purchase_prices,
        sale_prices: JSON.parse(props.product.sale_prices),
        sizes: JSON.parse(props.product.sizes),
        stock_min:props.product.stock_min,
        stock:props.product.stock

    });

    const editProduct = () => {
        form.put(route('products.update', props.product.id), {
            //forceFormData: true,
            errorBag: 'editProduct',
            preserveScroll: true,
        });
    };

    const addSize = () => {
        let ar = {
            size:'',
            quantity: ''
        };
        form.sizes.push(ar);
    };

    const removeSize = (index) => {
        if(index>0){
            form.sizes.splice(index,1);
        }
    };

    library.add(faTrashAlt);

</script>

<template>
    <FormSection @submitted="editProduct">
        <template #title>
            Producto Detalles
        </template>

        <template #description>
            Crear nuevo producto para realizar ventas
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="usine" value="Código Fabrica" />
                <TextInput
                    id="usine"
                    v-model="form.usine"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.usine" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="interne" value="Código Interno" />
                <TextInput
                    id="interne"
                    v-model="form.interne"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.interne" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="description" value="Descripción" />
                <TextInput
                    id="description"
                    v-model="form.description"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>
            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="image" value="Imagen" />
                <input type="file" @input="form.image = $event.target.files[0]" />
            </div> -->
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="purchase_prices" value="Precio de compra" />
                <TextInput
                    id="purchase_prices"
                    v-model="form.purchase_prices"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.purchase_prices" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="sale_prices" value="Precio de venta" />
                <TextInput
                    id="sale_prices"
                    v-model="form.sale_prices.high"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.sale_prices" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="sale_prices_medium" value="Precio de venta Medio" />
                <TextInput
                    id="sale_prices_medium"
                    v-model="form.sale_prices.medium"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.sale_prices" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="sale_prices_under" value="Precio de venta Minimo" />
                <TextInput
                    id="sale_prices_under"
                    v-model="form.sale_prices.under"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.sale_prices" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-6">
                <label>
                    Tallas
                    <button @click="addSize" type="button" class="inline-block px-6 py-2.5 bg-transparent text-blue-600 font-medium text-xs leading-tight uppercase rounded hover:text-blue-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none focus:ring-0 active:bg-gray-200 transition duration-150 ease-in-out">Agregar</button>
                </label>
                <div v-for="(item, index) in form.sizes" v-bind:key="index">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 4px;">
                                <div class="col-span-3 sm:col-span-2">
                                    <InputLabel value="Talla" />
                                    <TextInput
                                        v-model="item.size"
                                        type="text"
                                        class="block w-full mt-1"
                                        autofocus
                                    />
                                </div>
                            </td>
                            <td style="padding: 4px;">
                                <div class="col-span-3 sm:col-span-2">
                                    <InputLabel value="Cantidad" />
                                    <TextInput
                                        v-model="item.quantity"
                                        type="number"
                                        class="block w-full mt-1"
                                        autofocus
                                    />
                                </div>
                            </td>
                            <td style="padding: 4px;" valign="bottom">
                                <button @click="removeSize(index)" type="button" class="inline-block rounded-full bg-blue-600 text-white leading-normal uppercase shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out w-9 h-9">
                                    <font-awesome-icon :icon="faTrashAlt" />
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Actualizar
            </PrimaryButton>
            <a :href="route('products.index')"  class="ml-2 inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out">Ir al Listado</a>
        </template>
    </FormSection>
</template>

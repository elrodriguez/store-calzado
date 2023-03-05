
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { faTrashAlt, faPencilAlt, faPrint } from "@fortawesome/free-solid-svg-icons";
import { propsToAttrMap } from '@vue/shared';

const props = defineProps({
    locals: {
        type: Object,
        default: () => ({}),
    },
    sales: {
        type: Object,
        default: () => ({}),
    },
    start: {
        type: Object,
        default: () => ({}),
    },
    end: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    start:props.start,
    end:props.end,
});
console.log(props.sales);
function getLocal(id){
    let arreglo = props.locals;
    let local = arreglo.find(arreglo => arreglo.id == id);
    return local.description;
}

function getTotalQuantities(){
    let quantities=0;
    // let arreglo = props.locals;
    // arreglo.forEach(sale=> {
    //     quantities+=JSON.parse(sale.product).quantity;
    // });
    return quantities;
}
function getTotalPrices(){
    let prices=0;
    // props.locals.forEach(sale=> {
    //     quantities+=JSON.parse(sale.product).quantity*JSON.parse(sale.product).price;
    // });
    return prices;
}
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reportes
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="col-span-6 p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                        <div class="grid grid-cols-4 gap-3 py-2" id="form-dates">
                            <div>
                                <input type="date" v-model="form.start"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f1" />
                            </div>
                            <div>
                                <input type="date" v-model="form.end"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f2" />
                            </div>

                            <div class="text-right">
                                <ModalCashCreate :locals="locals" />
                            </div>
                        </div>
                </div>

            </div>
        </div><hr>
        <div id="ContenidoTabla">
            <table class="border mb-4" style="width: 100%;">
                        <thead class="border-b">

                            <tr v-if="form.start==form.end"><th colspan="7" class="text-center fs-1" style="text-align: center">Matos Store - Ventas del día: {{ form.start }} </th></tr>
                            <tr v-else><th colspan="7" class="text-center fs-1" style="text-align: center">Matos Store - Ventas del: {{ form.start }} al {{ form.end }}</th></tr>

                            <tr>
                                <th scope="col" class="w-2.5 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                #
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Fecha
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Tienda
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Cod. Prod.
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Producto
                                </th>
                                <th scope="col" class="w-4 text-left text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Precio Vendido
                                </th>
                                <th scope="col" class="w-4 text-left text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Cantidad
                                </th>
                                <th scope="col" class="w-4 text-left text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Talla
                                </th>
                                <th scope="col" class="w-4 text-left text-sm font-medium text-gray-900 px-6 py-4">
                                    Total
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(sale, index) in props.sales" :key="sale.id" class="border-b">
                                <td class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                                    {{ index + 1 }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ sale.created_at }}
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ getLocal(sale.local_id) }}
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ sale.interne }}
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                   <img :src="'/storage/'+sale.image" alt=""> {{ sale.product_description }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                   {{ JSON.parse(sale.product).price }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ JSON.parse(sale.product).quantity }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ JSON.parse(sale.product).size }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ JSON.parse(sale.product).quantity * JSON.parse(sale.product).price }}
                                </td>
                            </tr>

                        </tbody>

                        <tfoot>
                            <tr class="table-dark">
                                        <th class="fs-4" style="text-align: center">#</th>
                                        <th class="fs-4" style="text-align: center" colspan="3">Totales</th>
                                        <th class="fs-4" style="text-align: left"></th>
                                        <th class="fs-4" style="text-align: center"></th>
                                        <th class="fs-4" style="text-align: center">{{ getTotalQuantities()}}</th>
                                        <th class="fs-4" style="text-align: center"></th>
                                        <th class="fs-4" style="text-align: center">S/ {{ getTotalPrices()}}</th>
                            </tr>
                        </tfoot>
                        
                    </table>
                    <Pagination :data="sales" />

                </div>

    </AppLayout>
</template>

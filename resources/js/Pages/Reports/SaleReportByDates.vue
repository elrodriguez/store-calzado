
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import * as XLSX from 'xlsx/dist/xlsx.full.min';
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
    local_id:0,
    sales:props.sales,
    local_name:"TODOS LOS LOCALES"
});

function getLocal(id){
    let arreglo = props.locals;
    let local = arreglo.find(arreglo => arreglo.id == id);
    return local.description;
}

function getTotalQuantities(){
    let quantities=0;
    let arreglo = form.sales;
    arreglo.forEach(sale=> {
        quantities+=JSON.parse(sale.product).quantity;
    });
    return quantities;
}
function getTotalPrices(){
    let prices=0;
    form.sales.forEach(sale=> {
        prices+=JSON.parse(sale.product).quantity*JSON.parse(sale.product).price;
    });
    return prices;
}

function getReport(){
    let url = route('sales_report_dates', {
  start: form.start,
  end: form.end,
  local_id: form.local_id,
  consulta:true
});
axios.get(url)
  .then(response => {
    console.log(response.data);
    form.sales=null;
    form.sales = response.data;
    getTotalPrices();
    getTotalQuantities();
  })
  .catch(err => {
    console.log("ERROR ENCONTRADO", err);
  });

  props.locals.forEach(local => {
    if(form.local_id == local.id){
        form.local_name = local.description;
    }
    if(form.local_id == 0)form.local_name= "TODOS LOS LOCALES";
  });
}

// const botonImprimir = document.getElementById('boton-imprimir');
// botonImprimir.addEventListener('click', () => {
//   window.print();
// });

function downloadExcel(){
    /* Create worksheet from HTML DOM TABLE */
    const table = document.getElementById("table_export");
    const wb = XLSX.utils.table_to_book(table);
    wb['!autofit'] = true;

    /* Export to file (start a download) */
    let filename="ventas-"+form.start+"-"+form.end+".xlsx";
    XLSX.writeFile(wb, filename);
    test();
}

function test(){
    const workbook = XLSX.utils.book_new();

// Obtén la tabla HTML que deseas convertir
const table = document.getElementById('table_export');

// Convierte la tabla HTML en una hoja de cálculo
const worksheet = XLSX.utils.table_to_sheet(table);
worksheet['!cols'] = [
  {width:4}, // Columna "A" #
  {width:12}, // Columna "B" Fecha
  { width: 30 }, // Columna "C" Tienda
  {width:9}, // Columna "D" Cod. Prod.
  { width: 35 }, // Columna "E" Producto
  {width:12}, // Columna "F" Precio Vendido
  {width:9}, // Columna "G" Cantidad
  {width:9}, // Columna "H" Talla
  {width:9}, // Columna "I" Total
];

XLSX.utils.book_append_sheet(workbook, worksheet, form.start+'-'+form.end);
XLSX.writeFile(workbook, 'Ventas'+form.start+'-'+form.end+'.xlsx');
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

                    <div class="col-span-4 sm:col-span-1">
                            <InputLabel for="stablishment" value="Establecimiento" />
                            <select v-model="form.local_id" v-on:change="getReport()"
                                id="stablishment"
                                class="w-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="0">Todos los Locales</option>
                                <template v-for="(local, index) in props.locals" :key="index">
                                    <option :value="local.id">{{ local.description }}</option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-4 gap-3 py-2" id="form-dates">
                            <div>
                                <input type="date" v-model="form.start" v-on:change="getReport()"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f1" />
                            </div>
                            <div>
                                <input type="date" v-model="form.end" v-on:change="getReport()"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f2" />
                            </div>

                            <div>
                                <button v-on:click="downloadExcel()"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                     >Exportar a Excel</button>
                            </div>



                            <div class="text-right">
                                <ModalCashCreate :locals="locals" />
                            </div>
                        </div>
                </div>

            </div>
        </div><hr>
        <div id="ContenidoTabla">
            <table id="table_export" class="table border mb-4 table-fixed" style="width: 100%;">
                        <thead class="border-b">

                            <tr v-if="form.start==form.end"><th colspan="7" class="text-center fs-1" style="text-align: center">Ventas del día: {{ form.start }} </th></tr>
                            <tr v-else><th colspan="7" class="text-center fs-1" style="text-align: center">Matos Store - Ventas del: {{ form.start }} al {{ form.end }}</th></tr>
                            <tr><th colspan="7" class="text-center fs-1" style="text-align: center">De: {{ form.local_name }}</th></tr>

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
                            <tr v-for="(sale, index) in form.sales" :key="sale.id" class="border-b">
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
                                   <img :src="'/storage/'+sale.image" width="40"> {{ sale.product_description }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                   {{ JSON.parse(sale.product).price }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ JSON.parse(sale.product).quantity }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
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

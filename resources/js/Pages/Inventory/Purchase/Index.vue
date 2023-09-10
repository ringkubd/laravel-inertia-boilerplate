<template>
  <Head>
    <title>Inventory Purchase</title>
  </Head>
  <Authenticated>
    <template #header>
      <page-header>Purchase Management</page-header>
    </template>
    <div class="container-fluid min-h-screen">
      <div class="card mt-1 min-vh-100">
        <div class="card-header">
          <CardHeader :can="can" :search-method="search">
            <template #first>
              <Back :back-url="route('inventory.index')"></Back>
            </template>
          </CardHeader>
        </div>
        <div class="card-body">
          <div class="flex flex-column">
            <!-- Form  -->
            <div>
              <div class="flex flex-column justify-content-center align-items-center">
                <form @submit.prevent="formSubmit" class="w-full md:w-2/3">
                  <div class="flex flex-row justify-content-center space-x-8">
                    <div class="space-x-2 mb-6 w-full">
                      <label for="product" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product</label>
                      <select2
                          id="product"
                          v-model="form.product_id"
                          :settings="productSettings"
                      />
                      <Button @click.prevent="openTab(route('product.create'))">+</Button>
                    </div>
                    <div class="space-x-2 mb-6 w-full">
                      <label for="supplier" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                      <select2
                          id="supplier"
                          v-model="form.supplier_id"
                          :settings="suppliersSettings"
                      />
                      <Button @click.prevent="openTab(route('supplier.index'))">+</Button>
                    </div>
                  </div>
                  <div class="flex flex-row justify-content-center space-x-8">
                    <div class="space-x-2 mb-6 w-full">
                      <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity</label>
                      <input
                          type="number"
                          id="quantity"
                          step="0.01"
                          v-model="form.quantity"
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="space-x-2 mb-6 w-full">
                      <label for="unit_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Price</label>
                      <input
                          type="number"
                          step="0.01"
                          id="unit_price"
                          v-model="form.unit_price"
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                  </div>
                  <div class="flex flex-row justify-content-center space-x-8">
                    <div class="space-x-2 mb-6 w-full">
                      <label for="unit_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total Price</label>
                      <input
                          type="number"
                          step="0.01"
                          id="unit_price"
                          v-model="form.total_price"
                          disabled
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="space-x-2 mb-6 w-full">
                      <label for="purchase_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Purchase Date</label>
                      <input
                          type="date"
                          id="purchase_date"
                          v-model="form.purchase_date"
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                  </div>
                  <Button type="submit">Submit</Button>
                </form>
              </div>
            </div>
            <!-- End Form  -->
            <!-- Table  -->
            <div>
              <DataTable
                  :rows="data.data"
                  sn
                  filter
              >
                <template #thead-sn>
                  <TableHead>SN</TableHead>
                </template>
                <template #thead>
                  <TableHead>Name</TableHead>
                  <TableHead>Supplier</TableHead>
                  <TableHead>Quantity</TableHead>
                  <TableHead>Unit Price</TableHead>
                  <TableHead>Total Price</TableHead>
                  <TableHead>Purchase Date</TableHead>
                  <TableHead>Actions</TableHead>
                </template>

                <template #tbody-sn="{sn}">
                  <TableHead v-text="sn.toString().padStart(2, '0')"/>
                </template>

                <template #tbody="{row}">
                  <TableBody v-text="row.product?.title" />
                  <TableBody v-text="row.supplier?.name" />
                  <TableBody v-text="row.quantity" />
                  <TableBody v-text="row.unit_price" />
                  <TableBody v-text="row.total_price" />
                  <TableBody v-text="row.purchase_date" />
                  <TableBody>
                    <InertiaLink :href="route('purchase.destroy', row.id)" method="DELETE">Delete</InertiaLink>
                  </TableBody>
                </template>

                <template #empty>
                  <TableBodyCell colspan="7">
                    No record found.
                  </TableBodyCell>
                </template>
              </DataTable>
              <Paginator :paginator="data" />
            </div>
            <!-- Table End  -->
          </div>
        </div>
      </div>
    </div>
  </Authenticated>
</template>
<script>

import Authenticated from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Shared/PageHeader.vue";
import Back from "@/Shared/Back.vue";
import CardHeader from "@/Shared/CardHeader.vue";
import Button from "@/Shared/Button.vue";
import Input from "@/Components/Input.vue";

import { DataTable, TableBodyCell, TableHead, TableBody } from "@jobinsjp/vue3-datatable"
import "@jobinsjp/vue3-datatable/dist/style.css"
import {useForm} from "@inertiajs/inertia-vue3";
import Paginator from "@/Components/Paginator.vue";

export default {
  name: 'PurchaseIndex',
  props: ['can', 'data', 'products'],
  components: {
    Paginator,
    Input,
    Button,
    CardHeader,
    Back,
    PageHeader,
    Authenticated,
    DataTable,
    TableBodyCell,
    TableHead,
    TableBody
  },
  data(){
    return {
      form: useForm({
        product_id: '',
        supplier_id: '',
        quantity: '',
        unit_price: '',
        total_price: '',
        purchase_date: '',
        __token: this.$page.props.csrf
      }),
      stocks: {},
      productSettings: {
        ajax: {
          url: route('purchase_products'),
          dataType: 'json',
          data: function (params) {
            const queryParameters = {
              search: params.term
            };

            return queryParameters;
          },
          processResults: function (data) {
            return {
              results: data
            };
          },
        }
      },
      suppliersSettings: {
        ajax: {
          url: route('purchase_suppliers'),
          dataType: 'json',
          data: function (params) {
            const queryParameters = {
              search: params.term
            };

            return queryParameters;
          },
          processResults: function (data) {
            return {
              results: data
            };
          },
        }
      }
    }
  },
  methods: {
    search(){
    },
    formSubmit(){
      this.form.post(route('purchase.store'), {
        onSuccess: (s) => {
          this.form.reset()
        },
        onError: (e) => console.log(e)
      })
    },
    openTab(url) {
      const link = document.createElement('a');
      link.href = url;
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      link.remove();
    }
  },
  watch: {
    quantity(){
      this.form.total_price = this.form.quantity * this.form.unit_price
    },
    unit_price(){
      this.form.total_price = this.form.quantity * this.form.unit_price
    },
    total_price(){
      return this.form.total_price
    }
  },
  computed: {
    quantity(){
      this.form.total_price = this.form.quantity * this.form.unit_price
    },
    unit_price(){
      this.form.total_price = this.form.quantity * this.form.unit_price
    },
    total_price(){
      return this.form.total_price
    },
    productId(){
      axios
          .get(route(`purchase_existing_stocks`, this.form.product_id))
          .then(r => {
            this.stocks =  r.data
          })
    },
  }
}
</script>

<style scoped>

</style>

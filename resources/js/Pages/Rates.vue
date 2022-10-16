<template>
    <div class="grid grid-cols-3 bg-gray-50 dark:bg-gray-700 top-0 mt-0 p-0 fixed top-0 w-full z-10 py-5">
        <div>
            <img src="https://ltech.lv/wp-content/themes/ltech/images/ltech-light.svg" alt="logo" class="h-16 ml-16">
        </div>
        <div class="flex">
            <h1 class="text-gray-50 text-2xl text-center font-bold m-auto">Exchange Rates</h1>
        </div>
    </div>
    <div class="w-screen mt-16 py-16" style="height: calc(100vh - 140px)">
        <div class="overflow-x-auto relative mx-auto w-1/2 rounded-xl" style="height: calc(100vh - 140px)">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 overflow-auto">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                <tr>
                    <th width="33%" v-for="column in columns" @click="order(column.ref)" scope="col" class="py-3 px-6 text-center cursor-pointer">
                        <div class="flex h-8">
                            <div class="flex flex-grow"></div>
                            {{column.title}}
                            <img src="/img/arrow.png" alt="order" class="transform h-6 ml-6 -mt-1" v-if="orderData.column === column.ref" :class="{'-rotate-90': !orderData.orderASC, 'rotate-90': orderData.orderASC}">
                            <div class="flex flex-grow"></div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="rate in rateData" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th width="33%" scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
                        {{rate.currency}}
                    </th>
                    <td width="33%" class="py-4 px-6 text-center">
                        {{rate.rate.toFixed(5)}}
                    </td>
                    <td width="33%" class="py-4 px-6 text-center">
                        {{new Date(rate.updated_at).toLocaleDateString('day', 'month', 'year')}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name:"Rates",
    props:{
        rates:Array
    },
    data(){
        return{
            columns:[
                {
                    title: 'currency',
                    ref: 'currency'
                },
                {
                    title: 'rate',
                    ref: 'rate'
                },
                {
                    title: 'last update',
                    ref: 'updated_at'
                }
            ],
            orderData: {
                column: '',
                orderASC: false
            },
            rateData: {}
        }
    },
    methods:{
        order(column){
            if(this.orderData.column === '' || this.orderData.column !== column){
                this.sortASC(column)
            } else{
                if (this.orderData.orderASC) {
                    this.sortASC(column)
                } else {
                    this.sortDESC(column)
                }
            }
        },
        sortASC(column){
            this.rateData.sort((x, y) => (x[column] < y[column] ? -1 : 1))
            this.orderData.orderASC = false
            this.orderData.column = column
        },
        sortDESC(column){
            this.rateData.sort((x, y) => (x[column] > y[column] ? -1 : 1))
            this.orderData.orderASC = true
            this.orderData.column = column
        }
    },
    created(){
        this.rateData = this.rates
        this.rateData.map(function (rate){
            return rate.rate = parseFloat(rate.rate)
        })
    }
}
</script>

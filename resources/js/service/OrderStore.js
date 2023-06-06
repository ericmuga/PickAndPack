
import {defineStore} from "pinia";
import { Inertia } from "@inertiajs/inertia";
import axios from 'axios'
// import route from "vendor/tightenco/ziggy/src/js";
// import { router } from '@inertiajs/vue3'
// import route from 'vendor/tightenco/ziggy/src/js';



export let useOrderStore=defineStore('orders',{

                state:()=>({
                    //fetch orders from api
                        orders: null
                    })
                ,

                actions:{
                    async getOrders() {
                        try {
                        const response = await axios.get(route('orders.get'));
                        this.orders = response.data.orders;

                        } catch (error) {
                        console.error('Error fetching data:', error);
                        }

                    }
                }
            });

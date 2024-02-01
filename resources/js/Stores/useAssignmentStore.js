// ordersStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useAssignmentStore = defineStore({
  id: 'orders',
  state: () => ({
    orders: [],
  }),
  actions: {
    async fetchOrders() {
      const response = await axios.get('assignmentStore')
                                  .catch((response)=>{
                                    console.log(response)
                                  })
      this.orders = response.data.orders
    },
  },
});

// src/store/myDataStore.js
import { defineStore } from 'pinia';

export const useUserStore = defineStore('userStore', {
  state: () => ({
    data: null,
    isLoading: false,
  }),

  actions: {
    async fetchData() {
      // Check if data is already in the cache
      if (this.data) {
        return this.data;
      }

      // Fetch data from the backend
      try {
        this.isLoading = true;
        const response = await fetch('api/fetchUsers');
        const data = await response.json();

        // Update the store's state with the fetched data
        this.data = data;

        return data;
      } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },
  },
});

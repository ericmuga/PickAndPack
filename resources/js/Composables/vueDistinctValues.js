// vueDistinctValues.js

import { ref, computed } from 'vue';

export default function useDistinctValues(object, searchKey) {
  const distinctValues = ref([]);

  // Recursive function to perform deep search
  function deepSearch(obj) {
    const values = new Set();

    // Helper function to check if a value is an object
    function isObject(value) {
      return value !== null && typeof value === 'object';
    }

    // Recursive function to perform deep search within an object
    function search(obj) {
      if (Array.isArray(obj)) {
        obj.forEach((item) => {
          if (isObject(item)) {
            search(item);
          } else if (item === searchKey) {
            values.add(item);
          }
        });
      } else if (isObject(obj)) {
        Object.values(obj).forEach((value) => {
          if (isObject(value)) {
            search(value);
          } else if (value === searchKey) {
            values.add(value);
          }
        });
      }
    }

    search(obj);
    return Array.from(values);
  }

  // Watch for changes in the object or search key
  // and update the distinct values accordingly
  computed(() => {
    if (object && searchKey) {
      distinctValues.value = deepSearch(object);
    } else {
      distinctValues.value = [];
    }
  });

  return {
    distinctValues,
  };
}

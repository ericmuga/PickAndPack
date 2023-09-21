import { ref } from 'vue';

export function useSearchArray(initialArray) {
  const array = ref(initialArray);

  const search = (key, value) => {
    const result = array.value.find((item) => item[key] === value);

    if (result) {
      return { ...result }; // Clone the object to avoid reactivity issues
    } else {
      return 0;
    }
  };

  const advancedSearch = (searchParam, keysToSearch, searchEnd = false) => {
    const result = array.value.find((item) => {
      for (const key of keysToSearch) {
        const itemValue = item[key];
        if (itemValue) {
          if (searchEnd && itemValue.endsWith(searchParam)) {
            return true;
          }
          if (!searchEnd && itemValue.includes(searchParam)) {
            return true;
          }
        }
      }
      return false;
    });

    if (result) {
      return { ...result };
    } else {
      return 0;
    }
  };

  const searchKeyValuePair = (key, value) => {
    const result = array.value.find((item) => {
      return Object.keys(item).some((k) => k === key && item[k] === value);
    });

    if (result) {
      return { ...result }; // Clone the object to avoid reactivity issues
    } else {
      return 0;
    }
  };

  const searchByBarcodeOrItemNo = (input) => {

    const result = array.value.find((item) => {
      return item.barcode === input || item.item_no === input || item.barcode.slice(0,12)===input;
    });

    if (result) {
      return { ...result };
    } else {
      return 0;
    }
  };

  const searchByMultipleKeyValues = (keyValuePairs) => {
    const result = array.value.find((item) => {
      return keyValuePairs.every(([key, value]) => item[key] === value);
    });

    if (result) {
      return { ...result };
    } else {
      return 0;
    }
  };

  return {
    array,
    search,
    advancedSearch,
    searchKeyValuePair,
    searchByBarcodeOrItemNo,
    searchByMultipleKeyValues,
  };



}

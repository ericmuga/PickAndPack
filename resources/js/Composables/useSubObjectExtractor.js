import { ref, computed,watch } from 'vue';

// Main composable function
export function useSubObjectExtractor(mainObjects, subObjectKey) {
  const extractedSubObjects = ref([]);

  // Watch for changes in mainObjects
  watch(mainObjects, () => {
    extractSubObjects();
  }, { immediate: true });

  // Extract subobjects from mainObjects
  function extractSubObjects() {
    extractedSubObjects.value = mainObjects.map(mainObj => mainObj[subObjectKey]);
  }

  // Return the extracted subobjects as a computed property
  const subObjects = computed(() => extractedSubObjects.value);

  return {
    subObjects
  };
}

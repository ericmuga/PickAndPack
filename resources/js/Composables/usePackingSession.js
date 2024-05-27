export const resolveValue = (array, key, resolveKey) => {
    // Check if the input is a valid array
    if (!Array.isArray(array)) {
        return null;
    }

    // Find the item with the specified key
    const item = array.find(obj => obj[key] !== undefined && obj[key] === key);

    // Return the resolved value or a default message
    if (item && item[resolveKey] !== undefined) {
        return item[resolveKey];
    } else {
        return null; // Or any other default value
    }
};

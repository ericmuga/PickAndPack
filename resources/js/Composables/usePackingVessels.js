// usePackingVessels.js

const resolveDescriptionById = (id, packingVessels) => {
    const item = packingVessels.find(obj => obj.id === id);

    return item ? item.description : "Description not found";
};

const findMatchingObjects = (array, targetValue, form, props) => {
    const vesselType = resolveDescriptionById(form.packing_vessel_id, props.packingVessels.data);
    const matchingObjects = array.filter(obj =>
        obj.vessel_type === vesselType &&
        (obj.range_start === targetValue || obj.range_end === targetValue)
    );

    return matchingObjects;
};

export { resolveDescriptionById, findMatchingObjects };

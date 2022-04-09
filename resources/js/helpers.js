

let helpers = {
    toggleValueInArray(array, value) {
        if(array.includes(value)) {
            return array.filter(function (ele) { return ele !== value;});
        } else {
            array.push(value);
            return array;
        }
    }
};

export default helpers;

let formats = {
    getDateString(date, format = 'd.m.Y') {
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0');
        let yyyy = date.getFullYear()

        switch (format) {
            case "Y-m-d":
                return yyyy + "-" + mm + "-" + dd;
            case "d.m.Y":
            default:
                return dd + '.' + mm + '.' + yyyy;
        }
    }
};

export default formats;

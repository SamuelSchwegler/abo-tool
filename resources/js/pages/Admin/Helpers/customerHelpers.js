let customerHelpers = {
    getDeliveryOptions() {
        return [
            {id: 'match', name: 'Liefer- und Rechnungsadresse stimmen überein', description: '', type: 'match'},
            {id: 'split', name: 'Unterschiedliche Liefer- und Rechnungsadresse', description: '', type: 'split'},
        ]
    }
};

export default customerHelpers;

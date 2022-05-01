let customerHelpers = {
    getDeliveryOptions() {
        return [
            {id: 'match', name: 'Liefer und Rechnungsadresse stimmen überein', description: ''},
            {id: 'split', name: 'Unterschiedliche Rechnungs und Lieferadresse', description: ''},
            {id: 'pickup', name: 'Lieferung wird direkt vor Ort in Hünibach abgeholt', description: ''},
        ]
    }
};

export default customerHelpers;

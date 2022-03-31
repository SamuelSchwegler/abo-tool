<template>
    <progress-steps :steps="steps"></progress-steps>
    <div class="grid grid-cols-2 gap-4" :key="billKey">
        <div class="box">
            <h3 class="title">Ihre Bestellung</h3>
            <bundle :bundle="buy.bundle"></bundle>
        </div>
        <div class="box">
            <h3 class="title">Bezahlung</h3>
            <p>Besten Dank für Ihre Bestellung.</p>
            <p>Sie erhalten eine Email an {{ buy.customer.user.email }} mit der Rechnung für Ihre Bestellung.</p>
            <p>Bitte beachten Sie, dass das Abo erst gültig wird, wenn wir den Zahlungseingang bestätigt haben.</p>
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="proceed"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Meine Lieferungen anschauen
        </button>
    </div>
</template>

<script>
import Bundle from "../../components/Bundle";
import ProgressSteps from "../../components/parts/ProgressSteps";

export default {
    name: "Bill",
    components: {
        Bundle, ProgressSteps
    },
    data() {
        return {
            billKey: 0,
            buy: {
                bundle: {},
                customer: {
                    user: {}
                }
            },
            steps: [
                {
                    id: '01',
                    name: 'Produktauswahl',
                    description: 'Wählen Sie ihr Gemüsepaket aus.',
                    href: '#',
                    status: 'complete'
                },
                {
                    id: '02',
                    name: 'Angabe Lieferdaten',
                    description: 'Geben Sie ihre Liefer- und Rechnungsadresse an.',
                    href: '#',
                    status: 'complete'
                },
                {id: '03', name: 'Bezahlung', description: 'Zustellung Rechnung.', href: '#', status: 'current'},
            ]
        }
    },
    methods: {
        async load() {
            await this.$axios.get(`/api/buy/${this.$route.params.id}`)
                .then(response => {
                    this.buy = response.data.buy;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        proceed() {
            this.$router.push({name: 'orders'})
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>

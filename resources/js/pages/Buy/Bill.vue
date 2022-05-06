<template>
    <progress-steps :steps="steps"></progress-steps>
    <div class="grid lg:grid-cols-2 grid-cols-1 gap-4" :key="billKey">
        <div class="box bg-white">
            <h3 class="title">Ihre Bestellung</h3>
            <bundle :bundle="buy.bundle" :delivery_cost="buy.delivery_cost"></bundle>
        </div>
        <div class="box bg-white">
            <h3 class="title">Bezahlung</h3>
             Bitte beachten Sie, dass das Abonnement erst nach der
            Zahlungsbestätigung freigeschaltet wird.
            <p>Herzlichen Dank für die Bestellung ihres Gemüse-Abos! </p>
            <p>Sie erhalten die Rechnung per E-Mail an {{ buy.customer.user.email }} geschickt.</p>
            <p>Bitte beachten Sie, dass das Abonnement erst nach der Zahlungsbestätigung freigeschaltet wird.</p>
        </div>
    </div>
</template>

<script>
import ProgressSteps from "../parts/ProgressSteps";
import Bundle from "../parts/Bundle";

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

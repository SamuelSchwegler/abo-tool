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
            buy: {},
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
    created() {
        this.$axios.get(`/api/buy/${this.$route.params.id}`)
            .then(response => {
                this.buy = response.data.data;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    beforeRouteEnter(to, from, next) {
        if (!window.Laravel.isLoggedIn) {
            window.location.href = "/";
        }
        next();
    }
}
</script>

<style scoped>

</style>

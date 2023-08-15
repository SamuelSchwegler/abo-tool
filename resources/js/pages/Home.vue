<template>
    <div class="sm:flex sm:items-center mb-4" v-if="texts.hasOwnProperty('home_title')">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ texts.home_title }}</h1>
        </div>
    </div>
    <div class="box bg-white">
        <p>Werte Gemüse-Abo-Kundinnen und -Kunden, liebe Interessenten</p>
        <p class="mt-2">Per Ende Jahr 2023 müssen wir unser Gemüseangebot leider einstellen. Demnach können nur noch bis
            spätestens zum <strong>13. Oktober 2023</strong> Zahlungen getätigt bzw. Gemüse-Abos (12 Lieferungen)
            gebucht werden. Probeabos (6 Lieferungen) sind noch bis zum <strong>10. November 2023</strong> buch- bzw.
            zahlbar.
        </p>
        <p>Es gilt zu beachten, dass allfällige Pausen in diesen Fällen nicht mehr berücksichtigt werden können, da das
            Angebot Ende Dezember 2023 endet.</p>
        <p>Gerne liefern wir Ihnen bei Interesse alternative Gemüse-Abo – Adressen. Bitte melden Sie sich bei uns.</p>
        <p class="mt-2">Wir danken für Ihr Verständnis und Ihre Treue und wünschen Ihnen eine gute Zeit</p>
        <p class="mt-2">
            Herzlichst<br/>
            Ihr Gemüseabo-Team
        </p>
    </div>
    <div class="box bg-white" v-if="texts.hasOwnProperty('home_description') && texts.home_description.length > 0">
        <p>{{ texts.home_description }}</p>
    </div>
    <div class="mt-4 grid lg:grid-cols-2 gap-4 grid-cols-1">
        <bundle v-for="bundle in bundles" :bundle="bundle" :allowOrder="true"></bundle>
    </div>
    <div v-if="trials.length > 0">
        <div class="sm:flex sm:items-center mt-8">
            <div class="sm:flex-auto">
                <h3 class="sub-title">Abo ausprobieren</h3>
            </div>
        </div>
        <div class="mt-4 grid lg:grid-cols-2 gap-4 grid-cols-1">
            <bundle v-for="bundle in trials" :bundle="bundle" :allowOrder="true"></bundle>
        </div>
    </div>
</template>

<script>

import Bundle from "./parts/Bundle.vue";

export default {
    name: "Home",
    components: {
        Bundle
    },
    data: function () {
        return {
            bundles: [],
            trials: [],
            texts: {}
        }
    },
    methods: {
        async loadData() {
            await axios.get('/api/home').then(response => {
                this.bundles = response.data.bundles.filter(bundle => !bundle.trial);
                this.trials = response.data.bundles.filter(bundle => bundle.trial);
                this.texts = response.data.texts;
            }).catch(error => {

            });
        }
    },
    mounted() {
        this.loadData();
    }
}
</script>

<style scoped>

</style>

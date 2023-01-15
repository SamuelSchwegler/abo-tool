<template>
    <div class="sm:flex sm:items-center mb-4" v-if="texts.hasOwnProperty('home_title')">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{texts.home_title}}</h1>
        </div>
    </div>
    <div class="box bg-white" v-if="texts.hasOwnProperty('home_description') && texts.home_description.length > 0">
        <p>{{texts.home_description}}</p>
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
    methods:{
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

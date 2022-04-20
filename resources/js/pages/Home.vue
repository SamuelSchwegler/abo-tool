<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Frisches Biogemüse nach Hause liefern lassen</h1>
        </div>
    </div>
    <div class="box bg-white">
        <p>Abonnieren Sie unser Bio-Gemüse, und wir liefern es Ihnen frisch vom Feld bis vor die Haustür. Jede Woche
            neu.</p>
    </div>
    <div class="mt-4 grid lg:grid-cols-2 gap-4 grid-cols-1">
        <bundle v-for="bundle in bundles" :bundle="bundle" :allowOrder="true"></bundle>
    </div>
</template>

<script>
import Bundle from "../components/Bundle";

export default {
    name: "Home",
    components: {
        Bundle
    },
    data: function () {
      return {
          bundles: []
      }
    },
    methods:{
        async loadData() {
            await axios.get('/api/bundles').then(response => {
                this.bundles = response.data.data
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

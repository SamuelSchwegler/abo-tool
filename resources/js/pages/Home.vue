<template>
    <div class="box">
        <p>Abonnieren Sie unser Bio-Gemüse, und wir liefern es Ihnen frisch vom Feld bis vor die Haustür. Jede Woche
            neu.</p>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-4">
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

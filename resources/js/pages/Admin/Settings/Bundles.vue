<template>
    <PageHead page-title="Angebote">
        <template  #description>
            Die Angebote die auf der Startseite ersichtlich sind.
        </template>
    </PageHead>
    <PageContent>
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Titel</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Produkt</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Probe</th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">
                        Preis Total
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                        # Lieferungen
                    </th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">
                        Preis / Lieferung
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                        Kurzbeschreibung
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody class="bg-white">
                <bundle-row v-for="bundle in bundles" :key="bundle.id" :bundle="bundle"/>
                </tbody>
            </table>
        </div>
    </PageContent>
</template>

<script>
import BundleRow from "./../Parts/BundleRow.vue";
import PageHead from "../../../layout/PageHead.vue";
import PageContent from "../../../layout/PageContent.vue";

export default {
    name: "Bundles",
    data() {
        return {
            bundles: []
        }
    },
    methods: {
        async loadData() {
            await axios.get('/api/bundles').then(response => {
                this.bundles = response.data.data;
            }).catch(error => {

            });
        }
    },
    mounted() {
        this.loadData();
    },
    components: {
        PageContent,
        PageHead,
        BundleRow
    }
}
</script>

<style scoped>

</style>

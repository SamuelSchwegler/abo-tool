<template>
    <PageHead page-title="Design anpassen">
        <template #description>
            Passen Sie die Texte an
        </template>
    </PageHead>
    <PageContent>
        <div class="grid grid-cols-2 gap-4">
            <Box title="Texte">
                <text-input name="home_title" :value="texts.home_title" v-model="texts.home_title" class="mb-3"
                            label="Titel Startseite" :error="errors['home_title']" @change="updateText"></text-input>
                <multiline-text label="Beschreibung Startseite" v-model="texts.home_description" :value="texts.home_description" @change="updateText">
                </multiline-text>
            </Box>
        </div>
    </PageContent>
</template>

<script>

import PageHead from "../../../layout/PageHead.vue";
import PageContent from "../../../layout/PageContent.vue";
import Box from "../../../components/Box.vue";
import TextInput from "../../../components/form/textInput.vue";
import MultilineText from "../../../components/form/multilineText.vue";

export default {
    name: "Bundles",
    components: {MultilineText, TextInput, Box, PageContent, PageHead},
    data() {
        return {
            settings: {},
            texts: {},
            errors: []
        }
    },
    methods: {
        async loadData() {
            await axios.get('/api/settings').then(response => {
                this.settings = response.data.settings;
                let texts = this.settings.texts;
                this.texts = {
                    home_title: (texts !== null && texts.hasOwnProperty('home_title')) ? texts.home_title : '',
                    home_description: (texts !== null && texts.hasOwnProperty('home_description')) ? texts.home_description : ''
                };
            }).catch(error => {
                console.log(error);
            });
        },
        async updateText() {
            await axios.patch('/api/settings/texts', {
                home_title: this.texts.home_title,
                home_description: this.texts.home_description
            }).then((response) => {
            }).catch(error => {
                console.log(error);
            });
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>

<style scoped>

</style>

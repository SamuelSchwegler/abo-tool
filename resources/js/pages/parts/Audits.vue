<template>
    <Box v-if="can('see audits') && audits.length > 0" title="Verlauf">
        <ul role="list" class="divide-y divide-gray-200">
            <li v-for="audit in audits" :key="audit.id" class="py-4">
                <div class="flex space-x-3">
                    <div class="flex-1 space-y-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium">{{ audit.user.email }}</h3>
                            <p class="text-sm text-gray-500"><span class="text-gray-900">{{ audit.event }}</span> am {{ audit.created_at['d.m.Y H:i'] }}</p>
                        </div>
                        <p class="text-sm text-gray-500">
                            <span>{{audit.old_values}} >> {{audit.new_values}}</span>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </Box>
</template>

<script>
import Box from "../../components/Box.vue";

export default {
    name: "Audits",
    components: {Box},
    props: {
        audits: {
            type: Array,
            default: () => []
        }
    }
}
</script>

<style scoped>

</style>

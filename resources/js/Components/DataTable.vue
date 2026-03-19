<template>
    <div class="card overflow-hidden">
        <div v-if="$slots.header" class="card-header flex items-center justify-between">
            <slot name="header" />
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                        >
                            {{ col.label }}
                        </th>
                        <th v-if="$slots.actions" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-if="rows.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                    <tr v-for="row in rows" :key="row.id || row[columns[0].key]" class="hover:bg-gray-50 transition-colors">
                        <td v-for="col in columns" :key="col.key" class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="px-6 py-4 text-right whitespace-nowrap">
                            <slot name="actions" :row="row" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="$slots.footer" class="card-header">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    columns: Array,
    rows: Array,
});
</script>

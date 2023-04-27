<script setup>
import { ref, reactive, computed } from "vue";
import { EditOutlined, DeleteOutlined } from '@ant-design/icons-vue'

let props = defineProps({
    token: String,
    domains: Object,
});

const columns = reactive([
    {
        title: "Domain",
        dataIndex: "domain",
        key: "domain",
    },
    {
        title: "Description",
        key: "description",
        dataIndex: "description",
    },
    {
        title: "Active Call",
        dataIndex: "active_call",
        key: "active_call",
    },
    {
        title: "Action",
        key: "action",
        class: "text-center w-1",
    },
]);
</script>
<template>
    This is a fucking dashboard!

    <p>token {{ token }}</p>
    <div>
        <p>Domains</p>
    </div>

    <a-table :columns="columns" :data-source="domains">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'action'">
                <div class="flex items-center justify-end space-x-3">
                    <a-tooltip placement="top">
                        <template #title>
                            <span>Edit</span>
                        </template>
                        <a-button shape="circle">
                            <template #icon>
                                <edit-outlined />
                            </template>
                        </a-button>
                    </a-tooltip>

                    <a-tooltip placement="top">
                        <template #title>
                            <span>Remove</span>
                        </template>
                        <a-button shape="circle">
                            <template #icon>
                                <delete-outlined />
                            </template>
                        </a-button>
                    </a-tooltip>
                </div>
            </template>
        </template>
        ></a-table
    >
</template>

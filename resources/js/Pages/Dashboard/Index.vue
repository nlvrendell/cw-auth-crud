<script>
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: AuthLayout,
};
</script>
<script setup>
import { router } from "@inertiajs/vue3";
import { reactive, computed, ref, watch } from "vue";
import { EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import debounce from "lodash/debounce";

let props = defineProps({
    token: String,
    domains: Object,
    filters: Object,
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

const pagination = computed(() => ({
    total: props.domains.count.total,
    current: props.filters?.current,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: false,
}));

let search = ref(props.filters?.search);
const handleTableChange = (event) => {
    console.log({
        event: event,
        pagination: pagination,
        filters: props.filters,
    });
    router.get(
        window.location.pathname,
        {
            search: search.value,
            page: event.current,
        },
        {
            replace: true,
            preserveState: true,
        }
    );
};

watch(
    search,
    debounce(function (value) {
        pagination.value.current = 1;
        handleTableChange(pagination.value);
    }, 500)
);
</script>
<template>
    <div>
        <div class="mb-4">
            <h1 class="text-xl font-semibold mb-4">Domains</h1>
            <div class="flex justify-between mb-4">
                <div class="flex justify-end space-x-3">
                    <a-input-search
                        v-model:value="search"
                        placeholder="Search"
                        type="text"
                        allowClear
                    />
                </div>
            </div>
        </div>
        <a-table
            :columns="columns"
            :data-source="domains.data"
            :pagination="pagination"
            @change="handleTableChange"
        >
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
    </div>
</template>

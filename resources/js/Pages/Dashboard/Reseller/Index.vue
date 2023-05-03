<script>
import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    layout: AuthLayout,
};
</script>
<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { reactive, computed, ref, watch, createVNode } from "vue";
import {
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
    PlusOutlined,
} from "@ant-design/icons-vue";
import debounce from "lodash/debounce";
import { notification, Modal } from "ant-design-vue";

let props = defineProps({
    resellers: Object,
    filters: Object,
});

const columns = reactive([
    {
        title: "Territory",
        dataIndex: "territory",
        key: "territory",
    },
    {
        title: "Description",
        key: "description",
        dataIndex: "description",
    },
    {
        title: "Domains ",
        key: "domains",
        dataIndex: "domains",
    },
    {
        title: "Status ",
        key: "entry_status",
        dataIndex: "entry_status",
    },
    {
        title: "Action",
        key: "action",
        class: "text-center w-1",
    },
]);

const pagination = computed(() => ({
    total: props.filters?.search ? 0 : parseInt(props.resellers?.count?.total),
    current: props.filters?.current,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: false,
}));

let search = ref(props.filters?.search);
const handleTableChange = (event) => {
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

// Modal - create and update users
const visible = ref(false);

const form = useForm({
    id: null,
    territory: null,
    description: null,
});

const openModal = (data = null) => {
    if (data) {
        form.id = data.territory_id;
        form.territory = data.territory;
        form.description = data.description;
    }
    visible.value = true;
};

const handleCancel = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const handleSubmit = () => {
    if (!form.id) {
        form.post(route("resellers.store"), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                visible.value = false;
                notification["success"]({
                    message: "Reseller Successfully Created.",
                });
            },
        });
    } else {
        form.put(route("resellers.update", [form.id]), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                visible.value = false;
                notification["success"]({
                    message: "Reseller Successfully Updated.",
                });
            },
        });
    }
};

const handleRemove = (record) => {
    Modal.confirm({
        title: `Are you sure you want to delete ${record.territory}?`,
        icon: createVNode(ExclamationCircleOutlined),
        content: "Please make sure you understand this action",
        okText: "Confirm",
        async onOk() {
            try {
                // loading
                return await new Promise((resolve) => {
                    setTimeout(() => {
                        form.delete(
                            route("resellers.destroy", [record.territory]),
                            {
                                onSuccess: () => {
                                    search.value = ""; // refresh the page
                                    notification["success"]({
                                        message: `Reseller successfully removed.`,
                                    });
                                    resolve(); // stop the promise async
                                },
                                onFinish: () => {
                                    resolve(); // stop the promise async
                                },
                            }
                        );
                    }, 300);
                });
            } catch {
                return console.log("Oops errors!");
            }
        },
    });
};
</script>
<template>
    <div class="mb-4">
        <h1 class="text-xl font-semibold mb-4">Resellers</h1>
        <div class="flex justify-between mb-4">
            <div class="flex justify-end space-x-3">
                <a-input-search
                    v-model:value="search"
                    placeholder="Search"
                    type="text"
                    allowClear
                />
            </div>
            <a-button
                type="primary"
                class="flex justify-center"
                @click="openModal(null)"
            >
                <plus-outlined class="mt-0.5" /> Add Reseller</a-button
            >
        </div>
    </div>
    <!-- Table -->
    <a-table
        :columns="columns"
        :data-source="resellers.data"
        :pagination="pagination"
        @change="handleTableChange"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'entry_status'">
                {{
                    record.entry_status.charAt(0).toUpperCase() +
                    record.entry_status.slice(1)
                }}</template
            >

            <template v-if="column.key === 'action'">
                <div class="flex items-center justify-end space-x-3">
                    <a-tooltip placement="top">
                        <template #title>
                            <span>Edit</span>
                        </template>
                        <a-button shape="circle" @click="openModal(record)">
                            <template #icon>
                                <edit-outlined />
                            </template>
                        </a-button>
                    </a-tooltip>
                    <a-tooltip placement="top">
                        <template #title>
                            <span>Remove</span>
                        </template>
                        <a-button shape="circle" @click="handleRemove(record)">
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

    <!-- Modals -->
    <a-modal
        :title="form.id ? 'Edit Reseller' : 'Create Reseller'"
        :visible="visible"
        :ok-text="form.id ? 'Update' : 'Create'"
        :confirm-loading="form.processing"
        @ok="handleSubmit"
        @cancel="handleCancel"
    >
        <a-form :label-col="{ span: 8 }" :wrapper-col="{ span: 16 }">
            <p
                class="text-red-600 justify-center"
                v-if="form.errors.server"
                style="color: red"
            >
                Server Error: {{ form.errors.server }}
            </p>
            <a-form-item
                label="Territory"
                required
                :validate-status="form.errors.territory ? 'error' : null"
                :help="form.errors.territory"
                name="territory"
            >
                <a-input type="text" v-model:value="form.territory" />
            </a-form-item>

            <a-form-item
                label="Description"
                required
                :validate-status="form.errors.description ? 'error' : null"
                :help="form.errors.description"
                name="description"
            >
                <a-input type="text" v-model:value="form.description" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

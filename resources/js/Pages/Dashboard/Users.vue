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
} from "@ant-design/icons-vue";
import debounce from "lodash/debounce";
import { notification, Modal } from "ant-design-vue";

let props = defineProps({
    token: String,
    users: Object,
    filters: Object,
});

const columns = reactive([
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Domain",
        key: "domain",
        dataIndex: "domain",
    },
    {
        title: "Extension",
        key: "extension",
        dataIndex: "extension",
    },
    {
        title: "Department",
        dataIndex: "department",
        key: "department",
    },
    {
        title: "Site",
        dataIndex: "site",
        key: "site",
    },
    {
        title: "Scope",
        dataIndex: "scope",
        key: "scope",
    },
    {
        title: "Email",
        dataIndex: "email",
        key: "email",
    },
    {
        title: "Action",
        key: "action",
        class: "text-center w-1",
    },
]);

const pagination = computed(() => ({
    total: props.filters?.search ? 0 : parseInt(props.users?.count?.total),
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

// Modal - create and update users
const visible = ref(false);

const form = useForm({
    id: null,
    user: null,
    domain: "",
    first_name: null,
    last_name: null,
    email: null,
});

const openModal = (data = null) => {
    visible.value = true;
};

const handleCancel = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const handleSubmit = () => {
    if (!form.id) {
        form.post(route("domain.users.store"), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                visible.value = false;
                notification["success"]({
                    message: "User Successfully Created.",
                });
            },
        });
    } else {
        console.log("should trigger the edit!");
        form.put(route("domain.update", [form.id]), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                visible.value = false;
                notification["success"]({
                    message: "Domain Successfully Updated.",
                });
            },
        });
    }
};

const handleRemove = () => {};
</script>
<template>
    <div class="mb-4">
        <h1 class="text-xl font-semibold mb-4">Users</h1>
        <div class="flex justify-between mb-4">
            <div class="flex justify-end space-x-3">
                <a-input-search
                    v-model:value="search"
                    placeholder="Search"
                    type="text"
                    allowClear
                />
            </div>
            <a-button type="primary" @click="openModal">Add User</a-button>
        </div>
    </div>
    <a-table
        :columns="columns"
        :data-source="users.data"
        :pagination="pagination"
        @change="handleTableChange"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'name'">
                {{ record.first_name + " " + record.last_name }}</template
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
        :title="form.id ? 'Edit Domain' : 'Create Domain'"
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
                label="Domain"
                required
                :validate-status="form.errors.domain ? 'error' : null"
                :help="form.errors.domain"
                name="domain"
            >
                <a-input type="text" v-model:value="form.domain" />
            </a-form-item>
            <a-form-item
                label="First Name"
                required
                :validate-status="form.errors.first_name ? 'error' : null"
                :help="form.errors.first_name"
                name="first_name"
            >
                <a-input type="text" v-model:value="form.first_name" />
            </a-form-item>

            <a-form-item
                label="Last Name"
                required
                :validate-status="form.errors.last_name ? 'error' : null"
                :help="form.errors.last_name"
                name="last_name"
            >
                <a-input type="text" v-model:value="form.last_name" />
            </a-form-item>

            <a-form-item
                label="Email Address"
                required
                :validate-status="form.errors.email ? 'error' : null"
                :help="form.errors.email"
                name="email"
            >
                <a-input type="text" v-model:value="form.email" />
            </a-form-item>

            <a-form-item
                label="User"
                :validate-status="form.errors.user ? 'error' : null"
                :help="form.errors.user"
                name="user"
            >
                <a-input type="text" v-model:value="form.user" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

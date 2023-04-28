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
    SettingOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import debounce from "lodash/debounce";
import { notification, Modal } from "ant-design-vue";

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
    total: props.filters?.search ? 0 : props.domains?.count?.total,
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
    name: null,
    territory: null,
    description: null,
    call_limit: null,
    call_limit_ext: null,
    max_user: null,
    max_call_queue: null,
    max_aa: null,
    max_conference: null,
    max_department: null,
    max_site: null,
    max_device: null,
});

const openModal = (data = null) => {
    console.log(data);
    if (data?.domain) {
        // Set the current data on domain
        form.id = data.domain;
        form.name = data.domain;
        form.territory = data.territory;
        form.description = data.description;
        form.call_limit = data.call_limit;
        form.call_limit_ext = data.call_limit_ext;
        form.max_user = data.max_user;
        form.max_call_queue = data.max_call_queue;
        form.max_aa = data.max_aa;
        form.max_conference = data.max_conference;
        form.max_department = data.max_department;
        form.max_site = data.max_site;
        form.max_device = data.max_device;
    }
    visible.value = true;
};

const handleCancel = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const handleSubmit = () => {
    console.log(form);
    if (!form.id) {
        form.post(route("domain.store"), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                visible.value = false;
                notification["success"]({
                    message: "Domain Successfully Created.",
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

const handleRemove = (record) => {
    Modal.confirm({
        title: `Are you sure you want to delete ${record.domain}?`,
        icon: createVNode(ExclamationCircleOutlined),
        content: "Please make sure you understand this action",
        okText: "Confirm",
        async onOk() {
            try {
                // loading
                return await new Promise((resolve) => {
                    setTimeout(() => {
                        form.delete(route("domain.destroy", [record.domain]), {
                            onSuccess: () => {
                                console.log("at success");
                                search.value = ""; // refresh the page
                                notification["success"]({
                                    message: `Domain successfully removed.`,
                                });
                                resolve(); // stop the promise async
                            },
                            onFinish: () => {
                                console.log("Request finished");
                                resolve(); // stop the promise async
                            },
                        });
                    }, 300);
                });
            } catch {
                return console.log("Oops errors!");
            }
        },
    });
};

const goToUsers = (domain) => {
    router.get(route('domain.users', [domain]));
}
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
                <a-button type="primary" @click="openModal"
                    >Add Domain</a-button
                >
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
                                <span>Manage</span>
                            </template>
                            <!-- <a-button shape="circle" @click="openModal(record)">
                                <template #icon>
                                    <setting-outlined />
                                </template>
                            </a-button> -->

                            <a-dropdown :trigger="['click']" placement="left">
                                <a-button shape="circle">
                                    <template #icon>
                                        <setting-outlined />
                                    </template>
                                </a-button>
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item>
                                            <a href="javascript:;">Details</a>
                                        </a-menu-item>
                                        <a-menu-item>
                                            <a href="javascript:;" @click="goToUsers(record.domain)">User</a>
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </a-tooltip>
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
                            <a-button
                                shape="circle"
                                @click="handleRemove(record)"
                            >
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
                label="Name"
                required
                :validate-status="form.errors.name ? 'error' : null"
                :help="form.errors.name"
                name="first_name"
            >
                <a-input type="text" v-model:value="form.name" />
            </a-form-item>

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
                :validate-status="form.errors.description ? 'error' : null"
                :help="form.errors.description"
                name="description"
            >
                <a-input type="text" v-model:value="form.description" />
            </a-form-item>

            <a-form-item
                label="Call Limit"
                :validate-status="form.errors.call_limit ? 'error' : null"
                :help="form.errors.call_limit"
                name="call_limit"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.call_limit"
                />
            </a-form-item>

            <a-form-item
                label="External Call Limit"
                :validate-status="form.errors.call_limit_ext ? 'error' : null"
                :help="form.errors.call_limit_ext"
                name="call_limit_ext"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.call_limit_ext"
                />
            </a-form-item>

            <a-form-item
                label="Max User"
                :validate-status="form.errors.max_user ? 'error' : null"
                :help="form.errors.max_user"
                name="max_user"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_user"
                />
            </a-form-item>

            <a-form-item
                label="Max Call Queue"
                :validate-status="form.errors.max_call_queue ? 'error' : null"
                :help="form.errors.max_call_queue"
                name="max_call_queue"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_call_queue"
                />
            </a-form-item>

            <a-form-item
                label="Max Auto Attendant"
                :validate-status="form.errors.max_aa ? 'error' : null"
                :help="form.errors.max_aa"
                name="max_aa"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_aa"
                />
            </a-form-item>

            <a-form-item
                label="Max Conference Bridge"
                :validate-status="form.errors.max_conference ? 'error' : null"
                :help="form.errors.max_conference"
                name="max_conference"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_conference"
                />
            </a-form-item>

            <a-form-item
                label="Max Department"
                :validate-status="form.errors.max_department ? 'error' : null"
                :help="form.errors.max_department"
                name="max_department"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_department"
                />
            </a-form-item>

            <a-form-item
                label="Max Site"
                :validate-status="form.errors.max_site ? 'error' : null"
                :help="form.errors.max_site"
                name="max_site"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_site"
                />
            </a-form-item>
            <a-form-item
                label="Max Device"
                :validate-status="form.errors.max_device ? 'error' : null"
                :help="form.errors.max_device"
                name="max_device"
            >
                <a-input-number
                    :min="0"
                    class="w-full"
                    v-model:value="form.max_device"
                />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

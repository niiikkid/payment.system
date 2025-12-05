<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/ui/Pagination.vue';
import ModalDialog from '@/components/ui/modal/ModalDialog.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';

type RoleOption = { value: string; label: string };

type UserItem = {
    id: number;
    name: string;
    email: string;
    roles: string[];
    email_verified_at?: string | null;
    created_at?: string | null;
    can_manage_role: boolean;
    can_be_impersonated: boolean;
};

type PaginationLink = { url: string | null; label: string; active: boolean };
type PaginatedUsers = { data: UserItem[]; links: PaginationLink[] };

const page = usePage();
const users = computed(() => page.props.users as PaginatedUsers);
const roleOptions = computed(() => page.props.roleOptions as RoleOption[]);

const showCreate = ref(false);
const showEdit = ref(false);
const selectedUser = ref<UserItem | null>(null);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
});

const editForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
});

const impersonateForm = useForm({});

function openCreate() {
    createForm.reset();
    createForm.role = 'user';
    showCreate.value = true;
}

function submitCreate() {
    createForm.post('/admin/users', {
        preserveScroll: true,
        onSuccess: () => {
            showCreate.value = false;
            createForm.reset();
        },
        onError: () => {
            showCreate.value = true;
        },
    });
}

function openEdit(user: UserItem) {
    selectedUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.password_confirmation = '';
    editForm.role = user.roles[0] ?? 'user';
    editForm.clearErrors();
    showEdit.value = true;
}

function submitEdit() {
    if (!selectedUser.value) return;
    const allowRoleChange = selectedUser.value.can_manage_role;
    editForm.transform((data) => {
        if (allowRoleChange) {
            return data;
        }
        const { role, ...rest } = data;
        return { ...rest, role: null };
    });
    editForm.patch(`/admin/users/${selectedUser.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEdit.value = false;
            editForm.password = '';
            editForm.password_confirmation = '';
        },
        onError: () => {
            showEdit.value = true;
        },
        onFinish: () => {
            editForm.transform((data) => data);
        },
    });
}

function closeEdit() {
    showEdit.value = false;
    editForm.clearErrors();
}

function closeCreate() {
    showCreate.value = false;
    createForm.clearErrors();
}

function impersonate(user: UserItem) {
    impersonateForm.post(`/admin/impersonate/${user.id}`, { preserveScroll: true });
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Пользователи', href: '/admin/users' }]" :title="'Пользователи'">
        <template #header-actions>
            <button class="btn btn-primary btn-sm" @click="openCreate">Создать пользователя</button>
        </template>

        <div class="grid gap-6">
            <div class="lg:card lg:bg-base-100 lg:shadow">
                <div class="lg:card-body">
                    <h2 class="hidden lg:block card-title">Список пользователей</h2>
                    <h2 class="lg:hidden card-title mb-3">Список пользователей</h2>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Роли</th>
                                    <th>Создан</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="font-mono text-xs">#{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="role in user.roles" :key="role" class="badge badge-ghost badge-sm uppercase">{{ role }}</span>
                                        </div>
                                    </td>
                                    <td class="text-xs font-mono">
                                        <DateTimeFormat :value="user.created_at || ''" />
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button class="btn btn-xs" @click="openEdit(user)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                                </svg>
                                            </button>
                                            <button
                                                v-if="user.can_be_impersonated"
                                                class="btn btn-xs btn-warning"
                                                :disabled="impersonateForm.processing"
                                                @click="impersonate(user)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!users.data.length">
                                    <td colspan="6" class="text-center text-sm opacity-70 py-6">Пользователи не найдены</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="lg:hidden space-y-3">
                        <div v-for="user in users.data" :key="user.id" class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold">{{ user.name }}</div>
                                    <button class="btn btn-xs" @click="openEdit(user)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-sm opacity-80">{{ user.email }}</div>
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="role in user.roles" :key="role" class="badge badge-ghost badge-sm uppercase">{{ role }}</span>
                                </div>
                                <div class="text-xs opacity-70">
                                    <DateTimeFormat :value="user.created_at || ''" />
                                </div>
                                <button
                                    v-if="user.can_be_impersonated"
                                    class="btn btn-warning btn-sm w-full"
                                    :disabled="impersonateForm.processing"
                                    @click="impersonate(user)"
                                >
                                    Войти как пользователь
                                </button>
                            </div>
                        </div>
                        <div v-if="!users.data.length" class="text-center text-sm opacity-70 py-6">
                            Пользователи не найдены
                        </div>
                    </div>

                    <Pagination :links="users.links" />
                </div>
            </div>
        </div>

        <ModalDialog v-model="showCreate" title="Создать пользователя">
            <form class="grid gap-4" @submit.prevent="submitCreate">
                <div class="grid gap-2">
                    <label class="label text-sm" for="create-name">Имя</label>
                    <input id="create-name" v-model="createForm.name" type="text" class="input input-bordered w-full" placeholder="Имя" />
                    <p v-if="createForm.errors.name" class="text-error text-sm">{{ createForm.errors.name }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="create-email">Email</label>
                    <input id="create-email" v-model="createForm.email" type="email" class="input input-bordered w-full" placeholder="email@example.com" />
                    <p v-if="createForm.errors.email" class="text-error text-sm">{{ createForm.errors.email }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="create-password">Пароль</label>
                    <input id="create-password" v-model="createForm.password" type="password" class="input input-bordered w-full" placeholder="Пароль" />
                    <p v-if="createForm.errors.password" class="text-error text-sm">{{ createForm.errors.password }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="create-password-confirmation">Подтверждение пароля</label>
                    <input id="create-password-confirmation" v-model="createForm.password_confirmation" type="password" class="input input-bordered w-full" placeholder="Подтверждение пароля" />
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="create-role">Роль</label>
                    <select id="create-role" v-model="createForm.role" class="select select-bordered w-full">
                        <option v-for="role in roleOptions" :key="role.value" :value="role.value">{{ role.label }}</option>
                    </select>
                    <p v-if="createForm.errors.role" class="text-error text-sm">{{ createForm.errors.role }}</p>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="closeCreate">Отмена</button>
                    <button type="submit" class="btn btn-primary" :disabled="createForm.processing">
                        <span v-if="createForm.processing" class="loading loading-spinner loading-sm mr-2" />
                        Создать
                    </button>
                </div>
            </form>
        </ModalDialog>

        <ModalDialog v-model="showEdit" title="Редактировать пользователя" @close="closeEdit">
            <form v-if="selectedUser" class="grid gap-4" @submit.prevent="submitEdit">
                <div class="grid gap-2">
                    <label class="label text-sm" for="edit-name">Имя</label>
                    <input id="edit-name" v-model="editForm.name" type="text" class="input input-bordered w-full" />
                    <p v-if="editForm.errors.name" class="text-error text-sm">{{ editForm.errors.name }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="edit-email">Email</label>
                    <input id="edit-email" v-model="editForm.email" type="email" class="input input-bordered w-full" />
                    <p v-if="editForm.errors.email" class="text-error text-sm">{{ editForm.errors.email }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="edit-password">Новый пароль</label>
                    <input id="edit-password" v-model="editForm.password" type="password" class="input input-bordered w-full" placeholder="Оставьте пустым, чтобы не менять" />
                    <p v-if="editForm.errors.password" class="text-error text-sm">{{ editForm.errors.password }}</p>
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="edit-password-confirmation">Подтверждение</label>
                    <input id="edit-password-confirmation" v-model="editForm.password_confirmation" type="password" class="input input-bordered w-full" placeholder="Оставьте пустым, чтобы не менять" />
                </div>

                <div class="grid gap-2">
                    <label class="label text-sm" for="edit-role">Роль</label>
                    <select
                        id="edit-role"
                        v-model="editForm.role"
                        class="select select-bordered w-full"
                        :disabled="!selectedUser.can_manage_role"
                    >
                        <option v-for="role in roleOptions" :key="role.value" :value="role.value">{{ role.label }}</option>
                    </select>
                    <p v-if="editForm.errors.role" class="text-error text-sm">{{ editForm.errors.role }}</p>
                    <p v-if="!selectedUser.can_manage_role" class="text-xs opacity-70">Нельзя менять роль своей учетной записи</p>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="closeEdit">Отмена</button>
                    <button type="submit" class="btn btn-primary" :disabled="editForm.processing">
                        <span v-if="editForm.processing" class="loading loading-spinner loading-sm mr-2" />
                        Сохранить
                    </button>
                </div>
            </form>
        </ModalDialog>
    </AppLayout>
</template>



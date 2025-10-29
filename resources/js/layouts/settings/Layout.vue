<script setup lang="ts">
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    { title: 'Профиль', href: editProfile() },
    { title: 'Пароль', href: editPassword() },
    { title: 'Двухфакторная защита', href: show() },
    { title: 'Внешний вид', href: editAppearance() },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="bg-base-200 px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Настройки</h1>
            <p class="text-base-content/70">Управляйте профилем и настройками аккаунта</p>
        </div>

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        :href="item.href"
                        class="btn btn-ghost justify-start w-full"
                        :class="{ 'bg-base-200': urlIsActive(item.href, currentPath) }"
                    >
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <div class="divider my-6 lg:hidden"></div>

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <div class="rounded-box bg-base-100 shadow-sm p-4 lg:p-6">
                        <slot />
                    </div>
                </section>
            </div>
        </div>
    </div>
    
</template>

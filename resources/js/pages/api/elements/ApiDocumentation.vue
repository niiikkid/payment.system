<script setup lang="ts">
interface Props {
    apiBase: string;
}

defineProps<Props>();
</script>

<template>
    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-2">
            <h3 class="card-title">Документация</h3>
            <p class="text-sm">Все запросы должны содержать заголовок <code>X-Api-Key</code> со значением вашего токена. Базовый URL: <code>{{ apiBase }}</code>.</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><b>POST /invoices</b>: создать инвойс.</li>
                <li><b>GET /invoices/{id}</b>: получить инвойс.</li>
                <li><b>GET /invoices/{id}/status</b>: получить краткий статус.</li>
                <li><b>GET /invoices/{id}/public</b>: публичные данные (адрес и т.п.).</li>
                <li><b>GET /invoices/{id}/qr</b>: PNG‑QR адреса.</li>
                <li><b>POST /invoices/{id}/cancel</b>: отменить активный инвойс.</li>
            </ul>
        </div>
    </div>
</template>


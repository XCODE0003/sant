<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    statusTimeline: {
        type: Array,
        default: () => [],
    },
    isCancelled: {
        type: Boolean,
        default: false,
    },
    trackingUrl: {
        type: String,
        default: '',
    },
});

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: `Заказ ${props.order?.number ?? ''}` },
];

const page = usePage();

const order = computed(() => props.order ?? {});
const items = computed(() => order.value.items ?? []);
const statusTimeline = computed(() => props.statusTimeline ?? []);
const isCancelled = computed(() => props.isCancelled);
const flash = computed(() => page.props.flash ?? {});

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const dateFormatter = new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

const formatCurrency = (value) => {
    const numeric = Number.parseFloat(value ?? 0);
    return priceFormatter.format(Number.isFinite(numeric) ? numeric : 0);
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return dateFormatter.format(date);
};

const statusBadgeClass = computed(() => {
    if (isCancelled.value) {
        return 'bg-red-100 text-red-700 border border-red-200';
    }

    switch (order.value.status) {
        case 'pending':
            return 'bg-amber-100 text-amber-700 border border-amber-200';
        case 'confirmed':
            return 'bg-blue-100 text-blue-700 border border-blue-200';
        case 'shipped':
            return 'bg-indigo-100 text-indigo-700 border border-indigo-200';
        case 'delivered':
            return 'bg-green-100 text-green-700 border border-green-200';
        default:
            return 'bg-gray-100 text-gray-700 border border-gray-200';
    }
});

const resolvedTrackingUrl = computed(() => {
    if (props.trackingUrl) {
        return props.trackingUrl;
    }

    if (typeof window !== 'undefined') {
        return `${window.location.origin}${page.url}`;
    }

    return page.url ?? '';
});

const copySuccess = ref(false);
const copyError = ref(false);

const copyLink = async () => {
    copySuccess.value = false;
    copyError.value = false;

    try {
        if (!navigator?.clipboard) {
            throw new Error('Clipboard unavailable');
        }

        await navigator.clipboard.writeText(resolvedTrackingUrl.value);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 3000);
    } catch (error) {
        console.error('ORDER::LINK_COPY_FAILED', error);
        copyError.value = true;
        setTimeout(() => {
            copyError.value = false;
        }, 3000);
    }
};

const deliveryAddress = computed(() => {
    const delivery = order.value.delivery ?? {};

    if (!delivery) {
        return '—';
    }

    const segments = [delivery.city, delivery.street, delivery.house]
        .filter(Boolean)
        .join(', ');

    const details = [
        delivery.apartment && `кв. ${delivery.apartment}`,
        delivery.entrance && `подъезд ${delivery.entrance}`,
    ]
        .filter(Boolean)
        .join(', ');

    return [segments, details].filter(Boolean).join(' • ') || '—';
});
</script>

<template>
    <AppLayout :title="`Заказ ${order.number ?? ''} - Акватэрия`">
        <Breadcrumbs :items="breadcrumbs" />

        <div v-if="flash.success" class="bg-green-50 border-b border-green-200">
            <div class="container px-4 py-4 mx-auto">
                <p class="font-semibold text-green-800">{{ flash.success }}</p>
            </div>
        </div>

        <div class="bg-white border-b">
            <div class="container px-4 py-4 md:py-6 mx-auto">
                <div class="lg:flex-row lg:items-center lg:justify-between flex flex-col gap-3 md:gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            Заказ № {{ order.number }}
                        </h1>
                        <p class="mt-1 md:mt-2 text-sm md:text-base text-gray-500">
                            Отслеживание статуса и деталей заказа
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 md:gap-3 items-center">
                        <span :class="['px-3 md:px-4 py-2 rounded-full text-xs md:text-sm font-medium inline-flex items-center gap-2', statusBadgeClass]">
                            <i class="fas fa-circle text-xs"></i>
                            {{ order.status_label }}
                        </span>
                        <button
                            type="button"
                            class="hover:bg-gray-50 active:bg-gray-100 inline-flex gap-2 items-center px-3 md:px-4 py-2 text-xs md:text-sm font-semibold text-gray-700 rounded-lg border border-gray-200 transition-colors touch-manipulation"
                            @click="copyLink"
                        >
                            <i class="fas fa-link"></i>
                            <span class="hidden sm:inline">Скопировать ссылку</span>
                            <span class="sm:hidden">Ссылка</span>
                        </button>
                    </div>
                </div>
                <div v-if="copySuccess" class="container px-4 mx-auto">
                    <div class="inline-flex gap-2 items-center mt-2 text-sm text-green-600">
                        <i class="fas fa-check-circle"></i>
                        Ссылка скопирована в буфер обмена
                    </div>
                </div>
                <div v-if="copyError" class="container px-4 mx-auto">
                    <div class="inline-flex gap-2 items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle"></i>
                        Не удалось скопировать ссылку, попробуйте вручную
                    </div>
                </div>
            </div>
        </div>

        <div class="container px-4 py-6 md:py-10 mx-auto space-y-6 md:space-y-10">
            <div class="lg:grid-cols-3 grid grid-cols-1 gap-6 md:gap-8">
                <section class="lg:col-span-2 space-y-6 md:space-y-8">
                    <div class="p-5 md:p-8 bg-white rounded-xl md:rounded-2xl shadow-lg">
                        <h2 class="mb-4 md:mb-6 text-lg md:text-xl font-semibold text-gray-900">Этапы доставки</h2>
                        <div class="flex flex-col gap-4 md:gap-6">
                            <div v-for="(step, index) in statusTimeline" :key="step.value" class="flex items-start gap-3">
                                <div
                                    :class="[
                                        'w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-colors flex-shrink-0',
                                        step.completed ? 'bg-primary text-white border-primary' : 'bg-white text-gray-500 border-gray-300',
                                        step.current && !isCancelled ? 'ring-2 ring-offset-2 ring-primary/40' : '',
                                    ]"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm md:text-base">{{ step.label }}</p>
                                    <p class="text-xs md:text-sm text-gray-500">
                                        <span v-if="step.current && !isCancelled">Текущий этап</span>
                                        <span v-else-if="step.completed">Этап завершён</span>
                                        <span v-else>Ожидается</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-if="isCancelled" class="p-3 md:p-4 mt-4 md:mt-6 text-xs md:text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                            Заказ отменён. Для уточнения подробностей свяжитесь с менеджером магазина.
                        </div>
                    </div>

                    <div class="p-5 md:p-8 space-y-4 md:space-y-6 bg-white rounded-xl md:rounded-2xl shadow-lg">
                        <div class="flex flex-col gap-3 md:gap-4">
                            <div>
                                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Информация о заказе</h2>
                                <p class="text-xs md:text-sm text-gray-500 break-all">UUID: {{ order.uuid }}</p>
                            </div>
                            <div class="space-y-1 text-xs md:text-sm text-gray-600">
                                <div><span class="font-medium text-gray-800">Дата создания:</span> {{ formatDate(order.created_at) }}</div>
                                <div><span class="font-medium text-gray-800">Обновлён:</span> {{ formatDate(order.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                            <div class="p-4 rounded-lg md:rounded-xl border border-gray-200">
                                <h3 class="mb-2 md:mb-3 text-xs md:text-sm font-semibold tracking-wide text-gray-700 uppercase">Получатель</h3>
                                <p class="font-medium text-gray-900 text-sm md:text-base break-words">{{ order.customer?.first_name }} {{ order.customer?.last_name }}</p>
                                <p class="mt-2 text-xs md:text-sm text-gray-600 break-all">Телефон: {{ order.customer?.phone }}</p>
                                <p class="text-xs md:text-sm text-gray-600 break-all">Email: {{ order.customer?.email }}</p>
                            </div>
                            <div class="p-4 rounded-lg md:rounded-xl border border-gray-200">
                                <h3 class="mb-2 md:mb-3 text-xs md:text-sm font-semibold tracking-wide text-gray-700 uppercase">Доставка</h3>
                                <p class="font-medium text-gray-900 text-sm md:text-base">{{ order.delivery_method_label }}</p>
                                <p class="mt-2 text-xs md:text-sm text-gray-600 break-words">{{ deliveryAddress }}</p>
                                <p v-if="order.delivery?.comment" class="mt-2 text-xs md:text-sm text-gray-500 break-words">
                                    Комментарий: {{ order.delivery.comment }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg md:rounded-xl border border-gray-200">
                                <h3 class="mb-2 md:mb-3 text-xs md:text-sm font-semibold tracking-wide text-gray-700 uppercase">Оплата</h3>
                                <p class="font-medium text-gray-900 text-sm md:text-base">{{ order.payment_method_label }}</p>
                                <p class="mt-1 text-xs md:text-sm text-gray-500">
                                    Сумма к оплате: {{ formatCurrency(order.total_price) }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg md:rounded-xl border border-gray-200">
                                <h3 class="mb-2 md:mb-3 text-xs md:text-sm font-semibold tracking-wide text-gray-700 uppercase">Связь с магазином</h3>
                                <p class="text-xs md:text-sm text-gray-600">Для изменения заказа свяжитесь с менеджером по телефону:</p>
                                <p class="text-primary mt-2 text-base md:text-lg font-semibold">+7 (351) 000-00-00</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 bg-white rounded-xl md:rounded-2xl shadow-lg">
                        <h2 class="mb-4 md:mb-6 text-lg md:text-xl font-semibold text-gray-900">Состав заказа</h2>
                        <div v-if="items.length" class="space-y-3 md:space-y-4">
                            <article
                                v-for="item in items"
                                :key="item.id ?? `${item.title}-${item.total_price}`"
                                class="flex flex-col sm:flex-row sm:items-center gap-3 md:gap-4 p-3 md:p-4 rounded-lg md:rounded-xl border border-gray-200"
                            >
                                <div class="w-full sm:w-16 md:w-20 h-16 md:h-20 flex overflow-hidden justify-center items-center bg-gray-100 rounded-lg flex-shrink-0">
                                    <img v-if="item.image" :src="item.image" :alt="item.title" class="object-cover w-full h-full" />
                                    <i v-else class="fas fa-box text-xl md:text-2xl text-gray-300"></i>
                                </div>
                                <div class="flex-1 space-y-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm md:text-base font-semibold text-gray-900 break-words">{{ item.title }}</h3>
                                            <Link
                                                v-if="item.slug"
                                                :href="`/products/${item.slug}`"
                                                class="text-primary hover:underline text-xs md:text-sm inline-block mt-1"
                                            >
                                                Открыть карточку товара
                                            </Link>
                                        </div>
                                        <div class="text-left sm:text-right flex-shrink-0">
                                            <p class="text-primary text-base md:text-lg font-bold">{{ formatCurrency(item.total_price) }}</p>
                                            <p class="text-xs md:text-sm text-gray-500">{{ formatCurrency(item.unit_price) }} за шт.</p>
                                        </div>
                                    </div>
                                    <p class="text-xs md:text-sm text-gray-600">Количество: {{ item.quantity }} шт.</p>
                                </div>
                            </article>
                        </div>
                        <div v-else class="text-xs md:text-sm text-gray-500">
                            Товары не найдены.
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4 pt-3 md:pt-4 mt-4 md:mt-6 border-t">
                            <div class="text-xs md:text-sm text-gray-600">
                                Позиций: <span class="font-semibold text-gray-900">{{ order.items_count }}</span>
                            </div>
                            <div class="text-xl md:text-2xl font-bold text-gray-900">
                                Итого: <span class="text-primary">{{ formatCurrency(order.total_price) }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-4 md:space-y-6">
                    <div class="p-5 md:p-6 bg-white rounded-xl md:rounded-2xl shadow-lg">
                        <h2 class="mb-3 md:mb-4 text-base md:text-lg font-semibold text-gray-900">Состояние заказа</h2>
                        <dl class="space-y-2 md:space-y-3 text-xs md:text-sm text-gray-600">
                            <div class="flex justify-between gap-2">
                                <dt>Номер заказа:</dt>
                                <dd class="font-medium text-gray-900 break-all">{{ order.number }}</dd>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt>UUID:</dt>
                                <dd class="font-mono text-xs text-gray-500 break-all">{{ order.uuid }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Способ оплаты:</dt>
                                <dd class="font-medium text-gray-900">{{ order.payment_method_label }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Способ доставки:</dt>
                                <dd class="font-medium text-gray-900">{{ order.delivery_method_label }}</dd>
                            </div>
                            <div v-if="order.delivery?.comment" class="pt-2 text-gray-500 border-t">
                                Комментарий для курьера: {{ order.delivery.comment }}
                            </div>
                        </dl>
                    </div>

                    <div class="p-5 md:p-6 bg-white rounded-xl md:rounded-2xl shadow-lg">
                        <h2 class="mb-3 md:mb-4 text-base md:text-lg font-semibold text-gray-900">Как связаться</h2>
                        <p class="text-xs md:text-sm text-gray-600">
                            Если у вас есть вопросы по заказу, позвоните в магазин или напишите на почту.
                        </p>
                        <div class="mt-3 md:mt-4 space-y-2 md:space-y-3 text-xs md:text-sm text-gray-800">
                            <div class="flex gap-2 items-center">
                                <i class="fas fa-phone text-primary flex-shrink-0"></i>
                                <a href="tel:+73510000000" class="hover:underline font-semibold break-all">+7 (351) 000-00-00</a>
                            </div>
                            <div class="flex gap-2 items-center">
                                <i class="fas fa-envelope text-primary flex-shrink-0"></i>
                                <a href="mailto:info@shopsant.ru" class="hover:underline font-semibold break-all">info@shopsant.ru</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:p-6 text-xs md:text-sm text-blue-700 bg-blue-50 rounded-xl md:rounded-2xl border border-blue-100">
                        <h3 class="mb-2 text-sm md:text-base font-semibold text-blue-900">Поделитесь ссылкой</h3>
                        <p class="mb-3">Вы можете отправить ссылку на эту страницу для отслеживания заказа вашим коллегам или клиенту.</p>
                        <div class="p-2 md:p-3 text-xs text-blue-800 break-all bg-white rounded-lg md:rounded-xl border border-blue-100">
                            {{ resolvedTrackingUrl }}
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>

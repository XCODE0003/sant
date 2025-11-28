<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

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

// Payment logic
const paymentData = computed(() => order.value.payment_data ?? {});
const paymentUrl = computed(() => order.value.payment_url ?? paymentData.value?.PaymentURL ?? null);
const hasPaymentUrl = computed(() => !!paymentUrl.value && order.value.payment_method === 'card' && order.value.status === 'pending');

// Timer logic
const timeRemaining = ref(null);
const timerInterval = ref(null);
const isTimerExpired = ref(false);

const calculateTimeRemaining = () => {
    if (!order.value.created_at || !hasPaymentUrl.value) {
        timeRemaining.value = null;
        return;
    }

    const createdAt = new Date(order.value.created_at);
    const expirationTime = new Date(createdAt.getTime() + 60 * 60 * 1000); // 1 hour
    const now = new Date();
    const diff = expirationTime.getTime() - now.getTime();

    if (diff <= 0) {
        timeRemaining.value = null;
        isTimerExpired.value = true;
        stopTimer();
        return;
    }

    isTimerExpired.value = false;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    timeRemaining.value = {
        hours: hours.toString().padStart(2, '0'),
        minutes: minutes.toString().padStart(2, '0'),
        seconds: seconds.toString().padStart(2, '0'),
        total: diff,
    };
};

const startTimer = () => {
    if (!hasPaymentUrl.value) {
        return;
    }

    calculateTimeRemaining();

    timerInterval.value = setInterval(() => {
        calculateTimeRemaining();
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
    }
};

onMounted(() => {
    if (hasPaymentUrl.value) {
        startTimer();
        if(order.value.status === 'pending') {
            window.location.href = paymentUrl.value;
        }
    }
});

onUnmounted(() => {
    stopTimer();
});

const goToPayment = () => {
    if (paymentUrl.value) {
        window.open(paymentUrl.value, '_blank');
    }
};
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
            <div class="md:py-6 container px-4 py-4 mx-auto">
                <div class="lg:flex-row lg:items-center lg:justify-between md:gap-4 flex flex-col gap-3">
                    <div>
                        <h1 class="md:text-3xl text-2xl font-bold text-gray-900">
                            Заказ № {{ order.number }}
                        </h1>
                        <p class="md:mt-2 md:text-base mt-1 text-sm text-gray-500">
                            Отслеживание статуса и деталей заказа
                        </p>
                    </div>
                    <div class="md:gap-3 flex flex-wrap gap-2 items-center">
                        <span :class="['px-3 md:px-4 py-2 rounded-full text-xs md:text-sm font-medium inline-flex items-center gap-2', statusBadgeClass]">
                            <i class="fas fa-circle text-xs"></i>
                            {{ order.status_label }}
                        </span>
                        <button
                            type="button"
                            class="hover:bg-gray-50 active:bg-gray-100 md:px-4 md:text-sm touch-manipulation inline-flex gap-2 items-center px-3 py-2 text-xs font-semibold text-gray-700 rounded-lg border border-gray-200 transition-colors"
                            @click="copyLink"
                        >
                            <i class="fas fa-link"></i>
                            <span class="sm:inline hidden">Скопировать ссылку</span>
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

        <div class="md:py-10 md:space-y-10 container px-4 py-6 mx-auto space-y-6">
            <div class="lg:grid-cols-3 md:gap-8 grid grid-cols-1 gap-6">
                <section class="lg:col-span-2 md:space-y-8 space-y-6">
                    <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                        <h2 class="md:mb-6 md:text-xl mb-4 text-lg font-semibold text-gray-900">Этапы доставки</h2>
                        <div class="md:gap-6 flex flex-col gap-4">
                            <div v-for="(step, index) in statusTimeline" :key="step.value" class="flex gap-3 items-start">
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
                                    <p class="md:text-base text-sm font-semibold text-gray-800">{{ step.label }}</p>
                                    <p class="md:text-sm text-xs text-gray-500">
                                        <span v-if="step.current && !isCancelled">Текущий этап</span>
                                        <span v-else-if="step.completed">Этап завершён</span>
                                        <span v-else>Ожидается</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-if="isCancelled" class="md:p-4 md:mt-6 md:text-sm p-3 mt-4 text-xs text-red-700 bg-red-50 rounded-xl border border-red-200">
                            Заказ отменён. Для уточнения подробностей свяжитесь с менеджером магазина.
                        </div>
                    </div>

                    <div class="md:p-8 md:space-y-6 md:rounded-2xl p-5 space-y-4 bg-white rounded-xl shadow-lg">
                        <div class="md:gap-4 flex flex-col gap-3">
                            <div>
                                <h2 class="md:text-xl text-lg font-semibold text-gray-900">Информация о заказе</h2>
                                <p class="md:text-sm text-xs text-gray-500 break-all">UUID: {{ order.uuid }}</p>
                            </div>
                            <div class="md:text-sm space-y-1 text-xs text-gray-600">
                                <div><span class="font-medium text-gray-800">Дата создания:</span> {{ formatDate(order.created_at) }}</div>
                                <div><span class="font-medium text-gray-800">Обновлён:</span> {{ formatDate(order.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="sm:grid-cols-2 md:gap-6 grid grid-cols-1 gap-4">
                            <div class="md:rounded-xl p-4 rounded-lg border border-gray-200">
                                <h3 class="md:mb-3 md:text-sm mb-2 text-xs font-semibold tracking-wide text-gray-700 uppercase">Получатель</h3>
                                <p class="md:text-base text-sm font-medium text-gray-900 break-words">{{ order.customer?.first_name }} {{ order.customer?.last_name }}</p>
                                <p class="md:text-sm mt-2 text-xs text-gray-600 break-all">Телефон: {{ order.customer?.phone }}</p>
                                <p class="md:text-sm text-xs text-gray-600 break-all">Email: {{ order.customer?.email }}</p>
                            </div>
                            <div class="md:rounded-xl p-4 rounded-lg border border-gray-200">
                                <h3 class="md:mb-3 md:text-sm mb-2 text-xs font-semibold tracking-wide text-gray-700 uppercase">Доставка</h3>
                                <p class="md:text-base text-sm font-medium text-gray-900">{{ order.delivery_method_label }}</p>
                                <p class="md:text-sm mt-2 text-xs text-gray-600 break-words">{{ deliveryAddress }}</p>
                                <p v-if="order.delivery?.comment" class="md:text-sm mt-2 text-xs text-gray-500 break-words">
                                    Комментарий: {{ order.delivery.comment }}
                                </p>
                            </div>
                            <div class="md:rounded-xl p-4 rounded-lg border border-gray-200">
                                <h3 class="md:mb-3 md:text-sm mb-2 text-xs font-semibold tracking-wide text-gray-700 uppercase">Оплата</h3>
                                <p class="md:text-base text-sm font-medium text-gray-900">{{ order.payment_method_label }}</p>
                                <p class="md:text-sm mt-1 text-xs text-gray-500">
                                    Сумма к оплате: {{ formatCurrency(order.total_price) }}
                                </p>
                            </div>
                            <div class="md:rounded-xl p-4 rounded-lg border border-gray-200">
                                <h3 class="md:mb-3 md:text-sm mb-2 text-xs font-semibold tracking-wide text-gray-700 uppercase">Связь с магазином</h3>
                                <p class="md:text-sm text-xs text-gray-600">Для изменения заказа свяжитесь с менеджером по телефону:</p>
                                <p class="text-primary md:text-lg mt-2 text-base font-semibold">+7 (351) 000-00-00</p>
                            </div>
                        </div>
                    </div>

                    <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                        <h2 class="md:mb-6 md:text-xl mb-4 text-lg font-semibold text-gray-900">Состав заказа</h2>
                        <div v-if="items.length" class="md:space-y-4 space-y-3">
                            <article
                                v-for="item in items"
                                :key="item.id ?? `${item.title}-${item.total_price}`"
                                class="sm:flex-row sm:items-center md:gap-4 md:p-4 md:rounded-xl flex flex-col gap-3 p-3 rounded-lg border border-gray-200"
                            >
                                <div class="sm:w-16 md:w-20 md:h-20 flex overflow-hidden flex-shrink-0 justify-center items-center w-full h-16 bg-gray-100 rounded-lg">
                                    <img v-if="item.image" :src="item.image" :alt="item.title" class="object-cover w-full h-full" />
                                    <i v-else class="fas fa-box md:text-2xl text-xl text-gray-300"></i>
                                </div>
                                <div class="flex-1 space-y-1 min-w-0">
                                    <div class="sm:flex-row sm:items-start sm:justify-between flex flex-col gap-2">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="md:text-base text-sm font-semibold text-gray-900 break-words">{{ item.title }}</h3>
                                            <Link
                                                v-if="item.slug"
                                                :href="`/products/${item.slug}`"
                                                class="text-primary hover:underline md:text-sm inline-block mt-1 text-xs"
                                            >
                                                Открыть карточку товара
                                            </Link>
                                        </div>
                                        <div class="sm:text-right flex-shrink-0 text-left">
                                            <p class="text-primary md:text-lg text-base font-bold">{{ formatCurrency(item.total_price) }}</p>
                                            <p class="md:text-sm text-xs text-gray-500">{{ formatCurrency(item.unit_price) }} за шт.</p>
                                        </div>
                                    </div>
                                    <p class="md:text-sm text-xs text-gray-600">Количество: {{ item.quantity }} шт.</p>
                                </div>
                            </article>
                        </div>
                        <div v-else class="md:text-sm text-xs text-gray-500">
                            Товары не найдены.
                        </div>

                        <div class="sm:flex-row sm:items-center sm:justify-between md:gap-4 md:pt-4 md:mt-6 flex flex-col gap-3 pt-3 mt-4 border-t">
                            <div class="md:text-sm text-xs text-gray-600">
                                Позиций: <span class="font-semibold text-gray-900">{{ order.items_count }}</span>
                            </div>
                            <div class="md:text-2xl text-xl font-bold text-gray-900">
                                Итого: <span class="text-primary">{{ formatCurrency(order.total_price) }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="md:space-y-6 space-y-4">
                    <!-- Payment Block -->
                    <div v-if="hasPaymentUrl" class="md:p-6 md:rounded-2xl p-5 bg-amber-100 to-orange-50 rounded-xl border-2 border-amber-200 shadow-lg">
                        <div class="md:mb-4 flex gap-2 items-center mb-3">
                            <i class="fas fa-clock text-lg text-amber-600"></i>
                            <h2 class="md:text-lg text-base font-semibold text-amber-900">Требуется оплата</h2>
                        </div>
                        <p class="md:text-sm md:mb-4 mb-3 text-xs text-amber-800">
                            Для подтверждения заказа необходимо произвести оплату в течение часа
                        </p>
                        <div v-if="timeRemaining && !isTimerExpired" class="md:mb-5 mb-4">
                            <div class="md:gap-3 md:p-4 flex gap-2 justify-center items-center p-3 bg-white rounded-lg border-2 border-amber-300">
                                <div class="text-center">
                                    <div class="md:text-3xl text-2xl font-bold text-amber-700">{{ timeRemaining.hours }}</div>
                                    <div class="text-xs text-amber-600">часов</div>
                                </div>
                                <div class="md:text-3xl text-2xl font-bold text-amber-400">:</div>
                                <div class="text-center">
                                    <div class="md:text-3xl text-2xl font-bold text-amber-700">{{ timeRemaining.minutes }}</div>
                                    <div class="text-xs text-amber-600">минут</div>
                                </div>
                                <div class="md:text-3xl text-2xl font-bold text-amber-400">:</div>
                                <div class="text-center">
                                    <div class="md:text-3xl text-2xl font-bold text-amber-700">{{ timeRemaining.seconds }}</div>
                                    <div class="text-xs text-amber-600">секунд</div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="isTimerExpired" class="md:mb-5 mb-4">
                            <div class="md:p-4 p-3 bg-red-50 rounded-lg border-2 border-red-300">
                                <p class="md:text-base text-sm font-semibold text-center text-red-800">
                                    Время истекло
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="goToPayment"
                            :disabled="isTimerExpired"
                            class="hover:bg-amber-700 active:bg-amber-800 disabled:bg-gray-400 disabled:cursor-not-allowed md:py-4 touch-manipulation flex gap-2 justify-center items-center px-4 py-3 w-full font-semibold text-white bg-amber-600 rounded-lg transition-colors"
                        >
                            <i class="fas fa-credit-card"></i>
                            <span>Перейти к оплате</span>
                        </button>
                    </div>

                    <div class="md:p-6 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                        <h2 class="md:mb-4 md:text-lg mb-3 text-base font-semibold text-gray-900">Состояние заказа</h2>
                        <dl class="md:space-y-3 md:text-sm space-y-2 text-xs text-gray-600">
                            <div class="flex gap-2 justify-between">
                                <dt>Номер заказа:</dt>
                                <dd class="font-medium text-gray-900 break-all">{{ order.number }}</dd>
                            </div>
                            <div class="sm:flex-row sm:justify-between flex flex-col gap-1">
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

                    <div class="md:p-6 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                        <h2 class="md:mb-4 md:text-lg mb-3 text-base font-semibold text-gray-900">Как связаться</h2>
                        <p class="md:text-sm text-xs text-gray-600">
                            Если у вас есть вопросы по заказу, позвоните в магазин или напишите на почту.
                        </p>
                        <div class="md:mt-4 md:space-y-3 md:text-sm mt-3 space-y-2 text-xs text-gray-800">
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

                    <div class="md:p-6 md:text-sm md:rounded-2xl p-4 text-xs text-blue-700 bg-blue-50 rounded-xl border border-blue-100">
                        <h3 class="md:text-base mb-2 text-sm font-semibold text-blue-900">Поделитесь ссылкой</h3>
                        <p class="mb-3">Вы можете отправить ссылку на эту страницу для отслеживания заказа вашим коллегам или клиенту.</p>
                        <div class="md:p-3 md:rounded-xl p-2 text-xs text-blue-800 break-all bg-white rounded-lg border border-blue-100">
                            {{ resolvedTrackingUrl }}
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>

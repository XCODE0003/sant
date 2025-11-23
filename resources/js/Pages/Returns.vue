<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Обмен и возврат' },
];
</script>

<template>
    <AppLayout title="Обмен и возврат - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <section class="bg-primary md:py-16 py-10 text-white">
            <div class="container px-4 mx-auto text-center">
                <h1 class="md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold">Обмен и возврат</h1>
                <p class="md:text-lg lg:text-xl mx-auto max-w-4xl text-sm text-blue-100">
                    Политика обмена и возврата товаров в магазине Акватэрия. Мы соблюдаем требования закона о защите прав
                    потребителей и стараемся сделать процесс простым и понятным.
                </p>
            </div>
        </section>

        <div class="md:py-16 container px-4 py-10 mx-auto">
            <div class="md:grid-cols-3 grid grid-cols-1 gap-6 mb-12">
                <div class="md:col-span-2 p-6 bg-white rounded-2xl shadow">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-900">1. Возврат товара надлежащего качества</h2>
                    <p class="mb-4 text-gray-700">
                        Вы имеете право требовать обмена или возврата товара без объяснения причин, если он не подходит
                        по форме, габаритам, фасону, расцветке, размеру или комплектации, при условии соблюдения всех
                        пунктов ниже:
                    </p>
                    <ol class="space-y-3 text-gray-700 list-decimal list-inside">
                        <li>Товар и упаковка обладают первоначальным товарным видом (сохраняйте оригинальную заводскую упаковку сантехники).</li>
                        <li>Претензия подана не позднее 14 календарных дней с момента покупки, не считая день покупки (ст. 26.1 закона «О защите прав потребителей»).</li>
                    </ol>
                    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 text-blue-900">
                        При соблюдении условий мы вернём уплаченную сумму в течение 10 рабочих дней со дня возврата. При
                        оплате банковской картой возврат производится на ту же карту.
                    </div>
                </div>

                <div class="p-6 bg-gray-900 text-white rounded-2xl shadow">
                    <h3 class="mb-4 text-xl font-semibold">Важно</h3>
                    <p class="mb-3 text-gray-200">Перед возвратом свяжитесь с менеджером для согласования деталей.</p>
                    <p class="text-gray-200">Телефон: <a href="tel:+79512353226" class="text-primary-light hover:underline">+7 (951) 235-32-26</a></p>
                </div>
            </div>

            <div class="md:grid-cols-2 grid grid-cols-1 gap-6 mb-12">
                <div class="p-6 bg-white rounded-2xl shadow">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-900">2. Обмен или возврат при скрытых дефектах</h2>
                    <p class="mb-4 text-gray-700">
                        Если в результате эксплуатации обнаружен скрытый дефект, обмен/возврат возможен при выполнении следующих условий:
                    </p>
                    <ol class="space-y-3 text-gray-700 list-decimal list-inside">
                        <li>Сервисный центр производителя провёл экспертизу и выдал заключение о наличии дефекта.</li>
                        <li>Имеется кассовый чек интернет-магазина Акватэрия.</li>
                        <li>Претензия предъявлена до окончания гарантийного срока (или в течение 14 дней, если срок не установлен).</li>
                        <li>Недостаток не возник вследствие неправильной эксплуатации товара.</li>
                    </ol>
                    <p class="mt-4 text-gray-700">
                        Необходимо предоставить заключение сертифицированного сервисного центра или органа технической экспертизы. Если выяснится, что дефект возник по причинам, за которые не отвечает продавец, покупатель возмещает расходы на экспертизу, хранение и транспортировку товара.
                    </p>
                    <div class="mt-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100 text-yellow-900">
                        Если кассовый чек утерян, предоставьте свидетеля покупки в магазине Акватэрия. Чтобы избежать конфликтных ситуаций, сохраняйте чеки.
                    </div>
                </div>

                <div class="p-6 bg-white rounded-2xl shadow">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-900">3. Когда обмен/возврат невозможен</h2>
                    <ul class="space-y-3 text-gray-700 list-disc list-inside">
                        <li>Нарушены условия эксплуатации, установки или иные гарантийные требования.</li>
                        <li>Товар имеет дефекты (трещины, царапины, сколы, механические повреждения), кроме скрытых производственных дефектов.</li>
                        <li>Товар в неполной комплектации (нет документации, комплектующих или необходимых принадлежностей).</li>
                    </ul>
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200 text-gray-800">
                        Пожалуйста, проверяйте комплектацию и внешний вид товара при получении, чтобы избежать недоразумений.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

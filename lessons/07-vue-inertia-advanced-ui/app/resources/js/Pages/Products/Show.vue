<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`Product ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Product Detail
                </h2>
                <Link
                    :href="route('products.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                >
                    Back to Products
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900">
                            {{ product.name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            SKU: <span class="font-mono">{{ product.sku }}</span>
                        </p>
                    </div>

                    <p class="text-gray-700">
                        {{ product.description || 'No description provided yet.' }}
                    </p>

                    <dl class="grid gap-3 text-sm md:grid-cols-2">
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Status
                            </dt>
                            <dd class="text-gray-900">
                                {{ product.status }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Price
                            </dt>
                            <dd class="text-gray-900">
                                ${{ Number(product.price).toFixed(2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Stock
                            </dt>
                            <dd class="text-gray-900">
                                {{ product.stock }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Featured
                            </dt>
                            <dd class="text-gray-900">
                                {{ product.is_featured ? 'Yes' : 'No' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Created
                            </dt>
                            <dd class="text-gray-900">
                                {{ product.created_at }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-600">
                                Updated
                            </dt>
                            <dd class="text-gray-900">
                                {{ product.updated_at }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

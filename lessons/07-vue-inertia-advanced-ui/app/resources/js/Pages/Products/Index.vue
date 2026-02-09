<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useProductQueryState } from '@/composables/useProductQueryState';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    sort: {
        type: Object,
        required: true,
    },
    statusOptions: {
        type: Array,
        required: true,
    },
});

const { state, apply, reset, goToPage, toggleSort } = useProductQueryState(props.filters, props.sort);
const isLoading = ref(false);
const optimisticFeatured = ref({});
const toggleBusy = ref({});

const fromLabel = computed(() => props.products.from ?? 0);
const toLabel = computed(() => props.products.to ?? 0);
const totalLabel = computed(() => props.products.total ?? 0);

const currentFeatured = (product) => {
    if (Object.prototype.hasOwnProperty.call(optimisticFeatured.value, product.id)) {
        return optimisticFeatured.value[product.id];
    }

    return product.is_featured;
};

const onApply = () => {
    isLoading.value = true;
    apply({
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const onReset = () => {
    isLoading.value = true;
    reset({
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const onSort = (field) => {
    isLoading.value = true;
    toggleSort(field, {
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const onPage = (page) => {
    if (!page) {
        return;
    }

    isLoading.value = true;
    goToPage(page, {
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const toggleFeatured = async (product) => {
    if (toggleBusy.value[product.id]) {
        return;
    }

    toggleBusy.value[product.id] = true;
    const previous = currentFeatured(product);
    optimisticFeatured.value[product.id] = !previous;

    try {
        const response = await axios.patch(
            route('products.toggle-featured', product.id),
            {},
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        );
        optimisticFeatured.value[product.id] = response.data.data.is_featured;
    } catch {
        optimisticFeatured.value[product.id] = previous;
    } finally {
        toggleBusy.value[product.id] = false;
    }
};

const sortIndicator = (field) => {
    if (state.sort !== field) {
        return '';
    }

    return state.direction === 'asc' ? '↑' : '↓';
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Products
                </h2>
                <Link
                    :href="route('dashboard')"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                >
                    Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="grid gap-3 md:grid-cols-5">
                        <input
                            v-model="state.q"
                            type="text"
                            placeholder="Search name, SKU, description"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 md:col-span-2"
                        />
                        <select
                            v-model="state.status"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">
                                All statuses
                            </option>
                            <option
                                v-for="status in statusOptions"
                                :key="status"
                                :value="status"
                            >
                                {{ status }}
                            </option>
                        </select>
                        <select
                            v-model="state.featured"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">
                                Featured + non-featured
                            </option>
                            <option value="1">
                                Featured only
                            </option>
                            <option value="0">
                                Non-featured only
                            </option>
                        </select>
                        <select
                            v-model.number="state.per_page"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option :value="10">
                                10 / page
                            </option>
                            <option :value="20">
                                20 / page
                            </option>
                            <option :value="50">
                                50 / page
                            </option>
                        </select>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="isLoading"
                            @click="onApply"
                        >
                            Apply filters
                        </button>
                        <button
                            type="button"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="isLoading"
                            @click="onReset"
                        >
                            Reset
                        </button>
                        <span
                            class="self-center text-sm text-gray-500"
                            v-if="isLoading"
                        >
                            Loading...
                        </span>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="hover:text-gray-900"
                                            @click="onSort('name')"
                                        >
                                            Name {{ sortIndicator('name') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3">
                                        SKU
                                    </th>
                                    <th class="px-4 py-3">
                                        Status
                                    </th>
                                    <th class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="hover:text-gray-900"
                                            @click="onSort('price')"
                                        >
                                            Price {{ sortIndicator('price') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3">
                                        Stock
                                    </th>
                                    <th class="px-4 py-3">
                                        Featured
                                    </th>
                                    <th class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="hover:text-gray-900"
                                            @click="onSort('created_at')"
                                        >
                                            Created {{ sortIndicator('created_at') }}
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                v-if="products.data.length"
                                class="divide-y divide-gray-100 text-sm text-gray-800"
                            >
                                <tr
                                    v-for="product in products.data"
                                    :key="product.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-4 py-3 font-medium">
                                        <Link
                                            :href="route('products.show', product.id)"
                                            class="text-indigo-700 hover:text-indigo-500"
                                        >
                                            {{ product.name }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">
                                        {{ product.sku }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ product.status }}
                                    </td>
                                    <td class="px-4 py-3">
                                        ${{ Number(product.price).toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ product.stock }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="rounded-md border px-3 py-1 text-xs font-medium"
                                            :class="currentFeatured(product) ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-gray-300 bg-white text-gray-700'"
                                            :disabled="toggleBusy[product.id]"
                                            @click="toggleFeatured(product)"
                                        >
                                            {{ currentFeatured(product) ? 'Featured' : 'Mark featured' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ product.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-4 py-8 text-center text-sm text-gray-500"
                                    >
                                        No products match this filter set.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 text-sm text-gray-600">
                        <p>
                            Showing {{ fromLabel }}-{{ toLabel }} of {{ totalLabel }}
                        </p>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="rounded border border-gray-300 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="!products.prev_page_url || isLoading"
                                @click="onPage(products.current_page - 1)"
                            >
                                Prev
                            </button>
                            <button
                                type="button"
                                class="rounded border border-gray-300 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="!products.next_page_url || isLoading"
                                @click="onPage(products.current_page + 1)"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

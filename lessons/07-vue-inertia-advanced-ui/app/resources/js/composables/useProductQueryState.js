import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

export function useProductQueryState(filters, sort) {
    const state = reactive({
        q: filters.q ?? '',
        status: filters.status ?? '',
        featured: filters.featured ?? '',
        per_page: Number(filters.per_page ?? 10),
        sort: sort.field ?? 'created_at',
        direction: sort.direction ?? 'desc',
    });

    const buildParams = (overrides = {}) => {
        const merged = { ...state, ...overrides };

        return Object.fromEntries(
            Object.entries(merged).filter(([, value]) => value !== '' && value !== null && value !== undefined),
        );
    };

    const visit = (params, options = {}) => {
        router.get(route('products.index'), params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['products', 'filters', 'sort'],
            ...options,
        });
    };

    const apply = (options = {}) => {
        visit(buildParams({ page: 1 }), options);
    };

    const goToPage = (page, options = {}) => {
        visit(buildParams({ page }), options);
    };

    const reset = (options = {}) => {
        state.q = '';
        state.status = '';
        state.featured = '';
        state.per_page = 10;
        state.sort = 'created_at';
        state.direction = 'desc';
        visit({}, options);
    };

    const toggleSort = (field, options = {}) => {
        if (state.sort === field) {
            state.direction = state.direction === 'asc' ? 'desc' : 'asc';
        } else {
            state.sort = field;
            state.direction = 'asc';
        }

        apply(options);
    };

    return {
        state,
        apply,
        reset,
        goToPage,
        toggleSort,
    };
}

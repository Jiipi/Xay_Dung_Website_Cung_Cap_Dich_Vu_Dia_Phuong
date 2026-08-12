import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::index
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::requestWithdrawal
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
export const requestWithdrawal = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestWithdrawal.url(options),
    method: 'post',
})

requestWithdrawal.definition = {
    methods: ["post"],
    url: '/provider/finance/withdraw',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::requestWithdrawal
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
requestWithdrawal.url = (options?: RouteQueryOptions) => {
    return requestWithdrawal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::requestWithdrawal
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
requestWithdrawal.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestWithdrawal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::requestWithdrawal
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
const requestWithdrawalForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestWithdrawal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::requestWithdrawal
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
requestWithdrawalForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestWithdrawal.url(options),
    method: 'post',
})

requestWithdrawal.form = requestWithdrawalForm

const ProviderFinanceController = { index, requestWithdrawal }

export default ProviderFinanceController
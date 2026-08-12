import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/customer/wallet',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::index
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
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
* @see \App\Http\Controllers\Customer\CustomerWalletController::topup
* @see app/Http/Controllers/Customer/CustomerWalletController.php:44
* @route '/customer/wallet/topup'
*/
export const topup = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: topup.url(options),
    method: 'post',
})

topup.definition = {
    methods: ["post"],
    url: '/customer/wallet/topup',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::topup
* @see app/Http/Controllers/Customer/CustomerWalletController.php:44
* @route '/customer/wallet/topup'
*/
topup.url = (options?: RouteQueryOptions) => {
    return topup.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::topup
* @see app/Http/Controllers/Customer/CustomerWalletController.php:44
* @route '/customer/wallet/topup'
*/
topup.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: topup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::topup
* @see app/Http/Controllers/Customer/CustomerWalletController.php:44
* @route '/customer/wallet/topup'
*/
const topupForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: topup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::topup
* @see app/Http/Controllers/Customer/CustomerWalletController.php:44
* @route '/customer/wallet/topup'
*/
topupForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: topup.url(options),
    method: 'post',
})

topup.form = topupForm

const CustomerWalletController = { index, topup }

export default CustomerWalletController
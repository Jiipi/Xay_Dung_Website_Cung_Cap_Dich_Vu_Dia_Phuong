import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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

const wallet = {
    topup: Object.assign(topup, topup),
}

export default wallet
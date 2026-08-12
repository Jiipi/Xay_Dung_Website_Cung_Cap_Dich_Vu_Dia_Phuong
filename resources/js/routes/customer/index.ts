import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import bookings from './bookings'
import favorites from './favorites'
import aiPlanner from './ai-planner'
import profile937a89 from './profile'
import wallet0fdd46 from './wallet'
import payment from './payment'
import reviews from './reviews'
import notifications from './notifications'
/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/customer/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::dashboard
* @see app/Http/Controllers/Customer/CustomerController.php:26
* @route '/customer/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/customer/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
const profileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
profileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::profile
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
profileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

profile.form = profileForm

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
export const wallet = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallet.url(options),
    method: 'get',
})

wallet.definition = {
    methods: ["get","head"],
    url: '/customer/wallet',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
wallet.url = (options?: RouteQueryOptions) => {
    return wallet.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
wallet.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallet.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
wallet.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: wallet.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
const walletForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wallet.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
walletForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wallet.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerWalletController::wallet
* @see app/Http/Controllers/Customer/CustomerWalletController.php:16
* @route '/customer/wallet'
*/
walletForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: wallet.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

wallet.form = walletForm

const customer = {
    dashboard: Object.assign(dashboard, dashboard),
    bookings: Object.assign(bookings, bookings),
    favorites: Object.assign(favorites, favorites),
    aiPlanner: Object.assign(aiPlanner, aiPlanner),
    profile: Object.assign(profile, profile937a89),
    wallet: Object.assign(wallet, wallet0fdd46),
    payment: Object.assign(payment, payment),
    reviews: Object.assign(reviews, reviews),
    notifications: Object.assign(notifications, notifications),
}

export default customer
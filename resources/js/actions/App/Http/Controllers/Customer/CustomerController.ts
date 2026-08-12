import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
export const bookings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

bookings.definition = {
    methods: ["get","head"],
    url: '/customer/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
bookings.url = (options?: RouteQueryOptions) => {
    return bookings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
bookings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
bookings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: bookings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
const bookingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
bookingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::bookings
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
bookingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

bookings.form = bookingsForm

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
export const favorites = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favorites.url(options),
    method: 'get',
})

favorites.definition = {
    methods: ["get","head"],
    url: '/customer/favorites',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
favorites.url = (options?: RouteQueryOptions) => {
    return favorites.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
favorites.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favorites.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
favorites.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: favorites.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
const favoritesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favorites.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
favoritesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favorites.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::favorites
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
favoritesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favorites.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

favorites.form = favoritesForm

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggleFavorite
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
export const toggleFavorite = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleFavorite.url(options),
    method: 'post',
})

toggleFavorite.definition = {
    methods: ["post"],
    url: '/customer/favorites/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggleFavorite
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggleFavorite.url = (options?: RouteQueryOptions) => {
    return toggleFavorite.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggleFavorite
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggleFavorite.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleFavorite.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggleFavorite
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
const toggleFavoriteForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleFavorite.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggleFavorite
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggleFavoriteForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleFavorite.url(options),
    method: 'post',
})

toggleFavorite.form = toggleFavoriteForm

const CustomerController = { dashboard, bookings, favorites, toggleFavorite }

export default CustomerController
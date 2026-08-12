import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/customer/favorites',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:84
* @route '/customer/favorites'
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
* @see \App\Http\Controllers\Customer\CustomerController::toggle
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
export const toggle = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/customer/favorites/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggle
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggle.url = (options?: RouteQueryOptions) => {
    return toggle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggle
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggle.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggle
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
const toggleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::toggle
* @see app/Http/Controllers/Customer/CustomerController.php:119
* @route '/customer/favorites/toggle'
*/
toggleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(options),
    method: 'post',
})

toggle.form = toggleForm

const favorites = {
    index: Object.assign(index, index),
    toggle: Object.assign(toggle, toggle),
}

export default favorites
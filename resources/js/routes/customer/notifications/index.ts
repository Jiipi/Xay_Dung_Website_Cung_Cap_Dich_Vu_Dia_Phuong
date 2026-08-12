import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/customer/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::index
* @see app/Http/Controllers/Customer/NotificationController.php:20
* @route '/customer/notifications'
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
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
export const recent = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recent.url(options),
    method: 'get',
})

recent.definition = {
    methods: ["get","head"],
    url: '/customer/notifications/recent',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
recent.url = (options?: RouteQueryOptions) => {
    return recent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
recent.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
recent.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recent.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
const recentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
recentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::recent
* @see app/Http/Controllers/Customer/NotificationController.php:45
* @route '/customer/notifications/recent'
*/
recentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recent.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recent.form = recentForm

const notifications = {
    index: Object.assign(index, index),
    recent: Object.assign(recent, recent),
}

export default notifications
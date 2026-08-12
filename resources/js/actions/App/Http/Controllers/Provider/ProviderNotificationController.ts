import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::index
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:20
* @route '/provider/notifications'
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
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
export const markRead = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

markRead.definition = {
    methods: ["post"],
    url: '/provider/notifications/{id}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
markRead.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return markRead.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
markRead.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
const markReadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
markReadForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

markRead.form = markReadForm

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markAllRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
export const markAllRead = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllRead.url(options),
    method: 'post',
})

markAllRead.definition = {
    methods: ["post"],
    url: '/provider/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markAllRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
markAllRead.url = (options?: RouteQueryOptions) => {
    return markAllRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markAllRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
markAllRead.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllRead.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markAllRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
const markAllReadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAllRead.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::markAllRead
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
markAllReadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAllRead.url(options),
    method: 'post',
})

markAllRead.form = markAllReadForm

const ProviderNotificationController = { index, markRead, markAllRead }

export default ProviderNotificationController
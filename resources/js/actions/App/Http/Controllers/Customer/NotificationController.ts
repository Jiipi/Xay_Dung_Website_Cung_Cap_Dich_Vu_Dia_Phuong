import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\Customer\NotificationController::markRead
* @see app/Http/Controllers/Customer/NotificationController.php:66
* @route '/notifications/{id}/read'
*/
export const markRead = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

markRead.definition = {
    methods: ["post"],
    url: '/notifications/{id}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\NotificationController::markRead
* @see app/Http/Controllers/Customer/NotificationController.php:66
* @route '/notifications/{id}/read'
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
* @see \App\Http\Controllers\Customer\NotificationController::markRead
* @see app/Http/Controllers/Customer/NotificationController.php:66
* @route '/notifications/{id}/read'
*/
markRead.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::markRead
* @see app/Http/Controllers/Customer/NotificationController.php:66
* @route '/notifications/{id}/read'
*/
const markReadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::markRead
* @see app/Http/Controllers/Customer/NotificationController.php:66
* @route '/notifications/{id}/read'
*/
markReadForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

markRead.form = markReadForm

/**
* @see \App\Http\Controllers\Customer\NotificationController::markAllRead
* @see app/Http/Controllers/Customer/NotificationController.php:76
* @route '/notifications/read-all'
*/
export const markAllRead = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllRead.url(options),
    method: 'post',
})

markAllRead.definition = {
    methods: ["post"],
    url: '/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\NotificationController::markAllRead
* @see app/Http/Controllers/Customer/NotificationController.php:76
* @route '/notifications/read-all'
*/
markAllRead.url = (options?: RouteQueryOptions) => {
    return markAllRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\NotificationController::markAllRead
* @see app/Http/Controllers/Customer/NotificationController.php:76
* @route '/notifications/read-all'
*/
markAllRead.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAllRead.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::markAllRead
* @see app/Http/Controllers/Customer/NotificationController.php:76
* @route '/notifications/read-all'
*/
const markAllReadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAllRead.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\NotificationController::markAllRead
* @see app/Http/Controllers/Customer/NotificationController.php:76
* @route '/notifications/read-all'
*/
markAllReadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAllRead.url(options),
    method: 'post',
})

markAllRead.form = markAllReadForm

const NotificationController = { index, recent, markRead, markAllRead }

export default NotificationController
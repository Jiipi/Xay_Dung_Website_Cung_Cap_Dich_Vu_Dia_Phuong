import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
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
* @see \App\Http\Controllers\Provider\ProviderNotificationController::read
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
export const read = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

read.definition = {
    methods: ["post"],
    url: '/provider/notifications/{id}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::read
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
read.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return read.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::read
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
read.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: read.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::read
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
const readForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::read
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:45
* @route '/provider/notifications/{id}/read'
*/
readForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, options),
    method: 'post',
})

read.form = readForm

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::readAll
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
export const readAll = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

readAll.definition = {
    methods: ["post"],
    url: '/provider/notifications/read-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::readAll
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
readAll.url = (options?: RouteQueryOptions) => {
    return readAll.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::readAll
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
readAll.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readAll.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::readAll
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
const readAllForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readAll.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderNotificationController::readAll
* @see app/Http/Controllers/Provider/ProviderNotificationController.php:55
* @route '/provider/notifications/read-all'
*/
readAllForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readAll.url(options),
    method: 'post',
})

readAll.form = readAllForm

const notifications = {
    index: Object.assign(index, index),
    read: Object.assign(read, read),
    readAll: Object.assign(readAll, readAll),
}

export default notifications
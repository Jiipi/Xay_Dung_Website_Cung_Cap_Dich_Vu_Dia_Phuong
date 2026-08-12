import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::index
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
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
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/provider/bookings/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::show
* @see app/Http/Controllers/Provider/ProviderBookingController.php:58
* @route '/provider/bookings/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::confirm
* @see app/Http/Controllers/Provider/ProviderBookingController.php:103
* @route '/provider/bookings/{id}/confirm'
*/
export const confirm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

confirm.definition = {
    methods: ["post"],
    url: '/provider/bookings/{id}/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::confirm
* @see app/Http/Controllers/Provider/ProviderBookingController.php:103
* @route '/provider/bookings/{id}/confirm'
*/
confirm.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return confirm.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::confirm
* @see app/Http/Controllers/Provider/ProviderBookingController.php:103
* @route '/provider/bookings/{id}/confirm'
*/
confirm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::confirm
* @see app/Http/Controllers/Provider/ProviderBookingController.php:103
* @route '/provider/bookings/{id}/confirm'
*/
const confirmForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: confirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::confirm
* @see app/Http/Controllers/Provider/ProviderBookingController.php:103
* @route '/provider/bookings/{id}/confirm'
*/
confirmForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: confirm.url(args, options),
    method: 'post',
})

confirm.form = confirmForm

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::reject
* @see app/Http/Controllers/Provider/ProviderBookingController.php:116
* @route '/provider/bookings/{id}/reject'
*/
export const reject = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/provider/bookings/{id}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::reject
* @see app/Http/Controllers/Provider/ProviderBookingController.php:116
* @route '/provider/bookings/{id}/reject'
*/
reject.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reject.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::reject
* @see app/Http/Controllers/Provider/ProviderBookingController.php:116
* @route '/provider/bookings/{id}/reject'
*/
reject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::reject
* @see app/Http/Controllers/Provider/ProviderBookingController.php:116
* @route '/provider/bookings/{id}/reject'
*/
const rejectForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::reject
* @see app/Http/Controllers/Provider/ProviderBookingController.php:116
* @route '/provider/bookings/{id}/reject'
*/
rejectForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::complete
* @see app/Http/Controllers/Provider/ProviderBookingController.php:133
* @route '/provider/bookings/{id}/complete'
*/
export const complete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/provider/bookings/{id}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::complete
* @see app/Http/Controllers/Provider/ProviderBookingController.php:133
* @route '/provider/bookings/{id}/complete'
*/
complete.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return complete.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::complete
* @see app/Http/Controllers/Provider/ProviderBookingController.php:133
* @route '/provider/bookings/{id}/complete'
*/
complete.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::complete
* @see app/Http/Controllers/Provider/ProviderBookingController.php:133
* @route '/provider/bookings/{id}/complete'
*/
const completeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::complete
* @see app/Http/Controllers/Provider/ProviderBookingController.php:133
* @route '/provider/bookings/{id}/complete'
*/
completeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

complete.form = completeForm

const ProviderBookingController = { index, show, confirm, reject, complete }

export default ProviderBookingController
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/customer/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerController::index
* @see app/Http/Controllers/Customer/CustomerController.php:53
* @route '/customer/bookings'
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
* @see \App\Http\Controllers\Customer\BookingController::store
* @see app/Http/Controllers/Customer/BookingController.php:28
* @route '/customer/bookings'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/customer/bookings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\BookingController::store
* @see app/Http/Controllers/Customer/BookingController.php:28
* @route '/customer/bookings'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\BookingController::store
* @see app/Http/Controllers/Customer/BookingController.php:28
* @route '/customer/bookings'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::store
* @see app/Http/Controllers/Customer/BookingController.php:28
* @route '/customer/bookings'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::store
* @see app/Http/Controllers/Customer/BookingController.php:28
* @route '/customer/bookings'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
export const success = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(args, options),
    method: 'get',
})

success.definition = {
    methods: ["get","head"],
    url: '/customer/bookings/success/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
success.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return success.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
success.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
success.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: success.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
const successForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
successForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::success
* @see app/Http/Controllers/Customer/BookingController.php:43
* @route '/customer/bookings/success/{id}'
*/
successForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: success.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

success.form = successForm

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/customer/bookings/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
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
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::show
* @see app/Http/Controllers/Customer/BookingController.php:71
* @route '/customer/bookings/{id}'
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
* @see \App\Http\Controllers\Customer\BookingController::cancel
* @see app/Http/Controllers/Customer/BookingController.php:114
* @route '/customer/bookings/{id}/cancel'
*/
export const cancel = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/customer/bookings/{id}/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\BookingController::cancel
* @see app/Http/Controllers/Customer/BookingController.php:114
* @route '/customer/bookings/{id}/cancel'
*/
cancel.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return cancel.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\BookingController::cancel
* @see app/Http/Controllers/Customer/BookingController.php:114
* @route '/customer/bookings/{id}/cancel'
*/
cancel.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::cancel
* @see app/Http/Controllers/Customer/BookingController.php:114
* @route '/customer/bookings/{id}/cancel'
*/
const cancelForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\BookingController::cancel
* @see app/Http/Controllers/Customer/BookingController.php:114
* @route '/customer/bookings/{id}/cancel'
*/
cancelForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

cancel.form = cancelForm

const bookings = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    success: Object.assign(success, success),
    show: Object.assign(show, show),
    cancel: Object.assign(cancel, cancel),
}

export default bookings
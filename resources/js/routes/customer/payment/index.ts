import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
export const checkout = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(args, options),
    method: 'get',
})

checkout.definition = {
    methods: ["get","head"],
    url: '/customer/bookings/{id}/payment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
checkout.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return checkout.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
checkout.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
checkout.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
const checkoutForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
checkoutForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::checkout
* @see app/Http/Controllers/Customer/PaymentController.php:19
* @route '/customer/bookings/{id}/payment'
*/
checkoutForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

checkout.form = checkoutForm

/**
* @see \App\Http\Controllers\Customer\PaymentController::process
* @see app/Http/Controllers/Customer/PaymentController.php:59
* @route '/customer/bookings/{id}/payment/process'
*/
export const process = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

process.definition = {
    methods: ["post"],
    url: '/customer/bookings/{id}/payment/process',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\PaymentController::process
* @see app/Http/Controllers/Customer/PaymentController.php:59
* @route '/customer/bookings/{id}/payment/process'
*/
process.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return process.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\PaymentController::process
* @see app/Http/Controllers/Customer/PaymentController.php:59
* @route '/customer/bookings/{id}/payment/process'
*/
process.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::process
* @see app/Http/Controllers/Customer/PaymentController.php:59
* @route '/customer/bookings/{id}/payment/process'
*/
const processForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\PaymentController::process
* @see app/Http/Controllers/Customer/PaymentController.php:59
* @route '/customer/bookings/{id}/payment/process'
*/
processForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

process.form = processForm

const payment = {
    checkout: Object.assign(checkout, checkout),
    process: Object.assign(process, process),
}

export default payment
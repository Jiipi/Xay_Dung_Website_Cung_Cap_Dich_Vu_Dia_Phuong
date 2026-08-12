import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceConfirm
* @see app/Http/Controllers/Admin/AdminBookingController.php:49
* @route '/admin/bookings/{id}/force-confirm'
*/
export const forceConfirm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceConfirm.url(args, options),
    method: 'post',
})

forceConfirm.definition = {
    methods: ["post"],
    url: '/admin/bookings/{id}/force-confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceConfirm
* @see app/Http/Controllers/Admin/AdminBookingController.php:49
* @route '/admin/bookings/{id}/force-confirm'
*/
forceConfirm.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return forceConfirm.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceConfirm
* @see app/Http/Controllers/Admin/AdminBookingController.php:49
* @route '/admin/bookings/{id}/force-confirm'
*/
forceConfirm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceConfirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceConfirm
* @see app/Http/Controllers/Admin/AdminBookingController.php:49
* @route '/admin/bookings/{id}/force-confirm'
*/
const forceConfirmForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceConfirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceConfirm
* @see app/Http/Controllers/Admin/AdminBookingController.php:49
* @route '/admin/bookings/{id}/force-confirm'
*/
forceConfirmForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceConfirm.url(args, options),
    method: 'post',
})

forceConfirm.form = forceConfirmForm

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceComplete
* @see app/Http/Controllers/Admin/AdminBookingController.php:59
* @route '/admin/bookings/{id}/force-complete'
*/
export const forceComplete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceComplete.url(args, options),
    method: 'post',
})

forceComplete.definition = {
    methods: ["post"],
    url: '/admin/bookings/{id}/force-complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceComplete
* @see app/Http/Controllers/Admin/AdminBookingController.php:59
* @route '/admin/bookings/{id}/force-complete'
*/
forceComplete.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return forceComplete.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceComplete
* @see app/Http/Controllers/Admin/AdminBookingController.php:59
* @route '/admin/bookings/{id}/force-complete'
*/
forceComplete.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceComplete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceComplete
* @see app/Http/Controllers/Admin/AdminBookingController.php:59
* @route '/admin/bookings/{id}/force-complete'
*/
const forceCompleteForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceComplete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceComplete
* @see app/Http/Controllers/Admin/AdminBookingController.php:59
* @route '/admin/bookings/{id}/force-complete'
*/
forceCompleteForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceComplete.url(args, options),
    method: 'post',
})

forceComplete.form = forceCompleteForm

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceReject
* @see app/Http/Controllers/Admin/AdminBookingController.php:69
* @route '/admin/bookings/{id}/force-reject'
*/
export const forceReject = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceReject.url(args, options),
    method: 'post',
})

forceReject.definition = {
    methods: ["post"],
    url: '/admin/bookings/{id}/force-reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceReject
* @see app/Http/Controllers/Admin/AdminBookingController.php:69
* @route '/admin/bookings/{id}/force-reject'
*/
forceReject.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return forceReject.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceReject
* @see app/Http/Controllers/Admin/AdminBookingController.php:69
* @route '/admin/bookings/{id}/force-reject'
*/
forceReject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forceReject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceReject
* @see app/Http/Controllers/Admin/AdminBookingController.php:69
* @route '/admin/bookings/{id}/force-reject'
*/
const forceRejectForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceReject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::forceReject
* @see app/Http/Controllers/Admin/AdminBookingController.php:69
* @route '/admin/bookings/{id}/force-reject'
*/
forceRejectForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: forceReject.url(args, options),
    method: 'post',
})

forceReject.form = forceRejectForm

const bookings = {
    forceConfirm: Object.assign(forceConfirm, forceConfirm),
    forceComplete: Object.assign(forceComplete, forceComplete),
    forceReject: Object.assign(forceReject, forceReject),
}

export default bookings
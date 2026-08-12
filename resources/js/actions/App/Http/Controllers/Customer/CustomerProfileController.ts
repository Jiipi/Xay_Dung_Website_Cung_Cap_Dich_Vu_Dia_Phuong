import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/customer/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::edit
* @see app/Http/Controllers/Customer/CustomerProfileController.php:17
* @route '/customer/profile'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post","put","patch"],
    url: '/customer/profile/update',
} satisfies RouteDefinition<["post","put","patch"]>

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\CustomerProfileController::update
* @see app/Http/Controllers/Customer/CustomerProfileController.php:32
* @route '/customer/profile/update'
*/
updateForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const CustomerProfileController = { edit, update }

export default CustomerProfileController
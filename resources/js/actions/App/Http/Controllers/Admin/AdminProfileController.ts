import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::edit
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
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
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post","put","patch"],
    url: '/admin/profile/update',
} satisfies RouteDefinition<["post","put","patch"]>

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
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
* @see \App\Http\Controllers\Admin\AdminProfileController::update
* @see app/Http/Controllers/Admin/AdminProfileController.php:28
* @route '/admin/profile/update'
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

const AdminProfileController = { edit, update }

export default AdminProfileController
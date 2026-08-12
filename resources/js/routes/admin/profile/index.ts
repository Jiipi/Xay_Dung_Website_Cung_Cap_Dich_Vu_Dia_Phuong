import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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

const profile = {
    update: Object.assign(update, update),
}

export default profile
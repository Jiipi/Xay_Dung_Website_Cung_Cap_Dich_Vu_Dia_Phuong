import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::update
* @see app/Http/Controllers/Admin/AdminSettingsController.php:21
* @route '/admin/settings'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/admin/settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::update
* @see app/Http/Controllers/Admin/AdminSettingsController.php:21
* @route '/admin/settings'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::update
* @see app/Http/Controllers/Admin/AdminSettingsController.php:21
* @route '/admin/settings'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::update
* @see app/Http/Controllers/Admin/AdminSettingsController.php:21
* @route '/admin/settings'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::update
* @see app/Http/Controllers/Admin/AdminSettingsController.php:21
* @route '/admin/settings'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const settings = {
    update: Object.assign(update, update),
}

export default settings
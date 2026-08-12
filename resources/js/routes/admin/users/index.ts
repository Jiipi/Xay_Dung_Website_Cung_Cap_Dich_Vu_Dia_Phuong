import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminUserController::toggle
* @see app/Http/Controllers/Admin/AdminUserController.php:65
* @route '/admin/users/{id}/toggle-status'
*/
export const toggle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/admin/users/{id}/toggle-status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::toggle
* @see app/Http/Controllers/Admin/AdminUserController.php:65
* @route '/admin/users/{id}/toggle-status'
*/
toggle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return toggle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::toggle
* @see app/Http/Controllers/Admin/AdminUserController.php:65
* @route '/admin/users/{id}/toggle-status'
*/
toggle.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::toggle
* @see app/Http/Controllers/Admin/AdminUserController.php:65
* @route '/admin/users/{id}/toggle-status'
*/
const toggleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::toggle
* @see app/Http/Controllers/Admin/AdminUserController.php:65
* @route '/admin/users/{id}/toggle-status'
*/
toggleForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

toggle.form = toggleForm

const users = {
    toggle: Object.assign(toggle, toggle),
}

export default users
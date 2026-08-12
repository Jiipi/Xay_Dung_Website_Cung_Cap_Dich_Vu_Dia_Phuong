import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::index
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
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

const AdminStatsController = { index }

export default AdminStatsController
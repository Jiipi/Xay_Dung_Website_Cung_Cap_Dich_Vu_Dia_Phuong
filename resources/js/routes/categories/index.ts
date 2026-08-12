import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Category\CategoryController::index
* @see app/Http/Controllers/Category/CategoryController.php:22
* @route '/categories'
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

const categories = {
    index: Object.assign(index, index),
}

export default categories
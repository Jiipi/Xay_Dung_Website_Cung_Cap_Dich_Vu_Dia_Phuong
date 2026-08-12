import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/ai-planner',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
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

const aiPlanner = {
    index: Object.assign(index, index),
}

export default aiPlanner
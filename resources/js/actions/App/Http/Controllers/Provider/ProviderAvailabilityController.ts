import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/availability',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::index
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
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

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::update
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:46
* @route '/provider/availability'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/provider/availability',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::update
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:46
* @route '/provider/availability'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::update
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:46
* @route '/provider/availability'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::update
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:46
* @route '/provider/availability'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::update
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:46
* @route '/provider/availability'
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

update.form = updateForm

const ProviderAvailabilityController = { index, update }

export default ProviderAvailabilityController
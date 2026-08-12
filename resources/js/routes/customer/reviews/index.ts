import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/customer/reviews/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::create
* @see app/Http/Controllers/Customer/ReviewController.php:28
* @route '/customer/reviews/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Customer\ReviewController::store
* @see app/Http/Controllers/Customer/ReviewController.php:61
* @route '/customer/reviews'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/customer/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Customer\ReviewController::store
* @see app/Http/Controllers/Customer/ReviewController.php:61
* @route '/customer/reviews'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Customer\ReviewController::store
* @see app/Http/Controllers/Customer/ReviewController.php:61
* @route '/customer/reviews'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::store
* @see app/Http/Controllers/Customer/ReviewController.php:61
* @route '/customer/reviews'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Customer\ReviewController::store
* @see app/Http/Controllers/Customer/ReviewController.php:61
* @route '/customer/reviews'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const reviews = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
}

export default reviews
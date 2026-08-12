import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::index
* @see app/Http/Controllers/Provider/ProviderReviewController.php:22
* @route '/provider/reviews'
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
* @see \App\Http\Controllers\Provider\ProviderReviewController::reply
* @see app/Http/Controllers/Provider/ProviderReviewController.php:55
* @route '/provider/reviews/{id}/reply'
*/
export const reply = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reply.url(args, options),
    method: 'post',
})

reply.definition = {
    methods: ["post"],
    url: '/provider/reviews/{id}/reply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::reply
* @see app/Http/Controllers/Provider/ProviderReviewController.php:55
* @route '/provider/reviews/{id}/reply'
*/
reply.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reply.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::reply
* @see app/Http/Controllers/Provider/ProviderReviewController.php:55
* @route '/provider/reviews/{id}/reply'
*/
reply.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::reply
* @see app/Http/Controllers/Provider/ProviderReviewController.php:55
* @route '/provider/reviews/{id}/reply'
*/
const replyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderReviewController::reply
* @see app/Http/Controllers/Provider/ProviderReviewController.php:55
* @route '/provider/reviews/{id}/reply'
*/
replyForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, options),
    method: 'post',
})

reply.form = replyForm

const reviews = {
    index: Object.assign(index, index),
    reply: Object.assign(reply, reply),
}

export default reviews
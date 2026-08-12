import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::index
* @see app/Http/Controllers/Service/ServiceController.php:27
* @route '/services'
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
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/services/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::show
* @see app/Http/Controllers/Service/ServiceController.php:129
* @route '/services/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
export const aiPlanner = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: aiPlanner.url(options),
    method: 'get',
})

aiPlanner.definition = {
    methods: ["get","head"],
    url: '/ai-planner',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
aiPlanner.url = (options?: RouteQueryOptions) => {
    return aiPlanner.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
aiPlanner.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: aiPlanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
aiPlanner.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: aiPlanner.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
const aiPlannerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aiPlanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
aiPlannerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aiPlanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::aiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:244
* @route '/ai-planner'
*/
aiPlannerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aiPlanner.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

aiPlanner.form = aiPlannerForm

/**
* @see \App\Http\Controllers\Service\ServiceController::generateAiPlan
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
export const generateAiPlan = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateAiPlan.url(options),
    method: 'post',
})

generateAiPlan.definition = {
    methods: ["post"],
    url: '/customer/ai-planner/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::generateAiPlan
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generateAiPlan.url = (options?: RouteQueryOptions) => {
    return generateAiPlan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::generateAiPlan
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generateAiPlan.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateAiPlan.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::generateAiPlan
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
const generateAiPlanForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateAiPlan.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::generateAiPlan
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generateAiPlanForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateAiPlan.url(options),
    method: 'post',
})

generateAiPlan.form = generateAiPlanForm

/**
* @see \App\Http\Controllers\Service\ServiceController::chatAiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
export const chatAiPlanner = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chatAiPlanner.url(options),
    method: 'post',
})

chatAiPlanner.definition = {
    methods: ["post"],
    url: '/customer/ai-planner/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::chatAiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chatAiPlanner.url = (options?: RouteQueryOptions) => {
    return chatAiPlanner.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::chatAiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chatAiPlanner.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chatAiPlanner.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::chatAiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
const chatAiPlannerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: chatAiPlanner.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::chatAiPlanner
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chatAiPlannerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: chatAiPlanner.url(options),
    method: 'post',
})

chatAiPlanner.form = chatAiPlannerForm

const ServiceController = { index, show, aiPlanner, generateAiPlan, chatAiPlanner }

export default ServiceController
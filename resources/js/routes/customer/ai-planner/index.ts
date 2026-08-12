import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Service\ServiceController::generate
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/customer/ai-planner/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::generate
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::generate
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::generate
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::generate
* @see app/Http/Controllers/Service/ServiceController.php:286
* @route '/customer/ai-planner/generate'
*/
generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

generate.form = generateForm

/**
* @see \App\Http\Controllers\Service\ServiceController::chat
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
export const chat = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

chat.definition = {
    methods: ["post"],
    url: '/customer/ai-planner/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Service\ServiceController::chat
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chat.url = (options?: RouteQueryOptions) => {
    return chat.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Service\ServiceController::chat
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chat.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::chat
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
const chatForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: chat.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Service\ServiceController::chat
* @see app/Http/Controllers/Service/ServiceController.php:295
* @route '/customer/ai-planner/chat'
*/
chatForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: chat.url(options),
    method: 'post',
})

chat.form = chatForm

const aiPlanner = {
    generate: Object.assign(generate, generate),
    chat: Object.assign(chat, chat),
}

export default aiPlanner
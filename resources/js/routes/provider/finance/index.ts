import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::withdraw
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
export const withdraw = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: withdraw.url(options),
    method: 'post',
})

withdraw.definition = {
    methods: ["post"],
    url: '/provider/finance/withdraw',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::withdraw
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
withdraw.url = (options?: RouteQueryOptions) => {
    return withdraw.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::withdraw
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
withdraw.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: withdraw.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::withdraw
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
const withdrawForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: withdraw.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::withdraw
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:51
* @route '/provider/finance/withdraw'
*/
withdrawForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: withdraw.url(options),
    method: 'post',
})

withdraw.form = withdrawForm

const finance = {
    withdraw: Object.assign(withdraw, withdraw),
}

export default finance
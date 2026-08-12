import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import profile937a89 from './profile'
import services11ad26 from './services'
import bookings743b13 from './bookings'
import availability135fa0 from './availability'
import financeEb3fcc from './finance'
import reviews from './reviews'
import notifications from './notifications'
/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/provider/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderDashboardController::dashboard
* @see app/Http/Controllers/Provider/ProviderDashboardController.php:19
* @route '/provider/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/provider/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
const profileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
profileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderProfileController::profile
* @see app/Http/Controllers/Provider/ProviderProfileController.php:16
* @route '/provider/profile'
*/
profileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

profile.form = profileForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
export const services = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})

services.definition = {
    methods: ["get","head"],
    url: '/provider/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
services.url = (options?: RouteQueryOptions) => {
    return services.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
services.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
services.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: services.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
const servicesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
servicesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::services
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
servicesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

services.form = servicesForm

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
export const bookings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

bookings.definition = {
    methods: ["get","head"],
    url: '/provider/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
bookings.url = (options?: RouteQueryOptions) => {
    return bookings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
bookings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
bookings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: bookings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
const bookingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
bookingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderBookingController::bookings
* @see app/Http/Controllers/Provider/ProviderBookingController.php:23
* @route '/provider/bookings'
*/
bookingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

bookings.form = bookingsForm

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
export const availability = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: availability.url(options),
    method: 'get',
})

availability.definition = {
    methods: ["get","head"],
    url: '/provider/availability',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
availability.url = (options?: RouteQueryOptions) => {
    return availability.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
availability.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: availability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
availability.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: availability.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
const availabilityForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: availability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
availabilityForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: availability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderAvailabilityController::availability
* @see app/Http/Controllers/Provider/ProviderAvailabilityController.php:16
* @route '/provider/availability'
*/
availabilityForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: availability.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

availability.form = availabilityForm

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
export const finance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finance.url(options),
    method: 'get',
})

finance.definition = {
    methods: ["get","head"],
    url: '/provider/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
finance.url = (options?: RouteQueryOptions) => {
    return finance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
finance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
finance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: finance.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
const financeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
financeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderFinanceController::finance
* @see app/Http/Controllers/Provider/ProviderFinanceController.php:13
* @route '/provider/finance'
*/
financeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

finance.form = financeForm

const provider = {
    dashboard: Object.assign(dashboard, dashboard),
    profile: Object.assign(profile, profile937a89),
    services: Object.assign(services, services11ad26),
    bookings: Object.assign(bookings, bookings743b13),
    availability: Object.assign(availability, availability135fa0),
    finance: Object.assign(finance, financeEb3fcc),
    reviews: Object.assign(reviews, reviews),
    notifications: Object.assign(notifications, notifications),
}

export default provider
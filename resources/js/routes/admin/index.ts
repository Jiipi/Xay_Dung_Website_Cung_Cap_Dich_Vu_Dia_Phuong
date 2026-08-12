import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import profile937a89 from './profile'
import users48860f from './users'
import services11ad26 from './services'
import bookings743b13 from './bookings'
import settings69f00b from './settings'
import categories08bc8d from './categories'
import financeEb3fcc from './finance'
import reviews83e781 from './reviews'
/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::dashboard
* @see app/Http/Controllers/Admin/AdminDashboardController.php:20
* @route '/admin/dashboard'
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
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/admin/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
const profileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
*/
profileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminProfileController::profile
* @see app/Http/Controllers/Admin/AdminProfileController.php:14
* @route '/admin/profile'
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
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
export const users = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

users.definition = {
    methods: ["get","head"],
    url: '/admin/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
users.url = (options?: RouteQueryOptions) => {
    return users.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
users.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
users.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: users.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
const usersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
usersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::users
* @see app/Http/Controllers/Admin/AdminUserController.php:13
* @route '/admin/users'
*/
usersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

users.form = usersForm

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
export const services = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})

services.definition = {
    methods: ["get","head"],
    url: '/admin/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
services.url = (options?: RouteQueryOptions) => {
    return services.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
services.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
services.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: services.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
const servicesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
*/
servicesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminServiceController::services
* @see app/Http/Controllers/Admin/AdminServiceController.php:21
* @route '/admin/services'
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
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
export const bookings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

bookings.definition = {
    methods: ["get","head"],
    url: '/admin/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
bookings.url = (options?: RouteQueryOptions) => {
    return bookings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
bookings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
bookings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: bookings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
const bookingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
*/
bookingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: bookings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBookingController::bookings
* @see app/Http/Controllers/Admin/AdminBookingController.php:20
* @route '/admin/bookings'
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
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
export const settings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/admin/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
settings.url = (options?: RouteQueryOptions) => {
    return settings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
settings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
settings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
const settingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: settings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
settingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: settings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSettingsController::settings
* @see app/Http/Controllers/Admin/AdminSettingsController.php:12
* @route '/admin/settings'
*/
settingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: settings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

settings.form = settingsForm

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/admin/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
const statsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
statsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminStatsController::stats
* @see app/Http/Controllers/Admin/AdminStatsController.php:20
* @route '/admin/stats'
*/
statsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stats.form = statsForm

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
export const categories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

categories.definition = {
    methods: ["get","head"],
    url: '/admin/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
categories.url = (options?: RouteQueryOptions) => {
    return categories.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
categories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
categories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: categories.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
const categoriesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
categoriesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminCategoryController::categories
* @see app/Http/Controllers/Admin/AdminCategoryController.php:13
* @route '/admin/categories'
*/
categoriesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

categories.form = categoriesForm

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
export const finance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finance.url(options),
    method: 'get',
})

finance.definition = {
    methods: ["get","head"],
    url: '/admin/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
finance.url = (options?: RouteQueryOptions) => {
    return finance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
finance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
finance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: finance.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
const financeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
*/
financeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: finance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminFinanceController::finance
* @see app/Http/Controllers/Admin/AdminFinanceController.php:14
* @route '/admin/finance'
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

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
export const reviews = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviews.url(options),
    method: 'get',
})

reviews.definition = {
    methods: ["get","head"],
    url: '/admin/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
reviews.url = (options?: RouteQueryOptions) => {
    return reviews.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
reviews.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviews.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
reviews.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reviews.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
const reviewsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reviews.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
reviewsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reviews.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReviewController::reviews
* @see app/Http/Controllers/Admin/AdminReviewController.php:19
* @route '/admin/reviews'
*/
reviewsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reviews.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

reviews.form = reviewsForm

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    profile: Object.assign(profile, profile937a89),
    users: Object.assign(users, users48860f),
    services: Object.assign(services, services11ad26),
    bookings: Object.assign(bookings, bookings743b13),
    settings: Object.assign(settings, settings69f00b),
    stats: Object.assign(stats, stats),
    categories: Object.assign(categories, categories08bc8d),
    finance: Object.assign(finance, financeEb3fcc),
    reviews: Object.assign(reviews, reviews83e781),
}

export default admin
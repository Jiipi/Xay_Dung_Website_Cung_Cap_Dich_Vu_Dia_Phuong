import ProviderDashboardController from './ProviderDashboardController'
import ProviderProfileController from './ProviderProfileController'
import ProviderServiceController from './ProviderServiceController'
import ProviderBookingController from './ProviderBookingController'
import ProviderAvailabilityController from './ProviderAvailabilityController'
import ProviderFinanceController from './ProviderFinanceController'
import ProviderReviewController from './ProviderReviewController'
import ProviderNotificationController from './ProviderNotificationController'

const Provider = {
    ProviderDashboardController: Object.assign(ProviderDashboardController, ProviderDashboardController),
    ProviderProfileController: Object.assign(ProviderProfileController, ProviderProfileController),
    ProviderServiceController: Object.assign(ProviderServiceController, ProviderServiceController),
    ProviderBookingController: Object.assign(ProviderBookingController, ProviderBookingController),
    ProviderAvailabilityController: Object.assign(ProviderAvailabilityController, ProviderAvailabilityController),
    ProviderFinanceController: Object.assign(ProviderFinanceController, ProviderFinanceController),
    ProviderReviewController: Object.assign(ProviderReviewController, ProviderReviewController),
    ProviderNotificationController: Object.assign(ProviderNotificationController, ProviderNotificationController),
}

export default Provider
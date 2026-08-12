import AdminDashboardController from './AdminDashboardController'
import AdminProfileController from './AdminProfileController'
import AdminUserController from './AdminUserController'
import AdminServiceController from './AdminServiceController'
import AdminBookingController from './AdminBookingController'
import AdminSettingsController from './AdminSettingsController'
import AdminStatsController from './AdminStatsController'
import AdminCategoryController from './AdminCategoryController'
import AdminFinanceController from './AdminFinanceController'
import AdminReviewController from './AdminReviewController'

const Admin = {
    AdminDashboardController: Object.assign(AdminDashboardController, AdminDashboardController),
    AdminProfileController: Object.assign(AdminProfileController, AdminProfileController),
    AdminUserController: Object.assign(AdminUserController, AdminUserController),
    AdminServiceController: Object.assign(AdminServiceController, AdminServiceController),
    AdminBookingController: Object.assign(AdminBookingController, AdminBookingController),
    AdminSettingsController: Object.assign(AdminSettingsController, AdminSettingsController),
    AdminStatsController: Object.assign(AdminStatsController, AdminStatsController),
    AdminCategoryController: Object.assign(AdminCategoryController, AdminCategoryController),
    AdminFinanceController: Object.assign(AdminFinanceController, AdminFinanceController),
    AdminReviewController: Object.assign(AdminReviewController, AdminReviewController),
}

export default Admin
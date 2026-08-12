import CustomerController from './CustomerController'
import CustomerProfileController from './CustomerProfileController'
import CustomerWalletController from './CustomerWalletController'
import BookingController from './BookingController'
import PaymentController from './PaymentController'
import ReviewController from './ReviewController'
import NotificationController from './NotificationController'

const Customer = {
    CustomerController: Object.assign(CustomerController, CustomerController),
    CustomerProfileController: Object.assign(CustomerProfileController, CustomerProfileController),
    CustomerWalletController: Object.assign(CustomerWalletController, CustomerWalletController),
    BookingController: Object.assign(BookingController, BookingController),
    PaymentController: Object.assign(PaymentController, PaymentController),
    ReviewController: Object.assign(ReviewController, ReviewController),
    NotificationController: Object.assign(NotificationController, NotificationController),
}

export default Customer
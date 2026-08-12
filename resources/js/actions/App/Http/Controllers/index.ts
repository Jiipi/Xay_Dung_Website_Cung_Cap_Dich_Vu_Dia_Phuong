import HomeController from './HomeController'
import Service from './Service'
import SearchController from './SearchController'
import Category from './Category'
import Customer from './Customer'
import ChatController from './ChatController'
import Provider from './Provider'
import Admin from './Admin'
import Settings from './Settings'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    Service: Object.assign(Service, Service),
    SearchController: Object.assign(SearchController, SearchController),
    Category: Object.assign(Category, Category),
    Customer: Object.assign(Customer, Customer),
    ChatController: Object.assign(ChatController, ChatController),
    Provider: Object.assign(Provider, Provider),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers
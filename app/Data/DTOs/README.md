# DTOs

DTO dung de dong goi du lieu di qua giua request, service, repository, va response mapping.

Muc dich:
- Giu cho service method ro dau vao dau ra.
- Tach validation HTTP khoi du lieu nghiep vu.
- Ho tro search/filter phuc tap ma khong phai truyen mang tu do.

Goi y dat ten:
- `Category/CategoryFilterData.php`
- `Service/UpsertServiceData.php`
- `Booking/CreateBookingData.php`
- `Search/ServiceSearchData.php`
- `Recommendation/RecommendationContextData.php`

Thu muc `Shared` dung cho pagination, sort, range, location filter, va cac DTO dung chung.
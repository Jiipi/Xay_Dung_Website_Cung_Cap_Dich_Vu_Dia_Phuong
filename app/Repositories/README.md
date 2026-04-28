# Repositories

Tang repository chi nen dung cho query phuc tap, tong hop dashboard, search, bao cao, hoac khi can doi nguon du lieu.

Cau truc:
- `Contracts/` chua interface.
- `Eloquent/` chua implementation dung Eloquent/Query Builder.

Nguyen tac:
- Query CRUD don gian co the de o model hoac service.
- Repository tra ve model, collection, paginator, hoac DTO tuy use case.
- Service phu thuoc vao contract, khong phu thuoc truc tiep vao implementation.

Vi du:
- `Contracts/Service/ServiceRepositoryInterface.php`
- `Eloquent/Service/EloquentServiceRepository.php`
- `Contracts/Search/ServiceSearchRepositoryInterface.php`
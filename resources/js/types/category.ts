export interface DanhMucConSummary {
    id: number;
    ten_danh_muc: string;
    slug: string;
    mo_ta: string | null;
    trang_thai: string;
}

export interface ServiceCategorySummary {
    id: number;
    ten_danh_muc: string;
    slug: string;
    mo_ta: string | null;
    thu_tu_hien_thi: number;
    trang_thai: string;
    tong_danh_muc_con: number;
    danh_muc_con: DanhMucConSummary[];
}

export interface CategoryCatalogFilters {
    search: string | null;
    status: string;
    with_subcategories: boolean;
}

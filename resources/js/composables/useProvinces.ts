import { ref } from 'vue';

interface Province {
    name: string;
    code: number;
    division_type: string;
    codename: string;
    phone_code: number;
    wards: any[];
}

// Tạo reactive state để lưu trữ danh sách tỉnh thành
const provinces = ref<Province[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

export function useProvinces() {
    // Function để gọi API và load danh sách tỉnh thành
    const loadProvinces = async (): Promise<Province[]> => {
        // Nếu đã có data rồi thì không cần gọi API nữa
        if (provinces.value.length > 0) {
            return provinces.value;
        }

        loading.value = true;
        error.value = null;

        try {
            // Gọi API từ provinces.open-api.vn
            const response = await fetch('https://provinces.open-api.vn/api/v2/');
            
            // Kiểm tra response có thành công không
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Parse JSON response
            const data: Province[] = await response.json();
            
            // Lưu data vào reactive state
            provinces.value = data;
            
            return data;
        } catch (err) {
            // Xử lý lỗi
            const errorMessage = err instanceof Error ? err.message : 'Có lỗi xảy ra khi tải danh sách tỉnh thành';
            error.value = errorMessage;
            console.error('Error loading provinces:', err);
            
            // Trả về array rỗng nếu có lỗi
            return [];
        } finally {
            loading.value = false;
        }
    };

    // Function để tìm kiếm tỉnh thành
    const searchProvinces = (query: string): Province[] => {
        if (!query.trim()) return provinces.value;
        
        const searchTerm = query.toLowerCase().trim();
        return provinces.value.filter(province => 
            province.name.toLowerCase().includes(searchTerm) ||
            province.codename.toLowerCase().includes(searchTerm) ||
            province.division_type.toLowerCase().includes(searchTerm)
        );
    };

    // Function để reset data (nếu cần)
    const resetProvinces = () => {
        provinces.value = [];
        error.value = null;
    };

    // Return các function và state để component có thể sử dụng
    return {
        provinces,
        loading,
        error,
        loadProvinces,
        searchProvinces,
        resetProvinces
    };
}
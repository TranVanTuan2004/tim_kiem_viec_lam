# 🌍 Hướng dẫn Đa Ngôn Ngữ - Job Portal

## ✅ Đã Hoàn Thành

### 1. **Hệ thống đa ngôn ngữ cơ bản**

- ✅ Composable `useLanguage.ts` với localStorage persistence
- ✅ Language switcher đẹp trong Header (🇻🇳 Tiếng Việt / 🇬🇧 English)
- ✅ Translations đầy đủ cho toàn bộ website
- ✅ Auto switch toàn bộ trang khi đổi ngôn ngữ

### 2. **Components đã áp dụng đa ngôn ngữ**

- ✅ `ClientHeader.vue` - Navigation và buttons
- ✅ `ClientFooter.vue` - All footer sections
- ✅ `TopCompanies.vue` - Featured companies section
- ✅ `FeaturedJobs.vue` - Featured jobs section

### 3. **Translations Available**

File `resources/js/composables/useLanguage.ts` chứa translations cho:

- Header Navigation (Tìm việc, Công ty, Blog, Login, Register)
- Footer sections
- About Page (Giới thiệu)
- Contact Page (Liên hệ)
- Companies Page (Danh sách công ty)
- Top Companies & Featured Jobs sections
- Common actions & status

## 🚀 Cách sử dụng trong Component

### Import và setup

```vue
<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';

const { t, currentLanguage, setLanguage } = useLanguage();
</script>
```

### Sử dụng trong template

```vue
<template>
    <h1>{{ t.jobPortal }}</h1>
    <p>{{ t.aboutDescription }}</p>
    <button>{{ t.apply }}</button>
</template>
```

## 📝 Cập nhật các Pages khác

### Ví dụ: Cập nhật About Page

**Trước:**

```vue
<script setup lang="ts">
// ... existing imports
</script>

<template>
    <h1>Giới thiệu về Job Portal</h1>
    <h2>Sứ mệnh</h2>
    <p>Tạo ra một cầu nối...</p>
</template>
```

**Sau:**

```vue
<script setup lang="ts">
// ... existing imports
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
</script>

<template>
    <h1>{{ t.aboutPageTitle }}</h1>
    <h2>{{ t.mission }}</h2>
    <p>{{ t.missionText }}</p>
</template>
```

## 🔥 Pages CẦN Cập Nhật

### Danh sách Pages cần áp dụng đa ngôn ngữ:

1. **About.vue** ✅ (Có translations sẵn)
    - Thay thế: Giới thiệu, Sứ mệnh, Tầm nhìn, Core Values, etc.

2. **Contact.vue** ✅ (Có translations sẵn)
    - Thay thế: Email, Phone, Address, Send Message, etc.

3. **CompaniesIndex.vue** ✅ (Có translations sẵn)
    - Thay thế: Title, descriptions, filters, pagination, etc.

4. **JobsIndex.vue** ⚠️ (Cần thêm translations)
    - Thêm translations cho job listings

5. **JobDetail.vue** ⚠️ (Cần thêm translations)
    - Thêm translations cho job details

6. **CompanyDetail.vue** ⚠️ (Cần thêm translations)
    - Thêm translations cho company details

7. **Home.vue** ✅ (Sử dụng components đã có translations)
    - TopCompanies và FeaturedJobs đã có translations

## 📦 Thêm Translations Mới

### Trong file `useLanguage.ts`:

```typescript
export const translations = {
    vi: {
        // Thêm key mới
        yourNewKey: 'Nội dung tiếng Việt',
    },
    en: {
        // Thêm key tương ứng
        yourNewKey: 'English content',
    },
};
```

## 🎨 Best Practices

1. **Sử dụng key có nghĩa:** `t.aboutPageTitle` thay vì `t.text1`
2. **Group theo tính năng:** About Page, Contact Page, etc.
3. **Kiểm tra cả 2 ngôn ngữ:** Test UI với cả VI và EN
4. **Dynamic content:** Sử dụng template strings nếu cần:
    ```vue
    {{ t.companiesFound.replace('{count}', totalCount) }}
    ```

## 🔄 Testing

1. Click vào language switcher ở header (🌐 VI/EN)
2. Chọn ngôn ngữ khác
3. Kiểm tra toàn bộ trang đã chuyển đổi
4. Reload page - ngôn ngữ vẫn được giữ (localStorage)

## 🎯 Next Steps

### Để hoàn thiện 100% đa ngôn ngữ:

1. **Thêm translations cho các pages còn lại:**
    - JobsIndex, JobDetail, CompanyDetail
    - Dashboard pages nếu cần

2. **Dynamic content từ backend:**
    - Job titles, descriptions (từ database)
    - Company information
    - Có thể cần thêm translations ở backend

3. **Error messages và validations:**
    - Form validation messages
    - API error messages

4. **Date/Number formatting:**
    ```typescript
    // Trong useLanguage.ts
    const formatDate = (date: Date) => {
        return date.toLocaleDateString(
            currentLanguage.value === 'vi' ? 'vi-VN' : 'en-US',
        );
    };
    ```

## 📚 Available Translation Keys

Xem file `resources/js/composables/useLanguage.ts` để có danh sách đầy đủ các keys có sẵn.

### Categories:

- Header Navigation
- Common
- Footer
- About Page
- Contact Page
- Companies Page
- Top Companies Section
- Featured Jobs Section
- Common Actions
- Status

## 🐛 Troubleshooting

**Q: Ngôn ngữ không thay đổi?**
A: Đảm bảo component đã import và sử dụng `useLanguage()`

**Q: Thêm key mới nhưng không hiển thị?**
A: Kiểm tra đã thêm vào cả `vi` và `en` trong translations object

**Q: Reload page mất ngôn ngữ đã chọn?**
A: Kiểm tra localStorage có hoạt động không (browser console)

## 🎉 Kết luận

Hệ thống đa ngôn ngữ đã sẵn sàng! Bạn chỉ cần:

1. Import `useLanguage` vào component
2. Sử dụng `{{ t.keyName }}` trong template
3. Thêm translations mới khi cần

Happy coding! 🚀

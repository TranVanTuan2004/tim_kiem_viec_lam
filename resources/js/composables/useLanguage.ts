import { computed, ref, watch } from 'vue';

export type Language = 'vi' | 'en';

const currentLanguage = ref<Language>(
    (localStorage.getItem('language') as Language) || 'vi',
);

export const translations = {
    vi: {
        // Header Navigation
        findJobs: 'Tìm việc',
        companies: 'Công ty',
        blog: 'Blog IT',
        login: 'Đăng nhập',
        register: 'Đăng ký',
        dashboard: 'Dashboard',

        // Common
        jobPortal: 'Job Portal',
        search: 'Tìm kiếm',
        viewAll: 'Xem tất cả',
        viewDetails: 'Xem chi tiết',
        apply: 'Ứng tuyển',

        // Footer
        about: 'Giới thiệu',
        contact: 'Liên hệ',
        terms: 'Điều khoản',
        privacy: 'Chính sách bảo mật',
        forCandidates: 'Ứng viên',
        forEmployers: 'Nhà tuyển dụng',
        postJob: 'Đăng tuyển dụng',
        pricing: 'Bảng giá',
        support: 'Hỗ trợ',
        cvTemplates: 'Mẫu CV',
        careerGuide: 'Hướng dẫn',
        companyList: 'Danh sách công ty',

        // Job Related
        jobs: 'Việc làm',
        job: 'Việc làm',
        openPositions: 'vị trí đang tuyển',
        salary: 'Mức lương',
        experience: 'Kinh nghiệm',
        location: 'Địa điểm',
        deadline: 'Hạn nộp',

        // Company
        company: 'Công ty',
        verified: 'Đã xác thực',
        employees: 'Nhân viên',
        reviews: 'đánh giá',

        // Language
        vietnamese: 'Tiếng Việt',
        english: 'English',
        language: 'Ngôn ngữ',

        // Footer
        followUs: 'Theo dõi',
        allRightsReserved: 'All rights reserved',

        // About Page
        aboutUs: 'Giới thiệu về chúng tôi',
        aboutPageTitle: 'Giới thiệu về Job Portal',
        aboutDescription:
            'Nền tảng tuyển dụng hàng đầu kết nối nhà tuyển dụng và ứng viên tài năng trong ngành công nghệ',
        mission: 'Sứ mệnh',
        vision: 'Tầm nhìn',
        missionText:
            'Tạo ra một cầu nối tin cậy giữa các công ty công nghệ và những tài năng IT xuất sắc. Chúng tôi cam kết mang đến trải nghiệm tuyển dụng tốt nhất, giúp ứng viên tìm được công việc mơ ước và doanh nghiệp xây dựng đội ngũ vững mạnh.',
        visionText:
            'Trở thành nền tảng tuyển dụng IT hàng đầu Việt Nam, nơi mọi cơ hội việc làm công nghệ được kết nối một cách hiệu quả và minh bạch. Chúng tôi hướng đến việc xây dựng một cộng đồng chuyên nghiệp và phát triển bền vững.',
        coreValues: 'Giá trị cốt lõi',
        coreValuesTitle: 'Những giá trị chúng tôi theo đuổi',
        coreValuesDescription:
            'Chúng tôi xây dựng nền tảng dựa trên những giá trị bền vững',
        quality: 'Chất lượng',
        qualityDesc: 'Cam kết mang đến dịch vụ tuyển dụng chất lượng cao nhất',
        dedication: 'Tận tâm',
        dedicationDesc: 'Lắng nghe và thấu hiểu nhu cầu của mỗi khách hàng',
        credibility: 'Uy tín',
        credibilityDesc: 'Xây dựng niềm tin qua sự minh bạch và chuyên nghiệp',
        community: 'Cộng đồng',
        communityDesc: 'Xây dựng cộng đồng IT mạnh mẽ và gắn kết',
        ourStory: 'Câu chuyện của chúng tôi',
        totalCompanies: 'Công ty đối tác',
        totalJobs: 'Việc làm',
        totalCandidates: 'Ứng viên',
        satisfaction: 'Hài lòng',
        storyParagraph1:
            'Job Portal được thành lập với mục tiêu giải quyết những khó khăn trong việc tìm kiếm và tuyển dụng nhân tài IT. Chúng tôi hiểu rằng việc tìm được đúng người cho đúng vị trí là chìa khóa thành công của mọi doanh nghiệp.',
        storyParagraph2:
            'Với đội ngũ giàu kinh nghiệm trong lĩnh vực công nghệ và tuyển dụng, chúng tôi đã xây dựng một nền tảng hiện đại, dễ sử dụng và hiệu quả. Mỗi tính năng được thiết kế với sự tỉ mỉ để mang lại trải nghiệm tốt nhất cho cả nhà tuyển dụng và ứng viên.',
        storyParagraph3:
            'Hàng ngày, chúng tôi nỗ lực không ngừng để hoàn thiện dịch vụ, lắng nghe phản hồi và cập nhật những công nghệ mới nhất. Sự thành công của bạn chính là động lực để chúng tôi tiếp tục phát triển.',

        // Contact Page
        contactUs: 'Liên hệ với chúng tôi',
        contactDescription:
            'Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để lại thông tin và chúng tôi sẽ phản hồi sớm nhất!',
        email: 'Email',
        phone: 'Điện thoại',
        address: 'Địa chỉ',
        workingHours: 'Giờ làm việc',
        sendMessage: 'Gửi tin nhắn',
        yourName: 'Họ và tên',
        yourEmail: 'Email',
        subject: 'Tiêu đề',
        message: 'Nội dung',
        sending: 'Đang gửi...',
        visitOffice: 'Ghé thăm văn phòng của chúng tôi',
        contactDirectly: 'Liên hệ trực tiếp qua hotline',
        sendUsEmail: 'Gửi email cho chúng tôi bất cứ lúc nào',

        // Terms & Privacy Pages
        termsOfService: 'Điều khoản sử dụng',
        privacyPolicy: 'Chính sách bảo mật',
        ofService: 'Sử dụng',
        policy: 'Bảo mật',

        // Companies Page
        companiesPageTitle: 'Danh sách công ty',
        companiesHiring: 'Công ty đang tuyển dụng',
        discoverCompanies:
            'Khám phá các công ty uy tín đang có cơ hội việc làm hấp dẫn',
        discoverAllCompanies:
            'Tìm kiếm và khám phá các công ty uy tín với môi trường làm việc tuyệt vời',
        companiesFound: 'Tìm thấy {count} công ty',
        found: 'Tìm thấy',
        page: 'Trang',
        trustedCompanies: 'công ty uy tín',
        verifiedCompanies: 'công ty uy tín',
        currentlyHiring: 'đang tuyển',
        hiring: 'đang tuyển',
        noJobs: 'Chưa có việc làm',
        noDescription: 'Chưa có mô tả',
        exploreAllCompanies: 'Khám phá tất cả công ty',
        searchByCompanyName: 'Tìm kiếm theo tên công ty...',
        filter: 'Bộ lọc',
        industry: 'Ngành nghề',
        selectIndustry: 'Chọn ngành nghề',
        selectLocation: 'Chọn địa điểm',

        // Top Companies Section
        topCompanies: 'Top công ty nổi bật',
        topCompaniesDescription:
            'Khám phá các công ty uy tín hàng đầu với môi trường làm việc tuyệt vời',

        // Featured Jobs Section
        featuredJobs: 'Việc làm nổi bật',
        hotJobs: 'Hot Jobs',
        featuredJobsDescription:
            'Cơ hội việc làm IT hấp dẫn nhất từ các công ty hàng đầu',

        // Common Actions
        seeMore: 'Xem thêm',
        previous: 'Trước',
        next: 'Sau',
        close: 'Đóng',
        save: 'Lưu',
        cancel: 'Hủy',
        delete: 'Xóa',
        edit: 'Sửa',
        confirm: 'Xác nhận',

        // Status
        active: 'Đang hoạt động',
        inactive: 'Không hoạt động',
        pending: 'Chờ xử lý',
        approved: 'Đã duyệt',
        rejected: 'Từ chối',
    },
    en: {
        // Header Navigation
        findJobs: 'Find Jobs',
        companies: 'Companies',
        blog: 'IT Blog',
        login: 'Login',
        register: 'Sign Up',
        dashboard: 'Dashboard',

        // Common
        jobPortal: 'Job Portal',
        search: 'Search',
        viewAll: 'View All',
        viewDetails: 'View Details',
        apply: 'Apply',

        // Footer
        about: 'About Us',
        contact: 'Contact',
        terms: 'Terms of Service',
        privacy: 'Privacy Policy',
        forCandidates: 'For Candidates',
        forEmployers: 'For Employers',
        postJob: 'Post a Job',
        pricing: 'Pricing',
        support: 'Support',
        cvTemplates: 'CV Templates',
        careerGuide: 'Career Guide',
        companyList: 'Companies',

        // Job Related
        jobs: 'Jobs',
        job: 'Job',
        openPositions: 'open positions',
        salary: 'Salary',
        experience: 'Experience',
        location: 'Location',
        deadline: 'Deadline',

        // Company
        company: 'Company',
        verified: 'Verified',
        employees: 'Employees',
        reviews: 'reviews',

        // Language
        vietnamese: 'Vietnamese',
        english: 'English',
        language: 'Language',

        // Footer
        followUs: 'Follow Us',
        allRightsReserved: 'All rights reserved',

        // About Page
        aboutUs: 'About Us',
        aboutPageTitle: 'About Job Portal',
        aboutDescription:
            'Leading recruitment platform connecting employers and talented candidates in the technology industry',
        mission: 'Mission',
        vision: 'Vision',
        missionText:
            'Create a reliable bridge between technology companies and outstanding IT talents. We are committed to providing the best recruitment experience, helping candidates find their dream jobs and businesses build strong teams.',
        visionText:
            'Become the leading IT recruitment platform in Vietnam, where all technology job opportunities are connected effectively and transparently. We aim to build a professional and sustainable community.',
        coreValues: 'Core Values',
        coreValuesTitle: 'Values We Pursue',
        coreValuesDescription:
            'We build our platform based on sustainable values',
        quality: 'Quality',
        qualityDesc:
            'Committed to providing the highest quality recruitment services',
        dedication: 'Dedication',
        dedicationDesc: 'Listen and understand the needs of each customer',
        credibility: 'Credibility',
        credibilityDesc: 'Build trust through transparency and professionalism',
        community: 'Community',
        communityDesc: 'Building a strong and cohesive IT community',
        ourStory: 'Our Story',
        totalCompanies: 'Partner Companies',
        totalJobs: 'Jobs',
        totalCandidates: 'Candidates',
        satisfaction: 'Satisfaction',
        storyParagraph1:
            'Job Portal was founded with the goal of solving the difficulties in finding and recruiting IT talents. We understand that finding the right person for the right position is the key to success for any business.',
        storyParagraph2:
            'With a team of experienced professionals in technology and recruitment, we have built a modern, user-friendly, and efficient platform. Every feature is meticulously designed to provide the best experience for both recruiters and candidates.',
        storyParagraph3:
            'Every day, we strive to improve our services, listen to feedback, and update with the latest technologies. Your success is our motivation to continue developing.',

        // Contact Page
        contactUs: 'Contact Us',
        contactDescription:
            'We are always ready to listen and support you. Leave your information and we will respond as soon as possible!',
        email: 'Email',
        phone: 'Phone',
        address: 'Address',
        workingHours: 'Working Hours',
        sendMessage: 'Send Message',
        yourName: 'Your Name',
        yourEmail: 'Your Email',
        subject: 'Subject',
        message: 'Message',
        sending: 'Sending...',
        visitOffice: 'Visit our office',
        contactDirectly: 'Contact directly via hotline',
        sendUsEmail: 'Send us an email anytime',

        // Terms & Privacy Pages
        termsOfService: 'Terms of Service',
        privacyPolicy: 'Privacy Policy',
        ofService: 'of Service',
        policy: 'Policy',

        // Companies Page
        companiesPageTitle: 'Companies',
        companiesHiring: 'Companies Hiring',
        discoverCompanies:
            'Discover reputable companies with attractive job opportunities',
        discoverAllCompanies:
            'Search and discover reputable companies with great work environments',
        companiesFound: 'Found {count} companies',
        found: 'Found',
        page: 'Page',
        trustedCompanies: 'trusted companies',
        verifiedCompanies: 'verified companies',
        currentlyHiring: 'hiring',
        hiring: 'hiring',
        noJobs: 'No jobs available',
        noDescription: 'No description',
        exploreAllCompanies: 'Explore All Companies',
        searchByCompanyName: 'Search by company name...',
        filter: 'Filter',
        industry: 'Industry',
        selectIndustry: 'Select industry',
        selectLocation: 'Select location',

        // Top Companies Section
        topCompanies: 'Top Featured Companies',
        topCompaniesDescription:
            'Discover top reputable companies with excellent work environments',

        // Featured Jobs Section
        featuredJobs: 'Featured Jobs',
        hotJobs: 'Hot Jobs',
        featuredJobsDescription:
            'The most attractive IT job opportunities from leading companies',

        // Common Actions
        seeMore: 'See More',
        previous: 'Previous',
        next: 'Next',
        close: 'Close',
        save: 'Save',
        cancel: 'Cancel',
        delete: 'Delete',
        edit: 'Edit',
        confirm: 'Confirm',

        // Status
        active: 'Active',
        inactive: 'Inactive',
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected',
    },
};

export function useLanguage() {
    const setLanguage = (lang: Language) => {
        currentLanguage.value = lang;
        localStorage.setItem('language', lang);
        document.documentElement.lang = lang;
    };

    const toggleLanguage = () => {
        setLanguage(currentLanguage.value === 'vi' ? 'en' : 'vi');
    };

    // Make sure t is reactive and updates when language changes
    const t = computed(() => {
        return translations[currentLanguage.value];
    });

    // Watch for language changes
    watch(
        currentLanguage,
        (newLang) => {
            // You can add logic here to fetch translations from API if needed
            document.documentElement.lang = newLang;
            // Force a re-render by triggering reactivity
            console.log('Language changed to:', newLang);
        },
        { immediate: true },
    );

    return {
        currentLanguage: computed(() => currentLanguage.value),
        setLanguage,
        toggleLanguage,
        t: computed(() => translations[currentLanguage.value]),
    };
}

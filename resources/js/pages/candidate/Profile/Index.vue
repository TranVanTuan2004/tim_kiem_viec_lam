<template>
    <AuthenticatedLayout>
        <Head title="Hồ sơ cá nhân" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Profile Header with Gradient -->
                <div
                    class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl"
                >
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="relative px-8 py-12 sm:px-12">
                        <div class="flex flex-col items-center sm:flex-row sm:items-end sm:justify-between">
                            <!-- Avatar and Basic Info -->
                            <div class="flex flex-col items-center sm:flex-row sm:items-center sm:space-x-6">
                                <!-- Avatar -->
                                <div class="relative mb-4 sm:mb-0">
                                    <div
                                        v-if="profile.avatar_url"
                                        class="relative h-32 w-32 sm:h-40 sm:w-40"
                                    >
                                        <img
                                            :src="profile.avatar_url"
                                            :alt="user.name"
                                            class="h-full w-full rounded-full object-cover border-4 border-white shadow-2xl ring-4 ring-white/50"
                                        />
                                        <div
                                            v-if="profile.is_available"
                                            class="absolute bottom-2 right-2 h-6 w-6 rounded-full bg-green-500 border-2 border-white shadow-lg"
                                        ></div>
                                    </div>
                                    <div
                                        v-else
                                        class="relative h-32 w-32 sm:h-40 sm:w-40 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white shadow-2xl ring-4 ring-white/50"
                                    >
                                        <svg
                                            class="h-16 w-16 sm:h-20 sm:w-20 text-white"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        <div
                                            v-if="profile.is_available"
                                            class="absolute bottom-2 right-2 h-6 w-6 rounded-full bg-green-500 border-2 border-white shadow-lg"
                                        ></div>
                                    </div>
                                </div>

                                <!-- User Info -->
                                <div class="text-center sm:text-left">
                                    <h1
                                        class="text-3xl font-bold text-white sm:text-4xl mb-2"
                                    >
                                        {{ user.name }}
                                    </h1>
                                    <p
                                        v-if="profile.current_position"
                                        class="text-lg text-blue-100 mb-1"
                                    >
                                        {{ profile.current_position }}
                                        <span
                                            v-if="profile.current_company"
                                            class="text-blue-200"
                                        >
                                            tại {{ profile.current_company }}
                                        </span>
                                    </p>
                                    <div class="flex flex-wrap items-center justify-center gap-2 mt-3 sm:justify-start">
                                        <Badge
                                            :variant="
                                                profile.is_available
                                                    ? 'default'
                                                    : 'secondary'
                                            "
                                            class="bg-white/20 text-white border-white/30 backdrop-blur-sm"
                                        >
                                            {{
                                                profile.is_available
                                                    ? 'Đang tìm việc'
                                                    : 'Không tìm việc'
                                            }}
                                        </Badge>
                                        <Badge
                                            v-if="profile.experience_level"
                                            variant="outline"
                                            class="bg-white/10 text-white border-white/30 backdrop-blur-sm"
                                        >
                                            {{ getExperienceLevelLabel(profile.experience_level) }}
                                        </Badge>
                                        <Badge
                                            v-if="profile.city"
                                            variant="outline"
                                            class="bg-white/10 text-white border-white/30 backdrop-blur-sm"
                                        >
                                            <MapPin class="h-3 w-3 mr-1" />
                                            {{ profile.city }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 flex flex-col gap-3 sm:mt-0 sm:flex-row">
                                <Button
                                    @click="toggleAvailability"
                                    :variant="
                                        profile.is_available
                                            ? 'default'
                                            : 'secondary'
                                    "
                                    class="bg-white text-blue-700 hover:bg-blue-50 shadow-lg"
                                >
                                    <ToggleLeft
                                        v-if="profile.is_available"
                                        class="h-4 w-4"
                                    />
                                    <ToggleRight v-else class="h-4 w-4" />
                                    {{
                                        profile.is_available
                                            ? 'Đang tìm việc'
                                            : 'Không tìm việc'
                                    }}
                                </Button>
                                <Button
                                    as-child
                                    variant="outline"
                                    class="border-2 border-white/30 bg-white/10 text-white hover:bg-white/20 backdrop-blur-sm"
                                >
                                    <Link :href="edit.url()">
                                        <Edit class="h-4 w-4" />
                                        Chỉnh sửa hồ sơ
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Left Column - Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- About Section -->
                        <Card v-if="profile.summary">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Info class="h-5 w-5 text-blue-600" />
                                    Giới thiệu
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="text-gray-700 leading-relaxed">
                                    {{ profile.summary }}
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Personal Information -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <User class="h-5 w-5 text-blue-600" />
                                    Thông tin cá nhân
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100"
                                        >
                                            <User class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Họ và tên
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ user.name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100"
                                        >
                                            <Mail class="h-5 w-5 text-green-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Email
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="user.phone"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100"
                                        >
                                            <Phone class="h-5 w-5 text-purple-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Số điện thoại
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ user.phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.gender"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-pink-100"
                                        >
                                            <Users class="h-5 w-5 text-pink-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Giới tính
                                            </p>
                                            <p
                                                class="mt-1 capitalize text-gray-900"
                                            >
                                                {{
                                                    profile.gender === 'male'
                                                        ? 'Nam'
                                                        : profile.gender ===
                                                          'female'
                                                          ? 'Nữ'
                                                          : 'Khác'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.birth_date"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-100"
                                        >
                                            <Calendar class="h-5 w-5 text-yellow-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Ngày sinh
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ formatDate(profile.birth_date) }}
                                                <span
                                                    v-if="profile.age"
                                                    class="text-gray-500"
                                                >
                                                    ({{ profile.age }} tuổi)
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.city || profile.province"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100"
                                        >
                                            <MapPin class="h-5 w-5 text-red-600" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Địa chỉ
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{
                                                    profile.city && profile.province
                                                        ? `${profile.city}, ${profile.province}`
                                                        : profile.city ||
                                                          profile.province ||
                                                          'Chưa cập nhật'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="profile.address"
                                    class="mt-6 flex items-start space-x-3"
                                >
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100"
                                    >
                                        <Home class="h-5 w-5 text-indigo-600" />
                                    </div>
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Địa chỉ chi tiết
                                        </p>
                                        <p class="mt-1 text-gray-900">
                                            {{ profile.address }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Professional Details -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Briefcase class="h-5 w-5 text-blue-600" />
                                    Thông tin nghề nghiệp
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div
                                        v-if="profile.current_position"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100"
                                        >
                                            <Briefcase
                                                class="h-5 w-5 text-blue-600"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Vị trí hiện tại
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ profile.current_position }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.current_company"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100"
                                        >
                                            <Building2
                                                class="h-5 w-5 text-green-600"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Công ty
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{ profile.current_company }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.experience_level"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100"
                                        >
                                            <TrendingUp
                                                class="h-5 w-5 text-purple-600"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Cấp độ kinh nghiệm
                                            </p>
                                            <p class="mt-1 text-gray-900">
                                                {{
                                                    getExperienceLevelLabel(
                                                        profile.experience_level,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="profile.expected_salary"
                                        class="flex items-start space-x-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-yellow-100"
                                        >
                                            <DollarSign
                                                class="h-5 w-5 text-yellow-600"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-500"
                                            >
                                                Mức lương mong muốn
                                            </p>
                                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                                ${{
                                                    profile.expected_salary.toLocaleString()
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="profile.cv_url"
                                    class="mt-6 flex items-center space-x-3"
                                >
                                    <Button
                                        as-child
                                        variant="outline"
                                        class="w-full sm:w-auto"
                                    >
                                        <a
                                            :href="profile.cv_url"
                                            target="_blank"
                                            class="inline-flex items-center"
                                        >
                                            <FileText class="mr-2 h-4 w-4" />
                                            Tải CV/Resume
                                        </a>
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Skills -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Award class="h-5 w-5 text-blue-600" />
                                    Kỹ năng
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="
                                        profile.skills &&
                                        profile.skills.length > 0
                                    "
                                    class="flex flex-wrap gap-3"
                                >
                                    <div
                                        v-for="skill in profile.skills"
                                        :key="skill.id"
                                        class="group relative"
                                    >
                                        <Badge
                                            variant="secondary"
                                            class="px-4 py-2 text-sm font-medium shadow-sm hover:shadow-md transition-shadow"
                                        >
                                            <span class="font-semibold">{{
                                                skill.name
                                            }}</span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                • {{ getSkillLevelLabel(skill.level) }}
                                            </span>
                                            <span
                                                v-if="skill.years_experience > 0"
                                                class="ml-2 text-xs text-gray-500"
                                            >
                                                • {{ skill.years_experience }} năm
                                            </span>
                                        </Badge>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500">
                                    Chưa có kỹ năng nào được thêm vào
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Work Experience -->
                        <Card>
                            <CardHeader>
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <CardTitle class="flex items-center gap-2">
                                        <Briefcase
                                            class="h-5 w-5 text-blue-600"
                                        />
                                        Kinh nghiệm làm việc
                                    </CardTitle>
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                    >
                                        <Link
                                            :href="workExperiencesIndex.url()"
                                        >
                                            Quản lý
                                        </Link>
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="
                                        profile.work_experiences &&
                                        profile.work_experiences.length > 0
                                    "
                                    class="space-y-6"
                                >
                                    <div
                                        v-for="(exp, index) in profile.work_experiences"
                                        :key="exp.id"
                                        class="relative pl-8 pb-6"
                                        :class="{
                                            'border-l-2 border-blue-200': index < profile.work_experiences.length - 1,
                                        }"
                                    >
                                        <div
                                            class="absolute left-0 top-1 h-4 w-4 rounded-full bg-blue-600 border-2 border-white shadow-lg"
                                        ></div>
                                        <div
                                            class="rounded-lg bg-gray-50 p-4 hover:bg-gray-100 transition-colors"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ exp.position }}
                                            </h3>
                                            <p
                                                class="mt-1 text-gray-700 font-medium"
                                            >
                                                {{ exp.company_name }}
                                            </p>
                                            <p
                                                class="mt-2 flex items-center text-sm text-gray-500"
                                            >
                                                <Calendar class="mr-2 h-4 w-4" />
                                                {{ formatDate(exp.start_date) }} -
                                                {{
                                                    exp.is_current
                                                        ? 'Hiện tại'
                                                        : formatDate(
                                                              exp.end_date,
                                                          )
                                                }}
                                            </p>
                                            <p
                                                v-if="exp.description"
                                                class="mt-3 text-gray-600 leading-relaxed"
                                            >
                                                {{ exp.description }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500">
                                    Chưa có kinh nghiệm làm việc
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Education -->
                        <Card>
                            <CardHeader>
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <CardTitle class="flex items-center gap-2">
                                        <GraduationCap
                                            class="h-5 w-5 text-blue-600"
                                        />
                                        Học vấn
                                    </CardTitle>
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                    >
                                        <Link :href="educationsIndex.url()">
                                            Quản lý
                                        </Link>
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="
                                        profile.educations &&
                                        profile.educations.length > 0
                                    "
                                    class="space-y-6"
                                >
                                    <div
                                        v-for="(edu, index) in profile.educations"
                                        :key="edu.id"
                                        class="relative pl-8 pb-6"
                                        :class="{
                                            'border-l-2 border-green-200': index < profile.educations.length - 1,
                                        }"
                                    >
                                        <div
                                            class="absolute left-0 top-1 h-4 w-4 rounded-full bg-green-600 border-2 border-white shadow-lg"
                                        ></div>
                                        <div
                                            class="rounded-lg bg-gray-50 p-4 hover:bg-gray-100 transition-colors"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900"
                                            >
                                                {{ edu.degree }}
                                            </h3>
                                            <p
                                                class="mt-1 text-gray-700 font-medium"
                                            >
                                                {{ edu.institution }}
                                            </p>
                                            <p
                                                v-if="edu.field_of_study"
                                                class="mt-2 text-sm text-gray-600"
                                            >
                                                {{ edu.field_of_study }}
                                            </p>
                                            <p
                                                class="mt-2 flex items-center text-sm text-gray-500"
                                            >
                                                <Calendar class="mr-2 h-4 w-4" />
                                                {{ formatDate(edu.start_date) }} -
                                                {{
                                                    edu.is_current
                                                        ? 'Hiện tại'
                                                        : formatDate(
                                                              edu.end_date,
                                                          )
                                                }}
                                            </p>
                                            <p
                                                v-if="edu.description"
                                                class="mt-3 text-gray-600 leading-relaxed"
                                            >
                                                {{ edu.description }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500">
                                    Chưa có thông tin học vấn
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Portfolios -->
                        <Card>
                            <CardHeader>
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <CardTitle class="flex items-center gap-2">
                                        <Folder
                                            class="h-5 w-5 text-blue-600"
                                        />
                                        Dự án Portfolio
                                    </CardTitle>
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                    >
                                        <Link :href="portfoliosIndex.url()">
                                            Quản lý
                                        </Link>
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="
                                        profile.portfolios &&
                                        profile.portfolios.length > 0
                                    "
                                    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                >
                                    <div
                                        v-for="portfolio in profile.portfolios"
                                        :key="portfolio.id"
                                        class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-all hover:shadow-lg hover:border-blue-300"
                                    >
                                        <div
                                            v-if="portfolio.thumbnail"
                                            class="relative h-48 overflow-hidden bg-gray-100"
                                        >
                                            <img
                                                :src="portfolio.thumbnail"
                                                :alt="portfolio.title"
                                                class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                            />
                                            <div
                                                v-if="portfolio.is_featured"
                                                class="absolute top-2 right-2"
                                            >
                                                <Badge variant="default">
                                                    Nổi bật
                                                </Badge>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <h3
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ portfolio.title }}
                                            </h3>
                                            <p
                                                v-if="portfolio.description"
                                                class="mt-2 line-clamp-2 text-sm text-gray-600"
                                            >
                                                {{ portfolio.description }}
                                            </p>
                                            <a
                                                v-if="portfolio.project_url"
                                                :href="portfolio.project_url"
                                                target="_blank"
                                                class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700"
                                            >
                                                Xem dự án
                                                <ArrowRight
                                                    class="ml-1 h-4 w-4"
                                                />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500">
                                    Chưa có dự án portfolio
                                </p>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Right Column - Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-lg">
                                    Thống kê nhanh
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div
                                    class="flex items-center justify-between rounded-lg bg-blue-50 p-4"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100"
                                        >
                                            <Briefcase
                                                class="h-5 w-5 text-blue-600"
                                            />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-600"
                                            >
                                                Kinh nghiệm
                                            </p>
                                            <p class="text-lg font-bold text-gray-900">
                                                {{
                                                    profile.work_experiences?.length ||
                                                    0
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between rounded-lg bg-green-50 p-4"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100"
                                        >
                                            <GraduationCap
                                                class="h-5 w-5 text-green-600"
                                            />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-600"
                                            >
                                                Học vấn
                                            </p>
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ profile.educations?.length || 0 }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between rounded-lg bg-purple-50 p-4"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100"
                                        >
                                            <Award
                                                class="h-5 w-5 text-purple-600"
                                            />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-600"
                                            >
                                                Kỹ năng
                                            </p>
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ profile.skills?.length || 0 }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between rounded-lg bg-orange-50 p-4"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100"
                                        >
                                            <Folder
                                                class="h-5 w-5 text-orange-600"
                                            />
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-600"
                                            >
                                                Portfolio
                                            </p>
                                            <p class="text-lg font-bold text-gray-900">
                                                {{ profile.portfolios?.length || 0 }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { index as portfoliosIndex } from '@/routes/candidate/portfolios';
import {
    edit,
    toggleAvailability as toggleAvailabilityRoute,
} from '@/routes/candidate/profile';
import { index as workExperiencesIndex } from '@/routes/candidate/work-experiences';
import { index as educationsIndex } from '@/routes/candidate/educations';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    User,
    Mail,
    Phone,
    Calendar,
    MapPin,
    Home,
    Briefcase,
    Building2,
    TrendingUp,
    DollarSign,
    Award,
    FileText,
    GraduationCap,
    Folder,
    ArrowRight,
    Edit,
    ToggleLeft,
    ToggleRight,
    Users,
    Info,
} from 'lucide-vue-next';

interface Props {
    profile: any;
    user: any;
}

defineProps<Props>();

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getExperienceLevelLabel = (level: string) => {
    const labels: Record<string, string> = {
        fresher: 'Mới tốt nghiệp',
        junior: 'Junior',
        middle: 'Middle',
        senior: 'Senior',
        lead: 'Lead',
    };
    return labels[level] || level;
};

const getSkillLevelLabel = (level: string) => {
    const labels: Record<string, string> = {
        beginner: 'Cơ bản',
        intermediate: 'Trung bình',
        advanced: 'Nâng cao',
        expert: 'Chuyên gia',
    };
    return labels[level] || level;
};

const toggleAvailability = () => {
    router.post(
        toggleAvailabilityRoute.url(),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // Handle success if needed
            },
        },
    );
};
</script>

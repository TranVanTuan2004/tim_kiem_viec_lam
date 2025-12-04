import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    permission?: string;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface CompanyType {
    id: number;
    user_id: number;
    company_name: string;
    company_slug: string;
    description: string;
    website: string | null;
    logo: string | null;
    address: string | null;
    city: string;
    province: string;
    size: string | null;
    industry: string;
    rating: number;
    total_reviews: number;
    is_verified: boolean;
    created_at: string;
    updated_at: string;

    // Nếu bạn load user khi query company
    user?: User;
}

export interface UserType {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at?: string | null;
    created_at?: string;
    updated_at?: string;
}